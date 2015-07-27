<?php
namespace main\sounds;


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

use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerRespawnEvent;

use pocketmine\math\Vector3;

use pocketmine\level\Position;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\level\particle\SmokeParticle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\BubbleParticle;
use pocketmine\level\particle\EnchantParticle;
use pocketmine\level\sound\BatSound;
use pocketmine\level\sound\ClickSound;
use pocketmine\level\sound\LaunchSound;
use pocketmine\level\sound\PopSound;
use pocketmine\level\sound\GenericSound;
use pocketmine\level\sound\FizzSound;

use pocketmine\entity\Effect;
use pocketmine\entity\InstantEffect;
use main\debug\Debug;

class joinsound implements Listener
{
	private $plugin;
	private $debug;

	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
		$this->debug = new Debug($plugin);
	}
	
	public function onJoin(PlayerJoinEvent $event)
	{
		
		if($this->plugin->cfg->get("JoinSound"))
		{
			$this->debug->onDebug('JoinSound');

			$p = $event->getPlayer();
			$name = $event->getPlayer()->getName();
			$pos = new Vector3($p->x, $p->y, $p->z);
			
			if($this->plugin->cfg->get("JoinSoundTyp") == 'BatSound')
			{
				$this->debug->onDebug('BatSound');
				
				$event->getPlayer()->getLevel()->addSound(new BatSound($pos, 0));
			}
			if($this->plugin->cfg->get("JoinSoundTyp") == 'ClickSound')
			{
				$this->debug->onDebug('ClickSound');
				
				$event->getPlayer()->getLevel()->addSound(new ClickSound($pos, 0));
			}
		}
	}
}