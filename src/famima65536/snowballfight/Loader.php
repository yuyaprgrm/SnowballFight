<?php


namespace famima65536\snowballfight;

use famima65536\snowballfight\utils\Chat;
use pocketmine\lang\Language;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use Webmozart\PathUtil\Path;

class Loader extends PluginBase {
	private Language $lang;

	public function onLoad(): void{
		$path = Path::join($this->getFile(), "resources", "lang");
		$this->lang = new Language("jpn", $path);
		Chat::setInstance(new Chat($this->lang));
	}

	public function onEnable(): void{
		Server::getInstance()->getPluginManager()->registerEvents(new EventListener($this), $this);
	}

	public function getLanguage(): Language{
		return $this->lang;
	}
}