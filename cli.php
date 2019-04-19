<?php
	if(!defined('PATH')) {
		define('PATH', sprintf('%s/', dirname(__FILE__)));
	}
	
	require_once(dirname(PATH) . '/panel/.security.php');
	require_once(dirname(PATH) . '/panel/config.php');
	require_once(dirname(PATH) . '/panel/classes/Database.class.php');
	require_once(dirname(PATH) . '/panel/classes/DatabaseFactory.class.php');

	use fruithost\Database;
	
	define('TAB', "\t");
	define('BS', '\\');
	define('DS', DIRECTORY_SEPARATOR);
	
	function help() {
		print "Help..." . PHP_EOL;
	}
	
	function version() {
		print 'fruithost Version ' . file_get_contents(sprintf('%s%s%s%s%s', dirname(PATH), DS, 'panel', DS, '.version')) . PHP_EOL;
	}
	
	function line() {
		print PHP_EOL;
	}
	
	function color($color, $message, $break = true) {
		switch($color) {
			case 'yellow':
				$color = "\033[33m";
			break;
			case 'green';
				$color = "\033[0;32m";
			break;
			case 'blue';
				$color = "\033[34m";
			break;
			case 'red';
				$color = "\033[31;31m";
			break;
			case 'grey';
				$color = "\033[90m";
			break;
			case 'orange';
				$color = "\033[38;5;202m";
			break;
			default:
				$color = "";
			break;
		}
		
		printf("%s%s\033[39m", $color, $message);
		
		if($break) {
			line();
		}
	}
	
	if($_SERVER['argc'] === 1) {
		help();
		return;
	}
	
	switch($_SERVER['argv'][1]) {
		case 'daemon':
			$enabled	= [];
			$path		= sprintf('%s%s%s', dirname(PATH), DS, 'modules');
			
			color('yellow', 'Running Daemon...');
			
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
						color('green', '+ Run ' . $info->getFileName());
						require_once(sprintf('%s/daemon.php', $module));
					} else {
						color('yellow', '- Skip ' . $info->getFileName() . ' (No Daemon)');
					}
				} else {
					color('grey', '- Ignore ' . $info->getFileName() . ' (Disabled)');
				}
			}
		break;
		case 'repository':
			if($_SERVER['argc'] === 2) {
				color('yellow', 'Usage:');
				color(null, 'fruithost repository', false);
				color('green', ' <action>', false);
				color('yellow', ' <url>');
				
				line();
				color('yellow', 'Actions:');
				
				color('green', 'add', false);
				color(null, ' - Add the repository with given', false);
				color('yellow', ' <url>');
				
				color('green', 'remove', false);
				color(null, ' - Remove given repository', false);
				color('yellow', ' <url>');
				
				color('green', 'list', false);
				color(null, ' - List all available repositorys');
				return;
			}
			
			switch($_SERVER['argv'][2]) {
				case 'add':
					if($_SERVER['argc'] === 3) {
						color('orange', 'Please enter an repository URL!');
						return;
					}
					
					$repositorys = Database::fetch('SELECT * FROM `fh_repositorys` WHERE `url`=:url', [
						'url'	=> $_SERVER['argv'][3]
					]);
					
					if(count($repositorys) > 0) {
						color('orange', 'Repository already exists!');
					} else {
						color('green', 'Repository added.');
						
						Database::insert('fh_repositorys', [
							'id'			=> null,
							'url'			=> $_SERVER['argv'][3],
							'time_updated'	=> NULL
						]);
					}
				break;
				case 'remove':
					if($_SERVER['argc'] === 3) {
						color('orange', 'Please enter an repository URL!');
						return;
					}
					
					$repositorys = Database::fetch('SELECT * FROM `fh_repositorys` WHERE `url`=:url LIMIT 1', [
						'url'	=> $_SERVER['argv'][3]
					]);
					
					if(count($repositorys) === 0) {
						color('orange', 'Repository not exists!');
					} else {
						color('green', 'Repository removed.');
						
						Database::delete('fh_repositorys', [
							'url'	=> $_SERVER['argv'][3]
						]);
					}
				break;
				case 'list':
					$repositorys = Database::fetch('SELECT * FROM `fh_repositorys`');
					
					if(count($repositorys) === 0) {
						color('orange', 'No repositorys available.');
					} else {
						color('grey', 'Following repositorys are registred:');
						
						foreach(Database::fetch('SELECT * FROM `fh_repositorys`') AS $entry) {
							color('yellow', TAB . '- ' . $entry->url);
						}
					}
				break;
				default:
					color('orange', 'Unknown repository command.');
				break;
			}
		break;
		case 'update':
			$repositorys	= Database::fetch('SELECT * FROM `fh_repositorys`');
			$updateable		= [];
			$packages		= [];
			$conflicts		= [];
					
			if(count($repositorys) === 0) {
				color('orange', 'No repositorys for update available.');
			} else {
				$installed	= [];
				$path		= sprintf('%s%s%s', dirname(PATH), DS, 'modules');
				
				foreach(new \DirectoryIterator($path) AS $info) {
					if($info->isDot()) {
						continue;
					}

					$module = sprintf('%s%s%s', $path, DS, $info->getFilename());
					
					if(file_exists(sprintf('%s/module.package', $module))) {
						$info = file_get_contents(sprintf('%s/module.package', $module));
						
						if(empty($info)) {
							continue;
						}
						
						$installed[basename($module)] = json_decode($info);
					}
				}
				
				foreach(Database::fetch('SELECT * FROM `fh_repositorys`') AS $entry) {
					line();
					color('yellow', 'Load ' . $entry->url);
					
					// Load GitHub by RAW
					if(preg_match('/github\.com\/([^\/]+)\/(.*)(?:\/)?$/Uis', $entry->url, $matches)) {
						$entry->url = sprintf('https://raw.githubusercontent.com/%s/%s/master', $matches[1], rtrim($matches[2], '/'));
					}
					
					$list		= @file_get_contents(sprintf('%s/modules.list', $entry->url));
					
					if(empty($list)) {
						color('red', 'Bad repository!');
						color('yellow', 'modules.list', false);
						color(null, 'not exists.');
						continue;
					}
					
					$modules	= explode(PHP_EOL, $list);
					$loaded		= 0;
					
					foreach($modules AS $name) {
						$info = file_get_contents(sprintf('%s/%s/module.package', $entry->url, $name));
						
						if(!isset($conflicts[$name])) {
							$conflicts[$name] = [];
						}
						
						$conflicts[$name][] = $entry;
						
						++$loaded;
						
						if(isset($packages[$name])) {
							color('red', 'WARNING: ', false);
							color('orange', 'package conflict!');
							color(null, 'The module', false);
							color('yellow', ' ' . $name . ' ', false);
							color(null, 'already exists and will be overwritten.');
							
							color('grey', 'Following repositorys has these conflict:');
							
							foreach($conflicts[$name] AS $index => $repository) {
								color('grey', TAB . '[' . $index . ', #' . $repository->id . '] ', false);
								color('yellow', $repository->url);
							}
						}
						
						$packages[$name] = $info;
						
						if(empty($info)) {
							color(null, 'Checking ', false);
							color('grey', $name, false);
							color('yellow', ' [' . $loaded . '/' . count($modules) . ']');
							continue;
						}
						
						$color = 'orange';
						
						$check	= $installed[$name];
						$remote	= json_decode($info);
						
						if(!empty($check) && !empty($remote) && isset($remote->version) &&  isset($check->version) && version_compare($remote->version, $check->version, '>')) {
							$updateable[$name]	= $remote;
							$color				= 'green';
						}
						
						color(null, 'Checking ', false);
						color($color, $name, false);
						color('yellow', ' [' . $loaded . '/' . count($modules) . ']');
						
						if(!empty($check) && isset($check->version)) {
							color('grey', ' > Installed: ' . $check->version);
						}
						
						if(!empty($remote) && isset($remote->version)) {
							color('grey', ' > Current: ' . $remote->version);
						}
					}
				}
			}
			
			file_put_contents(sprintf('%s%s%s%s%s', dirname(PATH), DS, 'temp', DS, 'update.list'), json_encode($updateable));
			line();
			color('green', 'Found ' . count($updateable) . ' related Update' . (count($updateable) === 1 ? '' : 's'));
			
			if(count($updateable) >= 1) {
				color(null, 'Please run ', false);
				color('yellow', 'fruithost upgrade <module>', false);
				color(null, '.');
			} else {
				color(null, 'Your System is ', false);
				color('yellow', 'up to date', false);
				color(null, '!');
			}
			
			line();
			
			// @ToDo check Core
		break;
		case 'upgrade':
			$path = sprintf('%s%s%s%s%s', dirname(PATH), DS, 'temp', DS, 'update.list');
			
			if(!file_exists($path)) {
				color(null, 'No updates available.');
				color(null, 'Please run', false);
				color('yellow', ' fruithost update ', false);
				color(null, 'first!');
				return;
			}
			
			if($_SERVER['argc'] === 2) {
				color('yellow', 'Usage:');
				color(null, 'fruithost upgrade', false);
				color('green', ' <target>');
				
				line();
				color('yellow', 'Targets:');
				
				color('green', 'core', false);
				color(null, ' - The core files');
				
				color('green', '<module>', false);
				color(null, ' - ', false);
				color('yellow', '<name> ', false);
				color(null, 'of an module');
				return;
			}
			
			switch($_SERVER['argv'][2]) {
				case 'core':
					color('red', 'Core can\'t upgrade currently!', false);
					color('grey', ' (Under development)');
				break;
				default:
					$updateable = file_get_contents($path);
					
					if(empty($updateable)) {
						color('orange', 'Broken update list.');
						color(null, 'Please run ', false);
						color('yellow', 'fruithost update', false);
						color(null, '!');
						return;
					}
					
					$updateable = json_decode($updateable);
					
					if(empty($updateable->{$_SERVER['argv'][2]})) {
						color('orange', 'The module is unknown or has no upgrades!');
						return;
					}
					
					$module = $updateable->{$_SERVER['argv'][2]};
					
					color(null, 'Upgrade the module ', false);
					color('yellow', $module->name, false);
					color(null, ' to ', false);
					color('green', 'Version ' . $module->version, false);
					color(null, '...');
					color(null, 'Do you want to continue? [Y/n]');
					
					if(rtrim(fgets(STDIN), PHP_EOL) !== 'Y') {
						color(null, 'Canceled.');
						return;
					}
					
					color(null, 'Run Upgrade...');
					
					// Get RAW from GitHub
					if(preg_match('/github\.com\/([^\/]+)\/(.*)(?:\/)?$/Uis', $module->repository, $matches)) {
						$package = sprintf('https://raw.githubusercontent.com/%s/%s/master', $matches[1], rtrim($matches[2], '/'));
					} else {
						$package = $module->repository;
					}
					
					$module->repository = sprintf('%s/modules.packages/%s.zip', $package, $_SERVER['argv'][2]);
					$content = file_get_contents($module->repository);
					
					if(empty($content)) {
						color('orange', 'Broken package.');
						return;
					}
					
					$path = sprintf('%s%s%s%s%s', dirname(PATH), DS, 'temp', DS, 'update.package');
					file_put_contents($path, $content);
					
					$zip = new ZipArchive;
					
					if($zip->open($path) !== TRUE) {
						color('orange', 'Broken package.');
						return;
					} else {
						if(!$zip->extractTo(sprintf('%s%s%s%s', dirname(PATH), DS, 'modules', DS))) {
							print "\033[31;31mCan't upgrade: " . $zip->getStatusString() . "\033[39m" . PHP_EOL;
							return;
						}
						
						$zip->close();
						
						// @ToDo trigger update script from module
						color('green', 'Done.');
					}
				break;
			}
		break;
		case 'version':
			version();
		break;
		default:
			help();
		break;
	}
?>