<?php


namespace main;


use pocketmine\utils\TextFormat as MT;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\server;
use pocketmine\level;
use pocketmine\item\Item;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\math\Vector3;
use pocketmine\event\player\PlayerMoveEvent;

	class killplayery extends PluginBase implements Listener
	
	{
		public function onEnable()
		{
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
		}
		public function onPlayerFall(PlayerMoveEvent $event)
		{
			$y = $event->getPlayer()->getY();
			if($y < -25)
			{
		  	$event->getPlayer()->kill();
		  	$event->getPlayer()->sendMessage("Gefallen");
			}
		}
		
		public function onDisable()
		{
			$this->getLogger()->info("Plugin unloaded!");
		}
	}
