<?php
	require_once('../panel/.security.php');
	require_once('../panel/config.php');
	require_once('../panel/classes/Database.class.php');
	require_once('../panel/classes/DatabaseFactory.class.php');

	use fruithost\Database;
	
	if(!defined('PATH')) {
		define('PATH', sprintf('%s/', dirname(__FILE__)));
	}
	
	define('TAB', "\t");
	define('BS', '\\');
	define('DS', DIRECTORY_SEPARATOR);
	
	if($_SERVER['argc'] === 1) {
		print "Help..." . PHP_EOL;
		return;
	}
	
	switch($_SERVER['argv'][1]) {
		case 'daemon':
			$enabled	= [];
			$path		= sprintf('%s%s%s', dirname(PATH), DS, 'modules');
			
			print("\033[33mRunning Daemon...\033[39m") . PHP_EOL;
			
			foreach(Database::fetch('SELECT `name` FROM `fh_modules` WHERE `state`=\'ENABLED\'') AS $entry) {
				$enabled[] = $entry->name;
			}
			
			foreach(new \DirectoryIterator($path) AS $info) {
				if($info->isDot()) {
					continue;
				}

				$module = sprintf('%s%s%s', $path, DS, $info->getFilename());
				
				if(in_array(basename($module), $enabled)) {
					if(file_exists(sprintf('%s/daemon.php', $module))) {
						print("\033[0;32m+ Run " . $info->getFileName() . "\033[39m") . PHP_EOL;
						require_once(sprintf('%s/daemon.php', $module));
					} else {
						print("\033[1;33m- Skip " . $info->getFileName() . " (No Daemon)\033[39m") . PHP_EOL;
					}
				} else {
					print("\033[1;33m- Ignore " . $info->getFileName() . " (Disabled)\033[39m") . PHP_EOL;
				}
			}
		break;
		case 'repository':
		
		break;
		case 'update':
		
		break;
		case 'version':
			print file_get_contents('../panel/.version') . PHP_EOL;
		break;
		default:
			print "Help..." . PHP_EOL;
		break;
	}
?>