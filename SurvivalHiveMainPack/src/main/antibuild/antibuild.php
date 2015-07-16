<?php


namespace main\antibuild;

use pocketmine\plugin\PluginBase;
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

	class antibuild implements Listener
	{
		private $plugin;
		
		public function __construct(Plugin $plugin){
			$this->plugin = $plugin;
		}
		
		public function onPlayerPlaceBlock(BlockPlaceEvent $event)
		{
			If(!($event->getPlayer()->isOp()))
			{
				$event->setCancelled(true);
			}
		}
		
		public function onBlockBreakEvent(BlockBreakEvent $event)
		{
			If(!($event->getPlayer()->isOp()))
			{
				$event->setCancelled(true);
			}
		}	
	}
