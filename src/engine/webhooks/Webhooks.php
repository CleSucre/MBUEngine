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

namespace engine\webhooks;

use pocketmine\Server;

class Webhooks {
	public const TYPE_MESSAGE = "content";
	public const TYPE_EMBED = "embeds";
	public const NONE = 0;
	public const TEXT = 1;
	public const PATH = 2;

	/**
	 * @param ...$args
	 */
	public static function sendWithFile(string $url, mixed $message, bool $async = true, int $typeFile = self::PATH, ...$args) {
		if ($async) {
			Server::getInstance()->getAsyncPool()->submitTask(new WebhookTask("", $url, $message, true, $typeFile, ...$args));

			return;
		}

		$embed = json_decode($message, true);

		$POST = [
			"content" => "",
			"username" => "",
			"avatar_url" => "",
			//"tts" => false,
			"embeds" => $embed
		];
		if (count($POST['embeds']) == 0) {
			unset($POST['embeds']);
		}
		$ch = curl_init();

		$POSTFIELD = [
			"payload_json" => json_encode($POST)
		];

		if ($typeFile === self::PATH) {
			$fullPath = $args[0] . DIRECTORY_SEPARATOR . $args[1];
			$content = file_get_contents($fullPath);
			if ($content !== null && $args[0] !== null) {
				if (is_file($fullPath)) {
					$POSTFIELD[] = curl_file_create($fullPath, null, $args[1]);
				}
			}
		} elseif ($typeFile === self::TEXT) {
			$fullPath = $args[0] . DIRECTORY_SEPARATOR . $args[1];
			file_put_contents($fullPath, $args[2]);
			if (is_file($fullPath)) {
				$POSTFIELD[] = curl_file_create($fullPath, null, $args[1]);
			}
		}

		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($POST));

		curl_setopt_array($ch, [
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER => ["Content-Type: multipart/form-data; charset=utf-8"],
			CURLOPT_POSTFIELDS => $POSTFIELD,
			CURLOPT_SAFE_UPLOAD => true,
			CURLOPT_URL => $url,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_RETURNTRANSFER => true,
		]);

		curl_exec($ch);

		if (($typeFile === self::PATH && $args[2]) || ($typeFile === self::TEXT)) {
			unlink($args[0] . DIRECTORY_SEPARATOR . $args[1]);
		}

		if (isset($args[3])) {
			$args[3]();
		}
	}

	public static function send(string $type, string $url, mixed $message, bool $async = true) {
		if ($async) {
			Server::getInstance()->getAsyncPool()->submitTask(new WebhookTask($type, $url, $message));

			return;
		}

		$json_data = json_encode([
			$type => json_decode($message),
		]);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		$response = curl_exec($ch);

		curl_close($ch);

		if ($response === false) {
			Server::getInstance()->getLogger()->error("There was an error while executing the webhook curl request : " . curl_error($ch));
		}
	}

	/**
	 * @param ...$args
	 */
	public static function sendEmbed(string $url, ?string $title, ?string $subtitle, ?string $description, ?int $color, bool $async = true, int $typeFile = self::NONE, ...$args) : void {
		if ($typeFile !== self::NONE) {
			Webhooks::sendWithFile(
				$url,
				json_encode([[
					"title" => $title,
					"type" => "rich",
					"color" => $color,
					"fields" => [[
						"name" => $subtitle,
						"value" => $description
					]]
				]]),
				$async,
				$typeFile,
				...$args
			);
		} else {
			Webhooks::send(
				Webhooks::TYPE_EMBED,
				$url,
				json_encode([[
					"title" => $title,
					"type" => "rich",
					"color" => $color,
					"fields" => [[
						"name" => $subtitle,
						"value" => $description
					]]
				]]),
				$async
			);
		}
	}
}
