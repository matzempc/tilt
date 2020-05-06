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

$tilt = new tiltMySQL($_GET['beer']);

if ($tilt->initialized() == 0){
	exit;
}
$tilt->printBeersSelectForm();
$comment = $tilt->getBeerComment();
if ($comment != "")
{
	echo "<br>Kommentar:" . $comment . "<br>";
}
?>
<div id="fermentation" width=80% height=40%></div>
<script>
	var temperatures = { 
		x: <?php echo json_encode($tilt->timepoints, JSON_PRETTY_PRINT) ?>,
		y: <?php echo json_encode($tilt->temperatures, JSON_PRETTY_PRINT) ?>,
		type: 'scatter',
		name: 'Temperatur'
	};		
	var gravities = { 
		x: <?php echo json_encode($tilt->timepoints, JSON_PRETTY_PRINT) ?>,
		y: <?php echo json_encode($tilt->gravities, JSON_PRETTY_PRINT) ?>,
		type: 'scatter',
		name: 'Extraktgehalt',
		yaxis: 'y2'
	};	
 	var data = [ temperatures, gravities ];	
	var layout = {
	  title: 'Fermantationsgraph',
	  yaxis: {title: 'Grad Celsius'},
	  yaxis2: {
		title: 'Grad Plato',
		//titlefont: {color: 'rgb(148, 103, 189)'},
		//tickfont: {color: 'rgb(148, 103, 189)'},
		overlaying: 'y',
		side: 'right'
	  }
	};
	FERMENTER = document.getElementById('fermentation');
	Plotly.newPlot( FERMENTER, data, layout); 
</script>
<table WIDTH=80%>
<tr><td>Stammwuerze:</td>
<td><?php echo $tilt->getOriginalGravity() ?></td>
<td>Alkoholanteil (%Vol / %Gew):</td>
<td><?php echo $tilt->getAlcoholContentVol() ?>% / <?php echo $tilt->getAlcoholContentWeight() ?>%</td>
<td>Tage:</td>
<td><?php echo $tilt->getFermentationDuration() ?></td>
<td>Durchschnittstemperatur:</td>
<td><?php echo $tilt->getAverageTemperature() ?></td></tr>

<tr><td>scheinbarer Restextrakt:</td>
<td><?php echo $tilt->getCurrentGravity() ?></td>
<td>scheinbarer/tatsaechlicher Vergaerungsgrad:</td>
<td><?php echo $tilt->getDegreeFermentation() ?>% / <?php echo $tilt->getRealDegreeFermentation() ?>%</td>
<td>Startzeit:</td>
<td><?php echo $tilt->getStartTimestamp() ?></td>
<td>Min Temperatur:</td>
<td><?php echo $tilt->getMinTemperature() ?></td></tr>

<tr>
<td>tatsaechlicher Restextrakt:</td>
<td><?php echo $tilt->getRealCurrentGravity() ?></td>
<td>Energiegehalt pro 0,5l:</td>
<td><?php echo $tilt->getCaloriesHalfLiter() ?> kcal / <?php echo $tilt->getKiloJouleHalfLiter() ?> kJ</td>
<td>Endezeit:</td>
<td><?php echo $tilt->getStopTimestamp() ?></td>
<td>Max Temperatur:</td>
<td><?php echo $tilt->getMaxTemperature() ?></td>
</tr>

</table>
<br><h3>SG stabil seit: <?php echo $tilt->getGravityStableDays() ?></h3>
</div>
</body>
</html>
