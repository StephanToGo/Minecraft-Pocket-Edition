<?php
namespace main;

use pocketmine\utils\TextFormat as MT;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\level;
use pocketmine\item\Item;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;

	class disableme extends PluginBase implements Listener
	{
		public function onEnable()
		{
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->getLogger()->info(MT::AQUA."Plugin -=SH=-DisableMe loading...!");
			$this->getServer()->getScheduler()->scheduleRepeatingTask(new task($this), 15);

			if (!file_exists($this->getDataFolder())){@mkdir($this->getDataFolder(), true);}
			$this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML, array("Commands" => []));
			
			$this->commands = $this->config->get("Commands");
		}
		
		public function onDisable()
		{
			$this->getLogger()->info(MT::AQUA."Plugin unloaded!");			
		}

		public function onPlayerCommand(PlayerCommandPreprocessEvent $event)
		{
			$message = $event->getMessage();
			$name = strtolower($event->getPLayer()->getName());
			if($message{0} === "/")
			{ 
				$command = substr($message, 1);
				$args = explode(" ", $command);

				foreach($this->commands as $command)

				if($args[0] === "$command")
				{
					$event->getPlayer()->sendMessage(MT::RED."This command is blocked!");
					$event->setCancelled(true);
				}
			}
		}
	}

class task1 extends PluginTask
{
	public function __construct(Plugin $owner)
	{
		parent::__construct($owner);
	}
	public function onRun($currentTick)
	{

	}
}
