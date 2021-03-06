<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Magellan;

use Lemuria\Model\Fantasya\Combat;
use Lemuria\Model\World;

final class Translator
{
	public const BATTLE_ROW = [Combat::AGGRESSIVE => 0, Combat::BYSTANDER => 1, Combat::BACK  => 2,
		                       Combat::DEFENSIVE  => 3, Combat::REFUGEE   => 5, Combat::FRONT => 0];

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

	public const COMMODITY = [
		'Armor'      => 'Plattenpanzer',
		'Balsam'     => 'Balsam',
		'Battleaxe'  => 'Streitaxt',
		'Bow'        => 'Bogen',
		'Camel'      => 'Kamel',
		'Carriage'   => 'Wagen',
		'Catapult'   => 'Katapult',
		'Crossbow'   => 'Armbrust',
		'Elephant'   => 'Elefant',
		'Fur'        => 'Pelz',
		'Gem'        => 'Juwel',
		'Gold'       => 'Gold',
		'Griffin'    => 'Greif',
		'Griffinegg' => 'Greifenei',
		'Horse'      => 'Pferd',
		'Iron'       => 'Eisen',
		'Ironshield' => 'Eisenschild',
		'Mail'       => 'Kettenhemd',
		'Myrrh'      => 'Myrrhe',
		'Oil'        => 'Öl',
		'Olibanum'   => 'Weihrauch',
		'Peasant'    => 'Bauer',
		'Pegasus'    => 'Pegasus',
		'Silk'       => 'Seide',
		'Silver'     => 'Silber',
		'Spear'      => 'Speer',
		'Spice'      => 'Gewürz',
		'Stone'      => 'Stein',
		'Sword'      => 'Schwert',
		'Warhammer'  => 'Kriegshammer',
		'Wood'       => 'Holz',
		'Woodshield' => 'Holzschild'
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
		'Trading'        => 'Handel',
		'Weaponry'       => 'Waffenbau',
		'Woodchopping'   => 'Holzfällen'
	];

	public const TRANSLATIONS = [
		'Stangenwaffen' => 'Speerkampf'
	];
}
