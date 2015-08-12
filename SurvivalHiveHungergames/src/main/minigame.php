<?php

namespace main;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\block\Block;
use pocketmine\command\CommandSender;
use pocketmine\entity\Arrow;
use pocketmine\entity\DroppedItem;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\player\PlayerAchievementAwardedEvent;
use pocketmine\event\player\PlayerAnimationEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\Timings;
use pocketmine\inventory\BaseTransaction;
use pocketmine\inventory\BigShapelessRecipe;
use pocketmine\inventory\CraftingTransactionGroup;
use pocketmine\inventory\FurnaceInventory;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\inventory\SimpleTransactionGroup;
use pocketmine\inventory\StonecutterShapelessRecipe;
use pocketmine\item\Item;
use pocketmine\level;
use pocketmine\level\format\FullChunk;
use pocketmine\level\format\LevelProvider;
//use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\math\AxisAlignedBB;
use pocketmine\metadata\MetadataValue;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\Byte;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Double;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\Float;
use pocketmine\nbt\tag\Int;
use pocketmine\nbt\tag\String;
use pocketmine\network\protocol\AdventureSettingsPacket;
use pocketmine\network\protocol\AnimatePacket;
use pocketmine\network\protocol\DataPacket;
use pocketmine\network\protocol\EntityEventPacket;
use pocketmine\network\protocol\FullChunkDataPacket;
use pocketmine\network\protocol\Info as ProtocolInfo;
use pocketmine\network\protocol\LoginStatusPacket;
use pocketmine\network\protocol\MessagePacket;
use pocketmine\network\protocol\MovePlayerPacket;
use pocketmine\network\protocol\SetHealthPacket;
use pocketmine\network\protocol\SetSpawnPositionPacket;
use pocketmine\network\protocol\SetTimePacket;
use pocketmine\network\protocol\StartGamePacket;
use pocketmine\network\protocol\TakeItemEntityPacket;
use pocketmine\network\protocol\UpdateBlockPacket;
use pocketmine\network\SourceInterface;
use pocketmine\permission\PermissibleBase;
use pocketmine\permission\PermissionAttachment;
use pocketmine\plugin\Plugin;
use pocketmine\tile\Sign;
use pocketmine\tile\Chest;
use pocketmine\tile\Spawnable;
use pocketmine\tile\Tile;
use pocketmine\utils\ReversePriorityQueue;
use pocketmine\utils\TextFormat as MT;
use pocketmine\scheduler\PluginTask;
use pocketmine\scheduler\CallbackTask;

use pocketmine\tile\Chest as TileChest;

