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
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\math\Vector3;
use pocketmine\level\Position;

	class awayhandler extends PluginBase implements Listener
	
	{
		public $var;
		
		public function onEnable()
		{
			$this->getLogger()->info("SurvivalHive Lobbyhandler loaded!");
			if (!file_exists($this->getDataFolder()))
			{
				@mkdir($this->getDataFolder(), true);
			}
			$this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML, array("Server1" => "","Server1Port" => "","Server2" => "","Server2Port" => "", "Server3" => "","Server3Port" => "","Server4" => "","Server4Port" => ""));
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->var = 0;
		}
		
		public function onPlayerJoinEvent (PlayerJoinEvent $event)
		{
			$name = strtolower($event->getPlayer()->getName());
			switch($this->var)
			{
				case 0:
					$pl = $event->getPlayer();
					$addr1 = $this->config->get("Server1");
					$addr2 = $this->config->get("Server1Port");
					
					if($addr1 == "" && $addr2 == ""){return;}

					$ft = $this->getServer()->getPluginManager()->getPlugin("FastTransfer");
					if (!$ft) {
						$this->getLogger()->info("FAST TRANSFER NOT INSTALLED");
						return;
					}
					$this->getLogger()->info("0- Player:  ".$pl->getName()." => ".
							$addr1.":".$addr2);
					$ft->transferPlayer($pl,$addr1,$addr2);
					$this->var = 1;
					return;
					break;
				case 1:
					$pl = $event->getPlayer();
					$addr1 = $this->config->get("Server2");
					$addr2 = $this->config->get("Server2Port");
					
					if($addr1 == "" && $addr2 == ""){return;}
						
					$ft = $this->getServer()->getPluginManager()->getPlugin("FastTransfer");
					if (!$ft) {
						$this->getLogger()->info("FAST TRANSFER NOT INSTALLED");
						return;
					}
					$this->getLogger()->info("1- Player:  ".$pl->getName()." => ".
							$addr1.":".$addr2);
					$ft->transferPlayer($pl,$addr1,$addr2);
					$this->var = 2;
					return;
					break;
				case 2:
					$pl = $event->getPlayer();
					$addr1 = $this->config->get("Server3");
					$addr2 = $this->config->get("Server3Port");
						
					if($addr1 == "" && $addr2 == ""){return;}
					
					$ft = $this->getServer()->getPluginManager()->getPlugin("FastTransfer");
					if (!$ft) {
						$this->getLogger()->info("FAST TRANSFER NOT INSTALLED");
						return;
					}
					$this->getLogger()->info("2- Player:  ".$pl->getName()." => ".
							$addr1.":".$addr2);
					$ft->transferPlayer($pl,$addr1,$addr2);
					$this->var = 3;
					return;
					break;
				case 3:
					$pl = $event->getPlayer();
					$addr1 = $this->config->get("Server4");
					$addr2 = $this->config->get("Server4Port");
					
					if($addr1 == "" && $addr2 == ""){return;}
						
					$ft = $this->getServer()->getPluginManager()->getPlugin("FastTransfer");
					if (!$ft) {
						$this->getLogger()->info("FAST TRANSFER NOT INSTALLED");
						return;
					}
					$this->getLogger()->info("3- Player:  ".$pl->getName()." => ".
							$addr1.":".$addr2);
					$ft->transferPlayer($pl,$addr1,$addr2);
					$this->var = 0;
					return;
					break;
			}
		}

	
		public function onDisable()
		{
			$this->getLogger()->info("SurvivalHive Lobbyhandler unloaded!");
		}
	}