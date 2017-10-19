<?php

$dir = dirname( __FILE__ );
define("DRY",false);

define('H1',array(
	"web"=>"<h1>%s</h1>",
	"cli"=>"\n\n%s\n----------------------------------"
));
define('H2',array(
	"web"=>"<h2>%s</h2>",
	"cli"=>"\n\n%s\n----------------------------------"
));
define('H3',array(
	"web"=>"<h3>%s</h3>",
	"cli"=>"\n\n%s\n----------------------------------"
));
define('H4',array(
	"web"=>"<h4>%s</h4>",
	"cli"=>"\n\n%s\n----------------------------------"
));
define('DONE',array(
	"web"=>"<blockquote>%s</blockquote>",
	"cli"=>"\n\n%s\n\n"
));
define('LOG',array(
	"web"=>"<div><span style='color:#444;'>%s</span>: <span>%s</span>
</div>",
	"cli"=>"\n%s: %s"
));
define('TXT',array(
	"web"=>"<div>%s</div>",
	"cli"=>"\n%s"
));


foreach(glob($dir.DIRECTORY_SEPARATOR."_*.php") as $file){
	require_once($file);
}
$actors = array();
foreach(glob($dir.DIRECTORY_SEPARATOR."actors".DIRECTORY_SEPARATOR."*.php") as $file){
	$pathinfo = pathinfo($file);
	require_once($file);
	$class = "\\update\\actors\\".$pathinfo['filename'];

	if (class_exists($class)) {
		$return = $class::_def();
		$return['file'] = $file;
		$actors[] = $return;
	}
}



usort($actors, function($a, $b) {
	return $a['order'] <=> $b['order'];
});
foreach ($actors as $actor){


	$actor['class']::getInstance()->start();

}




function test($input) {
	if (is_array($input)){
		header("Content-Type: application/json");
		echo json_encode($input);
	} else {
		header("Content-Type: text/html");
		echo $input;
	}

	exit();
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