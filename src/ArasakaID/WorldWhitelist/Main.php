<?php

namespace ArasakaID\WorldWhitelist;

use ArasakaID\WorldWhitelist\command\WorldWhitelistCommand;
use ArasakaID\WorldWhitelist\whitelist\WorldWhitelistData;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\EventPriority;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Main extends PluginBase {

    private WorldWhitelistData $worldWhitelistData;

    protected function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvent(EntityTeleportEvent::class, function (EntityTeleportEvent $event): void {
            $player = $event->getEntity();
            $target = $event->getTo();
            $from = $event->getFrom();
            if($player instanceof Player && $target->world->getFolderName() !== $from->world->getFolderName()){
                $worldWhitelistData = $this->getWorldWhitelistData();
                $worldName = $target->world->getFolderName();
                if($worldWhitelistData->isWorldWhitelist($worldName) && !$worldWhitelistData->isPlayerWhitelisted($player->getName(), $worldName)) {
                    if ($this->getServer()->isOp($player->getName()) && $this->getConfig()->get("op-bypass", true)) {
                        return;
                    }
                    $player->sendMessage(TextFormat::colorize(str_replace("{WORLD}", $worldName, $worldWhitelistData->getWhitelistMessage($worldName))));
                    $event->cancel();
                }
            }
        }, EventPriority::MONITOR, $this);
        $this->getServer()->getCommandMap()->register($this->getName(), new WorldWhitelistCommand($this));

        $this->worldWhitelistData = new WorldWhitelistData($this, new Config($this->getDataFolder() . "worldwhitelist.yml", Config::YAML, []));
    }

    public function getWorldWhitelistData(): WorldWhitelistData
    {
        return $this->worldWhitelistData;
    }

}