<?php declare(strict_types=1);

namespace famima65536\snowballfight\utils;

use PHPUnit\Framework\TestCase;
use pocketmine\lang\Language;

final class ChatTest extends TestCase {

	private Chat $chat;

	protected function setUp(): void{
		$this->chat = new Chat(new Language("jpn", __DIR__ . "/../../resources/lang"));
	}

	public function testSystemChat(): void{
		$this->assertSame($this->chat->system("system.join.welcome"), "§7Sys >>§r§f Welcome to §l§bSnowball Fight 0.0.1");
		$this->assertSame($this->chat->system("system.join.broadcast", ["famima65536"]), "§7Sys >>§r§f famima65536が§a参加");
	}
}