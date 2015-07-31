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
use pocketmine\scheduler\CallbackTask;
use pocketmine\tile\Sign;
use pocketmine\tile\Spawnable;
use pocketmine\tile\Tile;
use pocketmine\utils\ReversePriorityQueue;
use pocketmine\utils\TextFormat as MT;

class statuscheck extends PluginTask
{
	public function __construct(Plugin $owner)
	{
		parent::__construct($owner);
	}

	public function onRun($currentTick)
	{
		$spieleranzahl = count($this->getOwner->getServer()->getOnlinePlayers());
		
		foreach($this->getOwner->getServer()->getOnlinePlayers() as $player)
		{
			$name = $player->getNamer();
			
			if($spieleranzahl < 2)$player->sendPopUp(MT::BLUE.'Wait for other players');
			
			if($spieleranzahl >= 2 && (!(isset($this->getOwner()->arena1[$name])) && !(isset($this->getOwner()->arena2[$name])) && !(isset($this->getOwner()->arena3[$name])) && !(isset($this->getOwner()->arena4[$name])) && !(isset($this->getOwner()->arena5[$name])))
			{
				$player->sendPopUp(MT::BLUE.'Vote for arena with /vote *arenanumber*');
			}
			else
			{
				$player->sendPopUp(MT::BLUE.'Wait for other arena votes');
			}
				
		}
	}
}

class minigame extends PluginBase implements Listener{
	
	public $players = array();
	
	public $arena1 = array();
	public $arena2 = array();
	public $arena3 = array();
	public $arena4 = array();
	public $arena5 = array();
	
	public $aftervotetimer = array();
	public $afterteleporttimer = array();

	private $listener;
	public $time = array();
	public $schalter = array();
	public $round = array();
	
		
	public function onEnable()
	{
		$this->getLogger()->info("SurvivalHive Hungergames loaded!");
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new statuscheck($this), 20);
		//if (!file_exists($this->getDataFolder()))
		//{
		//	@mkdir($this->getDataFolder(), true);
		//}
			$this->getServer()->getPluginManager()->registerEvents($this, $this);
		//	$this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML, array("Startblock" => array(),"Spielwelt"=> array()));
			$this->time['Zeit'] = 0;
			$this->round['Zeit'] = 0;
	}
	
