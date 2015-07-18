<?php
namespace main\worldborder;

use pocketmine\utils\TextFormat as MT;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;
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
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use main\debug\Debug;

	class worldborder extends PluginTask
	{

		public function __construct(Plugin $owner)
		{
			parent::__construct($owner);
			$this->debug = new Debug($owner);
		}
	
		public function onRun($currentTick)
		{
			$pos11 = $this->getOwner()->worldborder['pos1'];
			$pos22 = $this->getOwner()->worldborder['pos2'];
			$welt = $this->getOwner()->worldborder['world'];
			$pos1 = explode(",", $pos11);
			$pos2 = explode(",", $pos22);
			
			$this->debug->onDebug("Worldborder: $pos11 $pos22 $welt");
			
			
			foreach($this->getOwner()->getServer()->getOnlinePlayers() as $p)
			{
				$name = strtolower($p->getName());
				$welt = strtolower($p->getLevel()->getName());
					
				$ppos = new Vector3($p->x,$p->y,$p->z);
							
				if(( min($pos1[0],$pos2[0]) <= $ppos->getX()) && (max($pos1[0],$pos2[0]) >= $ppos->getX()) && (min($pos1[1],$pos2[1]) <= $ppos->getY()) && (max($pos1[1],$pos2[1]) >= $ppos->getY()) && (min($pos1[2],$pos2[2]) <= $ppos->getZ()) && (max($pos1[2],$pos2[2]) >= $ppos->getZ()))
				{
							
				}
				else
				{
					$knockback = 3.0;
	
					if($pos1[0]>=$pos2[0])
					{
						$minX = $pos2[0];
						$maxX = $pos1[0];
					}
								else 
								{
									$minX = $pos1[0];
									$maxX = $pos2[0];
								}
								if($pos1[2]>=$pos2[2])
								{
									$minZ = $pos2[2];
									$maxZ = $pos1[2];
								}
								else
								{
									$minZ = $pos1[2];
									$maxZ = $pos2[2];
								}
								$x = $p->getX();
								$z = $p->getZ();
								$y = $p->getY();
	
								if($x <= $minX) 
								{
									$x = $minX + $knockback;
								}
								elseif($x >= $maxX) 
								{
									$x = $maxX - $knockback;
								}
	
								if($z <= $minZ) 
								{
									$z = $minZ + $knockback;
								}
					elseif($z >= $maxZ) 
					{
						$z = $maxZ - $knockback;
					}
								
					$p->teleport(new Vector3($x, $y, $z));
					$p->sendMessage(MT::RED.'End of World/Ende der Welt');
				}
			}
		}
	}
			
?>