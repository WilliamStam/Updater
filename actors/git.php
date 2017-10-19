<?php

NAMESPACE update\actors;

class git extends \update\updater implements actorsInterface {

	private static $instance;
	function __construct(){
		parent::__construct();
		$this->def = self::_def();

	}
	public static function getInstance() {
		self::$instance = new self();
		return self::$instance;
	}
	static function _def(){
		return array(
			"group"=>"files",
			"label"=>"Git Pull",
			"class"=>__CLASS__,
			"order"=>1
		);
	}
	function start(){
		if (isset($this->cfg['git'])){

			$this->_output(parent::H1, "Files");


			$this->_output(parent::EXEC,"CHECKING GIT",$this->_exec('git --version'));

			$this->self_update();


			$this->_output(parent::H3,"Project");



				$this->_output(parent::EXEC,"INIT",$this->_exec('git init',$this->cfg_folder));
				$this->_output(parent::EXEC,"STASH",$this->_exec('git reset --hard HEAD',$this->cfg_folder));

			if ($this->cfg['git']['username']&&$this->cfg['git']['password']){
				$this->_output(parent::EXEC,"UPDATING",$this->_exec('git pull https://'.$this->cfg['git']['username'] .':'.$this->cfg['git']['password'] .'@'.$this->cfg['git']['path'] .' ' . $this->cfg['git']['branch']."",$this->cfg_folder));
			} else {
				$this->_output(parent::EXEC,"UPDATING",$this->_exec('git pull '.$this->cfg['git']['path'] .' ' . $this->cfg['git']['branch']."",$this->cfg_folder));
			}




			$this->_output(parent::DONE,"Success");

		}
	}
	function self_update(){
		$self['git'] = array(
			'username'=>"",
			"password"=>"",
			"path"=>"github.com/WilliamStam/Updater",
			"branch"=>"master"
		);

		$this->_output(parent::H3,"Self Update");

			$this->_output(parent::EXEC,"INIT",$this->_exec('git init'));
			$this->_output(parent::EXEC,"STASH",$this->_exec('git reset --hard HEAD'));


		$this->_output(parent::EXEC,"SELF UPDATING",$this->_exec('git pull https://'.$self['git']['path'] .' ' . $self['git']['branch'].""));


	}

}