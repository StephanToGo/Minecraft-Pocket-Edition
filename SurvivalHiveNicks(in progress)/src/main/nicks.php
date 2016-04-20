<?php
namespace main;

use pocketmine\utils\TextFormat as MT;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;
use pocketmine\event\player\PlayerInteractEvent;

	class nicks extends PluginBase implements Listener
	{
		public $schalter = array();
		public $namesave = array();
		
		public function onEnable()
		{
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->getLogger()->info(MT::AQUA."Plugin -=SH=-Nicks loading...!");

			if (!file_exists($this->getDataFolder())){@mkdir($this->getDataFolder(), true);}
			$this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML, array("Nicknames" => ['Hans','Peter']));
			
			$this->nicks = $this->config->get("Nicknames");
		}
		
		public function onDisable()
		{
			$this->getLogger()->info(MT::AQUA."Plugin unloaded!");			
		}

		public function onCommand(CommandSender $p, Command $command, $label, array $args)
		{
			if($p instanceof Player) 
			{
				if(strtolower($command->getName()) == "shnick")
				{
					$id = $p->getID();
					$name = strtolower($p->getName());
				
					if (! (in_array($id, $this->schalter)))
					{
						$this->schalter[$name] = $id;
						$p->sendMessage(MT::GREEN."SHNick Eingeschaltet");
						return true;
					}
					else
					{
						$index = array_search($id, $this->schalter);
						unset($this->schalter[$index]);
						$p->sendMessage(MT::GREEN."SHNick Ausgeschaltet");
						return true;
					}
				}
			}
			else 
			{
				$p->sendMessage("Nur im Spiel moeglich");
				return true;
			}
		}
		public function playerBlockTouch(PlayerInteractEvent $event)
		{
			$id = $event->getPlayer()->getID();
			$itemid = $event->getItem()->getID();
		
			if(in_array($id, $this->schalter))
			{
				if($itemid == 50 && isset($this->namesave[$id]))
				{
					$name = $event_>getPlayer()->getName();
					$event->getPlayer()->setNameTag("$name");
					$event->getPlayer()->sendMessage("$name");
					return true;
				}
				
				if(!isset($this->namesave[$id])){$this->namesave[$id] = $event->getPlayer()->getName();}
				$anzahldernicks = count($this->nicks)-1;
				$rand = mt_rand(0, $anzahldernicks);
				$randnickname = $this->nicks[$rand];
				$event->getPlayer()->sendMessage("$randnickname");
				$event->getPlayer()->setNameTag("$randnickname");	
				return true;
			}	
		}
	}
