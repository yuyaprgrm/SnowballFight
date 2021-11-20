<?php

namespace famima65536\snowballfight\system\game;

use famima65536\snowballfight\system\game\participant\Participant;
use famima65536\snowballfight\system\game\stage\IStage;
use famima65536\snowballfight\system\user\User;
use InvalidArgumentException;
use LogicException;
use pocketmine\utils\TextFormat;
use SplObjectStorage;

class Game implements IGame {

	private static int $currentId = -1;
	private int $max;
	private StartGamePolicy $startGamePolicy;

	public static function getNextId(): int{
		return ++self::$currentId;
	}

	protected int $id;
	protected int $phase = IGame::PHASE_PREPARE;

	/** @var SplObjectStorage<Participant> */
	protected SplObjectStorage $participants;

	protected IStage $stage;

	/** @var SplObjectStorage<Team>  */
	protected SplObjectStorage $teams;


	public function __construct(private int $numberOfTeams, private int $memberPerTeam, IStage $stage){
		$this->id = self::getNextId();
		if($this->numberOfTeams > 16){
			throw new InvalidArgumentException("number of teams cannot be over 16");
		}
		$this->max = $this->numberOfTeams*$this->memberPerTeam;
		$this->participants = new SplObjectStorage;
		$this->teams = new SplObjectStorage();
		for($i=0; $i<$this->numberOfTeams; $i++){
			$this->teams->attach(new Team($i, TextFormat::ESCAPE.dechex($i)));
		}

		$this->startGamePolicy = new StartGamePolicy(min_member_per_team: 1);
		$this->stage = $stage;
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): int{
		return $this->id;
	}

	/**
	 * @inheritDoc
	 */
	public function join(User $new, ?Team $team=null): Participant{
		if($this->isFull()){
			throw new LogicException("Game is full");
		}
		if($this->hasJoined($new)){
			throw new InvalidArgumentException("User has already joined");
		}
		if($team === null){
			$team = $this->chooseTeamToJoin();
		}
		$participant = new Participant($new->getXuid(), $this, $team);
		$team->join($participant);
		$this->participants->attach($participant);
		return $participant;
	}

	public function hasJoined(User $user): bool{
		foreach($this->participants as $participant){
			/** @var Participant $participant */
			if($participant->equalToUser($user)){
				return true;
			}
		}
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function quit(Participant $participant): void{
		$participant->getTeam()->quit($participant);
		$this->participants->detach($participant);
	}

	/**
	 * @inheritDoc
	 */
	public function getParticipants(): SplObjectStorage{
		return $this->participants;
	}

	public function getStage(): IStage{
		return $this->stage;
	}

	public function getMax(): int{
		return $this->max;
	}

	/**
	 * @inheritDoc
	 */
	public function isFull(): bool{
		return ($this->participants->count() >= $this->max);
	}

	/**
	 * @inheritDoc
	 */
	public function start(): void{
		$this->phase = IGame::PHASE_IN_GAME;
	}

	/**
	 * @inheritDoc
	 */
	public function finish(): void{
		$this->phase = IGame::PHASE_FINISHED;
	}

	/**
	 * @inheritDoc
	 */
	public function clean(): void{
		$this->participants = new SplObjectStorage;
	}

	/**
	 * @inheritDoc
	 */
	public function getTeams(): SplObjectStorage{
		return $this->teams;
	}

	public function chooseTeamToJoin(): Team{
		$current = null;
		$min = $this->getMemberPerTeam();
		foreach($this->getTeams() as $team){
			assert($team instanceof Team);
			$count = $team->count();
			if($count < $min){
				$min = $count;
				$current = $team;
			}
		}

		return $current;
	}

	public function getNumberOfTeams(): int{
		return $this->numberOfTeams;
	}

	public function getMemberPerTeam(): int{
		return $this->memberPerTeam;
	}

	public function getPhase(): int{
		return $this->phase;
	}

	public function canStart(): bool{
		return $this->startGamePolicy->satisfiedBy($this);
	}

	public function getMinMemberOfTeam(): int{
		$min = $this->memberPerTeam;
		/** @var Team $team */
		foreach($this->teams as $team){
			$count = $team->count();
			if($count < $min){
				$min = $count;
			}
		}
		return $min;
	}
}