<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Magellan;

use function Lemuria\getClass;
use Lemuria\Model\Coordinates;
use Lemuria\Model\Lemuria\Ability;
use Lemuria\Model\Lemuria\Commodity\Luxury\Balsam;
use Lemuria\Model\Lemuria\Commodity\Luxury\Gem;
use Lemuria\Model\Lemuria\Commodity\Luxury\Myrrh;
use Lemuria\Model\Lemuria\Commodity\Luxury\Oil;
use Lemuria\Model\Lemuria\Commodity\Luxury\Olibanum;
use Lemuria\Model\Lemuria\Commodity\Luxury\Silk;
use Lemuria\Model\Lemuria\Commodity\Luxury\Spice;
use Lemuria\Model\Lemuria\Commodity\Peasant;
use Lemuria\Model\Lemuria\Commodity\Silver;
use Lemuria\Model\Lemuria\Commodity\Wood;
use Lemuria\Model\Lemuria\Construction;
use Lemuria\Model\Lemuria\Luxuries;
use Lemuria\Model\Lemuria\Luxury;
use Lemuria\Model\Lemuria\Party;
use Lemuria\Model\Lemuria\Party\Census;
use Lemuria\Model\Lemuria\Quantity;
use Lemuria\Model\Lemuria\Region;
use Lemuria\Model\Lemuria\Relation;
use Lemuria\Model\Lemuria\Unit;
use Lemuria\Model\Lemuria\Vessel;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Renderer\Writer;

class MagellanWriter implements Writer
{
	private const HEADER = [
		'VERSION 67',
		'charset'       => 'UTF-8',
		'locale'        => 'de',
		'Spiel'         => 'Lemuria',
		'Konfiguration' => 'Lemuria',
		'Basis'         => 36,
		'noskillpoints' => 0,
		'max_units'     => 1000,
		'Build'         => '1.0.0',
		'date'          => '$DATE',
		'Runde'         => '$TURN',
		'Zeitalter'     => 1,
		'mailto'        => 'lemuria@online.de',
		'mailcmd'       => 'Lemuria Befehle'
	];

	/**
	 * @var resource|null
	 */
	private $file;

	/**
	 * @var array(string=>mixed)
	 */
	private array $variables = [];

	private Coordinates $origin;

	public function __construct(string $path) {
		$this->file = fopen($path, 'w');
		if (!$this->file) {
			throw new \RuntimeException('Could not open file ' . $path . '.');
		}
		$this->initVariables();
	}

	public function __destruct() {
		if ($this->file) {
			$this->close();
		}
	}

	public function render(Id $party): Writer {
		if (!$this->file) {
			throw new \RuntimeException('File has been closed.');
		}

		$this->writeHeader();

		$party = Party::get($party);
		$this->origin = Lemuria::World()->getCoordinates($party->Origin());
		$this->writeParty($party);

		$census = new Census($party);
		foreach ($census->getAtlas() as $region /* @var Region $region */) {
			$this->writeRegion($region);
		}

		$this->writeTranslations();

		if (!fclose($this->file)) {
			throw new \RuntimeException('Could not close file.');
		}
		$this->file = null;
		return $this;
	}

	private function initVariables(): void {
		$this->variables['$DATE'] = time();
		$this->variables['$TURN'] = Lemuria::Calendar()->Round();
	}

