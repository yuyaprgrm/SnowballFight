<?php

namespace famima65536\snowballfight\system\user;

class InMemoryUserRepository implements IUserRepository {

	/** @var User[] */
	private array $users = [];
	public function find(string $xuid): ?User{
		return clone $this->users[$xuid] ?? null;
	}

	public function save(User $user): void{
		$this->users[$user->getXuid()] = $user;
	}
}