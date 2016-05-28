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
	public $tpa = array();
	
	public function onEnable()
	{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info(MT::AQUA."-=SH=-SimpleTPA Plugin loading...!");
	}
	
	public function onJoin(PlayerJoinEvent $event)
	{
		$name = $event->getPlayer()->getName();
		unset($this->tpa[array_search($this->tpa, $name)]);
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		if($sender instanceof Player){
			if(strtolower($command->getName()) == "tpa"){
				if(!isset($args[0])){
					$sender->sendMessage(MT::GREEN . "/tpa [Spielername]");
				}
				if(isset($args[0])){
					$spieler = $this->getServer()->getPlayer($args[0]);
					if($spieler == null){
						$sender->sendMessage(MT::RED . "Der Spieler ist nicht online");
					}
					if($spieler !== null && $spieler->isOnline()){//Es reicht auch nur eine der 2 Optionen
						$sender->sendMessage(MT::GREEN . "Teleport Anfrage an " . MT::RED . $spieler->getName() . MT::GREEN . " gesendet!");
						$this->tpa[$spieler->getName()] = $sender->getName();
					}
				}
			}
			if(strtolower($command->getName()) == "tpja"){
				if(!isset($args[0])){
					$sender->sendMessage(MT::RED . "/tpja [Spieler]");
				}
				if(isset($args[0])){
					$spieler = $this->getServer()->getPlayer($args[0]);
					if($this->tpa[$spieler->getName()] == $sender->getName()){
						$sender->sendMessage(MT::GREEN . "Du wurdest zu " . MT::RED . $spieler->getName() . MT::GREEN . " teleportiert!");
						$spieler->sendMessage(MT::RED . $sender->getName() . MT::GREEN . " hat die Anfrage akzeptiert!");
						$sender->teleport(new Position($spieler->getX(), $spieler->getY(), $spieler->getZ()));
					}
					if($this->tpa[$spieler->getName()] !== $sender->getName()){
						$sender->sendMessage(MT::RED . "Der Spieler hat dir keine Anfrage geschickt!");
					}
				}
			}
			if(strtolower($command->getName()) == "tpnein"){
				$spieler = $this->getServer()->getPlayer($args[0]);
				if(!isset($args[0])){
					$sender->sendMessage(MT::RED . "/tpnein [Spieler]");
				}
				if(isset($args[0])){
					if($this->tpa[$spieler->getName()] == $sender->getName()){
						$sender->sendMessage(MT::GREEN . "Du hast die Anfrage von " . MT::RED . $spieler->getName() . MT::GREEN . " abgelehnt!");
						unset($this->tpa[array_search($this->tpa, $spieler->getName())]);
						$spieler->sendMessage(MT::RED . $sender->getName() . MT::GREEN . " hat die ANfrage abgelehnt!");
					}
					if($this->tpa[$spieler->getName()] !== $sender->getName()){
						$sender->sendMessage(MT::RED . "Der Spieler hat dir keine Anfrage geschickt!");
					}
					
				}
			}
		}
	}
   
    public function onDisable()
    {
    	$this->getLogger()->info(MT::AQUA."Plugin unloaded!");
    	unset ($this->tpa);
    }
}
