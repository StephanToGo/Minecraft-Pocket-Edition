<?php
namespace main;

use pocketmine\permission\Permission;
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
			$this->getLogger()->info(MT::AQUA.'Plugin -=SH=-Nicks loading...!');
			if (!file_exists($this->getDataFolder())){@mkdir($this->getDataFolder(), true);}
			$this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML, array("Nicknames" => [
																												'Hans',
																												'Peter',
																												'Karl-Heinz',
																												'Ingeborg',
																												'Willy',
																												'Selma',
																												'MegaMan',
																												'Son-Goku',
																												'Flash Gordon',
																												'Gordon Freeman',
																												'Wurstbrot',
																												'Kaesesuppe',
																												'Backfisch',
																												'Kraeuterquark',
																												'Salatgurke',
																												'Petrolium'],
																								'Permissions' => true) 
																								);	
			$this->nicks = $this->config->get('Nicknames');
			$this->permissions = $this->config->get('Permissions');
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
							return true;
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
			$id = $event->getPlayer()->getID();
			$itemid = $event->getItem()->getID();
		
			if(in_array($id, $this->schalter))
			{
				if($itemid == 50 && isset($this->namesave[$id]))
				{
					$name = $event->getPlayer()->getName();
					$event->getPlayer()->setNameTag("$name");
					$event->getPlayer()->sendMessage("$name");
					return true;
				}
				$anzahldernicks = count($this->nicks)-1;
				$rand = mt_rand(0, $anzahldernicks);
				$randnickname = $this->nicks[$rand];
				$event->getPlayer()->sendMessage("$randnickname");
				$event->getPlayer()->setNameTag("$randnickname");	
				return true;
			}	
		}
	}
