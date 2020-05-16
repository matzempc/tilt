<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
<title>Tiltlogger</title>
  </head>
  <body>
<div align="center">
<?php
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
