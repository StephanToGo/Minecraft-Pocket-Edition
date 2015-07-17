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
		$time = time();
		
		if($anzahl >= 1)
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
				}
			}
		}
		
		foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player)
		{
		}
	}
		
}
