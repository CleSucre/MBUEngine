<?php

/*
 *
 *   __  __   ____    _   _   _____                   _
 *  |  \/  | | __ )  | | | | | ____|  _ __     __ _  (_)  _ __     ___
 *  | |\/| | |  _ \  | | | | |  _|   | '_ \   / _` | | | | '_ \   / _ \
 *  | |  | | | |_) | | |_| | | |___  | | | | | (_| | | | | | | | |  __/
 *  |_|  |_| |____/   \___/  |_____| |_| |_|  \__, | |_| |_| |_|  \___|
 *                                            |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author MBU Team
 * @link http://github.com/CleSucre
 *
 *
 */

namespace engine\player;

use engine\event\PlayerWalkEvent;
use pocketmine\entity\Location;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\player\Player;
use pocketmine\player\PlayerInfo;
use pocketmine\Server;

class CustomPlayer extends Player {
	private const MOVES_PER_TICK = 2;
	private const MOVE_BACKLOG_SIZE = 100 * self::MOVES_PER_TICK; //100 ticks backlog (5 seconds)
	private float $lastTimeMovement = 0;
    private Session $session;

    public function __construct(Server $server, NetworkSession $session, PlayerInfo $playerInfo, bool $authenticated, Location $spawnLocation, ?CompoundTag $namedtag) {
        $this->session = new Session();
        parent::__construct($server, $session, $playerInfo, $authenticated, $spawnLocation, $namedtag);
    }

    public function getSession() : Session {
        return $this->session;
    }

    /**
	 * Fires movement events and synchronizes player movement, every tick.
	 */
	protected function processMostRecentMovements() : void {
		$now = microtime(true);
		$multiplier = $this->lastMovementProcess !== null ? ($now - $this->lastMovementProcess) * 20 : 1;
		$exceededRateLimit = $this->moveRateLimit < 0;
		$this->moveRateLimit = min(self::MOVE_BACKLOG_SIZE, max(0, $this->moveRateLimit) + self::MOVES_PER_TICK * $multiplier);
		$this->lastMovementProcess = $now;

		$from = clone $this->lastLocation;
		$to = clone $this->location;

		$delta = $to->distanceSquared($from);
		$deltaAngle = abs($this->lastLocation->yaw - $to->yaw) + abs($this->lastLocation->pitch - $to->pitch);

		if ($delta > 0.0001 || $deltaAngle > 1.0) {
			$ev = new PlayerMoveEvent($this, $from, $to);

			$ev->call();

			if ($ev->isCancelled()) {
				$this->revertMovement($from);

				return;
			}

			if ($to->distanceSquared($ev->getTo()) > 0.01) { //If plugins modify the destination
				$this->teleport($ev->getTo());

				return;
			}

			$this->lastLocation = $to;
			$this->broadcastMovement();

			$horizontalDistanceTravelled = sqrt($distance = (($from->x - $to->x) ** 2) + (($from->z - $to->z) ** 2));
			if ($horizontalDistanceTravelled > 0) {
				$ev = new PlayerWalkEvent($this, $from, $to, $from->distance($to), $distance, microtime(true) - $this->lastTimeMovement);
				$ev->call();
				$this->lastTimeMovement = microtime(true);

				if ($ev->isCancelled()) {
					$this->lastLocation = $from;
					$this->revertMovement($from);

					return;
				}
				//TODO: check for swimming
				if ($this->isSprinting()) {
					$this->hungerManager->exhaust(0.01 * $horizontalDistanceTravelled, PlayerExhaustEvent::CAUSE_SPRINTING);
				} else {
					$this->hungerManager->exhaust(0.0, PlayerExhaustEvent::CAUSE_WALKING);
				}

				if ($this->nextChunkOrderRun > 20) {
					$this->nextChunkOrderRun = 20;
				}
			}
		}

		if ($exceededRateLimit) { //client and server positions will be out of sync if this happens
			$this->logger->debug("Exceeded movement rate limit, forcing to last accepted position");
			$this->sendPosition($this->location, $this->location->getYaw(), $this->location->getPitch(), MovePlayerPacket::MODE_RESET);
		}
	}
}
