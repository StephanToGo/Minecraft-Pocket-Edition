<?php


namespace main;


use pocketmine\utils\TextFormat as MT;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\server;
use pocketmine\level;
use pocketmine\level\Position;
use pocketmine\item\Item;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\math\Vector3;

	class jumpnrun extends PluginBase implements Listener
	
	{
		public $var;
		public $zeit;
		public $tot;
		public $coords;
		
		public $platz1;
		public $platz2;
		public $platz3;
		
		public $start;
		public $timer;
		
		public function onEnable()
		{
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->getServer()->getScheduler()->scheduleRepeatingTask(new startticker($this), 40);
			$this->getLogger()->info(MT::RED.'PlotschPlugin damit er ruhe gibt... und ich weiter arbeiten kann xD');
			if (!file_exists($this->getDataFolder()))
			{
				@mkdir($this->getDataFolder(), true);
			}
			$this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML, [
					"JumpandRunWelt" => "world",
					]);
		}
		
		public function onPlayerMoveEvent(PlayerMoveEvent $event)
		{
			$welt = $event->getPlayer()->getLevel()->getName();
			if($this->cfg->get("JumpandRunWelt") != $welt){return;}
			$name = $event->getPlayer()->getName();
			$player = $event->getPlayer();	
			$welt = $event->getPLayer()->getLevel()->getName();		
			$block = $event->getPlayer()->getLevel()->getBlock(new Vector3($player->getX(),$player->getY()-1,$player->getZ()))->getId();
			$coords = (round($player->getX()).','.round($player->getY()).','.round($player->getZ()));
			$time = time();
			
			if($block == 41)	
			{	
				$event->getPlayer()->sendTip(MT::GOLD.'Checkpoint erreicht!');
				$this->coords[$name] = $coords;
			}
			if($block == 57)
			{
				$event->getPlayer()->sendTip(MT::GOLD.'Ziel erreicht!');
				if(!(isset($this->zeit))){$this->zeit = (time() + 60);}
				
				if(!(isset($this->platz1)))
				{
					$this->platz1 = "$name";
					$player->sendMessage(MT::GOLD."$name du bist Erster");
				}
				else 
				{
					if(!(isset($this->platz2)))
					{
						if($name == $this->platz1)return;
						$this->platz2 = "$name";
						$player->sendMessage(MT::GOLD."$name du bist Zweiter");
					}
					else
					{
						if(!(isset($this->platz3)))
						{
							if($name == $this->platz1 || $name == $this->platz2)return;
							$this->platz3 = "$name";
							$player->sendMessage(MT::GOLD."$name du bist Dritter");
						}
						else
						{
							//
						}
					}
					
				}
				
				foreach($this->getServer()->getOnlinePlayers() as $player)
				{
					$player->sendMessage(MT::GOLD."$name hat das Ziel erreicht");
					
				}
			}
			if(isset($this->zeit))
			{
				if($time <= $this->zeit)
				{
					$zeit = $this->zeit - $time;
					foreach($this->getServer()->getOnlinePlayers() as $player)
					{
						$player->sendTip(MT::GOLD.'Schnell... noch '.MT::RED."$zeit".MT::GOLD.' bis zum Ende');
					}
				}
				else
				{
					$platz1 = $this->platz1;
					$platz2 = $this->platz2;
					$platz3 = $this->platz3;
					
					foreach($this->getServer()->getOnlinePlayers() as $player)
					{
						$player->sendMessage(MT::RED."Wettlauf beendet");
						$player->sendMessage(MT::GOLD."Platz1: $platz1 Platz2: $platz2 Platz3: $platz3");
				
						$player->teleport($player->getLevel()->getSafeSpawn());
					}
					
					unset ($this->zeit);
					unset ($this->timer);
					unset ($this->start);
					
					unset ($this->tot);
					unset ($this->coords);
					
					unset ($this->platz1);
					unset ($this->platz2);
					unset ($this->platz3);
				}
			}
			if(!(isset($this->start)))
			{
				$event->setCancelled(true);
			}

			
		}
		
		public function onRespawn(PlayerRespawnEvent $event)
		{
			$welt = $event->getPlayer()->getLevel()->getName();
			if($this->cfg->get("JumpandRunWelt") != $welt){return;}
			
			$name = $event->getPlayer()->getName();
			
			if(isset($this->tot[$name]));
			{
				if(isset($this->coords[$name]))
				{
					$pos = $this->coords[$name];
					$pos1 = explode(",", $pos);
					$event->setRespawnPosition(new Position($pos1[0], $pos1[1], $pos1[2]));
					$this->tot[$name] = $name;
					return;
				}
				
			}
			$x = $this->getServer()->getDefaultLevel()->getSafeSpawn()->getX();
			$y = $this->getServer()->getDefaultLevel()->getSafeSpawn()->getY();
			$z = $this->getServer()->getDefaultLevel()->getSafeSpawn()->getZ();
					
			$event->setRespawnPosition(new Position($x, $y, $z));
			unset ($this->tot[$name]);
			
		}
		
		public function onDeath(PlayerDeathEvent $event)
		{
		
			$player = $event->getEntity();
			if(!$player instanceof Player){return;}
			$welt = $event->getEntity()->getLevel()->getName();
			if($this->cfg->get("JumpandRunWelt") != $welt){return;}
			$name = $event->getEntity()->getName();
			$this->tot[$name] = $name;
		}
		
		public function onDisable()
		{
			$this->getLogger()->info("Plugin unloaded!");
		}
	}
