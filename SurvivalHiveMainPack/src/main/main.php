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

use main\antifly\antifly;
use main\antifly\antiflyevent;

use main\antidoublechest\antidoublechest;
use main\allwaysonspawn\allwaysonspawn;
use main\liftsign\liftsign;
use main\antipvp\antipvp;
use main\antibuild\antibuild;
use main\particle\particleplayer;
use main\sounds\joinsound;

use main\mutejoin\mutejoin;
use main\bposition\bposition;
use main\jumpsign\jumpsign;
use main\sneaken\sneaken;
use main\waffenkits\waffenkits;
use main\weiterleitung\weiterleitung;
use main\worldborder\worldborder;
use main\vipslot\vipslot;
//---------------------------
use main\commandhandler as commandhandler;
//---------------------------

	class main extends PluginBase implements Listener
	
	{
		public $player = array();
		public $schalter = array();
		
		public $antidoublechest;
		//public $antifly;
		public $allwaysonspawn;
		public $mutejoin;
		public $liftsign;
		public $bposition;
		
		public $worldborder;
		
		private $command_class;
		
		public function onEnable()
		{
			$this->getLogger()->info(MT::GOLD."SurvivalHive Main loaded!");
			$this->getServer()->getPluginManager()->registerEvents($this,$this);

			if (!file_exists($this->getDataFolder()))
			{
				@mkdir($this->getDataFolder(), true);
			}
			$this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML, [
					"Anleitung/Manuell" => "PlayerParticleTypen = Spirale/Herzen | JoinSoindTypen = BatSound/ClickSound/",
					"debugmode" => true,
					"AFK-Kick" => true,
					"AntiBuildGenerell" => true,
					"AntiPvPGenrell" => true,
					"AntiDoubleChest" => true,
					"AllwaysOnSpawn" => true,
					"Antifly" => true,
					"Liftsign" => true,
					"MuteJoin" => true,
					"JumpSign" => true,
					"Waffenkits" => true,
					"Sneaken" => true,
					"BlockPosition" => true,
					"PlayerParticle" => true,
					"PlayerParticleTyp" => "Spirale",
					"JoinSound" => true,
					"JoinSoundTyp" => "BatSound",
					"Weiterleitung" => false,
					"WeiterleitungIP" => "148.251.4.154",
					"WeiterleitungPort" => "19132",
					"WorldBorder" => true,
					"WorldBorderPos1" => "1,1,1",
					"WorldBorderPos2" => "100,100,100",
					"WorldBorderWorld" => "world",
					"Vipslot" => true,
					"Vips" => [],
			]);
		}
		public function onCommand(CommandSender $sender, Command $command, $label, array $args)
		{
			$this->command_class->onCommand($sender, $command, $label, $args);
		}
		
	  	public function onDisable()
		{
			$this->getLogger()->info(MT::GOLD."SurvivalHive Main unloaded!");
		}
	}
