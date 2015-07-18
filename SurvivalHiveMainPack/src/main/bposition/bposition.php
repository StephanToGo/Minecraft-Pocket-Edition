<?php

namespace main\bposition;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\math\AxisAlignedBB;
use pocketmine\metadata\MetadataValue;
use pocketmine\plugin\Plugin;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat as MT;
use main\main;

class bposition implements Listener{
	
	public $schalter = array();
	
	private $plugin;

	public function __construct(Plugin $plugin)
	{
		$this->plugin = $plugin;
	}
    
	public function onBlockBreak(BlockBreakEvent $event)
	{
		if($this->plugin->cfg->get("debugmode") == "true"){$this->plugin->getServer()->getLogger()->info(MT::GREEN."CommandTest BPosition Break");}
		
		$id = $event->getPlayer()->getID();
		
		if(in_array($id, $this->schalter))
		{
			$bl = $event->getBlock();
			$n = strtolower($event->getPlayer()->getName());
			$this->pos[$n] = new Vector3($bl->getX(),$bl->getY(),$bl->getZ());
			$event->getPlayer()->sendMessage(MT::GOLD."Position(" . $this->pos[$n]->getX() . "," . $this->pos[$n]->getY() . "," . $this->pos[$n]->getZ() . ")");
			$event->setCancelled(true);
		}
	}
	
	public function onBlockPlace(BlockPlaceEvent $event)
	{
		if($this->plugin->cfg->get("debugmode") == "true"){$this->plugin->getServer()->getLogger()->info(MT::GREEN."CommandTest BPosition Place");}
		$id = $event->getPlayer()->getID();
	
		if(in_array($id, $this->schalter))
		{
			$bl = $event->getBlock();
			$n = strtolower($event->getPlayer()->getName());
			$this->pos[$n] = new Vector3($bl->getX(),$bl->getY(),$bl->getZ());
			$event->getPlayer()->sendMessage(MT::GOLD."Position(" . $this->pos[$n]->getX() . "," . $this->pos[$n]->getY() . "," . $this->pos[$n]->getZ() . ")");
			$event->setCancelled(true);
		}
	}
}