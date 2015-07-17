<?php

namespace main\weiterleitung;

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
use main\debug\Debug;

class weiterleitung implements Listener
{
	private $plugin;
	private $debug;
	
	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
		$this->debug = new Debug($plugin);
	}
	

	public function onPlayerKickEvent(PlayerKickEvent $event)
	{
		$name = $event->getPlayer()->getName();
		$p = $event->getPlayer();
		$reason = $event->getReason();
			if($reason == "disconnectionScreen.serverFull")
			{
				$pl = $event->getPlayer();
				$addr1 = $this->plugin->config->get("WeiterleitungIP");
				$addr2 = $this->plugin->config->get("WeiterleitungPort");
				
				$ft = $this->getServer()->getPluginManager()->getPlugin("FastTransfer");
				if (!$ft)
				{
					$this->debug('FAST TRANSFER NOT INSTALLED');
					return;
				}
				$this->debug("$name transfer to $addr1 $addr2");
				$ft->transferPlayer($pl,$addr1,$addr2);
				 
				$event->setCancelled(true);
			}
	}
}
