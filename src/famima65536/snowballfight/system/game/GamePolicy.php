<?php

namespace famima65536\snowballfight\system\game;

class GamePolicy {
	public function __construct(private ?int $numberOfTeams=null, private ?int $memberPerTeam=null){
	}

	public function satisfiedBy(IGame $game): bool{
		return
			($this->numberOfTeams === null or $game->getNumberOfTeams() === $this->numberOfTeams) and
			($this->memberPerTeam === null or $game->getMemberPerTeam() === $this->memberPerTeam) and
			!$game->isFull();
	}
}