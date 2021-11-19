<?php

namespace famima65536\snowballfight;

use famima65536\snowballfight\system\ApplicationService;
use famima65536\snowballfight\system\game\GameRepository;
use famima65536\snowballfight\system\game\GameService;
use famima65536\snowballfight\system\game\participant\ParticipantRepository;
use famima65536\snowballfight\system\user\BattleRecord;
use famima65536\snowballfight\system\user\InMemoryUserRepository;
use famima65536\snowballfight\system\user\IUserRepository;
use famima65536\snowballfight\system\user\User;
use famima65536\snowballfight\utils\Chat;
use InvalidArgumentException;
use LogicException;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\color\Color;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use famima65536\snowballfight\Loader;
use pocketmine\item\Snowball;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\PlayerMetadataFlags;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class EventListener implements Listener {

	private Loader $plugin;
	private IUserRepository $userRepository;
	private ApplicationService $applicationService;

	public function __construct(Loader $plugin){
		$this->plugin = $plugin;
		$this->userRepository = new InMemoryUserRepository;
		$gameRepository = new GameRepository();
		$this->applicationService = new ApplicationService($gameRepository, $this->userRepository, new ParticipantRepository(), new GameService($gameRepository), $plugin);
	}

	public function onPlayerLogin(PlayerLoginEvent $event): void{
		$player = $event->getPlayer();
		$user = $this->userRepository->find($player->getXuid());
		if($user === null){
			$user = new User($player->getXuid(), new BattleRecord(0,0));
			$this->userRepository->attach($user);
		}
	}

	public function onPlayerJoin(PlayerJoinEvent $event): void{
		$player = $event->getPlayer();
		$event->setJoinMessage(Chat::getInstance()->system("system.join.broadcast", [$player->getName()]));
		$player->sendMessage(Chat::getInstance()->system("system.join.welcome"));
	}

	public function onPlayerInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		switch($event->getBlock()->getId()){
			case BlockLegacyIds::EMERALD_ORE:
				try{
					$participant = $this->applicationService->join($player);
				}catch(InvalidArgumentException $ex){
					$player->sendMessage("すでに参加済みです!");
					return;
				}catch(LogicException $ex){
					$player->sendMessage("エラー発生");
					return;
				}
				$player->sendMessage("ゲーム:#{$participant->getGame()->getId()}に参加しました");
				if($participant->getGame()->isFull()){
					$this->applicationService->startGame($participant->getGame());
				}
		}
	}

	public function onPlayerQuit(PlayerQuitEvent $event): void{
		$player = $event->getPlayer();
		$user = $this->userRepository->find($player->getXuid());
	}

	public function onProjectileHit(ProjectileHitEntityEvent $event){
		$attacker = $event->getEntity()->getOwningEntity();
		$attacked = $event->getEntityHit();
		if($attacker instanceof Player and $attacked instanceof Player){
			$this->applicationService->hitSnowball($attacker, $attacked);
		}
	}

	public function onMove(PlayerMoveEvent $event){
		$event->getPlayer()->sendTip((string) $event->getPlayer()->getPosition());
	}

	public function onUseItem(PlayerItemUseEvent $event){
		if($event->getItem() instanceof Snowball){
			$event->getPlayer()->getInventory()->setItemInHand(VanillaItems::SNOWBALL()->setCount(16));
		}
	}
}