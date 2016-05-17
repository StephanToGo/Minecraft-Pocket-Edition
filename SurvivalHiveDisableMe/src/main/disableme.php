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

	class disableme extends PluginBase implements Listener
	{
		public function onEnable()
		{
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->getLogger()->info(MT::AQUA."Plugin -=SH=-DisableMe loading...!");

			if (!file_exists($this->getDataFolder())){@mkdir($this->getDataFolder(), true);}
			$this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML, array("Commands" => [], "Permissions" => true));
			
			$this->commands = $this->config->get("Commands");
			$this->permissions = $this->config->get('Permissions');
		}
		
		public function onDisable()
		{
			$this->getLogger()->info(MT::AQUA."Plugin unloaded!");			
		}

		public function onPlayerCommand(PlayerCommandPreprocessEvent $event)
		{
			$message = $event->getMessage();
			$name = strtolower($event->getPLayer()->getName());
			if($message{0} === "/" && !$event->getPlayer()->isOP())
			{ 
				$command = substr($message, 1);
				$args = explode(" ", $command);

				foreach($this->commands as $command)
				{
					if(strtolower($args[0]) === "$command")
					{
						$event->getPlayer()->sendMessage(MT::RED."This command is blocked!");
						$event->setCancelled(true);
					}
				}
			}
		}
	}
