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

use engine\Main;
use engine\system\systems\SystemBase;
use pocketmine\player\Player;

class AntiCheatSystem extends SystemBase {
	/** @var HackingPointsCache[][] */
	private array $hackingPoints = [];
	private string $whiteListPermisison;

	public function __construct(Main $plugin) {
		parent::__construct($plugin);
	}

	public function getWhiteListPermisison() : string {
		return $this->whiteListPermisison;
	}

	public function setWhiteListPermisison(string $whiteListPermisison) : void {
		$this->whiteListPermisison = $whiteListPermisison;
	}

	public function addHackingPoints(Player $player, string $cheatId, int $maxPoint, int $period) : bool {
		$cache = $this->hackingPoints[$player->getXuid()][$cheatId] = $this->hackingPoints[$player->getXuid()][$cheatId] ?? new HackingPointsCache();
		$cache->add();
		if ($cache->getPointsFrom($period) >= $maxPoint) {
			return true;
		}

		return false;
	}

	function load() : bool {
		return true;
	}
}
