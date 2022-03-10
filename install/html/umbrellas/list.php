<?php
echo "<pre>";
// $inputs = shell_exec('aconnect -il 2>&1');
// $outputs = shell_exec('aconnect -ol 2>&1');

$inputsoutputs = shell_exec('aconnect -l 2>&1');

$allDeviceArray = [];
// $inputDeviceArray = [];
// $outputDeviceArray = [];

$alldevices = preg_split("/client\s/", $inputsoutputs, -1, PREG_SPLIT_NO_EMPTY);
// $inputdevices = preg_split("/client\s/", $inputs, -1, PREG_SPLIT_NO_EMPTY);
// $outputdevices = preg_split("/client\s/", $outputs, -1, PREG_SPLIT_NO_EMPTY);

// print_r($inputdevices);

function trim_value(&$value) 
{ 
    $value = trim($value); 
}

function parseDevices(&$devicedata){
	$deviceArray = [];
	foreach($devicedata as &$value){
		$devicelines = preg_split("/\n/", $value, -1, PREG_SPLIT_NO_EMPTY);
		
		$subarray = ["clientId" => "","clientName" => "","ports" => []];
		$subMatch = [];
		
		foreach($devicelines as $devicevalues){
			$ports = [];
			$subPorts = [];
			$subMatch = [];
			if (preg_match("/^(\d+):/", $devicevalues, $clientId)){
				$matchedID = True;
				$subarray["clientId"] = $clientId[1];
			} else {
				$matchedID = False;
			}
			if (preg_match("/\d+:\s'(.*?)' \[type=(.*?)\]/", $devicevalues, $clientName)){
				// client 28: 'mioXL' [type=kernel,card=3]                                         
				preg_match("/card=(.*+)/", $clientName[2], $card);
				preg_match("/pid=(.*+)/", $clientName[2], $pid);
				$type = explode(",", $clientName[2]);
				$subarray["type"] = $type[0];
				$subarray["card"] = $card[1];
				$subarray["pid"] = $pid[1];

				$subarray["clientName"] = $clientName[1];
			}

			if (preg_match("/\s(\d+)\s'(.*?)'/", $devicevalues, $portMatches)){
				if (count($portMatches) > 0){
					$subMatch =[$portMatches[1], trim($portMatches[2])];
					array_push($subarray["ports"], $subMatch);

					$lastPort = $portMatches[1];
				}
				
			}
			
			if (preg_match("/To:\s(\d+:\d+)(,\s(\d+:\d+))*/", $devicevalues, $destination)){
				if (preg_match_all("/\s(\d+:\d+)/",$destination[0], $subDest)){
					if (count($subDest[1]) > 0){
						$tempToPort = ["To" => $subDest[1]];
// 						print_r($tempToPort);
 						array_push($subarray["ports"][$lastPort], $tempToPort);
					}
				}			
			}
		
			if (preg_match("/From:\s(\d+:\d+)(,\s(\d+\:\d+))*/", $devicevalues, $source)){
				if (preg_match_all("/\s(\d+:\d+)/",$source[0], $subSrc)){
					if (count($subSrc[1]) > 0){
						$tempFromPort = ["From" => $subSrc[1]];
// 						print_r($tempFromPort);
						array_push($subarray["ports"][$lastPort], $tempFromPort);
					}
				}
			}	
		}
		
		if ($subarray["clientId"] != ""){
			$deviceArray[$subarray["clientId"]] = $subarray;
		}
 		//print_r($subarray);
	}
	
	return $deviceArray;
}

$allDeviceArray = parseDevices($alldevices);
asort($allDeviceArray);

foreach($allDeviceArray as $client) {
	//echo $client["clientId"] . " = " . $client["clientName"] . "\n";
	foreach($client["ports"] as $port) {
		//echo $port[1] . ":" . $port[0] . "\n";
	}
	$client_map[$client["clientName"]] = $client;
	$client_map_lookup[$client["clientId"]] = $client["clientName"];
}

ksort($client_map);

// $inputDeviceArray = parseDevices($inputdevices);
// asort($inputDeviceArray);
// // 
// 
// $outputDeviceArray = parseDevices($outputdevices);
// asort($outputDeviceArray);

echo "</pre>";

function list_devices($client_map, $client_map_lookup, $which = "MIDI out") {
	$skip = ["Midi Through", "System"];

	foreach ($client_map as $eachDevice){
		if(in_array($eachDevice['clientName'], $skip)) continue;

		echo "<div class=\"device-port\">". $eachDevice['clientName'] . " : [" . $eachDevice['clientId'] . "]</div>\n";
		echo "<ul>";
		foreach ($eachDevice['ports'] as $portInfo){
			if ($portInfo[1] == $which || $eachDevice['type'] == "kernel"){
				echo "<li class=\"device-ports\">
					<a class=\"linky\" which=\"$which\" clientName=\"" . $eachDevice['clientName'] . "\" clientId=\"" . $eachDevice['clientId'] . "\" portId=\"" . $portInfo[0] . "\">$portInfo[1] : $portInfo[0]" . "</a>";

				foreach ($portInfo as $portDetail) {
					// Setting this up here to consolidate the following HTML
					if (isset($portDetail["To"]) && $which == "MIDI in") {
						$index = "To";
						$arrow = "-->";
					}
					else if (isset($portDetail["From"]) && $which == "MIDI out"){
						$index = "From";
						$arrow = "<--";
					}
					else {
						continue;
					}

					echo "<div>";
					foreach ($portDetail[$index] as $pts){ 
						// Is weird
						$from = $eachDevice['clientId'] . ":" . $portInfo[0];
						$to = $pts;

						$info = explode(":", $pts);

						echo $arrow . " " .$client_map_lookup[$info[0]] . " (" . $info[0] . ") : " . $info[1] . " ";
						echo "<a class=\"disconnect\" direction=\"$index\" from=\"$from\" to=\"$to\" href=\"#\">[Disconnect]</a>";
						echo "<br/>";
					}
					echo "</div>";
				}
			}
			echo "</li>\n";
		}
		echo "</ul>";
	}
}
?>

<div id="root">
	<div class="root-container">
	<h1>Connections</h1>
	<div class='container-wrapper'>

	<div class='row'>
		<div class='column'>
			<div class='left-column'><h3>Inputs</h3></div>
		</div>
		<div class='column'>
			<div class='left-column'><h3>Outputs</h3></div>
		</div>
	</div>

	<div class='row'>
		<div class='column'>
			<div class='left-column'>
				<!-- INPUTS-->
				<div class="device-list-box">
					<div class="device-list">
						<div><?php list_devices($client_map, $client_map_lookup, "MIDI in"); ?></div>
					</div>
				</div>
			</div>
		</div>
		<div class='column'>
			<div class='right-column'>
				<!-- OUTPUTS-->
				<div class="device-list-box">
					<div class="device-list">
						<div><?php list_devices($client_map, $client_map_lookup,  "MIDI out"); ?></div>
					</div>
				</div>
      			</div>
		</div>
	</div>
	<div class='row'>
		<div class='column'>
			<div class='left-column'>
				<div class="selectedItem" id="select-source"></div>
				<button id="connectbutton" onclick="aconnector('connect')">Connect</button>
				<input type="hidden" id="source-port" name="source-port">
			</div>
		</div>
		<div class='column'>
			<div class='right-column'>
				<div class="selectedItem" id="select-target"></div>
				<button class="red" id="disconnectbutton" onclick="aconnector('disconnect')">Disconnect</button>			
				<input type="hidden" id="target-port" name="target-port">
			</div>
		</div>
	</div>
</div>
<div>
<pre>
</pre>
</div>
