<?php
	if(!defined('PATH')) {
		define('PATH', sprintf('%s/', dirname(dirname(__FILE__))));
	}
	
	if(is_readable(PATH . '/panel/.security.php')) {
		require(PATH . '/panel/.security.php');
	} else if(is_readable(PATH . '/.security.php')) {
		require(PATH . '/.security.php');
	}
	
	if(is_readable(PATH . '/panel/.config.php')) {
		require(PATH . '/panel/.config.php');
	} else if(is_readable(PATH . '/.config.php')) {
		require(PATH . '/.config.php');
	}
	
	require_once(PATH . '/panel/classes/Session.class.php');
	require_once(PATH . '/panel/classes/Auth.class.php');
	require_once(PATH . '/panel/classes/Database.class.php');
	require_once(PATH . '/panel/classes/DatabaseFactory.class.php');
	require_once(PATH . '/panel/classes/Encryption.class.php');
	require_once(PATH . '/panel/libraries/Sepia/PoParser/Catalog/Catalog.php');
	require_once(PATH . '/panel/libraries/Sepia/PoParser/Catalog/CatalogArray.php');
	require_once(PATH . '/panel/libraries/Sepia/PoParser/Catalog/Entry.php');
	require_once(PATH . '/panel/libraries/Sepia/PoParser/Catalog/EntryFactory.php');
	require_once(PATH . '/panel/libraries/Sepia/PoParser/Catalog/Header.php');
	require_once(PATH . '/panel/libraries/Sepia/PoParser/Parser.php');
	require_once(PATH . '/panel/libraries/Sepia/PoParser/SourceHandler/SourceHandler.php');
	require_once(PATH . '/panel/libraries/Sepia/PoParser/SourceHandler/FileSystem.php');

	use fruithost\Database;
	use \Sepia\PoParser\SourceHandler\FileSystem;
	use \Sepia\PoParser\Parser;
	
	define('TAB', "\t");
	define('BS', '\\');
	define('DS', DIRECTORY_SEPARATOR);
	
	function help() {
		color('yellow', 'Usage:');
		color(null, 'fruithost', false);
		color('green', ' <command>', false);
		color('yellow', ' <args...>');
		
		line();
		color('yellow', 'Commands:');
		
		line();
		color('grey', '═ Globals ═');
		
		color('green', 'version', false);
		color(null, ' - List the version informations');
		
		color('green', 'help', false);
		color(null, ' - Print the help');
		
		color('green', 'status', false);
		color(null, ' - Show teh status of your system');
		
		color('green', 'statistics', false);
		color(null, ' - Show some statistics');
		
		color('green', 'daemon', false);
		color(null, ' - Run the daemon process');
		
		line();
		color('grey', '═ Updates & Upgrades ═');
		
		color('green', 'upgrade');
		color('grey', '  ├ ', false);
		color('yellow', 'core', false);
		color(null, ' - Upgrade the core files');
		color('grey', '  └ ', false);
		color('yellow', '<module>', false);
		color(null, ' - Upgrade the given module name');
		
		color('green', 'update', false);
		color(null, ' - Check for updates');
		
		line();
		color('grey', '═ Modules ═');		
		color('pink', 'If you using ', false);		
		color('red', '@', false);		
		color('pink', ' instead of an name of ', false);	
		color('yellow', '<module>', false);	
		color('pink', ', you can handle all available modules', true);	
		color('green', 'remove ', false);
		color('yellow', '<module>', false);
		color(null, ' - Delete / Deinstall given module');
		
		color('green', 'install ', false);
		color('yellow', '<module>', false);
		color(null, ' - Install given module');
		
		color('green', 'reinstall ', false);
		color('yellow', '<module>', false);
		color(null, ' - Reinstall given module');
		
		color('green', 'enable ', false);
		color('yellow', '<module>', false);
		color(null, ' - Enable the given module');
		
		color('green', 'disable ', false);
		color('yellow', '<module>', false);
		color(null, ' - Disable the given module');
		
		line();
		color('grey', '═ Repositorys ═');
		
		color('green', 'repository');
		color('grey', '  ├ ', false);
		color('yellow', 'add', false);
		color('blue', ' <url>', false);
		color(null, ' - Add a repository URL');
		color('grey', '  ├ ', false);
		color('yellow', 'remove', false);
		color('blue', ' <url>', false);
		color(null, ' - Remove a repository URL');
		color('grey', '  └ ', false);
		color('yellow', 'list', false);
		color(null, ' - List all registred repositorys');
		
		line();
		color('grey', '═ Languages ═');
		
		color('green', 'language');
		color('grey', '  ├ ', false);
		color('yellow', 'list', false);
		color(null, ' - List all available languages');
		color('grey', '  ├ ', false);
		color('yellow', 'scan', false);
		color(null, ' - Scan all files for language attributes');
		color('grey', '  └ ', false);
		color('yellow', 'add', false);
		color('blue', ' <xx_XX>', false);
		color(null, ' - Add a new language (for sample de_DE)');
	}
	
	function version() {
		$version = (object) [
			'panel'		=> file_get_contents(sprintf('%s%s%s%s%s', PATH, DS, 'panel', DS, '.version')),
			'binary'	=> file_get_contents(sprintf('%s%s%s%s%s', PATH, DS, 'bin', DS, '.version'))
		];
		
		color('yellow', 'fruithost', false);
		color(null, ' | The OpenSource Hosting Panel');
		
		color(null, TAB . 'Binary version ', false);
		color('green', $version->binary, false);
		color(null, ', Panel version ', false);
		color('green', $version->panel);
	}
	
	function line() {
		print PHP_EOL;
	}
	
	function getSettings(string $name, mixed $default = NULL) : mixed {
		$result = Database::single('SELECT * FROM `' . DATABASE_PREFIX . 'settings` WHERE `key`=:key LIMIT 1', [
			'key'		=> $name
		]);
		
		if(!empty($result) && !empty($result->value)) {
			// Is Boolean: False
			if(in_array(strtolower($result->value), [
				'off', 'false', 'no'
			])) {
				return false;
			// Is Boolean: True
			} else if(in_array(strtolower($result->value), [
				'on', 'true', 'yes'
			])) {
				return true;
			}
			
			return $result->value;
		}
		
		return $default;
	}
	
	function setSettings(string $name, mixed $value = NULL) {
		if(is_bool($value)) {
			$value = ($value ? 'true' : 'false');
		}
		
		if(Database::exists('SELECT `id` FROM `' . DATABASE_PREFIX . 'settings` WHERE `key`=:key LIMIT 1', [
			'key'		=> $name
		])) {
			Database::update(DATABASE_PREFIX . 'settings', [ 'key' ], [
				'key'			=> $name,
				'value'			=> $value
			]);
		} else {
			Database::insert(DATABASE_PREFIX . 'settings', [
				'id'			=> NULL,
				'key'			=> $name,
				'value'			=> $value
			]);
		}
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
				$color = "\033[1;34m";
			break;
			case 'red';
				$color = "\033[31;31m";
			break;
			case 'grey';
				$color = "\033[90m";
			break;
			case 'pink';
				$color = "\033[35m";
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
		case 'status':
			color('grey', '═ System ═');
			
			#setSettings('DAEMON_TIME_END',		date('Y-m-d H:i:s', time()));
			#setSettings('DAEMON_RUNNING_END',	microtime(true));
			
			$rebooting = getSettings('REBOOT', null);
			if(!empty($rebooting)) {
				color('yellow', 'WARNING: ', false);
				color('white', 'Server will be rebooting now...', false);
				color('blue', ' [' . $rebooting . ']');
				line();
			}
			
			color('yellow', 'Last Daemon:   ', false);
			color('white', getSettings('DAEMON_TIME_END', null));
			line();
			
			color('grey', '═ Git-Repositorys ═');
			foreach([
				'bin',
				'config',
				'panel',
				'placeholder',
				'Installers',
				'themes',
				'modules'
			] AS $directory) {
				if(!file_exists(sprintf('%s%s/.git', PATH, $directory))) {
					continue;
				}
				
				$result = shell_exec(sprintf('cd %s%s/ && git status -s', PATH, $directory));
				
				color('yellow', 'Tracked Repo: ', false);
				color('blue', sprintf('%s%s/', PATH, $directory));
				
				if(empty($result)) {
					color('grey', "   - No changes -");
				} else {
					color('white', str_replace([
						'M ',
						'D ',
						'A ',
						'?? '
					], [
						"   \033[33m*\033[39m ",
						"   \033[31;31m-\033[39m ",
						"   \033[0;32m+\033[39m ",
						"    \033[1;34m?\033[39m ",
					], $result));
				}
			}
		break;
		case 'statistics':
			color('red', 'Statistics currently not available', false);
			color('grey', ' (Under development)');
		break;
		case 'remove':
			color('red', 'Deinstallation currently not available', false);
			color('grey', ' (Under development)');
		break;
		case 'reinstall':
		case 'install':
			if($_SERVER['argc'] === 2) {
				$repositorys = Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys`');
					
				if(count($repositorys) === 0) {
					color('orange', 'No repositorys available.');
				} else {
					color('grey', 'Following modules can be installed:');
					
					foreach(Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys`') AS $entry) {
						line();
						
						// Load GitHub by RAW
						if(preg_match('/github\.com\/([^\/]+)\/(.*)(?:\/)?$/Uis', $entry->url, $matches)) {
							$entry->url = sprintf('https://raw.githubusercontent.com/%s/%s/master', $matches[1], rtrim($matches[2], '/'));
						}
						
						$list		= @file_get_contents(sprintf('%s/modules.list', $entry->url));
						
						if(empty($list)) {
							continue;
						}
						
						color('grey', $entry->url);
						
						$modules	= explode(PHP_EOL, $list);
						
						color('yellow', implode(', ', array_values($modules)));
					}
				}
				
				line();
				return;
			}
			
			$name		= trim($_SERVER['argv'][2]);
			$found		= false;
			$repository = NULL;
			
			if($_SERVER['argv'][1] == 'install') {
				if(Database::exists('SELECT `id` FROM `' . DATABASE_PREFIX . 'modules` WHERE `name`=:name LIMIT 1', [
					'name'			=> $name
				])) {
					color('red', 'Module is already installed!');
					return;
				}
			}
				
			foreach(Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys`') AS $entry) {
				// Load GitHub by RAW
				if(preg_match('/github\.com\/([^\/]+)\/(.*)(?:\/)?$/Uis', $entry->url, $matches)) {
					$entry->url = sprintf('https://raw.githubusercontent.com/%s/%s/master', $matches[1], rtrim($matches[2], '/'));
				}
				
				$list		= @file_get_contents(sprintf('%s/modules.list', $entry->url));
				
				if(empty($list)) {
					continue;
				}
				
				$modules	= explode(PHP_EOL, $list);
				
				if(in_array($name, array_values($modules))) {
					$found		= true;
					$repository	= $entry->url;
					break;
				}
			}
			
			if(!$found || empty($repository)) {
				color('red', sprintf('The module %s was not found!', $name));
				return;
			}
			
			color('grey', sprintf('Fetching %s Module...', $name));
			
			if(preg_match('/github\.com\/([^\/]+)\/(.*)(?:\/)?$/Uis', $repository, $matches)) {
				$package = sprintf('https://raw.githubusercontent.com/%s/%s/master', $matches[1], rtrim($matches[2], '/'));
			} else {
				$package = $repository;
			}
			
			// @ToDo --force argument
			if($_SERVER['argv'][1] == 'install') {
				$repository	= sprintf('%s/modules.packages/%s.zip', $package, $name);
				$content	= file_get_contents($repository);
				
				if(empty($content)) {
					color('orange', 'Broken package.');
					return;
				}
				
				$path = sprintf('%s%s%s%s%s', PATH, DS, 'temp', DS, 'install_' . $name. '.package');
				file_put_contents($path, $content);
				
				$zip = new ZipArchive;
				
				if($zip->open($path) !== TRUE) {
					color('orange', 'Broken package.');
					return;
				} else {
					if(!$zip->extractTo(sprintf('%s%s%s%s', PATH, DS, 'modules', DS))) {
						print "\033[31;31mCan't upgrade: " . $zip->getStatusString() . "\033[39m" . PHP_EOL;
						return;
					}
					
					$zip->close();
					
					$module_path = sprintf('%s%s%s%s%s', PATH, DS, 'modules', DS, $name);
					
					if(file_exists(sprintf('%s/setup/install.php', $module_path))) {
						color('green', '+ Run Install-Script');
						
						try {
							require_once(sprintf('%s/setup/install.php', $module_path));
						} catch(Exception $e) {
							color('red', $e->getMessage());
							color('orange', $e->getTraceAsString());
						}
					}
					
					Database::insert(DATABASE_PREFIX . 'modules', [
						'id'			=> NULL,
						'name'			=> $name,
						'state'			=> 'DISABLED',
						'time_enabled'	=> NULL,
						'time_updated'	=> NULL,
						'time_deleted'	=> NULL
					]);
					
					color('green', 'The module was successfully installed.');
					color(null, 'Please run ', false);
					color('yellow', 'fruithost enable <module>', false);
					color(null, '.');
					
					@unlink($path);
				}
			} else {
				$module_path = sprintf('%s%s%s%s%s', PATH, DS, 'modules', DS, $name);
					
				if(file_exists(sprintf('%s/setup/install.php', $module_path))) {
					color('green', '+ Run Install-Script');
					
					try {
						require_once(sprintf('%s/setup/install.php', $module_path));
					} catch(Exception $e) {
						color('red', $e->getMessage());
						color('orange', $e->getTraceAsString());
					}
				}
			}
		break;
		case 'enable':
			$installed		= [];
			$path			= sprintf('%s%s%s', PATH, DS, 'modules');
			$enabled		= [];
			
			foreach(Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'modules`') AS $module) {
				$enabled[$module->name] = $module;
			}
			
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
			
			if($_SERVER['argc'] === 2) {
				color('grey', 'Following modules are currently disabled:');
				
				foreach(array_keys($installed) AS $index => $module) {
					if(!in_array($module, array_keys($enabled)) || (isset($enabled[$module]) && $enabled[$module]->state === 'DISABLED')) {
						color('yellow', $module, false);
						color(null, ' (' . $installed[$module]->version . ')' . (count(array_keys($installed)) > $index + 1 ? ', ' : ''), false);
					}
				}
				
				line();
				return;
			}
			
			if($_SERVER['argv'][2] == '@') {
				$modules = [];
				
				foreach(array_keys($installed) AS $index => $module) {
					if(!in_array($module, array_keys($enabled)) || (isset($enabled[$module]) && $enabled[$module]->state === 'DISABLED')) {
						$modules[] = $module;
						Database::update(DATABASE_PREFIX . 'modules', 'name', [
							'name'			=> $module,
							'time_enabled'	=> date('Y-m-d H:i:s', time()),
							'state'			=> 'ENABLED'
						]);
					}
				}
				
				color('green', 'Follwing modiles was enabled: ' . implode(', ', $modules));
				return;
			}
			
			if(!isset($installed[$_SERVER['argv'][2]])) {
				color('orange', 'The module ' . $_SERVER['argv'][2] . ' not exists!');
				return;
			}
			
			if(isset($enabled[$_SERVER['argv'][2]]) && $enabled[$_SERVER['argv'][2]]->state === 'ENABLED') {
				color('orange', 'The module is already enabled!');
				return;
			}
			
			if(isset($enabled[$_SERVER['argv'][2]])) {
				color('green', 'The module was successfully enabled.');
				Database::update(DATABASE_PREFIX . 'modules', 'name', [
					'name'			=> $_SERVER['argv'][2],
					'time_enabled'	=> date('Y-m-d H:i:s', time()),
					'state'			=> 'ENABLED'
				]);
				return;
			}
			
			color('green', 'The module was successfully enabled.');
			Database::insert(DATABASE_PREFIX . 'modules', [
				'id'			=> NULL,
				'name'			=> $_SERVER['argv'][2],
				'time_enabled'	=> date('Y-m-d H:i:s', time()),
				'time_updated'	=> NULL,
				'state'			=> 'ENABLED'
			]);
		break;
		case 'disable':
			$installed		= [];
			$path			= sprintf('%s%s%s', PATH, DS, 'modules');
			$enabled		= [];
			
			foreach(Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'modules`') AS $module) {
				$enabled[$module->name] = $module;
			}
			
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
			
			if($_SERVER['argc'] === 2) {
				color('grey', 'Following modules are currently enabled:');
				
				foreach(array_keys($installed) AS $index => $module) {
					if(in_array($module, array_keys($enabled)) && (isset($enabled[$module]) && $enabled[$module]->state === 'ENABLED')) {
						color('yellow', $module, false);
						color(null, ' (' . $installed[$module]->version . ')' . (count(array_keys($installed)) > $index + 1 ? ', ' : ''), false);
					}
				}
				
				line();
				return;
			}
			
			if($_SERVER['argv'][2] == '@') {
				$modules = [];
				
				foreach(array_keys($installed) AS $index => $module) {
					if(!in_array($module, array_keys($enabled)) || (isset($enabled[$module]) && $enabled[$module]->state === 'ENABLED')) {
						$modules[] = $module;
						Database::update(DATABASE_PREFIX . 'modules', 'name', [
							'name'			=> $module,
							'state'			=> 'DISABLED'
						]);
					}
				}
				
				color('green', 'Follwing modiles was disabled: ' . implode(', ', $modules));
				return;
			}
			
			if(!isset($installed[$_SERVER['argv'][2]])) {
				color('orange', 'The module ' . $_SERVER['argv'][2] . ' not exists!');
				return;
			}
			
			if(isset($enabled[$_SERVER['argv'][2]]) && $enabled[$_SERVER['argv'][2]]->state === 'DISABLED') {
				color('orange', 'The module is already disabled!');
				return;
			}
			
			if(isset($enabled[$_SERVER['argv'][2]])) {
				color('green', 'The module was successfully disabled.');
				Database::update(DATABASE_PREFIX . 'modules', 'name', [
					'name'			=> $_SERVER['argv'][2],
					'state'			=> 'DISABLED'
				]);
				return;
			}
			
			color('green', 'The module was successfully disabled.');
			Database::insert(DATABASE_PREFIX . 'modules', [
				'id'			=> NULL,
				'name'			=> $_SERVER['argv'][2],
				'time_enabled'	=> NULL,
				'time_updated'	=> NULL,
				'state'			=> 'DISABLED'
			]);
		break;
		case 'daemon':
			setSettings('DAEMON_TIME_START',		date('Y-m-d H:i:s', time()));
			setSettings('DAEMON_RUNNING_START',		microtime(true));
			
			$enabled	= [];
			$deinstall	= [];
			$ignore		= [
				'modules.list',
				'.gitignore',
				'LICENSE',
				'.git',
				'README.md'
			];
			$path		= sprintf('%s%s%s', PATH, DS, 'modules');
			
			color('yellow', 'Running Daemon...');
			
			$count = 0;
			foreach(Database::fetch('SELECT `username` FROM `' . DATABASE_PREFIX . 'users`') AS $user) {
				if(!file_exists(sprintf('%s%s', HOST_PATH, $user->username))) {
					@mkdir(sprintf('%s%s', HOST_PATH, $user->username));
					++$count;
				}
			}
			
			if($count >= 1) {
				color('white', 'Fixing User-Paths [' . $count . ']');
			}
			
			foreach(Database::fetch('SELECT `name` FROM `' . DATABASE_PREFIX . 'modules` WHERE `state`=\'ENABLED\'') AS $entry) {
				$enabled[] = $entry->name;
			}
			
			foreach(Database::fetch('SELECT `name` FROM `' . DATABASE_PREFIX . 'modules` WHERE `time_deleted` IS NOT NULL') AS $entry) {
				$deinstall[] = $entry->name;
			}
			
			foreach(new \DirectoryIterator($path) AS $info) {
				if($info->isDot()) {
					continue;
				}

				$module = sprintf('%s%s%s', $path, DS, $info->getFilename());
				
				if(in_array(basename($module), $ignore)) {
					/* Do Nothing */
				} else if(in_array(basename($module), $deinstall)) {
					color('red', '~ DEINSTALL ' . $info->getFileName());
					
					if(file_exists(sprintf('%s/setup/deinstall.php', $module))) {
						color('green', '+ Run Uninstall-Script ' . $info->getFileName());
						
						try {
							require_once(sprintf('%s/setup/deinstall.php', $module));
						} catch(Exception $e) {
							color('red', $e->getMessage());
							color('orange', $e->getTraceAsString());
						}
					}
					
					Database::delete(DATABASE_PREFIX . 'modules', [
						'name' => basename($module)
					]);
					
					if(file_exists($module)) {
						foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($module, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) AS $fileinfo) {
							$todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
							$todo($fileinfo->getRealPath());
						}

						rmdir($module);
					}
				} else if(in_array(basename($module), $enabled)) {
					if(file_exists(sprintf('%s/daemon.php', $module))) {
						color('green', '+ Run ' . $info->getFileName());
						
						try {
							require_once(sprintf('%s/daemon.php', $module));
						} catch(Exception $e) {
							color('red', $e->getMessage());
							color('orange', $e->getTraceAsString());
						}
					} else {
						color('yellow', '- Skip ' . $info->getFileName() . ' (No Daemon)');
					}
				} else {
					color('grey', '- Ignore ' . $info->getFileName() . ' (Disabled)');
				}
			}
			
			setSettings('DAEMON_TIME_END',		date('Y-m-d H:i:s', time()));
			setSettings('DAEMON_RUNNING_END',	microtime(true));
			
			// Rebooting
			$rebooting = getSettings('REBOOT', null);
			if(!empty($rebooting)) {
				color('yellow', 'WARNING: ', false);
				color('white', 'Server will be rebooting now...', false);
				color('blue', ' [' . $rebooting . ']');
				setSettings('REBOOT', null);
				shell_exec('/sbin/shutdown -r now');
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
					
					$repositorys = Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys` WHERE `url`=:url', [
						'url'	=> $_SERVER['argv'][3]
					]);
					
					if(count($repositorys) > 0) {
						color('orange', 'Repository already exists!');
					} else {
						color('green', 'Repository added.');
						
						Database::insert(DATABASE_PREFIX . 'repositorys', [
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
					
					$repositorys = Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys` WHERE `url`=:url LIMIT 1', [
						'url'	=> $_SERVER['argv'][3]
					]);
					
					if(count($repositorys) === 0) {
						color('orange', 'Repository not exists!');
					} else {
						color('green', 'Repository removed.');
						
						Database::delete(DATABASE_PREFIX . 'repositorys', [
							'url'	=> $_SERVER['argv'][3]
						]);
					}
				break;
				case 'list':
					$repositorys = Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys`');
					
					if(count($repositorys) === 0) {
						color('orange', 'No repositorys available.');
					} else {
						color('grey', 'Following repositorys are registred:');
						
						foreach(Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys`') AS $entry) {
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
			$repositorys	= Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys`');
			$updateable		= [];
			$packages		= [];
			$conflicts		= [];
					
			if(count($repositorys) === 0) {
				color('orange', 'No repositorys for update available.');
			} else {
				$installed	= [];
				$path		= sprintf('%s%s%s', PATH, DS, 'modules');
				
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
				
				foreach(Database::fetch('SELECT * FROM `' . DATABASE_PREFIX . 'repositorys`') AS $entry) {
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
						
						if(!isset($installed[$name])) {
							continue;
						}
						
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
					
					Database::update(DATABASE_PREFIX . 'repositorys', 'id', [
						'id'			=> $entry->id,
						'time_updated'	=> date('Y-m-d H:i:s', time())
					]);
				}
			}
			
			file_put_contents(sprintf('%s%s%s%s%s', PATH, DS, 'temp', DS, 'update.list'), json_encode($updateable));
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
			$path = sprintf('%s%s%s%s%s', PATH, DS, 'temp', DS, 'update.list');
			
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
					
					$path = sprintf('%s%s%s%s%s', PATH, DS, 'temp', DS, 'update.package');
					file_put_contents($path, $content);
					
					$zip = new ZipArchive;
					
					if($zip->open($path) !== TRUE) {
						color('orange', 'Broken package.');
						return;
					} else {
						if(!$zip->extractTo(sprintf('%s%s%s%s', PATH, DS, 'modules', DS))) {
							print "\033[31;31mCan't upgrade: " . $zip->getStatusString() . "\033[39m" . PHP_EOL;
							return;
						}
						
						$zip->close();
						
						// @ToDo trigger update script from module
						
						Database::update(DATABASE_PREFIX . 'modules', 'name', [
							'name'			=> $_SERVER['argv'][2],
							'time_updated'	=> date('Y-m-d H:i:s', time())
						]);
						
						color('green', 'Done.');
					}
				break;
			}
		break;
		case 'version':
			version();
		break;
		case 'language':
			if($_SERVER['argc'] >= 3) {
				switch($_SERVER['argv'][2]) {
					case 'list':
						$languages = [];
						$directory = sprintf('%spanel/languages/', PATH);
						
						foreach(new \DirectoryIterator($directory) AS $info) {
							if($info->isDot()) {
								continue;
							}
							
							if(preg_match('/(.*)\.po$/Uis', $info->getFileName(), $matches)) {
								$language		= new Parser(new FileSystem($info->getPathName()));
								$parsed			= $language->parse();
								$header			= $parsed->getHeader();
								
								foreach($header->asArray() AS $line) {
									if(preg_match('/Language: (.*)$/Uis', $line, $names)) {
										$languages[$matches[1]] = $names[1];
										break;
									}
								}
							}
						}
						
						color('pink', 'Following Language-Files in ', false);		
						color('red', $directory, false);		
						color('pink', ':');
						
						foreach($languages AS $code => $name) {
							if(!next($languages)) {
								color('grey', '  └ ', false);
							} else {
								color('grey', '  ├ ', false);
							}
							
							color('yellow', $code, false);
							color('blue', ' "' . $name . '"', false);
							color('white', ' (' . $code . '.po)');
						}
					break;
					case 'add':
					case 'scan':
						if($_SERVER['argv'][2] == 'add' && $_SERVER['argc'] === 3) {
							color('orange', 'Please enter an language name!');
							return;
						}
						
						$scanned	= 0;
						$found		= [];
						$it			= new RecursiveIteratorIterator(
							new \RecursiveDirectoryIterator(PATH, \RecursiveDirectoryIterator::SKIP_DOTS),
							\RecursiveIteratorIterator::SELF_FIRST,
							\RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
						);
						
						$it->rewind();
						
						while($it->valid()) {
							if(!$it->isDot() && strpos($it->getSubPath(), '.git') == false) {
								$file = $it->current();
								
								if($file->getExtension() == 'php') {
									$content = file_get_contents($it->key());
									
									# Simple Quote
									preg_match_all('/I18N::(__|get)\(\'([^\'\)]+)\'\)/Uis', $content, $matches);
									
									if(count($matches[2]) > 0) {
										foreach($matches[2] AS $text) {
											if(in_array($text, $found)) {
												continue;
											}
											
											$found[] 	= $text;
										}
									}
									
									# Double Quote
									preg_match_all('/I18N::(__|get)\("([^"\)]+)"\)/Uis', $content, $matches);
									
									if(count($matches[2]) > 0) {
										foreach($matches[2] AS $text) {
											if(in_array($text, $found)) {
												continue;
											}
											
											$found[] 	= $text;
										}
									}
									
									++$scanned;
								}
							}

							$it->next();
						}
						
						$contents = '';
						$contents .= 'msgid ""' . PHP_EOL;
						$contents .= 'msgstr ""' . PHP_EOL;
						$contents .= '"Content-Transfer-Encoding: 8bit\n"' . PHP_EOL;
						$contents .= '"Content-Type: text/plain; charset=UTF-8\n"' . PHP_EOL;
						$contents .= '"Language: $name\n"' . PHP_EOL;
						$contents .= '' . PHP_EOL;

						foreach($found AS $text) {
							$contents .= '' . PHP_EOL;
							$contents .= sprintf('msgid %s', json_encode($text)) . PHP_EOL;
							$contents .= 'msgstr ""' . PHP_EOL;
						}
						
						$directory = sprintf('%spanel/languages/', PATH);
						file_put_contents(sprintf('%s$code.template', $directory), $contents);
						
						color('green', 'Scanned ' . $scanned . ' Files, Found ' . count($found) . ' language Strings.');
						
						if($_SERVER['argv'][2] == 'add' && $_SERVER['argc'] >= 3) {
							$code = $_SERVER['argv'][3];
							file_put_contents(sprintf('%s%s.po', $directory, $code), str_replace('$name', $code, $contents));
							color('green', 'Language ' . $code . ' was successfully added.');
						}
					break;
				}
			} else {
				help();
			}
		break;
		default:
			help();
		break;
	}
?>
