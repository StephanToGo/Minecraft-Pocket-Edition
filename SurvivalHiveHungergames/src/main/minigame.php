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
use pocketmine\level\format\FullChunk;
use pocketmine\level\format\LevelProvider;
use pocketmine\level\Level;
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
use pocketmine\tile\Spawnable;
use pocketmine\tile\Tile;
use pocketmine\utils\ReversePriorityQueue;
use pocketmine\utils\TextFormat as MT;
use pocketmine\scheduler\PluginTask;
use pocketmine\scheduler\CallbackTask;

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
		
		if(isset($this->getOwner()->players))
		{
			foreach($this->getOwner()->players as $p)
			{
				$this->getOwner()->getLogger()->info("$p ");
			}
		}
		
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
									$player->sendPopUp(MT::BLUE.'no arena voted');
								}
									
								foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
								{
									$x = rand(100,200);
									$y = rand(100,200);
									$z = rand(100,200);
									
									$player->sendMessage('Arena '.$arena.' win');
									$player->sendPopUp(MT::BLUE.'Teleport event start now');
									$player->teleport($this->getOwner()->getServer()->getLevelByName($arenaname)->getSafeSpawn(new Position($x,$y,$z)));
								}
								$this->getOwner()->afterteleporttimer = $time+30;
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
							
							unset ($this->getOwner()->players);
							unset ($this->getOwner()->arena1);
							unset ($this->getOwner()->arena2);
							unset ($this->getOwner()->arena3);
							unset ($this->getOwner()->arena4);
							unset ($this->getOwner()->arena5);
							unset ($this->getOwner()->aftervotetimer);
							unset ($this->getOwner()->afterteleporttimer);
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
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args)
	{
		$name = $sender->getName();
		
		if(!($sender instanceof Player))
		{
			$sender->sendMessage(MT::RED.'Nur im Spiel moeglich / Only ingame possible');
			return true;
		}
		
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
		$event->setCancelled(true);
	}
	
	public function onBlockBreak(BlockBreakEvent $event)
	{
		$event->setCancelled(true);
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

	public function onPlayerDeathEvent(PlayerDeathEvent $event)
	{
		$player = $event->getEntity();
		if(!($player instanceof Player))return;
		$name = $event->getEntity()->getName();
		unset ($this->players[$name]);	
	}
	
	public function onPlayerQuitEvent(PlayerQuitEvent $event)
	{
		$name = $event->getPlayer()->getName();
		unset ($this->players[$name]);
	}
	
 	public function onDisable()
 	{
		$this->getLogger()->info("SurvivalHive Hungergames unloaded!");
	}
}
