<?php

namespace famima65536\snowballfight\system\game\stage;

use pocketmine\world\Position;
use pocketmine\world\World;

class DummyStage implements IStage {

	public function getName(): string{
		return "ダミーステージ";
	}

	public function getSpawnPosition(int $teamId): Position{
		return new Position(0,0,0, null);
	}

	public function getWorld(): ?World{
		return null;
	}
}