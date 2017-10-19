<?php

NAMESPACE update\actors;

class composer extends \update\_ implements actorsInterface {

	private static $instance;
	function __construct(){
		parent::__construct();

		$this->def = self::_def();

	}
	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	static function _def(){
		return array(
			"group"=>"composer",
			"label"=>"Composer",
			"class"=>__CLASS__,
			"order"=>15
		);
	}

	function start(){

		$last_folder = null;
		$folder = dirname(__FILE__);
		while (is_dir($folder) && !file_exists($folder.DIRECTORY_SEPARATOR.'composer.lock') && $last_folder != $folder){
			$last_folder = $folder;
			$folder = dirname($folder);
		}
		$folder = $folder . DIRECTORY_SEPARATOR;

		if (file_exists($folder.'composer.lock')){
			$this->_start($folder);

			$this->output(DONE,"Success");

		}






	}
	function _start($folder){
		$this->output(H1,$this->def['label']);
		$this->output(LOG,"FOLDER",$folder);






		$this->output(LOG,"SELF-UPDATE",_exec('composer self-update',$folder));
		$this->output(LOG,"UPDATE",_exec('composer install',$folder));



	}


}