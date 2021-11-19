<?php declare(strict_types=1);

namespace famima65536\snowballfight\system\game;


use famima65536\snowballfight\system\game\participant\Participant;
use famima65536\snowballfight\system\game\stage\DummyStage;
use famima65536\snowballfight\system\user\BattleRecord;
use famima65536\snowballfight\system\user\User;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase {
	private GameRepository $gameRepository;
	private GameService $gameService;

	protected function setUp(): void{
		$this->gameRepository = new GameRepository();
		$this->gameService = new GameService($this->gameRepository);
	}

	public function testJoinGame(){
		$user = new User("testA", new BattleRecord(0,0));
		$game = new Game(10, 1, new DummyStage());
		$this->gameRepository->attach($game);
		$participantA = $game->join($user);
		$this->assertTrue($game->hasJoined($user));
		$team = $game->chooseTeamToJoin();
		$this->assertNotSame($team, $participantA->getTeam());
	}

	public function testHitSnowball(){
		// setup
		$userA = new User("testA", new BattleRecord(0,0));
		$userB = new User("testB", new BattleRecord(0,0));
		$userC = new User("testC", new BattleRecord(0,0));
		$game = new Game(10, 1, new DummyStage());
		$anotherGame = new Game(10, 1, new DummyStage());
		$this->gameRepository->attach($game);
		$this->gameRepository->attach($anotherGame);
		$participantA = $game->join($userA);
		$participantB = $game->join($userB);
		$participantC = $anotherGame->join($userC);

		// check team
		$this->assertNotSame($participantB->getTeam(), $participantA->getTeam());
		$this->assertSame($participantB->getGame(), $participantA->getGame());
		$this->assertNotSame($participantA->getGame(), $participantC->getGame());

		// check hit snowball
		$result = $this->gameService->hitSnowball($participantA, $participantB);
		$this->assertTrue($result);
		$this->assertSame($participantA->getAttack(), 1);
		$this->assertSame($participantB->getAttacked(), 1);

		$result = $this->gameService->hitSnowball($participantA, $participantC);
		$this->assertNotTrue($result);
		$this->assertSame($participantA->getAttack(), 1);
		$this->assertSame($participantC->getAttacked(), 0);
	}


}