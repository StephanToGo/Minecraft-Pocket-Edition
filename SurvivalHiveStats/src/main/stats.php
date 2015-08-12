<?php

namespace main;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;
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
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\Timings;
use pocketmine\item\Item;
use pocketmine\level\format\FullChunk;
use pocketmine\level\format\LevelProvider;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\plugin\Plugin;
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
		
		$this->mysqli = mysqli_connect("$this->serverip", "$this->user", "$this->password", "$this->database");
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
			$database = $this->database;
			$tname = $this->tablename;
			$join = $this->tablejoin;
			$kill = $this->tablekill;
			$death = $this->tabledeath;
			
			$name = strtolower($sender->getName());
  			$sql = "SELECT `$name`,`$join`,`$kill`,`$death` FROM `$database` WHERE `$tname` = `$name`";
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