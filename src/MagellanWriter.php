<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Magellan;

use function Lemuria\getClass;
use Lemuria\Engine\Fantasya\Availability;
use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Engine\Fantasya\Census;
use Lemuria\Engine\Fantasya\Command\Entertain;
use Lemuria\Engine\Fantasya\Effect\Hunger;
use Lemuria\Engine\Fantasya\Effect\PotionEffect;
use Lemuria\Engine\Fantasya\Effect\PotionInfluence;
use Lemuria\Engine\Fantasya\Event\Subsistence;
use Lemuria\Engine\Fantasya\Factory\Model\Observables;
use Lemuria\Engine\Fantasya\Factory\Model\SpellDetails;
use Lemuria\Engine\Fantasya\Factory\Model\TravelAtlas;
use Lemuria\Engine\Fantasya\Message\LemuriaMessage;
use Lemuria\Engine\Fantasya\Message\Region\TravelUnitMessage;
use Lemuria\Engine\Fantasya\Message\Region\TravelVesselMessage;
use Lemuria\Engine\Fantasya\Outlook;
use Lemuria\Engine\Fantasya\State;
use Lemuria\Engine\Message;
use Lemuria\Engine\Message\Filter;
use Lemuria\Engine\Message\Filter\NullFilter;
use Lemuria\Engine\Message\Section;
use Lemuria\Entity;
use Lemuria\Identifiable;
use Lemuria\Model\Fantasya\Ability;
use Lemuria\Model\Fantasya\Building\Site;
use Lemuria\Model\Fantasya\Commodity\Horse;
use Lemuria\Model\Fantasya\Commodity\Iron;
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
use Lemuria\Model\Fantasya\Commodity\Stone;
use Lemuria\Model\Fantasya\Commodity\Wood;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Continent;
use Lemuria\Model\Fantasya\Exception\JsonException;
use Lemuria\Model\Fantasya\Intelligence;
use Lemuria\Model\Fantasya\Luxuries;
use Lemuria\Model\Fantasya\Luxury;
use Lemuria\Model\Fantasya\Offer;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Potion;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Relation;
use Lemuria\Model\Fantasya\Resources;
use Lemuria\Model\Fantasya\Spell;
use Lemuria\Model\Fantasya\Talent\Magic;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Model\Fantasya\World\PartyMap;
use Lemuria\Model\World;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Renderer\Writer;
use Lemuria\Version;
use Lemuria\Version\VersionFinder;
use Lemuria\Version\VersionTag;

