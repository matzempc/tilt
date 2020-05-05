<html>
<head>   <!-- Include Plotly.js -->   
<script src="plotly-latest.min.js"></script>
</head>
<body>
<?php
$temperatures = array();
$gravities = array();
$timepoints = array();
	
if ($conn = mysqli_connect('localhost','tilt','tilt','tilt')){
	$query = "SELECT timestamp, gravity, temperature FROM hydrometer";
	$result = mysqli_query($conn, $query) or die('Error connecting to mysql');
	while ($row = mysqli_fetch_assoc($result)) {
		$temp = ($row['temperature'] - 32) / 1.8;
		$sg = $row['gravity'];
		$gravity = (-1 * 616.868) + 
			(1111.14 * $sg) - (630.272 * $sg * $sg) + 
			(135.997 * $sg * $sg * $sg);
		$timepoint = $row['timestamp'];
		//print_r($gravity);
		//echo "<br>"; 
		$temperatures[] = $temp;
		$gravities[] = $gravity;
		$timepoints[] = $timepoint;
	}
}
//print_r($gravities); 
//echo "<br>"; 
	
?>
<div id="fermentation" style="width:1400px;height:800px;"></div>
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

</body>
</html>
