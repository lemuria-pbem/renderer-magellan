<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Magellan;

use Lemuria\Engine\Message\Section;
use Lemuria\Model\Fantasya\Combat;
use Lemuria\Model\World;

final class Translator
{
	public const BATTLE_ROW = [Combat::AGGRESSIVE => 0, Combat::FRONT     => 1, Combat::CAREFUL => 1,
		                       Combat::BACK       => 2, Combat::DEFENSIVE => 3,
		                       Combat::BYSTANDER  => 4, Combat::REFUGEE   => 5];

	public const COAST = [World::NORTH => 1, World::NORTHEAST => 1, World::EAST => 2, World::SOUTHEAST => 3,
		                  World::SOUTH => 4, World::SOUTHWEST => 4, World::WEST => 5, World::NORTHWEST => 0];

	public const MONSTER_RESOURCE = [
		'herb'           => true,
		'potion'         => true,
		'Balsam'         => true,
		'Carnassial'     => true,
		'Fur'            => true,
		'Gem'            => true,
		'Goblinear'      => true,
		'Gold'           => true,
		'Griffinfeather' => true,
		'Myrrh'          => true,
		'Oil'            => true,
		'Olibanum'       => true,
		'Silk'           => true,
		'Silver'         => true,
		'Skull'          => true,
		'Spice'          => true
	];

	public const BUILDING = [
		'Blacksmith' => 'Schmiede',
		'Cabin'      => 'Holzfällerhütte',
		'Citadel'    => 'Zitadelle',
		'Dockyard'   => 'Schiffswerft',
		'Fort'       => 'Befestigung',
		'Lighthouse' => 'Leuchtturm',
		'Magespire'  => 'Magierturm',
		'Mine'       => 'Bergwerk',
		'Palace'     => 'Schloss',
		'Pit'        => 'Mine',
		'Port'       => 'Hafen',
		'Quarry'     => 'Steinbruch',
		'Quay'       => 'Steg',
		'Saddlery'   => 'Sattlerei',
		'Sawmill'    => 'Sägewerk',
		'Shack'      => 'Steingrube',
		'Signpost'   => 'Wegweiser',
		'Site'       => 'Baustelle',
		'Stronghold' => 'Festung',
		'Tavern'     => 'Taverne',
		'Tower'      => 'Turm',
		'Workshop'   => 'Werkstatt'
	];

	public const LANDSCAPE = [
		'Desert'   => 'Wüste',
		'Forest'   => 'Wald',
		'Glacier'  => 'Gletscher',
		'Highland' => 'Hochland',
		'Mountain' => 'Gebirge',
		'Ocean'    => 'Ozean',
		'Plain'    => 'Ebene',
		'Swamp'    => 'Sumpf'
	];

	public const RACE = [
		'Aquan'    => 'Aquaner',
		'Bear'     => 'Bär',
		'Dwarf'    => 'Zwerg',
		'Elf'      => 'Elf',
		'Ent'      => 'Baumhirte',
		'Ghoul'    => 'Ghoul',
		'Goblin'   => 'Kobold',
		'Halfling' => 'Halbling',
		'Kraken'   => 'Krake',
		'Human'    => 'Mensch',
		'Orc'      => 'Ork',
		'Skeleton' => 'Skelett',
		'Troll'    => 'Troll',
		'Zombie'   => 'Zombie'
	];

	public const HEALTH = [
		'gesund',
		'erschoepft',
		'verwundet',
		'schwer verwundet'
	];

