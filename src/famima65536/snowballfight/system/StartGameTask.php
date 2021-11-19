<?php

namespace famima65536\snowballfight\system;

use famima65536\snowballfight\system\game\Game;
use famima65536\snowballfight\system\game\GameService;
use famima65536\snowballfight\system\game\IGame;
use famima65536\snowballfight\system\game\participant\Participant;
use pocketmine\block\WoodenButton;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\StopSoundPacket;
use pocketmine\scheduler\Task;
use pocketmine\world\sound\ClickSound;

class StartGameTask extends Task {

	/**
	 * @param Game $game
	 * @param int $int
	 */
	public function __construct(private ApplicationService $applicationService, private Game $game, private int $time){
	}

	public function onRun(): void{
		if($this->game->getPhase() !== IGame::PHASE_PREPARE){
			$this->getHandler()->cancel();
			return;
		}
		$this->time--;

		if($this->time < 0){
			$this->getHandler()->cancel();
			$this->applicationService->startGame($this->game);
			return;
		}

		$msg = "開始まで {$this->time}";

		foreach($this->game->getParticipants() as $participant){
			/** @var Participant $participant */
			$player = $participant->asPlayer();
			if($player !== null){
				$player->sendTip($msg);
				$player->getWorld()->addSound($player->getPosition(), new ClickSound, [$player]);
			}
		}
	}
}