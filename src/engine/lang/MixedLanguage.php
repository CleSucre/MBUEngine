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

use pocketmine\lang\Language;
use pocketmine\lang\LanguageNotFoundException;
use pocketmine\utils\Utils;
use const pocketmine\LOCALE_DATA_PATH;

class MixedLanguage extends Language {
	public const LANGUAGE_MAP = [
		"fr" => "fra",
	];

	/**
	 * @throws LanguageNotFoundException
	 */
	public function __construct(string $lang, ?string $path = null, string $fallback = self::FALLBACK_LANGUAGE) {
		$this->langName = strtolower($lang);

		if ($path === null) {
			$path = LOCALE_DATA_PATH;
		}

		$this->lang = self::loadLang($path, $this->langName);
		$this->fallbackLang = self::loadLang($path, $fallback);
	}

	/**
	 * @return string[]
	 * @phpstan-return array<string, string>
	 */
	protected static function loadLang(string $path, string $languageCode, bool $isPocketmine = false) : array {
		$file = $path . DIRECTORY_SEPARATOR . $languageCode . ".json";
		if (file_exists($file)) {
			$data = Utils::assumeNotFalse(json_decode(file_get_contents($file), true), "Missing or inaccessible required resource files");
			$strings = array_map('stripcslashes', $data);
			if (count($strings) > 0) {
				return $strings;
			}
		}
		$file = $path . self::LANGUAGE_MAP[$languageCode] . ".ini";
		if (file_exists($file)) {
			$strings = array_map('stripcslashes', Utils::assumeNotFalse(parse_ini_file($file, false, INI_SCANNER_RAW), "Missing or inaccessible required resource files"));
			if (count($strings) > 0) {
				return $strings;
			}
		}

		throw new LanguageNotFoundException("Language \"$languageCode\" not found");
	}

	public function addPath(string $path, ?string $langNameForced = null) : void {
		if (is_dir($path)) {
			$this->lang = array_merge($this->lang, self::loadLang($path, $langNameForced ?? $this->langName));
		}
	}

	/**
	 * @throws LanguageNotFoundException
	 * @return string[]
	 * @phpstan-return array<string, string>
	 */
	public static function getLanguageList(string $path = "") : array {
		if ($path === "") {
			$path = LOCALE_DATA_PATH;
		}

		if (is_dir($path)) {
			$allFiles = scandir($path, SCANDIR_SORT_NONE);

			if ($allFiles !== false) {
				$files = array_filter($allFiles, function (string $filename) : bool {
					return substr($filename, -5) === ".json" || substr($filename, -4) === ".ini";
				});

				$result = [];

				foreach ($files as $file) {
					try {
						$code = explode(".", $file)[0];
						$strings = self::loadLang($path, $code);
						if (isset($strings["language.name"])) {
							$result[$code] = $strings["language.name"];
						}
					} catch (LanguageNotFoundException $e) {
						// no-op
					}
				}

				return $result;
			}
		}

		throw new LanguageNotFoundException("Language directory $path does not exist or is not a directory");
	}

	public function getAll() : array {
		return $this->lang;
	}
}
