<?php

namespace famima65536\snowballfight\system\user;

class BattleRecord {

	private int $play;
	private int $win;

	public function __construct(int $play, int $win){
		$this->play = $play;
		$this->win = $win;
	}

	public function getWinRate():float{
		return $this->win/$this->play;
	}

	public function winGame(): self{
		return new self($this->play+1, $this->win+1);
	}

	public function loseGame(): self{
		return new self($this->play+1, $this->win);
	}
}