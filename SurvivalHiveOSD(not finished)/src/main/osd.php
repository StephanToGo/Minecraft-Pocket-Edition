<?php
namespace main;

use pocketmine\utils\TextFormat as MT;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\level;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;

	class osd extends PluginBase implements Listener
	{
		public function onEnable()
		{
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->getLogger()->info(MT::AQUA."Plugin -=SH=-OSD loading...!");
			$this->getServer()->getScheduler()->scheduleRepeatingTask(new osdtask($this), 15);

			$test = date_default_timezone_set("Europe/Belgrade");
		}
		
		public function onDisable()
		{
			$this->getLogger()->info(MT::AQUA."Plugin unloaded!");			
		}

		public function onOSD()
		{
			$zeit = date("h:i");
			$leer = "\n\n\n\n\n\n\n";
			
			foreach($this->getServer()->getOnlinePlayers() as $p)
			{
				$p->sendTip(MT::AQUA."$leer$zeit");
			}
		}
	}

class osdtask extends PluginTask
{
	public function __construct(Plugin $owner)
	{
		parent::__construct($owner);
	}
	public function onRun($currentTick)
	{
		$this->getOwner()->onOSD();
	}
}
