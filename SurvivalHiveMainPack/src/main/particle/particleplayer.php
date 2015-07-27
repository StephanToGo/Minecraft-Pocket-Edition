<?php


namespace main\particle;


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
use pocketmine\level\particle\DustParticle;
use pocketmine\level\sound\LaunchSound;
use pocketmine\level\sound\PopSound;
use pocketmine\level\sound\GenericSound;
use pocketmine\level\sound\FizzSound;

use pocketmine\entity\Effect;
use pocketmine\entity\InstantEffect;
use main\debug\Debug;


	class particleplayer extends PluginTask
	{
		private $debug;
				
		public function __construct(Plugin $owner) 
		{
			parent::__construct($owner);
			$this->debug = new Debug($owner);
		}
	
		public function onRun($currentTick)
		{
			$this->debug->onDebug('SpiralParticleRegen');
			
			$pos33 = new Vector3(127.5, 4, 136);
			$level1 = "lobby";
			$level = $this->getOwner()->getServer()->getLevelByName($level1);
				
			foreach ($this->getOwner()->getServer()->getOnlinePlayers() as $spieler)
			{
				$name = strtolower($spieler->getName());
				
					$level = $spieler->getLevel();
					$x = $spieler->getX();
					$y = $spieler->getY();
					$z = $spieler->getZ();
					$center = new Vector3($x, $y, $z);
					$radius = 0.5;
					$count = 100;
					$colr = rand(1,254);
					$colg = rand(1,254);
					$colb = rand(1,254);
					$particle = new DustParticle($center, $colr, $colg, $colb, 1);
					for($yaw = 0, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 20, $y += 1 / 20){
						$x = -sin($yaw) + $center->x;
						$z = cos($yaw) + $center->z;
						$particle->setComponents($x, $y, $z);
						$level->addParticle($particle);
					}
				
			}
		}
	}