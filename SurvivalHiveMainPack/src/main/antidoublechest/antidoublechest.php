<?php

namespace main\antidoublechest;


use pocketmine\utils\TextFormat as MT;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase as Plugin;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\server;
use pocketmine\level;
use pocketmine\item\Item;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\math\Vector3;
use main\main;

class antidoublechest implements Listener{

	private $plugin;

	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
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
			$event->getPlayer()->sendPopup(MT::RED ."Keine Doppelkisten! - no double chests!");
			if($this->plugin->cfg->get("debugmode") == "true"){$this->plugin->getServer()->getLogger()->info(MT::GREEN."Doppelkiste versucht zu setzten");}
			$event->setCancelled(true);
		}
	}
}
}
