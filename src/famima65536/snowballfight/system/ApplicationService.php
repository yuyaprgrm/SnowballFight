<?php

namespace famima65536\snowballfight\system;

use famima65536\snowballfight\system\game\Game;
use famima65536\snowballfight\system\game\ChooseGamePolicy;
use famima65536\snowballfight\system\game\GameRepository;
use famima65536\snowballfight\system\game\IGame;
use famima65536\snowballfight\system\game\participant\Participant;
use famima65536\snowballfight\system\game\participant\ParticipantRepository;
use famima65536\snowballfight\system\game\GameService;
use famima65536\snowballfight\system\game\stage\TestStage;
use famima65536\snowballfight\system\game\Team;
use famima65536\snowballfight\system\user\IUserRepository;
use famima65536\snowballfight\utils\Chat;
use InvalidArgumentException;
use pocketmine\item\Snowball;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;

class ApplicationService {

	public function __construct(private GameRepository $gameRepository, private IUserRepository $userRepository, private ParticipantRepository $participantRepository, private GameService $gameService, private PluginBase $plugin){
	}

	public function join(Player $player, ?ChooseGamePolicy $policy=null): Participant{
		$xuid = $player->getXuid();
		$participant = $this->participantRepository->find($xuid);

		if($participant !== null){
			throw new InvalidArgumentException("User has already joined");
		}

		$policy ??= new ChooseGamePolicy(2, 4);

		$game = $this->gameService->chooseGameToJoin($policy);
		if($game === null){
			$game = new Game($policy->numberOfTeams ?? 2, $policy->memberPerTeam ?? 4, new TestStage(Server::getInstance()->getWorldManager()->getDefaultWorld()));
			$this->gameRepository->attach($game);
			$this->plugin->getScheduler()->scheduleRepeatingTask(new StartGameTask($this, $game, 30), 20);
		}

		$user = $this->userRepository->find($xuid);
		$participant = $game->join($user);
		$participant->attach($player);
		$this->participantRepository->attach($participant);


		$joinMessage = Chat::getInstance()->game("game.join.broadcast", [$player->getName(), $game->getId(), $game->getParticipants()->count(), $game->getMax()]);
		/** @var Participant $p */
		foreach($game->getParticipants() as $p){
			$p->asPlayer()?->sendMessage($joinMessage);
		}
		$player->setDisplayName($participant->getTeam()->getColorFormat().$player->getName()."§f");
		return $participant;
	}


	public function throwSnowball(Player $from): void{
		$thrower = $this->participantRepository->find($from->getXuid());
		$thrower?->throwSnowball();
	}

	public function hitSnowball(Player $from, Player $to): void{
		$attacker = $this->participantRepository->find($from->getXuid());
		$attacked = $this->participantRepository->find($to->getXuid());
		if($attacker === null or $attacked === null){
			return;
		}

		$isLegal = $this->gameService->hitSnowball($attacker, $attacked);
		if($isLegal){
			$from->sendPopup("攻撃した！");
			$to->sendPopup("攻撃された！");
			$to->teleport($attacked->getGame()->getStage()->getSpawnPosition($attacked->getTeam()->getId()));
		}
	}

	public function startGame(Game $game): bool{
		if($game->getPhase() !== IGame::PHASE_PREPARE){
			throw new \LogicException("game is in illegal phase");
		}

		$snowball = VanillaItems::SNOWBALL()->setCount(16);

		foreach($game->getTeams() as $team){
			/** @var Team $team*/
			$position = $game->getStage()->getSpawnPosition($team->getId());
			foreach($team->getMembers() as $member){
				/** @var Participant $member */
				$player = $member->asPlayer();
				if($player !== null){
					$player->sendTitle("Start!");
					$player->getInventory()->setItemInHand($snowball);
					$player->teleport($position);
				}
			}
		}

		$game->start();

		$this->plugin->getScheduler()->scheduleRepeatingTask(new FinishGameTask($this, $game, 180), 20);

		return true;
	}

	public function finishGame(Game $game){
		/** @var Participant $participant */
		foreach($game->getParticipants() as $participant){
			$player = $participant->asPlayer();
			if($player !== null){
				$player->setImmobile();
				$player->sendTitle("Finished");
			}
		}

		$game->finish();

		$this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function()use($game):void{
			$this->resultGame($game);
		}), 20*5);
	}

	public function resultGame(Game $game){
		/** @var Participant $participant */
		foreach($game->getParticipants() as $participant){
			$player = $participant->asPlayer();
			if($player !== null){
				$player->setImmobile(false);
				$player->sendMessage("==================");
				$player->sendMessage("hit rate {$participant->getAttack()}/{$participant->getThrow()}");
				$player->sendMessage("==================");
			}
			$this->participantRepository->detach($participant);
		}
		$this->gameRepository->detach($game);

	}

}