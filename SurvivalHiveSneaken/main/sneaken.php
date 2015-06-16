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

use pocketmine\math\Vector3;
use pocketmine\level\Position;

use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerRespawnEvent;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityMoveEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;

	class sneaken extends PluginBase implements Listener
	
	{
		public function onEnable()
		{
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
		}
		
		public function onPlayerItemHeldEvent (PlayerItemHeldEvent  $event)
		{
				$name = strtolower($event->getPlayer()->getName());
				if($event->getItem()->getId() == 50)
				{
					$this->var[$name] = $name;
				}
				else
				{
					unset ($this->var[$name]);
				}
			
		}
		
		public function onMove(PlayerMoveEvent $event)
		{
			$user = $event->getPlayer();
			
			if($user instanceof Entity)
			{
				$name = strtolower($event->getPlayer()->getName());
				
				if(isset($this->var[$name]))
				{
					$event->getPlayer()->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_SNEAKING, true);
					$event->getPlayer()->sendPopup(MT::GOLD."Schleichen aktiviert");
				}
				else
				{
					$event->getPlayer()->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_SNEAKING, false);
				}
			}
		}
	
		public function onDisable()
		{
			$this->getLogger()->info("Plugin unloaded!");
		}
	}
