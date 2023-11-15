<?php
	namespace fruithost;
	
	use \Sepia\PoParser\SourceHandler\FileSystem;
	use \Sepia\PoParser\Parser;
	
	class Translator {
		public function __construct() {
			if($_SERVER['argc'] >= 3) {
				switch($_SERVER['argv'][2]) {
					case 'list':
						$this->list();
					break;
					case 'scan':
						$this->scan();
					break;
					case 'add':
						if($_SERVER['argv'][2] == 'add' && $_SERVER['argc'] === 3) {
							color('orange', 'Please enter an language name!');
							return;
						}
						
						$this->scan();
						
						/*if($_SERVER['argv'][2] == 'add' && $_SERVER['argc'] >= 3) {
							$code = $_SERVER['argv'][3];
							file_put_contents(sprintf('%s%s.po', $directory, $code), str_replace('$name', $code, $contents));
							color('green', 'Language ' . $code . ' was successfully added.');
						}*/
					break;
				}
			} else {
				help();
			}
		}
		
		public function list() {
			$languages = [];
			$directory = sprintf('%s/languages/', PATH);
			
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
		}
		
		public function scan() {
			$count	= 0;
			$files  = [
				'core'		=> $this->scanPath(dirname(PATH)),
				'modules'	=> []
			];
			
			foreach(new \DirectoryIterator(sprintf('%s/modules', dirname(PATH))) AS $info) {
				if($info->isDot()) {
					continue;
				}
				
				$files['modules'][$info->getFilename()] = $this->scanPath(sprintf('%s/modules/%s%s', dirname(PATH), DS, $info->getFilename()));
				$count += count($files['modules'][$info->getFilename()]);
			}
			
			color('green', 'Scanned ' . count($files['core']) . ' Core-Files and ' . $count . ' Module-Files on ' . count($files['modules']) . ' Modules.');
			
			// Merge all Strings to one array for online translation
			$strings = [];
			foreach($files['core'] AS $index => $file) {
				foreach($file['strings'] AS $string) {
					if(in_array($string, $strings)) {
						continue;
					}
					
					$strings[] = $string;
				}
			}
			
			foreach($files['modules'] AS $index => $modules) {
				foreach($modules AS $file) {
					foreach($file['strings'] AS $string) {
						if(in_array($string, $strings)) {
							continue;
						}
						
						$strings[] = $string;
					}
				}
			}
			
			color('green', 'Completely found ' . count($strings) . ' Strings');
			
			// Fetch old Translations
			
			// Make Translation requests
			
			// Merge translated strings to $files with translation-array
			
			// Build .po files
			$strings = [];
			foreach($files['core'] AS $index => $file) {
				foreach($file['strings'] AS $string) {
					if(in_array($string, $strings)) {
						continue;
					}
					
					$strings[] = $string;
				}
			}
			
			$this->createPO($strings, sprintf('%s/languages/', PATH));
			
			foreach($files['modules'] AS $index => $modules) {
				$strings = [];
				
				foreach($modules AS $file) {
					foreach($file['strings'] AS $string) {
						if(in_array($string, $strings)) {
							continue;
						}
						
						$strings[] = $string;
					}
				}
				
				$this->createPO($strings, sprintf('%s/modules/%s/languages/', dirname(PATH), $index));
			}
		}
		
		public function createPO($strings, $directory) {
			$contents = '';
			$contents .= 'msgid ""' . PHP_EOL;
			$contents .= 'msgstr ""' . PHP_EOL;
			$contents .= '"Content-Transfer-Encoding: 8bit\n"' . PHP_EOL;
			$contents .= '"Content-Type: text/plain; charset=UTF-8\n"' . PHP_EOL;
			$contents .= '"Language: $name\n"' . PHP_EOL;
			$contents .= '' . PHP_EOL;

			foreach($strings AS $text) {
				$contents .= '' . PHP_EOL;
				$contents .= sprintf('msgid %s', json_encode($text)) . PHP_EOL;
				$contents .= 'msgstr ""' . PHP_EOL;
			}
			
			if(!file_exists($directory)) {
				mkdir($directory);
			}
			
			file_put_contents(sprintf('%s$code.template', $directory), $contents);
			
			color('green', '.po Created with ' . count($strings) . ' Strings, Path: ' . $directory . '$code.template');
		}
		
		public function scanPath($path) {
			$scanned	= 0;
			$files		= [];
			$it			= new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
				\RecursiveIteratorIterator::SELF_FIRST,
				\RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
			);
			
			$it->rewind();
			
			while($it->valid()) {
				if(!$it->isDot() && strpos($it->getSubPath(), '.git') == false) {
					$file = $it->current();
					
					if($file->getExtension() == 'php') {
						$files[] = $this->prepareContent($it->key());
						++$scanned;
					}
				}

				$it->next();
			}
			
			return $files;
		}
		
		public function prepareContent($file) {
			$content	= file_get_contents($file);
			$strings	= [];
			
			# Simple Quote
			preg_match_all("/I18N::(__|get)\('([^'\)].*)'\)/Uis", $content, $matches);

			if(count($matches[2]) > 0) {
				foreach($matches[2] AS $text) {
					if(in_array($text, $strings)) {
						continue;
					}
					
					$strings[] 	= str_replace([ "\'", '\"' ], [ "'", '"' ], $text);
				}
			}

			# Double Quote
			preg_match_all('/I18N::(__|get)\("([^"\)].*)"\)/Uis', $content, $matches);

			if(count($matches[2]) > 0) {
				foreach($matches[2] AS $text) {
					if(in_array($text, $strings)) {
						continue;
					}
					
					$strings[] 	= str_replace([ "\'", '\"' ], [ "'", '"' ], $text);
				}
			}


			return [
				'file'		=> $file,
				'strings'	=> $strings
			];
		}
	}
?>