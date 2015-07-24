<?php


namespace main\antifly;

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
use pocketmine\event\player\PlayerMoveEvent;

use pocketmine\math\Vector3;

use pocketmine\utils\TextFormat as MT;
use main\debug\Debug;

	class antiflyevent implements Listener
	{
		private $plugin;
		private $debug;
		public $antifly = array();
		

		public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
		$this->debug = new Debug($plugin);
		}
		
		public function onMove(PlayerMoveEvent $event)
		{
			If(!($event->getPlayer()->isOp()))
			{
				$player = $event->getPlayer();
				$name = $player->getName();
				//$player->setAllowFlight(false);
				//$player->setGamemode(0);
				
				$blockID = $event->getPlayer()->getLevel()->getBlock(new Vector3(round($player->getX()),round($player->getY())-1,round($player->getZ())))->getId();
				$blockID2 = $event->getPlayer()->getLevel()->getBlock(new Vector3(round($player->getX())+1,round($player->getY())-1,round($player->getZ())))->getId();
				$blockID3 = $event->getPlayer()->getLevel()->getBlock(new Vector3($player->x,$player->y-1,$player->z+1))->getId();
				$blockID4 = $event->getPlayer()->getLevel()->getBlock(new Vector3($player->x-1,$player->y-1,$player->z))->getId();
				$blockID5 = $event->getPlayer()->getLevel()->getBlock(new Vector3($player->x,$player->y-1,$player->z-1))->getId();
				$blockID6 = $event->getPlayer()->getLevel()->getBlock(new Vector3($player->x+1,$player->y-1,$player->z-1))->getId();
				$blockID7 = $event->getPlayer()->getLevel()->getBlock(new Vector3($player->x-1,$player->y-1,$player->z+1))->getId();
				$blockID8 = $event->getPlayer()->getLevel()->getBlock(new Vector3($player->x-1,$player->y-1,$player->z-1))->getId();
				$blockID9 = $event->getPlayer()->getLevel()->getBlock(new Vector3($player->x+1,$player->y-1,$player->z+1))->getId();
				
				if ($blockID != 0 || $blockID2 != 0 || $blockID3 != 0 || $blockID4 != 0 || $blockID5 != 0 || $blockID6 != 0 || $blockID7 != 0 || $blockID8 != 0 || $blockID9 != 0)
				{
					if(isset($this->plugin->antifly[$name]))
					{
						$this->debug->onDebug("move Antifly $blockID $blockID2 $blockID3 $blockID4 $blockID5 $blockID6 $blockID7 $blockID8 $blockID9");
						unset ($this->plugin->antifly[$name]);
						$this->debug->onDebug("move Antifly $name unset");
					}
				}
			}
		}


