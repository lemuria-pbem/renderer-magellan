<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Magellan\Header;

use Lemuria\Renderer\Magellan\Header;
use Lemuria\Renderer\Magellan\MailTo;

class Lemuria implements Header
{
	protected const GAME = 'Lemuria';

	protected const MAX_UNITS = 9999;

	protected const ERA = 1;

	protected string $game = self::GAME;

	protected int $maxUnits = self::MAX_UNITS;

	protected int $era = self::ERA;

	protected MailTo $mailTo;

	public function Game(): string {
		return $this->game;
	}

	public function MaxUnits(): int {
		return $this->maxUnits;
	}

	public function Era(): int {
		return $this->era;
	}

	public function MailTo(): MailTo {
		return $this->mailTo;
	}

	public function setGame(string $game): static {
		$this->game = $game;
		return $this;
	}

	public function setMaxUnits(int $maxUnits): static {
		$this->maxUnits = $maxUnits;
		return $this;
	}

	public function setEra(int $era): static {
		$this->era = $era;
		return $this;
	}

	public function setMailTo(MailTo $mailTo): static {
		$this->mailTo = $mailTo;
		return $this;
	}
}
