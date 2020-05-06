<html>
<head>   <!-- Include Plotly.js -->   
<script src="plotly-latest.min.js"></script>
<title> Fermentation</title>
</head>
<body>
<div align="center">
<h1><p>Fermentations-Verlauf</p></h1>
<?php
require_once ('tilt_include.php');
$temperatures = array();
$gravities = array();
$timepoints = array();

$conn = connectDB();
if ($conn == -1){
	exit;
}
$first_beer = collectBeersSelectForm($conn);
if ($_GET['beer'] == ""){
	$beer = $first_beer;
} else {
	$beer = $_GET['beer'];
}
$query = "SELECT timestamp, gravity, temperature FROM hydrometer WHERE beer LIKE \"$beer\"";
$result = mysqli_query($conn, $query) or die('Error on temperature, gravity graph query');
while ($row = mysqli_fetch_assoc($result)) {
	$temp = calculateTemperature($row['temperature']);
	$sg = $row['gravity'];
	$gravity = calculateSGToPlato($sg);
	$timepoint = $row['timestamp'];
	$temperatures[] = $temp;
	$gravities[] = $gravity;
	$timepoints[] = $timepoint;
}
?>
<div id="fermentation" width=80% height=40%></div>
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
		type: 'scatter',
		name: 'Plato'
	};	
 	var data = [ temperatures, gravities ];	
	console.log(temperatures);
	console.log(gravities);
	FERMENTER = document.getElementById('fermentation');
	Plotly.newPlot( FERMENTER, data); 
</script>
<table WIDTH=80%>
<tr><td>Stammwuerze:</td>
<td><?php echo getOriginalGravity($conn, $beer) ?></td>
<td>Alkoholanteil (%Vol / %Gew):</td>
<td><?php echo getAlcoholContentVol($conn, $beer) ?>% / <?php echo getAlcoholContentWeight($conn, $beer) ?>%</td>
<td>Tage:</td>
<td><?php echo getFermentationDuration($conn, $beer) ?></td>
<td>Durchschnittstemperatur:</td>
<td><?php echo getAverageTemperature($conn, $beer) ?></td></tr>

<tr><td>scheinbarer Restextrakt:</td>
<td><?php echo getCurrentGravity($conn, $beer) ?></td>
<td>scheinbarer/tatsaechlicher Vergaerungsgrad:</td>
<td><?php echo getDegreeFermentation($conn, $beer) ?>% / <?php echo getRealDegreeFermentation($conn, $beer) ?>%</td>
<td>Startzeit:</td>
<td><?php echo getStartTimestamp($conn, $beer) ?></td>
<td>Min Temperatur:</td>
<td><?php echo getMinTemperature($conn, $beer) ?></td></tr>

<tr>
<td>tatsaechlicher Restextrakt:</td>
<td><?php echo getRealCurrentGravity($conn, $beer) ?></td>
<td>Energiegehalt pro 0,5l:</td>
<td><?php echo getCaloriesHalfLiter($conn, $beer) ?> kcal / <?php echo getKiloJouleHalfLiter($conn, $beer) ?> kJ</td>
<td>Endezeit:</td>
<td><?php echo getStopTimestamp($conn, $beer) ?></td>
<td>Max Temperatur:</td>
<td><?php echo getMaxTemperature($conn, $beer) ?></td>
<!-- Vergärungsgrad, Stabil seit, Fertig??-->
</tr>

</table>
<br><h3>SG stabil seit: <?php echo getGravityStableDays($conn, $beer) ?></h3>
<br><?php TestFunc($conn, $beer) ?>
</div>
</body>
</html>
