<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Magellan;

class Writer
{
	private const IS_INT = ['Hiebwaffen' => true];

	private const HEADER = [
		'VERSION 67',
		'charset' => 'UTF-8',
		'locale' => 'de',
		'Spiel' => 'Lemuria',
		'Konfiguration' => 'Lemuria',
		'Basis' => 36,
		'noskillpoints' => 0,
		'max_units' => 1000,
		'Build' => '1.0.0',
		'date' => '$DATE',
		'Runde' => '$TURN',
		'Zeitalter' => 1,
		'mailto' => 'lemuria@online.de',
		'mailcmd' => 'Lemuria Befehle'
	];

	private const ISLAND = [
		['ISLAND 1', 'Name' => 'Kontinent Lemuria'],
		['ISLAND 2', 'Name' => 'Provinz Lemuriana'],
	];

	private const PARTY = [
		'PARTEI 1',
		'locale' => 'de',
		'age' => 5,
		'Optionen' => 2 + 8 + 256,
		'Typ' => 'Elfen',
		'Rekrutierungskosten' => 130,
		'Anzahl Personen' => 1,
		'Parteiname' => 'Lemurianer',
		'email' => 'lemuria@online.de',
		'banner' => 'Ambárandir Arannil, Hoher Herr der Caranthiri.'
	];

	private const REGION = [
		[
			'REGION 100 101',
			'id' => 36,
			'Name' => 'Oloolu',
			'Insel' => '1',
			'Terrain' => 'Gebirge',
			'visibility' => 'neighbour',
			'Silber' => 12342,
			'Bauern' => 100,
			'Pferde' => 15,
			'Unterh' => 250,
			'Rekruten' => 3,
			'Lohn' => 11
		],
		[
			'REGION 100 100',
			'id' => 72,
			'Name' => 'Lumpi',
			'Insel' => '2',
			'Terrain' => 'Ebene',
			'Beschr' => 'Hier wohnen die kurzläufigen Zwerghunde.',
			'visibility' => '',
			'Silber' => 1200500,
			'Bauern' => 11245,
			'Pferde' => 534,
			'Unterh' => 15000,
			'Rekruten' => 344,
			'Lohn' => 10,
			'PREISE',
			'Baum' => 1200,
			'Pferd' => 100,
			'Balsam' => 5,
			'Seide' => 134,
			'Holz' => 300,
			[
				'BURG 1',
				'Typ' => 'Handelsposten',
				'Name' => 'Markt',
				'Groesse' => 3,
				'Besitzer' => 10002,
				'Partei' => 1
			],
			[
				'EINHEIT 1',
				'Name' => 'Pulli',
				'Partei' => 1,
				'Anzahl' => 1,
				'Typ' => 'Elfen',
				'bewacht' => 1,
				'Kampfstatus' => 0,
				'weight' => 1500,
				'TALENTE',
				'Hiebwaffen' => '5500 10',
				'GEGENSTAENDE',
				'Silber' => 300,
				'Bihänder' => 1
			],
			[
				'EINHEIT 10001',
				'Name' => 'Vorratslager',
				'Partei' => 1,
				'Anzahl' => 1,
				'GEGENSTAENDE',
				'Balsam' => 111,
				'Seide' => 5,
				'Baum' => 125,
				'Pferd' => 235,
				'Silber' => 124325,
				'Holz' => 311
			],
			[
				'EINHEIT 10002',
				'Name' => 'Händler',
				'Partei' => 1,
				'Anzahl' => 1,
				'Burg' => 1,
				'GEGENSTAENDE',
				'Balsam' => 111,
				'Baum' => 25,
				'Holz' => 30,
				'Pferd' => 44,
				'Seide' => 5
			]
		]
	];

	/**
	 * @var resource|null
	 */
	private $file;

	/**
	 * @var array(string=>mixed)
	 */
	private $variables = [];

	/**
	 * @param string $path
	 */
	public function __construct(string $path) {
		$this->file = fopen($path, 'w');
		if (!$this->file) {
			throw new \RuntimeException('Could not open file ' . $path . '.');
		}
		$this->initVariables();
	}

	/**
	 * Close file if it has not been closed.
	 */
	public function __destruct() {
		if ($this->file) {
			$this->close();
		}
	}

	/**
	 * @throws \RuntimeException
	 */
	public function create(): void {
		if (!$this->file) {
			throw new \RuntimeException('File has been closed.');
		}

		$this->createExample();

		if (!fclose($this->file)) {
			throw new \RuntimeException('Could not close file.');
		}
		$this->file = null;
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
	 * Set variables.
	 */
	private function initVariables(): void {
		$this->variables['$DATE'] = time();
		$this->variables['$TURN'] = 123;
	}

	/**
	 * Test.
	 */
	private function createExample(): void {
		$this->exampleOutput(self::HEADER);
		$this->exampleOutput(self::ISLAND);
		$this->exampleOutput(self::PARTY);
		$this->exampleOutput(self::REGION);
	}

	/**
	 * @param array
	 */
	private function exampleOutput(array $data): void {
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$this->exampleOutput($value);
			} else {
				if (is_int($key)) {
					$line = $value;
				} else {
					if (isset($this->variables[$value])) {
						$value = $this->variables[$value];
					}
					if (isset(self::IS_INT[$key]) || is_int($value)) {
						$line = $value . ';' . $key;
					} else {
						$line = '"' . $value . '";' . $key;
					}
				}
				fputs($this->file, $line . PHP_EOL);
			}
		}
	}
}
