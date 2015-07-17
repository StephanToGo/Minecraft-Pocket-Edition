<?php

namespace main;

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

class startticker extends PluginTask
{

	public function __construct(Plugin $owner)
	{
		parent::__construct($owner);
	}

	public function onRun($currentTick)
	{
		$anzahl = count($this->getOwner()->getServer()->getOnlinePlayers());
		$i = 0;
		foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
		{
			$welt = $player->getLevel()->getName();
			if($this->getOwner()->cfg->get("JumpandRunWelt") == $welt)
			{
				$i++;
			}
		}
		
		$time = time();
		
		if($i >= 2)
		{
			if(!(isset($this->getOwner()->timer)))
			{
				$this->getOwner()->timer = (time() + 30);
			}
			
			if($time >= $this->getOwner()->timer)
			{
				if(!(isset($this->getOwner()->start)))
				{
					$this->getOwner()->start = 1;
					
					foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
					{
						$welt = $player->getLevel()->getName();
						if($this->getOwner()->cfg->get("JumpandRunWelt") == $welt)
						{
							$player->sendTip(MT::GREEN.'START START START START START START');
							$player->sendPopup(MT::GREEN.'START START START START START START');
						}
					}
				}	
			}
			else
			{
				$time2 = ($this->getOwner()->timer - $time);
				foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
				{
					$welt = $player->getLevel()->getName();
					if($this->getOwner()->cfg->get("JumpandRunWelt") == $welt)
					{
						$player->sendTip(MT::RED."$time2".MT::GOLD.' warten auf weitere / wait on more');
					}
				}
			}
			
			if(isset($this->getOwner()->zeit))
			{
				if($time <= $this->getOwner()->zeit)
				{
					$zeit = $this->getOwner()->zeit - $time;
					foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
					{
						$player->sendTip(MT::GOLD.'Schnell... noch '.MT::RED."$zeit".MT::GOLD.' bis zum Ende');
					}
				}
				else
				{
					if(isset($this->getOwner()->platz1))
					{$platz1 = $this->getOwner()->platz1;}else{$platz1 = 'niemand';}
					if(isset($this->getOwner()->platz2))
					{$platz2 = $this->getOwner()->platz2;}else{$platz2 = 'niemand';}
					if(isset($this->getOwner()->platz3))
					{$platz3 = $this->getOwner()->platz3;}else{$platz3 = 'niemand';}
						
					foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
					{
						$welt = $event->getPlayer()->getLevel()->getName();
						if($this->getOwner()->cfg->get("JumpandRunWelt") != $welt){return;}
						$player->sendMessage(MT::RED."Wettlauf beendet");
						$player->sendMessage(MT::GOLD."Platz1: $platz1 Platz2: $platz2 Platz3: $platz3");
					
						$player->teleport($player->getLevel()->getSafeSpawn());
					}
						
					unset ($this->getOwner()->zeit);
					unset ($this->getOwner()->timer);
					unset ($this->getOwner()->start);
						
					unset ($this->getOwner()->tot);
					unset ($this->getOwner()->coords);
						
					unset ($this->getOwner()->platz1);
					unset ($this->getOwner()->platz2);
					unset ($this->getOwner()->platz3);
				}
			}
		}	
		else
		{
			if(!(isset($this->getOwner()->start)))
			{
				if(!(isset($this->getOwner()->timer)))
				{
					foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
					{
						$welt = $player->getLevel()->getName();
						if($this->getOwner()->cfg->get("JumpandRunWelt") == $welt)
						{
							$player->sendTip(MT::GOLD.'Warte auf Mitspieler / Wait for other players');
						}
					}
				}
			}
			unset ($this->getOwner()->start);
			unset ($this->getOwner()->zeit);
			unset ($this->getOwner()->timer);
			unset ($this->getOwner()->start);
				
			unset ($this->getOwner()->tot);
			unset ($this->getOwner()->coords);
				
			unset ($this->getOwner()->platz1);
			unset ($this->getOwner()->platz2);
			unset ($this->getOwner()->platz3);
		}	
	}		
}
