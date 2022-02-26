<html lang="en"><head>
	<meta charset="utf-8">
	<title>midi-connections</title>
	<link href="style.css" rel="stylesheet">
	<style type="text/css">.c1-ui-state-hover {background-color:yellow !important;padding:5px !important}</style></head>
	<script src="assets/jquery.min.js"></script>

	<script type="text/javascript"><!--
	function ReplaceContentInContainer(id,content) {
		var container = document.getElementById(id);
		container.innerHTML = content;
	}
	//--></script>

<body>
	<script>
	function aconnector(str) {
        source = document.getElementById("source-port").innerHTML;
		target = document.getElementById("target-port").innerHTML;

		const xmlhttp = new XMLHttpRequest();
		xmlhttp.onload = function() {
			document.getElementById("outputtext").innerHTML = this.responseText;
			setTimeout(function(){location.reload()}, 3000);
		}
		xmlhttp.open("GET", "connect.php?action=" + str + "&source=" + source + "&target=" + target);
		xmlhttp.send();
	}

	</script>


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
			if (preg_match("/\d+:\s'(.*?)'/", $devicevalues, $clientName)){
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
// 		print_r($subarray);
	}
	
	return $deviceArray;
}

$allDeviceArray = parseDevices($alldevices);
asort($allDeviceArray);
// print_r($allDeviceArray);

// $inputDeviceArray = parseDevices($inputdevices);
// asort($inputDeviceArray);
// // 
// 
// $outputDeviceArray = parseDevices($outputdevices);
// asort($outputDeviceArray);

echo "</pre>";
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
					<div>

<?php
	foreach ($allDeviceArray as $eachDevice){
		echo "<div class=\"device-port\">". $eachDevice['clientName'] . " : [" . $eachDevice['clientId'] . "]</div>\n";
		echo "<ul>";
		foreach ($eachDevice['ports'] as $portInfo){
			if ($portInfo[1] != "MIDI out"){
			echo "<li class=\"device-ports\"><div class=\"linky\" onclick=\"ReplaceContentInContainer('select-source','". $eachDevice['clientName'] . " " . $eachDevice['clientId'] . ":" . $portInfo[0] . "'); ReplaceContentInContainer('source-port','" . $eachDevice['clientId'] . ":" . $portInfo[0] . "')\">$portInfo[1] : $portInfo[0]" . "</div>";
			foreach ($portInfo as $portDetail){
			if (isset($portDetail["To"])){
				echo "<div>--> ";
				foreach ($portDetail["To"] as $pts){ 
					echo $pts . " ";
				}
				echo "</div>";
			}
			if (isset($portDetail["From"])){
				echo "<div><-- ";
				foreach ($portDetail["From"] as $pts){ 
					echo $pts . " ";
				}
				echo "</div>";
			}
			}
			}
			echo "</li>\n";
		}
		echo "</ul>";
	}
?>

					</div>
					</div>
				</div>
				</div>
		</div>
		<div class='column'>
		  <div class='right-column'>

			<!-- OUTPUTS-->
			<div class="device-list-box">
				<div class="device-list">
				<div>
<?php
	foreach ($allDeviceArray as $eachDevice){
		echo "<div class=\"device-port\">". $eachDevice['clientName'] . " : [" . $eachDevice['clientId'] . "]</div>\n";
		echo "<ul>";
		foreach ($eachDevice['ports'] as $portInfo){
			if ($portInfo[1] != "MIDI in"){
			echo "<li class=\"device-ports\"><div class=\"linky\" onclick=\"ReplaceContentInContainer('select-target','". $eachDevice['clientName'] . " " . $eachDevice['clientId'] . ":" . $portInfo[0] . "'); ReplaceContentInContainer('target-port','" . $eachDevice['clientId'] . ":" . $portInfo[0] . "')\">$portInfo[1] : $portInfo[0]" . "</div>";
			foreach ($portInfo as $portDetail){
			if (isset($portDetail["To"])){
				echo "<div>--> ";
				foreach ($portDetail["To"] as $pts){ 
					echo $pts . " ";
				}
				echo "</div>";
			}
			if (isset($portDetail["From"])){
				echo "<div><-- ";
				foreach ($portDetail["From"] as $pts){ 
					echo $pts . " ";
				}
				echo "</div>";
			}
			}
			}
			echo "</li>\n";
		}
		echo "</ul>";
	}
?>
					</div>
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
				<div class="hiddenvalue" id="source-port"></div>
			</div>
		</div>
		<div class='column'>
			<div class='right-column'>
				<div class="selectedItem" id="select-target"></div>
				<button class="red" id="disconnectbutton" onclick="aconnector('disconnect')">Disconnect</button>			
				<div class="hiddenvalue" id="target-port"></div>
			</div>
		</div>
	</div>
	</div>
		<h3 id="outputtext"></h3>
	</div>
</div>

	</body>
</html>

<head>
