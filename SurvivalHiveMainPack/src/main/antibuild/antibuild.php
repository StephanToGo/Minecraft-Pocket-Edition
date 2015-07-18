<?php


namespace main\antibuild;

use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\server;
use pocketmine\level;
use pocketmine\item\Item;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\utils\TextFormat as MT;
use main\debug\Debug;

	class antibuild implements Listener
	{
		private $plugin;
		private $debug;
		
		public function __construct(Plugin $plugin){
			$this->plugin = $plugin;
			$this->debug = new Debug($plugin);
		}
		
		public function onPlayerPlaceBlock(BlockPlaceEvent $event)
		{
			If(!($event->getPlayer()->isOp()))
			{
				$event->setCancelled(true);
				$this->debug->onDebug('BlockPlaceEvent');
			}
		}
		
		public function onBlockBreakEvent(BlockBreakEvent $event)
		{
			If(!($event->getPlayer()->isOp()))
			{
				$event->setCancelled(true);
				$this->debug->onDebug('BlockBreakEvent');
			}
		}	
	}