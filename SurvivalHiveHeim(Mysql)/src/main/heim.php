<?php

namespace main;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as MT;

class heim extends PluginBase implements Listener 
{
	public $mysqli;
	
	public function onEnable()
	{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info(MT::AQUA."Plugin -=SH=-Heim loading...!");
		if (!file_exists($this->getDataFolder()))
			{
				@mkdir($this->getDataFolder(), true);
			}
		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, [
				"MySQL Server IP" => "127.0.0.1",
				"MySQL Benutzer" => "root",
				"MySQL Passwort" => "",
				"MySQL Datenbank" => "Pocketmine",
				"Maximale Heim Punkte" => "3",
			]);
		
		$server = $this->config->get("MySQL Server IP");
		$benutzer = $this->config->get("MySQL Benutzer");
		$passwort = $this->config->get("MySQL Passwort");
		$datenbank = $this->config->get("MySQL Datenbank");
		$heimanzahl = $this->config->get("Maximale Heim Punkte");
		
		$this->mysqli = mysqli_connect("$server","$benutzer","$passwort","$datenbank");	
		$sql = "CREATE TABLE heimpunkte (
				id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				name VARCHAR(30) NOT NULL,
				heim VARCHAR(30) NOT NULL,
				coord VARCHAR(50) NOT NULL,
				welt VARCHAR(50) NOT NULL
				)";
		$eintrag = $this->mysqli->prepare( $sql );
		$eintrag->execute();
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) 
	{
		if($sender instanceof Player)
		{
			if(strtolower($command->getName()) == "setheim")
			{
				if(isset($args[0]))
				{
					$name = strtolower($sender->getName());
				
					$p = $sender->getPlayer();
					$welt = $sender->getPlayer()->getLevel()->getName();
					$this->pos[$name] = new Vector3($p->getX(),$p->getY(),$p->getZ());
				
					$x = round($this->pos[$name]->getX());
					$y = round($this->pos[$name]->getY());
					$z = round($this->pos[$name]->getZ());
					$coords = "$x,$y,$z";
				
					$zaehler = 0;
				
					$sql = "SELECT heim,coords,welt FROM heimpunkte WHERE name = '$name'";
					$result = $this->mysqli->query($sql);
					
					if ($result != false)
					{
						while ($row = mysqli_fetch_row($result))
						{
							$zaehler++;
							if($args[0] == $row[0])
							{
								$sender->sendMessage("Heim name besteht bereits");
								$sender->sendMessage("Heim name exist");
								return true;
							}
						}
					
						if($zaehler > $heimanzahl)
						{
							$sender->sendMessage("Du hast das maximum erreicht");
							$sender->sendMessage("Maximum reached");
							return true;
						}
					}

					$sql = "INSERT INTO heimpunkte (name, heim, coord, welt) VALUES ('$name','$args[0]','$coords','$welt')";
					$eintrag = $this->mysqli->prepare( $sql );
					$eintrag->execute();

					$sender->sendMessage("Heim $args[0] erfolgreich gesetzt");
					$sender->sendMessage("Heim $args[0] accepted");
				
					return true;
				}
				else
				{
					$sender->sendMessage("Bitte Heim Namen angeben");
					$sender->sendMessage("Give the Heim a name");
					return true;
				}
			}
			if(strtolower($command->getName()) == "heim")
			{
				$welt = $sender->getPlayer()->getLevel()->getName();
				
				if(isset($args[0]))
				{
					$name = strtolower($sender->getName());
					$sql = "SELECT coord,welt FROM heimpunkte WHERE name = '$name' AND heim = '$args[0]'";
					$result = $this->mysqli->query($sql);
					
					if($row = mysqli_fetch_row($result))
					{
						if($welt == $row[1])
						{
							$exCoord = explode(',', $row[0]);
							$x = $exCoord[0];
							$y = $exCoord[1];
							$z = $exCoord[2];
							$welt = $row[1];
							
							$sender->teleport(Server::getInstance()->getLevelByName($welt)->getSafeSpawn(new Position($x, $y, $z)));
							$sender->sendMessage("Erfolgreich zu $args[0] teleportiert");
						}
						else
						{
							$sender->sendMessage("Du bist nicht in der richtigen Welt!\nBitte geh in die Welt $row[3]");
						}
					}
					return true;
				}
				else
				{	
					$name = strtolower($sender->getName());
					$sql = "SELECT heim FROM heimpunkte WHERE name = '$name'";
					$result = $this->mysqli->query($sql);
					$sender->sendMessage("Meine Heimpunkte");
					if ($result != false)
					{
						while ($row = mysqli_fetch_row($result))
						{
							$sender->sendMessage("$row[0]");
						}
					}
					return true;
				}
			}
			if(strtolower($command->getName()) == "delheim")
			{
				if(isset($args[0]))
				{
					$name = strtolower($sender->getName());
			
					$p = $sender->getPlayer();

					$sql = "DELETE FROM heimpunkte WHERE name = '$name' AND heim = '$args[0]'";
					$eintrag = $this->mysqli->prepare( $sql );
					$eintrag->execute();
					$sender->sendMessage("Heim $args[0] erfolgreich geloescht");
					return true;
				}
				else
				{
					$sender->sendMessage("Bitte Heim Namen angeben");
					return true;
				}
			}
		}
		else
		{
			$sender->sendMessage("Nur im Spiel moeglich");
			return true;
		}
	}
   
    public function onDisable()
    {
    	$this->getLogger()->info(MT::AQUA."Plugin unloaded!");
    	$this->mysqli->close();
    }
}

