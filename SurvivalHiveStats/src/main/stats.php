<?php

namespace main;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
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
use pocketmine\event\entity\EntityMoveEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\EntityTeleportEvent;
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
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerMoveEvent;
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
use pocketmine\utils\TextFormat;

class stats extends PluginBase implements Listener 
{
	public $mysqli;
	
	public $serverip;
	public $user;
	public $password;
	public $database;
	public $tablename;
	public $columname;
	public $columjoins;
	public $columdeaths;
	public $columkills;
	
	public function onEnable()
	{
		$this->getLogger()->info("SurvivalHive Stats loaded!");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if (!file_exists($this->getDataFolder()))
		{
			@mkdir($this->getDataFolder(), true);
		}
		$this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML, [
				"ServerIP" => "127.0.0.1",
				"user" => "root",
				"password" => "root",
				"databasename" => "stats",
				"tablename" => "stats",
				"columname" => "name",
				"columjoins" => "join",
				"columdeaths" => "death",
				"columkills" => "kill",
		]);
		$this->serverip = $this->cfg->get("ServerIP");
		$this->user = $this->cfg->get("user");
		$this->password = $this->cfg->get("password");
		$this->database = $this->cfg->get("databasename");
		$this->tablename = $this->cfg->get("tablename");
		$this->columname = $this->cfg->get("columname");
		$this->columjoins = $this->cfg->get("columjoins");
		$this->columdeaths = $this->cfg->get("columdeaths");
		$this->columkills = $this->cfg->get("columkills");
		
		$link = mysqli_connect("$this->serverip", "$this->user", "$this->password");
		$sql = "CREATE DATABASE IF NOT EXISTS `$this->database`";
		$db_selected = mysqli_query($link,$sql);
		
		$this->mysqli = mysqli_connect("$this->serverip", "$this->user", "$this->password", "$this->database");
		
		$sql2 = "CREATE TABLE IF NOT EXISTS `$this->tablename` (
                          `$this->columname` varchar(255) NOT NULL,
                          `$this->columkills` int(11) NOT NULL,
                          `$this->columdeaths` int(11) NOT NULL,
                          `$this->columjoins` int(11) NOT NULL,
                          PRIMARY KEY  ($this->columname)
                          )";
		$db_selected = mysqli_query($this->mysqli,$sql2);
	}
	
	public function onPlayerJoinEvent(PlayerJoinEvent $event)
	{
		$player = $event->getPlayer();
		if ($player instanceof Player)
		{
			$name = strtolower($event->getPlayer()->getName());
			
			$database = $this->tablename;
			$tname = $this->columname;
			$join = $this->columjoins;
			
	 		$sql = "INSERT INTO `$database` (`$tname`) VALUES (?)";
	 		$eintrag = $this->mysqli->prepare( $sql );
	 		$eintrag->bind_param( 's',$name);
	 		$eintrag->execute();
	
	 		$sql = "UPDATE `$database` SET `$join` = `$join`+1 WHERE `$tname` = ?";
	 		$eintrag = $this->mysqli->prepare( $sql );
	 		$eintrag->bind_param('s', $name);
	 		$eintrag->execute();
  		}						
	}

	public function onPlayerDeathEvent(PlayerDeathEvent $event)
	{
		$player = $event->getEntity();
		$name = strtolower($player->getName());
	
		if ($player instanceof Player)
		{
			$database = $this->tablename;
			$tname = $this->columname;
			$kill = $this->columkills;
			$death = $this->columdeaths;
			
			$sql = "INSERT INTO `$database` (`$tname`) VALUES (?)";
			$eintrag = $this->mysqli->prepare( $sql );
			$eintrag->bind_param( 's', $name);
			$eintrag->execute();
	
			$sql = "UPDATE `$database` SET `$death` = `$death`+1 WHERE `$tname` = ?";
			$eintrag = $this->mysqli->prepare( $sql );
			$eintrag->bind_param('s', $name);
			$eintrag->execute();
	
			$cause = $player->getLastDamageCause();
	
			if($cause instanceof EntityDamageByEntityEvent)
			{
				$damager = $cause->getDamager();
				if($damager instanceof Player)
				{
					$name = strtolower($damager->getName());
					$sql = "INSERT INTO `$database` (`$tname`) VALUES (?)";
					$eintrag = $this->mysqli->prepare( $sql );
					$eintrag->bind_param( 's', $name);
					$eintrag->execute();
	
					$sql = "UPDATE `$database` SET `$kill` = `$kill`+1 WHERE `$tname` = ?";
					$eintrag = $this->mysqli->prepare( $sql );
					$eintrag->bind_param('s', $name);
					$eintrag->execute();
				}
			}
		}
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) 
	{
		if(!($sender instanceof Player)) 
		{
			$sender->sendMessage("Nur im Spiel moeglich");
			return true;
		}
		if(strtolower($command->getName()) == "stats") 
		{
			$database = $this->tablename;
			$tname = $this->columname;
			$join = $this->columjoins;
			$kill = $this->columkills;
			$death = $this->columdeaths;
			
			$name = strtolower($sender->getName());
  			$sql = "SELECT `$tname`,`$join`,`$kill`,`$death` FROM `$database` WHERE `$tname` = '$name'";
  			$result = $this->mysqli->query($sql);
  			$row = mysqli_fetch_row($result);
  			$sender->sendMessage("Player: " . $row[0] . " Joins: " . $row[1] . " Kills: " . $row[2] . " Death: " . $row[3] );
  			return true;
		}	
	}
	
   
   
    public function onDisable()
    {
    	$this->getLogger()->info("SurvivalHive Stats unloaded!");
    	$this->mysqli->close();
    }
}