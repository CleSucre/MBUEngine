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

namespace engine\listener;

use engine\Main;

class ListenersManager {
	private Main $plugin;

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
		$this->loadListeners();
	}

	public function loadListeners() : void {
		$pluginManager = $this->plugin->getServer()->getPluginManager();

		$pluginManager->registerEvents(new PlayerWalkListener(), $this->plugin);
		$pluginManager->registerEvents(new PlayerCreationListener(), $this->plugin);
	}
}
