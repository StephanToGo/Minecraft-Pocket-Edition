<?php
namespace main\jump;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;
use pocketmine\permission\Permission;
use pocketmine\Server;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\tile\Tile;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat as MT;
use main\debug\Debug;

class jump implements Listener{

	private $plugin;

	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
		$this->debug = new Debug($plugin);
	}
    
    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) 
    {		
    	if(!($sender instanceof Player))
    	{
    		$sender->sendMessage(MT::RED."Nur im Spiel moeglich / Only ingame possible");
    		return true;
    	}
        switch($cmd->getName()) 
        {
         case "jump": 
         		
               	if(!(isset($args[0])))
               	{
               	$jumps = $this->plugin->cfg->get("Jumps");     
               	     foreach($jumps as $jump)
                     {
                     	$jumpdata = explode(",", $jump);
               	        $sender->sendMessage(MT::GREEN.$jumpdata[0].' in world '.$jumpdata[4]);
             	     }
            	    break;
           	    }
                if($args[0] == "add")
                {
                    if($sender->isOp())
                    {
                        if(isset($args[1]))
                        {
                            $jump = $args[1];
                            $sender->sendMessage("Jump hinzugefuegt " . $args[1]);
                            
                            $config = $this->plugin->cfg->getAll();
                            $items = $config["Jumps"]; 
                            $banid = ($jump.",".round($sender->getX()).",".round($sender->getY()).",".round($sender->getZ()).",".(strtolower($sender->getLevel()->getName())));      
                            $items[] = $banid;                 
                            $config["Jumps"] = $items;
                            $this->plugin->cfg->setAll($config);
                            $this->plugin->cfg->save();
                            break;
                        }
                        else
                        {
                            $sender->sendMessage(MT::RED."Bitte Jump Namen eingeben");
                            break;
                        }
                    }
                }
                if($args[0] == "del")
                {
                    if($sender->isOp())
                    {
                        if(isset($args[1]))
                        {
                        	$jumpname = $args[1];
                        	
                        	$jumps = $this->plugin->cfg->get("Jumps");
                        	
                        	foreach($jumps as $jump)
                        	{
                        		$jumpdata = explode(",", $jump);
                        		if($jumpdata[0] == $jumpname)
                        		{
                        			$config = $this->plugin->cfg->getAll();
                        			$items = $config["Jumps"];
                        			$banid = $jump;
                        			$key = array_search($banid, $items);
                        			unset($items[$key]);
                        			$sender->sendMessage("Erfolgreich entfernt ".$banid);
                        			$config["Jumps"] = $items;
                        			$this->plugin->cfg->setAll($config);
                        			$this->plugin->cfg->save();
                        			return;
                        		}
                        	}
                        	$sender->sendMessage(MT::RED."Jump nicht vorhanden");
                        	return;
                        }
                        else
                        {
                        	$sender->sendMessage(MT::RED."Bitte Jump Namen eingeben");
                        	return;
                        }                       
                    }   
                }
                if(isset($args[0]))
                {
                    $jumpname = $args[0];
                    
                    $jumps = $this->plugin->cfg->get("Jumps");
                    
                    foreach($jumps as $jump)
                    {
                    	$jumpdata = explode(",", $jump);
                    	if($jumpdata[0] == $jumpname)
                    	{
                    		$sender->sendMessage(MT::GREEN.'You jumped to '.$jumpname);
                    		$sender->teleport(Server::getInstance()->getLevelByName($jumpdata[4])->getSafeSpawn(new Position($jumpdata[1], $jumpdata[2], $jumpdata[3])));
                    		return;
                    	}
                    }
                    $sender->sendMessage(MT::RED.'Jump not exist');
                    return;
                }
                break;
           /* case "spawn":
            	$jump = 'spawn';
            	if($sender->getLevel()->getName() == 'lobby')
            	{
	            	$sender->sendMessage("Du bist zum Spawn gejumpt");
	            	$sender->teleport(Server::getInstance()->getLevelByName($this->jumps[$jump]['world'])->getSafeSpawn(new Position($this->jumps[$jump]['x'], $this->jumps[$jump]['y'], $this->jumps[$jump]['z'])));
	            	return;
            	}
            	else 
            	{
            		$pl = $sender->getPlayer();
            		$addr = '148.251.4.154';
            		$port = '19132';
            		$ft = $this->getServer()->getPluginManager()->getPlugin("FastTransfer");
            		if (!$ft) {
            			$this->getLogger()->info("FAST TRANSFER NOT INSTALLED");
            			$pl->sendMessage("Nothing happens!");
            			$pl->sendMessage("Somebody removed FastTransfer!");
            			return;
            		}
            		$this->getLogger()->info("FastTransfer being used hope it works!");
            		$this->getLogger()->info("- Player:  ".$pl->getName()." => ".
            				$addr.":".$port);
            		$ft->transferPlayer($pl,$addr,$port);
            		return;
            	}
            case "hub":
            	$jump = 'start';
            	if(($sender->getLevel()->getName() != 'dayz') || ($sender->getLevel()->getName() != 'inselkampf1'))
            	{
	            	$sender->sendMessage("Du bist zum Spawn gejumpt");
	            	$sender->teleport(Server::getInstance()->getLevelByName($this->jumps[$jump]['world'])->getSafeSpawn(new Position($this->jumps[$jump]['x'], $this->jumps[$jump]['y'], $this->jumps[$jump]['z'])));
	            	return;   
            	} 
            	else 
            	{
            		$pl = $sender->getPlayer();
            		$addr = '148.251.4.154';
            		$port = '19132';
            		$ft = $this->getServer()->getPluginManager()->getPlugin("FastTransfer");
            		if (!$ft) {
            			$this->getLogger()->info("FAST TRANSFER NOT INSTALLED");
            			$pl->sendMessage("Nothing happens!");
            			$pl->sendMessage("Somebody removed FastTransfer!");
            			return;
            		}
            		$this->getLogger()->info("FastTransfer being used hope it works!");
            		$this->getLogger()->info("- Player:  ".$pl->getName()." => ".
            				$addr.":".$port);
            		$ft->transferPlayer($pl,$addr,$port);
            		return;
            	}    */	
        }
        
       
    }
    
    public function playerBlockTouch(PlayerInteractEvent $event)
    {
    	$name = strtolower($event->getPlayer()->getName());
    
    	if($event->getBlock()->getID() == 323 || $event->getBlock()->getID() == 63 || $event->getBlock()->getID() == 68)
    	{
    		$sign = $event->getPlayer()->getLevel()->getTile($event->getBlock());
    			
    		if(!($sign instanceof Sign))
    		{
    			return;
    		}
    			
    		$sign = $sign->getText();
    		$i = $sign[1];
    		$i2 = $sign[2];
    		$i3 = $sign[3];
    		
    		$i13 = explode(" ", $i3);
    			
    		//$event->getPlayer()->sendMessage("$i13[0] $i13[1] / $i3");
    		
    		if($i13[0] == '/jump')
    		{
    			$event->getPlayer()->sendMessage("Du bist zu " . $i13[1] . " gejumpt");
    			$event->getPlayer()->teleport(Server::getInstance()->getLevelByName($this->jumps[$i13[1]]['world'])->getSafeSpawn(new Position($this->jumps[$i13[1]]['x'], $this->jumps[$i13[1]]['y'], $this->jumps[$i13[1]]['z'])));
    			return true;
    		}
    	}
    }
}
