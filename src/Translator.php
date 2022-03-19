<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Magellan;

final class Translator
{
	public final const BATTLE_ROW = [5, 4, 3, 2, 1, 1, 0];

	public final const COAST = ['N' => 1, 'NE' => 1, 'E' => 2, 'SE' => 3, 'S' => 4, 'SW' => 4, 'W' => 5, 'NW' => 0];

	public final const MONSTER_RESOURCE = [
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

	public final const BUILDING = [
		'Acropolis'      => 'Akropolis',
		'AlchemyKitchen' => 'Alchemistenküche',
		'Blacksmith'     => 'Schmiede',
		'Cabin'          => 'Holzfällerhütte',
		'Canal'          => 'Kanal',
		'CamelBreeding'  => 'Kamelzucht',
		'Citadel'        => 'Zitadelle',
		'Dockyard'       => 'Schiffswerft',
		'Fort'           => 'Befestigung',
		'HorseBreeding'  => 'Pferdezucht',
		'Lighthouse'     => 'Leuchtturm',
		'Magespire'      => 'Magierturm',
		'Mine'           => 'Bergwerk',
		'Palace'         => 'Schloss',
		'Pit'            => 'Mine',
		'Port'           => 'Hafen',
		'Quarry'         => 'Steinbruch',
		'Quay'           => 'Steg',
		'Saddlery'       => 'Sattlerei',
		'Sawmill'        => 'Sägewerk',
		'Shack'          => 'Steingrube',
		'Signpost'       => 'Wegweiser',
		'Site'           => 'Baustelle',
		'Stronghold'     => 'Festung',
		'Tavern'         => 'Taverne',
		'Tower'          => 'Turm',
		'Workshop'       => 'Werkstatt'
	];

	public final const LANDSCAPE = [
		'Desert'   => 'Wüste',
		'Forest'   => 'Wald',
		'Glacier'  => 'Gletscher',
		'Highland' => 'Hochland',
		'Mountain' => 'Gebirge',
		'Ocean'    => 'Ozean',
		'Plain'    => 'Ebene',
		'Swamp'    => 'Sumpf'
	];

	public final const RACE = [
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

	public final const HEALTH = [
		'gesund',
		'erschoepft',
		'verwundet',
		'schwer verwundet'
	];

	public final const COMMODITY = [
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

	public final const COMPOSITION = [
		'Scroll'    => "Schriftrolle",
		"Spellbook" => "Zauberbuch"
	];

	public final const SHIP = [
		'Boat'       => 'Boot',
		'Caravel'    => 'Karavelle',
		'Dragonship' => 'Drachenschiff',
		'Galleon'    => 'Galeone',
		'Longboat'   => 'Langboot',
		'Trireme'    => 'Trireme'
	];

	public final const TALENT = [
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

	public final const SECTION = [
		1 => 'battle',
		2 => 'economy',
		3 => 'errors',
		4 => 'events',
		5 => 'magic',
		6 => 'mail',
		7 => 'movement',
		8 => 'production',
		9 => 'study'
	];

	public final const SPELL = [
		'' => 'normal',
		0  => 'precombat',
		1  => 'combat'
	];

	public final const ALCHEMY = [
		'BerserkBlood'    => '10 Personen erhalten im Kampf einen Angriffsbonus',
		'Brainpower'      => 'erhöhte Lernchance für 10 Personen',
		'DrinkOfCreation' => 'verdoppelt Produktivität von 10 Leuten bei MACHEN',
		'DrinkOfTruth'    => 'Anwender durchschaut Illusionen',
		'ElixirOfPower'   => '10 Personen erhalten zusätzliche Trefferpunkte',
		'GoliathWater'    => '10 Leute Tragkraft wie Pferde',
		'HealingPotion'   => '1 Person überlebt sonst tödlichen Schaden',
		'HorseBliss'      => '50 Pferde vermehren sich bis zu vier mal',
		'PeasantJoy'      => '1.000 Bauern in der Region erhalten die zehnfache Chance, sich zu vermehren',
		'SevenLeagueTea'  => '10 Leute schnell wie Pferde',
		'WaterOfLife'     => 'macht aus einem Stück Holz oder Mallorn 10 Schößlinge/Mallornschößlinge',
		'Woundshut'       => 'bringt den Personen der Einheit bis zu 400 Trefferpunkte zurück',
	];

	public final const TRANSLATIONS = [
		'Stangenwaffen' => 'Speerkampf'
	];

	public final const MISC = [
		'isNotLooting' => 'sammelt keine Beute',
		'specialItems' => 'Besondere Gegenstände',
		'unnamed'      => 'unbenannt'
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
