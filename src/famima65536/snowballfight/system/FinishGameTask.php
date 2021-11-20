<?php

namespace famima65536\snowballfight\system;

use famima65536\snowballfight\system\game\Game;
use famima65536\snowballfight\system\game\GameService;
use famima65536\snowballfight\system\game\participant\Participant;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\scheduler\Task;

class FinishGameTask extends Task {

	private int $initialTime;
	private string $title;
	private string $subtitle;

	public function __construct(private ApplicationService $applicationService, private Game $game, private int $time){
		$this->initialTime = $time;
		$this->title = "Snowball Fight Game #{$game->getId()}";
		$this->subtitle = "";
		$pk = BossEventPacket::show(0, $this->title."\n".$this->subtitle, 100);
		$this->sendBossEventPacketToParticipants($pk);
	}

	public function onRun(): void{
		if(--$this->time < 0){
			$this->getHandler()->cancel();
			$this->sendBossEventPacketToParticipants(BossEventPacket::hide(0));
			$this->applicationService->finishGame($this->game);
			return;
		}
		$this->subtitle = "{$this->time}s";
		$this->sendBossEventPacketToParticipants(BossEventPacket::title(0, $this->title."\n\n".$this->subtitle));
		$this->sendBossEventPacketToParticipants(BossEventPacket::healthPercent(0, ($this->time/$this->initialTime)));
	}

	public function sendBossEventPacketToParticipants(BossEventPacket $pk){
		/** @var Participant $participant */
		foreach($this->game->getParticipants() as $participant){
			$player = $participant->asPlayer();
			if($player !== null and $player->isConnected()){
				$pk->bossActorUniqueId = $player->getId();
				$participant->asPlayer()?->getNetworkSession()?->sendDataPacket($pk);
			}
		}
	}
}