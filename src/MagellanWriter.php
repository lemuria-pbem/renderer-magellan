<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Magellan;

use function Lemuria\getClass;
use function Lemuria\number;
use Lemuria\Engine\Combat\Battle;
use Lemuria\Engine\Fantasya\Availability;
use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Engine\Fantasya\Census;
use Lemuria\Engine\Fantasya\Combat\Log\Message as BattleMessage;
use Lemuria\Engine\Fantasya\Command\Entertain;
use Lemuria\Engine\Fantasya\Context;
use Lemuria\Engine\Fantasya\Effect\Hunger;
use Lemuria\Engine\Fantasya\Effect\PotionEffect;
use Lemuria\Engine\Fantasya\Effect\PotionInfluence;
use Lemuria\Engine\Fantasya\Effect\Unmaintained;
use Lemuria\Engine\Fantasya\Factory\GrammarTrait;
use Lemuria\Engine\Fantasya\Factory\Model\Observables;
use Lemuria\Engine\Fantasya\Factory\Model\SpellDetails;
use Lemuria\Engine\Fantasya\Factory\Model\TravelAtlas;
use Lemuria\Engine\Fantasya\Factory\Model\Visibility;
use Lemuria\Engine\Fantasya\Factory\Model\Wage;
use Lemuria\Engine\Fantasya\Factory\RealmTrait;
use Lemuria\Engine\Fantasya\Factory\SpellParser;
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
use Lemuria\Model\Coordinates;
use Lemuria\Model\Domain;
use Lemuria\Model\Fantasya\BattleSpell;
use Lemuria\Model\Fantasya\Building\Canal;
use Lemuria\Model\Fantasya\Building\Market;
use Lemuria\Model\Fantasya\Building\Port;
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
use Lemuria\Model\Fantasya\Commodity\Potion\AbstractPotion;
use Lemuria\Model\Fantasya\Commodity\Silver;
use Lemuria\Model\Fantasya\Commodity\Stone;
use Lemuria\Model\Fantasya\Commodity\Wood;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Continent;
use Lemuria\Model\Fantasya\Exception\JsonException;
use Lemuria\Model\Fantasya\Exception\WorldMapException;
use Lemuria\Model\Fantasya\Extension\Duty;
use Lemuria\Model\Fantasya\Extension\Fee;
use Lemuria\Model\Fantasya\Extension\Market as MarketExtension;
use Lemuria\Model\Fantasya\Herb;
use Lemuria\Model\Fantasya\Intelligence;
use Lemuria\Model\Fantasya\Luxuries;
use Lemuria\Model\Fantasya\Luxury;
use Lemuria\Model\Fantasya\Offer;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Model\Fantasya\Potion;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Relation;
use Lemuria\Model\Fantasya\Resources;
use Lemuria\Model\Fantasya\Talent\Alchemy;
use Lemuria\Model\Fantasya\Talent\Magic;
use Lemuria\Model\Fantasya\Treasury;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Model\Fantasya\World\PartyMap;
use Lemuria\Model\World\Direction;
use Lemuria\Model\World\Geometry;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Renderer\PathFactory;
use Lemuria\Renderer\Writer;
use Lemuria\Version\Module;
use Lemuria\Version\VersionFinder;
use Lemuria\Version\VersionTag;

class MagellanWriter implements Writer
{
	use GrammarTrait;
	use RealmTrait;

	private const HEADER = [
		'VERSION 69',
		'charset'       => 'UTF-8',
		'locale'        => 'de',
		'Spiel'         => '$GAME',
		'Konfiguration' => 'Standard',
		'Basis'         => 36,
		'noskillpoints' => 0,
		'max_units'     => '$MAX_UNITS',
		'Koordinaten'   => 'Hex',
		'Build'         => '$VERSION',
		'date'          => '$DATE',
		'Runde'         => '$TURN',
		'Zeitalter'     => '$ERA',
		'mailto'        => '$MAILTO',
		'mailcmd'       => '$MAILCMD'
	];

	private const ROADS = [
		Direction::Northwest, Direction::Northeast, Direction::East, Direction::Southeast, Direction::Southwest, Direction::West
	];

	/**
	 * @var resource|null
	 */
	protected $file;

	/**
	 * @var array<string, mixed>
	 */
	private array $variables = [];

	private readonly PartyMap $map;

	private Filter $filter;

	private Statistics $statistics;

	private Context $context;

