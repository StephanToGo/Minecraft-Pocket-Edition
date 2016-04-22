<?php
namespace main;

use pocketmine\utils\TextFormat as MT;
use pocketmine\permission\Permission;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;
use pocketmine\event\player\PlayerInteractEvent;

	class love extends PluginBase implements Listener
	{
		public $schalter = array();
		public $namesave = array();
		
		public function onEnable()
		{
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->getLogger()->info(MT::AQUA.'Plugin -=SH=-Love loading...!');
			$this->saveDefaultConfig();
			$cfg = $this->getConfig();
		}
		
		public function onCommand(CommandSender $p, Command $command, $label, array $args)
		{
			if($p instanceof Player) 
			{
				if(strtolower($command->getName()) == 'shlove')
				{
					if($this->permissions == true)
					{
						if($p->isOp() || $p->hasPermission('survivalhive.nicks'))
						{
							if(isset($args[0]))
							{
								switch(strtolower($args[0]))
								{
									case "love":
										$this->inLove($p, $args[0]);
										break;
									case "hate":
										$this->inHate($p, $args[0]);
										break;
									case "unlove":
										$this->outLove($p, $args[0]);
										break;
									case "unhate":
										$this->outHate($p, $args[0]);
										break;
								}
							}
							else
							{
								
							}
						}
						else
						{
							$p->sendMessage(MT::RED.'You dont have the permissions to use this command!');
						}
					}
					else
					{
						
					}	
				}
			}
		}
	}