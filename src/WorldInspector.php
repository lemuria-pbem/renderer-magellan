<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Magellan;

use Lemuria\Engine\Fantasya\Factory\Model\Wage;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Landmass;
use Lemuria\Model\Fantasya\Navigable;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\World\PartyMap;
use Lemuria\Model\World\Map;
use Lemuria\Engine\Fantasya\Availability;
use Lemuria\Engine\Fantasya\Command\Entertain;
use Lemuria\Model\Fantasya\Building\Site;
use Lemuria\Model\Fantasya\Commodity\Horse;
use Lemuria\Model\Fantasya\Commodity\Iron;
use Lemuria\Model\Fantasya\Commodity\Peasant;
use Lemuria\Model\Fantasya\Commodity\Silver;
use Lemuria\Model\Fantasya\Commodity\Stone;
use Lemuria\Model\Fantasya\Commodity\Wood;
use Lemuria\Model\Fantasya\Continent;
use Lemuria\Model\Fantasya\Intelligence;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Id;
use Lemuria\Renderer\PathFactory;

class WorldInspector extends MagellanWriter
{
	private readonly string $path;

	private ?Party $party = null;

	private ?Landmass $regions = null;

	private bool $withInfrastructure = false;

	public function __construct(PathFactory $pathFactory) {
		parent::__construct($pathFactory);
		$map = Lemuria::World();
		if ($map instanceof Map) {
			$this->map = $map;
		}
	}

	public function render(Id $entity): static {
		$this->file = fopen($this->path, 'w');
		if (!$this->file) {
			throw new \RuntimeException('Could not open file ' . $this->path . '.');
		}

		$this->writeHeader();

		if ($this->party) {
			$this->writeParty($this->party);
			foreach (Party::all() as $party) {
				if ($party !== $this->party && !$party->hasRetired()) {
					$this->writeForeignParty($party, true);
				}
			}
		}

		foreach (Continent::all() as $continent) {
			$this->writeIsland($continent);
		}
		$this->writeRegions();

		if (!fclose($this->file)) {
			throw new \RuntimeException('Could not close file ' . $this->path . '.');
		}
		$this->file = null;
		return $this;
	}

	public function withInfrastructure(bool $withInfrastructure = true): static {
		$this->withInfrastructure = $withInfrastructure;
		return $this;
	}

	public function setParty(Party $party): static {
		$this->party = $party;
		$this->map   = new PartyMap(Lemuria::World(), $party);
		return $this;
	}

	public function setRegions(Landmass $regions): static {
		$this->regions = $regions;
		return $this;
	}

	public function setWorld(Map $world): static {
		$this->map = $world;
		return $this;
	}

	public function setPath(string $path): static {
		$this->path = $path;
		return $this;
	}

	private function writeRegions(): void {
		$regions = $this->regions ?: Region::all();
		foreach ($regions as $region) {
			$this->writeRegion($region);
		}
	}

	private function writeRegion(Region $region): void {
		$isNavigable  = $region->Landscape() instanceof Navigable;
		$coordinates  = $this->map->getCoordinates($region);
		$resources    = $region->Resources();
		$peasants     = $resources[Peasant::class]->Count();
		$intelligence = new Intelligence($region);

		$availability = new Availability($region);
		$wage         = new Wage($this->calculateInfrastructure($region));
		$data         = [
			'REGION ' . $coordinates->X() . ' ' . $coordinates->Y() . ' 0',
			'id'       => $region->Id()->Id(),
			'Name'     => $region->Name(),
			'Terrain'  => $this->translateSingleton($region->Landscape()),
			'Insel'    => $region->Continent()->Id()->Id(),
			'Beschr'   => $this->compileRealmDescription($region),
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

		$herbage = $region->Herbage();
		if ($herbage) {
			$data['herb']       = $this->translateSingleton($herbage->Herb());
			$data['herbamount'] = Translator::occurrence($herbage->Occurrence());
		}

		$this->writeData($data);

		if (!$isNavigable) {
			$this->writeRoads($region);
		}

		if (!$isNavigable) {
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

		if (!$isNavigable) {
			$hash = 1;
			foreach ($resources as $item) {
				$object = $item->getObject();
				if ($object::class !== Peasant::class || $object::class !== Silver::class) {
					$data = [
						'RESOURCE ' . $hash++,
						'type'   => $this->translateSingleton($object, 1),
						'skill'  => 1,
						'number' => $item->Count()
					];
					$this->writeData($data);
				}
			}
		}

		if ($this->withInfrastructure && !$isNavigable) {
			$estate = clone $region->Estate();
			foreach ($estate->sort() as $construction) {
				$this->writeConstruction($construction);
				$unit = $construction->Inhabitants()->Owner();
				if ($unit && $unit->Party() !== $this->party) {
					$this->writeForeignUnit($unit);
				}
			}
		}

		if ($this->withInfrastructure) {
			$fleet = clone $region->Fleet();
			foreach ($fleet->sort() as $vessel) {
				$captain = $vessel->Passengers()->Owner();
				if ($isNavigable) {
					if ($captain?->Party() === $this->party) {
						$this->writeVessel($vessel);
					}
				} else {
					$this->writeVessel($vessel);
					if ($captain && $captain->Party() !== $this->party) {
						$this->writeForeignUnit($captain);
					}
				}
			}
		}

		if ($this->party) {
			foreach ($intelligence->getUnits($this->party) as $unit) {
				$this->writeUnit($unit);
			}
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

	private function writeForeignUnit(Unit $unit): void {
		$party     = $unit->Party();
		$disguise  = $unit->Disguise();
		$data      = [
			'EINHEIT ' . $unit->Id()->Id(),
			'Name'          => $unit->Name(),
			'Beschr'        => $this->compileForeignUnitDescription($unit),
			'Partei'        => $party->Id()->Id(),
			'Parteitarnung' => $disguise !== false ? 1 : 0,
			'Anderepartei'  => $disguise ? $disguise->Id()->Id() : 0,
			'Verraeter'     => $disguise === $this->party ? 1 : 0,
			'Anzahl'        => $unit->Size(),
			'Typ'           => $this->translateSingleton($unit->Race()),
			'Burg'          => $unit->Construction()?->Id()->Id(),
			'Schiff'        => $unit->Vessel()?->Id()->Id(),
			'bewacht'       => $unit->IsGuarding() ? 1 : 0,
			'hp'            => Translator::HEALTH[0]
		];
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
}
