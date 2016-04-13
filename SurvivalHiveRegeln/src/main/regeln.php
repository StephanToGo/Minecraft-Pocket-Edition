<?php 
namespace main;

use pocketmine\utils\TextFormat as MT;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\level;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\math\Vector3;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;

	class regeln extends PluginBase implements Listener
	{
		
		public $config = array();
		public function onEnable()
		{
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->getLogger()->info(MT::AQUA."Plugin -=SH=-Regeln loading...!");
			@mkdir($this->getDataFolder());
			$this->config = (new Config($this->getDataFolder()."config.yml", Config::YAML))->getAll();
			if(!(isset($this->config['Rules'])))
			{
				$this->config['Rules'] = [];
				$this->onSave();
			}
			
			if(!(isset($this->config['Regeln'])))
			{
				$this->config['Regeln'] = [];
				$this->onSave();
			}
		}
		
		public function onCommand(CommandSender $sender, Command $command, $label, array $args) 
		{
			$name = strtolower($sender->getName());
		
			switch(strtolower($command->getName()))
			{
				case "rules":
					$this->onRules($sender);
					break;
				case "regeln":
					$this->onRegeln($sender);
					break;
			}
		}
		
		public function onRules($sender)
		{
			foreach($this->config['Rules'] as $rules)
			{
				$sender->sendMessage("$rules");
			}
		}
		
		public function onRegeln($sender)
		{
			foreach($this->config['Regeln'] as $regeln)
			{
				$sender->sendMessage("$regeln");
			}
		}

		private function onSave()
		{
			$config = (new Config($this->getDataFolder()."config.yml", Config::YAML));
			$config->setAll($this->config);
			$config->save();
			unset($config);
		}
		
		public function onDisable()
		{
			$this->getLogger()->info(MT::AQUA."Plugin unloaded!");			
		}
	}
