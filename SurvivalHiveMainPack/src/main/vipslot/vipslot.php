<?php
namespace main\vipslot;

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
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\utils\TextFormat as MT;
use main\debug\Debug;

class vipslot implements Listener
{
	private $plugin;
	private $debug;

	public function __construct(Plugin $plugin)
	{
		$this->plugin = $plugin;
		$this->debug = new Debug($plugin);
	}  
    
    public function onPlayerKick(PlayerKickEvent $event)
     {
     	$config = $this->plugin->cfg->getAll();
     	$player = $event->getPlayer();
     	$name = strtolower($event->getPlayer()->getName());
     	$items = $config["Vips"];
     	
     	$reason = $event->getReason();
     	if((($reason == "disconnectionScreen.serverFull") || $reason == MT::RED.'KICK! -> A-F-K') && (in_array($name, $items)))
     	{
     		$event->setCancelled(true);
     	}
     }  
}
