<?php

function calculateTemperature($fahrenheit)
{
	$celsius = ($fahrenheit - 32) / 1.8;
	return $celsius;
}

function calculateSGToPlato($sg)
{
	$plato = (-1 * 616.868) + 
		(1111.14 * $sg) - (630.272 * $sg * $sg) + 
		(135.997 * $sg * $sg * $sg);
	return $plato;
}

function calculateRealCurrentGravity($originalGravity, $currentGravity)
{		
	$realCurrentGravity = (0.1808 * $originalGravity) + (0.8192 * $currentGravity);
	return $realCurrentGravity;
}

function calculateAlcoholContentWeight($originalGravity, $currentGravity)
{
	$alcohol = ($originalGravity - $currentGravity) / (2.0665 - 0.010665 * $originalGravity);
	return $alcohol;
}

function calculateAlcoholContentVol($originalGravity, $currentGravity)
{
	$alcoholContentWeight = calculateAlcoholContentWeight($originalGravity, $currentGravity);
	$alcoholVol = $alcoholContentWeight / 0.795;
	return $alcoholVol;
}

function connectDB()
{
	if ($conn = mysqli_connect('localhost','tilt','tilt','tilt')){
		return $conn;
	} else {
		echo "Error connecting MySQL database!";
		return -1;
	}
}

function collectBeersSelectForm($conn)
{
	$query = "SELECT beer FROM hydrometer GROUP BY beer";
	$result = mysqli_query($conn, $query) or die('Error connecting to mysql');
	$first = 1;
	echo "<form method=\"get\">";
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
	return $first_beer;
}

function getStartTimestamp($conn, $beer)
{
	$query = "SELECT timestamp FROM hydrometer WHERE beer LIKE \"$beer\" ORDER BY timestamp ASC LIMIT 1";
	$result = mysqli_query($conn, $query) or die('Error on MySQL ' . __FUNCTION__);
	$row = mysqli_fetch_assoc($result);
	if ($row)
	{
		$timestamp = $row['timestamp'];
		return $timestamp;
	} else {
		echo "No mysql result for " . __FUNCTION__;
		return -1;
	}
}

function getStopTimestamp($conn, $beer)
{
	$query = "SELECT timestamp FROM hydrometer WHERE beer LIKE \"$beer\" ORDER BY timestamp DESC LIMIT 1";
	$result = mysqli_query($conn, $query) or die('Error on MySQL ' . __FUNCTION__);
	$row = mysqli_fetch_assoc($result);
	if ($row)
	{
		$timestamp = $row['timestamp'];
		return $timestamp;
	} else {
		echo "No mysql result for " . __FUNCTION__;
		return -1;
	}
}

function getFermentationDuration($conn, $beer)
{
	$query = "SELECT DATEDIFF('" . getStopTimestamp($conn, $beer) . "', '" . getStartTimestamp($conn, $beer) . "') AS days";
	$result = mysqli_query($conn, $query) or die('Error on MySQL ' . __FUNCTION__);
	$row = mysqli_fetch_assoc($result);
	if ($row)
	{
		$days = $row['days'];
		return $days;
	} else {
		echo "No mysql result for " . __FUNCTION__;
		return -1;
	};
}

function getOriginalGravity($conn, $beer)
{
	$query = "SELECT gravity FROM hydrometer WHERE beer LIKE \"$beer\" ORDER BY timestamp ASC LIMIT 1";
	$result = mysqli_query($conn, $query) or die('Error on MySQL ' . __FUNCTION__);
	$row = mysqli_fetch_assoc($result);
	if ($row)
	{
		$sg = calculateSGToPlato($row['gravity']);
		$sg = round($sg,1);
		$sg = 12;
		return $sg;
	} else {
		echo "No mysql result for " . __FUNCTION__;
		return -1;
	}
}

