<?php

namespace main;

use pocketmine\utils\TextFormat as MT;
use pocketmine\permission\Permission;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\tile\Sign;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\level\Position;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\tile\Tile;

class lift extends PluginBase implements Listener
{

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info(MT::AQUA."Plugin -=SH=-LiftSign loading...!");
		if (!file_exists($this->getDataFolder())){@mkdir($this->getDataFolder(), true);}
		$this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML, array("Permissions" => true));
    	$this->permissions = $this->config->get('Permissions');
    }
 
    
    public function schildaendern(SignChangeEvent $event)
    {	
		$p = $event->getPlayer();
		$linien = $event->getLine(0);	

		if($linien == 'lh' || $linien == 'lr' || $linien == '[Lift hoch]' || $linien == '[Lift runter]')
		{
			if($this->permissions == true)
			{
				if($p->isOp() || $p->hasPermission('survivalhive.lift'))
				{
					if($linien == 'lh'){$event->setLine(0, '[Lift hoch]');}
					if($linien == 'lr'){$event->setLine(0, '[Lift runter]');}

					return true;
				}
				else
				{
					$p->sendMessage(MT::RED.'You dont have the permissions to use this command!');
					$event->setLine(0, MT::RED.'Broken');
					return true;
				}
			}
			else
			{		
				if($linien == 'lh'){$event->setLine(0, '[Lift hoch]');}
				if($linien == 'lr'){$event->setLine(0, '[Lift runter]');}
				return true;			
			}	
		}			
    }

    public function playerBlockTouch(PlayerInteractEvent $event)
    {
        if($event->getBlock()->getID() == 323 || $event->getBlock()->getID() == 63 || $event->getBlock()->getID() == 68)
        {
           $sign = $event->getPlayer()->getLevel()->getTile($event->getBlock());
           
            if(!($sign instanceof Sign)){return;}
            
            $sign = $sign->getText();
            
            if($sign[0] == '[Lift hoch]')
            {
            	for ($i = 1; $i <= 32; $i++) {
            		$bl = $event->getBlock();
            		$pos = $event->getBlock()->getLevel()->getBlock(new Vector3($bl->x,$bl->y+$i,$bl->z));
            		if($pos->getID() == 323 || $pos->getID() == 63 || $pos->getID() == 323)
            		{
            			$event->getPlayer()->teleport(new Position($bl->x,$bl->y+$i,$bl->z));
            			return true;
            		}
            	}
            	
            }
            if($sign[0] == '[Lift runter]')
            {
            	for($i = 32; $i >=0; $i--) {
            		$bl = $event->getBlock();
            		$pos = $event->getBlock()->getLevel()->getBlock(new Vector3($bl->x,$bl->y-$i,$bl->z));
            		if($pos->getID() == 323 || $pos->getID() == 63 || $pos->getID() == 323)
            		{
            			$event->getPlayer()->teleport(new Position($bl->x,$bl->y-$i,$bl->z));
            			return true;
            		}
            	}
            	 
            }
        }
    }
    
    public function onDisable()
    {
    	$this->getLogger()->info(MT::AQUA."Plugin unloaded!");
    }
}
