<?php

namespace famima65536\snowballfight\system\game;

class GameRepository {
	/** @var IGame[] */
	private array $games;

	public function __construct(){
		$this->games = [];
	}

	public function find(int $id): ?IGame{
		return $this->games[$id] ?? null;
	}

	public function attach(IGame $game){
		$this->games[$game->getId()] = $game;
	}

	public function detach(IGame $game){
		$this->games[$game->getId()] = null;
	}

	public function findAll(){
		return $this->games;
	}
}