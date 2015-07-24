<?php

namespace main\antifly;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\block\Block;
use pocketmine\command\CommandSender;

use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\Timings;

use pocketmine\item\Item;
use pocketmine\level\format\FullChunk;
use pocketmine\level\format\LevelProvider;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\math\AxisAlignedBB;
use pocketmine\metadata\MetadataValue;


use pocketmine\permission\PermissibleBase;
use pocketmine\permission\PermissionAttachment;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;
use pocketmine\scheduler\CallbackTask;
use pocketmine\tile\Sign;
use pocketmine\tile\Spawnable;
use pocketmine\tile\Tile;
use pocketmine\utils\ReversePriorityQueue;
use pocketmine\utils\TextFormat as MT;
use main\debug\Debug;

	class antifly extends PluginTask
	{
		private $debug;
				
		public function __construct(Plugin $owner) 
		{
			parent::__construct($owner);
			$this->debug = new Debug($owner);
		}
    
		public function onRun($currentTick)
		{
			foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
			{
				if($player->isOp()){return;}
				$name = $player->getName();
				$block = $player->getLevel()->getBlock(new Vector3($player->getFloorX(),$player->getFloorY()-2,$player->getFloorZ()));
				$block2 = $player->getLevel()->getBlock(new Vector3($player->getFloorX()+1,$player->getFloorY()-2,$player->getFloorZ()));
				$block3 = $player->getLevel()->getBlock(new Vector3($player->getFloorX(),$player->getFloorY()-2,$player->getFloorZ()+1));
				$block4 = $player->getLevel()->getBlock(new Vector3($player->getFloorX()+1,$player->getFloorY()-2,$player->getFloorZ()+1));
				$block5 = $player->getLevel()->getBlock(new Vector3($player->getFloorX()-1,$player->getFloorY()-2,$player->getFloorZ()));
				$block6 = $player->getLevel()->getBlock(new Vector3($player->getFloorX(),$player->getFloorY()-2,$player->getFloorZ()-1));
				$block7 = $player->getLevel()->getBlock(new Vector3($player->getFloorX()-1,$player->getFloorY()-2,$player->getFloorZ()-1));
				$block8 = $player->getLevel()->getBlock(new Vector3($player->getFloorX()+1,$player->getFloorY()-2,$player->getFloorZ()-1));
				$block9 = $player->getLevel()->getBlock(new Vector3($player->getFloorX()-1,$player->getFloorY()-2,$player->getFloorZ()+1));
				
				if($block->getID() == 0 && $block2->getID() == 0 && $block3->getID() == 0 && $block4->getID() == 0 && $block5->getID() == 0 && $block6->getID() == 0 && $block7->getID() == 0 && $block8->getID() == 0 && $block9->getID() == 0)
				{
					$coords = (round($player->getX()).','.round($player->getY()).','.round($player->getZ()));
										
					$this->debug->onDebug("Antifly $name in Luft");
													
					if(!(isset($this->getOwner()->antifly[$name])))
					{
						$this->debug->onDebug("Antifly $name ticket 1");
						$this->getOwner()->antifly[$name] = $coords;
					}
					else
					{
							$this->debug->onDebug("Antifly $name ticket 1 else");

															
							$x = round($player->getX());
							$y = round($player->getY());
							$z = round($player->getZ());
															
							$coords2 = explode(",", $this->getOwner()->antifly[$name]);
															
							$x2 = $coords2[0];
							$y2 = $coords2[1];
							$z2 = $coords2[2];
															
													
							$this->debug->onDebug("Antifly $name $x $y $z $x2 $y2 $z2");
							if($y<$y2)
							{	
								$this->debug->onDebug("Antifly $name faellt");
								unset ($this->getOwner()->antifly[$name]);
							}							
							if($y>$y2)
							{
								unset ($this->getOwner()->antifly[$name]);
								$player->kick('Flyhack');
								$this->debug->onDebug("Antifly $name HACKT!");
								
							}
							if($y=$y2)
							{
								$this->debug->onDebug("Antifly $name gleich");
								$this->getOwner()->antifly[$name] = $coords;
							}
						}
					
				}
				else
				{
					unset ($this->getOwner()->antifly[$name]);
				}
			}													
    	}
