<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Magellan;

use function Lemuria\getClass;
use Lemuria\Engine\Fantasya\Outlook;
use Lemuria\Engine\Message;
use Lemuria\Engine\Message\Filter;
use Lemuria\Engine\Message\Filter\NullFilter;
use Lemuria\Model\Fantasya\Ability;
use Lemuria\Model\Fantasya\Commodity\Luxury\Balsam;
use Lemuria\Model\Fantasya\Commodity\Luxury\Gem;
use Lemuria\Model\Fantasya\Commodity\Luxury\Myrrh;
use Lemuria\Model\Fantasya\Commodity\Luxury\Oil;
use Lemuria\Model\Fantasya\Commodity\Luxury\Olibanum;
use Lemuria\Model\Fantasya\Commodity\Luxury\Silk;
use Lemuria\Model\Fantasya\Commodity\Luxury\Spice;
use Lemuria\Model\Fantasya\Commodity\Peasant;
use Lemuria\Model\Fantasya\Commodity\Silver;
use Lemuria\Model\Fantasya\Commodity\Wood;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Luxuries;
use Lemuria\Model\Fantasya\Luxury;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Party\Census;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Relation;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Model\Fantasya\World\PartyMap;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Renderer\Writer;

class MagellanWriter implements Writer
{
	private const MESSAGE_DEFAULT = 'Meldungen';

	private const MESSAGE_EVENT = 'events';

	private const MESSAGE_ERROR = 'errors';

	private const MESSAGE_PRODUCTION = 'production';

	private const MESSAGE_ECONOMY = 'economy';

	private const MESSAGE_MAGIC = 'magic';

	private const MESSAGE_STUDY = 'study';

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

	private const MESSAGETYPES = [
		self::MESSAGE_DEFAULT => 1, self::MESSAGE_ECONOMY    => 2, self::MESSAGE_ERROR => 3, self::MESSAGE_EVENT => 4,
		self::MESSAGE_MAGIC   => 5, self::MESSAGE_PRODUCTION => 6, self::MESSAGE_STUDY => 7
	];

	/**
	 * @var resource|null
	 */
	private $file;

	/**
	 * @var array(string=>mixed)
	 */
	private array $variables = [];

	private PartyMap $map;

	private Filter $filter;

