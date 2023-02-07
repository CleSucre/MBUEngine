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
use engine\system\systems\anticheat\cheats\BaseCheat;
use engine\system\systems\SystemBase;

class AntiCheatSystem extends SystemBase {
	/** @var BaseCheat[] */
	private array $hacks = [];

	public function __construct(Main $plugin) {
		parent::__construct($plugin);
	}

	public function register(BaseCheat $anti) : bool {
		if (isset($this->hacks[$anti::getName()])) {
			$this->plugin->getLogger()->error("AntiCheat with name " . $anti::getName() . " already exists!");

			return false;
		}
		$this->plugin->getLogger()->debug("Registered AntiCheat " . $anti::getName());
		$this->hacks[$anti::getName()] = $anti;

		return true;
	}

	public function load() : bool {
		if (!$this->isEnabled()) {
			return false;
		}
		//$this->register(new AntiSpeedHack($this->plugin, $this->plugin->getSettings()->getAntiCheatData(AntiSpeedHack::getName())));
		//$this->register(new AntiAirJump($this->plugin, $this->plugin->getSettings()->getAntiCheatData(AntiAirJump::getName())));
		//$this->register(new AntiNoClip($this->plugin, $this->plugin->getSettings()->getAntiCheatData(AntiNoClip::getName())));
		return true;
	}
}
