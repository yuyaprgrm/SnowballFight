<?php

namespace famima65536\snowballfight\system\game;

use famima65536\snowballfight\system\game\participant\Participant;
use SplObjectStorage;

class Team {

	/** @var SplObjectStorage<Participant> */
	private SplObjectStorage $members;

	public function __construct(private int $id, private string $colorFormat){
		$this->members = new SplObjectStorage;
	}

	/**
	 * @return int
	 */
	public function getId(): int{
		return $this->id;
	}

	public function join(Participant $new){
		$this->members->attach($new);
	}

	public function quit(Participant $member){
		$this->members->detach($member);
	}

	public function contains(Participant $participant): bool{
		return $this->members->contains($participant);
	}

	public function count(): int{
		return $this->members->count();
	}

	/**
	 * @return string
	 */
	public function getColorFormat(): string{
		return $this->colorFormat;
	}

	public function getMembers(): SplObjectStorage{
		return $this->members;
	}
}