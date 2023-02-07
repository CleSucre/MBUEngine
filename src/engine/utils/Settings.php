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

namespace engine\utils;

use engine\Main;
use engine\system\systems\anticheat\AntiCheatSystem;
use engine\system\systems\SystemBase;
use pocketmine\utils\Config;

class Settings {
	private Main $plugin;
	private Config $anti_cheat;

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
		foreach ($plugin->getResources() as $resource) {
			$plugin->saveResource($resource->getFilename());
		}

		$this->anti_cheat = (new Config($plugin->getDataFolder() . "anti-cheat.yml", Config::YAML));
	}

	public function setupSystemSettings(SystemBase $system) : void {
		switch (get_class($system)) {
			case AntiCheatSystem::class:
				if (!$this->anti_cheat->get("enabled")) {
					// is disabled by default
					break;
				}
				$system->enable();
				//TODO: custom settings for the system
				break;
		}
	}
}
