<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
<title>Tiltlogger</title>
  </head>
  <body>
<div align="center">
<?php
/*
$Timepoint = "43955.78502631945";
$Temp = "70.0";
$SG = "1.0238601134215501";
$Beer = "Blonde Beauty,2746";
$Color = "BLUE";
$Comment = "";
if ($connection = mysqli_connect('localhost','tilt','tilt','tilt')){
    $sql = "INSERT INTO hydrometer (timepoint, temperature, gravity, beer, color, comment) "
	 . "VALUES (\"" . 
	 $Timepoint . "\",\"" . 
	 $Temp . "\",\"" . 
	 $SG . "\",\"" . 
	 $Beer. "\",\"" . 
	 $Color . "\",\"" . 
	 $Comment . "\")";
    echo $sql . "\n";
    $result = mysqli_query($connection, $sql);
  } else {
	  echo "connect failed";
	      echo "Fehler: konnte nicht mit MySQL verbinden.";
    echo "Debug-Fehlernummer: " . mysqli_connect_errno();
    echo "Debug-Fehlermeldung: " . mysqli_connect_error();
    exit;
  }
*/
if ($connection = mysqli_connect('localhost','tilt','tilt','tilt')){
    $sql = "INSERT INTO hydrometer (timepoint, temperature, gravity, beer, color, comment) "
	 . "VALUES (\"" . $_POST["Timepoint"] 
	 . "\",\"" . $_POST["Temp"] 
	 . "\",\"" . $_POST["SG"] 
	 . "\",\"" . $_POST["Beer"]
	 . "\",\"" . $_POST["Color"] 
	 . "\",\"" . $_POST["Comment"] . "\")";
    echo $sql . "\n";
    $result = mysqli_query($connection, $sql);
  }

?>
</div>
  </body>
</html>
