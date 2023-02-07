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
use engine\system\SystemsManager;
use engine\utils\Settings;
use pocketmine\plugin\PluginBase;
use ReflectionException;

class Main extends PluginBase {
	private static self $instance;
	private LanguageManager $languageManager;
	private Settings $settings;
	private SystemsManager $systemsManager;

    /**
     * @throws ReflectionException
     */
    protected function onEnable() : void {
		// simply load everything
		self::$instance = $this;
		$this->languageManager = new LanguageManager($this);
		$this->settings = new Settings($this);
		$this->systemsManager = new SystemsManager($this);
	}

	public static function getInstance() : self {
		return self::$instance;
	}

	public function getLanguageManager() : LanguageManager {
		return $this->languageManager;
	}

	public function getSettings() : Settings {
		return $this->settings;
	}

	public function getSystemsManager() : SystemsManager {
		return $this->systemsManager;
	}
}
