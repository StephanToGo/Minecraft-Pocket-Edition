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

	class afkkicker extends PluginTask
	{
		public function __construct(Plugin $owner) 
		{
			parent::__construct($owner);
		}
	
		public function onRun($currentTick)
		{
			if($this->getOwner()->cfg->get("debugmode") == "true"){$this->getOwner()->getLogger()->info(MT::GREEN."AFKKICKER onRun");}
			foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
			{
				$name = $player->getName();
				$coords = (round($player->getX()).",".round($player->getY()).",".round($player->getZ()));
				
				if($this->getOwner()->cfg->get("debugmode") == "true"){$this->getOwner()->getLogger()->info(MT::GREEN."AFKKICKER $name $coords");}
				
				if(!$player->isOp())
				{
					if(!(isset($this->getOwner()->player[$name]['Coords'])))$this->getOwner()->player[$name]['Coords'] = 0;
					if(!(isset($this->getOwner()->player[$name]['Counter'])))$this->getOwner()->player[$name]['Counter'] = 0;
					
					if($this->getOwner()->player[$name]['Coords'] == $coords)
					{	
						if($this->getOwner()->player[$name]['Counter'] == 0)
						{
							$this->getOwner()->player[$name]['Counter'] = 1;
							if($this->getOwner()->cfg->get("debugmode") == "true"){$this->getOwner()->getLogger()->info(MT::GREEN."AFKKICKER If Counter 0");}
						}
						else
						{
							if($this->getOwner()->player[$name]['Counter'] == 1)
							{
								if($this->getOwner()->cfg->get("debugmode") == "true"){$this->getOwner()->getLogger()->info(MT::GREEN."AFKKICKER If Counter 1");}
								$player->kick($reason=MT::RED."KICK! -> A-F-K");
								unset ($this->getOwner()->player[$name]);
								return;
							}
						}
					}
					else
					{
						if($this->getOwner()->cfg->get("debugmode") == "true"){$this->getOwner()->getLogger()->info(MT::GREEN."AFKKICKER Else Counter 0");}
						$this->getOwner()->player[$name]['Counter'] = 0;
					}
					$this->getOwner()->player[$name]['Coords'] = $coords;
				}
				if($this->getOwner()->cfg->get("debugmode") == "true"){$this->getOwner()->getLogger()->info(MT::YELLOW."AFKKICKER: $name ". $this->getOwner()->player[$name]['Counter'] ." ".$this->getOwner()->player[$name]['Coords'] );}	
			}
			if($this->getOwner()->cfg->get("debugmode") == "true"){$this->getOwner()->getLogger()->info(MT::GREEN."AFKKICKER onEnd");}
		}
	}
