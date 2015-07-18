<?php


namespace main\antipvp;

use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\server;
use pocketmine\level;
use pocketmine\item\Item;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use main\debug\Debug;

	class antipvp extends PluginBase implements Listener
	
	{
		private $plugin;
		
		public function __construct(Plugin $plugin){
			$this->plugin = $plugin;
			$this->debug = new Debug($plugin);
		}
	
		public function onEntityDamageByEntity(EntityDamageEvent $event)
		{
			if($event instanceof EntityDamageByEntityEvent)
			{
				$victim = $event->getEntity();
				$attacker = $event->getDamager();
				$this->debug->onDebug('Damage denied');
				$event->setCancelled(true);
			}
		}
		
		public function onPlayerDeathEvent(PlayerDeathEvent $event)
		{
			$event->setKeepInventory(true);
			$this->debug->onDebug('Keep Inventory from player');
		}
		
	}