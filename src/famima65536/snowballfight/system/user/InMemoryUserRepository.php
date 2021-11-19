<?php

namespace famima65536\snowballfight\system\user;

class InMemoryUserRepository implements IUserRepository {

	/** @var User[] */
	private array $users = [];

	public function find(string $xuid): ?User{
		if(isset($this->users[$xuid])){
			return $this->users[$xuid];
		}
		return null;
	}

	public function attach(User $user): void{
		$this->users[$user->getXuid()] = $user;
	}
}