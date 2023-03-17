<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Magellan;

interface Header
{
	public function Game(): string;

	public function MaxUnits(): int;

	public function Era(): int;

	public function MailTo(): MailTo;
}
