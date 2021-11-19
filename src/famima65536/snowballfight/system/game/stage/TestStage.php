<?php

namespace famima65536\snowballfight\system\game\stage;

use pocketmine\world\Position;
use pocketmine\world\World;

class TestStage implements IStage {

	/** @var Position[] */
	private array $positions;

	public function __construct(World $world){
		$this->positions = [
			new Position(288, 74, 186, $world),
			new Position(288, 74, 186, $world),
			new Position(288, 74, 186, $world),
			new Position(288, 74, 186, $world),
			new Position(288, 74, 186, $world),
			new Position(288, 74, 186, $world),
			new Position(288, 74, 186, $world),
			new Position(288, 74, 186, $world),
			new Position(288, 74, 186, $world),
			new Position(288, 74, 186, $world),
			new Position(288, 74, 186, $world),
			new Position(288, 74, 186, $world),
			new Position(288, 74, 186, $world),
			new Position(288, 74, 186, $world),
			new Position(288, 74, 186, $world),
		];
	}


	public function getSpawnPosition(int $teamId): Position{
		return $this->positions[$teamId];
	}

	public function getWorld(): World{
		return $this->world;
	}

	public function getName(): string{
		return "テストステージ";
	}

}