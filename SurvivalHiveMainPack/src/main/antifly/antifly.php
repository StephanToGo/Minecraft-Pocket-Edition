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
			$block = $player->getLevel()->getBlock(new Vector3($player->getFloorX(),$player->getFloorY()-1,$player->getFloorZ()));
			$block2 = $player->getLevel()->getBlock(new Vector3($player->getFloorX()+1,$player->getFloorY()-1,$player->getFloorZ()));
			$block3 = $player->getLevel()->getBlock(new Vector3($player->getFloorX(),$player->getFloorY()-1,$player->getFloorZ()+1));
			$block4 = $player->getLevel()->getBlock(new Vector3($player->getFloorX()+1,$player->getFloorY()-1,$player->getFloorZ()+1));
			$block5 = $player->getLevel()->getBlock(new Vector3($player->getFloorX()-1,$player->getFloorY()-1,$player->getFloorZ()));
			$block6 = $player->getLevel()->getBlock(new Vector3($player->getFloorX(),$player->getFloorY()-1,$player->getFloorZ()-1));
			$block7 = $player->getLevel()->getBlock(new Vector3($player->getFloorX()-1,$player->getFloorY()-1,$player->getFloorZ()-1));
			$block8 = $player->getLevel()->getBlock(new Vector3($player->getFloorX()+1,$player->getFloorY()-1,$player->getFloorZ()-1));
			$block9 = $player->getLevel()->getBlock(new Vector3($player->getFloorX()-1,$player->getFloorY()-1,$player->getFloorZ()+1));
				
			if($block->getID() == 0 and !$block->getID() == 10 and !$block->getID() == 11 and !$block->getID() == 8 and !$block->getID() == 9 and !$block->getID() == 182 and !$block->getID() == 126 and !$block->getID() == 44)
			{
				if($block2->getID() == 0 and !$block2->getID() == 10 and !$block2->getID() == 11 and !$block2->getID() == 8 and !$block2->getID() == 9 and !$block2->getID() == 182 and !$block2->getID() == 126 and !$block2->getID() == 44)
				{
					if($block3->getID() == 0 and !$block3->getID() == 10 and !$block3->getID() == 11 and !$block3->getID() == 8 and !$block3->getID() == 9 and !$block3->getID() == 182 and !$block3->getID() == 126 and !$block3->getID() == 44)
					{
						if($block4->getID() == 0 and !$block4->getID() == 10 and !$block4->getID() == 11 and !$block4->getID() == 8 and !$block4->getID() == 9 and !$block4->getID() == 182 and !$block4->getID() == 126 and !$block4->getID() == 44)
						{
							if($block5->getID() == 0 and !$block5->getID() == 10 and !$block5->getID() == 11 and !$block5->getID() == 8 and !$block5->getID() == 9 and !$block5->getID() == 182 and !$block5->getID() == 126 and !$block5->getID() == 44)
							{
								if($block6->getID() == 0 and !$block6->getID() == 10 and !$block6->getID() == 11 and !$block6->getID() == 8 and !$block6->getID() == 9 and !$block6->getID() == 182 and !$block6->getID() == 126 and !$block6->getID() == 44)
								{
									if($block7->getID() == 0 and !$block7->getID() == 10 and !$block7->getID() == 11 and !$block7->getID() == 8 and !$block7->getID() == 9 and !$block7->getID() == 182 and !$block7->getID() == 126 and !$block7->getID() == 44)
									{
										if($block8->getID() == 0 and !$block8->getID() == 10 and !$block8->getID() == 11 and !$block8->getID() == 8 and !$block8->getID() == 9 and !$block8->getID() == 182 and !$block8->getID() == 126 and !$block8->getID() == 44)
										{
											if($block9->getID() == 0 and !$block9->getID() == 10 and !$block9->getID() == 11 and !$block9->getID() == 8 and !$block9->getID() == 9 and !$block9->getID() == 182 and !$block9->getID() == 126 and !$block9->getID() == 44)
											{
												$coords = (round($player->getX()).','.round($player->getY()).','.round($player->getZ()));
												$this->debug->onDebug("Antifly $name in Luft");
												if(!(isset($this->antifly[$name])))
												{
													$this->debug->onDebug("Antifly $name ticket 1");
													$this->antifly[$name] = $this->antifly[$name] = $coords;
												}
												else
												{
													$this->debug->onDebug("Antifly $name ticket 2");
													$x = round($player->getX());
													$y = round($player->getY());
													$z = round($player->getZ());
														
													$coords2 = explode(",", $this->antifly[$name]);
														
													$x2 = $coords2[0];
													$y2 = $coords2[1];
													$z2 = $coords2[2];
														
													$this->debug->onDebug("Antifly $name $x $y $z $x2 $y2 $z2");
														
													if(!($y<$y2))
													{
														$player->kick('Flyhack');
														$this->debug->onDebug("Antifly $name HACKT!");
														unset ($this->antifly[$name]);
													}
													else
													{
														$this->debug->onDebug("Antifly $name faellt");
														unset ($this->antifly[$name]);
													}
												}
											}
											else
											{
												unset ($this->antifly[$name]);
											}
										}
										else
										{
											unset ($this->antifly[$name]);
										}
									}
									else
									{
										unset ($this->antifly[$name]);
									}
								}
								else
								{
									unset ($this->antifly[$name]);
								}
							}
							else
							{
								unset ($this->antifly[$name]);
							}
						}
						else
						{
							unset ($this->antifly[$name]);
						}
					}
					else
					{
						unset ($this->antifly[$name]);
					}
				}
				else
				{
					unset ($this->antifly[$name]);
				}
			}
			else
			{
				unset ($this->antifly[$name]);
			}
		}													
    	}
}
