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
		$this->plugin = $core;
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) 
	{
		if($sender instanceof Player)
		{
			if(strtolower($command->getName()) == "shpos")
			{
				$id = $sender->getID();
				$name = strtolower($sender->getName());
		
				if (! (in_array($id, $this->plugin->bposition->schalter)))
				{
					$this->plugin->bposition->schalter[$name] = $id;
					$sender->sendMessage("Position Eingeschaltet");
					return true;
				}
				else
				{
					$index = array_search($id, $this->plugin->bposition->schalter);
					unset($this->plugin->bposition->schalter[$index]);
					$sender->sendMessage("Position Ausgeschaltet");
					return true;
				}
			}
		}
		else
		{
			$sender->sendMessage("Nur im Spiel moeglich");
			return true;
		}
	}
}
