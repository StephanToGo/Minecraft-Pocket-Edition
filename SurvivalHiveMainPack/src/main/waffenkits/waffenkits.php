<?php
namespace main\waffenkits;

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

class waffenkits implements Listener
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
			
			if($sign[0]=='[Waffen]')
			{
				if(!$event->getPlayer()->isOp())
				{
					$event->setLine(0,"[Nichtig]");
					return false;
				}
			}
			if($sign[0]=='[Leben]')
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
		$janein = false;

		
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
			
			if($sign[0] == '[Leben]')
			{
				$event->getPlayer()->sendMessage(MT::RED ."Leben aufgefuellt");
				$event->getPlayer()->setHealth(20);
				return true;
			}
			if($sign[0] == '[Waffen]')
			{
				if($i == 'Krieger')
				{
					for ($i = 0; $i < 36; $i++)
					{
					$item = $event->getPlayer()->getInventory()->getItem($i);
					$item1 = $item->getId();
					if($item1 == 259 || $item1 == 276 || $item1 == 261 || $item1 == 310 || $item1 == 311 || $item1 == 312 || $item1 == 313)
						{
							$event->getPlayer()->sendMessage(MT::RED ."Du hast schon deine Ausruestung");
							$event->setCancelled(true);
							return true;
						}
						else 
						{
							$janein = true;
						}
					}
					if($janein != false)
					{
						$event->getPlayer()->getInventory()->addItem(new Item(276, 0, 1));
						$event->getPlayer()->getInventory()->addItem(new Item(260, 0, 10));
					//  $player->getInventory()->addItem(new Item($item[0], $item[1], $item[2]));
					//	$player->getInventory()->addItem(new Item($item[0], $item[1], $item[2]));
						
						$event->getPlayer()->getInventory()->setHelmet(new Item(310, 0, 1));
						$event->getPlayer()->getInventory()->sendArmorContents($player);
						
						$event->getPlayer()->getInventory()->setChestplate(new Item(311, 0, 1));
						$event->getPlayer()->getInventory()->sendArmorContents($player);
						
						$event->getPlayer()->getInventory()->setLeggings(new Item(312, 0, 1));
						$event->getPlayer()->getInventory()->sendArmorContents($player);
						
						$event->getPlayer()->getInventory()->setBoots(new Item(313, 0, 1));
						$event->getPlayer()->getInventory()->sendArmorContents($player);
						
						$event->getPlayer()->sendMessage(MT::RED ."Krieger Ausruestung angelegt");
						return true;
					}
				return true;
				}
				if($i == 'Bogenschuetze')
				{
					
					
					
					for ($i = 0; $i < 36; $i++)
					{
					$item = $event->getPlayer()->getInventory()->getItem($i);
					$item1 = $item->getId();
						if($item1 == 259 || $item1 == 276 || $item1 == 261 || $item1 == 310 || $item1 == 311 || $item1 == 312 || $item1 == 313)
						{
							$event->getPlayer()->sendMessage(MT::RED ."Du hast schon deine Ausruestung");
							$event->setCancelled(true);
							return true;
						}
						else 
						{
							$janein = true;
							
						}
					}
					if($janein != false)
					{
						$player->getInventory()->addItem(new Item(261, 0, 1));
						$player->getInventory()->addItem(new Item(262, 0, 64));
						$player->getInventory()->addItem(new Item(260, 0, 10));
						//  $player->getInventory()->addItem(new Item($item[0], $item[1], $item[2]));
						//	$player->getInventory()->addItem(new Item($item[0], $item[1], $item[2]));
							
						$player->getInventory()->setHelmet(new Item(310, 0, 1));
						$event->getPlayer()->getInventory()->sendArmorContents($player);
							
						$event->getPlayer()->getInventory()->setChestplate(new Item(311, 0, 1));
						$event->getPlayer()->getInventory()->sendArmorContents($player);
							
						$event->getPlayer()->getInventory()->setLeggings(new Item(312, 0, 1));
						$event->getPlayer()->getInventory()->sendArmorContents($player);
							
						$event->getPlayer()->getInventory()->setBoots(new Item(313, 0, 1));
						$event->getPlayer()->getInventory()->sendArmorContents($player);
						$event->getPlayer()->sendMessage(MT::RED ."Bogenschuetzen Ausruestung angelegt");
						return true;
					}
				}
				if($i == 'Feuerfuchtler')
				{
				for ($i = 0; $i < 40; $i++)
					{
					$item = $event->getPlayer()->getInventory()->getItem($i);
					$item1 = $item->getId();
						if($item1 == 259 || $item1 == 276 || $item1 == 261 || $item1 == 310 || $item1 == 311 || $item1 == 312 || $item1 == 313)
						{
							$event->getPlayer()->sendMessage(MT::RED ."Du hast schon deine Ausruestung");
							$event->setCancelled(true);
							return true;
						}
						else 
						{
							$janein = true;
						
						}
					}
					if($janein != false)
					{
						$player->getInventory()->addItem(new Item(259, 0, 1));
						$player->getInventory()->addItem(new Item(260, 0, 10));
						//  $player->getInventory()->addItem(new Item($item[0], $item[1], $item[2]));
						//	$player->getInventory()->addItem(new Item($item[0], $item[1], $item[2]));
							
						$player->getInventory()->setHelmet(new Item(310, 0, 1));
						$player->getInventory()->sendArmorContents($player);
							
						$player->getInventory()->setChestplate(new Item(311, 0, 1));
						$player->getInventory()->sendArmorContents($player);
							
						$player->getInventory()->setLeggings(new Item(312, 0, 1));
						$player->getInventory()->sendArmorContents($player);
							
						$player->getInventory()->setBoots(new Item(313, 0, 1));
						$player->getInventory()->sendArmorContents($player);
						$event->getPlayer()->sendMessage(MT::RED ."Fauerfuchtler Ausruestung angelegt");
						return true;
					}
				}
			return true;
			}

		}
	}
}
