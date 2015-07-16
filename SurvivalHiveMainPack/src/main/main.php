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
use pocketmine\utils\Config;
//---------------------------
use main\afkkicker\afkkicker;
use main\antidoublechest\antidoublechest;
use main\allwaysonspawn\allwaysonspawn;
use main\liftsign\liftsign;
use main\mutejoin\mutejoin;
use main\worldborder\worldborder;
//---------------------------

	class main extends PluginBase implements Listener
	
	{
	  public function onDisable()
		{
			$this->getLogger()->info(MT::GOLD."SurvivalHive Main unloaded!");
		}
	}