function getCurrentGravity($conn, $beer)
{
	$query = "SELECT gravity FROM hydrometer WHERE beer LIKE \"$beer\" ORDER BY timestamp DESC LIMIT 1";
	$result = mysqli_query($conn, $query) or die('Error on MySQL ' . __FUNCTION__);
	$row = mysqli_fetch_assoc($result);
	if ($row)
	{
		$sg = calculateSGToPlato($row['gravity']);
		$sg = round($sg,1);
		return $sg;
	} else {
		echo "No mysql result for " . __FUNCTION__;
		return -1;
	}
}

function getRealCurrentGravity($conn, $beer)
{
	$originalSG = getOriginalGravity($conn, $beer);
	$currentSG = getCurrentGravity($conn, $beer);
	if ($originalSG != -1 && $currentSG != -1){
		$sg = calculateRealCurrentGravity($originalSG, $currentSG);
		$sg = round($sg,1);
		return $sg;
	} else {
		echo "No gravity result for " . __FUNCTION__;
		return -1;
	}
}

function getAlcoholContentWeight($conn, $beer)
{
	$originalSG = getOriginalGravity($conn, $beer);
	$currentSG = getRealCurrentGravity($conn, $beer);
	if ($originalSG != -1 && $currentSG != -1){
		$sg = calculateAlcoholContentWeight($originalSG, $currentSG);
		$sg = round($sg,1);
		return $sg;
	} else {
		echo "No gravity result for " . __FUNCTION__;
		return -1;
	}
}

function getAlcoholContentVol($conn, $beer)
{
	$originalSG = getOriginalGravity($conn, $beer);
	$currentSG = getRealCurrentGravity($conn, $beer);
	if ($originalSG != -1 && $currentSG != -1){
		$sg = calculateAlcoholContentVol($originalSG, $currentSG);
		$sg = round($sg,1);
		return $sg;
	} else {
		echo "No gravity result for " . __FUNCTION__;
		return -1;
	}
}

function getDegreeFermentation($conn, $beer)
{
	return -1;
}

function getRealDegreeFermentation($conn, $beer)
{
	return -1;
}

function getGravityStableDays($conn, $beer)
{
	return -1;
}

function getMinTemperature($conn, $beer)
{
	$query = "SELECT temperature FROM hydrometer WHERE beer LIKE \"$beer\" ORDER BY temperature ASC LIMIT 1";
	$result = mysqli_query($conn, $query) or die('Error on MySQL ' . __FUNCTION__);
	$row = mysqli_fetch_assoc($result);
	if ($row)
	{
		$temperature = calculateTemperature($row['temperature']);
		$temperature = round($temperature,1);
		return $temperature;
	} else {
		echo "No mysql result for " . __FUNCTION__;
		return -1;
	}
}

function getMaxTemperature($conn, $beer)
{
	$query = "SELECT temperature FROM hydrometer WHERE beer LIKE \"$beer\" ORDER BY temperature DESC LIMIT 1";
	$result = mysqli_query($conn, $query) or die('Error on MySQL ' . __FUNCTION__);
	$row = mysqli_fetch_assoc($result);
	if ($row)
	{
		$temperature = calculateTemperature($row['temperature']);
		$temperature = round($temperature,1);
		return $temperature;
	} else {
		echo "No mysql result for " . __FUNCTION__;
		return -1;
	}
}

function getAverageTemperature($conn, $beer)
{
	$query = "SELECT AVG(temperature) AS avgTemperature FROM hydrometer WHERE beer LIKE \"$beer\" ORDER BY temperature DESC";
	$result = mysqli_query($conn, $query) or die('Error on MySQL ' . __FUNCTION__);
	$row = mysqli_fetch_assoc($result);
	if ($row)
	{
		$temperature = calculateTemperature($row['avgTemperature']);
		$temperature = round($temperature,1);
		return $temperature;
	} else {
		echo "No mysql result for " . __FUNCTION__;
		return -1;
	}
}

?>
