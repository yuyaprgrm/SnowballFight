<?php

namespace famima65536\snowballfight\system\game\participant;

use famima65536\snowballfight\system\game\IGame;
use famima65536\snowballfight\system\game\Team;
use famima65536\snowballfight\system\user\User;
use pocketmine\player\Player;

class Participant {

	protected int $throw = 0;
	protected int $attack = 0;
	protected int $attacked = 0;
	private IGame $game;
	private Team $team;

	private ?Player $player = null;

	public function __construct(private string $xuid, IGame $game, Team $team){
		$this->game = $game;
		$this->team = $team;
	}

	public function getXuid(): string{
		return $this->xuid;
	}

	public function throwSnowball(){
		$this->throw += 1;
	}

	public function attack(){
		$this->attack += 1;
	}

	public function attacked(){
		$this->attacked += 1;
	}

	/**
	 * @return int
	 */
	public function getAttack(): int{
		return $this->attack;
	}

	/**
	 * @return int
	 */
	public function getAttacked(): int{
		return $this->attacked;
	}

	/**
	 * @return int
	 */
	public function getThrow(): int{
		return $this->throw;
	}

	public function getGame(): IGame{
		return $this->game;
	}

	public function getTeam(): Team{
		return $this->team;
	}

	public function attach(Player $player){
		$this->player = $player;
	}

	public function asPlayer(): ?Player{
		return $this->player;
	}

	public function equalToUser(User $user){
		return ($user->getXuid() === $this->xuid);
	}
}