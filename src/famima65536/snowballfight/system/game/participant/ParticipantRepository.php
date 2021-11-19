<?php

namespace famima65536\snowballfight\system\game\participant;

class ParticipantRepository {

	/** @var Participant[] */
	private array $participants = [];

	public function __construct(){
	}

	public function find(string $xuid): ?Participant{
		return $this->participants[$xuid] ?? null;
	}

	public function attach(Participant $participant){
		$this->participants[$participant->getXuid()] = $participant;
	}

	public function detach(Participant $participant){
		$this->participants[$participant->getXuid()] = null;
	}

}