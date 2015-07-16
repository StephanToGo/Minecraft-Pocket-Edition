<?php
namespace main;

use pocketmine\utils\TextFormat as MT;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\Config;

class commandhandler implements Listener{

	private $plugin;

	public function __construct(Main $core) 
	{
		//parent::__construct ( $core );
		
		$this->plugin = $core;
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) 
	{
			// hier kannste jetzt alles reinklatschen was du sonst in der Main hattest ;)
			// bla blub :D
			$sender->sendMessage("teste");
			
			// return nie vergessen X)
			return false;
		
	}
}
