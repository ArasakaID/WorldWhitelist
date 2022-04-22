<?php

namespace ArasakaID\WorldWhitelist\command;

use ArasakaID\WorldWhitelist\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;

class WorldWhitelistCommand extends Command implements PluginOwned {

    public function __construct(private Main $plugin)
    {
        $this->setPermission("worldwhitelist.command");
        parent::__construct("worldwhitelist", "Allow selected players to join world", null, ["ww"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if(!$this->testPermission($sender)){
            return;
        }
        if(count($args) > 0){
            $first = $args[0];
            switch ($first) {
                case "set":
                    if (!isset($args[1]) or !isset($args[2]) or !in_array($args[2], ["on", "off"])) {
                        $sender->sendMessage(TextFormat::RED . "Usage: /ww set <worldName> [on/off]");
                        break;
                    }
                    $worldName = $args[1];
                    $status = $args[2];
                    if ($status == "on" && $this->validateWorld($sender, $worldName, false)) {
                        $this->plugin->getWorldWhitelistData()->setWorldWhitelist($worldName, true);
                        $sender->sendMessage(TextFormat::GREEN . "Whitelist for $worldName has been enabled");
                    } elseif ($status == "off" && $this->validateWorld($sender, $worldName)) {
                        $sender->sendMessage(TextFormat::RED . "Whitelist for $worldName has been disable");
                        $this->plugin->getWorldWhitelistData()->setWorldWhitelist($worldName, false);
                    }
                    break;
                case "add":
                    if (!isset($args[1]) or !isset($args[2])) {
                        $sender->sendMessage(TextFormat::RED . "Usage: /ww add <worldName> <playerName>");
                        break;
                    }
                    $worldName = $args[1];
                    $playerName = $args[2];
                    if($this->validateWorld($sender, $worldName)){
                        $sender->sendMessage(TextFormat::GREEN . "Player $playerName added to $worldName whitelist");
                        $this->plugin->getWorldWhitelistData()->addPlayerToWorld($playerName, $worldName);
                    }
                    break;
                case "remove":
                    if (!isset($args[1]) or !isset($args[2])) {
                        $sender->sendMessage(TextFormat::RED . "Usage: /ww remove <worldName> <playerName>");
                        break;
                    }
                    $worldName = $args[1];
                    $playerName = $args[2];
                    if($this->validateWorld($sender, $worldName)){
                        $sender->sendMessage(TextFormat::RED . "Player $playerName has been removed from $worldName whitelist");
                        $this->plugin->getWorldWhitelistData()->removePlayerFromWorld($playerName, $worldName);
                    }
                    break;
                case "setmessage":
                    if (!isset($args[1]) or !isset($args[2])) {
                        $sender->sendMessage(TextFormat::RED . "Usage: /ww setmessage <worldName> <message>");
                        break;
                    }
                    array_shift($args);
                    $worldName = array_shift($args);
                    $message = implode(" ", $args);
                    if($this->validateWorld($sender, $worldName)){
                        $sender->sendMessage(TextFormat::GREEN . "Whitelist message for $worldName has updated to $message");
                        $this->plugin->getWorldWhitelistData()->setWhitelistMessage($worldName, $message);
                    }
                    break;
                case "list":
                    if (!isset($args[1])) {
                        $sender->sendMessage(TextFormat::RED . "Usage: /ww list <worldName>");
                        break;
                    }
                    $worldName = $args[1];
                    if($this->validateWorld($sender, $worldName)){
                        $players = $this->plugin->getWorldWhitelistData()->getWhitelistedPlayers($worldName);
                        if(empty($players)){
                            $message = TextFormat::RED . "There's no players has whitelisted in $worldName";
                        } else {
                            $message = TextFormat::GREEN . "World $worldName has " . count($players) . " whitelisted (" . implode(", ", $players) . ")";
                        }
                        $sender->sendMessage($message);
                    }
                    break;
                default:
                    $this->sendHelpMessage($sender);
                    break;
            }
        } else {
            $this->sendHelpMessage($sender);
        }
    }

    private function validateWorld(CommandSender $sender, string $worldName, bool $checkWhitelist = true): bool {
        $worldManager = $sender->getServer()->getWorldManager();
        if(!$worldManager->isWorldGenerated($worldName)){
            $sender->sendMessage(TextFormat::RED . "World $worldName is not found!");
            return false;
        }

        if($checkWhitelist && !$this->plugin->getWorldWhitelistData()->isWorldWhitelist($worldName)){
            $sender->sendMessage(TextFormat::RED . "World $worldName is not whitelisted!");
            return false;
        }
        return true;
    }

    private function sendHelpMessage(CommandSender $sender): void {
        $sender->sendMessage(TextFormat::GREEN .
            "WorldWhitelist Commands List:\n" .
            "- /ww set <worldName> [on/off] §e(Set on/off whitelist of the world)\n§a" .
            "- /ww add <worldName> <playerName> §e(Add player name to the whitelist)\n§a" .
            "- /ww remove <worldName> <playerName> §e(Remove player name from the whitelist)\n§a" .
            "- /ww setmessage <worldName> <message> §e(Set custom whitelist message, you can coloring the message with '&')\n§a" .
            "- /ww list <worldName> §e(See players has been whitelisted)\n§a" .
            "- /ww help §e(See all WorldWhitelist Commands)"
        );
    }

    public function getOwningPlugin(): Main
    {
        return $this->plugin;
    }
}