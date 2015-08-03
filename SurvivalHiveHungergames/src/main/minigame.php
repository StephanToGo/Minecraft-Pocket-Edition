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
			
			//if(isset($this->getOwner()->players))
			//{
			//	foreach($this->getOwner()->players as $p)
			//	{
			//		$this->getOwner()->getLogger()->info("$p");
			//	}
			//}
			
			//if(isset($this->getOwner()->kisten))
			//{
			//	foreach($this->getOwner()->kisten as $p)
			//	{
			//		$this->getOwner()->getLogger()->info("$p");
			//	}
			//}
			
			foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
			{
				$name = $player->getName();
				
				if($spieleranzahl < 2)
				{
					$player->sendPopUp(MT::BLUE.'Wait for other players');
				}
				else
				{
					if((!(isset($this->getOwner()->arena1))) && (!(isset($this->getOwner()->arena2))) && (!(isset($this->getOwner()->arena3))) && (!(isset($this->getOwner()->arena4))) && (!(isset($this->getOwner()->arena5))))
					{
						$player->sendPopUp(MT::BLUE.'Vote for arena with /vote *arenanumber*');
					}
					else
					{
						if(!(isset($this->getOwner()->afterteleporttimer)))
						{
							if(!(isset($this->getOwner()->aftervotetimer)))
							{
								$this->getOwner()->aftervotetimer = $time+60;
					
								$player->sendPopUp(MT::RED.'Teleport timer started');
							}
							else
							{
								if($time < $this->getOwner()->aftervotetimer)
								{
									$seconds = $this->getOwner()->aftervotetimer - $time;
									$player->sendPopUp(MT::BLUE.'Wait for other arena votes '.MT::RED.$seconds.'sec');
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
										$player->sendPopUp(MT::BLUE.'no winner by arena voting -> random map loaded');
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
										
									foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
									{
										$x = rand(100,200);
										$y = rand(100,200);
										$z = rand(100,200);
										
										$player->sendMessage('Arena '.$arena.' win');
										$player->sendPopUp(MT::BLUE.'Teleport event start now');
										$player->teleport($this->getOwner()->getServer()->getLevelByName($arenaname)->getSafeSpawn(new Position($x,$y,$z)));
										
										$player->getInventory()->clearAll();
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
								$player->sendPopUp(MT::BLUE.'Wait, game starts in '.MT::RED.$seconds.'sec');
					
								$this->getOwner()->players[$name] = $name;
								
								//Chest Generator---
								//Chest Generator---
								//Chest Generator---
								//Chest Generator---
								//Chest Generator---
								//Chest Generator---
								if(!(isset($this->getOwner()->chestgenerator)))
								{
									$level = $this->getOwner()->selectarena;
									$this->getOwner()->getLogger()->info('STARTET');
									
									for($i = 1; $i < 31; $i++)
									{
										$randx = rand(1,300);
										$randz = rand(1,300);
										
										for ($i2 = 1; $i2 <= 100; $i2++) 
										{
											$yachse = (3 + $i2);
											
											//$blocktest = $this->getOwner()->getServer()->getLevelbyName("$level")->getBlock(new Vector3(20,5,20))->getId();
												
											$block = $this->getOwner()->getServer()->getLevelbyName("$level")->getBlockIdAt($randx,$yachse,$randz);
											
											$this->getOwner()->getLogger()->info($randx." ".$randz." ".$yachse." ".$block." ".$level." ");
											
											if($block == 2)
											{
												$block0 = $this->getOwner()->getServer()->getLevelByName("$level")->getBlock(new Vector3($randx,($yachse+1),$randz));
												
												if($block0->getID() == 0)
												{
	
													$block1 = $this->getOwner()->getServer()->getLevelByName("$level")->getBlock(new Vector3($randx+1,$yachse+1,$randz));
													$block2 = $this->getOwner()->getServer()->getLevelByName("$level")->getBlock(new Vector3($randx-1,$yachse+1,$randz));
													$block3 = $this->getOwner()->getServer()->getLevelByName("$level")->getBlock(new Vector3($randx,$yachse+1,$randz+1));
													$block4 = $this->getOwner()->getServer()->getLevelByName("$level")->getBlock(new Vector3($randx,$yachse+1,$randz-1));
													
													if(($block1->getID() == 0) && ($block2->getID() == 0) && ($block3->getID() == 0) && ($block4->getID() == 0))
													{
														
														$chest = $this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($randx,$yachse+1,$randz), BLOCK::get(54), false, true);
														$nbt = new Compound("", [
																new Enum("Items", []),
																new String("id", Tile::CHEST),
																new Int("x", $randx),
																new Int("y", $yachse+1),
																new Int("z", $randz)
														]);
														$nbt->Items->setTagType(NBT::TAG_Compound);
														$tile = Tile::createTile("Chest", $this->getOwner()->getServer()->getLevelByName("$level")->getChunk($randx >> 4, $randz >> 4), $nbt);
														
														if($chest instanceof TileChest and $tile instanceof TileChest)
														{
															$chest->pairWith($tile);
															$tile->pairWith($chest);
														}	
														
														$kistencoords = ($randx.",".($yachse+1).",".$randz);
														$this->getOwner()->kisten[] = $kistencoords;
															
														//Kisten füllen
														$truhe15 = $this->getOwner()->getServer()->getLevelbyName("$level")->getBlock(new Vector3($randx, $yachse+1, $randz));
														$chest15 = $this->getOwner()->getServer()->getLevelbyName("$level")->getTile($truhe15);
														
														if($chest15 instanceof Chest)
														{
															$rand = rand(1,10);
																
															$chest15->getInventory()->clearAll();
																
															if($rand == 1)
															{
																$chest15->getRealInventory()->addItem(new Item(298, 0, 1));
															}
															if($rand == 2)
															{
																$chest15->getRealInventory()->addItem(new Item(299, 0, 1));
															}
															if($rand == 3)
															{
																$chest15->getRealInventory()->addItem(new Item(300, 0, 1));
															}
															if($rand == 4)
															{
																$chest15->getRealInventory()->addItem(new Item(301, 0, 1));
															}
															if($rand == 5)
															{
																$chest15->getRealInventory()->addItem(new Item(260, 0, 5));
															}
															if($rand == 6)
															{
																$chest15->getRealInventory()->addItem(new Item(339, 0, 5));
															}
															if($rand == 7)
															{
																$chest15->getRealInventory()->addItem(new Item(325, 8, 1));
															}
															if($rand == 8)
															{
																$chest15->getRealInventory()->addItem(new Item(351, 15, 1));
															}
															if($rand == 9)
															{
																$chest15->getRealInventory()->addItem(new Item(297, 0, 2));
															}
															if($rand == 10)
															{
																$chest15->getRealInventory()->addItem(new Item(320, 0, 2));
															}
														}
														$this->getOwner()->getLogger()->info('erfolgreich kiste gesetzt');
													}
													else
													{
														$this->getOwner()->getLogger()->info('bloecke drum herum sind nicht luft');
													}
												}
												else
												{
													$this->getOwner()->getLogger()->info('block ueber grass ist nicht luft');
												}
											}
											else
											{
												$this->getOwner()->getLogger()->info('kein Grass block gefunden');
											}
										}
									
									}
								$this->getOwner()->chestgenerator = 1;
								//Chest Generator---
								//Chest Generator---
								}
							}
							if($time > $this->getOwner()->afterteleporttimer && $time < $this->getOwner()->afterteleporttimer + 10)
							{
								$player->sendPopUp(MT::GREEN.'Game start NOW!!!');
							}
							if(count($this->getOwner()->players) <= 1 && $time > $this->getOwner()->afterteleporttimer)
							{
								foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
								{
									foreach($this->getOwner()->players as $p)
									{
										$gewinner = $p;
									}
									
									$player->teleport($this->getOwner()->getServer()->getDefaultLevel()->getSafeSpawn());
									$player->sendMessage(MT::RED.'Game Over '.MT::GREEN.'Winner ist '.MT::AQUA.$gewinner);
								}
								
								
								if(isset($this->getOwner()->kisten))
								{
									foreach($this->getOwner()->kisten as $kiste)
									{
										$this->getOwner()->getServer()->getLogger()->info('kiste start');
										$level = $this->getOwner()->selectarena;
										$coords = explode(",", $kiste);
										$this->getOwner()->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);	
										$this->getOwner()->getServer()->getLogger()->info('kiste weg');
									}
									
									foreach($this->getOwner()->getServer()->getLevelbyName("$level")->getTiles() as $chest) {
										if($chest instanceof Chest) {
											$chest->close();
										}
									}
								}
								
								
								$this->getOwner()->getServer()->getLogger()->info('unset start');
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
								$this->getOwner()->getServer()->getLogger()->info('unset ende');
								
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
	
		
	public function onEnable()
	{
		$this->getLogger()->info("SurvivalHive Hungergames loaded!");
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new statuscheck($this), 20);
		if (!file_exists($this->getDataFolder()))
		{
			@mkdir($this->getDataFolder(), true);
		}
			$this->getServer()->getPluginManager()->registerEvents($this, $this);
			$this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML, array("Arena1" => 'world',"Arena2" => 'world2',"Arena3" => 'world3',"Arena4" => 'world4',"Arena5" => 'world5'));
	}
	
	public function onJoin(PlayerJoinEvent $event)
	{
		$name = $event->getPlayer()->getName();
		$event->setJoinMessage(MT::GREEN.'Welcome '.MT::RED.$name.MT::GREEN.' to Hungergameslobby!');
		
		$event->getPlayer()->sendMessage(MT::AQUA.'Vote your fighting place /vote');
		$event->getPlayer()->sendMessage(MT::AQUA.'After first vote and 2 players starts timer');
		$event->getPlayer()->sendMessage(MT::AQUA.'All players in lobby will be teleportet in the arena');
		
		$this->getLogger()->info("$name joint");
		$event->getPlayer()->getInventory()->clearAll();
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
					$sender->sendMessage(MT::RED.'At the moment only "1" possible');
					$this->getLogger()->info("$name benutzt vote befehl");
					return true;
				}
				else
				{
					if($args[0] == 1 || $args[0] == 2 || $args[0] == 3 || $args[0] == 4 || $args[0] == 5)
					{
						$sender->sendMessage(MT::RED.'Thank u for voting the arena '.MT::GREEN.$args[0]);
						$this->getLogger()->info("$name hat gevotet");
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
		}
	}
	
	public function onPlayerMoveEvent(PlayerMoveEvent $event)
	{
		$time = time();
		$name = $event->getPlayer()->getName();
		if(isset($this->afterteleporttimer))
		{
			if($time < $this->afterteleporttimer)
			{
				$this->getLogger()->info("$name move cancel afterteleporttimer");
				$event->setCancelled(true);
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
				if($this->getServer()->getDefaultLevel() == $victim->getLevel())
				{
					$event->setCancelled(true);
				}
			}
		}
	}
	
	public function onRespawn(PlayerRespawnEvent $event)
	{
		$pos = $this->getServer()->getDefaultLevel()->getSafeSpawn();
		
		$event->setRespawnPosition($pos);
	}

	public function onPlayerDeathEvent(PlayerDeathEvent $event)
	{
		$player = $event->getEntity();
		if(!($player instanceof Player))return;
		$name = $event->getEntity()->getName();
		unset ($this->players[$name]);
		
		if(isset($this->players))
		{
			$letztespieler = count($this->players);
			
			foreach($this->getServer()->getOnlinePlayers() as $player)
			{
				$player->sendMessage(MT::RED.$name.' would be killed -> '.MT::GREEN.$letztespieler.MT::RED.' players alive');
			}
		}


	}
	
	public function onPlayerQuitEvent(PlayerQuitEvent $event)
	{
		$name = $event->getPlayer()->getName();
		unset ($this->players[$name]);
		
		if(isset($this->players))
		{
			$letztespieler = count($this->players);
			
			foreach($this->getServer()->getOnlinePlayers() as $player)
			{
				$player->sendMessage(MT::RED.$name.' has left the game -> '.MT::GREEN.$letztespieler.MT::RED.' players alive');
			}
		}
	}
	
 	public function onDisable()
 	{
 		if(isset($this->kisten))
 		{
 			foreach($this->kisten as $kiste)
 			{
 				$this->getServer()->getLogger()->info('kiste start');
 				$level = $this->selectarena;
 				$coords = explode(",", $kiste);
 				$this->getServer()->getLevelByName("$level")->setBlock(new Vector3($coords[0],$coords[1],$coords[2]), BLOCK::get(0), false, true);
 				$this->getServer()->getLogger()->info('kiste weg');
 			}
 				
 			foreach($this->getServer()->getLevelbyName("$level")->getTiles() as $chest) {
 				if($chest instanceof Chest) {
 					$chest->close();
 				}
 			}
 		}
		$this->getLogger()->info("SurvivalHive Hungergames unloaded!");
	}
}
