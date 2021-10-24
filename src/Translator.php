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

	public const BUILDING = [
		'Cabin'      => 'Holzfällerhütte',
		'Citadel'    => 'Zitadelle',
		'Fort'       => 'Befestigung',
		'Palace'     => 'Schloss',
		'Sawmill'    => 'Sägewerk',
		'Site'       => 'Baustelle',
		'Stronghold' => 'Festung',
		'Tower'      => 'Turm'
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
		'Dwarf'    => 'Zwerg',
		'Elf'      => 'Elf',
		'Halfling' => 'Halbling',
		'Human'    => 'Mensch',
		'Orc'      => 'Ork',
		'Troll'    => 'Troll'
	];

	public const HEALTH = [
		'gesund',
		'erschoepft',
		'verwundet',
		'schwer verwundet'
	];

	public const COMMODITY = [
		'Armor'           => 'Plattenpanzer',
		'Balsam'          => 'Balsam',
		'Battleaxe'       => 'Streitaxt',
		'BerserkBlood'    => 'Berserkerblut',
		'Brainpower'      => 'Gehirnschmalz',
		'Bow'             => 'Bogen',
		'Bubblemorel'     => 'Blasenmorchel',
		'Bugleweed'       => 'Gurgelkraut',
		'Camel'           => 'Kamel',
		'Carriage'        => 'Wagen',
		'Catapult'        => 'Katapult',
		'CaveLichen'      => 'Höhlenglimm',
		'CobaltFungus'    => 'Blauer Baumringel',
		'Crossbow'        => 'Armbrust',
		'DrinkOfCreation' => 'Schaffenstrunk',
		'DrinkOfTruth'    => 'Trank der Wahrheit',
		'Elephant'        => 'Elefant',
		'ElixirOfPower'   => 'Elixier der Macht',
		'Elvendear'       => 'Elfenlieb',
		'FjordFungus'     => 'Fjordwuchs',
		'Flatroot'        => 'Flachwurz',
		'Fur'             => 'Pelz',
		'Gapgrowth'       => 'Spaltwachs',
		'Gem'             => 'Juwel',
		'Gold'            => 'Gold',
		'GoliathWater'    => 'Goliathwasser',
		'Griffin'         => 'Greif',
		'Griffinegg'      => 'Greifenei',
		'HealingPotion'   => 'Heiltrank',
		'Horse'           => 'Pferd',
		'HorseBliss'      => 'Pferdeglück',
		'IceBegonia'      => 'Eisblume',
		'Iron'            => 'Eisen',
		'Ironshield'      => 'Eisenschild',
		'Knotroot'        => 'Knotiger Saugwurz',
		'Mail'            => 'Kettenhemd',
		'Mandrake'        => 'Alraune',
		'Myrrh'           => 'Myrrhe',
		'Oil'             => 'Öl',
		'Olibanum'        => 'Weihrauch',
		'Owlsgaze'        => 'Eulenauge',
		'Peasant'         => 'Bauer',
		'PeasantJoy'      => 'Bauernlieb',
		'Pegasus'         => 'Pegasus',
		'Peyote'          => 'Kakteenschwitz',
		'Rockweed'        => 'Steinbeißer',
		'Sandreeker'      => 'Sandfäule',
		'SevenLeagueTea'  => 'Siebenmeilentee',
		'Silk'            => 'Seide',
		'Silver'          => 'Silber',
		'Snowcrystal'     => 'Schneekristall',
		'Spear'           => 'Speer',
		'Spice'           => 'Gewürz',
		'SpiderIvy'       => 'Grüner Spinnerich',
		'Stone'           => 'Stein',
		'Sword'           => 'Schwert',
		'TangyTemerity'   => 'Würziger Wagemut',
		'Warhammer'       => 'Kriegshammer',
		'Waterfinder'     => 'Wasserfinder',
		'WaterOfLife'     => 'Wasser des Lebens',
		'WhiteHemlock'    => 'Weißer Wüterich',
		'Windbag'         => 'Windbeutel',
		'Wood'            => 'Holz',
		'Woodshield'      => 'Holzschild',
		'Woundshut'       => 'Wundsalbe'
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