	public function __construct(protected PathFactory $pathFactory) {
		$this->filter     = new NullFilter();
		$this->statistics = new Statistics();
		$this->context    = new Context(State::getInstance());
		$this->initDictionary();
		$this->initVariables();
	}

	public function __destruct() {
		if ($this->file) {
			$this->close();
		}
	}

	public function setHeader(Header $header): MagellanWriter {
		$this->variables['$GAME'] = $header->Game();
		$this->variables['$MAX_UNITS'] = $header->MaxUnits();
		$this->variables['$ERA'] = $header->Era();
		$this->variables['$MAILTO'] = $header->MailTo()->Address();
		$this->variables['$MAILCMD'] = $header->MailTo()->Command();
		return $this;
	}

	public function setFilter(Filter $filter): Writer {
		$this->filter = $filter;
		return $this;
	}

	/**
	 * @throws JsonException
	 */
	public function render(Id $entity): Writer {
		$party = Party::get($entity);
		$this->context->setParty($party);
		$path       = $this->pathFactory->getPath($this, $party);
		$this->file = fopen($path, 'w');
		if (!$this->file) {
			throw new \RuntimeException('Could not open file ' . $path . '.');
		}

		$this->writeHeader();

		$this->map = new PartyMap(Lemuria::World(), $party);
		$census    = new Census($party);
		$outlook   = new Outlook($census);
		$this->writeParties($outlook);
		$this->writeMagic($party);
		$this->writeAlchemy($party);
		$this->writeIslands();
		$this->writeRegions($outlook);
		$this->writeMessagetype();
		$this->writeTranslations();

		if (!fclose($this->file)) {
			throw new \RuntimeException('Could not close file ' . $path . '.');
		}
		$this->file = null;
		return $this;
	}

	public function getVersion(): VersionTag {
		$versionFinder = new VersionFinder(__DIR__ . '/..');
		return $versionFinder->get();
	}

	/**
	 * Write the Magellan VERSION header.
	 */
	protected function writeHeader(): void {
		$this->writeData(self::HEADER);
	}

	protected function writeIslands(): void {
		foreach (Continent::all() as $continent) {
			$data = [
				'ISLAND ' . $continent->Id()->Id(),
				'Name'   => $continent->Name(),
				'Beschr' => $continent->Description()
			];
			$this->writeData($data);
		}
	}

