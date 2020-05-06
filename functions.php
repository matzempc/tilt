<?php

$username = 'tilt';
$password = 'tilt';
$database = 'tilt';

class tiltMySQL
{
	private $conn;
	private $beer;
	private $originalGravity;
	private $currentGravity;
	private $currentSG;
	private $realCurrentGravity;
	public $temperatures = array();
	public $gravities = array();
	public $timepoints = array();
	
	function __construct($selectedBeer)
	{
		$this->conn = $this->connectDB();
		$this->beer = $this->gatherBeer($selectedBeer);
		$this->originalGravity = $this->getOriginalGravity();
		$this->currentGravity = $this->getCurrentGravity();
		$this->currentSG = $this->getCurrentSG();
		$this->realCurrentGravity = $this->getRealCurrentGravity();
		$this->getTemperaturesGravitiesTimepoints();
	}
	
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

	function calculateRealCurrentGravity()
	{		
		$realCurrentGravity = (0.1808 * $this->originalGravity) + (0.8192 * $this->currentGravity);
		return $realCurrentGravity;
	}

	function calculateAlcoholContentWeight()
	{
		$alcohol = ($this->originalGravity - $this->realCurrentGravity) / (2.0665 - 0.010665 * $this->originalGravity);
		return $alcohol;
	}

	function calculateAlcoholContentVol()
	{
		$alcoholContentWeight = $this->calculateAlcoholContentWeight();
		$alcoholVol = $alcoholContentWeight / 0.795;
		return $alcoholVol;
	}

	function calculateFermentationDegree()
	{
		$fermentationDegree = ($this->originalGravity - $this->currentGravity) * 100 / $this->originalGravity;
		return $fermentationDegree;
	}

	function calculateRealFermentationDegree()
	{
		$fermentationDegree = $this->calculateFermentationDegree();
		if ($fermentationDegree != - 1)
		{
			$realFermentationDegree = $fermentationDegree * 0.81;
		} else {
			return -1;
		}
		return $realFermentationDegree;
	}

