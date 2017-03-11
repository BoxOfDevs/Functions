<?php
$server = proc_open(PHP_BINARY . " src/pocketmine/PocketMine.php --no-wizard", [
//$server = proc_open("./start.sh --no-wizard", [
	0 => ["pipe", "r"],
	1 => ["pipe", "w"],
	2 => ["pipe", "w"]
], $pipes);
if(!is_resource($server)){
	die('Failed to create process');
}
fwrite($pipes[0], "plugins\nstop\n\n");
fclose($pipes[0]);
while(!feof($pipes[1])){
	echo fgets($pipes[1]);
}
fclose($pipes[1]);
fclose($pipes[2]);
echo "\n\nReturn value: ". proc_close($server) ."\n";
if(count(glob("crashdumps/CrashDump*.log")) === 0){
	echo "The functions plugin is currently working well.\nOur jenkins system doesn't currently build phar files.\nTo download the plugin, get it from source.\n";
	exit(0);
}else{
	echo "The functions plugin has a syntax error.\nIt will be fixed whenever the developers have a chance.\nPlease be patient and wait for them to fix it.\n";
	exit(1);
}
