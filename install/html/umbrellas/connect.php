<?php

$target = $_GET['target'];
$source = $_GET['source'];

// echo $target . "--" . $source;

if ($_GET['action'] == "disconnect"){
	$ports = $source .  " -- " . $target;

 	exec('aconnect -d ' . $source . " " . $target, $output, $retval);
 	
		if (!$retval){
			echo "Disconnected: " . $ports;
		} else {
// 			print_r($output);
			echo "Disconnect Error";
		}
}
if ($_GET['action'] == "connect"){
	$ports = $source .  "--> " . $target;

 	exec('aconnect ' . $source . " " . $target, $output, $retval);
 	
	if (!$retval){
		echo "Connected: " . $ports;
	} else {
// 		print_r($output);
		echo "Connect Error";
	}
}
?>