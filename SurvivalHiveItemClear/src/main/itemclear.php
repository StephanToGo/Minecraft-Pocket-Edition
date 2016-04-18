<?php

namespace main;

use pocketmine\utils\TextFormat as MT;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;

class itemclear extends PluginBase implements Listener 
{
	public function onEnable()
	{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info(MT::AQUA."-=SH=-ItemClear Plugin loading...!");
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) 
	{
		switch(strtolower($command->getName()))
		{
			case "itemclear":
				$this->onItemclear($sender);
				break;
		}
	}
	
	public function onItemclear($sender)
	{
		if($sender->isOp())
		{
			foreach($this->plugin->getServer()->getLevels() as $level)
			{
				$levelname = $level->getName();
				foreach($this->plugin->getServer()->getLevelbyName($levelname)->getEntities() as $entity)
				{
					if(!$entity instanceof Player){$entity->kill();}
				}
			}
		}
		$sender->sendMessage(MT::RED.'All entities cleared');
	}
	
	public function onDisable()
    {
    	$this->getLogger()->info(MT::AQUA."Plugin unloaded!");
    }
}