class sgworldborder extends PluginTask
{
	public function __construct(Plugin $owner)
	{
		parent::__construct($owner);
	}
	public function onRun($currentTick)
	{
		foreach($this->getOwner()->getServer()->getOnlinePlayers() as $p)
		{
			$name = strtolower($p->getName());
			$welt = strtolower($p->getLevel()->getName());	
			if($welt == $this->getOwner()->lobbyname)
			{
				$pos1 = explode(",", $this->getOwner()->lobbyareapos1);
				$pos2 = explode(",", $this->getOwner()->lobbyareapos2);
			}
			if($welt == $this->getOwner()->arena1name)
			{
				$pos1 = explode(",", $this->getOwner()->arena1areapos1);
				$pos2 = explode(",", $this->getOwner()->arena1areapos2);
			}
			if($welt == $this->getOwner()->arena2name)
			{
				$pos1 = explode(",", $this->getOwner()->arena2areapos1);
				$pos2 = explode(",", $this->getOwner()->arena2areapos2);
			}
			if($welt == $this->getOwner()->arena3name)
			{
				$pos1 = explode(",", $this->getOwner()->arena3areapos1);
				$pos2 = explode(",", $this->getOwner()->arena3areapos2);
			}
			if($welt == $this->getOwner()->arena4name)
			{
				$pos1 = explode(",", $this->getOwner()->arena4areapos1);
				$pos2 = explode(",", $this->getOwner()->arena4areapos2);
			}
			if($welt == $this->getOwner()->arena5name)
			{
				$pos1 = explode(",", $this->getOwner()->arena5areapos1);
				$pos2 = explode(",", $this->getOwner()->arena5areapos2);
			}		
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
class statuscheck extends PluginTask
{
	public function __construct(Plugin $owner)
	{
		parent::__construct($owner);
	}
	public function onRun($currentTick)
	{
			$spieleranzahl = count($this->getOwner()->getServer()->getOnlinePlayers());
			$time = time();
			foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
			{
				$name = $player->getName();	
				if($spieleranzahl < 2 && (!(isset($this->getOwner()->players))))
				{
					$player->sendPopUp(MT::AQUA.'Wait for other players');
					unset ($this->getOwner()->players);		
					unset ($this->getOwner()->arena1);
					unset ($this->getOwner()->arena2);
					unset ($this->getOwner()->arena3);
					unset ($this->getOwner()->arena4);
					unset ($this->getOwner()->arena5);		
					unset ($this->getOwner()->aftervotetimer);
					unset ($this->getOwner()->afterteleporttimer);				
					unset ($this->getOwner()->selectarena);
					unset ($this->getOwner()->chestgenerator);
					unset ($this->getOwner()->kisten);
				}
				else
				{
					if((!(isset($this->getOwner()->arena1))) && (!(isset($this->getOwner()->arena2))) && (!(isset($this->getOwner()->arena3))) && (!(isset($this->getOwner()->arena4))) && (!(isset($this->getOwner()->arena5))))
					{
						$player->sendPopUp(MT::AQUA.'Vote for arena with /vote *arenanumber*');
						$this->getOwner()->stats[$name]['Kills'] = 0;
					}
					else
					{
						if(!(isset($this->getOwner()->afterteleporttimer)))
						{
							if(!(isset($this->getOwner()->aftervotetimer)))
							{
								$this->getOwner()->aftervotetimer = $time+30;
								$player->sendPopUp(MT::RED.'Teleport timer started');
							}
							else
							{
								if($time < $this->getOwner()->aftervotetimer)
								{
									$seconds = $this->getOwner()->aftervotetimer - $time;
									$player->sendPopUp(MT::AQUA.'Wait for other arena votes '.MT::RED.$seconds.'sec');
								}
								if($time > $this->getOwner()->aftervotetimer)
								{
									if(!(isset($this->getOwner()->arena1)))
									{
										$arena1 = 0;
									}
									else
									{
										$arena1 = count ($this->getOwner()->arena1);
									}
									if(!(isset($this->getOwner()->arena2)))
									{
										$arena2 = 0;
									}
									else
									{
											$arena2 = count ($this->getOwner()->arena2);
									}
									if(!(isset($this->getOwner()->arena3)))
									{
										$arena3 = 0;
									}
									else
									{
										$arena3 = count ($this->getOwner()->arena3);
									}
									if(!(isset($this->getOwner()->arena4)))
									{
										$arena4 = 0;
									}
									else
									{
										$arena4 = count ($this->getOwner()->arena4);
									}
									if(!(isset($this->getOwner()->arena5)))
									{
										$arena5 = 0;
									}
									else
									{
										$arena5 = count ($this->getOwner()->arena5);
									}
									$this->getOwner()->getLogger()->info("$arena1 $arena2 $arena3 $arena4 $arena5");										
									if($arena1 > $arena2 && $arena1 > $arena3 && $arena1 > $arena4 && $arena1 > $arena5)
									{
										$arena = 1;
										$arenaname = $this->getOwner()->config->get("Arena1");
									}
									elseif($arena2 > $arena1 && $arena2 > $arena3 && $arena2 > $arena4 && $arena2 > $arena5)
									{
										$arena = 2;
										$arenaname = $this->getOwner()->config->get("Arena2");
									}
									elseif($arena3 > $arena1 && $arena3 > $arena2 && $arena3 > $arena4 && $arena3 > $arena5)
									{
										$arena = 3;
										$arenaname = $this->getOwner()->config->get("Arena3");
									}
									elseif($arena4 > $arena1 && $arena4 > $arena2 && $arena4 > $arena3 && $arena4 > $arena5)
									{
										$arena = 4;
										$arenaname = $this->getOwner()->config->get("Arena4");
									}
									elseif($arena5 > $arena1 && $arena5 > $arena2 && $arena5 > $arena3 && $arena5 > $arena4)
									{
										$arena = 5;
										$arenaname = $this->getOwner()->config->get("Arena5");
									}
									else
									{
										$player->sendPopUp(MT::AQUA.'no winner by arena voting -> random map loaded');
										$random = rand(1,5);
										if($random == 1)
										{
											$arena = 1;
											$arenaname = $this->getOwner()->config->get("Arena1");
										}
										if($random == 2)
										{
											$arena = 2;
											$arenaname = $this->getOwner()->config->get("Arena2");
										}
										if($random == 3)
										{
											$arena = 3;
											$arenaname = $this->getOwner()->config->get("Arena3");
										}
										if($random == 4)
										{
											$arena = 4;
											$arenaname = $this->getOwner()->config->get("Arena4");
										}
										if($random == 5)
										{
											$arena = 5;
											$arenaname = $this->getOwner()->config->get("Arena5");
										}
									}
									$spawnvar = 0;
									foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
									{
										if($this->getOwner()->arena1name == $arenaname)
										{
											$pos11 = explode(",", $this->getOwner()->arena1areapos1);
											$pos22 = explode(",", $this->getOwner()->arena1areapos2);
										}
										if($this->getOwner()->arena2name == $arenaname)
										{
											$pos11 = explode(",", $this->getOwner()->arena2areapos1);
											$pos22 = explode(",", $this->getOwner()->arena2areapos2);
										}
										if($this->getOwner()->arena3name == $arenaname)
										{
											$pos11 = explode(",", $this->getOwner()->arena3areapos1);
											$pos22 = explode(",", $this->getOwner()->arena3areapos2);
										}
										if($this->getOwner()->arena4name == $arenaname)
										{
											$pos11 = explode(",", $this->getOwner()->arena4areapos1);
											$pos22 = explode(",", $this->getOwner()->arena4areapos2);
										}
										if($this->getOwner()->arena5name == $arenaname)
										{
											$pos11 = explode(",", $this->getOwner()->arena5areapos1);
											$pos22 = explode(",", $this->getOwner()->arena5areapos2);
										}
										
										if($this->getOwner()->config->get("RandomPlayerSpawn"))
										{
											$x = rand($pos11[0], $pos22[0]);
											$y = 75;
											$z = rand($pos11[2], $pos22[2]);
											$player->teleport($this->getOwner()->getServer()->getLevelByName($arenaname)->getSafeSpawn(new Position($x,$y,$z)));
										}
										else
										{
											if($this->getOwner()->arena1name == $this->getOwner()->selectarena)
											{
												$anzahlspawnpos = count($this->getOwner()->randomplayerspawn)-1;
												$coords = explode(",", $this->getOwner()->randomplayerspawn[$spawnvar]);
												$player->teleport($this->getOwner()->getServer()->getLevelByName($arenaname)->getSafeSpawn(new Position($coords[0],$coords[1],$coords[2])));
												if($anzahlspawnpos > $spawnvar)$spawnvar = $spawnvar+1;
											}
											if($this->getOwner()->arena2name == $this->getOwner()->selectarena)
											{
												$anzahlspawnpos = count($this->getOwner()->randomplayerspawn2)-1;
												$coords = explode(",", $this->getOwner()->randomplayerspawn2[$spawnvar]);
												$player->teleport($this->getOwner()->getServer()->getLevelByName($arenaname)->getSafeSpawn(new Position($coords[0],$coords[1],$coords[2])));
												if($anzahlspawnpos > $spawnvar)$spawnvar = $spawnvar+1;
											}
											if($this->getOwner()->arena3name == $this->getOwner()->selectarena)
											{
												$anzahlspawnpos = count($this->getOwner()->randomplayerspawn3)-1;
												$coords = explode(",", $this->getOwner()->randomplayerspawn3[$spawnvar]);
												$player->teleport($this->getOwner()->getServer()->getLevelByName($arenaname)->getSafeSpawn(new Position($coords[0],$coords[1],$coords[2])));
												if($anzahlspawnpos > $spawnvar)$spawnvar = $spawnvar+1;
											}
											if($this->getOwner()->arena4name == $this->getOwner()->selectarena)
											{
												$anzahlspawnpos = count($this->getOwner()->randomplayerspawn4)-1;
												$coords = explode(",", $this->getOwner()->randomplayerspawn4[$spawnvar]);
												$player->teleport($this->getOwner()->getServer()->getLevelByName($arenaname)->getSafeSpawn(new Position($coords[0],$coords[1],$coords[2])));
												if($anzahlspawnpos > $spawnvar)$spawnvar = $spawnvar+1;
											}
											if($this->getOwner()->arena5name == $this->getOwner()->selectarena)
											{
												$anzahlspawnpos = count($this->getOwner()->randomplayerspawn5)-1;
												$coords = explode(",", $this->getOwner()->randomplayerspawn5[$spawnvar]);
												$player->teleport($this->getOwner()->getServer()->getLevelByName($arenaname)->getSafeSpawn(new Position($coords[0],$coords[1],$coords[2])));
												if($anzahlspawnpos > $spawnvar)$spawnvar = $spawnvar+1;
											}

										}		
										$player->setGamemode(0);
										$player->setHealth(20);
										if($player->isOnline())$player->getInventory()->clearAll();
										$player->sendMessage(MT::GREEN.'Arena '.MT::RED.$arena.MT::GREEN.' win');
										$player->sendPopUp(MT::AQUA.'Teleport event start now');
										$name = $player->getName();
										$this->getOwner()->players[$name] = $name;
									}
									$this->getOwner()->afterteleporttimer = $time+30;
									$this->getOwner()->selectarena = $arenaname;
								}
							}
						}
						else
						{
							if($time < $this->getOwner()->afterteleporttimer)
							{
								$seconds = $this->getOwner()->afterteleporttimer - $time;
								$player->sendPopUp(MT::AQUA.'Wait, game starts in '.MT::RED.$seconds.'sec');	
								
								if($this->getOwner()->config->get("RandomChestSpawn"))
								{
								//Chest Generator---
								//Chest Generator---
									if(!(isset($this->getOwner()->chestgenerator)))
									{
										$level = $this->getOwner()->selectarena;
										$this->getOwner()->getLogger()->info('STARTET');					
										$chestanzahl = $this->getOwner()->numberofchests;		
										for($i = 0; $i < $chestanzahl; $i++)
										{
											if($this->getOwner()->arena1name == $this->getOwner()->selectarena)
											{
												$pos1 = explode(",", $this->getOwner()->arena1areapos1);
												$pos2 = explode(",", $this->getOwner()->arena1areapos2);
											}
											if($this->getOwner()->arena2name == $this->getOwner()->selectarena)
											{
												$pos1 = explode(",", $this->getOwner()->arena2areapos1);
												$pos2 = explode(",", $this->getOwner()->arena2areapos2);
											}
											if($this->getOwner()->arena3name == $this->getOwner()->selectarena)
											{
												$pos1 = explode(",", $this->getOwner()->arena3areapos1);
												$pos2 = explode(",", $this->getOwner()->arena3areapos2);
											}
											if($this->getOwner()->arena4name == $this->getOwner()->selectarena)
											{
												$pos1 = explode(",", $this->getOwner()->arena4areapos1);
												$pos2 = explode(",", $this->getOwner()->arena4areapos2);
											}
											if($this->getOwner()->arena5name == $this->getOwner()->selectarena)
											{
												$pos1 = explode(",", $this->getOwner()->arena5areapos1);
												$pos2 = explode(",", $this->getOwner()->arena5areapos2);
											}		
											$randx = rand($pos1[0],$pos2[0]);
											$randz = rand($pos1[2],$pos2[2]);
											for ($i2 = 1; $i2 <= 100; $i2++) 
											{
												$yachse = (3 + $i2);	
												$block = $this->getOwner()->getServer()->getLevelbyName("$level")->getBlockIdAt($randx,$yachse,$randz);	
												$block0 = $this->getOwner()->getServer()->getLevelbyName("$level")->getBlockIdAt($randx,($yachse-1),$randz);
												$block5 = $this->getOwner()->getServer()->getLevelbyName("$level")->getBlockIdAt($randx,($yachse-2),$randz);
												$block1 = $this->getOwner()->getServer()->getLevelByName("$level")->getBlockIdAt($randx,($yachse+1),$randz);
												$block2 = $this->getOwner()->getServer()->getLevelByName("$level")->getBlockIdAt($randx,($yachse+2),$randz);
												$block3 = $this->getOwner()->getServer()->getLevelByName("$level")->getBlockIdAt($randx,($yachse+3),$randz);
												$block4 = $this->getOwner()->getServer()->getLevelByName("$level")->getBlockIdAt($randx,($yachse+4),$randz);
												
												if(($block == 0) && ($block0 != 0) && ($block0 != 54) && ($block5 != 0) && ($block1 == 0) && ($block2 == 0) && ($block3 == 0) && ($block4 == 0))
												{
								
													$chest = $this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($randx,$yachse,$randz), BLOCK::get(54), false, true);
													$nbt = new Compound("", [
															new Enum("Items", []),
															new String("id", Tile::CHEST),
															new Int("x", $randx),
															new Int("y", $yachse),
															new Int("z", $randz)
													]);
															
													$nbt->Items->setTagType(NBT::TAG_Compound);
													$tile = Tile::createTile("Chest", $this->getOwner()->getServer()->getLevelByName("$level")->getChunk($randx >> 4, $randz >> 4), $nbt);
													if($chest instanceof TileChest and $tile instanceof TileChest)
													{
														$chest->pairWith($tile);
														$tile->pairWith($chest);
													}	
													$kistencoords = ($randx.",".$yachse.",".$randz);
													$this->getOwner()->kisten[] = $kistencoords;	
															//Kisten füllen
													$truhe15 = $this->getOwner()->getServer()->getLevelbyName("$level")->getBlock(new Vector3($randx, $yachse, $randz));
													$chest15 = $this->getOwner()->getServer()->getLevelbyName("$level")->getTile($truhe15);
													if($chest15 instanceof Chest)
													{
														$rand = rand(1,10);			
														$chest15->getInventory()->clearAll();
														$inv = $chest15->getRealInventory();
																
														$ids = $this->getOwner()->itemids;
														$itemanzahl = $this->getOwner()->numberofitemsperchest;
														$ids[array_rand($ids)];
														for($i3 = 0; $i3 < $itemanzahl; $i3++)
														{
															$inv->addItem(Item::get($ids[mt_rand(0, count($ids) - 1)]));
														}				
													}
												}
											}
										}
										
										$this->getOwner()->chestgenerator = 1;
										//Chest Generator---
										$zahl = count($this->getOwner()->kisten);
										$this->getOwner()->getLogger()->info("Kisten $zahl");
									}
								}
								else
								{
									if(!(isset($this->getOwner()->chestgenerator)))
									{
										if($this->getOwner()->arena1name == $this->getOwner()->selectarena)
										{
											$level = $this->getOwner()->arena1name;
											foreach($this->getOwner()->randomchestspawn as $chestspawn)
											{
												$coords = explode(",", $chestspawn);
													
												$chest = $this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(54), false, true);
													
												$nbt = new Compound("", [
														new Enum("Items", []),
														new String("id", Tile::CHEST),
														new Int("x", $coords[0]),
														new Int("y", $coords[1]),
														new Int("z", $coords[2])
												]);
											
												$nbt->Items->setTagType(NBT::TAG_Compound);
												$tile = Tile::createTile("Chest", $this->getOwner()->getServer()->getLevelByName("$level")->getChunk($coords[0] >> 4, $coords[2] >> 4), $nbt);
												if($chest instanceof TileChest and $tile instanceof TileChest)
												{
													$chest->pairWith($tile);
													$tile->pairWith($chest);
												}
												$truhe15 = $this->getOwner()->getServer()->getLevelbyName("$level")->getBlock(new Vector3($coords[0], $coords[1], $coords[2]));
												$chest15 = $this->getOwner()->getServer()->getLevelbyName("$level")->getTile($truhe15);
												if($chest15 instanceof Chest)
												{
													$rand = rand(1,10);
													$chest15->getInventory()->clearAll();
													$inv = $chest15->getRealInventory();
														
													$ids = $this->getOwner()->itemids;
													$itemanzahl = $this->getOwner()->numberofitemsperchest;
													$ids[array_rand($ids)];
													for($i3 = 0; $i3 < $itemanzahl; $i3++)
													{
														$inv->addItem(Item::get($ids[mt_rand(0, count($ids) - 1)]));
													}
												}
											}
											$this->getOwner()->chestgenerator = 1;
										}
										if($this->getOwner()->arena2name == $this->getOwner()->selectarena)
										{
											$level = $this->getOwner()->arena2name;
											foreach($this->getOwner()->randomchestspawn2 as $chestspawn)
											{
												$coords = explode(",", $chestspawn);
														
												$chest = $this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(54), false, true);
														
												$nbt = new Compound("", [
														new Enum("Items", []),
														new String("id", Tile::CHEST),
														new Int("x", $coords[0]),
														new Int("y", $coords[1]),
														new Int("z", $coords[2])
												]);
												
												$nbt->Items->setTagType(NBT::TAG_Compound);
												$tile = Tile::createTile("Chest", $this->getOwner()->getServer()->getLevelByName("$level")->getChunk($coords[0] >> 4, $coords[2] >> 4), $nbt);
												if($chest instanceof TileChest and $tile instanceof TileChest)
												{
													$chest->pairWith($tile);
													$tile->pairWith($chest);
												}
												$truhe15 = $this->getOwner()->getServer()->getLevelbyName("$level")->getBlock(new Vector3($coords[0], $coords[1], $coords[2]));
												$chest15 = $this->getOwner()->getServer()->getLevelbyName("$level")->getTile($truhe15);
												if($chest15 instanceof Chest)
												{
													$rand = rand(1,10);
													$chest15->getInventory()->clearAll();
													$inv = $chest15->getRealInventory();
															
													$ids = $this->getOwner()->itemids;
													$itemanzahl = $this->getOwner()->numberofitemsperchest;
													$ids[array_rand($ids)];
													for($i3 = 0; $i3 < $itemanzahl; $i3++)
													{
														$inv->addItem(Item::get($ids[mt_rand(0, count($ids) - 1)]));
													}
												}
											}
											$this->getOwner()->chestgenerator = 1;
										}
										if($this->getOwner()->arena3name == $this->getOwner()->selectarena)
										{
											$level = $this->getOwner()->arena3name;
											foreach($this->getOwner()->randomchestspawn3 as $chestspawn)
											{
												$coords = explode(",", $chestspawn);
														
												$chest = $this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(54), false, true);
														
												$nbt = new Compound("", [
														new Enum("Items", []),
														new String("id", Tile::CHEST),
														new Int("x", $coords[0]),
														new Int("y", $coords[1]),
														new Int("z", $coords[2])
												]);
												
												$nbt->Items->setTagType(NBT::TAG_Compound);
												$tile = Tile::createTile("Chest", $this->getOwner()->getServer()->getLevelByName("$level")->getChunk($coords[0] >> 4, $coords[2] >> 4), $nbt);
												if($chest instanceof TileChest and $tile instanceof TileChest)
												{
													$chest->pairWith($tile);
													$tile->pairWith($chest);
												}
												$truhe15 = $this->getOwner()->getServer()->getLevelbyName("$level")->getBlock(new Vector3($coords[0], $coords[1], $coords[2]));
												$chest15 = $this->getOwner()->getServer()->getLevelbyName("$level")->getTile($truhe15);
												if($chest15 instanceof Chest)
												{
													$rand = rand(1,10);
													$chest15->getInventory()->clearAll();
													$inv = $chest15->getRealInventory();
															
													$ids = $this->getOwner()->itemids;
													$itemanzahl = $this->getOwner()->numberofitemsperchest;
													$ids[array_rand($ids)];
													for($i3 = 0; $i3 < $itemanzahl; $i3++)
													{
														$inv->addItem(Item::get($ids[mt_rand(0, count($ids) - 1)]));
													}
												}
											}
											$this->getOwner()->chestgenerator = 1;
										}
										if($this->getOwner()->arena4name == $this->getOwner()->selectarena)
										{
											$level = $this->getOwner()->arena4name;
											foreach($this->getOwner()->randomchestspawn4 as $chestspawn)
											{
												$coords = explode(",", $chestspawn);
														
												$chest = $this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(54), false, true);
													
												$nbt = new Compound("", [
														new Enum("Items", []),
														new String("id", Tile::CHEST),
														new Int("x", $coords[0]),
														new Int("y", $coords[1]),
														new Int("z", $coords[2])
												]);
												
												$nbt->Items->setTagType(NBT::TAG_Compound);
												$tile = Tile::createTile("Chest", $this->getOwner()->getServer()->getLevelByName("$level")->getChunk($coords[0] >> 4, $coords[2] >> 4), $nbt);
												if($chest instanceof TileChest and $tile instanceof TileChest)
												{
													$chest->pairWith($tile);
													$tile->pairWith($chest);
												}
												$truhe15 = $this->getOwner()->getServer()->getLevelbyName("$level")->getBlock(new Vector3($coords[0], $coords[1], $coords[2]));
												$chest15 = $this->getOwner()->getServer()->getLevelbyName("$level")->getTile($truhe15);
												if($chest15 instanceof Chest)
												{
													$rand = rand(1,10);
													$chest15->getInventory()->clearAll();
													$inv = $chest15->getRealInventory();
															
													$ids = $this->getOwner()->itemids;
													$itemanzahl = $this->getOwner()->numberofitemsperchest;
													$ids[array_rand($ids)];
													for($i3 = 0; $i3 < $itemanzahl; $i3++)
													{
														$inv->addItem(Item::get($ids[mt_rand(0, count($ids) - 1)]));
													}
												}
											}
											$this->getOwner()->chestgenerator = 1;
										}
										if($this->getOwner()->arena5name == $this->getOwner()->selectarena)
										{
											$level = $this->getOwner()->arena5name;
											foreach($this->getOwner()->randomchestspawn5 as $chestspawn)
											{
												$coords = explode(",", $chestspawn);
														
												$chest = $this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(54), false, true);
														
												$nbt = new Compound("", [
														new Enum("Items", []),
														new String("id", Tile::CHEST),
														new Int("x", $coords[0]),
														new Int("y", $coords[1]),
														new Int("z", $coords[2])
												]);
												
												$nbt->Items->setTagType(NBT::TAG_Compound);
												$tile = Tile::createTile("Chest", $this->getOwner()->getServer()->getLevelByName("$level")->getChunk($coords[0] >> 4, $coords[2] >> 4), $nbt);
												if($chest instanceof TileChest and $tile instanceof TileChest)
												{
													$chest->pairWith($tile);
													$tile->pairWith($chest);
												}
												$truhe15 = $this->getOwner()->getServer()->getLevelbyName("$level")->getBlock(new Vector3($coords[0], $coords[1], $coords[2]));
												$chest15 = $this->getOwner()->getServer()->getLevelbyName("$level")->getTile($truhe15);
												if($chest15 instanceof Chest)
												{
													$rand = rand(1,10);
													$chest15->getInventory()->clearAll();
													$inv = $chest15->getRealInventory();
															
													$ids = $this->getOwner()->itemids;
													$itemanzahl = $this->getOwner()->numberofitemsperchest;
													$ids[array_rand($ids)];
													for($i3 = 0; $i3 < $itemanzahl; $i3++)
													{
														$inv->addItem(Item::get($ids[mt_rand(0, count($ids) - 1)]));
													}
												}
											}
											$this->getOwner()->chestgenerator = 1;
										}	
									}
								}
							}
							if($time > $this->getOwner()->afterteleporttimer && $time < $this->getOwner()->afterteleporttimer + 5)
							{
								$player->sendPopUp(MT::GREEN.'Game start NOW!!!');	
							}
							if($time > $this->getOwner()->afterteleporttimer + 5 && $time < ($this->getOwner()->afterteleporttimer + $this->getOwner()->roundtime))
							{
								$time = time();
								$rundenzeit = ($this->getOwner()->afterteleporttimer + $this->getOwner()->roundtime) - $time;
								$letztespieler = count($this->getOwner()->players);
								$player->sendPopUp(MT::RED.$letztespieler.MT::GOLD.' players alive '.MT::RED.$rundenzeit.MT::GREEN.' left');
								
								if(isset($this->getOwner()->stats[$name]))
								{
									$kills = $this->getOwner()->stats[$name]['Kills'];
									$player->sendTip("\n\n\n\n".'                                  Kills '.$kills);
								}
								
							}
							if(count($this->getOwner()->players) <= 1 && $time > $this->getOwner()->afterteleporttimer)
							{
								foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
								{
									if(count($this->getOwner()->players) == 0)
									{
										$gewinner = "winner logt out...";
									}
									else
									{
										foreach($this->getOwner()->players as $p)
										{
											$gewinner = $p;
										}
									}
									$player->setHealth(20);
									$player->setGamemode(0);
									$player->teleport($this->getOwner()->getServer()->getDefaultLevel()->getSafeSpawn());
									$player->sendMessage(MT::RED.'Game Over '.MT::GREEN.'winner is '.MT::AQUA.$gewinner);	
								}
								$this->getOwner()->getLogger()->info("Gewinner $gewinner");
								if($this->getOwner()->config->get("RandomChestSpawn"))
 								{
									if(isset($this->getOwner()->kisten))
									{
										foreach($this->getOwner()->kisten as $kiste)
										{
											$level = $this->getOwner()->selectarena;
											$coords = explode(",", $kiste);
											$this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
										}
										
									}
 								}
 								else 
 								{
 								 	if($this->getOwner()->arena1name == $this->getOwner()->selectarena)
 									{
	 									foreach($this->getOwner()->randomchestspawn as $chestspawn)
							 			{
							 				$level = $this->getOwner()->arena1name;
							 				$coords = explode(",", $chestspawn);
							 				$this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
							 			}
 									}
 									if($this->getOwner()->arena2name == $this->getOwner()->selectarena)
 									{
	 									foreach($this->getOwner()->randomchestspawn2 as $chestspawn)
							 			{
							 				$level = $this->getOwner()->arena2name;
							 				$coords = explode(",", $chestspawn);
							 				$this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
							 			}
 									}
 									if($this->getOwner()->arena3name == $this->getOwner()->selectarena)
 									{
	 									foreach($this->getOwner()->randomchestspawn3 as $chestspawn)
							 			{
							 				$level = $this->getOwner()->arena3name;
							 				$coords = explode(",", $chestspawn);
							 				$this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
							 			}
 									}
 									if($this->getOwner()->arena4name == $this->getOwner()->selectarena)
 									{
	 									foreach($this->getOwner()->randomchestspawn4 as $chestspawn)
							 			{
							 				$level = $this->getOwner()->arena4name;
							 				$coords = explode(",", $chestspawn);
							 				$this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
							 			}
 									}
 									if($this->getOwner()->arena5name == $this->getOwner()->selectarena)
 									{
	 									foreach($this->getOwner()->randomchestspawn5 as $chestspawn)
							 			{
							 				$level = $this->getOwner()->arena5name;
							 				$coords = explode(",", $chestspawn);
							 				$this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
							 			}
 									}	
 								}
								$tileanzahl = 0;
								foreach($this->getOwner()->getServer()->getLevelbyName("$level")->getTiles() as $chest)
								{
									if($chest instanceof Chest)
									{
										$chest->close();
										$tileanzahl++;
									}
								}
								$this->getOwner()->getLogger()->info("Tiles closed ".$tileanzahl);
								
								$test = $this->getOwner()->getServer()->getLevelbyName($this->getOwner()->selectarena)->getEntities();
								foreach($test as $sender)
								{
									if(!($sender instanceof Player))
									{
										$sender->close();
									}
								}
								
								if($this->getOwner()->arena1name == $this->getOwner()->selectarena)
								{
									$pos1 = explode(",", $this->getOwner()->arena1areapos1);
									$pos2 = explode(",", $this->getOwner()->arena1areapos2);
								}
								if($this->getOwner()->arena2name == $this->getOwner()->selectarena)
								{
									$pos1 = explode(",", $this->getOwner()->arena2areapos1);
									$pos2 = explode(",", $this->getOwner()->arena2areapos2);
								}
								if($this->getOwner()->arena3name == $this->getOwner()->selectarena)
								{
									$pos1 = explode(",", $this->getOwner()->arena3areapos1);
									$pos2 = explode(",", $this->getOwner()->arena3areapos2);
								}
								if($this->getOwner()->arena4name == $this->getOwner()->selectarena)
								{
									$pos1 = explode(",", $this->getOwner()->arena4areapos1);
									$pos2 = explode(",", $this->getOwner()->arena4areapos2);
								}
								if($this->getOwner()->arena5name == $this->getOwner()->selectarena)
								{
									$pos1 = explode(",", $this->getOwner()->arena5areapos1);
									$pos2 = explode(",", $this->getOwner()->arena5areapos2);
								}
								$startX = $pos1[0];
								$endX = $pos2[0];
								$startY = $pos1[1];
								$endY = $pos2[1];
								$startZ = $pos1[2];
								$endZ = $pos2[2];
								$level = $this->getOwner()->selectarena;
								for($x = $startX; $x <= $endX; ++$x)
								{
									for($y = $startY; $y <= $endY; ++$y)
									{
										for($z = $startZ; $z <= $endZ; ++$z)
										{
											if($this->getOwner()->getServer()->getLevelByName("$level")->getBlockIdAt($x, $y, $z) == 51)
											{
												$this->getOwner()->getServer()->getLevelByName("$level")->setBlockIdAt($x, $y, $z, 0);
												$this->getOwner()->getLogger()->info("Fire removed");
											}
										}
									}
								}
								unset ($this->getOwner()->players);
								unset ($this->getOwner()->arena1);
								unset ($this->getOwner()->arena2);
								unset ($this->getOwner()->arena3);
								unset ($this->getOwner()->arena4);
								unset ($this->getOwner()->arena5);	
								unset ($this->getOwner()->aftervotetimer);
								unset ($this->getOwner()->afterteleporttimer);
								unset ($this->getOwner()->selectarena);
								unset ($this->getOwner()->chestgenerator);
								unset ($this->getOwner()->kisten);	
								return true;
							}
							else
							{
								$letztespieler = count($this->getOwner()->players);
								foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
								{	
									$time = time();
									$rundenzeit = ($this->getOwner()->afterteleporttimer + $this->getOwner()->roundtime) - $time;
									$level = $player->getLevel()->getName();
									if($this->getOwner()->lobbyname == $level)$player->sendPopUp(MT::GOLD.'   Please wait '.MT::RED.$letztespieler.MT::GOLD.' players alive'."\nuse ".MT::AQUA.'/watch'.MT::GREEN.' for spectator mode'."\n             ".MT::RED.$rundenzeit.MT::GREEN.' left');
								}
							}
							if($time > ($this->getOwner()->afterteleporttimer + $this->getOwner()->roundtime))
							{
								foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
								{
									foreach($this->getOwner()->players as $p)
									{
										$gewinner = $p;
									}
									$player->setHealth(20);
									$player->setGamemode(0);
									$player->teleport($this->getOwner()->getServer()->getDefaultLevel()->getSafeSpawn());
									$player->sendMessage(MT::RED.'Game Over '.MT::AQUA.'no winner');
								}
								if($this->config->get("RandomChestSpawn"))
 								{
									if(isset($this->getOwner()->kisten))
									{
										foreach($this->getOwner()->kisten as $kiste)
										{
											$level = $this->getOwner()->selectarena;
											$coords = explode(",", $kiste);
											$this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
										}
										
									}
 								}
 								else 
 								{
 									if($this->getOwner()->arena1name == $this->getOwner()->selectarena)
 									{
	 									foreach($this->getOwner()->randomchestspawn as $chestspawn)
							 			{
							 				$level = $this->getOwner()->arena1name;
							 				$coords = explode(",", $chestspawn);
							 				$this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
							 			}
 									}
 									if($this->getOwner()->arena2name == $this->getOwner()->selectarena)
 									{
	 									foreach($this->getOwner()->randomchestspawn2 as $chestspawn)
							 			{
							 				$level = $this->getOwner()->arena2name;
							 				$coords = explode(",", $chestspawn);
							 				$this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
							 			}
 									}
 									if($this->getOwner()->arena3name == $this->getOwner()->selectarena)
 									{
	 									foreach($this->getOwner()->randomchestspawn3 as $chestspawn)
							 			{
							 				$level = $this->getOwner()->arena3name;
							 				$coords = explode(",", $chestspawn);
							 				$this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
							 			}
 									}
 									if($this->getOwner()->arena4name == $this->getOwner()->selectarena)
 									{
	 									foreach($this->getOwner()->randomchestspawn4 as $chestspawn)
							 			{
							 				$level = $this->getOwner()->arena4name;
							 				$coords = explode(",", $chestspawn);
							 				$this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
							 			}
 									}
 									if($this->getOwner()->arena5name == $this->getOwner()->selectarena)
 									{
	 									foreach($this->getOwner()->randomchestspawn5 as $chestspawn)
							 			{
							 				$level = $this->getOwner()->arena5name;
							 				$coords = explode(",", $chestspawn);
							 				$this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
							 			}
 									}

 								}
								$tileanzahl = 0;
								foreach($this->getOwner()->getServer()->getLevelbyName("$level")->getTiles() as $chest)
								{
									if($chest instanceof Chest)
									{
										$chest->close();
										$tileanzahl++;
									}
								}
								$this->getOwner()->getLogger()->info("Tiles closed ".$tileanzahl);
								
								$test = $this->getOwner()->getServer()->getLevelbyName($this->getOwner()->selectarena)->getEntities();
								foreach($test as $sender)
								{
									if(!($sender instanceof Player))
									{
										$sender->close();
									}
								}
								
								if($this->getOwner()->arena1name == $this->getOwner()->selectarena)
								{
									$pos1 = explode(",", $this->getOwner()->arena1areapos1);
									$pos2 = explode(",", $this->getOwner()->arena1areapos2);
								}
								if($this->getOwner()->arena2name == $this->getOwner()->selectarena)
								{
									$pos1 = explode(",", $this->getOwner()->arena2areapos1);
									$pos2 = explode(",", $this->getOwner()->arena2areapos2);
								}
								if($this->getOwner()->arena3name == $this->getOwner()->selectarena)
								{
									$pos1 = explode(",", $this->getOwner()->arena3areapos1);
									$pos2 = explode(",", $this->getOwner()->arena3areapos2);
								}
								if($this->getOwner()->arena4name == $this->getOwner()->selectarena)
								{
									$pos1 = explode(",", $this->getOwner()->arena4areapos1);
									$pos2 = explode(",", $this->getOwner()->arena4areapos2);
								}
								if($this->getOwner()->arena5name == $this->getOwner()->selectarena)
								{
									$pos1 = explode(",", $this->getOwner()->arena5areapos1);
									$pos2 = explode(",", $this->getOwner()->arena5areapos2);
								}
								$startX = $pos1[0];
								$endX = $pos2[0];
								$startY = $pos1[1];
								$endY = $pos2[1];
								$startZ = $pos1[2];
								$endZ = $pos2[2];
								$level = $this->getOwner()->selectarena;
								for($x = $startX; $x <= $endX; ++$x)
								{
									for($y = $startY; $y <= $endY; ++$y)
									{
										for($z = $startZ; $z <= $endZ; ++$z)
										{
											if($this->getOwner()->getServer()->getLevelByName("$level")->getBlockIdAt($x, $y, $z) == 51)
											{
												$this->getOwner()->getServer()->getLevelByName("$level")->setBlockIdAt($x, $y, $z, 0);
												$this->getOwner()->getLogger()->info("Fire removed");
											}
										}
									}
								}
								unset ($this->getOwner()->players);
								unset ($this->getOwner()->arena1);
								unset ($this->getOwner()->arena2);
								unset ($this->getOwner()->arena3);
								unset ($this->getOwner()->arena4);
								unset ($this->getOwner()->arena5);
								unset ($this->getOwner()->aftervotetimer);
								unset ($this->getOwner()->afterteleporttimer);
								unset ($this->getOwner()->selectarena);
								unset ($this->getOwner()->chestgenerator);
								unset ($this->getOwner()->kisten);
								return true;
							}
						}
					}
				}
			}
		}	
}

