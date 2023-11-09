<?php
	namespace fruithost;
	
	if(is_readable('.DEBUG') || is_readable('../.DEBUG')) {
		define('DEBUG', true);
	}
	
	if(defined('DEBUG') && DEBUG) {
		@ini_set('display_errors', true);
		error_reporting(E_ALL);
	}
	
	define('TAB',	"\t");
	define('BS',	'\\');
	define('DS',	DIRECTORY_SEPARATOR);
	
	class Loader {
		public function __construct() {
			if(defined('DAEMON') && DAEMON || isset($_SERVER['DAEMON']) && !empty($_SERVER['DAEMON'])) {
				if(!defined('DAEMON')) {
					define('DAEMON', true);
				}
				
				if(!defined('PATH')) {
					define('PATH', sprintf('%s/panel/', dirname(dirname(__FILE__))));
				}
			} else {
				if(!defined('PATH')) {
					define('PATH', sprintf('%s/', dirname(__FILE__)));
				}
			}
			
			if($this->readable('.security')) {
				$this->require('.security');
			} else if($this->readable('../.security')) {
				$this->require('../.security');
			} else {
				if(defined('DAEMON') && DAEMON) {
					print "\033[31;31m";
				}
				
				print 'ERROR loading .security ' . PHP_EOL;

				if(defined('DAEMON') && DAEMON) {
					print "\033[39m";
				}
			}
			
			if($this->readable('.mail')) {
				$this->require('.mail');
			} else if($this->readable('../.mail')) {
				$this->require('../.mail');
			}
			
			if($this->readable('.config')) {
				$this->require('.config');
			} else if($this->readable('../.config')) {
				$this->require('../.config');
			} else {
				if(defined('DAEMON') && DAEMON) {
					print "\033[31;31m";
				}
				
				print 'ERROR loading .config.php ' . PHP_EOL;
				
				if(defined('DAEMON') && DAEMON) {
					print "\033[39m";
				}
			}
						
			spl_autoload_register([ $this, 'load' ]);
			
			// @ToDo Hash verify?
			if(isset($_SERVER['MODULE']) && !empty($_SERVER['MODULE'])) {
				if(file_exists($_SERVER['MODULE'])) {
					$this->require('libraries/skoerfgen/ACMECert');
					
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
	
		private function readable(string $file) : bool {
			$path = sprintf('%s%s.php', PATH, $file);
			
			if(!file_exists($path)) {
				return false;
			}
			
			return is_readable($path);
		}
		
		private function require(string $file) {
			$path = sprintf('%s%s.php', PATH, $file);
			
			if(!file_exists($path)) {
				if(defined('DAEMON') && DAEMON) {
					print "\033[31;31m";
				}
				
				print 'ERROR loading: ' . $path . PHP_EOL;
				
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