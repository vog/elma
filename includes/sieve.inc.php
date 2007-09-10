<?php

function loadSieveTemplates() {
    include("lib/sieve/initSieve.php");
    return $sieveFilter;
}

function createSieveFilter ( $sieveFilter, $sieveValues ) {

    $requireValues .= implode(",",$sieveFilter["require"]);
    $sieveFilterScript = "require [$requireValues];\n";

    $index = 0;
    foreach ( $sieveValues as $categorie ) {
        $categories = array_keys($sieveValues);
        $categorie_name = strtolower($categories[$index]);
        foreach ( $categorie as $keyword => $value) {
            $sieveFilter["rules"][$categorie_name] = str_replace("%$keyword%", $value, $sieveFilter["rules"][$categorie_name]);
        }
        $index++;
        $sieveFilterScript .= implode("\n",$sieveFilter["rules"][$categorie_name])."\n";
    }

    //$sieveFilterScript .= implode("\n",$sieveFilter["rules"][$categorie_name]);
    my_print_r($sieveFilterScript);
    return (sieveEscapeChars($sieveFilterScript));
}

function parseSieveFilter ( $sieveFilter ) {
    $lines = array();
    $lines = preg_split("/\n/",$sieveFilter);
    $line = array_shift($lines);
    while ( isset($line) ) {
        unset ($values);
        if ( preg_match(,$line,$values) ) {
            echo "JA";
            $sieveValues["spamfilter"] = array( STATUS => sieveUnescapeChars($values[1]), 
                                                ACTION => sieveUnescapeChars($values[2]));
        }
        if ( preg_match('/^(.*)vacation :days 7 :addresses "(.*)" "(.*)"; # VACATION$/i',$line,$values) ) {
            $sieveValues["vacation"] = array( STATUS => sieveUnescapeChars($values[1]), 
                                           RECIPIENT => sieveUnescapeChars($values[2]),
                                             MESSAGE => sieveUnescapeChars($values[3]));
        }
        if ( preg_match('/^(.*)redirect "(.*)"; keep; # REDIRECT$/i',$line,$values) ) {
            $sieveValues["redirect"] = array( STATUS => sieveUnescapeChars($values[1]),
                                           RECIPIENT => sieveUnescapeChars($values[2]));
        }
    $line = array_shift($lines);
    }
    return $sieveValues;
}

/**
* Make a string safe for the encoded index. Replace CRLFs and & chars.
*
* @param string $string The string to make safe
* @return string The safe string
*/
function sieveEscapeChars($string)
{
    $string = preg_replace("/\r\n/", "\\n", $string);
    $string = preg_replace("/&/", "\&", $string);
    return $string;
}

/**
* Unescape a string made safe by escapeChars().
*
* @param string $string The string to unescape
* @return string The unescaped string
*/
function sieveUnescapeChars($string)
{
    $string = preg_replace("/\\\\n/", "\r\n", $string);
    $string = preg_replace("/\\\&/", "&", $string);
    return $string;
}


// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>
