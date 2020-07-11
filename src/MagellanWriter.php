<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Magellan;

use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Renderer\Writer;

class MagellanWriter implements Writer
{
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
	 * @param Id $party
	 * @return Writer
	 */
	public function render(Id $party): Writer {
		if (!$this->file) {
			throw new \RuntimeException('File has been closed.');
		}

		$this->writeHeader();

		if (!fclose($this->file)) {
			throw new \RuntimeException('Could not close file.');
		}
		$this->file = null;
		return $this;
	}

	/**
	 * Set variables.
	 */
	private function initVariables(): void {
		$this->variables['$DATE'] = time();
		$this->variables['$TURN'] = Lemuria::Calendar()->Round();
	}

	/**
	 * @param array
	 */
	private function writeData(array $data): void {
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$this->writeData($value);
			} else {
				if (is_int($key)) {
					$line = $value;
				} else {
					if (isset($this->variables[$value])) {
						$value = $this->variables[$value];
					}
					if (is_int($value)) {
						$line = $value . ';' . $key;
					} else {
						$line = '"' . $value . '";' . $key;
					}
				}
				fputs($this->file, $line . PHP_EOL);
			}
		}
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
	 * Write the Magellan VERSION header.
	 */
	private function writeHeader(): void {
		$this->writeData(self::HEADER);
	}
}
