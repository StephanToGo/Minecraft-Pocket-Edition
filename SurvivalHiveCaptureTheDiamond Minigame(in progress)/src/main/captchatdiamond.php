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
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\inventory\InventoryHolder;
use pocketmine\inventory\SimpleTransactionGroup;
use pocketmine\inventory\StonecutterShapelessRecipe;
use pocketmine\item\Item;
use pocketmine\level;
use pocketmine\level\format\FullChunk;
use pocketmine\level\format\LevelProvider;
use pocketmine\level\Position;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\math\AxisAlignedBB;
use pocketmine\metadata\MetadataValue;
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
		$time = time();
		foreach($this->getOwner()->getServer()->getOnlinePlayers() as  $player)
		{
			$name = $player->getName();
			if(isset($this->getOwner()->pteam[$name]))
			{
				switch ($this->getOwner()->pteam[$name])
				{
					case "Rot":
						$player->sendTip("\n\n\n\n".MT::RED.$this->getOwner()->team['Rot'].MT::BLUE.$this->getOwner()->team['Blau'].MT::GREEN.$this->getOwner()->team['Gruen'].MT::YELLOW.$this->getOwner()->team['Gelb']."\n".MT::BLUE.$this->getOwner()->punkte['Blau'].MT::GREEN.$this->getOwner()->punkte['Gruen'].MT::YELLOW.$this->getOwner()->punkte['Gelb'].'                               '.MT::RED.'Team Red '.$this->getOwner()->punkte['Rot']);
						break;
					case "Blau":
						$player->sendTip("\n\n\n\n".MT::RED.$this->getOwner()->team['Rot'].MT::BLUE.$this->getOwner()->team['Blau'].MT::GREEN.$this->getOwner()->team['Gruen'].MT::YELLOW.$this->getOwner()->team['Gelb']."\n".MT::RED.$this->getOwner()->punkte['Rot'].MT::GREEN.$this->getOwner()->punkte['Gruen'].MT::YELLOW.$this->getOwner()->punkte['Gelb'].'                               '.MT::BLUE.'Team Blue '.$this->getOwner()->punkte['Blau']);
						break;
					case "Gruen":
						$player->sendTip("\n\n\n\n".MT::RED.$this->getOwner()->team['Rot'].MT::BLUE.$this->getOwner()->team['Blau'].MT::GREEN.$this->getOwner()->team['Gruen'].MT::YELLOW.$this->getOwner()->team['Gelb']."\n".MT::BLUE.$this->getOwner()->punkte['Blau'].MT::RED.$this->getOwner()->punkte['Rot'].MT::YELLOW.$this->getOwner()->punkte['Gelb'].'                              '.MT::GREEN.'Team Green '.$this->getOwner()->punkte['Gruen']);
						break;
					case "Gelb":
						$player->sendTip("\n\n\n\n".MT::RED.$this->getOwner()->team['Rot'].MT::BLUE.$this->getOwner()->team['Blau'].MT::GREEN.$this->getOwner()->team['Gruen'].MT::YELLOW.$this->getOwner()->team['Gelb']."\n".MT::BLUE.$this->getOwner()->punkte['Blau'].MT::GREEN.$this->getOwner()->punkte['Gruen'].MT::RED.$this->getOwner()->punkte['Rot'].'                               '.MT::YELLOW.'Team YELLOW '.$this->getOwner()->punkte['Gelb']);
						break;
				}
				
				if(isset($this->getOwner()->hatflagge))
				{
					$nameflagge = $this->getOwner()->hatflagge;
					
					switch ($this->getOwner()->pteam[$nameflagge])
					{
						case "Rot":
							$player->sendPopUp(MT::GOLD.$this->getOwner()->hatflagge.MT::WHITE.' from'.MT::RED.' team red'.MT::WHITE.' have the diamond');
							break;
						case "Blau":
							$player->sendPopUp(MT::GOLD.$this->getOwner()->hatflagge.MT::WHITE.' from'.MT::BLUE.' team blue'.MT::WHITE.' have the diamond');
							break;
						case "Gruen":
							$player->sendPopUp(MT::GOLD.$this->getOwner()->hatflagge.MT::WHITE.' from'.MT::GREEN.' team green'.MT::WHITE.' have the diamond');
							break;
						case "Gelb":
							$player->sendPopUp(MT::GOLD.$this->getOwner()->hatflagge.MT::WHITE.' from'.MT::YELLOW.' team yellow'.MT::WHITE.' have the diamond');
							break;
					}	
				}
				else
				{
					if($this->getOwner()->isrunning)
					{
					$roundtime = $this->getOwner()->roundtime - $time;
					$player->sendPopUp(MT::GOLD.'Seconds until gameround end '.$roundtime);
					}
				}
			}
			
			
			if(!isset($this->getOwner()->pteam[$name]) && !$this->getOwner()->isrunning)$player->sendTip(MT::AQUA."Select your team color\nWaehle deine Teamfarbe");
			if(!isset($this->getOwner()->pteam[$name]) && $this->getOwner()->isrunning)
			{
				$roundtime = $this->getOwner()->roundtime - $time;
				$player->sendTip(MT::AQUA."Round is running, please wait... $roundtime\nuse ".MT::GOLD.'/watch'.MT::AQUA." to see the running game\n".MT::BLUE.$this->getOwner()->punkte['Blau'].MT::GREEN.$this->getOwner()->punkte['Gruen'].MT::YELLOW.$this->getOwner()->punkte['Gelb'].MT::RED.$this->getOwner()->punkte['Rot']);
			}
			
			if(($this->getOwner()->team['Rot'] != 0 || $this->getOwner()->team['Blau'] != 0 || $this->getOwner()->team['Gruen'] != 0  || $this->getOwner()->team['Gelb'] != 0))
			{
				if(!isset($this->getOwner()->roundstarttime))$this->getOwner()->roundstarttime = ($time + 60);
				
				if($time <= $this->getOwner()->roundstarttime)
				{
					$zeit = $this->getOwner()->roundstarttime - $time;
					$player->sendPopUp(MT::AQUA."Somebody have select a team\n$zeit seconds until round start");
				}
				if($time > $this->getOwner()->roundstarttime && $this->getOwner()->isrunning == false)
				{
					foreach($this->getOwner()->getServer()->getLevelbyName($this->getOwner()->arena1name)->getEntities() as $entity)
					{
						if(!$entity instanceof Player)$entity->close();
					}
					$coords = explode(",", $this->diamondspawn);
					$pos = new Vector3($coords[0], $coords[1], $coords[2]);
					$this->getOwner()->getServer()->getLevelbyName($this->getOwner()->arena1name)->dropItem($pos, Item::get(264));
					
					foreach($this->getOwner()->getServer()->getOnlinePlayers() as  $player)
					{
						$name = $player->getName();
						if(!isset($this->getOwner()->pteam[$name]))
						{
							if(!($this->getOwner()->team['Rot'] > $this->getOwner()->team['Blau'] || $this->getOwner()->team['Rot'] > $this->getOwner()->team['Gruen'] || $this->getOwner()->team['Rot'] > $this->getOwner()->team['Gelb']))
							{
								$player->setNameTag(MT::RED.$player->getDisplayName());
								$this->getOwner()->team['Rot'] = ($this->getOwner()->team['Rot']+1);
								$this->getOwner()->pteam[$name] = "Rot";
								$player->sendMessage(MT::RED.'Your now in team red');
							}
							elseif(!($this->getOwner()->team['Blau'] > $this->getOwner()->team['Rot'] || $this->getOwner()->team['Blau'] > $this->getOwner()->team['Gruen'] || $this->getOwner()->team['Blau'] > $this->getOwner()->team['Gelb']))
							{
								$player->setNameTag(MT::BLUE.$player->getDisplayName());
								$this->getOwner()->team['Blau'] = ($this->getOwner()->team['Blau']+1);
								$this->getOwner()->pteam[$name] = "Blau";
								$player->sendMessage(MT::BLUE.'Your now in team blue');
								$this->getOwner()->pteam[$name] = "Blau";
							}
							elseif(!($this->getOwner()->team['Gelb'] > $this->getOwner()->team['Blau'] || $this->getOwner()->team['Gelb'] > $this->getOwner()->team['Gruen'] || $this->getOwner()->team['Gelb'] > $this->getOwner()->team['Rot']))
							{
								$player->setNameTag(MT::YELLOW.$player->getDisplayName());
								$this->getOwner()->team['Gelb'] = ($this->getOwner()->team['Gelb']+1);
								$this->getOwner()->pteam[$name] = "Gelb";
								$player->sendMessage(MT::YELLOW.'Your now in team yellow');
								$this->getOwner()->pteam[$name] = "Gelb";
							}
							elseif(!($this->getOwner()->team['Gruen'] > $this->getOwner()->team['Blau'] || $this->getOwner()->team['Gruen'] > $this->getOwner()->team['Rot'] || $this->getOwner()->team['Gruen'] > $this->getOwner()->team['Gelb']))
							{
								$player->setNameTag(MT::GREEN.$player->getDisplayName());
								$this->getOwner()->team['Gruen'] = ($this->getOwner()->team['Gruen']+1);
								$this->getOwner()->pteam[$name] = "Gruen";
								$player->sendMessage(MT::GREEN.'Your now in team green');
								$this->getOwner()->pteam[$name] = "Gruen";
							}
						}
						switch ($this->getOwner()->pteam[$name])
						{
							case "Rot":
								$pos = explode(",", $this->getOwner()->teamspawnrot);
								$player->teleport($this->getOwner()->getServer()->getLevelByName($this->getOwner()->arena1name)->getSafeSpawn(new Position($pos[0], $pos[1], $pos[2])));
								break;
							case "Blau":
								$pos = explode(",", $this->getOwner()->teamspawnblau);
								$player->teleport($this->getOwner()->getServer()->getLevelByName($this->getOwner()->arena1name)->getSafeSpawn(new Position($pos[0], $pos[1], $pos[2])));
								break;
							case "Gelb":
								$pos = explode(",", $this->getOwner()->teamspawngelb);
								$player->teleport($this->getOwner()->getServer()->getLevelByName($this->getOwner()->arena1name)->getSafeSpawn(new Position($pos[0], $pos[1], $pos[2])));
								break;
							case "Gruen":
								$pos = explode(",", $this->getOwner()->teamspawngruen);
								$player->teleport($this->getOwner()->getServer()->getLevelByName($this->getOwner()->arena1name)->getSafeSpawn(new Position($pos[0], $pos[1], $pos[2])));
								break;
						}
						$team = $this->getOwner()->pteam[$name];
						$this->getOwner()->getLogger()->info("$name $team");
						
						$this->getOwner()->teilnehmer[$name] = 1;
						
						//Schwert
						$player->getInventory()->addItem(new Item(268, 0, 1));
						//Bogen&Pfeile
						$player->getInventory()->addItem(new Item(261, 0, 1));
						$player->getInventory()->addItem(new Item(262, 0, 15));
						//Essen
						$player->getInventory()->addItem(new Item(357, 0, 5));
						
						$player->getInventory()->setHelmet(new Item(298));
						$player->getInventory()->sendArmorContents($player);
						
						$player->getInventory()->setChestplate(new Item(299, 0, 1));
						$player->getInventory()->sendArmorContents($player);
						
						$player->getInventory()->setLeggings(new Item(300, 0, 1));
						$player->getInventory()->sendArmorContents($player);
						
						$player->getInventory()->setBoots(new Item(301, 0, 1));
						$player->getInventory()->sendArmorContents($player);
						
					}
					$this->getOwner()->roundtime = $time + 300;
					$this->getOwner()->isrunning = true;	
				}
			}
			if($this->getOwner()->isrunning && $this->getOwner()->punkte['Rot'] == 3 || $this->getOwner()->punkte['Blau'] == 3 || $this->getOwner()->punkte['Gruen'] == 3 || $this->getOwner()->punkte['Gelb'] == 3)
			{
				foreach($this->getOwner()->getServer()->getLevelbyName($this->getOwner()->arena1name)->getEntities() as $entity)
				{
					if(!$entity instanceof Player)$entity->close();
				}
				foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
				{
					$player->teleport($this->getOwner()->getServer()->getLevelbyName($this->getOwner()->lobbyname)->getSafeSpawn());
					$player->getInventory()->clearAll();
					$player->setHealth(20);
					$player->setGamemode(0);
					$player->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_SNEAKING, false);
					$player->setNameTag($player->getDisplayName());
					if($this->getOwner()->punkte['Rot'] == 3)$player->sendMessage(MT::RED.'Team red have win the game!');
					if($this->getOwner()->punkte['Blau'] == 3)$player->sendMessage(MT::BLUE.'Team blue have win the game!');
					if($this->getOwner()->punkte['Gruen'] == 3)$player->sendMessage(MT::GREEN.'Team green have win the game!');
					if($this->getOwner()->punkte['Gelb'] == 3)$player->sendMessage(MT::YELLOW.'Team yellow have win the game!');
				}
				$this->getOwner()->onRoundReset();
			}
			if($this->getOwner()->isrunning && ((count($this->getOwner()->teilnehmer) < 2) || $time > $this->getOwner()->roundtime))
			{
				$rot = $this->getOwner()->punkte['Rot'];
				$blau = $this->getOwner()->punkte['Blau'];
				$gruen = $this->getOwner()->punkte['Gruen'];
				$gelb = $this->getOwner()->punkte['Gelb'];
				
				if($rot > $blau && $rot > $gruen && $rot > $gelb)
				{
					foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
					{
						$player->sendMessage(MT::RED.'Team red have win the game!');
						
					}
				}
				elseif($blau > $rot && $blau > $gruen && $blau > $gelb)
				{
					foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
					{
						$player->sendMessage(MT::BLUE.'Team blue have win the game!');
					}
				}
				elseif($gruen > $rot && $gruen > $blau && $gruen > $gelb)
				{
					foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
					{
						$player->sendMessage(MT::GREEN.'Team green have win the game!');
					}
				}
				elseif($gelb > $blau && $gelb > $gruen && $gelb > $rot)
				{
					foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
					{
						$player->sendMessage(MT::YELLOW.'Team yellow have win the game!');
					}
				}
				else
				{
					foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
					{
						$player->sendMessage(MT::WHITE.'No winning team');
					}
				}
				
				$this->getOwner()->onRoundReset();
			}
			if(isset($this->getOwner()->diamonddrop))
			{
				$diff = $this->getOwner()->diamonddrop - $time;
				if(($this->getOwner()->isrunning) && ($time > $this->getOwner()->diamonddrop))
				{
					foreach($this->getOwner()->getServer()->getLevelbyName($this->getOwner()->arena1name)->getEntities() as $entity)
					{
						if(!$entity instanceof Player)$entity->close();
					}
					foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
					{
						$player->sendMessage(MT::GOLD.'Diamond dropped for to long time -> back to the middle!');
						$player->sendMessage(MT::GOLD.'Diamant lag zulang rum -> zurueck zur Mitte!');
					}
					$coords = explode(",", $this->diamondspawn);
					$pos = new Vector3($coords[0], $coords[1], $coords[2]);
					$this->getOwner()->getServer()->getLevelbyName($this->getOwner()->arena1name)->dropItem($pos, Item::get(264));
					
					unset ($this->getOwner()->diamonddrop);
				}
			}
		}
	}	
}
class status extends PluginTask
{
	public function __construct(Plugin $owner)
	{
		parent::__construct($owner);
	}
	public function onRun($currentTick)
	{
		if(isset($this->getOwner()->pname))$this->getOwner()->onDebug(MT::GREEN.'Pname gesetzt');
		if($this->getOwner()->isrunning)$this->getOwner()->onDebug(MT::GREEN. 'IsRunning TRUE');
		if(!$this->getOwner()->isrunning)$this->getOwner()->onDebug(MT::GREEN. 'IsRunning FALSE');
	}
}
class captchatdiamond extends PluginBase implements Listener{
	
