<?php

namespace famima65536\snowballfight\system;

use famima65536\snowballfight\system\game\Game;
use famima65536\snowballfight\system\game\GamePolicy;
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
use pocketmine\player\Player;
use pocketmine\Server;

class ApplicationService {

	public function __construct(private GameRepository $gameRepository, private IUserRepository $userRepository, private ParticipantRepository $participantRepository, private GameService $gameService){
	}

	public function join(Player $player): Participant{
		$xuid = $player->getXuid();
		$participant = $this->participantRepository->find($xuid);

		if($participant !== null){
			throw new InvalidArgumentException("User has already joined");
		}

		$game = $this->gameService->chooseGameToJoin(new GamePolicy());
		if($game === null){
			$game = new Game(2, 1, new TestStage(Server::getInstance()->getWorldManager()->getDefaultWorld()));
			$this->gameRepository->attach($game);
		}

		$user = $this->userRepository->find($xuid);
		$participant = $game->join($user);
		$this->participantRepository->attach($participant);

		$player->setDisplayName($participant->getTeam()->getColorFormat().$player->getName()."§f");
		return $participant;
	}

	public function joinSoloGame(Player $player): Participant{
		$xuid = $player->getXuid();
		$participant = $this->participantRepository->find($xuid);

		if($participant !== null){
			throw new InvalidArgumentException("User has already joined");
		}

		$game = $this->gameService->chooseGameToJoin(new GamePolicy(memberPerTeam: 1));
		if($game === null){
			$game = new Game(10, 1, new TestStage(Server::getInstance()->getWorldManager()->getDefaultWorld()));
			$this->gameRepository->attach($game);
		}
		$user = $this->userRepository->find($xuid);
		$participant = $game->join($user);
		$this->participantRepository->attach($participant);

		$player->setDisplayName($participant->getTeam()->getColorFormat().$player->getName()."§f");
		return $participant;
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
		foreach($game->getTeams() as $team){
			/** @var Team $team*/
			$position = $game->getStage()->getSpawnPosition($team->getId());
			foreach($team->getMembers() as $member){
				/** @var Participant $member */
				$player = $this->getPlayerByXUID($member->getXuid());
				$player?->teleport($position);
				$player->sendMessage("リス地に移動しました");
			}
		}

		return true;
	}

	private function getPlayerByXUID(string $xuid): ?Player{
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			if($player->getXuid() === $xuid){
				return $player;
			}
		}
		return null;
	}

}