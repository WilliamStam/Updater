<?php


namespace update;

class _ {
	function __construct(){
		$cfg = array();
		$root_folder = dirname(__FILE__);
		$root_folder = $root_folder . DIRECTORY_SEPARATOR;

		$errorFolder = $root_folder . "logs";
		$errorFile = $errorFolder . DIRECTORY_SEPARATOR . "php-".date("Y-m") . ".log";
		ini_set("error_log", $errorFile);




		$last_folder = null;
		$folder = dirname(__FILE__);
		while (is_dir($folder) && !file_exists($folder.DIRECTORY_SEPARATOR.'config.default.inc.php') && $last_folder != $folder){
			$last_folder = $folder;
			$folder = dirname($folder);
		}
		$folder = $folder . DIRECTORY_SEPARATOR;


		$cfg = array();
		require($folder.'config.default.inc.php');
		if (file_exists($folder."config.inc.php")) {
			require($folder.'config.inc.php');
		}





		$this->cfg = $cfg;
		$this->cfg_folder = $folder;

	}



	function output(){

		$args = array();
		foreach (func_get_args() as $item){
			$args[] = $item;
		};


		$template = $args[0];
		if (php_sapi_name() != 'cli'){
			$template = $template['web'];
		} else {
			$template = $template['cli'];
		}
		array_shift($args);

		$str = vsprintf($template,$args);



		/*

		$str = $str . PHP_EOL;
		if ($this->cfg['git']['username'] && $this->cfg['git']['password']){
			$str = str_replace("https://".$this->cfg['git']['username'].':'.$this->cfg['git']['password'] .'@',"&lt; auth &gt;",$str);
		}
		if ($this->cfg['git']['password']){
			$str = str_replace($this->cfg['git']['password'],"********",$str);
		}
		if ($this->cfg['DB']['password']){
			$str = str_replace($this->cfg['DB']['password'],"********",$str);
		}

		*/

		echo $str;


		ob_flush();
		return $str;
	}





}