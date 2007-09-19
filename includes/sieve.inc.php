<?php

function loadSieveTemplates() {
    $fields = array("require","template","regex","values");
    $rulesets = array("redirect","spamfilter","vacation");

    foreach ( $rulesets as $ruleset ) {
        $sieveFilter[$ruleset] = array();
        foreach ( $fields as $field ) {
            $sieveFilter[$ruleset][$field] = array();
        }
    }

    // Redirect Template
    $sieveFilter["redirect"]["template"] = '%STATUS%redirect "%RECIPIENT%"; keep; # REDIRECT';
    $sieveFilter["redirect"]["regex"] = '/^(.*)redirect "(.*)"; keep; # REDIRECT$/i';
    $sieveFilter["redirect"]["values"] = array("STATUS" => "",
                                               "RECIPIENT" => "");

    // Spamfilter Template
    $sieveFilter["spamfilter"]["template"] = '%STATUS%if header :matches "X-Spam-Flag" "yes" { %SIEVEACTION% }; # SPAMFILTER %ACTION%';
    $sieveFilter["spamfilter"]["regex"] = '/^(.*)if header :matches \"X-Spam-Flag\" \"yes\" \{ (.*) \}; # SPAMFILTER (.*)$/i';
    $sieveFilter["spamfilter"]["values"] = array("STATUS" => "",
                                                 "SIEVEACTION" => "",
                                                 "ACTION" => "");

    // Vacation Template
    $sieveFilter["vacation"]["require"] = "\"vacation\"";
    $sieveFilter["vacation"]["template"] = '%STATUS%vacation :days 7 :addresses "%RECIPIENT%" "%MESSAGE%"; # VACATION';
    $sieveFilter["vacation"]["regex"] = '/^(.*)vacation :days 7 :addresses "(.*)" "(.*)"; # VACATION$/i';
    $sieveFilter["vacation"]["values"] = array("STATUS" => "",
                                               "RECIPIENT" => "",
                                               "MESSAGE" => "");
    
    return $sieveFilter;
}

function createSieveFilter ( $sieveValues ) {
    $sieveFilter = loadSieveTemplates();

    foreach ( array_keys($sieveValues) as $categorie ) {
        $sieveFilterStr[$categorie] = $sieveFilter[$categorie]["template"];
        foreach ( $sieveValues[$categorie]["values"] as $keyword => $value ) {
           $sieveFilterStr[$categorie] = str_replace("%$keyword%", $value, $sieveFilterStr[$categorie]);
        }
    }
    $sieveFilterScript = implode("\n",$sieveFilterStr)."\n";  
   
    return (sieveEscapeChars($sieveFilterScript));
}

function parseSieveFilter ( $sieveFilterString ) {
    $sieveFilter = loadSieveTemplates();
    
    $lines = array();
    $lines = preg_split("/\n/",$sieveFilterString);
    $line = array_shift($lines);

    while ( isset($line) ) {
        foreach ( array_keys($sieveFilter) as $ruleset ) {
            if ( preg_match($sieveFilter[$ruleset]["regex"],$line,$values) ) {
                array_shift($values); // don't need the whole string in $0
                $i = 0;
                foreach ( array_keys($sieveFilter[$ruleset]["values"]) as $valuename ) {
                    $sieveFilter[$ruleset]["values"][$valuename] = sieveUnescapeChars($values[$i]);
                    $i++;
                }
            }
        }
    $line = array_shift($lines);
    }
    return $sieveFilter;
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
