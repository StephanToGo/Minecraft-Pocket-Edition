<?php

namespace main\mutejoin;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\Plugin;
use main\main;

class mutejoin implements Listener 
{
	
	private $plugin;

	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
	}
	
	public function onJoin (PlayerJoinEvent $ev)
	{
		$ev->setJoinMessage("");
	}

	public function onQuit(PlayerQuitEvent $ev)
	{
		$ev->setQuitMessage("");
	} 

	public function onDeath(PlayerDeathEvent $ev)
	{
		$player = $ev->getEntity();
		
		if($player instanceof Player)
		{
			$ev->setDeathMessage("");
		}
	}
}