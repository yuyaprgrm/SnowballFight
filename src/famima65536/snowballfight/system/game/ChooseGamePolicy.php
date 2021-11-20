<?php

namespace famima65536\snowballfight\system\game;

/**
 * @property-read int|null $numberOfTeams
 * @property-read int|null $memberPerTeam
 */
class ChooseGamePolicy {
	public function __construct(private ?int $numberOfTeams=null, private ?int $memberPerTeam=null){
	}

	public function satisfiedBy(IGame $game): bool{
		return
			($this->numberOfTeams === null or $game->getNumberOfTeams() === $this->numberOfTeams) and
			($this->memberPerTeam === null or $game->getMemberPerTeam() === $this->memberPerTeam) and
			$game->getPhase() === IGame::PHASE_PREPARE and
			!$game->isFull();
	}

	public function __get($name){
		return $this->$name;
	}
}