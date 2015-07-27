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
use main\debug\Debug;

class mutejoin implements Listener 
{
	
	private $plugin;

	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
		$this->debug = new Debug($plugin);
	}
	
	public function onJoin (PlayerJoinEvent $ev)
	{
		$this->debug->onDebug('Mutejoin onJoin');
		$ev->setJoinMessage('');
	}

	public function onQuit(PlayerQuitEvent $ev)
	{
		$this->debug->onDebug('Mutejoin onQuit');
		$ev->setQuitMessage('');
	} 

	public function onDeath(PlayerDeathEvent $ev)
	{
		$this->debug->onDebug('Mutejoin onDeath');
		$player = $ev->getEntity();
		
		if($player instanceof Player)
		{
			$ev->setDeathMessage('');
		}
	}
}