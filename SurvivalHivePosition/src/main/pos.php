<?php

namespace main;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as MT;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;

class pos extends PluginBase implements Listener {

	public function onEnable() 
	{	
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getLogger()->info(MT::AQUA."Plugin -=SH=-Position loading...!");
    }

	public function onCommand(CommandSender $p, Command $command, $label, array $args) 
	{
		if(strtolower($command->getName()) == "shpos") 
		{
			if($p instanceof Player) 
			{
				$n = strtolower($p->getName());
				$this->pos[$n] = new Vector3(round($p->getX()),round($p->getY()),round($p->getZ()));
				$p->sendMessage(MT::AQUA."Position(" . $this->pos[$n]->getX() . "," . $this->pos[$n]->getY() . "," . $this->pos[$n]->getZ() . ")");
			}
		}
	}
	
	public function onDisable()
	{
		$this->getLogger()->info(MT::AQUA."Plugin unloaded!");			
	}
}