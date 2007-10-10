<?php

function loadEximFilterTemplates() {
    $fields = array("template","regex","values");
    $rulesets = array("redirect","spamfilter","vacation");

    foreach ( $rulesets as $ruleset ) {
        $eximFilter[$ruleset] = array();
        foreach ( $fields as $field ) {
            $eximFilter[$ruleset][$field] = array();
        }
    }

    // Redirect Template
    $eximFilter["redirect"]["template"] = '%STATUS%deliver %RECIPIENT% # REDIRECT';
    $eximFilter["redirect"]["regex"] = '/^(.*)deliver (.*) # REDIRECT$/i';
    $eximFilter["redirect"]["values"] = array("STATUS" => "#",
                                              "RECIPIENT" => "");

    // Spamfilter Template
    $eximFilter["spamfilter"]["template"] = '%STATUS%if $header_X-Spam-Flag: contains "YES" then %FILTERACTION% endif # SPAMFILTER %ACTION%';
    $eximFilter["spamfilter"]["regex"] = '/^(.*)if \$header_X-Spam-Flag: contains "YES" then (.*) endif # SPAMFILTER (.*)$/i';
    $eximFilter["spamfilter"]["values"] = array("STATUS" => "#",
                                                "FILTERACTION" => "",
                                                "ACTION" => "MARK");

    // Vacation Template
    $eximFilter["vacation"]["template"] = '%STATUS%if personal then mail to $reply_address subject "Re: $h_subject:" text "%MESSAGE%" once $home/.vacation.db once_repeat 7d # VACATION';
    $eximFilter["vacation"]["regex"] = '/^(.*)if personal then mail to \$reply_address subject "Re: \$h_subject:" text "(.*)" once \$home\/\.vacation.db once_repeat 7d # VACATION$/i';
    $eximFilter["vacation"]["values"] = array("STATUS" => "#",
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