	private $listener;
	
	public $isrunning;
	
	public $hatflagge;
	
	public $diamonddrop;
	
	public $roundtime;
	
	public $team = array();
	public $punkte = array();
	public $pteam = array();
	
	public function onEnable()
	{
		$this->getLogger()->info('SurvivalHive CTF loaded!');
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new statuscheck($this), 20);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new status($this), 300);
		
		if (!file_exists($this->getDataFolder()))
		{
			@mkdir($this->getDataFolder(), true);
		}
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML, 
				array(
						"Lobby" => 'lobbyworld',
						"Arena1" => 'arena',
						"DiamondSpawn" => "124,64,124",
						"TeamSpawnBlau" => "126,64,128",
						"TeamSpawnRot" => "126,64,122",
						"TeamSpawnGelb" => "122,64,122",
						"TeamSpawnGruen" => "122,64,128"	
				));
		
		//-----------------------------------------
		
		$this->lobbyname = 	$this->config->get("Lobby");
		
		$this->arena1name = 	$this->config->get("Arena1");
		
		$this->teamspawnblau = $this->config->get("TeamSpawnBlau");
		$this->teamspawnrot = $this->config->get("TeamSpawnRot");
		$this->teamspawngelb = $this->config->get("TeamSpawnGelb");
		$this->teamspawngruen = $this->config->get("TeamSpawnGruen");
		$this->diamondspawn = $this->config->get("DiamondSpawn");
		
		$this->team['Rot'] = 0;
		$this->team['Blau'] = 0;
		$this->team['Gelb'] = 0;
		$this->team['Gruen'] = 0;
		
		$this->punkte['Rot'] = 0;
		$this->punkte['Blau'] = 0;
		$this->punkte['Gelb'] = 0;
		$this->punkte['Gruen'] = 0;
		
		$this->isrunning = false;
	}
	public function onJoin(PlayerJoinEvent $event)
	{
		$name = $event->getPlayer()->getName();
		$event->setJoinMessage('');
		
		$event->getPlayer()->sendMessage(MT::GREEN.'Welcome '.MT::RED.$name.MT::GREEN.' to Catpcha the DIAMOND!');
		$event->getPlayer()->sendMessage(MT::AQUA.'Select a team in that u want to play');
		$event->getPlayer()->sendMessage(MT::AQUA.'If u dont select one its random by start');
		$event->getPlayer()->sendMessage(MT::GOLD.'Got the diamond in the middle and bring it back to your base');
		$event->getPlayer()->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_SNEAKING, false);
		$event->getPlayer()->getInventory()->clearAll();
		$event->getPlayer()->setGamemode(0);
		$event->getPlayer()->teleport($this->getServer()->getLevelbyName($this->lobbyname)->getSafeSpawn());
	}
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args)
	{
		$name = $sender->getName();
		
		switch($cmd->getName())
		{
			case "watch":
				$level = $sender->getLevel()->getName();
				if($level == $this->lobbyname && $this->isrunning)
				{
					if(!isset($this->pteam[$name]))
					{
					$coords = explode(",", $this->diamondspawn);
					$sender->teleport($this->getServer()->getLevelByName($this->arena1name)->getSafeSpawn(new Position($coords[0], $coords[1], $coords[2])));
					$sender->setGamemode(3);
					return true;
					}
				}
				if($level == $this->arena1name && $this->isrunning)
				{
					if(!isset($this->pteam[$name]))
					{
					$sender->teleport($this->getServer()->getLevelByName($this->lobbyname)->getSafeSpawn());
					$sender->setGamemode(0);
					return true;
					}
				}
		}
	}
	public function onPlayerMoveEvent(PlayerMoveEvent $event)
	{
		
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
				$victimname = $victim->getName();
				$attackername = $attacker->getName();		
				if($this->lobbyname == $victim->getLevel()->getName() || $this->lobbyname == $attacker->getLevel()->getName() )
				{
					$event->setCancelled(true);
				}
				else 
				{
					if(isset($this->pteam[$victimname]) && isset($this->pteam[$attackername]))
					{
						if($this->pteam[$victimname] == $this->pteam[$attackername])
						{
							$attacker->sendMessage(MT::GOLD.'Player is in your team!');
							$event->setCancelled(true);
						}
					}
				}
			}
		}
	}
	public function onRespawn(PlayerRespawnEvent $event)
	{
		$name = $event->getPlayer()->getName();
		$event->getPlayer()->getInventory()->clearAll();
		
		if($this->isrunning && (isset($this->pteam[$name])))
		{
			switch($this->pteam[$name])
			{
				case "Rot":
					$pos = explode(",", $this->teamspawnrot);
					$event->setRespawnPosition(new Position($pos[0], $pos[1], $pos[2]));
					break;
				case "Blau":
					$pos = explode(",", $this->teamspawnblau);
					$event->setRespawnPosition(new Position($pos[0], $pos[1], $pos[2]));
					break;
				case "Gruen":
					$pos = explode(",", $this->teamspawngruen);
					$event->setRespawnPosition(new Position($pos[0], $pos[1], $pos[2]));
					break;
				case "Gelb":
					$pos = explode(",", $this->teamspawngelb);
					$event->setRespawnPosition(new Position($pos[0], $pos[1], $pos[2]));
					break;
			}
			
			//Schwert
			$event->getPlayer()->getInventory()->addItem(new Item(268, 0, 1));
			//Bogen&Pfeile
			$event->getPlayer()->getInventory()->addItem(new Item(261, 0, 1));
			$event->getPlayer()->getInventory()->addItem(new Item(262, 0, 15));
			//Essen
			$event->getPlayer()->getInventory()->addItem(new Item(357, 0, 5));
			
			$event->getPlayer()->getInventory()->setHelmet(new Item(298));
			$event->getPlayer()->getInventory()->sendArmorContents($event->getPlayer());
			
			$event->getPlayer()->getInventory()->setChestplate(new Item(299, 0, 1));
			$event->getPlayer()->getInventory()->sendArmorContents($event->getPlayer());
			
			$event->getPlayer()->getInventory()->setLeggings(new Item(300, 0, 1));
			$event->getPlayer()->getInventory()->sendArmorContents($event->getPlayer());
			
			$event->getPlayer()->getInventory()->setBoots(new Item(301, 0, 1));
			$event->getPlayer()->getInventory()->sendArmorContents($event->getPlayer());
			
			$event->getPlayer()->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_SNEAKING, false);
		}
		else
		{
			$event->getPlayer()->getInventory()->clearAll();
			$pos = $this->getServer()->getLevelbyName($this->lobbyname)->getSafeSpawn();
			$event->setRespawnPosition($pos);
		}
		
	}
	public function onPlayerDeathEvent(PlayerDeathEvent $event)
	{
		
		if($this->isrunning)
		{
			if($event->getEntity() instanceof Player)
			{
				$event->setKeepInventory(true);
				$name = $event->getEntity()->getName();	
				if(isset($this->hatflagge))
				{
					if($this->hatflagge == $name)
					{
						foreach($this->getServer()->getOnlinePlayers() as $player)
						{
							$player->sendMessage(MT::GOLD.'Caution: Diamond is back the middle!');
						}
						$coords = explode(",", $this->diamondspawn);
						$pos = new Vector3($coords[0], $coords[1], $coords[2]);
						$this->getServer()->getLevelbyName($this->arena1name)->dropItem($pos, Item::get(264));
						unset ($this->hatflagge);
					}
				}
			}
			
		}
		
	}
	public function onItemPickupEvent(InventoryPickupItemEvent $event)
	{
		$name = $event->getInventory()->getHolder()->getName();
		
		$item = $event->getItem()->getItem()->getID(); 
		if($item == 264)
		{
			$this->onDebug(MT::GOLD.$name.' hat Flagge');
			$event->getInventory()->getHolder()->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_SNEAKING, true);
			$this->hatflagge = $name;
			unset ($this->diamonddrop);
		}		
	}
	public function onPlayerdropItemEvent(PlayerDropItemEvent $event)
	{
		$name = $event->getPlayer()->getName();
		$item = $event->getItem()->getID();
		$time = time();
		if($item == 264)
		{
			$event->getPlayer()->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_SNEAKING, false);
			$punktezoneID = $this->getServer()->getLevelbyname($this->arena1name)->getBlock(new Vector3($event->getPlayer()->getX(),$event->getPlayer()->getY()-1,$event->getPlayer()->getZ()))->getId();
			$punktezoneID1 = $this->getServer()->getLevelbyname($this->arena1name)->getBlockDataAt($event->getPlayer()->getX(),$event->getPlayer()->getY()-1,$event->getPlayer()->getZ());
			unset ($this->hatflagge);
			$this->onDebug(MT::GOLD.'Flagge wurde fallen gelassen');
			if($punktezoneID == '159' && $this->isrunning)
			{

				switch($this->pteam[$name])
				{
					case "Rot":
						if($punktezoneID1 == 14)
						{
							$coords = explode(",", $this->diamondspawn);
							$pos = new Vector3($coords[0], $coords[1], $coords[2]);
							$this->getServer()->getLevelbyName($this->arena1name)->dropItem($pos, Item::get(264));
							
							$event->setCancelled();
							$event->getPlayer()->getInventory()->remove(new Item(264));
							
							$this->punkte['Rot'] = ($this->punkte['Rot'] + 1);
							
							foreach($this->getServer()->getOnlinePlayers() as $player)
							{
								$player->sendMessage(MT::RED.'Team red got a point!');
								$player->sendMessage(MT::GOLD.'Diamond is back to the middle');
							}
						}
						else
						{
							$event->getPlayer()->sendMessage(MT::GOLD.'Wrong base bro...');
						}
					break;
					case "Blau":
						if($punktezoneID1 == 3)
						{
							$coords = explode(",", $this->diamondspawn);
							$pos = new Vector3($coords[0], $coords[1], $coords[2]);
							$this->getServer()->getLevelbyName($this->arena1name)->dropItem($pos, Item::get(264));
							
							$event->setCancelled();
							$event->getPlayer()->getInventory()->remove(new Item(264));
							
							$this->punkte['Blau'] = ($this->punkte['Blau'] + 1);
							
							foreach($this->getServer()->getOnlinePlayers() as $player)
							{
								$player->sendMessage(MT::BLUE.'Team blue got a point!');
								$player->sendMessage(MT::GOLD.'Diamond is back to the middle');
							}
						}
						else
						{
							$event->getPlayer()->sendMessage(MT::GOLD.'Wrong base bro...');
						}
					break;
					case "Gruen":
						if($punktezoneID1 == 5)
						{
							$coords = explode(",", $this->diamondspawn);
							$pos = new Vector3($coords[0], $coords[1], $coords[2]);
							$this->getServer()->getLevelbyName($this->arena1name)->dropItem($pos, Item::get(264));
							
							$event->setCancelled();
							$event->getPlayer()->getInventory()->remove(new Item(264));
							
							$this->punkte['Gruen'] = ($this->punkte['Gruen'] + 1);
							
							foreach($this->getServer()->getOnlinePlayers() as $player)
							{
								$player->sendMessage(MT::GREEN.'Team green got a point!');
								$player->sendMessage(MT::GOLD.'Diamond is back to the middle');
							}
						}
						else
						{
							$event->getPlayer()->sendMessage(MT::GOLD.'Wrong base bro...');
						}
					break;
					case "Gelb":
						if($punktezoneID1 == 4)
						{
							$coords = explode(",", $this->diamondspawn);
							$pos = new Vector3($coords[0], $coords[1], $coords[2]);
							$this->getServer()->getLevelbyName($this->arena1name)->dropItem($pos, Item::get(264));
							
							$event->setCancelled();
							$event->getPlayer()->getInventory()->remove(new Item(264));
							
							$this->punkte['Gelb'] = ($this->punkte['Gelb'] + 1);
							
							foreach($this->getServer()->getOnlinePlayers() as $player)
							{
								$player->sendMessage(MT::YELLOW.'Team yellow got a point!');
								$player->sendMessage(MT::GOLD.'Diamond is back to the middle');
							}
						}
						else
						{
							$event->getPlayer()->sendMessage(MT::GOLD.'Wrong base bro...');
						}
					break;
				}
			}
			if($punktezoneID != '159' && $this->isrunning)
			{
				$this->diamonddrop = $time + 60;
			}
		}
	}
	
	public function onPlayerQuitEvent(PlayerQuitEvent $event)
	{
		$name = $event->getPlayer()->getName();
		if($this->isrunning == true && isset($this->pteam[$name]))
		{
			unset ($this->teilnehmer[$name]);

			switch($this->pteam[$name])
			{
				case "Rot":
					$this->team['Rot'] = ($this->team['Rot']-1);
					unset ($this->pteam[$name]);
					break;
				case "Blau":
					$this->team['Blau'] = ($this->team['Blau']-1);
					unset ($this->pteam[$name]);
					break;
				case "Gruen":
					$this->team['Gruen'] = ($this->team['Gruen']-1);
					unset ($this->pteam[$name]);
					break;
				case "Gelb":
					$this->team['Gelb'] = ($this->team['Gelb']-1);
					unset ($this->pteam[$name]);
					break;
			}
			if(isset($this->hatflagge))
			{
				if($this->hatflagge == $name)
				{
					foreach($this->getServer()->getOnlinePlayers() as $player)
					{
						$player->sendMessage(MT::GOLD.'Caution: Diamond is back the middle!');
					}
					$coords = explode(",", $this->diamondspawn);
							$pos = new Vector3($coords[0], $coords[1], $coords[2]);
					$this->getServer()->getLevelbyName($this->arena1name)->dropItem($pos, Item::get(264));
					unset ($this->hatflagge);
				}
			}
			$this->onDebug(MT::AQUA.'erfolgreich ausgelogt');
		}
	}
	public function onPlayerInteract(PlayerInteractEvent $event)
	{
		$name = $event->getPlayer()->getName();
		$player = $event->getPlayer();
		$welt = $player->getLevel()->getName();
		$time = time();

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
		
			$i32 = explode(",", $sign[3]);
			if($sign[0] == '[CTD]' && $sign[1] == 'Team')
			{
				if(!$this->isrunning)
				{
					switch($i2)
					{
						case "Rot/Red":
							$farbe = "Rot";
							$this->onTeamselect($name, $farbe);
						break;
						case "Blau/Blue":
							$farbe = "Blau";
							$this->onTeamselect($name, $farbe);
						break;
						case "Gruen/Green":
							$farbe = "Gruen";
							$this->onTeamselect($name, $farbe);
						break;
						case "Gelb/Yellow":
							$farbe = "Gelb";
							$this->onTeamselect($name, $farbe);
						break;
						case "Reset":
							$farbe = "Reset";
							$this->onTeamselect($name, $farbe);
							break;
					}
				}
				else 
				{
					$player->sendMessage(MT::GOLD.'Round is running, please wait...');
				}
			}
		}
	}
	public function onRoundReset()
	{
		foreach($this->getServer()->getOnlinePlayers() as $player)
		{
			$name = $player->getName();
			$player->teleport($this->getServer()->getLevelbyName($this->lobbyname)->getSafeSpawn());
			$player->getInventory()->clearAll();
			$player->setHealth(20);
			$player->setGamemode(0);
			$player->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_SNEAKING, false);
			$player->setNameTag($player->getDisplayName());
			$this->onDebug(MT::GREEN.$name.' Reset Inv Health Gamemode Sneaking Nametag');
		}
		unset ($this->team);
		$this->onDebug(MT::GREEN.'unset Team');
		unset ($this->pteam);
		$this->onDebug(MT::GREEN.'unset Player Team');
		unset ($this->teilnehmer);
		$this->onDebug(MT::GREEN.'unset Teilnehmer');
		unset ($this->hatflagge);
		$this->onDebug(MT::GREEN.'unset hatFlagge');
		unset ($this->punkte);
		$this->onDebug(MT::GREEN.'unset Punkte');
		unset ($this->roundtime);
		$this->onDebug(MT::GREEN.'unset Roundtime');
		unset ($this->roundstarttime);
		$this->onDebug(MT::GREEN.'unset Roundstartime');
		
		$this->team['Rot'] = 0;
		$this->team['Blau'] = 0;
		$this->team['Gelb'] = 0;
		$this->team['Gruen'] = 0;
		$this->onDebug(MT::GREEN.'Team auf 0 gesetzt');
		$this->punkte['Rot'] = 0;
		$this->punkte['Blau'] = 0;
		$this->punkte['Gelb'] = 0;
		$this->punkte['Gruen'] = 0;
		$this->onDebug(MT::GREEN.'Punkte auf 0 gesetzt');
		$this->isrunning = false;
		$this->onDebug(MT::GREEN.'Running false');
	}
	public function onTeamselect($name, $farbe)
	{
		$player = $this->getServer()->getPlayer($name);
			switch($farbe)
			{
				case "Rot":
					
					if($this->team['Rot'] > $this->team['Blau'] + 1 || $this->team['Rot'] > $this->team['Gruen'] + 1  || $this->team['Rot'] > $this->team['Gelb'] + 1)
					{
						$player->sendMessage(MT::GOLD.'This choice is not fair bro...');
						$player->sendMessage(MT::GOLD.'Das ist keine faire Auswahl...');
					}
					else
					{
						if(!isset($this->pteam[$name]))
						{
							$this->onDebug(MT::BLUE.'Rot');
							$player->setNameTag(MT::RED.$player->getDisplayName());
							$this->team['Rot'] = ($this->team['Rot']+1);
							$this->pteam[$name] = "Rot";
							$player->sendMessage(MT::RED.'Your now in team red');
								
						}
						else
						{
							$player->sendMessage(MT::GOLD.'You have a team - reset your team first!');
						}
						
					}
					break;
				case "Blau":
					
					if($this->team['Blau'] > $this->team['Rot'] + 1 || $this->team['Blau'] > $this->team['Gruen'] + 1  || $this->team['Blau'] > $this->team['Gelb'] + 1)
					{
						
						$player->sendMessage(MT::GOLD.'This choice is not fair bro...');
						$player->sendMessage(MT::GOLD.'Das ist keine faire Auswahl...');
					}
					else
					{
						if(!isset($this->pteam[$name]))
						{
							$this->onDebug(MT::BLUE.'Blau');
							$player->setNameTag(MT::BLUE.$player->getDisplayName());
							$this->team['Blau'] = ($this->team['Blau']+1);
							$this->pteam[$name] = "Blau";
							$player->sendMessage(MT::BLUE.'Your now in team blue');
						}
						else
						{
							$player->sendMessage(MT::GOLD.'You have a team - reset your team first!');
						}
						
					}
					break;
				case "Gruen":
					
					if($this->team['Gruen'] > $this->team['Rot'] + 1 || $this->team['Gruen'] > $this->team['Blau'] + 1  || $this->team['Gruen'] > $this->team['Gelb'] + 1)
					{
						
						$player->sendMessage(MT::GOLD.'This choice is not fair bro...');
						$player->sendMessage(MT::GOLD.'Das ist keine faire Auswahl...');
					}
					else
					{
						if(!isset($this->pteam[$name]))
						{
							$this->onDebug(MT::BLUE.'Gruen');
							$player->setNameTag(MT::GREEN.$player->getDisplayName());
							$this->team['Gruen'] = ($this->team['Gruen']+1);
							$this->pteam[$name] = "Gruen";
							$player->sendMessage(MT::GREEN.'Your now in team green');
						}
						else
						{
							$player->sendMessage(MT::GOLD.'You have a team - reset your team first!');
						}
						
					}
					break;
				case "Gelb":
					
					if($this->team['Gelb'] > $this->team['Rot'] + 1 || $this->team['Gelb'] > $this->team['Blau'] + 1  || $this->team['Gelb'] > $this->team['Gruen'] + 1)
					{
					
						$player->sendMessage(MT::GOLD.'This choice is not fair bro...');
						$player->sendMessage(MT::GOLD.'Das ist keine faire Auswahl...');
						
					}
					else
					{
						if(!isset($this->pteam[$name]))
						{
							$this->onDebug(MT::BLUE.'Gelb');
							$player->setNameTag(MT::YELLOW.$player->getDisplayName());
							$this->team['Gelb'] = ($this->team['Gelb']+1);
							$this->pteam[$name] = "Gelb";
							$player->sendMessage(MT::YELLOW.'Your now in team yellow');
						}
						else
						{
							$player->sendMessage(MT::GOLD.'You have a team - reset your team first!');
						}
					}
					break;
				case "Reset":
					$player->setNameTag($player->getDisplayName());
					if(isset($this->pteam[$name]))
					{
						$this->onDebug(MT::BLUE.'Reset');
						switch($this->pteam[$name])
						{
							case "Rot":
								$this->team['Rot'] = ($this->team['Rot']-1);
								break;
							case "Blau":
								$this->team['Blau'] = ($this->team['Blau']-1);
								break;
							case "Gruen":
								$this->team['Gruen'] = ($this->team['Gruen']-1);
								break;
							case "Gelb":
								$this->team['Gelb'] = ($this->team['Gelb']-1);
								break;
						}
					}
					$player->sendMessage('Now you have no team');
					unset ($this->pteam[$name]);			
					break;
			}
	}
	public function onDebug($msg)
	{
		$var = 0;
		if($var == 0)$this->getLogger()->info($msg);
	}
 	public function onDisable()
 	{
		$this->getLogger()->info('SurvivalHive CTF unloaded!');
	}
}
