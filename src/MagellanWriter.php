<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Magellan;

use function Lemuria\getClass;
use Lemuria\Engine\Fantasya\Availability;
use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Engine\Fantasya\Census;
use Lemuria\Engine\Fantasya\Command\Entertain;
use Lemuria\Engine\Fantasya\Event\Subsistence;
use Lemuria\Engine\Fantasya\Factory\Model\TravelAtlas;
use Lemuria\Engine\Fantasya\Outlook;
use Lemuria\Engine\Message;
use Lemuria\Engine\Message\Filter;
use Lemuria\Engine\Message\Filter\NullFilter;
use Lemuria\Model\Fantasya\Ability;
use Lemuria\Model\Fantasya\Building\Site;
use Lemuria\Model\Fantasya\Commodity\Luxury\Balsam;
use Lemuria\Model\Fantasya\Commodity\Luxury\Fur;
use Lemuria\Model\Fantasya\Commodity\Luxury\Gem;
use Lemuria\Model\Fantasya\Commodity\Luxury\Myrrh;
use Lemuria\Model\Fantasya\Commodity\Luxury\Oil;
use Lemuria\Model\Fantasya\Commodity\Luxury\Olibanum;
use Lemuria\Model\Fantasya\Commodity\Luxury\Silk;
use Lemuria\Model\Fantasya\Commodity\Luxury\Spice;
use Lemuria\Model\Fantasya\Commodity\Peasant;
use Lemuria\Model\Fantasya\Commodity\Silver;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Continent;
use Lemuria\Model\Fantasya\Intelligence;
use Lemuria\Model\Fantasya\Luxuries;
use Lemuria\Model\Fantasya\Luxury;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Relation;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Model\Fantasya\World\PartyMap;
use Lemuria\Model\World;
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
		'VERSION 69',
		'charset'       => 'UTF-8',
		'locale'        => 'de',
		'Spiel'         => 'Lemuria',
		'Konfiguration' => 'Standard',
		'Basis'         => 36,
		'noskillpoints' => 0,
		'max_units'     => 1000,
		'Koordinaten'   => 'Hex',
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

	private const ROADS = [
		World::NORTHWEST, World::NORTHEAST, World::EAST, World::SOUTHEAST, World::SOUTHWEST, World::WEST
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
		$census    = new Census($party);
		$outlook   = new Outlook($census);
		$continent = Continent::get(new Id(1));
		$this->writeParties($outlook);
		$this->writeIsland($continent);
		$this->writeRegions($outlook);
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

	private function writeParties(Outlook $outlook): void {
		$census = $outlook->Census();
		$party  = $census->Party();

		$this->writeParty($party);
		$acquaintances = $party->Diplomacy()->Acquaintances();

		$parties = [];
		foreach ($census->getAtlas() as $region /* @var Region $region */) {
			foreach ($outlook->Apparitions($region) as $unit /* @var Unit $unit */) {
				$foreign = $census->getParty($unit);
				if ($foreign && $foreign !== $party) {
					$id           = $foreign->Id()->Id();
					$parties[$id] = $foreign;
				}
			}
		}

		foreach ($parties as $id => $foreign) {
			$this->writeForeignParty($foreign, $acquaintances->has(new Id($id)));
		}
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
			'email'               => $party->Banner(),
			'banner'              => $party->Description(),
		];
		$this->writeData($data);

		foreach ($party->Diplomacy() as $relation) {
			$this->writeAlliance($relation);
		}
		foreach (Lemuria::Report()->getAll($party) as $message) {
			$this->writeMessage($message, self::MESSAGE_EVENT);
		}
	}

	private function writeForeignParty(Party $party, bool $isKnown): void {
		$data = [
			'PARTEI ' . $party->Id()->Id(),
			'locale'     => self::HEADER['locale'],
			'age'        => 1,
			'Typ'        => Translator::RACE[getClass($party->Race())],
			'Parteiname' => $party->Name(),
			'email'      => $party->Banner(),
			'banner'     => $party->Description(),
		];
		if (!$isKnown) {
			unset($data['Typ']);
			//unset($data['Parteiname']);
			unset($data['banner']);
		}
		$this->writeData($data);
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

	private function writeIsland(Continent $continent): void {
		$data = [
			'ISLAND ' . $continent->Id()->Id(),
			'Name'   => $continent->Name(),
			'Beschr' => $continent->Description()
		];
		$this->writeData($data);
	}

	protected function writeRegions(Outlook $outlook): void {
		$atlas = new TravelAtlas($outlook->Census()->Party());
		foreach ($atlas->forRound(Lemuria::Calendar()->Round() - 1) as $region /* @var Region $region */) {
			$visibility = match ($atlas->getVisibility($region)) {
				TravelAtlas::WITH_UNIT => '',
				TravelAtlas::TRAVELLED => 'travel',
				default                => 'neighbour'
			};
			$this->writeRegion($region, $visibility, $outlook);
		}
	}

	private function writeRegion(Region $region, string $visibility, Outlook $outlook): void {
		$coordinates  = $this->map->getCoordinates($region);
		$resources    = $region->Resources();
		$intelligence = new Intelligence($region);

		if (empty($visibility)) {
			$availability = new Availability($region);
			$data         = [
				'REGION ' . $coordinates->X() . ' ' . $coordinates->Y(),
				'id'       => $region->Id()->Id(),
				'Name'     => $region->Name(),
				'Terrain'  => Translator::LANDSCAPE[getClass($region->Landscape())],
				'Insel'    => 1,
				'Beschr'   => $region->Description(),
				'Bauern'   => $resources[Peasant::class]->Count(),
				'Silber'   => $resources[Silver::class]->Count(),
				'Unterh'   => (int)floor($resources[Silver::class]->Count() * Entertain::QUOTA),
				'Rekruten' => $availability->getResource(Peasant::class)->Count(),
				'Lohn'     => $intelligence->getWage(Subsistence::WAGE)
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
		$this->writeRoads($region);

		if ($visibility !== 'neighbour') {
			foreach (Lemuria::Report()->getAll($region) as $message) {
				$this->writeMessage($message, self::MESSAGE_EVENT);
			}

			$castle = $intelligence->getGovernment();
			if ($castle?->Size() > Site::MAX_SIZE) {
				$this->writeMarket($region->Luxuries());
			}

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

			if (empty($visibility)) {
				$census = $outlook->Census();
				$party  = $census->Party();
				foreach ($region->Residents() as $unit /* @var Unit $unit */) {
					if ($unit->Party() === $party) {
						$this->writeUnit($unit);
						foreach (Lemuria::Report()->getAll($unit) as $message) {
							$this->writeMessage($message, self::MESSAGE_PRODUCTION);
						}
					} elseif ($unit->Construction() || $unit->Vessel()) {
						$this->writeForeignUnit($unit, $census);
					}
				}
				foreach ($outlook->Apparitions($region) as $unit /* @var Unit $unit */) {
					if ($unit->Party() !== $party) {
						$this->writeForeignUnit($unit, $census);
					}
				}
			}

			foreach ($region->Estate() as $construction /* @var Construction $construction */) {
				$this->writeConstruction($construction, $visibility);
				foreach (Lemuria::Report()->getAll($construction) as $message) {
					$this->writeMessage($message, self::MESSAGE_ECONOMY);
				}
			}
			foreach ($region->Fleet() as $vessel /* @var Vessel $vessel */) {
				$this->writeVessel($vessel, $visibility);
				foreach (Lemuria::Report()->getAll($vessel) as $message) {
					$this->writeMessage($message, self::MESSAGE_ECONOMY);
				}
			}
		}
	}

	private function writeRoads(Region $region): void {
		$roads = $region->Roads();
		foreach (self::ROADS as $road => $direction) {
			if ($region->hasRoad($direction)) {
				$percent = 100;
			} elseif ($roads && $roads[$direction] > 0.0) {
				$percent = (int)round(100.0 * $roads[$direction]);
			} else {
				continue;
			}
			$data = [
				'GRENZE ' . $region->Id()->Id() . $road,
				'typ'      => 'Straße',
				'richtung' => $road,
				'prozent'  => $percent
			];
			$this->writeData($data);
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
				'Pelz'      => $this->getPrice(Fur::class, $luxuries),
				'Seide'     => $this->getPrice(Silk::class, $luxuries),
				'Weihrauch' => $this->getPrice(Olibanum::class, $luxuries)
			];
			$this->writeData($data);
		}
	}

	private function writeUnit(Unit $unit): void {
		$health   = Translator::HEALTH[0];
		$hp       = $health . ' (' . $unit->Race()->Hitpoints() . '/' . $unit->Race()->Hitpoints() . ')';
		$disguise = $unit->Disguise();
		$data     = [
			'EINHEIT ' . $unit->Id()->Id(),
			'Name'          => $unit->Name(),
			'Beschr'        => $unit->Description(),
			'Partei'        => $unit->Party()->Id()->Id(),
			'Parteitarnung' => $disguise !== false ? 1 : 0,
			'Anderepartei'  => $disguise ? $disguise->Id()->Id() : 0,
			'Anzahl'        => $unit->Size(),
			'Typ'           => Translator::RACE[getClass($unit->Race())],
			'Burg'          => $unit->Construction()?->Id()->Id(),
			'Schiff'        => $unit->Vessel()?->Id()->Id(),
			'bewacht'       => $unit->IsGuarding() ? 1 : 0,
			'Kampfstatus'   => Translator::BATTLE_ROW[$unit->BattleRow()] ?? 4,
			'hp'            => $hp,
			'weight'        => $unit->Weight()
		];
		if ($disguise === false) {
			unset($data['Parteitarnung']);
			unset($data['Anderepartei']);
		}
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
			$calculus = new Calculus($unit);
			$data     = ['TALENTE'];
			foreach ($unit->Knowledge() as $ability/* @var Ability $ability */) {
				$experience    = $ability->Experience();
				$ability       = $calculus->knowledge($ability->Talent());
				$talent        = Translator::TALENT[getClass($ability->Talent())];
				$data[$talent] = [$experience, $ability->Level()];
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

	private function writeForeignUnit(Unit $unit, Census $census): void {
		$party    = $census->getParty($unit)?->Id()->Id() ?? 0;
		$disguise = $unit->Disguise();
		$data     = [
			'EINHEIT ' . $unit->Id()->Id(),
			'Name'          => $unit->Name(),
			'Beschr'        => $unit->Description(),
			'Partei'        => $party,
			'Parteitarnung' => $disguise !== false ? 1 : 0,
			'Anderepartei'  => $disguise ? $disguise->Id()->Id() : 0,
			'Verraeter'     => $disguise === $census->Party() ? 1 : 0,
			'Anzahl'        => $unit->Size(),
			'Typ'           => Translator::RACE[getClass($unit->Race())],
			'Burg'          => $unit->Construction()?->Id()->Id(),
			'Schiff'        => $unit->Vessel()?->Id()->Id(),
			'bewacht'       => $unit->IsGuarding() ? 1 : 0,
			'hp'            => 'gut'
		];
		if (!$party) {
			unset($data['Partei']);
		}
		if ($disguise === false) {
			unset($data['Parteitarnung']);
			unset($data['Anderepartei']);
			unset($data['Verraeter']);
		}
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
	}

	private function writeConstruction(Construction $construction, string $visibility): void {
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
		if (!empty($visibility)) {
			unset($data['Besitzer']);
		}
		$this->writeData($data);
	}

	private function writeVessel(Vessel $vessel, string $visibility): void {
		$ship       = $vessel->Ship();
		$size       = (int)round($vessel->Completion() * $ship->Wood());
		$coast      = Translator::COAST[$vessel->Anchor()] ?? null;
		$passengers = $vessel->Passengers();
		$captain    = $passengers->Owner();
		$cargo      = 0;
		foreach ($passengers as $unit /* @var Unit $unit */) {
			$cargo += $unit->Weight();
		}
		$data = [
			'SCHIFF ' . $vessel->Id()->Id(),
			'Typ'      => Translator::SHIP[getClass($ship)],
			'Name'     => $vessel->Name(),
			'Beschr'   => $vessel->Description(),
			'Groesse'  => $size,
			'Schaden'  => (int)round(100.0 * (1.0 - $vessel->Completion())),
			'cargo'    => $cargo,
			'capacity' => $ship->Payload(),
			'Kapitaen' => $captain?->Id()->Id(),
			'Partei'   => $captain->Party()->Id()->Id(),
			'Kueste'   => $coast
		];
		if (!$captain) {
			unset($data['Kapitaen']);
			unset($data['Partei']);
		}
		if (!empty($visibility)) {
			unset($data['cargo']);
			unset($data['Kapitaen']);
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

	private function escape(string $string): string {
		return str_replace('"', '\\"', $string);
	}

	private function getPrice(string $class, Luxuries $luxuries): int {
		/* @var Luxury $luxury */
		$luxury = Lemuria::Builder()->create($class);
		if ($luxury === $luxuries->Offer()->Commodity()) {
			return -$luxury->Value();
		}
		return $luxuries[$class]->Price();
	}
}
