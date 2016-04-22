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

	class antidoublechest extends PluginBase implements Listener
	
	{
		public function onEnable()
		{
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->getLogger()->info(MT::AQUA."-=SH=-AntiDoubleChest loading...!");
		}
	
		public function onPlayerPlaceBlock(BlockPlaceEvent $event)
		{
			$blockID = $event->getBlock()->getID();
			$bl = $event->getBlock();
			$pos = $event->getBlock(new Vector3($bl->x,$bl->y,$bl->z));
		
			if ($blockID == 54)
			{				
				$block1 = $event->getBlock()->getLevel()->getBlock(new Vector3($bl->x+1,$bl->y,$bl->z));
				$block2 = $event->getBlock()->getLevel()->getBlock(new Vector3($bl->x-1,$bl->y,$bl->z));
				$block3 = $event->getBlock()->getLevel()->getBlock(new Vector3($bl->x,$bl->y,$bl->z+1));
				$block4 = $event->getBlock()->getLevel()->getBlock(new Vector3($bl->x,$bl->y,$bl->z-1));
						
				if($block1->getID() == 54 || $block2->getID() == 54 || $block3->getID() == 54 || $block4->getID() == 54)
				{
					$event->setCancelled(true);
				}
			}
		}
		
		public function onDisable()
		{
			$this->getLogger()->info(MT::AQUA."Plugin unloaded!");
		}
	}