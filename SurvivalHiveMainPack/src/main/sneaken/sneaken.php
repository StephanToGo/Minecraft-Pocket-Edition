<?php


namespace main\sneaken;


use pocketmine\utils\TextFormat as MT;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
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
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerRespawnEvent;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityMoveEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;

class sneaken implements Listener 
{
	
	private $plugin;
	private $var = array();

	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
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
		
		public function onPlayerInteractEvent(PlayerInteractEvent $event)
		{
				$name = strtolower($event->getPlayer()->getName());
				
				if(isset($this->var[$name]))
				{
					if($event->getPlayer()->getDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_SNEAKING) == true)
					{
						$event->getPlayer()->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_SNEAKING, false);
					}
					else
					{
						$event->getPlayer()->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_SNEAKING, true);
					}
					$event->getPlayer()->sendTip(MT::GOLD."Schleichen aktiviert/deaktiviert");
				}
		}
	}