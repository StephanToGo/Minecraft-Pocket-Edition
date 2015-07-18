<?php
namespace main\debug;

use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\level\Position;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;

class Debug implements Listener
{
	private $plugin;
	
	public function __construct(Plugin $plugin ){
		$this->plugin = $plugin;
	}
	
	public function onDebug($text)
	{	if(!$this->plugin->cfg->get('debugmode')){return;}
		$this->plugin->getLogger()->warning($text);
	}
	
	public function onInfo($txt)
	{
		if(!$this->plugin->cfg->get('debugmode')){return;}
		$this->plugin->getLogger()->info(MT::GOLD.$text);
	}
}