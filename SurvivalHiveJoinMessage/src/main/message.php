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
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as MT;

class message extends PluginBase implements Listener 
{
	public function onEnable() 
	{	
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getLogger()->info(MT::AQUA.'-=SH=-JoinMessage loading...!');
		$this->saveDefaultConfig();	
		$this->joinmessage = $this->getConfig()->get('Join');
		$this->quitmessage = $this->getConfig()->get('Quit');
		$this->killedmessage = $this->getConfig()->get('Killed');
   	}
	
	public function onJoin (PlayerJoinEvent $ev)
	{
		$playername = $ev->getPlayer()->getName();
		$message = str_replace('{Player}', MT::GREEN.$playername, $this->joinmessage);
		$ev->setJoinMessage("$message");
	}

	public function onQuit(PlayerQuitEvent $ev)
	{
		$playername = $ev->getPlayer()->getName();
		$message = str_replace('{Player}', MT::GREEN.$playername, $this->quitmessage);
		$ev->setQuitMessage("$message");
	} 

	public function onDeath(PlayerDeathEvent $ev)
	{
		$player = $ev->getEntity();
		if($player instanceof Player){$ev->setDeathMessage('');}
	}
	
	public function onDisable()
	{
		$this->getLogger()->info(MT::AQUA.'Plugin unloaded!');
	}
}
