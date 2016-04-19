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
		$this->getLogger()->info(MT::AQUA."-=SH=-JoinMessage loading...!");
		
		if (!file_exists($this->getDataFolder())){@mkdir($this->getDataFolder(), true);}
		$this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML, array("Join" => '{Player} joined...',"Quit" => '{Player} gone...',"Killed" => '{Player} killed by {Damager}'));
	
		$this->joinmessage = $this->config->get("Join");
		$this->quitmessage = $this->config->get("Quit");
		$this->killedmessage = $this->config->get("Killed");
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
		if($player instanceof Player){$ev->setDeathMessage("");}
	}
	
	public function onDisable()
	{
		$this->getLogger()->info(MT::AQUA."Plugin unloaded!");
	}
}
