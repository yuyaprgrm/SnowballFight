<?php

namespace famima65536\snowballfight\system\game\stage;

use pocketmine\world\Position;

interface IStage {
	public function getName(): string;
	public function getSpawnPosition(int $teamId): Position;
}