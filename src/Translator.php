<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Magellan;

final class Translator
{
	/**
	 * @type array<int>
	 */
	public final const array BATTLE_ROW = [5, 4, 3, 2, 1, 1, 0];

	/**
	 * @type array<string, int>
	 */
	public final const array COAST = ['N' => 1, 'NE' => 1, 'E' => 2, 'SE' => 3, 'S' => 4, 'SW' => 4, 'W' => 5, 'NW' => 0];

	/**
	 * @type array<string, true>
	 */
	public final const array MONSTER_RESOURCE = [
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
		'Pegasusfeather' => true,
		'Silk'           => true,
		'Silver'         => true,
		'Skull'          => true,
		'Spice'          => true,
		'WolfSkin'       => true
	];

	/**
	 * @type array<string>
	 */
	public final const array HEALTH = [
		'gesund',
		'erschoepft',
		'verwundet',
		'schwer verwundet'
	];

	/**
	 * @type array<string, string>
	 */
	public final const array COMMODITY = [
		'herb'   => 'Kräuter',
		'potion' => 'Trank'
	];

	/**
	 * @type array<int, string>
	 */
	public final const array SECTION = [
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

	/**
	 * @type array<int|string, string>
	 */
	public final const array SPELL = [
		'' => 'normal',
		0  => 'precombat',
		1  => 'combat'
	];

	/**
	 * @type array<string, string>
	 */
	public final const array ALCHEMY = [
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

	/**
	 * @type array<string, array<string, string>>
	 */
	public final const array BUILDINGS = [
		'canal' => [
			'notMaintained' => 'Der Kanal ist derzeit nicht nutzbar.',
			'fee'           => 'Die Nutzungsgebühr beträgt $fee.',
			'noFee'         => 'Es gibt keine Nutzungsgebühr.'
		],
		'market' => [
			'notMaintained' => 'Der Markt ist geschlossen.',
			'order'         => 'Marktordnung',
			'fee'           => 'Die Marktgebühr beträgt $fee.',
			'feePercent'    => 'Die Marktgebühr beträgt $fee % des Umsatzes.',
			'noFee'         => 'Es gibt keine Marktgebühr.',
			'allowed'       => 'Auf diesem Markt dürfen nur die folgenden Waren gehandelt werden: $goods',
			'forbidden'     => 'Die Marktaufsicht hat den Handel mit den folgenden Waren verboten: $goods',
			'noRules'       => 'Es gibt keine Handelsbeschränkungen.'
		],
		'port' => [
			'notMaintained' => 'Der Hafen ist derzeit außer Betrieb.',
			'fee'           => 'Die Liegegebühr beträgt $fee.',
			'noFee'         => 'Es gibt keine Liegegebühr.',
			'duty'          => 'Der Hafenmeister erhebt $duty % Zoll auf eingeführte Luxuswaren.'
		]
	];

	/**
	 * @type array<string, string>
	 */
	public final const array TRANSLATIONS = [
		'Stangenwaffen' => 'Speerkampf'
	];

	/**
	 * @type array<string, string>
	 */
	public final const array MISC = [
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
