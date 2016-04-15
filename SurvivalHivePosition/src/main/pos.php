<?php

namespace main;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\block\Block;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\math\AxisAlignedBB;
use pocketmine\metadata\MetadataValue;
use pocketmine\plugin\Plugin;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat;

class pos extends PluginBase implements Listener {
	
	public $schalter = array();
	
	public function onEnable() 
	{	
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getLogger()->info(MT::AQUA."-=SH=-Position loading...!");
    }
	
    public function onCommand(CommandSender $p, Command $command, $label, array $args)
    {
    	if($p instanceof Player) 
    	{
    		if(strtolower($command->getName()) == "shpos")
    		{
    			$id = $p->getID();
    			$name = strtolower($p->getName());
    		
    			if (! (in_array($id, $this->schalter)))
    			{
    				$this->schalter[$name] = $id;
    				$p->sendMessage("Position Eingeschaltet");
    				return true;
    			}
    			else
    			{
    				$index = array_search($id, $this->schalter);
    				unset($this->schalter[$index]);
    				$p->sendMessage("Position Ausgeschaltet");
    				return true;
    			}
    		}
    	}
    	else 
    	{
    	$p->sendMessage("Nur im Spiel moeglich");
    	return true;
    	}
    	break;
    }
    
	public function onBlockBreak(BlockBreakEvent $event)
	{
		$id = $event->getPlayer()->getID();
		
		if(in_array($id, $this->schalter))
		{
			$bl = $event->getBlock();
			$n = strtolower($event->getPlayer()->getName());
			$this->pos[$n] = new Vector3($bl->getX(),$bl->getY(),$bl->getZ());
			$event->getPlayer()->sendMessage("Position(" . $this->pos[$n]->getX() . "," . $this->pos[$n]->getY() . "," . $this->pos[$n]->getZ() . ")");
			$event->setCancelled(true);
		}
	}
	
	public function onBlockPlace(BlockPlaceEvent $event)
	{
		$id = $event->getPlayer()->getID();
	
		if(in_array($id, $this->schalter))
		{
			$bl = $event->getBlock();
			$n = strtolower($event->getPlayer()->getName());
			$this->pos[$n] = new Vector3($bl->getX(),$bl->getY(),$bl->getZ());
			$event->getPlayer()->sendMessage("Position(" . $this->pos[$n]->getX() . "," . $this->pos[$n]->getY() . "," . $this->pos[$n]->getZ() . ")");
			$event->setCancelled(true);
		}
	}
	
	public function onDisable()
	{
		$this->getLogger()->info(MT::AQUA."Plugin unloaded!");
	}
}