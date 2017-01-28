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
	use pocketmine\server;
	use pocketmine\IPlayer;
	use pocketmine\utils\Config;
	use pocketmine\plugin\PluginBase;
	class Main extends PluginBase implements Listener{


		const AND = "&&";
		const OR = "||";
		const NONE = "IDK what to put here";


		public function onEnable(){
				$this->cmds = [];
				$this->getServer()->getPluginManager()->registerEvents($this, $this);
				$this->saveDefaultConfig();
				foreach($this->getConfig()->getAll() as $cmd => $cmds) {
						$cmd  = substr($cmd, 1); // Removing the "/"
						$this->cmds[$cmd] = new \pocketmine\command\PluginCommand($cmd, $this);
						$this->cmds[$cmd]->setUsage(isset($cmds["usage"]) ? $cmds["usage"] :"/$cmd [arguments]");
						$this->cmds[$cmd]->setDescription(isset($cmds["desc"]) ? $cmds["desc"] : "Runs function $cmd");
						$this->getServer()->getCommandMap()->register($cmd, $this->cmds[$cmd]);
				}
		}



		public function onCommand(CommandSender $sender, Command $command, $label, array $args){
			switch($command->getName()){
				case "function":
					if(isset($args[0])){
							switch($args[0]){
								case "c":
								case "create":
								if(count($args) < 2){
									return false;
								}else{
									$cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
									$default = ["tell {sender} This is default command, modify it with /function setc <function> <Command number> <command...>{console}"];
									$cfg->set("/".$args[1], $default);
									$cfg->save();
									$this->reloadConfig();
									$this->cmds[$args[1]] = new \pocketmine\command\PluginCommand($args[1], $this);
									$this->cmds[$args[1]]->setUsage("/{$args[1]} [arguments]");
									$this->cmds[$args[1]]->setDescription("Runs function $args[1].");
									$this->getServer()->getCommandMap()->register($args[1], $this->cmds[$args[1]]);
									$sender->sendMessage("§4§l[Functions]§r§4 Function " . $args[1] . " has been created! You can edit it on the config or by doing /function ac <function> <command number> <command...>.");
								}
								return true;
							break;
							case "ac":
							case "addc":
							case "acmd":
							case "addcmd":
								$cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
								if(is_array($cfg->get("/".$args[1]))){
									unset($args[0]);
									$funcname = $args[1];
									unset($args[1]);
									$funccmds = $cfg->get("/".$funcname);
									array_push($funccmds, implode(" ", $args));
									$cfg->set("/".$funcname, $funccmds);
									$cfg->save();
									$sender->sendMessage("§4§l[Functions]§r§4 Command ". implode(" ", $args)."  for function " . $funcname . " has been added!");
									$this->reloadConfig();
								}else{
									$sender->sendMessage("§4§l[Functions]§r§4 Function " . $args[1] . " not found. Create it with /function c " . $args[1]);
								}
								return true;
							break;
							case "rc":
							case "resetcmd":
								$cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
								$func = $cfg->get("/".$args[1]);
								$oldcmd = $func[$args[2]];
								$func[$args[2]] = "nothink";
								$sender->sendMessage("§4§l[Functions]§r§4 Removed command (" . $oldcmd . ") of function " . $args[1]);
								$cfg->set("/".$args[1], $func);
								$cfg->save();
								$this->reloadConfig();
								return true;
							break;
							case "rmc":
							case "removecmd":
								$cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
								$func = $cfg->get("/".$args[1]);
								if(!is_array($func)){
									$sender->sendMessage("§l§4[Function]§r§4 Function $args[1] does not exist! Create it with /function create $args[1]");
								}
								unset($func[$args[2]-1]);
								$cfg->set("/".$args[1], $func);
								$cfg->save();
								$this->reloadConfig();
								$sender->sendMessage("§4§l[Functions]§r§4 Removed command $args[2] from function $args[1]");
								return true;
							break;
							case "read":
								$cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
								$i = 1;
								$sender->sendMessage("§4§l[Functions] Commands for function " . $args[1] . ":");
								$funcname = $args[1];
								$func = $cfg->get("/".$funcname);
								foreach($func as $funccmds){
									$sender->sendMessage("Command " . $i . ": /" . $funccmds);
									$i += 1;
								}
								return true;
							break;
							case "delete":
							case "remove":
							case "del":
							case "rm":
								$cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
								$cfg->remove("/", $args[1]);
								$cfg->save();
								$this->reloadConfig();
								$this->getServer()->getCommandMap()->unregister($this->cmds[$args[1]]);
								unset($this->cmds[$args[1]]);
								$sender->sendMessage("§4§l[Functions]§r§4 Succefully removed function " . $args[1] . ".");
								return true;
							break;
							case "setdesc":
							case "description":
							case "setdescription":
							case "desc":
								$cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
								$func = $cfg->get("/" . $args[1]);
								$name = $args[1];
								unset($args[0], $args[1]);
								$func["desc"] = implode(" ", $args);
								$cfg->set("/" . $name, $func);
								$this->cmds[$name]->setDescription(implode(" ", $args));
								$cfg->save();
								$this->reloadConfig();
								$sender->sendMessage("§4§l[Functions]§r§4 Succefully set description of function " . $name . " to ". implode(" ", $args) .".");
							break;
							case "setuse":
							case "usage":
							case "setsuage":
							case "use":
								$cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
								$func = $cfg->get("/" . $args[1]);
								$name = $args[1];
								unset($args[0], $args[1]);
								$func["usage"] = implode(" ", $args);
								$cfg->set("/" . $name, $func);
								$this->cmds[$name]->setUsage(implode(" ", $args));
								$cfg->save();
								$this->reloadConfig();
								$sender->sendMessage("§4§l[Functions]§r§4 Succefully set usage of function " . $name . " to ". implode(" ", $args) .".");
							break;
							case "setc":
							case "setcmd":
							case "cmd":
							case "command":
							case "setcommand":
								$cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
								$func = $cfg->get("/" . $args[1]);
								$name = $args[1];
								$id = $args[2];
								unset($args[0], $args[1], $args[2]);
								$func[$id] = implode(" ", $args);
								$cfg->set("/" . $name, $func);
								$cfg->save();
								$this->reloadConfig();
								$sender->sendMessage("§4§l[Functions]§r§4 Succefully set command $id of function " . $name . " to ". implode(" ", $args) .".");
							break;
							default:
								$sender->sendMessage("§4§l[Functions]§r§4 Help for Function: \n------------------------- \n- /function create <function>:§6 Create a function \n- /function setcmd <function> <command id> <command>:§6 Sets a command on a function\n- /function usage <function> <usage>:§6 Sets the usage of a function  \n- /function desc <function> <description>:§6 Sets the description of a function \n- /function ac <function> <command>:§6 Add a command to a function  \n- /function rc <function> <command id> <command>:§6 Reset a command from a function\n- /function rmc <function> <command id> <command>:§6 Remove a command from a function\n- /function read <function>:§6 Read all commands of a function \n- /function delete <function>:§6 Deletes a function \n---------------------------\n");
								return true;
							break;
						}
					}else{
								$sender->sendMessage("§4§l[Functions]§r§4 Help for Function: \n------------------------- \n- /function create <function>:§6 Create a function \n- /function setcmd <function> <command id> <command>:§6 Sets a command on a function\n- /function usage <function> <usage>:§6 Sets the usage of a function  \n- /function desc <function> <description>:§6 Sets the description of a function \n- /function ac <function> <command>:§6 Add a command to a function  \n- /function rc <function> <command id> <command>:§6 Reset a command from a function\n- /function rmc <function> <command id> <command>:§6 Remove a command from a function\n- /function read <function>:§6 Read all commands of a function \n- /function delete <function>:§6 Deletes a function \n---------------------------\n");
					}
					return true;
					break;
			}
				$cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
				$cmds = $cfg->get("/" . $command->getName());
				var_dump($cmds);
				if(is_array($cmds)){
						$funcname = $command->getName();
						if($sender->isPermissionSet("func.use." . $funcname) ? $sender->hasPermission("func.use." . $funcname) : $sender->hasPermission("func.use.default")){
						foreach($cmds as $cmdkey => $cmd){
								if($cmd !== "nothink" && is_int($cmdkey)){ // CMD exists
										// Basic Player's infos
										$cmd = str_ireplace("{sender}", $sender->getName(), $cmd);
										$cmd = str_ireplace("{isop}", $sender->isOp() ? "true" : "false", $cmd);
										$cmd = str_ireplace("{usage}", $this->cmds[$command->getName()]->getUsage(), $cmd);
										$cmd = str_ireplace("{desc}", $this->cmds[$command->getName()]->getDescription(), $cmd);
										if($sender instanceof \pocketmine\command\ConsoleCommandSender) {
											$cmd = str_ireplace("{level}", $this->getServer()->getDefaultLevel()->getName(), $cmd);
											$cmd = str_ireplace("{x}", $this->getServer()->getDefaultLevel()->getSafeSpawn()->x, $cmd);
											$cmd = str_ireplace("{y}", $this->getServer()->getDefaultLevel()->getSafeSpawn()->y, $cmd);
											$cmd = str_ireplace("{z}", $this->getServer()->getDefaultLevel()->getSafeSpawn()->z, $cmd);
											$cmd = str_ireplace("{yaw}", 0, $cmd);
											$cmd = str_ireplace("{pitch}", 0, $cmd);
										} else {
											$cmd = str_ireplace("{level}", $sender->getLevel()->getName(), $cmd);
											$cmd = str_ireplace("{x}", $sender->x, $cmd);
											$cmd = str_ireplace("{y}", $sender->y, $cmd);
											$cmd = str_ireplace("{z}", $sender->z, $cmd);
											$cmd = str_ireplace("{yaw}", $sender->yaw, $cmd);
											$cmd = str_ireplace("{pitch}", $sender->pitch, $cmd);
										}

										// Arguments
										if(!isset($args[0])){
											$cmd = str_ireplace("{args[0]}", "", $cmd);
										} else {
											$cmd = str_ireplace("{args[0]}", $args[0], $cmd);
										}
										if(!isset($args[1])){
												$cmd = str_ireplace("{args[1]}", "", $cmd);
										} else {
												$cmd = str_ireplace("{args[1]}", $args[1], $cmd);
										}
										if(!isset($args[2])){
												$cmd = str_ireplace("{args[2]}", "", $cmd);
										} else {
												$cmd = str_ireplace("{args[2]}", $args[2], $cmd);
										}
										if(!isset($args[3])){
												$cmd = str_ireplace("{args[3]}", "", $cmd);
										} else {
												$cmd = str_ireplace("{args[3]}", $args[3], $cmd);
										}
										$cmd = str_ireplace("{args}", json_encode($args), $cmd);






										// If statements
										$execute = true;
										preg_match_all("/\{if:(.+?;)then:(.+?)\}/", $cmd, $matches);
										foreach($matches[0] as $key => $match) {
											// And conditions
											if(strpos($matches[1][$key], "&&") > 0) {
												$substatements = explode(self::AND, $matches[1][$key]);
												$state = self::AND;
											}

											// Or Statements
											elseif(strpos($matches[1][$key], "||") > 0) {
												$substatements = explode(self::OR, $matches[1][$key]);
												$state = self::OR;
											} 


											// No statement, returning an array.
											else {
												$substatements = [$matches[1][$key]];
												$state = self::NONE;
											}

											$current = [];
											
											foreach($substatements as $statement) {
												switch(true) {
													case preg_match_all("/(.+?)=(.+?);/", $statement, $values) > 0: // Check if two values are equal
													$this->getLogger()->debug($values[1][0]." == ".$values[2][0]);
													if($values[1][0] == $values[2][0]) {
														array_push($current, true);
													} elseif($values[1][0] !== $values[2][0]) {
														array_push($current, false);
													}
													break;
													case preg_match_all("/(.+?)!=(.+?);/", $statement, $values) > 0: // Check if two values are not equal
													$this->getLogger()->debug($values[1][0]." !== ".$values[2][0]);
													if($values[1][0] !== $values[2][0]) {
														array_push($current, true);
													} elseif($values[1][0] == $values[2][0]) {
														array_push($current, false);
													}
													break;
													case preg_match("/(.+?)>(.+?);/", $statement, $values) > 0: // Check if a value is more than an another
													$this->getLogger()->debug($values[1][0]." > ".$values[2][0]);
													if($values[1][0] > $values[2][0]) {
														array_push($current, true);
													} elseif(!($values[1][0] > $values[2][0])) {
														array_push($current, false);
													}
													break;
													case preg_match("/(.+?)<(.+?);/", $statement, $values) > 0: // Check if a value is less than an another
													$this->getLogger()->debug($values[1][0]." < ".$values[2][0]);
													if($values[1][0] < $values[2][0]) {
														array_push($current, true);
													} elseif(!($values[1][0] < $values[2][0])) {
														array_push($current, false);
													}
													break;
												}
											}
											if(in_array(true, $current) && $state == self::OR) {
												$current = true;
												$this->getLogger()->debug($current ? "true" : "false" . $state);
											} elseif(in_array(false, $current) && $state == self::AND) {
												$current = false;
												$this->getLogger()->debug($current ? "true" : "false" . $state);
											} elseif($state == self::OR) {
												$current = $current[0];
												$this->getLogger()->debug($current ? "true" : "false" . $state);
											}
											switch($matches[2][$key]) {
												// if executing
												case "exec":
												case "execute":
												if($current) {
													$execute = true;
												}
												break;
												case "!exec":
												case "!execute":
												if($current) {
													$execute = false;
												}
												break;

												// Changing executor
												case "asop":
												case "op":
												$cmd .= "{op}";
												$cmd = str_ireplace("{console}", "", $cmd);
												break;
												case "asconsole":
												case "console":
												$cmd .= "{console}";
												$cmd = str_ireplace("{op}", "", $cmd);
												break;
												case "asdefault":
												case "default":
												$cmd = str_ireplace("{op}", "", $cmd);
												$cmd = str_ireplace("{console}", "", $cmd);
												break;
												default:
												if(preg_match("/^as(.+?)$/", $cmd, $mat)) {
													if(!is_null($this->getServer()->getPlayer($mat[1][0]))) {
														$sender = $this->getServer()->getPlayer($mat[1][0]);
														$cmd = str_ireplace("{op}", "", $cmd);
														$cmd = str_ireplace("{console}", "", $cmd);
													}
												} else {
													$this->getLogger()->warning("{$matches[2][$key]} is not a valid action in command $cmdkey.");
												}
												break;
											}
											$cmd = str_replace($match, "", $cmd); // If there are multiple time the same command, won't be executed multiple times.
										}

										// Sending command
										if($execute) {
											if(strpos($cmd, "{console}")){
													$cmd = str_ireplace("{console}", "", $cmd);
													$cmd = str_ireplace("{op}", "", $cmd);
													$this->getServer()->dispatchCommand(new ConsoleCommandSender(), $cmd);
											}elseif(strpos($cmd, "{op}")){
												$cmd = str_ireplace("{op}", "", $cmd);
												if ($sender->isOp()){
													$this->getServer()->dispatchCommand($sender, $cmd);
												} else {
													$sender->setOp(true);
													$this->getServer()->dispatchCommand($sender, $cmd);
													$sender->setOp(false);
												}
											}else{
												$this->getServer()->dispatchCommand($sender, $cmd);
											}
										} else {
											$this->getLogger()->debug("Prevented command $cmd from running.");
										}
									}
								}
						} else {
							$sender->sendMessage("You do not have permission to use this function.");
						}
						return true;
					}
				}
			}
