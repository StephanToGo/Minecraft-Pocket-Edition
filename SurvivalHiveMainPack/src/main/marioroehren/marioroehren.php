<?php
namespace main\marioroehren;

use pocketmine\utils\TextFormat as MT;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\server;
use pocketmine\item\Item;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\entity\EntityInventoryChangeEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\math\Vector3;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector2;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;
use main\debug\Debug;

class marioroehren extends PluginTask
{

	public function __construct(Plugin $plugin)
	{
		parent::__construct($plugin);
		$this->plugin = $plugin;
		$this->debug = new Debug($plugin);
	}

	public function onRun($currentTick)
	{
		foreach($this->plugin->getServer()->getOnlinePlayers() as $p)
		{
  			$name = strtolower($p->getName());
  			$welt = strtolower($p->getLevel()->getName());
  			$ppos = new Vector3($p->x,$p->y,$p->z);
  			$block = ($p->getX().",".$p->getY().",".$p->getZ()); 
  			
  			$config = $this->plugin->cfg->getAll();
			$items = $config["MarioRoehrenList"];

			foreach($items as $roehre)
			{
				$this->plugin->getLogger()->info("$roehre");
			}
  			if(!(isset($this->var[$name])))
  			{
	  			$pos1 = explode(",", $row[0]);
	  			$pos2 = explode(",", $row[1]);
	  			$pos = explode(",", $row[3]);
	  					
	  			if($welt == $row[2])
	  			{  						
	  				if((min($pos1[0],$pos2[0]) <= $ppos->getX()) && (max($pos1[0],$pos2[0]) >= $ppos->getX()) && (min($pos1[1],$pos2[1]) <= $ppos->getY()) && (max($pos1[1],$pos2[1]) >= $ppos->getY()) && (min($pos1[2],$pos2[2]) <= $ppos->getZ()) && (max($pos1[2],$pos2[2]) >= $ppos->getZ()))
		  			{
			  			if($row[6] == '')
			  			{
			  				$this->debug->onDebug("$name");
				  			$p->sendMessage(MT::GREEN ."Geportet");
				  			$p->teleport(Server::getInstance()->getLevelByName($row[5])->getSafeSpawn(new Position($pos[0], $pos[1], $pos[2])));
				  			break;
			  			}
			  			else
			  			{
			  				$this->debug->onDebug("$name");
							$addr = explode(",", $row[6]);
			  					
			  				$ft = $this->plugin->getServer()->getPluginManager()->getPlugin("FastTransfer");
			  				if (!$ft) 
			  				{
			  					$this->plugin->getLogger()->info("FAST TRANSFER NOT INSTALLED");
			  					return;
			  				}
			  								
			  				$this->debug->onDebug("$name $addr[0] $addr[1]");
			  				$p->teleport($p->getLevel()->getSafeSpawn());
			  				$ft->transferPlayer($p,$addr[0],$addr[1]);
			  			}		  				
		  			}
	  			}
  			}
		}
	}
}