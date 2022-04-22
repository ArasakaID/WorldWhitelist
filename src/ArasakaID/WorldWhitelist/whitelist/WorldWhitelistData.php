<?php

namespace ArasakaID\WorldWhitelist\whitelist;

use ArasakaID\WorldWhitelist\Main;
use pocketmine\utils\Config;

final class WorldWhitelistData {

    public function __construct(private Main $plugin, private Config $config)
    {
    }

    public function setWorldWhitelist(string $worldName, bool $whitelist): void {
        if($whitelist) {
            $this->config->setNested("$worldName.players", []);
            $this->config->setNested("$worldName.message", $this->plugin->getConfig()->get("default-whitelist-message"));
        } else {
            $this->config->removeNested($worldName);
        }
        $this->config->save();
    }

    public function setWhitelistMessage(string $worldName, string $message): void {
        $this->config->setNested("$worldName.message", $message);
        $this->config->save();
    }

    public function getWhitelistMessage(string $worldName): string {
        return $this->config->getNested("$worldName.message");
    }

    public function addPlayerToWorld(string $playerName, string $worldName): void {
        $players = array_merge($this->getWhitelistedPlayers($worldName), [$playerName]);
        $this->config->setNested("$worldName.players", $players);
        $this->config->save();
    }

    public function removePlayerFromWorld(string $playerName, string $worldName): void {
        $players = $this->getWhitelistedPlayers($worldName);
        unset($players[array_search($playerName, $players)]);
        $this->config->setNested("$worldName.players", $players);
        $this->config->save();
    }

    public function isPlayerWhitelisted(string $playerName, string $worldName): bool {
        return in_array($playerName, $this->getWhitelistedPlayers($worldName));
    }

    public function isWorldWhitelist(string $worldName): bool {
        return $this->config->get($worldName, null) !== null;
    }

    /**
     * @return string[]
     */
    public function getWhitelistedPlayers(string $worldName): array {
        return $this->config->getNested("$worldName.players", []);
    }

}