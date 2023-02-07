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

namespace engine\lang;

use engine\Main;
use engine\player\CustomPlayer;
use pocketmine\lang\Language;
use pocketmine\lang\LanguageNotFoundException;
use pocketmine\lang\Translatable;
use const pocketmine\LOCALE_DATA_PATH;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use ReflectionException;
use ReflectionProperty;

class LanguageManager {
	use SingletonTrait;
	const DIRECTORY_NAME = "locale-data";
	const DEFAULT_LANG = "eng";

	/** @var MixedLanguage[] */
	private array $languages = [];
	private Main $plugin;

	/**
	 * @throws ReflectionException
	 */
	public function __construct(Main $plugin) {
		self::setInstance($this);
		$this->plugin = $plugin;
		$this->loadLanguages();
	}

	/**
	 * @throws ReflectionException
	 */
	private function loadLanguages() : array {
		$patch = $this->plugin->getDataFolder() . self::DIRECTORY_NAME;
		@mkdir($patch);
		//TODO: let users edit language files without losing their changes
		$this->plugin->saveResource(self::DIRECTORY_NAME . "/fra.json", true);
		$this->plugin->saveResource(self::DIRECTORY_NAME . "/eng.json", true);
		// scan the DIRECTORY_NAME folder for languages files and read them
		foreach (MixedLanguage::getLanguageList($patch) as $code => $langName) {
			$this->languages[$code] = new MixedLanguage($code, $patch, self::DEFAULT_LANG);
			try {
				$this->languages[$code]->addPath(LOCALE_DATA_PATH);
			} catch (LanguageNotFoundException $e) {
				// prevent unknown language in pocketmine
				$this->languages[$code]->addPath(LOCALE_DATA_PATH, self::DEFAULT_LANG);
			}
		}
		// sync the new languages with already loaded languages by PocketMine-MP
		$this->syncPocketmineDefinitions();

		return $this->languages;
	}

	/**
	 * @throws ReflectionException
	 */
	private function syncPocketmineDefinitions() : void {
		$defaultLanguage = $this->plugin->getServer()->getLanguage();
		$reflection = new ReflectionProperty($defaultLanguage, "lang");
		$reflection->setAccessible(true);
		$reflection->setValue($defaultLanguage, array_merge(
			$reflection->getValue($defaultLanguage),
			$this->languages[self::DEFAULT_LANG]->getAll()
		));
	}

	public function getLanguage(string $lang = self::DEFAULT_LANG) : Language {
		return $this->languages[$lang] ?? $this->languages[self::DEFAULT_LANG];
	}

	public function broadcastMessage(string $text, array $params = []) : void {
		$translatable = new Translatable($text, $params);
		foreach (Server::getInstance()->getOnlinePlayers() as $player) {
			/** @var CustomPlayer $player */
			$player->sendMessage($translatable);
		}
		$server = $this->plugin->getServer();
		$server->getLogger()->info("broadcast -> " . $server->getLanguage()->translate($translatable));
	}
}
