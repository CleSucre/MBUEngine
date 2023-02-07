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

namespace engine;

use engine\lang\LanguageManager;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase {
	private static self $instance;
    private LanguageManager $languageManager;

	protected function onEnable() : void {
        // simply load everything
		self::$instance = $this;
        $this->languageManager = new LanguageManager($this);
	}

	public static function getInstance() : self {
		return self::$instance;
	}

	public function getAntiCheatManager() : AntiCheatManager {
		return $this->antiCheatManager;
	}

    public function getLanguageManager() : LanguageManager {
        return $this->languageManager;
    }
}
