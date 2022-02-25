<?php

namespace NewTheBuilder\PlayerInfo\Command;

use NewTheBuilder\PlayerInfo\Main;
use NewTheBuilder\PlayerInfo\Utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class PlayerInfoCommand extends Command {

    private static array $os = [
        DeviceOS::UNKNOWN => "unknown",
        DeviceOS::ANDROID => "Android",
        DeviceOS::IOS => "iPhone",
        DeviceOS::OSX => "mac",//macos
        DeviceOS::AMAZON => "fire",//fire tablet, fireOS
        DeviceOS::GEAR_VR => "gear vr",
        DeviceOS::HOLOLENS => "HoloLens",
        DeviceOS::WINDOWS_10 => "windows",
        DeviceOS::WIN32 => "windows",//32
        DeviceOS::DEDICATED => "unknown(dedicated)",
        DeviceOS::TVOS => " Apple TV",//tvOS
        DeviceOS::PLAYSTATION => "PlayStation",//Orbis OS
        DeviceOS::NINTENDO => "Nintendo Switch",//NX(development code name)
        DeviceOS::XBOX => "Xbox",
        DeviceOS::WINDOWS_PHONE => "Windows Phone",
    ];

    public function __construct() {
        parent::__construct("playerinfo", "Show all information of player", "/playerinfo <player>", ["pinfo"]);
        $this->setPermission("playerinfo.command.use");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        $config = new Config(Main::getInstance()->getDataFolder() . "Config.yml", Config::YAML);
        if (!$sender instanceof Player){
            $sender->sendMessage(Utils::getPrefix() . $config->get("Console_Command"));
            return true;
        }

        if (!$sender->hasPermission("playerinfo.command.use")){
            $sender->sendMessage(Utils::getPrefix() . $config->get("NoPermission"));
            return true;
        }

        if (!isset($args[0])){
            $sender->sendMessage(Utils::getPrefix() . "Usage: /playerinfo <player>");
            return true;
        }
        $player = Main::getInstance()->getServer()->getPlayerByPrefix($args[0]);

        if (!$player instanceof Player){
            $sender->sendMessage($config->get("Player_Offline"));
            return true;
        }
        $playerInfo = $player->getNetworkSession()->getPlayerInfo();
        if($playerInfo === null){
            Main::getInstance()->getLogger()->info("error playerInfo is null");
            return true;
        }
        $deviceos = $playerInfo->getExtraData()["DeviceOS"] ?? DeviceOS::UNKNOWN;//int
        $deviceModel = $playerInfo->getExtraData()["DeviceModel"] ?? "unknown";//string
        $osname = self::$os[$deviceos] ?? "unknown";
        var_dump($deviceos, $deviceModel, $osname);

        $sender->sendMessage(Utils::getPrefix() . "Informations " . Utils::getColor() . $player->getName() . " §f:");
        $sender->sendMessage(" ");
        $sender->sendMessage(Utils::getColor() . "- §fName : " . Utils::getColor() . $player->getName());
        if (Main::getInstance()->getServer()->getPluginManager()->getPlugin("EconomyAPI")){
            $money = Main::getInstance()->getServer()->getPluginManager()->getPlugin("EconomyAPI");
            $sender->sendMessage(Utils::getColor() . "- §fMoney : " . Utils::getColor() . $money->myMoney($player));
        }else{
            $sender->sendMessage("");
        }
        if (Main::getInstance()->getServer()->getPluginManager()->getPlugin("PurePerms")){
            $pureperms = Main::getInstance()->getServer()->getPluginManager()->getPlugin("PurePerms");
            $group = $pureperms->getUserDataMgr()->getGroup($player);
            $sender->sendMessage(Utils::getColor() . "- §fRank : " . Utils::getColor() . $group);
        }else{
            $sender->sendMessage("");
        }
        $sender->sendMessage(Utils::getColor() . "- §fIP : " . Utils::getColor() . $player->getNetworkSession()->getIp());
        $sender->sendMessage(Utils::getColor() . "- §fUUID : " . Utils::getColor() . $player->getUniqueId());
        $sender->sendMessage(Utils::getColor() . "- §fCID : " . Utils::getColor() . $player->getId());
        $sender->sendMessage(Utils::getColor() . "- §fOS : " . Utils::getColor() . $osname);
        $sender->sendMessage(" ");

        return true;
    }

}