<?php

namespace main\afkkicker;

use pocketmine\utils\TextFormat as MT;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;
use main\debug\Debug;

	class afkkicker extends PluginTask
	{
		private $debug;
				
		public function __construct(Plugin $owner) 
		{
			parent::__construct($owner);
			$this->debug = new Debug($owner);
		}
	
		public function onRun($currentTick)
		{
			$this->debug->onDebug('AFKKICKER OnRun');
			
			foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
			{
				if($player->isOp()){return;}
				$name = $player->getName();
				$coords = (round($player->getX()).','.round($player->getY()).','.round($player->getZ()));
				
				$this->debug->onDebug("$name $coords");
				
				
				if(!(isset($this->getOwner()->player[$name]['Coords'])))
				{
					$this->getOwner()->player[$name]['Coords'] = $coords;
				}
				else
				{	
					if($this->getOwner()->player[$name]['Coords'] == $coords)
					{					
						$this->debug->onDebug("AFKKICKER $name");
						$player->kick($reason=MT::RED.'KICK! -> A-F-K');
						unset ($this->getOwner()->player[$name]);
					}
					else
					{
						unset ($this->getOwner()->player[$name]);
					}
				}
				
			}	
		}
			
	}
	
