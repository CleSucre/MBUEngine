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

namespace engine\system\systems\anticheat;

class HackingPointsCache {
	const MAX_CACHE_TIME = 10;
	private array $points = [];

	public function add(int $points = 1) : void {
		$id = uniqid();
		$this->points[$id]["time"] = round(microtime(true), 5);
		$this->points[$id]["points"] = $points;
	}

	/**
	 * @param float $period in seconds
	 */
	public function getPointsFrom(float $period) : int {
		//remove points that are older than self::MAX_CACHE_TIME
		foreach ($this->points as $data) {
			$time = $data["time"];
			if ($time < round(microtime(true), 5) - self::MAX_CACHE_TIME) {
				unset($this->points[$time]);
			}
		}
		//count points
		$finalPoints = 0;
		foreach ($this->points as $data) {
			$time = $data["time"];
			if ($time < round(microtime(true), 5) - $period) {
				unset($this->points[$time]);
			} else {
				$finalPoints += $data["points"];
			}
		}

		return $finalPoints;
	}
}