	public const COMMODITY = [
		'herb'                 => 'Kräuter',
		'potion'               => 'Trank',
		'Armor'                => 'Plattenpanzer',
		'Balsam'               => 'Balsam',
		'Battleaxe'            => 'Streitaxt',
		'BerserkBlood'         => 'Berserkerblut',
		'Brainpower'           => 'Gehirnschmalz',
		'Bow'                  => 'Bogen',
		'Bubblemorel'          => 'Blasenmorchel',
		'Bugleweed'            => 'Gurgelkraut',
		'Camel'                => 'Kamel',
		'Carnassial'           => 'Reißzahn',
		'Carriage'             => 'Wagen',
		'CarriageWreck'        => 'Wagenwrack',
		'Catapult'             => 'Katapult',
		'CaveLichen'           => 'Höhlenglimm',
		'CobaltFungus'         => 'Blauer Baumringel',
		'Crossbow'             => 'Armbrust',
		'DentedArmor'          => 'Verbeulte Rüstung',
		'DentedIronshield'     => 'Verbeulter Eisenschild',
		'DrinkOfCreation'      => 'Schaffenstrunk',
		'DrinkOfTruth'         => 'Trank der Wahrheit',
		'Elephant'             => 'Elefant',
		'ElixirOfPower'        => 'Elixier der Macht',
		'Elvendear'            => 'Elfenlieb',
		'FjordFungus'          => 'Fjordwuchs',
		'Flatroot'             => 'Flachwurz',
		'Fur'                  => 'Pelz',
		'Gapgrowth'            => 'Spaltwachs',
		'Gem'                  => 'Juwel',
		'GoblinEar'            => 'Koboldohr',
		'Gold'                 => 'Gold',
		'GoliathWater'         => 'Goliathwasser',
		'Griffin'              => 'Greif',
		'Griffinegg'           => 'Greifenei',
		'GriffinFeather'       => 'Greifenfeder',
		'HealingPotion'        => 'Heiltrank',
		'Horse'                => 'Pferd',
		'HorseBliss'           => 'Pferdeglück',
		'IceBegonia'           => 'Eisblume',
		'Iron'                 => 'Eisen',
		'Ironshield'           => 'Eisenschild',
		'Knotroot'             => 'Knotiger Saugwurz',
		'LeatherArmor'         => 'Lederrüstung',
		'LooseWarhammer'       => 'Lockerer Kriegshammer',
		'Mail'                 => 'Kettenhemd',
		'Mandrake'             => 'Alraune',
		'Myrrh'                => 'Myrrhe',
		'Oil'                  => 'Öl',
		'Olibanum'             => 'Weihrauch',
		'Owlsgaze'             => 'Eulenauge',
		'Peasant'              => 'Bauer',
		'PeasantJoy'           => 'Bauernlieb',
		'Pegasus'              => 'Pegasus',
		'Peyote'               => 'Kakteenschwitz',
		'Rockweed'             => 'Steinbeißer',
		'RustyBattleaxe'       => 'Rostige Streitaxt',
		'RustyMail'            => 'Rostiges Kettenhemd',
		'RustySword'           => 'Rostiges Schwert',
		'Sandreeker'           => 'Sandfäule',
		'SevenLeagueTea'       => 'Siebenmeilentee',
		'Silk'                 => 'Seide',
		'Silver'               => 'Silber',
		'SkewedCatapult'       => 'Marodes Katapult',
		'Skull'                => 'Totenschädel',
		'Snowcrystal'          => 'Schneekristall',
		'Spear'                => 'Speer',
		'Spice'                => 'Gewürz',
		'SpiderIvy'            => 'Grüner Spinnerich',
		'SplitWoodshield'      => 'Gespaltener Holzschild',
		'Stone'                => 'Stein',
		'StumpSpear'           => 'Stumpfer Speer',
		'Sword'                => 'Schwert',
		'TangyTemerity'        => 'Würziger Wagemut',
		'TatteredLeatherArmor' => 'Zerrissene Lederrüstung',
		'UngirtBow'            => 'Schlaffer Bogen',
		'UngirtCrossbow'       => 'Schlaffe Armbrust',
		'Warhammer'            => 'Kriegshammer',
		'Waterfinder'          => 'Wasserfinder',
		'WaterOfLife'          => 'Wasser des Lebens',
		'WhiteHemlock'         => 'Weißer Wüterich',
		'Windbag'              => 'Windbeutel',
		'Wood'                 => 'Holz',
		'Woodshield'           => 'Holzschild',
		'Woundshut'            => 'Wundsalbe'
	];

	public const SHIP = [
		'Boat'       => 'Boot',
		'Caravel'    => 'Karavelle',
		'Dragonship' => 'Drachenschiff',
		'Galleon'    => 'Galeone',
		'Longboat'   => 'Langboot',
		'Trireme'    => 'Trireme'
	];

	public const TALENT = [
		'Alchemy'        => 'Alchemie',
		'Archery'        => 'Bogenschießen',
		'Armory'         => 'Rüstungsbau',
		'Bladefighting'  => 'Hiebwaffen',
		'Bowmaking'      => 'Bogenbau',
		'Camouflage'     => 'Tarnung',
		'Carriagemaking' => 'Wagenbau',
		'Catapulting'    => 'Katapultschießen',
		'Constructing'   => 'Burgenbau',
		'Crossbowing'    => 'Armbrustschießen',
		'Entertaining'   => 'Unterhaltung',
		'Espionage'      => 'Spionage',
		'Herballore'     => 'Kräuterkunde',
		'Horsetaming'    => 'Pferdedressur',
		'Magic'          => 'Magie',
		'Mining'         => 'Bergbau',
		'Navigation'     => 'Segeln',
		'Perception'     => 'Wahrnehmung',
		'Quarrying'      => 'Steinbau',
		'Riding'         => 'Reiten',
		'Roadmaking'     => 'Straßenbau',
		'Shipbuilding'   => 'Schiffbau',
		'Spearfighting'  => 'Stangenwaffen',
		'Stamina'        => 'Ausdauer',
		'Tactics'        => 'Taktik',
		'Taxcollecting'  => 'Steuereintreiben',
		'Trading'        => 'Handeln',
		'Weaponry'       => 'Waffenbau',
		'Woodchopping'   => 'Holzfällen'
	];

	public const SECTION = [
		Section::EVENT      => 'events',
		Section::ERROR      => 'errors',
		Section::BATTLE     => 'battle',
		Section::ECONOMY    => 'economy',
		Section::MAGIC      => 'magic',
		Section::MAIL       => 'mail',
		Section::MOVEMENT   => 'movement',
		Section::PRODUCTION => 'production',
		Section::STUDY      => 'study'
	];

	public const TRANSLATIONS = [
		'Stangenwaffen' => 'Speerkampf'
	];

	public const MISC = [
		'isNotLooting' => 'sammelt keine Beute'
	];

	public static function occurrence(float $occurrence): string {
		return match (true) {
			$occurrence <= 0.2 => 'sehr wenige',
			$occurrence <= 0.4 => 'wenige',
			$occurrence <= 0.6 => 'einige',
			$occurrence <= 0.8 => 'viele',
			$occurrence <= 1.0 => 'sehr viele'
		};
	}
}
