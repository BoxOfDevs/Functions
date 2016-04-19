<?php
namespace Ad5001\Functions;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\command\Loader;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\katana\Console;
use pocketmine\server;
use pocketmine\IPlayer;
use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{
  public function onEnable(){
	  $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }
  public function onLoad(){
    }
  public function onCommand(CommandSender $sender, Command $command, $label, array $args){
    switch($command->getName()){
		case "function":
     	if(isset($args[0])) {
		    switch($args[0]){
				case "c":
				case "create":
				if(count($args) < 2) {
					return false;
				} else {
					 $cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
					  $default = ["op", "tell {sender} This is default command, modify it with /function setc <function> <Command number> <command...>","nothink", "nothink", "nothink", "nothink"];
					  $cfg->set($args[1], $default);
                      $cfg->save();
					  $sender->sendMessage("§4§l[Functions] Function " . $args[1] . " has been created! You can edit it on the config or by doing /function sc <function> <command number> <command...>.");
			    }
					 return true;
					 break;
				case "setcmd":
				  $sender->sendMessage("§4§l[Functions] Use /function sc <function> <command number> <command>! It's more speedy but your way work too");
				case "sc":
				#if($cfg->get($args[1] ==! null) {
					$cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
				     unset($args[0]);
					 $funcname = $args[1];
					 unset($args[1]);
				     $cmdid = $args[2];
					 unset($args[2]);
					 $funccmds = $cfg->get($funcname);
					 $funccmds[$cmdid + 1] = implode(" ", $args);
				     $cfg->set($funcname, $funccmds);
					 $cfg->save();
					 $sender->sendMessage("Command " . $cmdid . " for function " . $funcname . " has been change to: /" . implode(" ", $args));
				#} else {
				#	$sender->sendMessage("§4§l[Functions] Function " . $args[1] . " not found. Create it with /function c " . $args[1]);
				#}
					 return true;
					 break;
				case "rc":
				case "removecmd":
				     $cfg = new Config($this->getDataFolder() . "config.yaml", Config::YAML);
					 $func = $cfg->get($args[1]);
					 $oldcmd = $func[$args[0]+1];
					 unset($func[$args[0]+1]);
				     $sender->sendMessage("§4§l[Functions] Removed command (" . $oldcmd . ") of function " . $args[1]);
					 return true;
					 break;
				case "read":
				     $cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
				     $i = 1;
					 $sender->sendMessage("§4§l[Functions] Commands for function " . $args[1] . ":");
					 $funcname = $args[0];
					 $func = $cfg->get($funcname);
					 $default = $func[0];
					 unset($func[0]);
					 foreach($func as $funccmds) {
						 array_push($cmdname, $i);
						 $sender->sendMessage("Command " . $i . ": /" . $funccmds);
						 $i = $i + 1;
				      }
					  $sender->sendMessage("Default: " . $default);
					 return true;
					 break;
			   default:
				      $sender->sendMessage("§4§l[Functions] Help for Function: \n- /function create <function>:§6 Create a function \n- /function sc <function> <command id> <command>:§6 Modify a command a function \n- /function rc <function> <command id> <command>:§6 Remove a command from a function");
					  return true;
					  break;
			}
			} else {
				$sender->sendMessage("§4§l[Functions] Help for Function: \n- /function create <function>:§6 Create a function \n- /function sc <function> <command id> <command>:§6 Modify a command a function \n- /function rc <function> <command id> <command>:§6 Remove a command from a function");
			}
			return true;
			break;
  }
  }
  public function onCommandPreProcess(PlayerCommandPreprocessEvent $event) {
		    $cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
			$args = $event->getMessage();
			$sender = $event->getPlayer();
			$cmds = explode(" ", $args);
			$args = explode("/", $args[0]);
			$cmds = $cfg->get($args[0]);
		    if($cmds[0] ===! "op" or !$cmds[0] ===! "perm" or $cmds[0] ===! "true" or $cmds[0] ===! "console"){
			} else {
				$event->setCancelled();
				$funcname = $args[0];
				switch($cmds[0]) {
					case "op":
					    if($sender->isOp()) {
							unset($cmds[0]);
							foreach($cmds as $cmd) {
						          if ($cmd === "nothink") {
								  } else {
									  $this->getServer()->dispatchCommand($sender, $cmd);
								  }
				           }
						} else {
								$sender->sendMessage("§4You must be OP to use this command");
						}
						return true;
						break;
					case "perm":
					    if($sender->hasPermission("func.use.func")) {
							unset($cmds[0]);
							foreach($cmds as $cmd) {
						          if ($cmd === "nothink") {
								  } else {
									  $this->getServer()->dispatchCommand($sender, implode("", $cmd));
								  }
				           }
						} else {
								$sender->sendMessage("§4You don't have the permission to use this command");
						}
					case "true":
							unset($cmds[0]);
							foreach($cmds as $cmd) {
						          if ($cmd === "nothink") {
								  } else {
									  $this->getServer()->dispatchCommand($sender, implode("", $cmd));
								  }
				           }
					case "console":
					    if(!$sender instanceof Player) {
							unset($cmds[0]);
							foreach($cmds as $cmd) {
						          if ($cmd === "nothink") {
								  } else {
									  $this->getServer()->dispatchCommand($sender, implode("", $cmd));
								  }
				           }
						} else {
								$sender->sendMessage("§4You must run this in console");
						}
					default:
					     $sender->sendMessage("§4§l[Functions] Default value not recognized. Changed to op.");
						 $cmds = $cfg->get($args[0]);
						 $cmds[0] = "op";
						 $cfg->set($args[0], $cmds);
					     $cfg->save();
					     if($sender->isOp()) {
							unset($cmds[0]);
							foreach($cmds as $cmd) {
						          if ($cmd === "nothink") {
								  } else {
									  $this->getServer()->dispatchCommand($sender, implode("", $cmd));
								  }
				           }
						}
				}
			}
	}
  }