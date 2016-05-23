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
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\math\Vector3;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;

class kickloc extends PluginBase implements Listener
{	
		public function onEnable()
		{
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->getLogger()->info(MT::AQUA."Plugin -=SH=-AntiKickLoc loading...!");
		}
		
		public function onKick(PlayerKickEvent $event)
		{
			$grund = $event->getReason();
			$kicklocationreason = 'logged in from another location';

			if($grund == $kicklocationreason)
			{
				$event->setCancelled();
			}
			return;
		}
		
		public function onDisable(){
			$this->getLogger()->info(MT::AQUA."Plugin unloaded!");					
		}
}