	public function onJoin(PlayerJoinEvent $event)
	{
		$name = $event->getPlayer()->getName();
		$event->setJoinMessage(MT::GREEN.'Welcome '.MT::RED.$name.MT::GREEN.' to Hungergameslobby!');
		
		$event->setJoinMessage(MT::AQUA.'Vote your fighting place /vote');
		$event->setJoinMessage(MT::AQUA.'After first vote and 2 players starts timer');
		$event->setJoinMessage(MT::AQUA.'All players in lobby will be teleportet in the arena');
		$event->setJoinMessage(MT::AQUA.'Fight start after 30 seconds');
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args)
	{
		if(!($sender instanceof Player))
		{
			$sender->sendMessage(MTT::RED.'Nur im Spiel moeglich / Only ingame possible');
			return true;
		}
		switch($cmd->getName())
		{
			case "vote":
				if(!(isset($args[0])))
				{
					$sender->sendMessage(MT::RED.'Vote with /vote *arenanumber* (1-5)');
					$sender->sendMessage(MT::RED.'At the moment only "1" possible');
					return true;
				}
				else
				{
					if($args[0] === 1 || $args[0] === 2 || $args[0] === 3 || $args[0] === 4 || $args[0] === 5)
					{
						$sender->sendMessage(MT::RED.'Thank u for voting the arena '.MT::GREEN.$args[0]);
						$name = $sender->getName();
						
						if($args[0] === 1)$this->arena1[$name] = 1;		
						if($args[0] === 2)$this->arena2[$name] = 1;
						if($args[0] === 3)$this->arena3[$name] = 1;
						if($args[0] === 4)$this->arena4[$name] = 1;
						if($args[0] === 5)$this->arena5[$name] = 1;
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
	
	public function onBlockPlaceEvent(BlockPlaceEvent $event)
	{
		$event->setCancelled(true);
	}
	
	public function onBlockBreak(BlockBreakEvent $event)
	{
		$event->setCancelled(true);
	}
	
	
		$time = time();
		$id = $event->getBlock()->getID();
		$p = $event->getPlayer();
		$bl = $event->getBlock();
		$pos = new Vector3($bl->getX(),$bl->getY(),$bl->getZ());
		$name = strtolower($event->getPlayer()->getName());
		
			
			if ($bl->getLevel()->getName() == in_array($bl->getLevel()->getName(), $this->config->get("Spielwelt")))
			{				
				if($id == in_array($id, $this->config->get("Startblock")))
				{
				$time = time();
					if(!($time >= $this->round['Zeit']))
					{
					$event->getPlayer()->sendMessage("Eine Runde laeuft bereits");
					$event->setCancelled();	
					}
					else
					{
						if(count($p->getLevel()->getNearbyEntities(new AxisAlignedBB($pos->getX()-5, $pos->getY()-5, $pos->getZ()-5, $pos->getX()+5, $pos->getY()+5, $pos->getZ()+5))) >= 4)
						{
							$p = $event->getPlayer();
							$pos = new Vector3($bl->getX(),$bl->getY(),$bl->getZ());
							$event->getPlayer()->teleport(new Position(rand(170, 161), 4, rand(132, 136))) || $event->getPlayer()->teleport(new Position(rand(102, 113), 4, rand(120, 132))) || $event->getPlayer()->teleport(new Position(rand(117, 122), 4, rand(131, 147))) || $event->getPlayer()->teleport(new Position(rand(151, 156), 4, rand(109, 125)));
							$event->getPlayer()->sendMessage("15 Sek. bis zum Start LEGE DEINE RUESTUNG AN!!!");
							$this->schalter[$name] = $p->getID();
							$this->time['Zeit'] = ($time + 15);
							$this->round['Zeit'] = ($time + 135);
							
							foreach($p->getLevel()->getNearbyEntities(new AxisAlignedBB($pos->getX()-5, $pos->getY()-5, $pos->getZ()-5, $pos->getX()+5, $pos->getY()+5, $pos->getZ()+5), $p) as $entity)
							{
								if ($entity instanceof Player)
								{
									$p2 = $entity->getPlayer();
									$entity->sendMessage("15 Sek. bis zum Start LEGE DEINE RUESTUNG AN!!!");
									$entity->teleport(new Position(rand(170, 161), 4, rand(132, 136))) || $event->getPlayer()->teleport(new Position(rand(102, 113), 4, rand(120, 132))) || $event->getPlayer()->teleport(new Position(rand(117, 122), 4, rand(131, 147))) || $event->getPlayer()->teleport(new Position(rand(151, 156), 4, rand(109, 125)));
									$id = $entity->getID();
									$name = $entity->getName();
									$this->schalter[$name] = $entity->getID();
									$time = time();
									
									$p->getInventory()->addItem(new Item(276, 0, 1));
									$p->getInventory()->addItem(new Item(310, 0, 1));
									$p->getInventory()->addItem(new Item(311, 0, 1));
									$p->getInventory()->addItem(new Item(312, 0, 1));
									$p->getInventory()->addItem(new Item(313, 0, 1));
									$p->getInventory()->addItem(new Item(297, 0, 5));
									$p2->getInventory()->addItem(new Item(276, 0, 1));
									$p2->getInventory()->addItem(new Item(310, 0, 1));
									$p2->getInventory()->addItem(new Item(311, 0, 1));
									$p2->getInventory()->addItem(new Item(312, 0, 1));
									$p2->getInventory()->addItem(new Item(313, 0, 1));
									$p2->getInventory()->addItem(new Item(297, 0, 5));
									
									$event->setCancelled();
								}
							}
						$event->setCancelled();
					}
					else
					{
						$event->getPlayer()->sendMessage("Erst ab 4 Spieler und mehr - " . count($p->getLevel()->getNearbyEntities(new AxisAlignedBB($pos->getX()-5, $pos->getY()-5, $pos->getZ()-5, $pos->getX()+5, $pos->getY()+5, $pos->getZ()+5))) . " Spieler in der naehe.");
						$event->setCancelled();
					}
				}
			}
			$event->setCancelled();
		
	}
	
	
	
	public function onPlayerMoveEvent(PlayerMoveEvent $event)
	{
		if ($event->getPlayer()->getLevel()->getName() == in_array($event->getPlayer()->getLevel()->getName(), $this->config->get("Spielwelt")))
		{
		$time = time();
		$name = strtolower($event->getPlayer()->getName());
		$p = $event->getPlayer();
		
				if(!($time >= $this->time['Zeit']))
				{
					$zeigzeit = ($this->time['Zeit'] - $time);
					$event->setCancelled();
					
				}
				if($time == ($this->round['Zeit']-60) || $time == ($this->round['Zeit']-59)|| $time == ($this->round['Zeit']-58))
				{
					$zeigzeit = ($this->round['Zeit'] - $time);
					$event->getPlayer()->sendMessage("$zeigzeit");
					$event->getPlayer()->sendMessage("Spieler " . count($this->schalter)." noch 1 Minute" );
					
				}
				if(in_array($event->getPLayer()->getID(), $this->schalter))
				{
					if($time >= $this->round['Zeit'])
					{
						$zeigzeit = ($this->round['Zeit'] - $time);
						$bl = $event->getPlayer();
						$pos = new Vector3($bl->getX(),$bl->getY(),$bl->getZ());
								
						if(count($this->schalter) >= 2)
						{
							foreach($event->getPlayer()->getLevel()->getNearbyEntities(new AxisAlignedBB($pos->getX()-150, $pos->getY()-100, $pos->getZ()-150, $pos->getX()+150, $pos->getY()+100, $pos->getZ()+150), $p) as $entity)
							{
								if ($entity instanceof Player)
								{	
									$p2 = $entity->getPlayer();
									$entity->sendMessage("Runde ist beendet");
									$entity->sendMessage("Unentschieden - kein Sieger - Kein Gewinn");
									//$index = array_search($entity->getID(), $this->schalter);
									//unset($this->schalter[$index]);
									$entity->teleport(Server::getInstance()->getLevelByName('pocket')->getSafeSpawn());
								}
							}
							
							$event->getPlayer()->sendMessage("Runde ist beendet");
							$event->getPlayer()->sendMessage("Unentschieden - kein Sieger - Kein Gewinn");
							//$index = array_search($event->getPlayer()->getID(), $this->schalter);
							//unset($this->schalter[$index]);
							$event->getPlayer()->teleport(Server::getInstance()->getLevelByName('pocket')->getSafeSpawn());
						}
					}
					if(count($this->schalter) == 1)
					{
						$event->getPlayer()->teleport(Server::getInstance()->getLevelByName('pocket')->getSafeSpawn());
						//$event->getPlayer()->sendMessage("Du hast gewonnen $1000 + 5Diamanten");
						$event->getPlayer()->sendMessage("GEWONNEN - Noch gibt es keine Gewinne - Tut uns leid (Test)");
						$i = "givemoney {PLAYER} 1000";
						$i2 = "give {PLAYER} 264 5";
						//$this->getServer()->dispatchCommand(new ConsoleCommandSender(),str_replace("{PLAYER}",$event->getPlayer()->getName(),$i));
						//$this->getServer()->dispatchCommand(new ConsoleCommandSender(),str_replace("{PLAYER}",$event->getPlayer()->getName(),$i2));
						//$index = array_search($event->getPlayer()->getID(), $this->schalter);
						//unset($this->schalter[$index]);
						$this->round['Zeit'] = 0;
					}
				}				
		}
	}
	
	public function onPlayerDeathEvent(PlayerDeathEvent $event)
	{
			if ($event->getEntity()->getLevel()->getName() == in_array($event->getEntity()->getLevel()->getName(), $this->config->get("Spielwelt")))
			{
				$p = $event->getEntity();

				$event->setKeepInventory(true);
				$p->teleport(Server::getInstance()->getLevelByName('pocket')->getSafeSpawn());
				/*$id = $event->getEntity()->getID();
				$name = $event->getEntity()->getName();
				$index = array_search($id, $this->schalter);
				unset($this->schalter[$index]);
				$event->getEntity()->sendMessage("Du bist ausgeschieden");	*/	
			}
		
	}
	
	public function onEntityLevelChangeEvent(EntityLevelChangeEvent $event)
	{
		//if ($event->getEntity()->getLevel()->getName() == in_array($event->getEntity()->getLevel()->getName(), $this->config->get("Spielwelt")))
		//{
			$player = $event->getEntity();
			$id = $event->getEntity()->getID();
			$ziel = $event->getTarget()->getName();
			$von = $event->getOrigin()->getName();
			
			
			if($ziel == 'pocket' && $von == 'minigame' && in_array($id, $this->schalter))
			{
			
			
			$name = $event->getEntity()->getName();
			$index = array_search($id, $this->schalter);
			unset($this->schalter[$index]);
			$event->getEntity()->sendMessage("Du bist ausgeschieden");
			
			$player->getInventory()->remove(new Item(276, 0, 1));
			$player->getInventory()->remove(new Item(310, 0, 1));
			$player->getInventory()->remove(new Item(311, 0, 1));
			$player->getInventory()->remove(new Item(312, 0, 1));
			$player->getInventory()->remove(new Item(313, 0, 1));
			$player->getInventory()->remove(new Item(297, 0, 5));
			}
		//}
	
	}
	
	public function onPlayerQuitEvent(PlayerQuitEvent $event)
	{
		if ($event->getPlayer()->getLevel()->getName() == in_array($event->getPlayer()->getLevel()->getName(), $this->config->get("Spielwelt")))
		{
			//$event->getPlayer()->teleport(Server::getInstance()->getLevelByName('pocket')->getSafeSpawn());
			$id = $event->getPlayer()->getID();
			$index = array_search($id, $this->schalter);
			unset($this->schalter[$index]);
		}
	}
	
 	public function onDisable(){
		$this->getLogger()->info("SurvivalHive Hungergames unloaded!");
	}
}
