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
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\level\particle\SmokeParticle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\BubbleParticle;
use pocketmine\level\particle\EnchantParticle;
use pocketmine\level\particle\DustParticle;
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
			if($this->owner->cfg->get("PlayerParticleTyp") == 'Spirale')
			{
				$this->debug->onDebug('SpiralParticleRegen');
					
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
						
						for($yaw = 0, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 20, $y += 1 / 20)
						{
							$x = -sin($yaw) + $center->x;
							$z = cos($yaw) + $center->z;
							$particle->setComponents($x, $y, $z);
							$level->addParticle($particle);
						}
					
				}
			}
			if($this->owner->cfg->get("PlayerParticleTyp") == 'Herzen')
			{
				$this->debug->onDebug('HerzParticle');
				
				foreach ($this->getOwner()->getServer()->getOnlinePlayers() as $spieler)
				{
					$name = strtolower($spieler->getName());
					$level = $spieler->getLevel();
					$x = $spieler->getX();
					$y = $spieler->getY();
					$z = $spieler->getZ();
				
					$pos = new Vector3($x,$y+2,$z);
					$level->addParticle(new HeartParticle($pos));
				}
			}
			if($this->owner->cfg->get("PlayerParticleTyp") == 'Kreis')
			{		
				$this->debug->onDebug('Kreis');
				foreach ($this->getOwner()->getServer()->getOnlinePlayers() as $spieler)
				{
					
					$x = $spieler->getX();
					$y = $spieler->getY();
					$z = $spieler->getZ();
					
					$level = $spieler->getLevel();
					
					$pos = new Vector3($x,$y+2,$z);
					
					$radius = 1;
					
					$colr = rand(1,254);
					$colg = rand(1,254);
					$colb = rand(1,254);					
							
					$particle = new DustParticle($pos, $colr, $colg, $colb, 1);
									
					
						$particle->setComponents($x + $radius, $y+2, $z + $radius);
						$level->addParticle($particle);
						
						$particle->setComponents($x - $radius, $y+2, $z - $radius);
						$level->addParticle($particle);
						
						$particle->setComponents($x + $radius, $y+2, $z - $radius);
						$level->addParticle($particle);
						
						$particle->setComponents($x - $radius, $y+2, $z + $radius);
						$level->addParticle($particle);
						
						$particle->setComponents($x + $radius, $y+2, $z);
						$level->addParticle($particle);
						
						$particle->setComponents($x, $y+2, $z + $radius);
						$level->addParticle($particle);
						
						$particle->setComponents($x - $radius, $y+2, $z);
						$level->addParticle($particle);
						
						$particle->setComponents($x, $y+2, $z - $radius);
						$level->addParticle($particle);
					}		
			}
			if($this->owner->cfg->get("PlayerParticleTyp") == 'Regen')
			{
				$this->debug->onDebug('Regen');
				foreach ($this->getOwner()->getServer()->getOnlinePlayers() as $spieler)
				{
					$level = $spieler->getLevel();
				}
				
				
				$colr = rand(1,254);
				$colg = rand(1,254);
				$colb = rand(1,254);
				
				$pos1 = explode(",",$this->owner->cfg->get("PlayerParticleRegenPos1"));
				$pos2 = explode(",",$this->owner->cfg->get("PlayerParticleRegenPos2"));
				
				
				$startX = $pos1[0];
				$endX = $pos2[0];
				$startY = $pos1[1];
				$endY = $pos2[1];
				$startZ = $pos1[2];
				$endZ = $pos2[2];
				
				
			/*	$startX = -52;
				$endX = -46;
				$startY = 11;
				$endY = 11;
				$startZ = 187;
				$endZ = 193;*/
					
				$this->debug->onDebug('0');
				for($x = $startX; $x <= $endX; ++$x)
				{
					
					for($y = $startY; $y <= $endY; ++$y)
					{
						
						for($z = $startZ; $z <= $endZ; ++$z)
						{
							//$this->debug->onDebug('3');
							$pos = new Vector3($x,$y,$z);
							$particle = new DustParticle($pos, $colr, $colg, $colb, 1);
							$level->addParticle($particle);
							
						}
					}
				}
				$this->debug->onDebug('1');
			}
		}
}