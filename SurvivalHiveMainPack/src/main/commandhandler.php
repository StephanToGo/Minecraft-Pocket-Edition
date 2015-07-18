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
	
	public function __construct(Plugin $plugin) 
	{
		$this->plugin = $plugin;
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) 
	{
		$name = strtolower($sender->getName());
		
		switch($command->getName())
		{
			case "shpos":
					$this->onSHPOS($sender,$args);
					break;
			case "shvip":
					$this->onSHVIP($sender,$args);
					break;
		}
	}
	
	public function onSHPOS($player, $args)
	{
		if(!($player instanceof Player)){$player->sendMessage('nope');return;}
		$id = $player->getID();
		$name = strtolower($player->getName());
		
		if (! (in_array($id, $this->plugin->bposition->schalter)))
		{
			$this->plugin->bposition->schalter[$name] = $id;
			$player->sendMessage(MT::GOLD."Position Eingeschaltet");
			return true;
		}
		else
		{
			$index = array_search($id, $this->plugin->bposition->schalter);
			unset($this->plugin->bposition->schalter[$index]);
			$player->sendMessage(MT::GOLD."Position Ausgeschaltet");
			return true;
		}
	}
	
	public function onSHVIP($player, $args)
	{
		$config = $this->plugin->cfg->getAll();
		$items = $config["Vips"];
		
		if(!(isset($args[0])))
		{
			$player->sendMessage(MT::GOLD."Vips: ".implode(", ", $items));
			return;
		}
		if($args[0] == "add")
		{
			if($player->isOp())
			{
				if(isset($args[1]))
				{
					$banid = $args[1];
					if(!in_array($banid, $items))
					{
						if(!is_array($items))
						{
							$items = array($banid);
							break;
						}
						else
						{
							$items[] = $banid;
							$player->sendMessage(MT::GOLD."Erfolgreich als VIP hinzugefuegt");
							$config["Vips"] = $items;
							$this->plugin->cfg->setAll($config);
							$this->plugin->cfg->save();
							break;
						}
					}
					else
					{
						$player->sendMessage(MT::GOLD."Ist schon VIP");
						break;
					}
						
				}
				else
				{
					$player->sendMessage(MT::GOLD."Nutz /shvip add NAME");
					break;
				}
			}
			else
			{
				$player->sendMessage(MT::GOLD."Nur fuer Operatoren");
				break;
			}
		}
		
		if($args[0] == "del")
		{
			if($player->isOp())
			{
				if(isset($args[1]))
				{
					$banid = $args[1];
					if(in_array($banid, $items))
					{
						$key = array_search($banid, $items);
						unset($items[$key]);
						$player->sendMessage("VIP erfolgreich entfernt");
						$config["Vips"] = $items;
						$this->plugin->cfg->setAll($config);
						$this->plugin->cfg->save();
					}
					else
					{
						$player->sendMessage("Spieler ist kein VIP");
						break;
					}
				}
				else
				{
					$player->sendMessage("Nutz /shvip del NAME");
					break;
				}
			}
			else
			{
				$player->sendMessage("Nur fuer Operatoren");
				break;
			}
		}
	}
}