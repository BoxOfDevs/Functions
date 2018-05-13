<?php

namespace BoxOfDevs\Functions;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\Loader;
use pocketmine\command\PluginCommand;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener
{
	private $cmds = [];
	const AND = "&&";
	const OR = "||";
	const NONE = "IDK what to put here";

	public function onEnable() : void
	{
		$this->cmds = [];
		$this->getServer()->getPluginManager()->registerEvents(($this) , $this);
		$this->saveDefaultConfig();
		foreach ($this->getConfig()->getAll() as $cmd => $cmds) {
			$cmd = substr($cmd , 1); // Removing the "/"
			$this->cmds[$cmd] = new PluginCommand($cmd , $this);
			$this->cmds[$cmd]->setUsage(isset($cmds["usage"]) ? $cmds["usage"] : "/$cmd [arguments]");
			$this->cmds[$cmd]->setDescription(isset($cmds["desc"]) ? $cmds["desc"] : "Runs function $cmd");
			$this->getServer()->getCommandMap()->register($cmd , $this->cmds[$cmd]);
		}
		$this->getLogger()->info("Plugin Enabled.");
	}

	public function onCommand(CommandSender $sender , Command $command , string $label , array $args) : bool
	{
		switch ($command->getName()) {
			case "func":
			case "function":
				if (isset($args[0])) {
					switch ($args[0]) {
						/*
						Creates a function
						/function create <function name>
						*/
						case "S*":
						case "create":
							if (count($args) >= 2) {
								$cfg = new Config($this->getDataFolder() . "config.yml" , Config::YAML);
								$default = ["tell {sender} This is default command, modify it with /function setc <function> <Command number> <command...>{console}"];
								$cfg->set("/" . $args[1] , $default);
								$cfg->save();
								$this->reloadConfig();
								$this->cmds[$args[1]] = new PluginCommand($args[1] , $this);
								$this->cmds[$args[1]]->setUsage("/{$args[1]} [arguments]");
								$this->cmds[$args[1]]->setDescription("Runs function $args[1].");
								$this->getServer()->getCommandMap()->register($args[1] , $this->cmds[$args[1]]);
								$sender->sendMessage("§4§l[Functions]§r§4 Function " . $args[1] . " has been created! You can edit it on the config or by doing /function ac <function> <command number> <command...>.");
							}
							break;
						/*
						Adds a command to a function
						/function addcmd <function name> <command>
						*/
						case "ac":
						case "addc":
						case "acmd":
						case "addcmd":
							if (count($args) >= 3) {
								$cfg = new Config($this->getDataFolder() . "config.yml" , Config::YAML);
								if (is_array($cfg->get("/" . $args[1]))) {
									unset($args[0]);
									$funcname = $args[1];
									unset($args[1]);
									$funccmds = $cfg->get("/" . $funcname);
									array_push($funccmds , implode(" " , $args));
									$cfg->set("/" . $funcname , $funccmds);
									$cfg->save();
									$sender->sendMessage("§4§l[Functions]§r§4 Command " . implode(" " , $args) . "  for function " . $funcname . " has been added!");
									$this->reloadConfig();
								} else {
									$sender->sendMessage("§4§l[Functions]§r§4 Function " . $args[1] . " not found. Create it with /function c " . $args[1]);
								}
							}
							break;
						/*
						Resets a command from a function
						/function reset <function name> <command id>
						*/
						case "rc":
						case "resetcmd":
							if (count($args) >= 3) {
								$cfg = new Config($this->getDataFolder() . "config.yml" , Config::YAML);
								if (is_array($cfg->get("/" . $args[1]))) {
									$func = $cfg->get("/" . $args[1]);
									$oldcmd = $func[$args[2]];
									$func[$args[2]] = "nothink";
									$sender->sendMessage("§4§l[Functions]§r§4 Removed command (" . $oldcmd . ") of function " . $args[1]);
									$cfg->set("/" . $args[1] , $func);
									$cfg->save();
									$this->reloadConfig();
								} else {
									$sender->sendMessage("§4§l[Functions]§r§4 No function found with name " . $args[1] . ".");
								}
							}
							break;
						/*
						Removes a command from a function
						/function removecmd <function name> <command id>
						*/
						case "rmc":
						case "removecmd":
							if (count($args) >= 3) {
								$cfg = new Config($this->getDataFolder() . "config.yml" , Config::YAML);
								if (is_array($cfg->get("/" . $args[1]))) {
									$func = $cfg->get("/" . $args[1]);
									if (!is_array($func)) {
										$sender->sendMessage("§l§4[Function]§r§4 Function $args[1] does not exist! Create it with /function create $args[1]");
									}
									unset($func[$args[2] - 1]);
									$cfg->set("/" . $args[1] , $func);
									$cfg->save();
									$this->reloadConfig();
									$sender->sendMessage("§4§l[Functions]§r§4 Removed command $args[2] from function $args[1]");
								} else {
									$sender->sendMessage("§4§l[Functions]§r§4 No function found with name " . $args[1] . ".");
								}
							}
							break;
						/*
						Reads a function's commands
						/function read <function name>
						*/
						case "read":
							if (count($args) >= 2) {
								$cfg = new Config($this->getDataFolder() . "config.yml" , Config::YAML);
								if (is_array($cfg->get("/" . $args[1]))) {
									$i = 1;
									$sender->sendMessage("§4§l[Functions] Commands for function " . $args[1] . ":");
									$funcname = $args[1];
									$func = $cfg->get("/" . $funcname);
									foreach ($func as $funccmds) {
										$sender->sendMessage("Command " . $i . ": /" . $funccmds);
										$i += 1;
									}
								} else {
									$sender->sendMessage("§4§l[Functions]§r§4 No function found with name " . $args[1] . ".");
								}
							}
							break;
						/*
						Deletes a function
						/function delete <function name>
						*/
						case "delete":
						case "remove":
						case "del":
						case "rm":
							if (count($args) >= 2) {
								$cfg = new Config($this->getDataFolder() . "config.yml" , Config::YAML);
								if (is_array($cfg->get("/" . $args[1]))) {
									$cfg->remove("/" , $args[1]);
									$cfg->save();
									$this->reloadConfig();
									$this->cmds[$args[1]]->unregister($this->getServer()->getCommandMap());
									unset($this->cmds[$args[1]]);
									$sender->sendMessage("§4§l[Functions]§r§4 Succefully removed function " . $args[1] . ".");
								} else {
									$sender->sendMessage("§4§l[Functions]§r§4 No function found with name " . $args[1] . ".");
								}
							}
							break;
						/*
						Sets the usage of a command
						/function setdesc <function name> <description>
						*/
						case "setdesc":
						case "description":
						case "setdescription":
						case "desc":
							if (count($args) >= 2) {
								$cfg = new Config($this->getDataFolder() . "config.yml" , Config::YAML);
								if (is_array($cfg->get("/" . $args[1]))) {
									$func = $cfg->get("/" . $args[1]);
									$name = $args[1];
									unset($args[0] , $args[1]);
									$func["desc"] = implode(" " , $args);
									$cfg->set("/" . $name , $func);
									$this->cmds[$name]->setDescription(implode(" " , $args));
									$cfg->save();
									$this->reloadConfig();
									$sender->sendMessage("§4§l[Functions]§r§4 Succefully set description of function " . $name . " to " . implode(" " , $args) . ".");
								} else {
									$sender->sendMessage("§4§l[Functions]§r§4 No function found with name " . $args[1] . ".");
								}
							}
							break;
						/*
						Sets the usage of a command
						/function usage <function name> <usage>
						*/
						case "setuse":
						case "usage":
						case "setsuage":
						case "use":
							if (count($args) >= 2) {
								$cfg = new Config($this->getDataFolder() . "config.yml" , Config::YAML);
								if (is_array($cfg->get("/" . $args[1]))) {
									$func = $cfg->get("/" . $args[1]);
									$name = $args[1];
									unset($args[0] , $args[1]);
									$func["usage"] = implode(" " , $args);
									$cfg->set("/" . $name , $func);
									$this->cmds[$name]->setUsage(implode(" " , $args));
									$cfg->save();
									$this->reloadConfig();
									$sender->sendMessage("§4§l[Functions]§r§4 Succefully set usage of function " . $name . " to " . implode(" " , $args) . ".");
								} else {
									$sender->sendMessage("§4§l[Functions]§r§4 No function found with name " . $args[1] . ".");
								}
							}
							break;
						/*
						Sets a command by it's id on a function'
						/function setcmd <function name> <command id> <command>
						*/
						case "setc":
						case "setcmd":
						case "cmd":
						case "command":
						case "setcommand":
							if (count($args) >= 3) {
								$cfg = new Config($this->getDataFolder() . "config.yml" , Config::YAML);
								if (is_array($cfg->get("/" . $args[1]))) {
									$func = $cfg->get("/" . $args[1]);
									$name = $args[1];
									$id = $args[2];
									unset($args[0] , $args[1] , $args[2]);
									$func[$id] = implode(" " , $args);
									$cfg->set("/" . $name , $func);
									$cfg->save();
									$this->reloadConfig();
									$sender->sendMessage("§4§l[Functions]§r§4 Succefully set command $id of function " . $name . " to " . implode(" " , $args) . ".");
								} else {
									$sender->sendMessage("§4§l[Functions]§r§4 No function found with name " . $args[1] . ".");
								}
							}
							break;

						/*
						Import a function from an exported .func
						/function import <func file name> [password]
						*/
						case "import":
						case "createfrom":
						case "cfrom":
						case "createf":
							if (count($args) >= 2) {
								if (file_exists($this->getDataFolder() . $args[1] . ".func")) {
									$cfg = new Config($this->getDataFolder() . "config.yml" , Config::YAML);
									$content = file_get_contents($this->getDataFolder() . $args[1] . ".func");
									if (substr($content , 0 , 10) == "1.1.3PWD?1" && !isset($args[2])) { //Password is required but no password provided
										$sender->sendMessage("§l§4[Functions]§r§4 This function is encrypted using a password. Please enter the password to import it.");

										return false;
									} elseif (substr($content , 0 , 10) == "1.1.3PWD?1" && isset($args[2])) {
										$this->getLogger()->debug("Encrypting password to decrypt function...");
										$pwd = str_split(hash("sha512" , $args[2]));
									} elseif (substr($content , 0 , 10) == "1.1.3PWD?0") {
										$pwd = str_split(hash("sha512" , "default encryption"));
									} else {
										$sender->sendMessage("§4§l[Functions]§r§4 Functions cannot decrypt this outdated/corrupted function file.");

										return true;
									}
									// Reencrypting to decode
									for ($i = 0 ; $i < 128 ; $i++) {
										$pwd[$i] = ord($pwd[$i]);
									}
									$content = substr($content , 10);
									$chars = str_split($content , 2);
									$i = 127;
									foreach ($chars as $key => $char) {
										$chars[$key] = chr(hexdec($char) - $pwd[$i]); // Encrypting so it's not editable using a text editor.
										$this->getLogger()->debug(hexdec($char) - $pwd[$i] . " Pwd: " . $pwd[$i] . " ANSIIed: " . hexdec($char) . " Return: " . $chars[$key]);
										$i--;
										if ($i == -1 || $i < -1) {
											$i = 127;
										}
									}
									$chars = implode("" , $chars);
									$this->getLogger()->debug($chars);
									$default = @json_decode($chars , true);
									if ($default == null) {
										$sender->sendMessage("§l§4[Functions]§r§4 Incorect password. Please retry.");
									}
									$name = $default["name"];
									unset($default["name"]);
									$cfg->set("/" . $name , $default);
									$cfg->save();
									$this->reloadConfig();
									$this->cmds[$name] = new PluginCommand($name , $this);
									$this->cmds[$name]->setUsage(isset($default["usage"]) ? $default["usage"] : "/$name [arguments]");
									$this->cmds[$name]->setDescription(isset($default["desc"]) ? $default["desc"] : "Runs function $name");
									$this->getServer()->getCommandMap()->register($name , $this->cmds[$name]);
									$sender->sendMessage("§4§l[Functions]§r§4 Function " . $name . " has been succefully imported from " . $args[1] . ".func! You can now take a look at it using /function read $name.");
								}
							}
							break;

						/*
						Export a function to a .func
						/function export <function name> [password]
						*/
						case "export":
						case "expt":
							if (count($args) >= 2) {
								if (file_exists($this->getDataFolder() . $args[1] . ".func")) $sender->sendMessage("File with name " . $args[1] . ".func already exists. Overwriting....");
								$cfg = new Config($this->getDataFolder() . "config.yml" , Config::YAML);
								$pwd = str_split(hash("sha512" , "default encryption"));
								if (is_array($cfg->get("/" . $args[1]))) {
									if (isset($args[2])) { // Set password to prevent from leaking.
										$pwd = str_split(hash("sha512" , $args[2]));
									}
									// Reencrypting the SHA512 string to less bruteforce
									for ($i = 0 ; $i < 128 ; $i++) {
										$pwd[$i] = ord($pwd[$i]);
									}
									$chars = str_split(json_encode(array_merge($cfg->get("/" . $args[1]) , ["name" => $args[1]])));
									$i = 127;
									foreach ($chars as $key => $char) {
										$num = ord($char) + $pwd[$i];
										$hex = dechex($num);
										if (strlen($hex) < 2) {
											$hex = "0" . $hex;
										}
										$this->getLogger()->debug(ord($char) + $pwd[$i] . " Pwd: " . $pwd[$i] . " ANSIIed: " . ord($char) . " = " . $char . " Does: " . $hex . " len: " . strlen($hex));
										$chars[$key] = dechex($num); // Encrypting so it's not editable using a text editor.
										$i--;
										if ($i == -1 || $i < -1) {
											$i = 127;
										}
									}
									$chars = implode("" , $chars);
									if (isset($args[2])) {
										$chars = "1.1.3PWD?1" . $chars;
									} else {
										$chars = "1.1.3PWD?0" . $chars;
									}
									file_put_contents($this->getDataFolder() . $args[1] . ".func" , $chars);
									$sender->sendMessage("§4§l[Functions]§r§4 Function " . $args[1] . " has been succefully exported to " . $args[1] . ".func! You can now share it to any other server" . (isset($args[2]) ? " using password " . $args[2] : "") . ".");
								} else {
									$sender->sendMessage("§4§l[Functions]§r§4 No function found with name " . $args[1] . ".");
								}
							}
							break;
						default:
							$sender->sendMessage("§4----------------------------{§d§lFunctions§r §aHelp Page§4}----------------------------");
							$sender->sendMessage("§l§c»§r " . TextFormat::DARK_GREEN . "/function create <function> §l§b»§r " . TextFormat::WHITE . "Create a function.");
							$sender->sendMessage("§l§c»§r " . TextFormat::DARK_GREEN . "/function setcmd <function> <commandID> <command> §l§b»§r " . TextFormat::WHITE . "Sets a command on a function.");
							$sender->sendMessage("§l§c»§r " . TextFormat::DARK_GREEN . "/function usage <function> <usage> §l§b»§r " . TextFormat::WHITE . "Sets the usage of a function.");
							$sender->sendMessage("§l§c»§r " . TextFormat::DARK_GREEN . "/function desc <function> <description> §l§b»§r " . TextFormat::WHITE . "Sets the description of a function.");
							$sender->sendMessage("§l§c»§r " . TextFormat::DARK_GREEN . "/function ac <function> <command> §l§b»§r " . TextFormat::WHITE . "Add a command to a function.");
							$sender->sendMessage("§l§c»§r " . TextFormat::DARK_GREEN . "/function rc <function> <commandID> <command> §l§b»§r " . TextFormat::WHITE . "Reset a command from a function.");
							$sender->sendMessage("§l§c»§r " . TextFormat::DARK_GREEN . "/function rmc <function> <commandID> <command> §l§b»§r " . TextFormat::WHITE . "Remove a command from a function.");
							$sender->sendMessage("§l§c»§r " . TextFormat::DARK_GREEN . "/function read <function> §l§b»§r " . TextFormat::WHITE . "Read all commands of a function.");
							$sender->sendMessage("§l§c»§r " . TextFormat::DARK_GREEN . "/function delete <function> §l§b»§r " . TextFormat::WHITE . "Deletes a function.");
							$sender->sendMessage("§4----------------------------------------------------------------------------");							return true;
							break;
					}
				} else {
					$sender->sendMessage("§4----------------------------{§d§lFunctions§r §aHelp Page§4}----------------------------");
					$sender->sendMessage("§l§c»§r " . TextFormat::DARK_GREEN . "/function create <function> §l§b»§r " . TextFormat::WHITE . "Create a function.");
					$sender->sendMessage("§l§c»§r " . TextFormat::DARK_GREEN . "/function setcmd <function> <commandID> <command> §l§b»§r " . TextFormat::WHITE . "Sets a command on a function.");
					$sender->sendMessage("§l§c»§r " . TextFormat::DARK_GREEN . "/function usage <function> <usage> §l§b»§r " . TextFormat::WHITE . "Sets the usage of a function.");
					$sender->sendMessage("§l§c»§r " . TextFormat::DARK_GREEN . "/function desc <function> <description> §l§b»§r " . TextFormat::WHITE . "Sets the description of a function.");
					$sender->sendMessage("§l§c»§r " . TextFormat::DARK_GREEN . "/function ac <function> <command> §l§b»§r " . TextFormat::WHITE . "Add a command to a function.");
					$sender->sendMessage("§l§c»§r " . TextFormat::DARK_GREEN . "/function rc <function> <commandID> <command> §l§b»§r " . TextFormat::WHITE . "Reset a command from a function.");
					$sender->sendMessage("§l§c»§r " . TextFormat::DARK_GREEN . "/function rmc <function> <commandID> <command> §l§b»§r " . TextFormat::WHITE . "Remove a command from a function.");
					$sender->sendMessage("§l§c»§r " . TextFormat::DARK_GREEN . "/function read <function> §l§b»§r " . TextFormat::WHITE . "Read all commands of a function.");
					$sender->sendMessage("§l§c»§r " . TextFormat::DARK_GREEN . "/function delete <function> §l§b»§r " . TextFormat::WHITE . "Deletes a function.");
					$sender->sendMessage("§4----------------------------------------------------------------------------");
				}

				return true;
				break;
		}

		// Executing commands
		$cfg = new Config($this->getDataFolder() . "config.yml" , Config::YAML);
		$cmds = $cfg->get("/" . $command->getName());
		if (is_array($cmds)) {
			$funcname = $command->getName();
			if ($sender->isPermissionSet("func.use." . $funcname) ? $sender->hasPermission("func.use." . $funcname) : $sender->hasPermission("func.use.default")) {
				foreach ($cmds as $cmdkey => $cmd) {
					if ($cmd !== "nothink" && is_int($cmdkey)) { // CMD exists
						// Basic Player's infos
						$cmd = str_ireplace("{sender}" , $sender->getName() , $cmd);
						$cmd = str_ireplace("{isop}" , $sender->isOp() ? "true" : "false" , $cmd);
						$cmd = str_ireplace("{usage}" , $this->cmds[$command->getName()]->getUsage() , $cmd);
						$cmd = str_ireplace("{desc}" , $this->cmds[$command->getName()]->getDescription() , $cmd);
						if ($sender instanceof ConsoleCommandSender) {
							$cmd = str_ireplace("{level}" , $this->getServer()->getDefaultLevel()->getName() , $cmd);
							$cmd = str_ireplace("{x}" , $this->getServer()->getDefaultLevel()->getSafeSpawn()->x , $cmd);
							$cmd = str_ireplace("{y}" , $this->getServer()->getDefaultLevel()->getSafeSpawn()->y , $cmd);
							$cmd = str_ireplace("{z}" , $this->getServer()->getDefaultLevel()->getSafeSpawn()->z , $cmd);
							$cmd = str_ireplace("{yaw}" , 0 , $cmd);
							$cmd = str_ireplace("{pitch}" , 0 , $cmd);
						} elseif ($sender instanceof Player) {
							$cmd = str_ireplace("{level}" , $sender->getLevel()->getName() , $cmd);
							$cmd = str_ireplace("{x}" , $sender->x , $cmd);
							$cmd = str_ireplace("{y}" , $sender->y , $cmd);
							$cmd = str_ireplace("{z}" , $sender->z , $cmd);
							$cmd = str_ireplace("{yaw}" , $sender->yaw , $cmd);
							$cmd = str_ireplace("{pitch}" , $sender->pitch , $cmd);
						}
						// Arguments
						if (!isset($args[0])) {
							$cmd = str_ireplace("{args[0]}" , "" , $cmd);
						} else {
							$cmd = str_ireplace("{args[0]}" , $args[0] , $cmd);
						}
						if (!isset($args[1])) {
							$cmd = str_ireplace("{args[1]}" , "" , $cmd);
						} else {
							$cmd = str_ireplace("{args[1]}" , $args[1] , $cmd);
						}
						if (!isset($args[2])) {
							$cmd = str_ireplace("{args[2]}" , "" , $cmd);
						} else {
							$cmd = str_ireplace("{args[2]}" , $args[2] , $cmd);
						}
						if (!isset($args[3])) {
							$cmd = str_ireplace("{args[3]}" , "" , $cmd);
						} else {
							$cmd = str_ireplace("{args[3]}" , $args[3] , $cmd);
						}
						$cmd = str_ireplace("{args}" , json_encode($args) , $cmd);
						// If statements
						$execute = true;
						preg_match_all("/\{if:(.+?;)then:(.+?)\}/" , $cmd , $matches);
						foreach ($matches[0] as $key => $match) {
							// And conditions
							if (strpos($matches[1][$key] , "&&") > 0) {
								$substatements = explode(self:: AND , $matches[1][$key]);
								$state = self:: AND;
							} // Or Statements
							elseif (strpos($matches[1][$key] , "||") > 0) {
								$substatements = explode(self:: OR , $matches[1][$key]);
								$state = self:: OR;
							} // No statement, returning an array.
							else {
								$substatements = [$matches[1][$key]];
								$state = self::NONE;
							}
							$current = [];
							foreach ($substatements as $statement) {
								switch (true) {
									case preg_match_all("/(.+?)=(.+?);/" , $statement , $values) > 0: // Check if two values are equal
										$this->getLogger()->debug($values[1][0] . " == " . $values[2][0]);
										if ($values[1][0] == $values[2][0]) {
											array_push($current , true);
										} elseif ($values[1][0] !== $values[2][0]) {
											array_push($current , false);
										}
										break;
									case preg_match_all("/(.+?)!=(.+?);/" , $statement , $values) > 0: // Check if two values are not equal
										$this->getLogger()->debug($values[1][0] . " !== " . $values[2][0]);
										if ($values[1][0] !== $values[2][0]) {
											array_push($current , true);
										} elseif ($values[1][0] == $values[2][0]) {
											array_push($current , false);
										}
										break;
									case preg_match("/(.+?)>(.+?);/" , $statement , $values) > 0: // Check if a value is more than an another
										$this->getLogger()->debug($values[1][0] . " > " . $values[2][0]);
										if ($values[1][0] > $values[2][0]) {
											array_push($current , true);
										} elseif (!($values[1][0] > $values[2][0])) {
											array_push($current , false);
										}
										break;
									case preg_match("/(.+?)<(.+?);/" , $statement , $values) > 0: // Check if a value is less than an another
										$this->getLogger()->debug($values[1][0] . " < " . $values[2][0]);
										if ($values[1][0] < $values[2][0]) {
											array_push($current , true);
										} elseif (!($values[1][0] < $values[2][0])) {
											array_push($current , false);
										}
										break;
								}
							}
							if (in_array(true , $current) && $state == self:: OR) {
								$current = true;
								$this->getLogger()->debug($current ? "true" : "false" . $state);
							} elseif (in_array(false , $current) && $state == self:: AND) {
								$current = false;
								$this->getLogger()->debug($current ? "true" : "false" . $state);
							} elseif ($state == self::NONE) {
								$current = $current[0];
								$this->getLogger()->debug($current ? "true" : "false" . $state);
							}
							switch ($matches[2][$key]) {
								// If executing
								case "exec":
								case "execute":
									if ($current) {
										$execute = true;
									}
									break;
								case "!exec":
								case "!execute":
									if ($current) {
										$execute = false;
									}
									break;
								// Changing executor
								case "asop":
								case "op":
									$cmd .= "{op}";
									$cmd = str_ireplace("{console}" , "" , $cmd);
									break;
								case "asconsole":
								case "console":
									$cmd .= "{console}";
									$cmd = str_ireplace("{op}" , "" , $cmd);
									break;
								case "asdefault":
								case "default":
									$cmd = str_ireplace("{op}" , "" , $cmd);
									$cmd = str_ireplace("{console}" , "" , $cmd);
									break;
								default:
									if (preg_match("/^as(.+?)$/" , $cmd , $mat)) {
										if (!is_null($this->getServer()->getPlayer($mat[1][0]))) {
											$sender = $this->getServer()->getPlayer($mat[1][0]);
											$cmd = str_ireplace("{op}" , "" , $cmd);
											$cmd = str_ireplace("{console}" , "" , $cmd);
										}
									} else {
										$this->getLogger()->warning("{$matches[2][$key]} is not a valid action in command $cmdkey.");
									}
									break;
							}
							$cmd = str_replace($match , "" , $cmd); // If there are multiple time the same command, won't be executed multiple times.
						}
						// Sending command
						if ($execute) {
							if (strpos($cmd , "{console}")) {
								$cmd = str_ireplace("{console}" , "" , $cmd);
								$cmd = str_ireplace("{op}" , "" , $cmd);
								$this->getServer()->dispatchCommand(new ConsoleCommandSender() , $cmd);
							} elseif (strpos($cmd , "{op}")) {
								$cmd = str_ireplace("{op}" , "" , $cmd);
								if ($sender->isOp()) {
									$this->getServer()->dispatchCommand($sender , $cmd);
								} else {
									$sender->setOp(true);
									$this->getServer()->dispatchCommand($sender , $cmd);
									$sender->setOp(false);
								}
							} else {
								$this->getServer()->dispatchCommand($sender , $cmd);
							}
						} else {
							$this->getLogger()->debug("Prevented command $cmd from running.");
						}
					}
				}
			} else {
				$sender->sendMessage("You do not have permission to use this function.");
			}
		}

		return true;
	}

	/*
	Converts an int to an unicode char.
	@param     $char    int
	@return string
	*/
	public function from_unicode(int $char) : string
	{
		return pack("C*" , $char);
		/*
		$dex = dechex($char);
		if(strlen($dex) < 4){
			for($i = strlen($dex); $i < 4; $i++){
				$dex = "0" . $dex; // Adding 0s if not 4 chars dec. 
			}
		}
		return json_decode('{"t":"\u' . $dex . '"}', true)["t"];
		*/
	}

	/*
	Converts an unicode char to an int
	Taken from http://randomchaos.com/documents/?source=php_and_unicode
	@param     $char    string
	@return int
	*/
	public function to_unicode(string $str) : int
	{
		return unpack("C*" , $str)[1];
		/*
		$unicode = array();        
        $values = array();
        $lookingFor = 1;
		for($i = 0; $i < strlen( $str ); $i++ ){
			$thisValue = ord( $str[ $i ] );
			if($thisValue < 128 ) $unicode[] = $thisValue;
			else{
				if(count( $values ) == 0 ) $lookingFor = ( $thisValue < 224 ) ? 2 : 3;
				$values[] = $thisValue;
				if( count( $values ) == $lookingFor ){
					$number = ( $lookingFor == 3 ) ?
					( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
					( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );
					$unicode[] = $number;
                    $values = array();
                    $lookingFor = 1;
				}
			}
		}
		return $unicode;
		*/
	}
}