	function calculateDensity()
	{
		$density = 261.1 / (261.53 - $this->currentGravity);
		return $density;
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
	
	function initialized()
	{
		if ($this->currentGravity == -1)
		{
			return 0;
		}
		return 1;
	}
	
	function getBeer()
	{
		return $this->beer;
	}
	
	function getTemperaturesGravitiesTimepoints()
	{
		$query = "SELECT timestamp, gravity, temperature FROM hydrometer WHERE beer LIKE \"$this->beer\"";
		$result = mysqli_query($this->conn, $query) or die('Error on temperature, gravity graph query');
		while ($row = mysqli_fetch_assoc($result)) {
			$temp = $this->calculateTemperature($row['temperature']);
			$sg = $row['gravity'];
			$gravity = $this->calculateSGToPlato($sg);
			$timepoint = $row['timestamp'];
			$this->temperatures[] = $temp;
			$this->gravities[] = $gravity;
			$this->timepoints[] = $timepoint;
		}
	}

	function printBeersSelectForm()
	{
		$query = "SELECT beer FROM hydrometer GROUP BY beer";
		$result = mysqli_query($this->conn, $query) or die('Error connecting to mysql');
		echo "<form method=\"get\">";
		echo "<select name=\"beer\">";
		while ($row = mysqli_fetch_assoc($result)) {
			$beer = $row['beer'];
			if ($this->beer == $beer)
			{
				echo "<option value=\"$beer\" selected> $beer";
			} else {
				echo "<option value=\"$beer\"> $beer";
			}
		}
		echo "</select><br>";
		echo "<p><input type = \"submit\" value = \"OK\">";
		echo "</form>";
	}
	
	function gatherBeer($selectedBeer)
	{
		if ($selectedBeer == ""){
			$selectedBeer = $this->getLatestBeer();
		}
		return $selectedBeer;
	}

	function getLatestBeer()
	{
		$query = "SELECT beer FROM hydrometer ORDER BY timestamp DESC LIMIT 1";
		$result = mysqli_query($this->conn, $query) or die('Error on MySQL ' . __FUNCTION__);
		$row = mysqli_fetch_assoc($result);
		if ($row)
		{
			$beer = $row['beer'];
			return $beer;
		} else {
			echo "No mysql result for " . __FUNCTION__;
			return -1;
		}
	}

	function getStartTimestamp()
	{
		$query = "SELECT timestamp FROM hydrometer WHERE beer LIKE \"$this->beer\" ORDER BY timestamp ASC LIMIT 1";
		$result = mysqli_query($this->conn, $query) or die('Error on MySQL ' . __FUNCTION__);
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

	function getStopTimestamp()
	{
		$query = "SELECT timestamp FROM hydrometer WHERE beer LIKE \"$this->beer\" ORDER BY timestamp DESC LIMIT 1";
		$result = mysqli_query($this->conn, $query) or die('Error on MySQL ' . __FUNCTION__);
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

	function getFermentationDuration()
	{
		$query = "SELECT DATEDIFF('" . $this->getStopTimestamp() . "', '" . $this->getStartTimestamp() . "') AS days";
		$result = mysqli_query($this->conn, $query) or die('Error on MySQL ' . __FUNCTION__);
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

	function getOriginalGravity()
	{
		$query = "SELECT gravity FROM hydrometer WHERE beer LIKE \"$this->beer\" ORDER BY timestamp ASC LIMIT 1";
		$result = mysqli_query($this->conn, $query) or die('Error on MySQL ' . __FUNCTION__);
		$row = mysqli_fetch_assoc($result);
		if ($row)
		{
			$sg = $this->calculateSGToPlato($row['gravity']);
			$sg = round($sg,1);
			return $sg;
		} else {
			echo "No mysql result for " . __FUNCTION__;
			return -1;
		}
	}

	function getCurrentGravity()
	{
		$query = "SELECT gravity FROM hydrometer WHERE beer LIKE \"$this->beer\" ORDER BY timestamp DESC LIMIT 1";
		$result = mysqli_query($this->conn, $query) or die('Error on MySQL ' . __FUNCTION__);
		$row = mysqli_fetch_assoc($result);
		if ($row)
		{
			$sg = $this->calculateSGToPlato($row['gravity']);
			$sg = round($sg,1);
			return $sg;
		} else {
			echo "No mysql result for " . __FUNCTION__;
			return -1;
		}
	}

	function getCurrentSG()
	{
		$query = "SELECT gravity FROM hydrometer WHERE beer LIKE \"$this->beer\" ORDER BY timestamp DESC LIMIT 1";
		$result = mysqli_query($this->conn, $query) or die('Error on MySQL ' . __FUNCTION__);
		$row = mysqli_fetch_assoc($result);
		if ($row)
		{
			$currentSG = round($row['gravity'],3);
			return $currentSG;
		} else {
			echo "No current gravity mysql result for " . __FUNCTION__;
			return -1;
		}
	}
	
	function getCurrentGravityFirstTimeStamp()
	{
		$currentSGLow = $this->currentSG - 0.001;
		$currentSGHigh = $this->currentSG + 0.001;
		$query = "SELECT timestamp FROM hydrometer WHERE beer LIKE \"$this->beer\" AND gravity >= $currentSGLow AND gravity <= $currentSGHigh ORDER BY timestamp ASC LIMIT 1";
		$result = mysqli_query($this->conn, $query) or die('Error on MySQL ' . __FUNCTION__);
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

	function getRealCurrentGravity()
	{
		$sg = $this->calculateRealCurrentGravity();
		$sg = round($sg,1);
		return $sg;
	}

	function getAlcoholContentWeight()
	{
		$sg = $this->calculateAlcoholContentWeight();
		$sg = round($sg,1);
		return $sg;
	}

	function getAlcoholContentVol()
	{
		$sg = $this->calculateAlcoholContentVol();
		$sg = round($sg,1);
		return $sg;
	}

	function getDegreeFermentation()
	{
		$fermentationDegree = $this->calculateFermentationDegree();
		$fermentationDegree = round($fermentationDegree);
		return $fermentationDegree;
	}

	function getRealDegreeFermentation()
	{
		$fermentationDegree = $this->calculateRealFermentationDegree();
		$fermentationDegree = round($fermentationDegree);
		return $fermentationDegree;
	}

	function getCaloriesHalfLiter()
	{
		$density = $this->calculateDensity();
		$alcohol = $this->calculateAlcoholContentWeight();
		$kcal = $density * (3.5 * $this->realCurrentGravity + 7 * $alcohol);
		$kcalHalfLiter = round ($kcal * 5);
		return $kcalHalfLiter;
	}

	function getKiloJouleHalfLiter()
	{
		$kcal = $this->getCaloriesHalfLiter();
		if ($kcal != -1){
			$kJ = round($kcal * 4.184);
			return $kJ;
		} else {
			return -1;
		}
	}

	function getGravityStableDays()
	{
		$datetime1 = new DateTime($this->getCurrentGravityFirstTimeStamp());
		$datetime2 = new DateTime($this->getStopTimestamp());
		$interval = $datetime1->diff($datetime2);
		if ($interval->d > 1)
		{
			return $interval->format('%d Tage %h Stunden');
		} else {
			return $interval->format('%d Tag %h Stunden');	
		}
	}

	function getMinTemperature()
	{
		$query = "SELECT temperature FROM hydrometer WHERE beer LIKE \"$this->beer\" ORDER BY temperature ASC LIMIT 1";
		$result = mysqli_query($this->conn, $query) or die('Error on MySQL ' . __FUNCTION__);
		$row = mysqli_fetch_assoc($result);
		if ($row)
		{
			$temperature = $this->calculateTemperature($row['temperature']);
			$temperature = round($temperature,1);
			return $temperature;
		} else {
			echo "No mysql result for " . __FUNCTION__;
			return -1;
		}
	}

	function getMaxTemperature()
	{
		$query = "SELECT temperature FROM hydrometer WHERE beer LIKE \"$this->beer\" ORDER BY temperature DESC LIMIT 1";
		$result = mysqli_query($this->conn, $query) or die('Error on MySQL ' . __FUNCTION__);
		$row = mysqli_fetch_assoc($result);
		if ($row)
		{
			$temperature = $this->calculateTemperature($row['temperature']);
			$temperature = round($temperature,1);
			return $temperature;
		} else {
			echo "No mysql result for " . __FUNCTION__;
			return -1;
		}
	}

	function getAverageTemperature()
	{
		$query = "SELECT AVG(temperature) AS avgTemperature FROM hydrometer WHERE beer LIKE \"$this->beer\" ORDER BY temperature DESC";
		$result = mysqli_query($this->conn, $query) or die('Error on MySQL ' . __FUNCTION__);
		$row = mysqli_fetch_assoc($result);
		if ($row)
		{
			$temperature = $this->calculateTemperature($row['avgTemperature']);
			$temperature = round($temperature,1);
			return $temperature;
		} else {
			echo "No mysql result for " . __FUNCTION__;
			return -1;
		}
	}

	function getBeerComment()
	{
		$query = "SELECT comment FROM hydrometer WHERE beer LIKE \"$this->beer\" ORDER BY timestamp DESC LIMIT 1";
		$result = mysqli_query($this->conn, $query) or die('Error on MySQL ' . __FUNCTION__);
		$row = mysqli_fetch_assoc($result);
		if ($row)
		{
			$comment = $row['comment'];
			return $comment;
		} else {
			echo "No mysql result for " . __FUNCTION__;
			return -1;
		}
	}
}
?>
