<?php

$fields = array("require","template","regex","values");
$rulesets = array("redirect","spamfilter_discard","vacation");

foreach ( $rulesets as $ruleset ) { 
    $sieveFilter[$ruleset] = array();
    foreach ( $fields as $field ) {
        $sieveFilter[$ruleset][$field] = array();
    }
}

// Redirect Template
array_push($sieveFilter["redirect"]["template"], '%STATUS%redirect "%RECIPIENT%"; keep; # REDIRECT');
array_push($sieveFilter["redirect"]["regex"], '/^(.*)redirect "(.*)"; keep; # REDIRECT$/i');
array_push($sieveFilter["redirect"]["values"], array("STATUS","REDIRECT"));

// Spamfilter Template
array_push($sieveFilter["spamfilter_discard"]["template"], '%STATUS%if header :matches "X-Spam-Flag" "yes" { discard; }; # SPAMFILTER_DISCARD');
array_push($sieveFilter["spamfilter_discard"]["regex"], '/^(.*)if header :matches \"X-Spam-Flag\" \"yes\" \{(.*)\}; # SPAMFILTER_DISCARD$/i');
array_push($sieveFilter["spamfilter_discard"]["values"], array("STATUS"));

// Vacation Template
array_push($sieveFilter["vacation"]["require"],"\"vacation\"");
array_push($sieveFilter["vacation"]["template"], '%STATUS%vacation :days 7 :addresses "%RECIPIENT%" "%MESSAGE%"; # VACATION');
array_push($sieveFilter["vacation"]["regex"], '/^(.*)vacation :days 7 :addresses "(.*)" "(.*)"; # VACATION$/i');
array_push($sieveFilter["vacation"]["values"], array("STATUS","RECIPIENT","MESSAGE"));

?>
