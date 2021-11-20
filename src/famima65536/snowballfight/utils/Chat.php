<?php

namespace famima65536\snowballfight\utils;

use pocketmine\lang\Language;
use pocketmine\lang\Translatable;
use pocketmine\utils\SingletonTrait;

class Chat {
	use SingletonTrait;
	private Language $lang;

	public function __construct(Language $lang){
		$this->lang = $lang;
	}

	public function system(string $key, array $params = []): string{
		return $this->lang->translate(new Translatable("type.system", [0 => new Translatable($this->lang->get($key), $params)]));
	}

	public function game(string $key, array $params = []): string{
		return $this->lang->translate(new Translatable("type.game", [0 => new Translatable($this->lang->get($key), $params)]));
	}
}