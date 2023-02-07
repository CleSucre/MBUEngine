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

use pocketmine\scheduler\AsyncTask;
use Volatile;

class WebhookTask extends AsyncTask {
	private string $type;
	private string $url;
	private string|Volatile $message;
	private bool $withFile;
	private array $args;

	public function __construct(string $type, mixed $url, string|array $message, $withFile = false, ...$args) {
		$this->type = $type;
		$this->url = $url;
		$this->message = $message;
		$this->withFile = $withFile;
		$this->args = $args;
	}

	public function onRun() : void {
		if ($this->withFile) {
			Webhooks::sendWithFile($this->url, $this->message, false, ...$this->args);
		} else {
			Webhooks::send($this->type, $this->url, $this->message, false);
		}
	}
}
