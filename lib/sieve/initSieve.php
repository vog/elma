<?php


$sieveFilter["require"] = array();
$sieveFilter["rules"] = array();

foreach ( $field as array("templates","regex","values") ) {
    $sieveFilter[$field] = array();
    foreach ( $ruleset as array("redirect","spamfilter_discard","vacation") ) { 
        $sieveFilter[$field][$ruleset] = array();
    }
}

// Redirect Template
array_push($sieveFilter["templates"]["redirect"], '%STATUS%redirect "%RECIPIENT%"; keep; # REDIRECT');
array_push($sieveFilter["regex"]["redirect"], '/^(.*)redirect "(.*)"; keep; # REDIRECT$/i');
array_push($sieveFilter["values"]["redirect"], array("STATUS","REDIRECT"));

// Spamfilter Template
array_push($sieveFilter["templates"]["spamfilter_discard"], '%STATUS%if header :matches "X-Spam-Flag" "yes" { discard; }; # SPAMFILTER');
array_push($sieveFilter["regex"]["spamfilter_discard"], '/^(.*)if header :matches \"X-Spam-Flag\" \"yes\" \{(.*)\}; # SPAMFILTER$/i');
array_push($sieveFilter["values"]["spamfilter_discard"], array("STATUS"));

// Vacation Template
array_push($sieveFilter["require"],"\"vacation\"");
array_push($sieveFilter["templates"]["vacation"], '%STATUS%vacation :days 7 :addresses "%RECIPIENT%" "%MESSAGE%"; # VACATION');
array_push($sieveFilter["regex"]["vacation"], '/^(.*)vacation :days 7 :addresses "(.*)" "(.*)"; # VACATION$/i'
array_push($sieveFilter["values"]["vacation"], array("STATUS","RECIPIENT","MESSAGE"));


?>
