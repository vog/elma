<?php

$sieveFilter["require"] = array();
$sieveFilter["rules"] = array();
$sieveFilter["rules"]["vacation"] = array();



array_push($sieveFilter["require"],"\"vacation\"");
array_push($sieveFilter["rules"], "%REDIRECT.STATUS%redirect \"%REDIRECT.RECIPIENT%\"; keep; # REDIRECT");
array_push($sieveFilter["rules"], "%VACATION.STATUS%vacation :days 7 :addresses \"%VACATION.RECIPIENT%\" \"%VACATION.MESSAGE%\"; # VACATION");

?>
