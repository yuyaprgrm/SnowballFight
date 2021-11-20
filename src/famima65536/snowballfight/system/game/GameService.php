<?php

namespace famima65536\snowballfight\system\game;

use famima65536\snowballfight\system\game\participant\Participant;
use function PHPStan\Testing\assertType;

class GameService {

	public function __construct(private GameRepository $gameRepository){
	}

	/**
	 * @param Participant $from
	 * @param Participant $to
	 * @return bool whether attack is legal or not.
	 */
	public function hitSnowball(Participant $from, Participant $to): bool{
		if($from->getGame() === $to->getGame() and $from->getGame()->getPhase() === IGame::PHASE_IN_GAME and !$from->getTeam()->contains($to) and !$to->isTemporalNoDamage()){
			$from->attack();
			$to->attacked();
			$to->setTemporalNoDamage();
			return true;
		}

		return false;
	}

	public function chooseGameToJoin(ChooseGamePolicy $gamePolicy): ?Game{
		foreach($this->gameRepository->findAll() as $game){
			if($gamePolicy->satisfiedBy($game)){
				return $game;
			}
		}
		return null;
	}

}