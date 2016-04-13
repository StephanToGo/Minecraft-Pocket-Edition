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
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\math\Vector3;

	class hsensor extends PluginBase implements Listener
	
	{
		public function onEnable()
		{
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
		}
	
		public function onPlayerJoinEvent(PlayerJoinEvent $event)
		{
			$ip = $event->getPlayer()->getAddress();
			$query = @unserialize(file_get_contents('http://ip-api.com/php/'.$ip));
			if($query && $query['status'] == 'success') {
				$this->getLogger()->info($query['country']);
			} else {
					$this->getLogger()->info("Nicht moeglich");
			}	
		}
		
		public function onDisable()
		{
			$this->getLogger()->info("Plugin unloaded!");
		}
	}