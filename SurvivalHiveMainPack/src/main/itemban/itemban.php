<?php
namespace main\itemban;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;
use pocketmine\permission\Permission;
use pocketmine\Server;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\utils\TextFormat as MT;
use main\debug\Debug;

class itemban implements Listener
{
	private $plugin;
	private $debug;

	public function __construct(Plugin $plugin)
	{
		$this->plugin = $plugin;
		$this->debug = new Debug($plugin);
	}  
    
	public function onPlayerInteract(PlayerInteractEvent $event) 
	{
		$id = $event->getBlock()->getID();
		$player = $event->getPlayer();
		if($id == in_array($id, $this->plugin->cfg->get("Items")))
		{
			if(! ($player->isOp()))
			{
					$event->setCancelled();
			}
		}
	}

	public function onBlockPlace(BlockPlaceEvent $event) 
	{	
		$id = $event->getBlock()->getID();
		$player = $event->getPlayer();
		if($id == in_array($id, $this->plugin->cfg->get("Items")))
		{
			if(! ($player->isOp()))
			{
				$event->setCancelled();
			}				
		}
	}
	
	public function onBlockBreak(BlockBreakEvent $event)
	{
		$id = $event->getBlock()->getID();
		$player = $event->getPlayer();
		if($id == in_array($id, $this->plugin->cfg->get("Items")))
		{
			if(! ($player->isOp()))
			{
				$event->setCancelled();
			}
		}
	}
}
