<?php
namespace main;


use pocketmine\utils\TextFormat as MT;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\level;
use pocketmine\item\Item;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use pocketmine\level\sound\BatSound;
use pocketmine\level\sound\ClickSound;
use pocketmine\level\sound\LaunchSound;
use pocketmine\level\sound\PopSound;
use pocketmine\level\sound\GenericSound;
use pocketmine\level\sound\FizzSound;
use main\debug\Debug;

class sounds extends PluginBase implements Listener 
{
	public function onEnable()
	{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info(MT::AQUA."-=SH=-Sounds Plugin loading...!");
		if (!file_exists($this->getDataFolder())){@mkdir($this->getDataFolder(), true);}
		$this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML, array("Join" => 'BatSound',"Quit" => 'ClickSound'));
	}
	
	public function onJoin(PlayerJoinEvent $event)
	{
		$p = $event->getPlayer();
		$name = $event->getPlayer()->getName();
		$pos = new Vector3($p->x, $p->y, $p->z);
			
		if($this->cfg->get("Join") == 'BatSound')
		{		
			$event->getPlayer()->getLevel()->addSound(new BatSound($pos, 0));
		}
		if($this->cfg->get("Join") == 'ClickSound')
		{	
			$event->getPlayer()->getLevel()->addSound(new ClickSound($pos, 0));
		}	
	}
	
	public function onQuit(PlayerQuitEvent $event)
	{	
		$p = $event->getPlayer();
		$name = $event->getPlayer()->getName();
		$pos = new Vector3($p->x, $p->y, $p->z);
			
		if($this->cfg->get("Quit") == 'BatSound')
		{
				
			$event->getPlayer()->getLevel()->addSound(new BatSound($pos, 0));
		}
		if($this->cfg->get("Quit") == 'ClickSound')
		{	
			$event->getPlayer()->getLevel()->addSound(new ClickSound($pos, 0));
		}
	}
}