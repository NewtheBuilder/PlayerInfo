<?php

namespace NewTheBuilder\PlayerInfo\Utils;

use NewTheBuilder\PlayerInfo\Main;
use pocketmine\utils\Config;

class Utils {

    /**
     * @return string
     */
    public static function getPrefix() : string {
        $config = new Config(Main::getInstance()->getDataFolder() . "Config.yml", Config::YAML);
        return $config->get("Prefix");
    }

    public static function getColor() : string {
        $config = new Config(Main::getInstance()->getDataFolder() . "Config.yml", Config::YAML);
        return $config->get("Color_Text");
    }

}