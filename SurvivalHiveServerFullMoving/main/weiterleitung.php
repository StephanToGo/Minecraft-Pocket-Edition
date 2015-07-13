<?php

namespace main;

use pocketmine\utils\TextFormat as MT;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\Config;

class weiterleitung extends PluginBase implements Listener

{

	public function onEnable()
	{
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		if (!file_exists($this->getDataFolder()))
		{
			@mkdir($this->getDataFolder(), true);
		}
		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, ["IP" => "127.0.0.1","Port" => "19132",]);
	}

	public function onPlayerKickEvent(PlayerKickEvent $event)
	{
		$name = $event->getPlayer()->getName();
		$p = $event->getPlayer();
		$reason = $event->getReason();
			if($reason == "disconnectionScreen.serverFull")
			{
				$pl = $event->getPlayer();
				$addr1 = $this->config->get("IP");
				$addr2 = $this->config->get("Port");
				
				$ft = $this->getServer()->getPluginManager()->getPlugin("FastTransfer");
				if (!$ft)
				{
					$this->getLogger()->info("FAST TRANSFER NOT INSTALLED");
					return;
				}
				$this->getLogger()->info(MT::YELLOW."$name transfer to $addr1 $addr2");
				$ft->transferPlayer($pl,$addr1,$addr2);
				$event->setCancelled(true);
			}
	}

	public function onDisable()
	{
		$this->getLogger()->info("Plugin unloaded!");
	}
}
