<?php
namespace main;

use pocketmine\utils\TextFormat as MT;
use pocketmine\permission\Permission;
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
			$this->getLogger()->info(MT::AQUA.'Plugin -=SH=-Nicks loading...!');

			$this->saveDefaultConfig();
			$cfg = $this->getConfig();
			
			$this->nicks = $cfg->get('Nicknames');
			$this->permissions = $cfg->get('Permissions');
		}
		
		public function onDisable()
		{
			$this->getLogger()->info(MT::AQUA.'Plugin unloaded!');			
		}

		public function onCommand(CommandSender $p, Command $command, $label, array $args)
		{
			if($p instanceof Player) 
			{
				if(strtolower($command->getName()) == 'shnick')
				{
					if($this->permissions == true)
					{
						if($p->isOp() || $p->hasPermission('survivalhive.nicks'))
						{		
							$this->onSchalter($p);
						}
						else
						{
							$p->sendMessage(MT::RED."You dont have the permissions to use this command!");
							return true;
						}
					}
					else
					{
						$this->onSchalter($p);
					}
				}
			}
			else 
			{
				$p->sendMessage("Nur im Spiel moeglich");
				return true;
			}
		}
		
		public function onSchalter($p)
		{
			$id = $p->getID();
			$name = strtolower($p->getName());
				
			if (! (in_array($id, $this->schalter)))
			{
				$this->schalter[$name] = $id;
				$p->sendMessage(MT::GREEN."-=SH=-Nick Eingeschaltet");
				return true;
			}
			else
			{
				$index = array_search($id, $this->schalter);
				unset($this->schalter[$index]);
				$p->sendMessage(MT::GREEN."-=SH=-Nick Ausgeschaltet");
				return true;
			}
		}
		
		public function playerBlockTouch(PlayerInteractEvent $event)
		{
			$p = $event->getPlayer();
			$id = $event->getPlayer()->getID();
			$itemid = $event->getItem()->getID();
		
			if(in_array($id, $this->schalter))
			{
				if($itemid == 50)
				{
					$name = $event->getPlayer()->getName();
					$this->onNickchange($p, $name);
				}
				else
				{
					$anzahldernicks = count($this->nicks)-1;
					$rand = mt_rand(0, $anzahldernicks);
					$randnickname = $this->nicks[$rand];
					$this->onNickchange($p, $randnickname);
				}
			}	
		}

		public function onNickchange($p, $nick)
		{
			$p->setNameTag("$nick");
			$p->sendMessage("$nick");
			$p->setDisplayName("$nick");
			return true;
		}
	}
