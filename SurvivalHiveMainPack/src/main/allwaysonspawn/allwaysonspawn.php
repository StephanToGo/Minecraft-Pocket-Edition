<?php


namespace main\allwaysonspawn;

use pocketmine\utils\TextFormat as MT;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase as Plugin;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\level;
use pocketmine\item\Item;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use main\main;

	class allwaysonspawn implements Listener
	
	{
		private $plugin;
		
		public function __construct(Plugin $plugin){
			$this->plugin = $plugin;
		}
	
		public function onRespawn(PlayerRespawnEvent $event)
		{		
			$x = $this->plugin->getServer()->getDefaultLevel()->getSafeSpawn()->getX();
			$y = $this->plugin->getServer()->getDefaultLevel()->getSafeSpawn()->getY();
			$z = $this->plugin->getServer()->getDefaultLevel()->getSafeSpawn()->getZ();
			
			$event->setRespawnPosition(new Position($x, $y, $z));
			if($this->plugin->cfg->get("debugmode") == "true"){$this->plugin->getServer()->getLogger()->info(MT::GREEN."Zum Start respawnt");}
		}
		
		public function onJoin(PlayerJoinEvent $event)
		{
			$x = $this->plugin->getServer()->getDefaultLevel()->getSafeSpawn()->getX();
			$y = $this->plugin->getServer()->getDefaultLevel()->getSafeSpawn()->getY();
			$z = $this->plugin->getServer()->getDefaultLevel()->getSafeSpawn()->getZ();
				
			$event->getPlayer()->teleport($event->getPlayer()->getLevel()->getSafeSpawn());
			if($this->plugin->cfg->get("debugmode") == "true"){$this->plugin->getServer()->getLogger()->info(MT::GREEN."Zum Start teleportiert");}
		}
	}
