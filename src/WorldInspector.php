<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Magellan;

use Lemuria\Model\World;
use Lemuria\Engine\Fantasya\Availability;
use Lemuria\Engine\Fantasya\Command\Entertain;
use Lemuria\Engine\Fantasya\Event\Subsistence;
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
use Lemuria\Renderer\Writer;

class WorldInspector extends MagellanWriter
{
	private readonly string $path;

	private World $map;

	public function __construct(PathFactory $pathFactory) {
		parent::__construct($pathFactory);
	}

	public function render(Id $entity): Writer {
		$this->file = fopen($this->path, 'w');
		if (!$this->file) {
			throw new \RuntimeException('Could not open file ' . $this->path . '.');
		}

		$this->writeHeader();

		$continent = Continent::get(new Id(1));
		$this->writeIsland($continent);
		$this->writeRegions();

		if (!fclose($this->file)) {
			throw new \RuntimeException('Could not close file ' . $this->path . '.');
		}
		$this->file = null;
		return $this;
	}

	public function setWorld(World $world): WorldInspector {
		$this->map = $world;
		return $this;
	}

	public function setPath(string $path): WorldInspector {
		$this->path = $path;
		return $this;
	}

	private function writeRegions(): void {
		foreach (Region::all() as $region) {
			$this->writeRegion($region);
		}
	}

	private function writeRegion(Region $region): void {
		$coordinates  = $this->map->getCoordinates($region);
		$resources    = $region->Resources();
		$peasants     = $resources[Peasant::class]->Count();
		$intelligence = new Intelligence($region);

		$availability = new Availability($region);
		$data         = [
			'REGION ' . $coordinates->X() . ' ' . $coordinates->Y() . ' 0',
			'id'       => $region->Id()->Id(),
			'Name'     => $region->Name(),
			'Terrain'  => $this->translateSingleton($region->Landscape()),
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

		$herbage = $region->Herbage();
		if ($herbage) {
			$data['herb']       = $this->translateSingleton($herbage->Herb());
			$data['herbamount'] = Translator::occurrence($herbage->Occurrence());
		}

		$this->writeData($data);

		$castle = $intelligence->getGovernment();
		if ($castle?->Size() > Site::MAX_SIZE) {
			$this->writeMarket($region->Luxuries());
		} else {
			$offer = $region->Luxuries()?->Offer();
			if ($offer) {
				$this->writeOffer($offer);
			}
		}

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

	private function writeIsland(Continent $continent): void {
		$data = [
			'ISLAND ' . $continent->Id()->Id(),
			'Name'   => $continent->Name(),
			'Beschr' => $continent->Description()
		];
		$this->writeData($data);
	}
}
