<?php

NAMESPACE update\actors;

class git extends \update\_ implements actorsInterface {

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
			"group"=>"files",
			"label"=>"Git Pull",
			"class"=>__CLASS__,
			"order"=>1
		);
	}
	function start(){
		if (isset($this->cfg['git'])){

			$this->output(H1, "Files");



			$this->self_update();


			$this->output(H4,"Project");
			$this->output(LOG,"CHECKING GIT",_exec('git --version'));


			if (file_exists(($this->cfg_folder)."\\.git")){
				$this->output(LOG,"STASH",_exec('git reset --hard HEAD',$this->cfg_folder));
			} else {
				$this->output(LOG,"INIT",_exec('git init',$this->cfg_folder));
			}

			if ($this->cfg['git']['username']&&$this->cfg['git']['password']){
				$this->output(LOG,"UPDATING",_exec('git pull https://'.$this->cfg['git']['username'] .':'.$this->cfg['git']['password'] .'@'.$this->cfg['git']['path'] .' ' . $this->cfg['git']['branch']."",$this->cfg_folder));
			} else {
				$this->output(LOG,"UPDATING",_exec('git pull '.$this->cfg['git']['path'] .' ' . $this->cfg['git']['branch']."",$this->cfg_folder));
			}




			$this->output(DONE,"Success");

		}
	}
	function self_update(){
		$self['git'] = array(
			'username'=>"",
			"password"=>"",
			"path"=>"github.com/WilliamStam/Updater",
			"branch"=>"master"
		);

		$this->output(H4,"Self Update");

		if (file_exists("\\.git")){
			$this->output(LOG,"STASH",_exec('git reset --hard HEAD'));
		} else {
			$this->output(LOG,"INIT",_exec('git init'));
		}


		$this->output(LOG,"SELF UPDATING",_exec('git pull https://'.$self['git']['path'] .' ' . $self['git']['branch'].""));


	}

}