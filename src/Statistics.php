<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Magellan;

use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Exception\LemuriaException;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Statistics\Data\Singletons;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Statistics\Record;

class Statistics
{
	public function getTalents(Unit $unit): Singletons {
		$record = new Record(Subject::Talents->name, $unit);
		$data   = Lemuria::Statistics()->request($record)->Data();
		if ($data instanceof Singletons) {
			return $data;
		}
		throw new LemuriaException();
	}
}
