<?php
namespace main;

use pocketmine\utils\TextFormat as MT;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;
use pocketmine\network\Network;
use pocketmine\network\protocol\BatchPacket;
use pocketmine\network\protocol\CraftingDataPacket;
use pocketmine\network\protocol\DataPacket;
use pocketmine\network\protocol\PlayerListPacket;
use pocketmine\network\query\QueryHandler;
use pocketmine\network\RakLibInterface;
use pocketmine\network\rcon\RCON;
use pocketmine\network\upnp\UPnP;
use pocketmine\network\CompressBatchedTask;

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
			
			$this->motd = $this->cfg->get("Modt");
			
			$motd = str_replace('{G}', MT::GREEN, $this->motd);
			$motd = str_replace('{R}', MT::RED, $motd);
			$motd = str_replace('{O}', MT::GOLD, $motd);
			$motd = str_replace('{A}', MT::AQUA, $motd);

			$test = $this->getServer()->getNetwork()->getName();
			$this->getLogger()->info(MT::AQUA."$test");	
			$this->getServer()->getNetwork()->setName($motd);
			$test = $this->getServer()->getNetwork()->getName();
			$this->getLogger()->info(MT::AQUA."$test");
		}
	
		public function onDisable()
		{
			$this->getLogger()->info(MT::AQUA."Plugin unloaded!");			
		}
	}