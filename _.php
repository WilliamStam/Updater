<?php


namespace update;

class _ {
	function __construct(){


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



	function _exec($cmd,$folder=false){

		if (DRY){
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

		if (DRY && !$force){
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