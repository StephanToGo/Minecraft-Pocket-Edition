<?php
namespace main;

use pocketmine\utils\TextFormat as MT;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\server;
use pocketmine\level;
use pocketmine\item\Item;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\math\Vector3;

	class kickban extends PluginBase implements Listener
	
	{
		public function onEnable()
		{
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->getLogger()->info(MT::AQUA."-=SH=-KickBan loading...!");
		}
	
		public function onCommand(CommandSender $sender, Command $command, $label, array $args)
		{
			if($p instanceof Player)
			{
				if(strtolower($command->getName()) == "shban")
				{
					if(isset($args[0]))
					{
					
					}
					else
					{
						$sender->sendMessage(MT::RED."bitte Spielernamen eingeben");
					}
				}
				if(strtolower($command->getName()) == "shkick")
				{
					if(isset($args[0]) && isset($args[1]))
					{
						$p = array_shift($args);
                                                $player = $this->getServer()->getPlayer($p);
                                                if($player !== null && $player->isOnline()){
                                                	$sender->sendMessage(MT::RED"Du hast " . $player . " erfolgreich gekickt!");
                                                	$this->getServer()->getPlayer($player)->kick($args[1] . $args[2] . $args[3] . $args[5]);
                                                }
                                                if($player == null){
                                                	$sender->sendMessage(MT::RED"Der Spieler ist offline!");
                                                }
					}
					else
					{
						$sender->sendMessage(MT::RED."/shkick [Name] [Grund]");
					}
				}
			}
		}
			
		public function onDisable()
		{
			$this->getLogger()->info(MT::AQUA."Plugin unloaded!");
		}
	}
