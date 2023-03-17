<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Magellan;

interface MailTo
{
	public function Command(): string;

	public function Address(): string;
}
