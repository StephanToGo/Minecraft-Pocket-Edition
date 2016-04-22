<?php 
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
		public function onEnable(){
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->getLogger()->info(MT::AQUA."Plugin -=SH=-Chatfilter loading...!");
			$this->saveDefaultConfig();
			$cfg = $this->getConfig();
			$this->errormessage = $cfg->get('Errormessage');
			$this->badwords = $cfg->get('Badwords');
		}
		public function onChat(PlayerChatEvent $event){
			$player = $event->getPlayer();
			$name = $player->getName();
			$chat = $event->getMessage();
			
			foreach($this->badwords as $badword)
			{
				if(strpos($chat, $badword)!== false)
				{
					$event->setCancelled();
					$player->sendMessage(MT::RED.$this->errormessage);
					$this->getLogger()->info(MT::RED."$name try to use a bad word: $badword");
				}	
			}
		}
		public function onDisable(){
			$this->getLogger()->info(MT::AQUA."Plugin unloaded!");			
		}
	}
