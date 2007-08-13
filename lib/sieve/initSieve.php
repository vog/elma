<?php

$sieveFilter["require"] = array();
$sieveFilter["rules"] = array();

array_push($sieveFilter["require"],"\"vacation\"");
array_push($sieveFilter["rules"], "%STATUS%vacation :days 7 :addresses \"%RECIPIENT%\" \"%MESSAGE%\";");

?>
