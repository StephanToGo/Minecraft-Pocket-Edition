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
use pocketmine\event\player\PlayerDeathEvent;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;

	class moneyforkill extends PluginBase implements Listener
	
	{
		public function onEnable()
		{
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->getLogger()->info(MT::AQUA."Plugin -=SH=-Money4Kill loading...!");
			$this->api = EconomyAPI::getInstance();
			
			if (!file_exists($this->getDataFolder()))
			{
				@mkdir($this->getDataFolder(), true);
			}
			$this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML, array("BetragKiller" => "10","BetragLoser"=> "10"));	
		}
	
		public function onPlayerDeathEvent(PlayerDeathEvent $event)
		{
			$player = $event->getEntity();
			$name = strtolower($player->getName());
		
			if ($player instanceof Player)
			{
				$cause = $player->getLastDamageCause();
		
				if($cause instanceof EntityDamageByEntityEvent)
				{
					$damager = $cause->getDamager();
					
					if($damager instanceof Player)
					{
						$BetragKiller = $this->config->get("BetragKiller");
						$BetragLoser = $this->config->get("BetragLoser");
						$damager->sendTip(MT::GOLD."get $BetragKiller $");
						$player->sendTip(MT::GOLD."lose $BetragLoser $");
						$this->api->addMoney($damager, $BetragKiller);
						$this->api->reduceMoney($player, $BetragLoser);
					}
				}
			}
		}
		
		public function onDisable()
		{
			$this->getLogger()->info(MT::AQUA."Plugin unloaded!");
		}
	}