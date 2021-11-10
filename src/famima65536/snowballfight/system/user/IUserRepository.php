<?php

namespace famima65536\snowballfight\system\user;

interface IUserRepository {
	public function find(string $xuid): ?User;
	public function save(User $user): void;
}