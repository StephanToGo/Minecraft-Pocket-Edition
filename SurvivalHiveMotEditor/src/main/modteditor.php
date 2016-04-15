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
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;

	class modteditor extends PluginBase implements Listener
	{
		public function onEnable()
		{
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->getLogger()->info(MT::AQUA."Plugin -=SH=-Modteditor loading...!");

			if (!file_exists($this->getDataFolder()))
			{
				@mkdir($this->getDataFolder(), true);
			}
			$this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML, [
					"Modt" => "{G}My {R}new {O}server {A}RUNS",
			]);
			
			$this->motd = $this->cfg->get("Motd");
			
			$motd = str_replace('{G}', MT::GREEN, $this->motd);
			$motd = str_replace('{R}', MT::RED, $motd);
			$motd = str_replace('{O}', MT::GOLD, $motd);
			$motd = str_replace('{A}', MT::AQUA, $motd);
			$this->getServer()->getNetwork()->setName($motd);
		}
	
		public function onDisable()
		{
			$this->getLogger()->info(MT::AQUA."Plugin unloaded!");			
		}
	}