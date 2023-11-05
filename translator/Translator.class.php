<?php
	require_once(PATH . '/panel/libraries/Sepia/PoParser/Catalog/Catalog.php');
	require_once(PATH . '/panel/libraries/Sepia/PoParser/Catalog/CatalogArray.php');
	require_once(PATH . '/panel/libraries/Sepia/PoParser/Catalog/Entry.php');
	require_once(PATH . '/panel/libraries/Sepia/PoParser/Catalog/EntryFactory.php');
	require_once(PATH . '/panel/libraries/Sepia/PoParser/Catalog/Header.php');
	require_once(PATH . '/panel/libraries/Sepia/PoParser/Parser.php');
	require_once(PATH . '/panel/libraries/Sepia/PoParser/SourceHandler/SourceHandler.php');
	require_once(PATH . '/panel/libraries/Sepia/PoParser/SourceHandler/FileSystem.php');
	
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
		}
		
		public function list() {
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
		}
		
		public function scan() {
			$count	= 0;
			$files  = [
				'core'		=> $this->scanPath(sprintf('%spanel', PATH)),
				'modules'	=> []
			];
			
			foreach(new \DirectoryIterator(sprintf('%smodules', PATH)) AS $info) {
				if($info->isDot()) {
					continue;
				}
				
				$files['modules'][$info->getFilename()] = $this->scanPath(sprintf('%smodules%s%s', PATH, DS, $info->getFilename()));
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
			
			// Make Translation requests
			
			// Merge translated strings to $files with translation-array
			
			// Build .po files
		}
		
		public function scanPath($path) {
			$scanned	= 0;
			$files		= [];
			$it			= new RecursiveIteratorIterator(
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