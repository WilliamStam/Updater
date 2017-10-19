<?php

NAMESPACE update\actors;

class composer extends \update\updater implements actorsInterface {

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

			$this->_output(updater::DONE,"Success");

		}






	}
	function _start($folder){
		$this->_output($this->H1,$this->def['label']);
		$this->_output($this->LOG,"FOLDER",$folder);






		$this->_output($this->LOG,"SELF-UPDATE",$this->_exec('composer self-update',$folder));
		$this->_output($this->LOG,"UPDATE",$this->_exec('composer install',$folder));



	}


}