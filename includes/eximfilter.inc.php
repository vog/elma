<?php

function loadSieveTemplates() {
    $fields = array("require","template","regex","values");
    $rulesets = array("redirect","spamfilter","vacation");

    foreach ( $rulesets as $ruleset ) {
        $eximFilterFilter[$ruleset] = array();
        foreach ( $fields as $field ) {
            $eximFilterFilter[$ruleset][$field] = array();
        }
    }

    // Redirect Template
    $eximFilterFilter["redirect"]["template"] = '%STATUS%redirect "%RECIPIENT%"; keep; # REDIRECT';
    $eximFilterFilter["redirect"]["regex"] = '/^(.*)redirect "(.*)"; keep; # REDIRECT$/i';
    $eximFilterFilter["redirect"]["values"] = array("STATUS" => "#",
                                               "RECIPIENT" => "");

    // Spamfilter Template
    $eximFilterFilter["spamfilter"]["template"] = '%STATUS%if header :matches "X-Spam-Flag" "yes" { %SIEVEACTION% }; # SPAMFILTER %ACTION%';
    $eximFilterFilter["spamfilter"]["regex"] = '/^(.*)if header :matches \"X-Spam-Flag\" \"yes\" \{ (.*) \}; # SPAMFILTER (.*)$/i';
    $eximFilterFilter["spamfilter"]["values"] = array("STATUS" => "#",
                                                 "SIEVEACTION" => "",
                                                 "ACTION" => "MARK");

    // Vacation Template
    $eximFilterFilter["vacation"]["require"] = "\"vacation\"";
    $eximFilterFilter["vacation"]["template"] = '%STATUS%vacation :days 7 :addresses "%RECIPIENT%" "%MESSAGE%"; # VACATION';
    $eximFilterFilter["vacation"]["regex"] = '/^(.*)vacation :days 7 :addresses "(.*)" "(.*)"; # VACATION$/i';
    $eximFilterFilter["vacation"]["values"] = array("STATUS" => "#",
                                               "RECIPIENT" => "",
                                               "MESSAGE" => "");
    
    return $eximFilterFilter;
}

function createSieveFilter ( $eximFilterValues ) {
    $eximFilterFilter = loadSieveTemplates();

    foreach ( array_keys($eximFilterValues) as $categorie ) {
        $eximFilterFilterStr[$categorie] = $eximFilterFilter[$categorie]["template"];
        foreach ( $eximFilterValues[$categorie]["values"] as $keyword => $value ) {
           $eximFilterFilterStr[$categorie] = str_replace("%$keyword%", $value, $eximFilterFilterStr[$categorie]);
        }
    }
    $eximFilterFilterScript = implode("\n",$eximFilterFilterStr)."\n";  
   
    return (eximFilterEscapeChars($eximFilterFilterScript));
}

function parseSieveFilter ( $eximFilterFilterString ) {
    $eximFilterFilter = loadSieveTemplates();
    
    $lines = array();
    $lines = preg_split("/\n/",$eximFilterFilterString);
    $line = array_shift($lines);

    while ( isset($line) ) {
        foreach ( array_keys($eximFilterFilter) as $ruleset ) {
            if ( preg_match($eximFilterFilter[$ruleset]["regex"],$line,$values) ) {
                array_shift($values); // don't need the whole string in $0
                $i = 0;
                foreach ( array_keys($eximFilterFilter[$ruleset]["values"]) as $valuename ) {
                    $eximFilterFilter[$ruleset]["values"][$valuename] = eximFilterUnescapeChars($values[$i]);
                    $i++;
                }
            }
        }
    $line = array_shift($lines);
    }
    return $eximFilterFilter;
}

/**
* Make a string safe for the encoded index. Replace CRLFs and & chars.
*
* @param string $string The string to make safe
* @return string The safe string
*/
function eximFilterEscapeChars($string)
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
function eximFilterUnescapeChars($string)
{
    $string = preg_replace("/\\\\n/", "\r\n", $string);
    $string = preg_replace("/\\\&/", "&", $string);
    return $string;
}


// vim:tabstop=4:expandtab:shiftwidth=4:filetype=php:syntax:ruler:
?>
