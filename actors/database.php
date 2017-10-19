<?php

NAMESPACE update\actors;

class database extends \update\_ implements actorsInterface {

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
			"group"=>"databases",
			"label"=>"Databases",
			"class"=>__CLASS__,
			"order"=>10
		);
	}

	function start(){
		$last_folder = null;
		$folder = dirname(__FILE__);
		while (is_dir($folder) && !file_exists($folder.DIRECTORY_SEPARATOR.'db_update.php') && $last_folder != $folder){
			$last_folder = $folder;
			$folder = dirname($folder);
		}
		$folder = $folder . DIRECTORY_SEPARATOR;


		$sql = array();
		if (file_exists($folder."db_update.php")) {
			require($folder.'db_update.php');
		} else {
			$sql = false;
		}



		$this->db_update = $sql;



		if (isset($this->cfg['DB']) && $this->db_update){
			$this->output(H1,$this->def['label']);



			$this->_start($this->cfg);









			$this->output(DONE,"Success");

		}
	}
	function backup($cfg){
		$this->output(H3,"Backup");
		if (!file_exists($cfg['backup'])) {
			@mkdir($cfg['backup'], 0777, true);
			$this->output(LOG," - Folder","Created");

		} else {
			$this->output(LOG," - Folder","OK");
		}
		$compressprogpath =$cfg['updater']['7zip'];

		$filename = $cfg['DB']['database'] . "_".date("YmdHis") .  ".sql";
		$compress = file_exists($compressprogpath);
		if ($compress){
			$filename = $filename.".7z";
			$this->output(LOG," - Compression","TRUE");
		} else {
			$this->output(LOG," - Compression","FALSE");
		}



		$filepath = $cfg['backup'] .$filename;
		$this->output(LOG," - Starting Backup",$filename);
	}
	function _start($cfg){


		$link = mysqli_connect($cfg['DB']['host'], $cfg['DB']['username'], $cfg['DB']['password'], $cfg['DB']['database']);


		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}


		$this->output(LOG,"DATABASE",$cfg['DB']['database']);

		$this->backup($cfg);



		mysqli_query($link,"CREATE TABLE IF NOT EXISTS `_version` (  `table` varchar(100) NOT NULL,  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),  `version` int(4) DEFAULT NULL,  PRIMARY KEY (`table`),  UNIQUE KEY `table` (`table`));") or die(mysqli_error($link));


		$tables_ = _sql('SELECT  table_name AS `table` FROM  information_schema.tables WHERE  table_schema = DATABASE()',$link, function($data){ return array_map(function($i){ return $i['table']; },$data);},true);





		$version = _sql('SELECT * FROM _version',$link, function($data){return $data;},true);

		$changes = $this->db_update;

		$tables = array();
		foreach ($tables_ as $table){
			$tables[$table] = array(
				"version"=>0,
				"timestamp"=>""
			);
		}

		foreach ($version as $ver){
			$tables[$ver['table']]['version'] = $ver['version'];
			$tables[$ver['table']]['timestamp'] = $ver['timestamp'];
		}



		$needsupdate = false;


		$this->output(H4,"Checking Table Versions");

		$tablelist = array();
		foreach ($tables as $k=>$item){
			$str = " - " . $k . ": " . $item['version'] . " (".$item['timestamp'].")";

			$status = "Ok";
			if (isset($changes[$k]) && count($changes[$k])!=$item['version']){

				$needsupdate = true;
				$outby = count($changes[$k]) - ($item['version'] * 1);

				$adj = "Outdated";
				if (count($changes[$k])<$item['version']){
					$adj = "Over";
				}
				$status = $adj." - ". $outby . " (". count($changes[$k]) . ")";
			}

			$this->output(LOG,$str,$status);
		}




		if ($needsupdate){
			$this->output(H4,"Updating Tables");

			foreach ($tables as $k=>$item){

				if (isset($changes[$k]) && count($changes[$k])!=$item['version']){
					$i = 0;
					foreach ($changes[$k] as $sql){
						$i = $i + 1;


						mysqli_query($link,$sql) or die(mysqli_error($link));
						mysqli_query($link,"INSERT INTO `_version` (`table`,`timestamp`,`version`) VALUES ('{$k}', now() ,'{$i}') ON DUPLICATE KEY UPDATE version = '{$i}', timestamp = now();") or die(mysqli_error($this->link));


					}


					$status = "Ok";
					$this->output(LOG,"- $k",$status);
				}


			}
		}














	}

}