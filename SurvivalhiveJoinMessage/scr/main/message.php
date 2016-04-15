<?php

namespace main;

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
use pocketmine\utils\TextFormat as MT;

class message extends PluginBase implements Listener 
{
	public function onEnable() 
	{	
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getLogger()->info(MT::AQUA."-=SH=-JoinMessage loading...!");
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
		if($player instanceof Player){$ev->setDeathMessage("");}
	}
	
	public function onDisable()
	{
		$this->getLogger()->info(MT::AQUA."Plugin unloaded!");
	}
}
