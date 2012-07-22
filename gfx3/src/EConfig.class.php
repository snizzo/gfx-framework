<?php

/*
 *   TRT GFX 3.0.1 (beta build) BackToSlash
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	http://www.gfx3.org
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the MIT License.
 *   http://opensource.org/licenses/mit-license.php
 */
 
/*
 * This class aims to manage all the gfx config in general contained in gfx3/config.
 * 
 */

class EConfig{
	
	private $safe_path;
	private $config_path;
	private $data;
	
	public function __construct(){
		$this->safe_path = getcwd();
	}
	
	/*
	 * Takes an array and returns the same array of strings without
	 * every string of php code.
	 */
	public function avoid_php_code($lines){
		$result = array();
		$inside = false;
		foreach($lines as $line){
			if(stristr($line, '<?php')){
				$inside = true;
			} elseif(!$inside){
				$result[] = $line;
			}
			if(stristr($line, '?>')){
				$inside = false;
			}
		}
	}
	
	/*
	 * returns a parsed file array mapped as
	 * $array[$key] = $value
	 */
	public function parse_file($filename){
		//initializing empty array
		$result = array();
		
		//mapping file line per line
		$file = file($filename);
		$file = $this->avoid_php_code($file);
		foreach($file as $line){
			$chunks = explode("=",$line);
			//gives correct key and correct value, erasing line break.
			$result[$chunks[0]] = rtrim($chunks[1], "\n"); 
		}
		return $result;
	}
	
	/*
	 * return an array with every config file merged
	 */
	public function load_all(){
		$this->data = array();
		//enters in conf directory
		chdir(EIncluder::$config_path);
		//parse every single conf file and place it in an associative array
		foreach(glob("*") as $filename){
			$name = EFileSystem::get_file_name($filename);
			$filelist[$name] = $this->parse_file($filename);
		}
	}
	
}

?>