	public function __construct(string $path) {
		$this->filter = new NullFilter();
		$this->file   = fopen($path, 'w');
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

	public function setFilter(Filter $filter): Writer {
		$this->filter = $filter;
		return $this;
	}

	public function render(Id $party): Writer {
		if (!$this->file) {
			throw new \RuntimeException('File has been closed.');
		}

		$this->writeHeader();

		$party     = Party::get($party);
		$this->map = new PartyMap(Lemuria::World(), $party);
		$this->writeParty($party);
		$this->writeRegions($party);
		$this->writeMessagetype();
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
						$line = '"' . $this->escape($value) . '";' . $key;
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
		];
		$this->writeData($data);

		foreach ($party->Diplomacy() as $relation) {
			$this->writeAlliance($relation);
		}
		foreach (Lemuria::Report()->getAll($party) as $message) {
			$this->writeMessage($message, self::MESSAGE_EVENT);
		}
		$this->writeIsland();
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

	private function writeIsland(): void {
		$data = [
			'ISLAND 1',
			'Name'   => 'Kontinent Lemuria',
			'Beschr' => 'Dies ist der Hauptkontinent Lemuria.'
		];
		$this->writeData($data);
	}

	protected function writeRegions(Party $party): void {
		$regions = [];
		$census  = new Census($party);
		foreach ($census->getAtlas() as $id => $region /* @var Region $region */) {
			$regions[$id] = $region;
		}

		$neighbours = [];
		$outlook    = new Outlook($census);
		foreach ($regions as $id => $region) {
			foreach ($outlook->Panorama($region) as $neighbour /* @var Region $neighbour */) {
				$nid = $neighbour->Id()->Id();
				if (!isset($regions[$nid]) && !isset($neighbours[$nid])) {
					$neighbours[$nid] = $neighbour;
				}
			}
		}

		$ids = array_fill_keys(array_keys($regions), '') + array_fill_keys(array_keys($neighbours), 'neighbour');
		ksort($ids);
		foreach ($ids as $id => $visibility) {
			$region = empty($visibility) ? $regions[$id] : $neighbours[$id];
			$this->writeRegion($region, $visibility, $party);
		}
	}

	private function writeRegion(Region $region, string $visibility, Party $party): void {
		$coordinates = $this->map->getCoordinates($region);
		$resources   = $region->Resources();

		if (empty($visibility)) {
			$data = [
				'REGION ' . $coordinates->X() . ' ' . $coordinates->Y(),
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
		} else {
			$data = [
				'REGION ' . $coordinates->X() . ' ' . $coordinates->Y(),
				'id'         => $region->Id()->Id(),
				'Name'       => $region->Name(),
				'Terrain'    => Translator::LANDSCAPE[getClass($region->Landscape())],
				'Insel'      => 1,
				'visibility' => $visibility
			];
		}
		$this->writeData($data);

		if (empty($visibility)) {
			foreach (Lemuria::Report()->getAll($region) as $message) {
				$this->writeMessage($message, self::MESSAGE_EVENT);
			}
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

			foreach ($region->Residents() as $unit/* @var Unit $unit */) {
				$isOwnUnit = $unit->Party() === $party;
				$this->writeUnit($unit, $isOwnUnit);
				if ($isOwnUnit) {
					foreach (Lemuria::Report()->getAll($unit) as $message) {
						$this->writeMessage($message, self::MESSAGE_PRODUCTION);
					}
				}
			}
			foreach ($region->Estate() as $construction/* @var Construction $construction */) {
				$this->writeConstruction($construction);
				foreach (Lemuria::Report()->getAll($construction) as $message) {
					$this->writeMessage($message, self::MESSAGE_ECONOMY);
				}
			}
			foreach ($region->Fleet() as $vessel/* @var Vessel $vessel */) {
				$this->writeVessel($vessel);
				foreach (Lemuria::Report()->getAll($vessel) as $message) {
					$this->writeMessage($message, self::MESSAGE_ECONOMY);
				}
			}
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

	private function writeUnit(Unit $unit, bool $isOwnUnit): void {
		$hp   = $isOwnUnit ? 'gut (' . $unit->Race()->Hitpoints() . '/' . $unit->Race()->Hitpoints() . ')' : 'gut';
		$data = [
			'EINHEIT ' . $unit->Id()->Id(),
			'Name'        => $unit->Name(),
			'Beschr'      => $unit->Description(),
			'Partei'      => $unit->Party()->Id()->Id(),
			'Anzahl'      => $unit->Size(),
			'Typ'         => Translator::RACE[getClass($unit->Race())],
			'Burg'        => $unit->Construction()?->Id()->Id(),
			'Schiff'      => $unit->Vessel()?->Id()->Id(),
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
		if (!$isOwnUnit) {
			unset($data['Kampfstatus']);
			unset($data['weight']);
		}
		$this->writeData($data);

		if ($isOwnUnit) {
			if (count($unit->Knowledge()) > 0) {
				$data = ['TALENTE'];
				foreach ($unit->Knowledge() as $ability/* @var Ability $ability */) {
					$talent        = Translator::TALENT[getClass($ability->Talent())];
					$data[$talent] = [$ability->Experience(), $ability->Level()];
				}
				$this->writeData($data);
			}

			if (count($unit->Inventory()) > 0) {
				$data = ['GEGENSTAENDE'];
				foreach ($unit->Inventory() as $quantity/* @var Quantity $quantity */) {
					$commodity        = Translator::COMMODITY[getClass($quantity->Commodity())];
					$data[$commodity] = $quantity->Count();
				}
				$this->writeData($data);
			}

			$orders = Lemuria::Orders()->getDefault($unit->Id());
			if (count($orders)) {
				$data = ['COMMANDS'];
				foreach ($orders as $order) {
					$data[] = '"' . $this->escape($order) . '"';
				}
				$this->writeData($data);
			}
		}
	}

	private function writeConstruction(Construction $construction): void {
		$owner = $construction->Inhabitants()->Owner();
		$data  = [
			'BURG ' . $construction->Id()->Id(),
			'Typ'      => Translator::BUILDING[getClass($construction->Building())],
			'Name'     => $construction->Name(),
			'Beschr'   => $construction->Description(),
			'Groesse'  => $construction->Size(),
			'Besitzer' => $owner?->Id()->Id(),
			'Partei'   => $owner?->Party()->Id()->Id()
		];
		if (!$owner) {
			unset($data['Besitzer']);
			unset($data['Partei']);
		}
		$this->writeData($data);
	}

	private function writeVessel(Vessel $vessel): void {
		$captain  = $vessel->Passengers()->Owner();
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
			'Kapitaen' => $captain?->Id()->Id(),
			'Partei'   => $captain->Party()->Id()->Id(),
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

	private function writeMessage(Message $message, string $section = self::MESSAGE_DEFAULT): void {
		if (!$this->filter->retains($message)) {
			$data = [
				'MESSAGE ' . $message->Id()->Id(),
				'type'     => self::MESSAGETYPES[$section] ?? self::MESSAGETYPES[self::MESSAGE_DEFAULT],
				'rendered' => (string)$message
			];
			$this->writeData($data);
		}
	}

	private function writeMessagetype(): void {
		foreach (self::MESSAGETYPES as $section => $id) {
			$data = [
				'MESSAGETYPE ' . $id,
				'text'    => '"$rendered"',
				'section' => $section
			];
			$this->writeData($data);
		}
	}

	private function writeTranslations(): void {
		$data = ['TRANSLATION'];
		foreach (Translator::TRANSLATIONS as $key => $translation) {
			$data[$key] = $translation;
		}
		$this->writeData($data);
	}

	private function escape(string $string): string {
		return str_replace('"', '\\"', $string);
	}
}
