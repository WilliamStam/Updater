<?php


namespace update;
class updater {

	const
		DRY = false,
		H1=array(
			"web"=>"<h1>%s</h1>",
			"cli"=>"\n\n%s\n----------------------------------"
		),
		H2=array(
			"web"=>"<h2>%s</h2>",
			"cli"=>"\n\n%s\n----------------------------------"
		),
		H3=array(
			"web"=>"<h3>%s</h3>",
			"cli"=>"\n\n%s\n----------------------------------"
		),
		H4=array(
			"web"=>"<h4>%s</h4>",
			"cli"=>"\n\n%s\n----------------------------------"
		),
		DONE=array(
			"web"=>"<blockquote>%s</blockquote>",
			"cli"=>"\n\n%s\n\n"
		),
		LOG=array(
			"web"=>"<div><span style='color:#444;'>%s</span>: <span>%s</span></div>",
			"cli"=>"\n%s: %s"
		),
		TXT=array(
			"web"=>"<div>%s</div>",
			"cli"=>"\n%s"
		);



	function __construct($cfg=false) {
		header('X-Accel-Buffering: no');
		$dir = dirname( __FILE__ );




		foreach(glob($dir.DIRECTORY_SEPARATOR."_*.php") as $file){
			require_once($file);
		}


		$actors = array();
		foreach(glob($dir.DIRECTORY_SEPARATOR."actors".DIRECTORY_SEPARATOR."*.php") as $file){
			$pathinfo = pathinfo($file);
			require_once($file);
			$class = "\\update\\actors\\".$pathinfo['filename'];
			$actor = basename($file,".php");

			if (class_exists($class)) {
				$return = $class::_def();
				$return['file'] = $file;
				$return['actor'] = $actor;
				$actors[$actor] = $return;
			}
		}



		uasort($actors, function($a, $b) {
			return $a['order'] <=> $b['order'];
		});

		$this->actors = $actors;


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
		if (file_exists($folder."config.default.inc.php")) {
			require($folder.'config.default.inc.php');
		}
		if (file_exists($folder."config.inc.php")) {
			require($folder.'config.inc.php');
		}




		$this->cfg = $cfg;
		$this->cfg_folder = $folder;





	}

	function run($actor=false){


		if ($actor===false){
			foreach ($this->actors as $ob){
				$ob['class']::getInstance()->start();
			}
		} else {

			if (isset($this->actors[$actor])){
				$this->actors[$actor]['class']::getInstance()->start();
			} else {
				echo "no actor like that exists here: " . $actor;
				exit();
			}

		}
	}


	function _output(){


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



		echo $str;

		ob_flush();
		flush();
		return $str;
	}



	function _exec($cmd,$folder=false){

		if (self::DRY){
			return $cmd;
		} else {
			$curfolder = getcwd();
			if ($folder){
				chdir($folder);
			}

			$return = shell_exec($cmd." 2>&1 &");

			if ($folder){
				chdir($curfolder);
			}

			return $return;
		}


	}
	function _sql($cmd,$link, $fn,$force=false){

		if (self::DRY && !$force){
			return $cmd;
		} else {
			$result = mysqli_query($link,$cmd) or die(mysqli_error($link));

			$data = array();
			while($item = $result->fetch_assoc()){
				$data[] = $item;
			}


			//test(array($data,$cmd));

			return call_user_func_array($fn,array($data));
		}


	}


}



