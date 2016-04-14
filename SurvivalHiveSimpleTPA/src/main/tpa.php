<?php

namespace main;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\Timings;
use pocketmine\level\format\FullChunk;
use pocketmine\level\format\LevelProvider;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\math\AxisAlignedBB;
use pocketmine\plugin\Plugin;
use pocketmine\utils\ReversePriorityQueue;
use pocketmine\utils\TextFormat as MT;

class tpa extends PluginBase implements Listener 
{
	public function onEnable()
	{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info(MT::AQUA."-=SH=-SimpleTPA Plugin loading...!");
	}
	
	public function onJoin(PlayerJoinEvent $event)
	{
		$name = $event->getPlayer()->getName();
		$this->tpa[$name] = null;
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) 
	{
		if($sender instanceof Player)
		{
			if(strtolower($command->getName()) == "tpa")
			{
				if(isset($args[0]))
				{
					$name = strtolower($sender->getPlayer()->getName());
					
					if($target = $sender->getServer()->getPlayer($args[0]))
					{
						$targetname = strtolower($sender->getServer()->getPlayer($args[0])->getName());
						
						$sender->getServer()->getPlayer($args[0])->sendMessage(MT::GREEN."$name hat Ihnen eine TP anfrage gesendet | $name has sent you a tpa request");
						$sender->getServer()->getPlayer($args[0])->sendMessage(MT::YELLOW."/tpja zum bestaetigen /tpnein zum ablehnen | /tpja for accept /tpnein for decline");
						
						$sender->sendMessage(MT::GREEN."Tpa Anfrage wurde gesendet! || Tpa request has been sent!");
						
						$this->tpa[$targetname] = $name;
						return true;
					}
					else
					{
						$sender->sendMessage(MT::RED."Spieler exestiert nicht in dieser Welt || Player dont exist in this world");
						return true;
					}
				}
				else
				{
					$sender->sendMessage(MT::RED."Bitte Spieler Namen angeben || Missing player name");
					return true;
				}
			}
			
			if(strtolower($command->getName()) == "tpja")
			{
				$name = strtolower($sender->getName());
				
				if($this->tpa[$name] != null)
				{
					$ziel = $this->tpa[$name];
					$target = $sender->getServer()->getPlayer($ziel);
					
					$pos = new Position($sender->x, $sender->y, $sender->z, $sender->getLevel());
					$target->teleport($pos);
					
					$sender->getServer()->getPlayer($ziel)->sendMessage(MT::GREEN."Tpa Anfrage bestaetigt | Tpa request accepted");
					$sender->sendMessage(MT::GREEN."Tpa Anfrage bestaetigt | Tpa request accepted");
					
					$this->tpa[$name] = null;
					return true;
				}
				return true;
			}
			
			if(strtolower($command->getName()) == "tpnein")
			{
				$name = strtolower($sender->getName());
			
				if($this->tpa[$name] != null)
				{
				
					$ziel = $this->tpa[$name];
					
					$sender->getServer()->getPlayer($ziel)->sendMessage(MT::RED."Tpa Anfrage abgelehnt | Tpa request declined");
					$sender->sendMessage("Tpa Anfrage abgelehnt | Tpa request declined");
					
					$this->tpa[$name] = null;
					return true;
				}	
			}
		}
		else
		{
			$sender->sendMessage("Nur im Spiel moeglich | Only in game");
			return true;
		}
		break;
	}
   
    public function onDisable()
    {
    	$this->getLogger()->info(MT::AQUA."Plugin unloaded!");
    	unset ($this->tpa);
    }
}