	protected function writeData(array $data): void {
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

	protected function writeMarket(?Luxuries $luxuries): void {
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

	protected function writeOffer(Offer $offer): void {
		$data = [
			'PREISE',
			$this->translateSingleton($offer->Commodity()) => -$offer->Price()
		];
		$this->writeData($data);
	}

	private function initVariables(): void {
		$this->variables['$DATE']    = time();
		$this->variables['$TURN']    = Lemuria::Calendar()->Round();
		$version                     = Lemuria::Version();
		$this->variables['$VERSION'] = $version[Module::Game][0]->version ?? '1.0.0';
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

	private function writeParties(Outlook $outlook): void {
		$census = $outlook->Census();
		$party  = $census->Party();
		$this->writeParty($party);

		$parties = [];
		foreach ($census->getAtlas() as $region) {
			$this->collectParties($outlook, $region, $parties);
		}
		$acquaintances = $party->Diplomacy()->Acquaintances();
		foreach ($parties as $id => $foreign) {
			$this->writeForeignParty($foreign, $acquaintances->has(new Id($id)));
		}
	}

	private function writeParty(Party $party): void {
		$data = [
			'PARTEI ' . $party->Id()->Id(),
			'locale'              => self::HEADER['locale'],
			'age'                 => Lemuria::Calendar()->Round() - $party->Round() + 1,
			'Optionen'            => 1 + 2 + 8 + 64 + 256 + 512,
			'Punkte'              => 0,
			'Punktedurchschnitt'  => 0,
			'Typ'                 => $this->translateSingleton($party->Race()),
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
		foreach ($party->People() as $unit) {
			foreach (Lemuria::Report()->getAll($unit) as $message) {
				$this->writeUnitMessage($message, $unit);
			}
		}
		foreach (Lemuria::Hostilities()->findFor($party) as $battleLog) {
			$this->writeBattle($battleLog);
		}
	}

	private function writeForeignParty(Party $party, bool $isKnown): void {
		$data = [
			'PARTEI ' . $party->Id()->Id(),
			'locale'     => self::HEADER['locale'],
			'age'        => 1,
			'Typ'        => $this->translateSingleton($party->Race()),
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

	/**
	 * @throws JsonException
	 */
	private function writeMagic(Party $party): void {
		$id = 1;
		foreach ($party->SpellBook() as $spell) {
			$details = new SpellDetails($spell);
			$data = [
				'ZAUBER ' . $id++,
				'name'  => $details->Name(),
				'level' => $spell->Difficulty(),
				'rank'  => $spell->Order(),
				'info'  => implode(' ', $details->Description())
			];
			if ($spell instanceof BattleSpell) {
				$data['class'] = Translator::SPELL[$spell->Phase()->value];
			} else {
				$data['class'] = Translator::SPELL[''];
			}
			if (SpellParser::getSyntax($spell) === SpellParser::LEVEL_AND_TARGET) {
				$data['syntax'] = 'u';
			}
			$this->writeData($data);

			$data = [
				'KOMPONENTEN',
				'aura' => $spell->Aura() . ' ' . (int)$spell->IsIncremental()
			];
			$this->writeData($data);
		}
	}

	private function writeAlchemy(Party $party): void {
		$alchemy = Lemuria::Builder()->create(Alchemy::class);
		$level   = 0;
		foreach ($party->People() as $unit) {
			$calculus  = new Calculus($unit);
			$level = max($level, $calculus->knowledge($alchemy)->Level());
		}
		$level = (int)floor($level / 2);

		$id = 1;
		foreach (AbstractPotion::all() as $potion /** @var Potion $potion */) {
			$difficulty = $potion->Level();
			if ($difficulty > $level) {
				continue;
			}

			$class = getClass($potion);
			$data  = [
				'TRANK ' . $id++,
				'Name'   => $this->translateSingleton($class),
				'Stufe'  => $potion->Level(),
				'Beschr' => Translator::ALCHEMY[$class]
			];
			$this->writeData($data);

			$data = [
				'ZUTATEN'
			];
			foreach ($potion->getMaterial() as $quantity) {
				$data[] = '"' . $this->translateSingleton($quantity->Commodity()) . '"';
			}
			$this->writeData($data);
		}
	}

	/**
	 * @throws JsonException
	 */
	private function writeRegions(Outlook $outlook): void {
		$atlas = new TravelAtlas($outlook->Census()->Party());
		try {
			$withBeyond = $this->map->Geometry() === Geometry::Spherical;
		} catch (WorldMapException) {
			$withBeyond = false;
		}
		$visibilities = [Visibility::Travelled, Visibility::WithUnit];

		$beyond = [];
		foreach ($atlas->forRound(Lemuria::Calendar()->Round() - 1) as $region) {
			$visibility = $atlas->getVisibility($region);
			$this->writeRegion($region, $visibility, $outlook);
			if ($withBeyond && $this->map->isEdge($region) && in_array($visibility, $visibilities)) {
				$regions = $this->map->getBeyond($region);
				$n       = count($regions);
				for ($i = 0; $i < $n; $i++) {
					$region = $regions->getLocation($i);
					$beyond[$region->Id()->Id()] = [$regions->getCoordinates($i), $region];
				}
			}
		}
		$this->writeBeyond($beyond);
	}

	/**
	 * @throws JsonException
	 */
	private function writeRegion(Region $region, Visibility $visibility, Outlook $outlook): void {
		$coordinates  = $this->map->getCoordinates($region);
		$resources    = $region->Resources();
		$peasants     = $resources[Peasant::class]->Count();
		$intelligence = $this->context->getIntelligence($region);

		$magellanVisibility = match ($visibility) {
			Visibility::WithUnit, Visibility::Farsight => '',
			Visibility::Travelled                       => 'travel',
			Visibility::Lighthouse                      => 'lighthouse',
			default                                     => 'neighbour'
		};

		if (empty($magellanVisibility)) {
			$availability = new Availability($region);
			$wage         = new Wage($this->calculateInfrastructure($region));
			$data         = [
				'REGION ' . $coordinates->X() . ' ' . $coordinates->Y() . ' 0',
				'id'       => $region->Id()->Id(),
				'Name'     => $region->Name(),
				'Terrain'  => $this->translateSingleton($region->Landscape()),
				'Insel'    => $region->Continent()->Id()->Id(),
				'Beschr'   => $this->compileRegionDescription($region),
				'Bauern'   => $peasants,
				'Baeume'   => $resources[Wood::class]->Count(),
				'Pferde'   => $resources[Horse::class]->Count(),
				'Steine'   => $resources[Stone::class]->Count(),
				'Eisen'    => $resources[Iron::class]->Count(),
				'Silber'   => $resources[Silver::class]->Count(),
				'Unterh'   => (int)floor($resources[Silver::class]->Count() * Entertain::QUOTA),
				'Rekruten' => $availability->getResource(Peasant::class)->Count(),
				'Lohn'     => $wage->getWage()
			];
		} else {
			$data = [
				'REGION ' . $coordinates->X() . ' ' . $coordinates->Y(),
				'id'         => $region->Id()->Id(),
				'Name'       => $region->Name(),
				'Terrain'    => $this->translateSingleton($region->Landscape()),
				'Insel'      => $region->Continent()->Id()->Id(),
				'visibility' => $magellanVisibility
			];
		}

		$herbage = $outlook->Census()->Party()->HerbalBook()->getHerbage($region);
		if ($herbage) {
			$data['herb']       = $this->translateSingleton($herbage->Herb());
			$data['herbamount'] = Translator::occurrence($herbage->Occurrence());
		}

		$this->writeData($data);
		$this->writeRoads($region);

		if ($magellanVisibility !== 'neighbour') {
			$travelled = [];
			$navigated = [];
			if ($visibility !== Visibility::Farsight) {
				foreach (Lemuria::Report()->getAll($region) as $message) {
					if ($this->containsMessage($message, TravelUnitMessage::class)) {
						$travelled[] = $message;
					}
					if ($this->containsMessage($message, TravelVesselMessage::class)) {
						$navigated[] = $message;
					}
					$this->writeRegionMessage($message, $region);
				}
			}

			if ($peasants > 0) {
				$castle = $intelligence->getCastle();
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
						'type'   => $this->translateSingleton($object, 1),
						'skill'  => 1,
						'number' => $item->Count()
					];
					$this->writeData($data);
				}
			}
			foreach ($region->Treasury() as $unicum) {
				$data = [
					'RESOURCE ' . $hash++,
					'type'   => $this->translateSingleton($unicum->Composition()),
					'skill'  => 0,
					'number' => 1
				];
				$this->writeData($data);
			}

			if ($visibility !== Visibility::Farsight) {
				if ($visibility !== Visibility::Lighthouse) {
					$this->writeEffects($region);
				}
				$this->writeTravelled($travelled);
				$this->writeNavigated($navigated);
			}

			if (empty($magellanVisibility)) {
				$census     = $outlook->Census();
				$party      = $census->Party();
				$isGuarding = $this->isGuarding($party, $intelligence);
				foreach ($region->Residents() as $unit) {
					if ($unit->Party() === $party) {
						$this->writeUnit($unit);
					} elseif ($unit->Construction() || $unit->Vessel()) {
						$this->writeForeignUnit($unit, $census, $isGuarding);
					}
				}
				foreach ($outlook->getApparitions($region) as $unit) {
					if ($unit->Party() !== $party) {
						$this->writeForeignUnit($unit, $census, $isGuarding);
					}
				}
			} elseif (in_array($magellanVisibility, ['travel', 'lighthouse'])) {
				$census = $outlook->Census();
				foreach ($outlook->getTravelled($region) as $unit) {
					$this->writeForeignUnit($unit, $census, false);
				}
			}

			$estate = clone $region->Estate();
			foreach ($estate->sort() as $construction) {
				$this->writeConstruction($construction, $magellanVisibility);
				if (!in_array($visibility, [Visibility::Lighthouse, Visibility::Farsight])) {
					foreach (Lemuria::Report()->getAll($construction) as $message) {
						$this->writeMessage($message);
					}
				}
			}
			$fleet = clone $region->Fleet();
			foreach ($fleet->sort() as $vessel) {
				$this->writeVessel($vessel, $magellanVisibility);
				if (!in_array($visibility, [Visibility::Lighthouse, Visibility::Farsight])) {
					foreach (Lemuria::Report()->getAll($vessel) as $message) {
						$this->writeMessage($message);
					}
				}
			}
		}
	}

	private function writeBeyond(array $regions): void {
		foreach ($regions as $beyond) {
			$coordinates = $beyond[0];
			$region      = $beyond[1];
			$data        = [
				'REGION ' . $coordinates->X() . ' ' . $coordinates->Y(),
				'id'         => Lemuria::Catalog()->nextId(Domain::Location)->Id(),
				'Name'       => $region->Name(),
				'Terrain'    => $this->translateSingleton($region->Landscape()),
				'Insel'      => $region->Continent()->Id()->Id(),
				'visibility' => 'neighbour'
			];
			$this->writeData($data);
		}
	}

	private function writeRoads(Region $region): void {
		$roads = $region->Roads();
		foreach (self::ROADS as $road => $direction) {
			if ($region->hasRoad($direction)) {
				$percent = 100;
			} elseif ($roads && $roads[$direction] > 0.0) {
				$percent = (int)floor(100.0 * $roads[$direction]);
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
			'Beschr'        => $this->compileUnitDescription($unit),
			'Partei'        => $unit->Party()->Id()->Id(),
			'Parteitarnung' => $disguise !== false ? 1 : 0,
			'Anderepartei'  => $disguise ? $disguise->Id()->Id() : 0,
			'Anzahl'        => $unit->Size(),
			'Typ'           => $this->translateSingleton($unit->Race()),
			'Burg'          => $unit->Construction()?->Id()->Id(),
			'Schiff'        => $unit->Vessel()?->Id()->Id(),
			'bewacht'       => $unit->IsGuarding() ? 1 : 0,
			'Kampfstatus'   => Translator::BATTLE_ROW[$unit->BattleRow()->value] ?? 4,
			'hp'            => Translator::HEALTH[$healthCode],
			'weight'        => $unit->Weight()
		];
		if (!$unit->IsLooting()) {
			$data['privat'] = Translator::MISC['isNotLooting'];
		}
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
		$this->writeResources($unit->Inventory(), $unit->Treasury());
		$this->writeOrders($unit);
		$this->writeEffects($unit);
	}

	private function writeForeignUnit(Unit $unit, Census $census, bool $seenByGuards): void {
		$party     = $census->getParty($unit)?->Id()->Id() ?? 0;
		$isMonster = $unit->Party()->Type() === Type::Monster;
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
			'Typ'           => $this->translateSingleton($unit->Race()),
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
			$this->writeMonsterResources($unit->Inventory());
		} elseif ($seenByGuards) {
			$this->writeResources(new Observables($unit->Inventory()));
		}
	}

	private function writeConstruction(Construction $construction, string $visibility): void {
		$owner = $construction->Inhabitants()->Owner();
		$data  = [
			'BURG ' . $construction->Id()->Id(),
			'Typ'      => $this->translateSingleton($construction->Building()),
			'Name'     => $construction->Name(),
			'Beschr'   => $this->compileCostructionDescription($construction),
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
		$coast      = Translator::COAST[$vessel->Anchor()->value] ?? null;
		$passengers = $vessel->Passengers();
		$captain    = $passengers->Owner();
		$cargo      = 0;
		foreach ($passengers as $unit) {
			$cargo += $unit->Weight();
		}
		$data = [
			'SCHIFF ' . $vessel->Id()->Id(),
			'Typ'      => $this->translateSingleton($ship),
			'Name'     => $vessel->Name(),
			'Beschr'   => $this->compileVesselDescription($vessel),
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
			$calculus   = new Calculus($unit);
			$statistics = $this->statistics->getTalents($unit);
			$data       = ['TALENTE'];
			foreach ($knowledge as $ability) {
				$talent        = $ability->Talent();
				$experience    = $ability->Experience();
				$ability       = $calculus->knowledge($talent);
				$change        = $statistics[getClass($talent)]?->change ?? 0;
				$talent        = $this->dictionary->get('talent.' . getClass($talent));
				$data[$talent] = [$experience, $ability->Level(), $change];
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
			foreach ($spellBook as $spell) {
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

	private function writeResources(Resources $resources, ?Treasury $treasury = null): void {
		if (count($resources) > 0) {
			$data = ['GEGENSTAENDE'];
			foreach ($resources as $quantity) {
				$commodity        = $this->translateSingleton($quantity->Commodity());
				$data[$commodity] = $quantity->Count();
			}
			if ($treasury) {
				foreach ($treasury as $unicum) {
					$composition        = $this->translateSingleton($unicum->Composition());
					$data[$composition] = 1;
				}
			}
			$this->writeData($data);
		}
	}

	private function writeMonsterResources(Resources $resources): void {
		if (count($resources) > 0) {
			$data = ['GEGENSTAENDE'];
			foreach ($resources as $quantity) {
				$commodity = $quantity->Commodity();
				$class     = match (true) {
					$commodity instanceof Herb   => 'herb',
					$commodity instanceof Potion => 'potion',
					default                      => getClass($commodity)
				};
				$commodity = match ($class) {
					'herb', 'potion' => Translator::COMMODITY[$class],
					default          => $this->translateSingleton($class)
				};
				$data[$commodity] = isset(Translator::MONSTER_RESOURCE[$class]) ? 0 : $quantity->Count();
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
				foreach ($effect->getPotions() as $potion /** @var Potion $potion */) {
					$effects[getClass($potion)] = $effect->getCount($potion);
				}
			}
		}
		if ($effects) {
			$data = ['EFFECTS'];
			foreach ($effects as $effect => $count) {
				$data[] = '"' . $count . ' ' . $this->translateSingleton($effect) . '"';
			}
			$this->writeData($data);
		}
	}

	private function writeBattle(Battle $battle): void {
		if ($battle->count()) {
			$coordinates = $this->map->getCoordinates($battle->Location());
			$this->writeData(['BATTLE ' . $coordinates->X() . ' ' . $coordinates->Y() . ' 0']);
			foreach ($battle as $message /** @var BattleMessage $message */) {
				$this->writeBattleMessage($message, $coordinates);
			}
		}
	}

	/**
	 * @param array<LemuriaMessage> $travelled
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
	 * @param array<LemuriaMessage> $navigated
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
				'type'     => $this->section($message->Section()),
				'rendered' => (string)$message
			];
			$this->writeData($data);
		}
	}

	private function writeBattleMessage(BattleMessage $message, Coordinates $coordinates): void {
		$data = [
			'MESSAGE ' . $message->Id()->Id(),
			'type'     => Section::Battle->value,
			'rendered' => (string)$message,
			'region'   => $coordinates->X() . ' ' . $coordinates->Y() . ' 0'
		];
		$this->writeData($data);
	}

	private function writeRegionMessage(Message $message, Region $region): void {
		if (!$this->filter->retains($message)) {
			$coordinates = $this->map->getCoordinates($region);
			$data        = [
				'MESSAGE ' . $message->Id()->Id(),
				'type'     => $this->section($message->Section()),
				'rendered' => (string)$message,
				'region'   => $coordinates->X() . ' ' . $coordinates->Y() . ' 0'
			];
			$this->writeData($data);
		}
	}

	private function writeUnitMessage(Message $message, Unit $unit): void {
		if (!$this->filter->retains($message)) {
			$coordinates = $this->map->getCoordinates($unit->Region());
			$data        = [
				'MESSAGE ' . $message->Id()->Id(),
				'type'     => $this->section($message->Section()),
				'rendered' => (string)$message,
				'unit'     => $unit->Id()->Id(),
				'region'   => $coordinates->X() . ' ' . $coordinates->Y() . ' 0'
			];
			$this->writeData($data);
		}
	}

	private function writeMessagetype(): void {
		foreach (Section::cases() as $section) {
			$code        = $this->section($section);
			$data[$code] = [
				'MESSAGETYPE ' . $code,
				'text'    => '"$rendered"',
				'section' => Translator::SECTION[$code]
			];
			$this->writeData(array_values($data));
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

	private function hasHunger(Unit $unit): bool {
		$effect = new Hunger(new State());
		return Lemuria::Score()->find($effect->setUnit($unit)) instanceof Hunger;
	}

	private function collectParties(Outlook $outlook, Region $region, array &$parties): void {
		$census = $outlook->Census();
		$party  = $census->Party();
		foreach ($region->Estate() as $construction) {
			foreach ($construction->Inhabitants() as $unit) {
				$foreign = $census->getParty($unit);
				if ($foreign && $foreign !== $party) {
					$id           = $foreign->Id()->Id();
					$parties[$id] = $foreign;
				}
			}
		}
		foreach ($region->Fleet() as $vessel) {
			foreach ($vessel->Passengers() as $unit) {
				$foreign = $census->getParty($unit);
				if ($foreign && $foreign !== $party) {
					$id           = $foreign->Id()->Id();
					$parties[$id] = $foreign;
				}
			}
		}
		foreach ($outlook->getApparitions($region) as $unit) {
			$foreign = $census->getParty($unit);
			if ($foreign && $foreign !== $party) {
				$id           = $foreign->Id()->Id();
				$parties[$id] = $foreign;
			}
		}
	}

	private function getPrice(string $class, Luxuries $luxuries): int {
		/** @var Luxury $luxury */
		$luxury = Lemuria::Builder()->create($class);
		$offer  = $luxuries->Offer();
		if ($luxury === $offer->Commodity()) {
			return -$offer->Price();
		}
		return $luxuries[$class]->Price();
	}

	private function isGuarding(Party $party, Intelligence $intelligence): bool {
		foreach ($intelligence->getGuards() as $unit) {
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

	private function compileUnitDescription(Unit $unit): string {
		return $this->compileFullTreasuryDescription($unit->Description(), $unit->Treasury());
	}

	private function compileRegionDescription(Region $region): string {
		$description = $this->compileRealmDescription($region);
		$description = $this->compileQuotasDescription($description, $region);
		return $this->compileShortTreasuryDescription($description, $region->Treasury());
	}

	private function compileRealmDescription(Region $region) : string {
		$description = trim($region->Description());
		$realm       = $region->Realm();
		$central     = $realm?->Territory()->Central();
		if ($central) {
			if ($description) {
				$description .= str_ends_with($description, '.') ? ' ' : '. ';
			}
			if ($region === $central) {
				$description .= 'Zentralregion des Reiches ' . $realm->Name() . '.';
			} else {
				$description .= 'Die Region gehört zum Reich ' . $realm->Name() . '.';
			}
		}
		return $description;
	}

	private function compileQuotasDescription(string $description, Region $region) : string {
		$quotas = $this->context->Party()->Regulation()->getQuotas($region);
		if ($quotas?->count() > 0) {
			if ($description) {
				$description .= (str_ends_with($description, '.') ? ' ' : '. ') . 'Grenzen: ';
			}
			$list = [];
			foreach ($quotas as $quota) {
				$commodity = $quota->Commodity();
				$item      = $commodity instanceof Herb ? $this->dictionary->get('kind.Herb') : $this->translateSingleton($commodity);
				$threshold = $quota->Threshold();
				$list[]    = $item . ' ' . (is_float($threshold) ? (int)(100 * $threshold) . '%' : number($threshold));
			}
			$description .= implode(', ', $list) . '.';
		}
		return $description;
	}

	private function compileCostructionDescription(Construction $construction): string {
		$description = $construction->Description();
		switch ($construction->Building()::class) {
			case Canal::class :
				$description = $this->compileCanalDescription($description, $construction);
				break;
			case Market::class :
				$description = $this->compileMarketDescription($description, $construction);
				break;
			case Port::class :
				$description = $this->compilePortDescription($description, $construction);
		}
		return $this->compileShortTreasuryDescription($description, $construction->Treasury());
	}

	private function compileCanalDescription($description, $construction): string {
		$compilation = trim($description);
		if (!empty($compilation)) {
			$compilation .= str_ends_with($compilation, '.') ? ' ' : '. ';
		}

		if ($this->isMaintained($construction)) {
			/** @var Fee $extension */
			$extension = $construction->Extensions()->offsetGet(Fee::class);
			$fee       = $extension->Fee();
			if ($fee instanceof Quantity) {
				$compilation .= str_replace('$fee', $this->good($fee), Translator::BUILDINGS['canal']['fee']);
			} else {
				$compilation .= Translator::BUILDINGS['canal']['noFee'];
			}
		} else {
			$compilation .= Translator::BUILDINGS['canal']['notMaintained'];
		}
		return $compilation;
	}

	private function compileMarketDescription($description, $construction): string {
		$compilation = trim($description);
		if (!empty($compilation)) {
			$compilation .= str_ends_with($compilation, '.') ? ' ' : '. ';
		}
		if (!$this->isMaintained($construction)) {
			$compilation .= Translator::BUILDINGS['market']['notMaintained'];
			return $compilation;
		}

		/** @var MarketExtension $market */
		$market       = $construction->Extensions()->offsetGet(MarketExtension::class);
		$fee          = $market->Fee();
		$compilation .= Translator::BUILDINGS['market']['order'] . ': ';
		if ($fee instanceof Quantity) {
			$compilation .= str_replace('$fee', $this->good($fee), Translator::BUILDINGS['market']['fee']);
		} elseif (is_float($fee)) {
			$fee          = (string)(int)round(100.0 * $fee);
			$compilation .= str_replace('$fee', $fee, Translator::BUILDINGS['market']['feePercent']);
		} else {
			$compilation .= Translator::BUILDINGS['market']['noFee'];
		}

		$tradeables = $market->Tradeables();
		if ($tradeables->isEmpty()) {
			$compilation .= ' ' . Translator::BUILDINGS['market']['noRules'];
		} else {
			$commodities = [];
			foreach ($tradeables as $commodity) {
				$commodities[] = $this->translateSingleton($commodity, 1);
			}
			$goods = implode(', ', $commodities);
			if ($tradeables->IsExclusion()) {
				$translation = Translator::BUILDINGS['market']['forbidden'];
			} else {
				$translation = Translator::BUILDINGS['market']['allowed'];
			}
			$compilation .= ' ' . str_replace('$goods', $goods, $translation);
		}

		return $compilation;
	}

	private function compilePortDescription($description, $construction): string {
		$compilation = trim($description);
		if (!empty($compilation)) {
			$compilation .= str_ends_with($compilation, '.') ? ' ' : '. ';
		}

		if ($this->isMaintained($construction)) {
			/** @var Fee $extension */
			$extension = $construction->Extensions()->offsetGet(Fee::class);
			$fee       = $extension->Fee();
			if ($fee instanceof Quantity) {
				$compilation .= str_replace('$fee', $this->good($fee), Translator::BUILDINGS['port']['fee']);
			} else {
				$compilation .= Translator::BUILDINGS['port']['noFee'];
			}

			/** @var Duty $extension */
			$extension = $construction->Extensions()->offsetGet(Duty::class);
			$duty      = $extension->Duty();
			if ($duty > 0.0) {
				$duty         = number($duty);
				$compilation .= ' ' . str_replace('$duty', $duty, Translator::BUILDINGS['port']['duty']);
			}
		} else {
			$compilation .= Translator::BUILDINGS['port']['notMaintained'];
		}
		return $compilation;
	}

	private function compileVesselDescription(Vessel $vessel): string {
		return $this->compileShortTreasuryDescription($vessel->Description(), $vessel->Treasury());
	}

	private function compileFullTreasuryDescription(string $compilation, Treasury $treasury): string {
		$compilation = trim($compilation);
		if ($treasury->count() > 0) {
			if (!empty($compilation)) {
				$compilation .= str_ends_with($compilation, '.') ? ' ' : '. ';
			}
			$compilation .= Translator::MISC['specialItems'] . ':';
		}
		$next = false;
		foreach ($treasury as $unicum) {
			if ($next) {
				$compilation .= ',';
			}
			$id           = $unicum->Id();
			$name         = $this->escape($unicum->Name());
			$description  = $this->escape($unicum->Description());
			$composition  = $this->translateSingleton($unicum->Composition());
			if ($name) {
				$unicumName = $name . ' [' . $id . '] (' . $composition . ')';
			} else {
				$unicumName = $composition . ' [' . $id . '] (' . Translator::MISC['unnamed'] . ')';
			}
			if ($description) {
				$unicumName .= ': ' . $description;
			}
			$compilation .= ' ' . $unicumName;
			$next         = !str_ends_with($description, '.');
		}
		return $compilation;
	}

	private function compileShortTreasuryDescription(string $compilation, Treasury $treasury): string {
		$compilation = trim($compilation);
		if ($treasury->count() > 0) {
			if (!empty($compilation)) {
				$compilation .= str_ends_with($compilation, '.') ? ' ' : '. ';
			}
			$compilation .= Translator::MISC['specialItems'] . ':';
		}
		$next = false;
		foreach ($treasury as $unicum) {
			if ($next) {
				$compilation .= ',';
			}
			$id           = $unicum->Id();
			$composition  = $this->translateSingleton($unicum->Composition());
			$unicumName   = $composition . ' [' . $id . ']';
			$compilation .= ' ' . $unicumName;
			$next         = true;
		}
		return $compilation;
	}

	private function good(Quantity $quantity): string {
		$commodity = getClass($quantity->Commodity());
		$amount    = $quantity->Count();
		$index     = $amount === 1 ? 0 : 1;
		return number($amount) . ' ' . $this->translateSingleton($commodity, $index);
	}

	private function section(Section $section): int {
		return $section === Section::Guard ? Section::Movement->value : $section->value;
	}

	private function isMaintained(Construction $construction): bool {
		$effect = new Unmaintained(State::getInstance());
		return !Lemuria::Score()->find($effect->setConstruction($construction));
	}
}
