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

namespace engine\system\systems;

use engine\Main;

abstract class SystemBase {
	protected Main $plugin;
	private bool $isEnabled = false;

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}

	public function enable() : void {
		$this->isEnabled = true;
	}

	public function disable() : void {
		$this->isEnabled = false;
	}

	public function isEnabled() : bool {
		return $this->isEnabled;
	}

	abstract function load() : bool;
}
