<?php
namespace main\jumpsign;

use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\tile\Sign;
use pocketmine\event\block\SignChangeEvent;
/** Not currently used but may be later used  */
use pocketmine\level\Position;
use pocketmine\level\Level;
use pocketmine\entity\Entity;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\Item;
use pocketmine\tile\Tile;
use pocketmine\Player;
use pocketmine\utils\TextFormat as MT;
use main\debug\Debug;
class jumpsign implements Listener
{

	private $plugin;
	
	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
		$this->debug = new Debug($plugin);
	}

public function tileupdate(SignChangeEvent $event)
{
		if($event->getBlock()->getID() == 323 || $event->getBlock()->getID() == 63 || $event->getBlock()->getID() == 68)
		{
			$sign = $event->getPlayer()->getLevel()->getTile($event->getBlock());
			
			if(!($sign instanceof Sign))
			{
				return true;
			}
			$sign = $event->getLines();
			
			if($sign[0]=='[Jump]')
			{
				if(!$event->getPlayer()->isOp())
				{
					$event->setLine(0,"[Nichtig]");
					return false;
				}
			}
		}
		return true;
	}
	
   
	public function playerBlockTouch(PlayerInteractEvent $event)
	{
		$player = $event->getPlayer();
		$name = strtolower($player->getName());
			
		if($event->getBlock()->getID() == 323 || $event->getBlock()->getID() == 63 || $event->getBlock()->getID() == 68)
		{
			$sign = $event->getPlayer()->getLevel()->getTile($event->getBlock());
			if(!($sign instanceof Sign))
			{
				return;
			}
			$sign = $sign->getText();
			$i = $sign[1];
			$i2 = $sign[2];
			$i3 = $sign[3];
			
			if($sign[0] == '[Jump]')
			{
				$i11 = explode(",", $i);
				$i12 = explode(",", $i2);
				
				$this->getLogger()->info("$i11[0] $i11[1] $i11[2] $i12[0] $i12[1] $i12[2]");
				
				$event->getPlayer()->teleport(Server::getInstance()->getLevelByName($i3)->getSafeSpawn(new Position(rand($i11[0],$i12[0]), rand($i11[1],$i12[1]), rand($i11[2],$i12[2]))));
				return true;
			}
		}
	}
}