	private function writeData(array $data): void {
		$block = current($data);
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				if ($block === 'TALENTE') {
					$line = implode(' ', $value) . ';' . $key;
					fputs($this->file, $line . PHP_EOL);
				} else {
					$this->writeData($value);
				}
			} else {
				if (is_int($key)) {
					$line = $value;
				} else {
					if (is_string($value) && isset($this->variables[$value])) {
						$value = $this->variables[$value];
					}
					if (is_int($value)) {
						$line = $value . ';' . $key;
					} else {
						$line = '"' . $value . '";' . $key;
					}
				}
				fputs($this->file, $line . PHP_EOL);
			}
		}
	}

	/**
	 * @throws \RuntimeException
	 */
	private function close(): void {
		if (!fclose($this->file)) {
			throw new \RuntimeException('Could not close file.');
		}
		$this->file = null;
	}

	/**
	 * Write the Magellan VERSION header.
	 */
	private function writeHeader(): void {
		$this->writeData(self::HEADER);
	}

	private function writeParty(Party $party): void {
		$data = [
			'PARTEI ' . $party->Id()->Id(),
			'locale'              => self::HEADER['locale'],
			'age'                 => 1,
			'Optionen'            => 1 + 2 + 8 + 64 + 256 + 512,
			'Punkte'              => 0,
			'Punktedurchschnitt'  => 0,
			'Typ'                 => Translator::RACE[getClass($party->Race())],
			'Rekrutierungskosten' => $party->Race()->Recruiting(),
			'Anzahl Personen'     => $party->People()->count(),
			'Parteiname'          => $party->Name(),
			'email'               => 'lemuria@online.de',
			'banner'              => $party->Description(),
			'ISLAND 1',
			'Name'                => 'Kontinent Lemuria',
			'Beschr'              => 'Dies ist der Hauptkontinent Lemuria.'
		];
		$this->writeData($data);

		foreach ($party->Diplomacy() as $relation) {
			$this->writeAlliance($relation);
		}
	}

	private function writeAlliance(Relation $relation): void {
		$status = 0;
		if ($relation->has(Relation::SILVER)) {
			$status += 1;
		}
		if ($relation->has(Relation::COMBAT)) {
			$status += 2;
		}
		if ($relation->has(Relation::PERCEPTION)) {
			$status += 4;
		}
		if ($relation->has(Relation::GIVE)) {
			$status += 8;
		}
		if ($relation->has(Relation::GUARD)) {
			$status += 16;
		}
		if ($relation->has(Relation::DISGUISE)) {
			$status += 32;
		}

		if ($status > 0) {
			$data = [
				'ALLIANZ ' . $relation->Party()->Id()->Id(),
				'Parteiname' => $relation->Party()->Name(),
				'Status'     => $status
			];
			$this->writeData($data);
		}
	}

	private function writeRegion(Region $region): void {
		$coordinates = Lemuria::World()->getCoordinates($region);
		$x           = $coordinates->X() - $this->origin->X();
		$y           = $coordinates->Y() - $this->origin->Y();
		$resources   = $region->Resources();
		$data = [
			'REGION ' . $x . ' ' . $y,
			'id'       => $region->Id()->Id(),
			'Name'     => $region->Name(),
			'Terrain'  => Translator::LANDSCAPE[getClass($region->Landscape())],
			'Insel'    => 1,
			'Beschr'   => $region->Description(),
			'Bauern'   => $resources[Peasant::class]->Count(),
			'Silber'   => $resources[Silver::class]->Count(),
			'Unterh'   => (int)floor($resources[Silver::class]->Count() * 0.05),
			'Rekruten' => (int)floor($resources[Peasant::class]->Count() * 0.05),
			'Lohn'     => 11
		];
		$this->writeData($data);
		$this->writeMarket($region->Luxuries());

		$peasant = Lemuria::Builder()->create(Peasant::class);
		$silver  = Lemuria::Builder()->create(Silver::class);
		$hash    = 1;
		foreach ($resources as $item) {
			$object = $item->getObject();
			if ($object !== $peasant || $object !== $silver) {
				$data = [
					'RESOURCE ' . $hash++,
					'type'   => Translator::COMMODITY[getClass($object)],
					'skill'  => 1,
					'number' => $item->Count()
				];
				$this->writeData($data);
			}
		}

		foreach ($region->Residents() as $unit /* @var Unit $unit */) {
			$this->writeUnit($unit);
		}
		foreach ($region->Estate() as $construction /* @var Construction $construction */) {
			$this->writeConstruction($construction);
		}
		foreach ($region->Fleet() as $vessel /* @var Vessel $vessel */) {
			$this->writeVessel($vessel);
		}
	}

	private function writeMarket(?Luxuries $luxuries): void {
		if ($luxuries) {
			$data = [
				'PREISE',
				'Balsam'    => $this->getPrice(Balsam::class, $luxuries),
				'Gewürz'    => $this->getPrice(Spice::class, $luxuries),
				'Juwel'     => $this->getPrice(Gem::class, $luxuries),
				'Myrrhe'    => $this->getPrice(Myrrh::class, $luxuries),
				'Öl'        => $this->getPrice(Oil::class, $luxuries),
				'Seide'     => $this->getPrice(Silk::class, $luxuries),
				'Weihrauch' => $this->getPrice(Olibanum::class, $luxuries)
			];
			$this->writeData($data);
		}
	}

	private function getPrice(string $class, Luxuries $luxuries): int {
		/* @var Luxury $luxury */
		$luxury = Lemuria::Builder()->create($class);
		if ($luxury === $luxuries->Offer()->Commodity()) {
			return -$luxury->Value();
		}
		return $luxuries[$class]->Price();
	}

	private function writeUnit(Unit $unit): void {
		$hp   = 'gut (' . $unit->Race()->Hitpoints() . '/' . $unit->Race()->Hitpoints() . ')';
		$data = [
			'EINHEIT ' . $unit->Id()->Id(),
			'Name'        => $unit->Name(),
			'Beschr'      => $unit->Description(),
			'Partei'      => $unit->Party()->Id()->Id(),
			'Anzahl'      => $unit->Size(),
			'Typ'         => Translator::RACE[getClass($unit->Race())],
			'Burg'        => $unit->Construction() ? $unit->Construction()->Id()->Id() : 0,
			'Schiff'      => $unit->Vessel() ? $unit->Vessel()->Id()->Id() : 0,
			'bewacht'     => $unit->IsGuarding() ? 1 : 0,
			'Kampfstatus' => Translator::BATTLE_ROW[$unit->BattleRow()] ?? 4,
			'hp'          => $hp,
			'weight'      => $unit->Weight()
		];
		if (!$unit->Construction()) {
			unset($data['Burg']);
		}
		if (!$unit->Vessel()) {
			unset($data['Schiff']);
		}
		if (!$unit->IsGuarding()) {
			unset($data['bewacht']);
		}
		$this->writeData($data);

		if (count($unit->Knowledge()) > 0) {
			$data = ['TALENTE'];
			foreach ($unit->Knowledge() as $ability /* @var Ability $ability */) {
				$talent        = Translator::TALENT[getClass($ability->Talent())];
				$data[$talent] = [$ability->Experience(), $ability->Level()];
			}
			$this->writeData($data);
		}

		if (count($unit->Inventory()) > 0) {
			$data = ['GEGENSTAENDE'];
			foreach ($unit->Inventory() as $quantity /* @var Quantity $quantity */) {
				$commodity        = Translator::COMMODITY[getClass($quantity->Commodity())];
				$data[$commodity] = $quantity->Count();
			}
			$this->writeData($data);
		}
	}

	private function writeConstruction(Construction $construction): void {
		$owner = $construction->Inhabitants()->Owner();
		$party = $owner ? $owner->Party()->Id()->Id() : 0;
		$data  = [
			'BURG ' . $construction->Id()->Id(),
			'Typ'      => Translator::BUILDING[getClass($construction->Building())],
			'Name'     => $construction->Name(),
			'Beschr'   => $construction->Description(),
			'Groesse'  => $construction->Size(),
			'Besitzer' => $owner ? $owner->Id()->Id() : 0,
			'Partei'   => $party
		];
		if (!$owner) {
			unset($data['Besitzer']);
			unset($data['Partei']);
		}
		$this->writeData($data);
	}

	private function writeVessel(Vessel $vessel): void {
		$captain  = $vessel->Passengers()->Owner();
		$party    = $captain ? $captain->Party()->Id()->Id() : 0;
		$material = $vessel->Ship()->getMaterial();
		$size     = (int)round($vessel->Completion() * $material[Wood::class]->Count());
		$coast    = Translator::COAST[$vessel->Anchor()] ?? null;
		$cargo    = 0;
		foreach ($vessel->Passengers() as $unit /* @var Unit $unit */) {
			$cargo += $unit->Weight();
		}
		$data = [
			'SCHIFF ' . $vessel->Id()->Id(),
			'Typ'      => Translator::SHIP[getClass($vessel->Ship())],
			'Name'     => $vessel->Name(),
			'Beschr'   => $vessel->Description(),
			'Groesse'  => $size,
			'cargo'    => $cargo,
			'capacity' => $vessel->Ship()->Payload(),
			'Kapitaen' => $captain ? $captain->Id()->Id() : 0,
			'Partei'   => $party,
			'Kueste'   => $coast
		];
		if (!$captain) {
			unset($data['Kapitaen']);
			unset($data['Partei']);
		}
		if ($coast === null) {
			unset($data['Kueste']);
		}
		$this->writeData($data);
	}

	private function writeTranslations(): void {
		$data = ['TRANSLATION'];
		foreach (Translator::TRANSLATIONS as $key => $translation) {
			$data[$key] = $translation;
		}
		$this->writeData($data);
	}
}
