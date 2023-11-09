<?php
	namespace fruithost;
	
	if(is_readable('.DEBUG') || is_readable('../.DEBUG')) {
		define('DEBUG', true);
	}
	
	if(defined('DEBUG') && DEBUG) {
		@ini_set('display_errors', true);
		error_reporting(E_ALL);
	}
	
	define('BS', '\\');
	define('DS', DIRECTORY_SEPARATOR);
	
	class Loader {
		public function __construct() {
			if(isset($_SERVER['DAEMON']) && !empty($_SERVER['DAEMON'])) {
				define('DAEMON', true);
				
				if(!defined('PATH')) {
					define('PATH', sprintf('%s/panel/', dirname(dirname(__FILE__))));
				}
			} else {
				if(!defined('PATH')) {
					define('PATH', sprintf('%s/', dirname(__FILE__)));
				}
			}
			
			$this->require('libraries/skoerfgen/ACMECert');
			
			if(is_readable('.security.php')) {
				$this->require('.security');
			} else if(is_readable('../.security.php')) {
				$this->require('../.security');
			}
			
			if(is_readable('.mail.php')) {
				$this->require('.mail');
			} else if(is_readable('../.mail.php')) {
				$this->require('../.mail');
			}
			
			if(is_readable('.config.php')) {
				$this->require('.config');
			} else if(is_readable('../.config.php')) {
				$this->require('../.config');
			}
			
			spl_autoload_register([ $this, 'load' ]);
			
			// @ToDo Hash verify?
			if(isset($_SERVER['MODULE']) && !empty($_SERVER['MODULE'])) {
				if(file_exists($_SERVER['MODULE'])) {
					if(is_readable($_SERVER['MODULE'])) {
						require_once($_SERVER['MODULE']);
					} else {
						printf('Error: Module is not readable (%s).', $_SERVER['MODULE']);
					}
				} else {
					printf('Error: Can\'t load module (%s).', $_SERVER['MODULE']);
				}
			}
		}
	
		private function require(string $file) {
			$path = sprintf('%s%s.php', PATH, $file);
			
			if(!file_exists($path)) {
				if(defined('DAEMON') && DAEMON) {
					print "\033[31;31m";
				}
				
				print 'ERROR loading: ' . $path;
				
				if(defined('DAEMON') && DAEMON) {
					print "\033[39m";
				}
				
				return;
			}
			
			require_once($path);
		}
		
		public function load(string $class) {			
			$file			= trim($class, BS);
			$file_array		= explode(BS, $file);
			
			array_shift($file_array);
			array_unshift($file_array, 'classes');
			
			$path			= sprintf('%s%s.class.php', PATH, implode(DS, $file_array));

			if(!file_exists($path)) {
				// Check it's an Library
				$file_array		= explode(BS, $file);
				array_unshift($file_array, 'libraries');
				$path	= sprintf('%s%s.php', PATH, implode(DS, $file_array));
				
				if(!is_readable($path)) {
					if(defined('DAEMON') && DAEMON) {
						print "\033[31;31m";
					}
						print 'Error accessing Library: ' . $path . PHP_EOL;
				
					if(defined('DAEMON') && DAEMON) {
						print "\033[39m";
					}
					return;
				}
				
				if(file_exists($path)) {
					require_once($path);
					return;
				}
				// Check it's an Library on an module!
				/*} else if(preg_match('/\/module\//Uis', $_SERVER['REQUEST_URI'])) {
					$file_array		= explode(BS, $file);
					array_unshift($file_array, 'www');
					array_unshift($file_array, str_replace('module', 'modules', $_SERVER['REQUEST_URI']));
					$path	= sprintf('%s%s.php', dirname(PATH), implode(DS, $file_array));
					require_once($path);
					return;
				}*/
				
				if(defined('DAEMON') && DAEMON) {
					print "\033[31;31m";
				}
				
				print 'Error Loading Library: ' . $path . PHP_EOL;
				
				if(defined('DAEMON') && DAEMON) {
					print "\033[39m";
				}
				
				return;
			}
			
			require_once($path);
		}
	}
	
	new Loader();
?>