<html lang="en">
<head>
	<meta charset="utf-8"/>
	<title>midi-connections</title>
	<link href="style.css" rel="stylesheet"/>
	<style type="text/css">.c1-ui-state-hover {background-color:yellow !important;padding:5px !important}</style></head>
	<script src="assets/jquery.min.js"></script>
	<script type="text/javascript">
	function ReplaceContentInContainer(id, content) {
		$("#" + id).val(content);
	}
	function aconnector(str) {
		source = $("#source-port").val();
		target = $("#target-port").val();

		$.ajax({
		  type: "POST",
		  url: "connect.php",
		  data: {"source" : source, "target" : target, "action" : str},
		  success: function(data, status) {
			$("#outputtext").html(data);
			$("#list").load("list.php");
			// reload list
			}
		});
	}

	$(document).ready(function() {
		$("#list").load("list.php");
	});

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
