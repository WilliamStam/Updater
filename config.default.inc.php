<?php
$cfg['DB']['host'] = "localhost";
$cfg['DB']['username'] = "";
$cfg['DB']['password'] = "";
$cfg['DB']['database'] = "PHPUpdater";

$cfg['git'] = array(
	'username'=>"",
	"password"=>"",
	"path"=>"github.com/WilliamStam/Updater",
	"branch"=>"master"
);

$cfg['media'] = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR. "media" . DIRECTORY_SEPARATOR;
$cfg['backup'] = $cfg['media'] . "backups" . DIRECTORY_SEPARATOR;


$cfg['updater'] = array(
	"7zip"=>"C:\\Program Files\\7-Zip\\7z.exe"
);
