namespace main;
use pocketmine\utils\TextFormat as MT;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\level;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\math\Vector3;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;
	class chat extends PluginBase implements Listener
	{
		
		public $config = array();
		public function onEnable()
		{
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->getLogger()->info(MT::AQUA."Plugin -=SH=-Chatfilter loading...!");
			@mkdir($this->getDataFolder());
			$this->config = (new Config($this->getDataFolder()."config.yml", Config::YAML))->getAll();
			if(!(isset($this->config['Badwords'])))
			{
				$this->config['Badwords'] = [];
				$this->onSave();
			}
			
			if(!(isset($this->config['Errormessage'])))
			{
				$this->config['Errormessage'] = 'Bad word dude...';
				$this->onSave();
			}
		}
		public function onChat(PlayerChatEvent $event)
		{
			$player = $event->getPlayer();
			$name = $player->getName();
			$chat = $event->getMessage();
			
			foreach($this->config['Badwords'] as $badword)
			{
				if(strpos($chat, $badword)!== false)
				{
					$event->setCancelled();
					$player->sendMessage(MT::RED.$this->config['Errormessage']);
					$this->getLogger()->info(MT::RED."$name try to use a bad word: $badword);
				}	
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
