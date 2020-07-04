<?php
declare(strict_types = 1);
namespace Lemuria\Tests\Renderer\Magellan;

use Lemuria\Tests\Test;

use Lemuria\Renderer\Magellan\Writer;

class WriterTest extends Test
{
	private const PATH = __DIR__ . '/work/output.cr';

	/**
	 * Clear work space.
	 */
	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		if (file_exists(self::PATH)) {
			if (!unlink(self::PATH)) {
				throw new \RuntimeException('Could not clear workspace.');
			}
		}
	}

	/**
	 * @test
	 * @return Writer
	 */
	public function construct(): Writer {
		$writer = new Writer(self::PATH);

		$this->assertFileExists(self::PATH);

		return $writer;
	}

	/**
	 * @test
	 * @depends construct
	 * @param Writer $writer
	 */
	public function destruct(Writer $writer): void {
		$writer = null;

		$this->assertFileExists(self::PATH);
	}

	/**
	 * @test
	 * @depends construct
	 * @param Writer $writer
	 */
	public function create(Writer $writer): void {
		$writer->create();

		$this->assertFileExists(self::PATH);
		$this->assertFileIsReadable(self::PATH);
		$this->assertFileIsWritable(self::PATH);

		$lines = explode(PHP_EOL, (string)file_get_contents(self::PATH));

		$this->assertGreaterThanOrEqual(14, count($lines));
		$this->assertTrue(in_array('"UTF-8";charset', $lines));
		$this->assertTrue(in_array('"Lemuria";Spiel', $lines));
	}
}
