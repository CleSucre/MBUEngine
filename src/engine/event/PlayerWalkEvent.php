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

namespace engine\event;

use pocketmine\entity\Location;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class PlayerWalkEvent extends PlayerEvent implements Cancellable {
	use CancellableTrait;
	private Location $from;
	private Location $to;
	private float $distance;
	private float $distanceHorizontally;
	private float $movementTime;

	public function __construct(Player $player, Location $from, Location $to, float $distance, float $distanceHorizontally, float $time) {
		$this->player = $player;
		$this->from = $from;
		$this->to = $to;
		$this->distance = $distance;
		$this->distanceHorizontally = $distanceHorizontally;
		$this->movementTime = $time;
	}

	public function getFrom() : Location {
		return $this->from;
	}

	public function getTo() : Location {
		return $this->to;
	}

	public function setTo(Location $to) : void {
		$this->to = $to;
	}

	public function getDistance() : float {
		return $this->distance;
	}

	public function getDistanceHorizontally() : float {
		return $this->distanceHorizontally;
	}

	public function getMovementTime() : float {
		return $this->movementTime;
	}
}