class minigame extends PluginBase implements Listener{
	
	private $listener;
	
	public $time = array();
	public $schalter = array();
	public $round = array();
	
	public $lobbyname;
	public $lobbyareapos1;
	public $lobbyareapos2;

	public $arena1name;
	public $arena1areapos1;
	public $arena1areapos2;
	public $arena2name;
	public $arena2areapos1;
	public $arena2areapos2;
	public $arena3name;
	public $arena3areapos1;
	public $arena3areapos2;
	public $arena4name;
	public $arena4areapos1;
	public $arena4areapos2;
	public $arena5name;
	public $arena5areapos1;
	public $arena5areapos2;
	public $numberofchests;
	public $roundtime;
	public $stats = array ();
	
	public $numberofitemsperchest;
	public $itemids = array();
	
	public $randomchestspawn = array ();
	public $randomplayerspawn = array ();
	public $randomchestspawn2 = array ();
	public $randomplayerspawn2 = array ();
	public $randomchestspawn3 = array ();
	public $randomplayerspawn3 = array ();
	public $randomchestspawn4 = array ();
	public $randomplayerspawn4 = array ();
	public $randomchestspawn5 = array ();
	public $randomplayerspawn5 = array ();
	
	public function onEnable()
	{
		$this->getLogger()->info('SurvivalHive Hungergames loaded!');
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new statuscheck($this), 20);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new sgworldborder($this), 40);
		
		if (!file_exists($this->getDataFolder()))
		{
			@mkdir($this->getDataFolder(), true);
		}
			$this->getServer()->getPluginManager()->registerEvents($this, $this);
			$this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML, array("Lobby" => 'lobbyworld',"LobbyPos1" => "50,0,50","LobbyPos2" => "50,0,50","Arena1" => 'world',"Arena1Pos1" => "50,0,50","Arena1Pos2" => "50,0,50", "Arena2" => 'world2',"Arena2Pos1" => "50,0,50","Arena2Pos2" => "50,0,50","Arena3" => 'world3',"Arena3Pos1" => "50,0,50","Arena3Pos2" => "50,0,50","Arena4" => 'world4',"Arena4Pos1" => "50,0,50","Arena4Pos2" => "50,0,50","Arena5" => 'world5',"Arena5Pos1" => "50,0,50","Arena5Pos2" => "50,0,50","NumberofChests" => "30","NumberofItemsperChest" => "6","PossibleItemIds" => [259, 260, 261, 262, 264, 265, 268, 271, 272, 275, 280, 282, 298, 299, 300, 301, 302, 303 ,304, 305, 306, 308, 309, 314, 315, 316, 317, 319, 320, 354, 357, 363, 364, 365, 366],"Roundtime" => "10", "RandomChestSpawn" => true, "RandomPlayerSpawn" => true, "ChestspawnPos" => [], "PlayerspawnPos" => [], "ChestspawnPos2" => [], "PlayerspawnPos2" => [], "ChestspawnPos3" => [], "PlayerspawnPos3" => [], "ChestspawnPos4" => [], "PlayerspawnPos4" => [], "ChestspawnPos5" => [], "PlayerspawnPos5" => []));
	
		$this->lobbyname = 	$this->config->get("Lobby");
		$this->lobbyareapos1 = $this->config->get("LobbyPos1");
		$this->lobbyareapos2 = $this->config->get("LobbyPos2");
		
		$this->arena1name = 	$this->config->get("Arena1");
		$this->arena1areapos1 = $this->config->get("Arena1Pos1");
		$this->arena1areapos2 = $this->config->get("Arena1Pos2");
		
		$this->arena2name = 	$this->config->get("Arena2");
		$this->arena2areapos1 = $this->config->get("Arena2Pos1");
		$this->arena2areapos2 = $this->config->get("Arena2Pos2");
		
		$this->arena3name = 	$this->config->get("Arena3");
		$this->arena3areapos1 = $this->config->get("Arena3Pos1");
		$this->arena3areapos2 = $this->config->get("Arena3Pos2");
		
		$this->arena4name = 	$this->config->get("Arena4");
		$this->arena4areapos1 = $this->config->get("Arena4Pos1");
		$this->arena4areapos2 = $this->config->get("Arena4Pos2");
		
		$this->arena5name = 	$this->config->get("Arena5");
		$this->arena5areapos1 = $this->config->get("Arena5Pos1");
		$this->arena5areapos2 = $this->config->get("Arena5Pos2");
		
		$this->numberofchests = $this->config->get("NumberofChests");
		
		$this->roundtime = (($this->config->get("Roundtime")*20)*3);
		
		$this->numberofitemsperchest = $this->config->get("NumberofItemsperChest");
		$this->itemids = $this->config->get("PossibleItemIds");
		
		$this->randomchestspawn = $this->config->get("ChestspawnPos");
		$this->randomplayerspawn = $this->config->get("PlayerspawnPos");
		
		$this->randomchestspawn2 = $this->config->get("ChestspawnPos2");
		$this->randomplayerspawn2 = $this->config->get("PlayerspawnPos2");
		
		$this->randomchestspawn3 = $this->config->get("ChestspawnPos3");
		$this->randomplayerspawn3 = $this->config->get("PlayerspawnPos3");
		
		$this->randomchestspawn4 = $this->config->get("ChestspawnPos4");
		$this->randomplayerspawn4 = $this->config->get("PlayerspawnPos4");
		
		$this->randomchestspawn5 = $this->config->get("ChestspawnPos5");
		$this->randomplayerspawn5 = $this->config->get("PlayerspawnPos5");
	}
	public function onJoin(PlayerJoinEvent $event)
	{
		$name = $event->getPlayer()->getName();
		$event->setJoinMessage('');
		$event->getPlayer()->sendMessage(MT::GREEN.'Welcome '.MT::RED.$name.MT::GREEN.' to Hungergameslobby!');
		$event->getPlayer()->sendMessage(MT::AQUA.'Vote for your arena with /vote *number 1-5*');
		$event->getPlayer()->sendMessage(MT::AQUA.'After first vote and 2 players starts timer');
		$event->getPlayer()->sendMessage(MT::AQUA.'All players in lobby will be teleportet in the arena');
		$event->getPlayer()->getInventory()->clearAll();
		$event->getPlayer()->setGamemode(0);
		$event->getPlayer()->teleport($this->getServer()->getLevelbyName($this->lobbyname)->getSafeSpawn());
		$this->stats[$name]['Kills'] = 0;
	}
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args)
	{
		$name = $sender->getName();
		
		switch($cmd->getName())
		{
			case "vote":
				if(!(isset($args[0])))
				{
					$sender->sendMessage(MT::RED.'Vote with /vote *arenanumber* (1-5)');
					$this->getLogger()->info("$name benutzt vote befehl");
					return true;
				}
				else
				{
					if($args[0] == 1 || $args[0] == 2 || $args[0] == 3 || $args[0] == 4 || $args[0] == 5)
					{
						$sender->sendMessage(MT::RED.'Thank u for voting the arena '.MT::GREEN.$args[0]);
						$this->getLogger()->info("$name hat gevotet Arena $args[0]");
						if($args[0] == 1)$this->arena1[$name] = 1;		
						if($args[0] == 2)$this->arena2[$name] = 1;
						if($args[0] == 3)$this->arena3[$name] = 1;
						if($args[0] == 4)$this->arena4[$name] = 1;
						if($args[0] == 5)$this->arena5[$name] = 1;
						return true;
					}
					else 
					{
						$sender->sendMessage(MT::RED.'Wrong arena number or arena not loaded');
						return false;
					}
				}
				break;
			case "watch":
				$level = $sender->getLevel()->getName();
				if($level == $this->lobbyname)
				{
					if(isset($this->selectarena))
					{
						$arenaname = $this->selectarena;
						
						if($this->arena1name == $arenaname)
						{
							$pos111 = explode(",", $this->arena1areapos1);
							$pos222 = explode(",", $this->arena1areapos2);
						}
						if($this->arena2name == $arenaname)
						{
							$pos111 = explode(",", $this->arena2areapos1);
							$pos222 = explode(",", $this->arena2areapos2);
						}
						if($this->arena3name == $arenaname)
						{
							$pos111 = explode(",", $this->arena3areapos1);
							$pos222 = explode(",", $this->arena3areapos2);
						}
						if($this->arena4name == $arenaname)
						{
							$pos111 = explode(",", $this->arena4areapos1);
							$pos222 = explode(",", $this->arena4areapos2);
						}
						if($this->arena5name == $arenaname)
						{
							$pos111 = explode(",", $this->arena5areapos1);
							$pos222 = explode(",", $this->arena5areapos2);
						}
						$randx = rand($pos111[0],$pos222[0]);
						$randz = rand($pos111[2],$pos222[2]);
						$randy = rand($pos111[1],$pos222[1]);
						
						$sender->teleport($this->getServer()->getLevelByName($this->selectarena)->getSafeSpawn(new Position($randx,$randy,$randz)));
						$sender->setGamemode(3);
						return true;
					}
					else
					{
						$sender->sendMessage(MT::RED.'Round isnt started yet!');
						return false;
					}
				}
				else
				{
					$name = $sender->getName();
					
					if(in_array($name, $this->players))
					{
						$sender->sendMessage(MT::RED.'Your are not in Lobby and playing!');
					}
					else 
					{
						$sender->teleport($this->getServer()->getLevelByName($this->lobbyname)->getSafeSpawn());
						$sender->setGamemode(0);
					}
					return false;
				}
				break;
				case "playerspawn":
					$level = $sender->getLevel()->getName();
					if($sender->isOp())
					{
						if(isset($args[0]))
						{
							$config = $this->config->getAll();
							$ppos = $config["PlayerspawnPos"];
							$ppos2 = $config["PlayerspawnPos2"];
							$ppos3 = $config["PlayerspawnPos3"];
							$ppos4 = $config["PlayerspawnPos4"];
							$ppos5 = $config["PlayerspawnPos5"];
							
							if($args[0] == "add")
							{
								if(isset($args[1]))
								{
									if($this->arena1name == $level)
									{
										$commandpos = $args[1];
										if(!in_array($commandpos, $ppos))
										{
											if(!is_array($ppos))
											{
												$ppos = array($commandpos);
												return true;
											}
											else
											{
											
												$ppos[] = $commandpos;
												$sender->sendMessage(MT::GREEN."Position setted ".$commandpos);
												$config["PlayerspawnPos"] = $ppos;
												$this->config->setAll($config);
												$this->config->save();
												return true;
											}
										}
										else
										{
											$sender->sendMessage(MT::RED."Position exist");
											return false;
										}
									}
									if($this->arena2name == $level)
									{
										$commandpos = $args[1];
										if(!in_array($commandpos, $ppos2))
										{
											if(!is_array($ppos2))
											{
												$ppos2 = array($commandpos);
												return true;
											}
											else
											{
											
												$ppos2[] = $commandpos;
												$sender->sendMessage(MT::GREEN."Position setted ".$commandpos);
												$config["PlayerspawnPos2"] = $ppos2;
												$this->config->setAll($config);
												$this->config->save();
												return true;
											}
										}
										else
										{
											$sender->sendMessage(MT::RED."Position exist");
											return false;
										}
									}
									if($this->arena3name == $level)
									{
										$commandpos = $args[1];
										if(!in_array($commandpos, $ppos3))
										{
											if(!is_array($ppos3))
											{
												$ppos3 = array($commandpos);
												return true;
											}
											else
											{
											
												$ppos3[] = $commandpos;
												$sender->sendMessage(MT::GREEN."Position setted ".$commandpos);
												$config["PlayerspawnPos3"] = $ppos3;
												$this->config->setAll($config);
												$this->config->save();
												return true;
											}
										}
										else
										{
											$sender->sendMessage(MT::RED."Position exist");
											return false;
										}
									}
									if($this->arena4name == $level)
									{
										$commandpos = $args[1];
										if(!in_array($commandpos, $ppos4))
										{
											if(!is_array($ppos4))
											{
												$ppos4 = array($commandpos);
												return true;
											}
											else
											{
											
												$ppos4[] = $commandpos;
												$sender->sendMessage(MT::GREEN."Position setted ".$commandpos);
												$config["PlayerspawnPos4"] = $ppos4;
												$this->config->setAll($config);
												$this->config->save();
												return true;
											}
										}
										else
										{
											$sender->sendMessage(MT::RED."Position exist");
											return false;
										}
									}
									if($this->arena5name == $level)
									{
										$commandpos = $args[1];
										if(!in_array($commandpos, $ppos5))
										{
											if(!is_array($ppos5))
											{
												$ppos5 = array($commandpos);
												return true;
											}
											else
											{
											
												$ppos5[] = $commandpos;
												$sender->sendMessage(MT::GREEN."Position setted ".$commandpos);
												$config["PlayerspawnPos5"] = $ppos5;
												$this->config->setAll($config);
												$this->config->save();
												return true;
											}
										}
										else
										{
											$sender->sendMessage(MT::RED."Position exist");
											return false;
										}
									}
								}
								else
								{
									$sender->sendMessage(MT::RED."Please select a position \nexample : /playerspawn add 10,5,25");
									return false;
								}
							}
							if($args[0] == "dell")
							{
								if(isset($args[1]))
								{
									$commandpos = $args[1];
									
									if($this->arena1name == $level)
									{
										if(in_array($commandpos, $ppos))
										{
											$key = array_search($commandpos, $ppos);
											unset($ppos[$key]);
											$sender->sendMessage(MT::GREEN."Position deleted ".$commandpos);
											$config["PlayerspawnPos"] = $ppos;
											$this->config->setAll($config);
											$this->config->save();
											return true;
										}
										else
										{
											$sender->sendMessage(MT::RED."Position dont exist");
											return false;
										}
									}
									if($this->arena2name == $level)
									{
										if(in_array($commandpos, $ppos2))
										{
											$key = array_search($commandpos, $ppos2);
											unset($ppos2[$key]);
											$sender->sendMessage(MT::GREEN."Position deleted ".$commandpos);
											$config["PlayerspawnPos2"] = $ppos2;
											$this->config->setAll($config);
											$this->config->save();
											return true;
										}
										else
										{
											$sender->sendMessage(MT::RED."Position dont exist");
											return false;
										}
									}
									if($this->arena3name == $level)
									{
										if(in_array($commandpos, $ppos3))
										{
											$key = array_search($commandpos, $ppos3);
											unset($ppos3[$key]);
											$sender->sendMessage(MT::GREEN."Position deleted ".$commandpos);
											$config["PlayerspawnPos3"] = $ppos3;
											$this->config->setAll($config);
											$this->config->save();
											return true;
										}
										else
										{
											$sender->sendMessage(MT::RED."Position dont exist");
											return false;
										}
									}
									if($this->arena4name == $level)
									{
										if(in_array($commandpos, $ppos4))
										{
											$key = array_search($commandpos, $ppos4);
											unset($ppos4[$key]);
											$sender->sendMessage(MT::GREEN."Position deleted ".$commandpos);
											$config["PlayerspawnPos4"] = $ppos4;
											$this->config->setAll($config);
											$this->config->save();
											return true;
										}
										else
										{
											$sender->sendMessage(MT::RED."Position dont exist");
											return false;
										}
									}
									if($this->arena5name == $level)
									{
										if(in_array($commandpos, $ppos5))
										{
											$key = array_search($commandpos, $ppos5);
											unset($ppos5[$key]);
											$sender->sendMessage(MT::GREEN."Position deleted ".$commandpos);
											$config["PlayerspawnPos5"] = $ppos5;
											$this->config->setAll($config);
											$this->config->save();
											return true;
										}
										else
										{
											$sender->sendMessage(MT::RED."Position dont exist");
											return false;
										}
									}
								}
								else
								{
									$sender->sendMessage(MT::RED."Please select a position \nexample : /playerspawn dell 10,5,25");
									return false;
								}
							}
							if($args[0] == "list")
							{
								$sender->sendMessage(MT::GREEN."Playerspawns: ".implode(", ", $ppos));
								return true;
							}
						}
						else
						{
							$sender->sendMessage(MT::RED.'add / dell / list');
							return true;
						}
					}
					else
					{
						$sender->sendMessage(MT::RED.'Only for Operators');
						return false;
					}
					break;
				case "chestspawn":
					$level = $sender->getLevel()->getName();
					if($sender->isOp())
					{
						if(isset($args[0]))
						{
							$config = $this->config->getAll();
							$ppos = $config["ChestspawnPos"];
							$ppos2 = $config["ChestspawnPos2"];
							$ppos3 = $config["ChestspawnPos3"];
							$ppos4 = $config["ChestspawnPos4"];
							$ppos5 = $config["ChestspawnPos5"];
							if($args[0] == "add")
							{
								if(isset($args[1]))
								{
									$commandpos = $args[1];
									if($this->arena1name == $level)
									{
										if(!in_array($commandpos, $ppos))
										{
											if(!is_array($ppos))
											{
												$ppos = array($commandpos);
												return true;
											}
											else
											{
												$ppos[] = $commandpos;
												$sender->sendMessage(MT::GREEN."Position setted ".$commandpos);
												$config["ChestspawnPos"] = $ppos;
												$this->config->setAll($config);
												$this->config->save();
												return true;
											}
										}
										else
										{
											$sender->sendMessage(MT::RED."Position exist");
											return false;
										}
									}
									if($this->arena2name == $level)
									{
										if(!in_array($commandpos, $ppos2))
										{
											if(!is_array($ppos2))
											{
												$ppos2 = array($commandpos);
												return true;
											}
											else
											{
												$ppos2[] = $commandpos;
												$sender->sendMessage(MT::GREEN."Position setted ".$commandpos);
												$config["ChestspawnPos2"] = $ppos2;
												$this->config->setAll($config);
												$this->config->save();
												return true;
											}
										}
										else
										{
											$player->sendMessage(MT::RED."Position exist");
											return false;
										}
									}
									if($this->arena3name == $level)
									{
										if(!in_array($commandpos, $ppos3))
										{
											if(!is_array($ppos3))
											{
												$ppos3 = array($commandpos);
												return true;
											}
											else
											{
												$ppos3[] = $commandpos;
												$sender->sendMessage(MT::GREEN."Position setted ".$commandpos);
												$config["ChestspawnPos3"] = $ppos3;
												$this->config->setAll($config);
												$this->config->save();
												return true;
											}
										}
										else
										{
											$sender->sendMessage(MT::RED."Position exist");
											return false;
										}
									}
									if($this->arena4name == $level)
									{
										if(!in_array($commandpos, $ppos4))
										{
											if(!is_array($ppos4))
											{
												$ppos4 = array($commandpos);
												return true;
											}
											else
											{
												$ppos4[] = $commandpos;
												$sender->sendMessage(MT::GREEN."Position setted ".$commandpos);
												$config["ChestspawnPos4"] = $ppos4;
												$this->config->setAll($config);
												$this->config->save();
												return true;
											}
										}
										else
										{
											$sender->sendMessage(MT::RED."Position exist");
											return false;
										}
									}
									if($this->arena5name == $level)
									{
										if(!in_array($commandpos, $ppos5))
										{
											if(!is_array($ppos5))
											{
												$ppos = array($commandpos);
												return true;
											}
											else
											{
												$ppos5[] = $commandpos;
												$sender->sendMessage(MT::GREEN."Position setted ".$commandpos);
												$config["ChestspawnPos5"] = $ppos5;
												$this->config->setAll($config);
												$this->config->save();
												return true;
											}
										}
										else
										{
											$sender->sendMessage(MT::RED."Position exist");
											return false;
										}
									}	
								}
								else
								{
									$sender->sendMessage(MT::RED."Please select a position \nexample : /playerspawn add 10,5,25");
									return false;
								}
							}
							if($args[0] == "dell")
							{
								if(isset($args[1]))
								{
									$commandpos = $args[1];
									if($this->arena1name == $level)
									{
										if(in_array($commandpos, $ppos))
										{
											$key = array_search($commandpos, $ppos);
											unset($ppos[$key]);
											$sender->sendMessage(MT::GREEN."Position deleted ".$commandpos);
											$config["ChestspawnPos"] = $ppos;
											$this->config->setAll($config);
											$this->config->save();
											return true;
										}
										else
										{
											$sender->sendMessage(MT::RED."Position dont exist");
											return false;
										}
									}
									if($this->arena2name == $level)
									{
										if(in_array($commandpos, $ppos2))
										{
											$key = array_search($commandpos, $ppos2);
											unset($ppos2[$key]);
											$sender->sendMessage(MT::GREEN."Position deleted ".$commandpos);
											$config["ChestspawnPos2"] = $ppos2;
											$this->config->setAll($config);
											$this->config->save();
											return true;
										}
										else
										{
											$sender->sendMessage(MT::RED."Position dont exist");
											return false;
										}
									}
									if($this->arena3name == $level)
									{
										if(in_array($commandpos, $ppos3))
										{
											$key = array_search($commandpos, $ppos3);
											unset($ppos3[$key]);
											$sender->sendMessage(MT::GREEN."Position deleted ".$commandpos);
											$config["ChestspawnPos"] = $ppos3;
											$this->config->setAll($config);
											$this->config->save();
											return true;
										}
										else
										{
											$sender->sendMessage(MT::RED."Position dont exist");
											return false;
										}
									}
									if($this->arena4name == $level)
									{
										if(in_array($commandpos, $ppos4))
										{
											$key = array_search($commandpos, $ppos4);
											unset($ppos4[$key]);
											$sender->sendMessage(MT::GREEN."Position deleted ".$commandpos);
											$config["ChestspawnPos4"] = $ppos4;
											$this->config->setAll($config);
											$this->config->save();
											return true;
										}
										else
										{
											$sender->sendMessage(MT::RED."Position dont exist");
											return false;
										}
									}
									if($this->arena5name == $level)
									{
										if(in_array($commandpos, $ppos5))
										{
											$key = array_search($commandpos, $ppos5);
											unset($ppos5[$key]);
											$sender->sendMessage(MT::GREEN."Position deleted ".$commandpos);
											$config["ChestspawnPos5"] = $ppos5;
											$this->config->setAll($config);
											$this->config->save();
											return true;
										}
										else
										{
											$sender->sendMessage(MT::RED."Position dont exist");
											return false;
										}
									}
									
								}
								else
								{
									$sender->sendMessage(MT::RED."Please select a position \nexample : /playerspawn dell 10,5,25");
									return false;
								}
							}
							if($args[0] == "list")
							{
								$sender->sendMessage(MT::GREEN."Playerspawns: ".implode(", ", $ppos));
								return true;
							}
						}
						else
						{
							$sender->sendMessage(MT::RED.'add / dell / list');
							return true;
						}
					}
					else
					{
						$sender->sendMessage(MT::RED.'Only for Operators');
						return false;
					}
					break;
		}
	}
	public function onPlayerMoveEvent(PlayerMoveEvent $event)
	{
		$time = time();
		$name = $event->getPlayer()->getName();
		$level = $event->getPlayer()->getLevel()->getName();
		
		if($level != $this->lobbyname)
		{
			if(isset($this->afterteleporttimer))
			{
				if($time < $this->afterteleporttimer)
				{
					$event->setCancelled(true);
				}
			}
		}
	}
	public function onBlockPlaceEvent(BlockPlaceEvent $event)
	{
		if(!$event->getPlayer()->isOp())
		{
			$event->setCancelled(true);
		}
	
	}
	public function onBlockBreak(BlockBreakEvent $event)
	{
		if(!$event->getPlayer()->isOp())
		{
			$event->setCancelled(true);
		}
	}
	public function onEntityDamageByEntity(EntityDamageEvent $event)
	{
		if($event instanceof EntityDamageByEntityEvent)
		{
			$victim = $event->getEntity();
			$attacker = $event->getDamager();
			if($victim instanceof Player && $attacker instanceof Player)
			{			
				if($this->lobbyname == $victim->getLevel()->getName())
				{
					$event->setCancelled(true);
				}
			}
		}
	}
	public function onRespawn(PlayerRespawnEvent $event)
	{
		$event->getPlayer()->getInventory()->clearAll();
		$pos = $this->getServer()->getDefaultLevel()->getSafeSpawn();
		$event->setRespawnPosition($pos);
	}
	public function onPlayerDeathEvent(PlayerDeathEvent $event)
	{
		$player = $event->getEntity();
		if(!($player instanceof Player))return;
		
		$name = $event->getEntity()->getName();
		unset ($this->players[$name]);
		$player->setGamemode(0);
		
		if(isset($this->players))
		{
			$letztespieler = count($this->players);
			
			foreach($this->getServer()->getOnlinePlayers() as $player)
			{
				$player->sendMessage(MT::GREEN.$name.MT::RED.' died '.MT::AQUA.'-> '.MT::RED.$letztespieler.MT::GREEN.' players alive');
			}
			foreach($this->players as $p)
			{
				$this->getLogger()->info("$p");
			}
		}
		$cause = $event->getEntity()->getLastDamageCause();
		if($cause instanceof EntityDamageByEntityEvent)
		{
			$damager = $cause->getDamager();
			if($damager instanceof Player)
			{
				$name = $damager->getName();
				$this->stats[$name]['Kills'] = ($this->stats[$name]['Kills']+1);
				$kills = $this->stats[$name]['Kills'];
				$this->getLogger()->info("$kills");
			}
		}	
	}
	public function onPlayerQuitEvent(PlayerQuitEvent $event)
	{
		$name = $event->getPlayer()->getName();
		unset ($this->players[$name]);
		$event->setQuitMessage('');
	}
	public function onPlayerInteract(PlayerInteractEvent $event)
	{
		$name = $event->getPlayer()->getName();
		$welt = $event->getBlock()->getLevel()->getName();

		if($event->getItem()->getId() == 259)
		{
			if($welt == $this->lobbyname)
			{
				$event->setCancelled(true);
			}
		}	
	}
 	public function onDisable()
 	{
 		if($this->config->get("RandomChestSpawn"))
 		{
	 		if(isset($this->kisten))
	 		{
	 			$level = $this->selectarena;
	 			foreach($this->kisten as $kiste)
	 			{
	 				$coords = explode(",", $kiste);
	 				$this->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
	 			}
	 		}
 		}
 		else
 		{	
 			
 			if($this->arena1name == $this->selectarena)
 			{
 				foreach($this->randomchestspawn as $chestspawn)
 				{
 					$level = $this->arena1name;
 					$coords = explode(",", $chestspawn);
 					$this->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
 				}
 			}
 			if($this->arena2name == $this->selectarena)
 			{
 				foreach($this->randomchestspawn2 as $chestspawn)
 				{
 					$level = $this->arena2name;
 					$coords = explode(",", $chestspawn);
 					$this->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
 				}
 			}
 			if($this->arena3name == $this->selectarena)
 			{
 				foreach($this->randomchestspawn3 as $chestspawn)
 				{
 					$level = $this->arena3name;
 					$coords = explode(",", $chestspawn);
 					$this->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
 				}
 			}
 			if($this->arena4name == $this->selectarena)
 			{
 				foreach($this->randomchestspawn4 as $chestspawn)
 				{
 					$level = $this->arena4name;
 					$coords = explode(",", $chestspawn);
 					$this->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
 				}
 			}
 			if($this->arena5name == $this->selectarena)
 			{
 				foreach($this->randomchestspawn5 as $chestspawn)
 				{
 					$level = $this->arena5name;
 					$coords = explode(",", $chestspawn);
 					$this->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
 				}
 			}

 		}
 		
 		$tileanzahl = 0;
 		foreach($this->getServer()->getLevelbyName("$level")->getTiles() as $chest)
 		{
 			if($chest instanceof Chest)
 			{
 				$chest->close();
 				$tileanzahl++;
 			}
 		}
 		$this->getLogger()->info("Tiles closed ".$tileanzahl);
 		
 		$dropeditems = $this->getServer()->getLevelbyName($this->selectarena)->getEntities();
 		foreach($dropeditems as $dropeditems1)
 		{
 			if(!($dropeditems1 instanceof Player))
 			{
 				$dropeditems1->close();
 			}
 		}
 		
 		if($this->arena1name == $this->selectarena)
 		{
 			$pos1 = explode(",", $this->arena1areapos1);
 			$pos2 = explode(",", $this->arena1areapos2);
 		}
 		if($this->arena2name == $this->selectarena)
 		{
 			$pos1 = explode(",", $this->arena2areapos1);
 			$pos2 = explode(",", $this->arena2areapos2);
 		}
 		if($this->arena3name == $this->selectarena)
 		{
 			$pos1 = explode(",", $this->arena3areapos1);
 			$pos2 = explode(",", $this->arena3areapos2);
 		}
 		if($this->arena4name == $this->selectarena)
 		{
 			$pos1 = explode(",", $this->arena4areapos1);
 			$pos2 = explode(",", $this->arena4areapos2);
 		}
 		if($this->arena5name == $this->selectarena)
 		{
 			$pos1 = explode(",", $this->arena5areapos1);
 			$pos2 = explode(",", $this->arena5areapos2);
 		}
 		$startX = $pos1[0];
 		$endX = $pos2[0];
 		$startY = $pos1[1];
 		$endY = $pos2[1];
 		$startZ = $pos1[2];
 		$endZ = $pos2[2];
 		$level = $this->selectarena;
 		for($x = $startX; $x <= $endX; ++$x)
 		{
 			for($y = $startY; $y <= $endY; ++$y)
 			{
 				for($z = $startZ; $z <= $endZ; ++$z)
 				{
 					if($this->getServer()->getLevelByName("$level")->getBlockIdAt($x, $y, $z) == 51)
 					{
 						$this->getServer()->getLevelByName("$level")->setBlockIdAt($x, $y, $z, 0);
 						$this->getLogger()->info("Fire removed");
 					}
 				}
 			}
 		}
		$this->getLogger()->info('SurvivalHive Hungergames unloaded!');
	}
}
