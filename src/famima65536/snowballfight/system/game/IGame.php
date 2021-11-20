<?php

namespace famima65536\snowballfight\system\game;

use famima65536\snowballfight\system\game\participant\Participant;
use famima65536\snowballfight\system\game\stage\IStage;
use famima65536\snowballfight\system\user\User;
use SplObjectStorage;

interface IGame {

	const PHASE_PREPARE = 0;
	const PHASE_IN_GAME = 1;
	const PHASE_FINISHED = 2;
	/**
	 * return game id
	 * @return int
	 */
	public function getId(): int;

	public function getPhase(): int;

	/**
	 * call to join participant into game.
	 * @param User $participant
	 * @return Participant
	 */
	public function join(User $new, Team $team): Participant;

	/**
	 * call to quit participant from game.
	 * @param Participant $participant
	 */
	public function quit(Participant $participant): void;

	/**
	 * return current participants of game.
	 * @return SplObjectStorage<Participant>
	 */
	public function getParticipants(): SplObjectStorage;

	public function getStage(): IStage;

	/**
	 * return whether game is full.
	 * when game is full, no one can join.
	 * @return bool
	 */
	public function isFull(): bool;

	public function getMemberPerTeam(): int;
	public function getNumberOfTeams(): int;

	/**
	 * call to start game.
	 */
	public function start(): void;

	/**
	 * call to finish game.
	 * in this phase, winner will be determined.
	 */
	public function finish(): void;

	/**
	 * call to clean up game.
	 */
	public function clean(): void;

	/**
	 * @return SplObjectStorage<Team, Team>
	 */
	public function getTeams(): SplObjectStorage;

	public function chooseTeamToJoin(): Team;

	public function canStart(): bool;

	public function getMinMemberOfTeam(): int;
}