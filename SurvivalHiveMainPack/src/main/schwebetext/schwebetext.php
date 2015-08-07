<?php


namespace main\schwebetext;


use pocketmine\utils\TextFormat as MT;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\server;
use pocketmine\level;
use pocketmine\item\Item;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\math\Vector3;

use pocketmine\entity\Entity;

use pocketmine\nbt\tag\Byte;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Double;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\Float;
use pocketmine\nbt\tag\Short;
use pocketmine\nbt\tag\String;
use main\debug\Debug;

	class schwebetext implements Listener
	
	{
		private $plugin;
		private $debug;
		
		public function __construct(Plugin $plugin)
		{
			$this->plugin = $plugin;
			$this->debug = new Debug($plugin);
		}
		
		public function onJoin(PlayerJoinEvent $event)
		{
			//$skin = $event->getPlayer()->getSkinData();
			if(!(isset($this->var)))
			{
				$test = $event->getPlayer()->getLevel()->getEntities();
				
				foreach($test as $sender)
				{	
					if(!($sender instanceof Player))
					{
						$sender->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, true);
						$this->debug->onDebug('Schwebetext -> Entity unsichtbar gemacht');
					}	
					
					$this->debug->onDebug("Join Schwebetext $sender");	
				}
				$this->var = 1;
			}
		}
	
		public function onCommand(CommandSender $p, Command $command, $label, array $args)
		{
			if(strtolower($command->getName()) == "create")
			{
				if(!($player->isOp())){$player->sendMessage('nope');return;}
				
				if(isset($args[0]))
				{
					if($args[0] == "gruen" || $args[0] == "blau" || $args[0] == "rot" || $args[0] == "gelb")
					{
						if(isset($args[1]))
						{
							$block = $p;
							$level = $p->getLevel();
							$chunk = $level->getChunk($block->getX() >> 4, $block->getZ() >> 4);
							
							$nbt = new Compound;
							
							$nbt->Pos = new Enum("Pos", [
									 
									new Double("", $block->getX()),
									new Double("", $block->getY()),
									new Double("", $block->getZ())		 
							]);
							
							$nbt->Motion = new Enum("Motion", [
									 
									new Double("", 0),
									new Double("", 0),
									new Double("", 0)		 
							]);
							 
							$nbt->Rotation = new Enum("Rotation", [
									new Float("", lcg_value() * 360),
									new Float("", 0)	 
							]);
		 
							$nbt->Inventory = new Enum("Inventory", []);

							$nbt->Skin = new Compound("Skin", [
  							"Data" => new String("Data", str_repeat("\xFF", 32*16*2) . str_repeat("\xFF", 32*16*2) . str_repeat("\xFF", 32*16*2) . str_repeat("\xFF", 32*16*2) . str_repeat("\x80", 32*16*2) . str_repeat("\x80", 32*16*2) . str_repeat("\x80", 32*16*2) . str_repeat("\x80", 32*16*2)),"Slim" => new Byte(0)
								]); 

							$farbe = $args[0];
							
							//$entity = Entity::createEntity(15, $chunk, $nbt);
							$entity = Entity::createEntity("$args[2]", $chunk, $nbt);
							$entity->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_SHOW_NAMETAG, true);
							$entity->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_AIR, true);
						//	$entity->setSkin(str_repeat("\xFF", 64 * 32 * 4) . str_repeat("\xFF", 64 * 32 * 4) . str_repeat("\xFF", 64 * 32 * 4) . str_repeat("\xFF", 64 * 32 * 4) . str_repeat("\x80", 64 * 32 * 4) . str_repeat("\x80", 64 * 32 * 4) . str_repeat("\x80", 64 * 32 * 4) . str_repeat("\x80", 64 * 32 * 4), false);
							//$entity->setSkin($skin, false);
							$entity->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, true);
							$entity->canCollideWith();
							
							if($farbe == "gruen") $entity->setNameTag(MT::GREEN."$args[1]");
							if($farbe == "blau") $entity->setNameTag(MT::AQUA."$args[1]");
							if($farbe == "rot") $entity->setNameTag(MT::RED."$args[1]");
							if($farbe == "gelb") $entity->setNameTag(MT::YELLOW."$args[1]");
							
							$test = $p->getLevel()->getEntities();
							
							foreach($test as $z)
							{
								$this->debug->onDebug("$z");
							}
										
							$entity->spawnToAll();
						}
					}
					if($args[0] == "remove")
					{
						$this->debug->onDebug('Schwebetext -> Remove start');
						$test = $p->getLevel()->getEntities();
						foreach($test as $z)
						{
							if(!($z instanceof Player))
							{
								$z->close();
								$this->debug->onDebug('Schwebetext -> geloescht');
							}
						}
					}
				}
				else
				{
					$p->sendMessage(MT::RED."Farbe und Text waehlen");
					return true;
				}
			}
		}
	}