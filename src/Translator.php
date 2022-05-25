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

	public final const HEALTH = [
		'gesund',
		'erschoepft',
		'verwundet',
		'schwer verwundet'
	];

	public final const COMMODITY = [
		'herb'                  => 'Kräuter',
		'potion'                => 'Trank'
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
