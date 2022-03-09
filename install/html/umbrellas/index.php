<html lang="en">
<head>
	<meta charset="utf-8"/>
	<title>midi-connections</title>
	<link href="style.css" rel="stylesheet"/>
	<style type="text/css">.c1-ui-state-hover {background-color:yellow !important;padding:5px !important}</style></head>
	<script src="assets/jquery.min.js"></script>
	<script type="text/javascript">
	/*
	* Commented out cuz jquery $(selector).val() or $(selector).text() is more concise
	function ReplaceContentInContainer(id, content) {
		$("#" + id).val(content);
	}
	*/
	function aconnector(str) {
		source = $("#source-port").val();
		target = $("#target-port").val();

		$.ajax({
		  type: "POST",
		  url: "connect.php",
		  data: {"source" : source, "target" : target, "action" : str},
		  success: function(data, status) {
			$("#outputtext").html(data);

			reloadList();
			}
		});
	}

	function reloadList() {
		$("#list").load("list.php", function() {
			// Set up click handlers on "success" of loading list.php
			// Have to wait to do this so everything in list.php has been added to the DOM
			$(".linky").click(function(e) {
				console.log($(this).attr("descriptioncontainer"));
				ClickedItem(this);
			});

			$(".delete_connection").click(function(e) {
				if(window.confirm("Delete connection?")) {
					let from = $(this).attr("from");
					let to = $(this).attr("to");
					$("#source-port").val(from);
					$("#target-port").val(to);

					if(from != null && to != null)
						aconnector('disconnect');
				}
			});
		});
	}

	$(document).ready(function() {
		reloadList();
	});

	function ClickedItem(item) {
		which = $(item).attr("which");
		clientName = $(item).attr("clientName");
		clientId = $(item).attr("clientId");
		portId = $(item).attr("portId");

		switch(which) {
			case "MIDI in":
				descriptioncontainer = "select-source";
				hiddencontainer = "source-port";
				break;
			case "MIDI out":
				descriptioncontainer = "select-target";
				hiddencontainer = "target-port";
				break;
			default:
				return;
		}
		$("#" + descriptioncontainer).text(clientName + " " + clientId + ":" + portId);
		$("#" + hiddencontainer).val(clientId + ":" + portId);
	}
	</script>
</head>
<body>
	<div id="list"></div>
<center>
	<div>
		<h3 id="outputtext"></h3>
	</div>
</center>
</body>
</html>
