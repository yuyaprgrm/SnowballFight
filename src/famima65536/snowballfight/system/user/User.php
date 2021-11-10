<?php

namespace famima65536\snowballfight\system\user;

class User {

	private string $xuid;
	public BattleRecord $battleRecord;

	public function __construct(string $xuid, BattleRecord $battleRecord){
		$this->xuid = $xuid;
		$this->battleRecord = $battleRecord;
	}

	/**
	 * @return string
	 */
	public function getXuid(): string{
		return $this->xuid;
	}

}