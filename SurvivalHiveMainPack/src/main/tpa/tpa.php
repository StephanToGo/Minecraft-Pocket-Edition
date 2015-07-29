<?php
namespace main\tpa;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\IPlayer;
use pocketmine\plugin\PluginBase;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\block\Block;
use pocketmine\command\CommandSender;
use pocketmine\entity\Arrow;
use pocketmine\entity\DroppedItem;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityMoveEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\player\PlayerAchievementAwardedEvent;
use pocketmine\event\player\PlayerAnimationEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\Timings;
use pocketmine\inventory\BaseTransaction;
use pocketmine\inventory\BigShapelessRecipe;
use pocketmine\inventory\CraftingTransactionGroup;
use pocketmine\inventory\FurnaceInventory;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\inventory\SimpleTransactionGroup;
use pocketmine\inventory\StonecutterShapelessRecipe;
use pocketmine\item\Item;
use pocketmine\level\format\FullChunk;
use pocketmine\level\format\LevelProvider;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\math\AxisAlignedBB;
use pocketmine\metadata\MetadataValue;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\Byte;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Double;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\Float;
use pocketmine\nbt\tag\Int;
use pocketmine\nbt\tag\String;
use pocketmine\network\protocol\AdventureSettingsPacket;
use pocketmine\network\protocol\AnimatePacket;
use pocketmine\network\protocol\DataPacket;
use pocketmine\network\protocol\EntityEventPacket;
use pocketmine\network\protocol\FullChunkDataPacket;
use pocketmine\network\protocol\Info as ProtocolInfo;
use pocketmine\network\protocol\LoginStatusPacket;
use pocketmine\network\protocol\MessagePacket;
use pocketmine\network\protocol\MovePlayerPacket;
use pocketmine\network\protocol\SetHealthPacket;
use pocketmine\network\protocol\SetSpawnPositionPacket;
use pocketmine\network\protocol\SetTimePacket;
use pocketmine\network\protocol\StartGamePacket;
use pocketmine\network\protocol\TakeItemEntityPacket;
use pocketmine\network\protocol\UpdateBlockPacket;
use pocketmine\network\SourceInterface;
use pocketmine\permission\PermissibleBase;
use pocketmine\permission\PermissionAttachment;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\CallbackTask;
use pocketmine\tile\Sign;
use pocketmine\tile\Spawnable;
use pocketmine\tile\Tile;
use pocketmine\utils\ReversePriorityQueue;
use pocketmine\utils\TextFormat as MT;
use main\debug\Debug;

class tpa implements Listener
{
	private $plugin;
	private $debug;
	
	public function __construct(Plugin $plugin)
	{
		$this->plugin = $plugin;
		$this->debug = new Debug($plugin);
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args)
		{
		if($sender instanceof Player)
		{	
			if(strtolower($command->getName()) == "tpa")
			{
				if(isset($args[0]))
				{
					$name = strtolower($sender->getPlayer()->getName());
						
					if($target = $sender->getServer()->getPlayer($args[0]))
					{
						$targetname = strtolower($sender->getServer()->getPlayer($args[0])->getName());
	
						$sender->getServer()->getPlayer($args[0])->sendMessage(MT::GREEN."$name hat Ihnen eine TP anfrage gesendet | $name has sent you a tpa request");
						$sender->getServer()->getPlayer($args[0])->sendMessage(MT::YELLOW."/tpja zum bestaetigen /tpnein zum ablehnen | /tpja for accept /tpnein for decline");
	
						$sender->sendMessage(MT::GREEN."Tpa Anfrage wurde gesendet! || Tpa request has been sent!");
	
						$this->tpa[$targetname] = $name;
						return true;
					}
					else
					{
						$sender->sendMessage(MT::RED."Spieler exestiert nicht in dieser Welt || Player dont exist in this world");
						return true;
					}
				}
				else
				{
					$sender->sendMessage(MT::RED."Bitte Spieler Namen angeben || Missing player name");
					return true;
				}
			}
				
			if(strtolower($command->getName()) == "tpja")
			{
				$name = strtolower($sender->getName());
	
				if($this->tpa[$name] != null)
				{
					$ziel = $this->tpa[$name];
					$target = $sender->getServer()->getPlayer($ziel);
						
					$pos = new Position($sender->x, $sender->y, $sender->z, $sender->getLevel());
					$target->teleport($pos);
						
					$sender->getServer()->getPlayer($ziel)->sendMessage(MT::GREEN."Tpa Anfrage bestaetigt | Tpa request accepted");
					$sender->sendMessage(MT::GREEN."Tpa Anfrage bestaetigt | Tpa request accepted");
						
					$this->tpa[$name] = null;
					return true;
				}
				return true;
			}
				
			if(strtolower($command->getName()) == "tpnein")
			{
				$name = strtolower($sender->getName());
					
				if($this->tpa[$name] != null)
				{
	
					$ziel = $this->tpa[$name];
						
					$sender->getServer()->getPlayer($ziel)->sendMessage(MT::RED."Tpa Anfrage abgelehnt | Tpa request declined");
					$sender->sendMessage("Tpa Anfrage abgelehnt | Tpa request declined");
						
					$this->tpa[$name] = null;
					return true;
				}
			}
		}
		else
		{
			$sender->sendMessage("Nur im Spiel moeglich | Only in game");
			return true;
		}
	}
}