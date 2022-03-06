<?php
$target = $_POST['target'];
$source = $_POST['source'];
$action = $_POST['action'];


switch($action) {
case "disconnect":
	$ports = $source .  " -- " . $target;

	exec('aconnect -d ' . $source . " " . $target, $output, $retval);

	if (!$retval){
		echo "Disconnected: " . $ports;
	} else {
		// 			print_r($output);
		echo "Disconnect Error";
	}
	break;
case "connect":
	$ports = $source .  "--> " . $target;

	exec('aconnect ' . $source . " " . $target, $output, $retval);

	if (!$retval){
		echo "Connected: " . $ports;
	} else {
		// 		print_r($output);
		echo "Connect Error";
	}
	break;

default:
	echo "I dunno";
}
?>
