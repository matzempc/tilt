<html>
<head>   <!-- Include Plotly.js -->   
<script src="plotly-latest.min.js"></script>
<title> Fermentation</title>
</head>
<body>
<div align="center">
<?php
$temperatures = array();
$gravities = array();
$timepoints = array();
	
if ($conn = mysqli_connect('localhost','tilt','tilt','tilt')){
	$query = "SELECT beer FROM hydrometer GROUP BY beer";
	$result = mysqli_query($conn, $query) or die('Error connecting to mysql');
	$first = 1;
	echo "<h1><p>Fermentation monitoring</p>";
	echo "<form action=\"tilt.php\" method=\"get\">";
	echo "<select name=\"beer\">";
	while ($row = mysqli_fetch_assoc($result)) {
		$beer = $row['beer'];
		if ($first == 1)
		{
			$first = 0;
			$first_beer = $beer;
			echo "<option value=\"$beer\" selected> $beer";
		} else {
			echo "<option value=\"$beer\"> $beer";
		}
	}
	echo "</select><br>";
	echo "<p><input type = \"submit\" value = \"OK\">";
	echo "</form>";
	if ($_GET['beer'] == ""){
		$query = "SELECT timestamp, gravity, temperature FROM hydrometer WHERE beer LIKE \"$first_beer\"";
	} else {
		$beer = $_GET['beer'];
		$query = "SELECT timestamp, gravity, temperature FROM hydrometer WHERE beer LIKE \"$beer\"";
	}
	$result = mysqli_query($conn, $query) or die('Error connecting to mysql');
	while ($row = mysqli_fetch_assoc($result)) {
		$temp = ($row['temperature'] - 32) / 1.8;
		$sg = $row['gravity'];
		$gravity = (-1 * 616.868) + 
			(1111.14 * $sg) - (630.272 * $sg * $sg) + 
			(135.997 * $sg * $sg * $sg);
		$timepoint = $row['timestamp'];
		$temperatures[] = $temp;
		$gravities[] = $gravity;
		$timepoints[] = $timepoint;
	}
}
?>
<div id="fermentation" width=80%></div>
<script>
	var temperatures = { 
		x: <?php echo json_encode($timepoints, JSON_PRETTY_PRINT) ?>,
		y: <?php echo json_encode($temperatures, JSON_PRETTY_PRINT) ?>,
		type: 'scatter',
		name: 'Temperature'
	};		
	var gravities = { 
		x: <?php echo json_encode($timepoints, JSON_PRETTY_PRINT) ?>,
		y: <?php echo json_encode($gravities, JSON_PRETTY_PRINT) ?>,
		type: 'lines+markers',
		name: 'Plato'
	};	
 	var data = [ temperatures, gravities ];	
	console.log(temperatures);
	console.log(gravities);
	FERMENTER = document.getElementById('fermentation');
	Plotly.newPlot( FERMENTER, data); 
</script>
<table WIDTH=80%>
<tr>
<td>
Stammwuerze:
</td>
<td>
Wert1
</td>
<td>
Tage:
</td>
<td>
Wert1
</td>
<td>
Durchschnitt:
</td>
<td>
Wert1
</td>
</tr>
<tr>
<td>
SG:
</td>
<td>
Wert2
</td>
<td>
Start:
</td>
<td>
Wert2
</td>
<td>
Min:
</td>
<td>
Wert2
</td>
</tr>
<tr>
<td>
Alkoholanteil:
</td>
<td>
Wert2
</td>
<td>
Ende:
</td>
<td>
Wert2
</td>
<td>
Max:
</td>
<td>
Wert2
</td>
<!-- Vergärungsgrad, Stabil seit, Fertig??-->
</tr>
</table>
</div>
</body>
</html>
