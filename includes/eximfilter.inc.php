<?php

function loadEximFilterTemplates() {
    $fields = array("require","template","regex","values");
    $rulesets = array("redirect","spamfilter","vacation");

    foreach ( $rulesets as $ruleset ) {
        $eximFilter[$ruleset] = array();
        foreach ( $fields as $field ) {
            $eximFilter[$ruleset][$field] = array();
        }
    }

    // Redirect Template
    $eximFilter["redirect"]["template"] = '%STATUS%redirect "%RECIPIENT%"; keep; # REDIRECT';
    $eximFilter["redirect"]["regex"] = '/^(.*)redirect "(.*)"; keep; # REDIRECT$/i';
    $eximFilter["redirect"]["values"] = array("STATUS" => "#",
                                               "RECIPIENT" => "");

    // Spamfilter Template
    $eximFilter["spamfilter"]["template"] = '%STATUS%if header :matches "X-Spam-Flag" "yes" { %SIEVEACTION% }; # SPAMFILTER %ACTION%';
    $eximFilter["spamfilter"]["regex"] = '/^(.*)if header :matches \"X-Spam-Flag\" \"yes\" \{ (.*) \}; # SPAMFILTER (.*)$/i';
    $eximFilter["spamfilter"]["values"] = array("STATUS" => "#",
                                                 "SIEVEACTION" => "",
                                                 "ACTION" => "MARK");

    // Vacation Template
    $eximFilter["vacation"]["require"] = "\"vacation\"";
    $eximFilter["vacation"]["template"] = '%STATUS%vacation :days 7 :addresses "%RECIPIENT%" "%MESSAGE%"; # VACATION';
    $eximFilter["vacation"]["regex"] = '/^(.*)vacation :days 7 :addresses "(.*)" "(.*)"; # VACATION$/i';
    $eximFilter["vacation"]["values"] = array("STATUS" => "#",
                                               "RECIPIENT" => "",
                                               "MESSAGE" => "");
    
    return $eximFilter;
}

function createEximFilter ( $eximFilterValues ) {
    $eximFilter = loadEximFilterTemplates();

    foreach ( array_keys($eximFilterValues) as $categorie ) {
        $eximFilterStr[$categorie] = $eximFilter[$categorie]["template"];
        foreach ( $eximFilterValues[$categorie]["values"] as $keyword => $value ) {
           $eximFilterStr[$categorie] = str_replace("%$keyword%", $value, $eximFilterStr[$categorie]);
        }
    }
    $eximFilterScript = implode("\n",$eximFilterStr)."\n";  
   
    return (eximFilterEscapeChars($eximFilterScript));
}

function parseEximFilter ( $eximFilterStr ) {
    $eximFilter = loadEximFilterTemplates();
    
    $lines = array();
    $lines = preg_split("/\n/",$eximFilterStr);
    $line = array_shift($lines);

    while ( isset($line) ) {
        foreach ( array_keys($eximFilter) as $ruleset ) {
            if ( preg_match($eximFilter[$ruleset]["regex"],$line,$values) ) {
                array_shift($values); // don't need the whole string in $0
                $i = 0;
                foreach ( array_keys($eximFilter[$ruleset]["values"]) as $valuename ) {
                    $eximFilter[$ruleset]["values"][$valuename] = eximFilterUnescapeChars($values[$i]);
                    $i++;
                }
            }
        }
    $line = array_shift($lines);
    }
    return $eximFilter;
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