class MagellanWriter implements Writer
{
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
		'Build'         => '$VERSION',
		'date'          => '$DATE',
		'Runde'         => '$TURN',
		'Zeitalter'     => 1,
		'mailto'        => 'lemuria@online.de',
		'mailcmd'       => 'Lemuria Befehle'
	];

	private const ROADS = [
		World::NORTHWEST, World::NORTHEAST, World::EAST, World::SOUTHEAST, World::SOUTHWEST, World::WEST
	];

	/**
	 * @var resource|null
	 */
	protected $file;

	/**
	 * @var array(string=>mixed)
	 */
	private array $variables = [];

	private PartyMap $map;

	private Filter $filter;

	public function __construct(protected string $path) {
		$this->filter = new NullFilter();
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

	/**
	 * @throws JsonException
	 */
	public function render(Id $party): Writer {
		$this->file = fopen($this->path, 'w');
		if (!$this->file) {
			throw new \RuntimeException('Could not open file ' . $this->path . '.');
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
			throw new \RuntimeException('Could not close file ' . $this->path . '.');
		}
		$this->file = null;
		return $this;
	}

	public function getVersion(): VersionTag {
		$versionFinder = new VersionFinder(__DIR__ . '/..');
		return $versionFinder->get();
	}

	private function initVariables(): void {
		$this->variables['$DATE']    = time();
		$this->variables['$TURN']    = Lemuria::Calendar()->Round();
		$version                     = Lemuria::Version();
		$this->variables['$VERSION'] = $version[Version::GAME][0]->version ?? '1.0.0';
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
			$this->writeMessage($message);
		}
		foreach ($party->People() as $unit /* @var Unit $unit */) {
			foreach (Lemuria::Report()->getAll($unit) as $message) {
				$this->writeUnitMessage($message, $unit);
			}
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

	/**
	 * @throws JsonException
	 */
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

	/**
	 * @throws JsonException
	 */
	private function writeRegion(Region $region, string $visibility, Outlook $outlook): void {
		$coordinates  = $this->map->getCoordinates($region);
		$resources    = $region->Resources();
		$peasants     = $resources[Peasant::class]->Count();
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
				'Bauern'   => $peasants,
				'Baeume'   => $resources[Wood::class]->Count(),
				'Pferde'   => $resources[Horse::class]->Count(),
				'Steine'   => $resources[Stone::class]->Count(),
				'Eisen'    => $resources[Iron::class]->Count(),
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

		$herbage = $outlook->Census()->Party()->HerbalBook()->getHerbage($region);
		if ($herbage) {
			$data['herb']       = Translator::COMMODITY[getClass($herbage->Herb())];
			$data['herbamount'] = Translator::occurrence($herbage->Occurrence());
		}

		$this->writeData($data);
		$this->writeRoads($region);

		if ($visibility !== 'neighbour') {
			$travelled = [];
			$navigated = [];
			foreach (Lemuria::Report()->getAll($region) as $message) {
				if ($this->containsMessage($message, TravelUnitMessage::class)) {
					$travelled[] = $message;
				}
				if ($this->containsMessage($message, TravelVesselMessage::class)) {
					$navigated[] = $message;
				}
				$this->writeMessage($message);
			}

			if ($peasants > 0) {
				$castle = $intelligence->getGovernment();
				if ($castle?->Size() > Site::MAX_SIZE) {
					$this->writeMarket($region->Luxuries());
				} else {
					$offer = $region->Luxuries()?->Offer();
					if ($offer) {
						$this->writeOffer($offer);
					}
				}
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

			$this->writeEffects($region);
			$this->writeTravelled($travelled);
			$this->writeNavigated($navigated);

			if (empty($visibility)) {
				$census     = $outlook->Census();
				$party      = $census->Party();
				$isGuarding = $this->isGuarding($party, $intelligence);
				foreach ($region->Residents() as $unit /* @var Unit $unit */) {
					if ($unit->Party() === $party) {
						$this->writeUnit($unit);
					} elseif ($unit->Construction() || $unit->Vessel()) {
						$this->writeForeignUnit($unit, $census, $isGuarding);
					}
				}
				foreach ($outlook->Apparitions($region) as $unit /* @var Unit $unit */) {
					if ($unit->Party() !== $party) {
						$this->writeForeignUnit($unit, $census, $isGuarding);
					}
				}
			}

			foreach ($region->Estate() as $construction /* @var Construction $construction */) {
				$this->writeConstruction($construction, $visibility);
				foreach (Lemuria::Report()->getAll($construction) as $message) {
					$this->writeMessage($message);
				}
			}
			foreach ($region->Fleet() as $vessel /* @var Vessel $vessel */) {
				$this->writeVessel($vessel, $visibility);
				foreach (Lemuria::Report()->getAll($vessel) as $message) {
					$this->writeMessage($message);
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

	private function writeOffer(Offer $offer): void {
		$data = [
			'PREISE',
			Translator::COMMODITY[getClass($offer->Commodity())] => -$offer->Price()
		];
		$this->writeData($data);
	}

	/**
	 * @throws JsonException
	 */
	private function writeUnit(Unit $unit): void {
		$aura       = $unit->Aura();
		$disguise   = $unit->Disguise();
		$health     = $unit->Health();
		$healthCode = match (true) {
			$health <= 0.35 => 3,
			$health <= 0.7  => 2,
			$health < 1.0   => 1,
			default         => 0
		};
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
			'hp'            => Translator::HEALTH[$healthCode],
			'weight'        => $unit->Weight()
		];
		if ($this->hasHunger($unit)) {
			$data['hunger'] = 1;
		}
		if ($aura) {
			$data['Aura']    = $aura->Aura();
			$data['Auramax'] = $aura->Maximum();
		}
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

		$this->writeKnowledge($unit);
		$this->writeResources($unit->Inventory());
		$this->writeOrders($unit);
		$this->writeEffects($unit);
	}

	private function writeForeignUnit(Unit $unit, Census $census, bool $seenByGuards): void {
		$party     = $census->getParty($unit)?->Id()->Id() ?? 0;
		$isMonster = $unit->Party()->Type() === Party::MONSTER;
		$disguise  = $unit->Disguise();
		$data      = [
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
			'hp'            => Translator::HEALTH[0]
		];
		if (!$party || $isMonster) {
			unset($data['Partei']);
		}
		if ($disguise === false || $isMonster) {
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

		if ($isMonster) {
			$this->writeResources($unit->Inventory());
		} elseif ($seenByGuards) {
			$this->writeResources(new Observables($unit->Inventory()));
		}
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
		$this->writeEffects($construction);
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
			'Partei'   => $captain?->Party()->Id()->Id(),
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
		$this->writeEffects($vessel);
	}

	/**
	 * @throws JsonException
	 */
	private function writeKnowledge(Unit $unit): void {
		$knowledge = $unit->Knowledge();
		if (count($knowledge) > 0) {
			$calculus = new Calculus($unit);
			$data     = ['TALENTE'];
			foreach ($knowledge as $ability/* @var Ability $ability */) {
				$experience    = $ability->Experience();
				$ability       = $calculus->knowledge($ability->Talent());
				$talent        = Translator::TALENT[getClass($ability->Talent())];
				$data[$talent] = [$experience, $ability->Level()];
			}
			$this->writeData($data);
		}
		if (isset($knowledge[Magic::class])) {
			$this->writeSpells($unit);
		}
	}

	/**
	 * @throws JsonException
	 */
	private function writeSpells(Unit $unit): void {
		$spellBook = $unit->Party()->SpellBook();
		if (count($spellBook) > 0) {
			$data = ['SPRUECHE'];
			foreach ($spellBook as $spell /* @var Spell $spell */) {
				$details = new SpellDetails($spell);
				$data[]  = '"' . $details->Name() . '"';
			}
			$this->writeData($data);
		}
		$battleSpells = $unit->BattleSpells();
		if ($battleSpells && $battleSpells->count() > 0) {
			$data = [];
			$preparation = $battleSpells->Preparation();
			if ($preparation) {
				$details       = new SpellDetails($preparation->Spell());
				$data[]        = 'KAMPFZAUBER 0';
				$data['name']  = $details->Name();
				$data['level'] = $preparation->Level();
			}
			$combat = $battleSpells->Combat();
			if ($combat) {
				$details       = new SpellDetails($combat->Spell());
				$data[]        = 'KAMPFZAUBER 1';
				$data['name']  = $details->Name();
				$data['level'] = $combat->Level();
			}
			$this->writeData($data);
		}
	}

	private function writeResources(Resources $resources): void {
		if (count($resources) > 0) {
			$data = ['GEGENSTAENDE'];
			foreach ($resources as $quantity/* @var Quantity $quantity */) {
				$commodity        = Translator::COMMODITY[getClass($quantity->Commodity())];
				$data[$commodity] = $quantity->Count();
			}
			$this->writeData($data);
		}
	}

	private function writeOrders(Unit $unit): void {
		$orders = Lemuria::Orders()->getDefault($unit->Id());
		if (count($orders)) {
			$data = ['COMMANDS'];
			foreach ($orders as $order) {
				$data[] = '"' . $this->escape($order) . '"';
			}
			$this->writeData($data);
		}
	}

	private function writeEffects(Identifiable $entity): void {
		$effects = [];
		foreach (Lemuria::Score()->findAll($entity) as $effect) {
			if ($effect instanceof PotionEffect) {
				$effects[getClass($effect->Potion())] = $effect->Count();
			} elseif ($effect instanceof PotionInfluence) {
				foreach ($effect->getPotions() as $potion /* @var Potion $potion */) {
					$effects[getClass($potion)] = $effect->getCount($potion);
				}
			}
		}
		if ($effects) {
			$data = ['EFFECTS'];
			foreach ($effects as $effect => $count) {
				$data[] = '"' . $count . ' ' . Translator::COMMODITY[$effect] . '"';
			}
			$this->writeData($data);
		}
	}

	/**
	 * @param LemuriaMessage[] $travelled
	 */
	private function writeTravelled(array $travelled): void {
		if (!empty($travelled)) {
			$data = ['DURCHREISE'];
			foreach ($travelled as $message) {
				$data[] = '"' . $this->getTravelMessageEntity($message) . '"';
			}
			$this->writeData($data);
		}
	}

	/**
	 * @param LemuriaMessage[] $navigated
	 */
	private function writeNavigated(array $navigated): void {
		if (!empty($navigated)) {
			$data = ['DURCHSCHIFFUNG'];
			foreach ($navigated as $message) {
				$data[] = '"' . $this->getTravelMessageEntity($message) . '"';
			}
			$this->writeData($data);
		}
	}

	private function writeMessage(Message $message): void {
		if (!$this->filter->retains($message)) {
			$data = [
				'MESSAGE ' . $message->Id()->Id(),
				'type'     => $message->Section(),
				'rendered' => (string)$message
			];
			$this->writeData($data);
		}
	}

	private function writeUnitMessage(Message $message, Unit $unit): void {
		if (!$this->filter->retains($message)) {
			$data = [
				'MESSAGE ' . $message->Id()->Id(),
				'type'     => $message->Section(),
				'rendered' => (string)$message,
				'unit'     => $unit->Id()->Id(),
				'region'   => $unit->Region()->Id()->Id()
			];
			$this->writeData($data);
		}
	}

	private function writeMessagetype(): void {
		for ($section = Section::EVENT; $section <= Section::STUDY; $section++) {
			$data = [
				'MESSAGETYPE ' . $section,
				'text'    => '"$rendered"',
				'section' => Translator::SECTION[$section]
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

	private function hasHunger(Unit $unit): bool {
		$effect = new Hunger(new State());
		return Lemuria::Score()->find($effect->setUnit($unit)) instanceof Hunger;
	}

	private function getPrice(string $class, Luxuries $luxuries): int {
		/* @var Luxury $luxury */
		$luxury = Lemuria::Builder()->create($class);
		$offer  = $luxuries->Offer();
		if ($luxury === $offer->Commodity()) {
			return -$offer->Price();
		}
		return $luxuries[$class]->Price();
	}

	private function isGuarding(Party $party, Intelligence $intelligence): bool {
		foreach ($intelligence->getGuards() as $unit /* @var Unit $unit */) {
			if ($unit->Party() === $party) {
				return true;
			}
		}
		return false;
	}

	private function containsMessage(LemuriaMessage $message, string $class): bool {
		if ($message->MessageClass() === $class) {
			return !$this->filter->retains($message);
		}
		return false;
	}

	private function getTravelMessageEntity(LemuriaMessage $message): string {
		$entity = Entity::from($message->getParameter());
		return $entity->Name() . ' (' . $entity->Id() . ')';
	}
}
