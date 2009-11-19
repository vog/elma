<?php

function loadEximFilterTemplates() {
    $fields = array("filtertype","template","regex","values");
    $rulesets = array("spamfilter","vacation");

    foreach ( $rulesets as $ruleset ) {
        $eximFilter[$ruleset] = array();
        foreach ( $fields as $field ) {
            $eximFilter[$ruleset][$field] = array();
        }
    }

    // set the header, so that exim can determine what kind of filter languiage is used
    $eximFilter["filtertype"]["template"] = '%STATUS% Exim filter';
    $eximFilter["filtertype"]["regex"] = '/^(.*) Exim filter$/i';
    $eximFilter["filtertype"]["values"] = array("STATUS" => "#");
    
    // DomainAlias Template
    $eximFilter["maildomainalias"]["template"] = '%STATUS%deliver $local_part@%TARGETDOMAIN% # DOMAINALIAS';
    $eximFilter["maildomainalias"]["regex"] = '/^(.*)deliver \$local_part@(.*) # DOMAINALIAS$/i';
    $eximFilter["maildomainalias"]["values"] = array("STATUS" => "#",
	    					"TARGETDOMAIN" => "");

    // Redirect Template
    $eximFilter["redirect"]["template"] = '%STATUS%deliver %RECIPIENT% # REDIRECT';
    $eximFilter["redirect"]["regex"] = '/^(.*)deliver (.*) # REDIRECT$/i';
    $eximFilter["redirect"]["values"] = array("STATUS" => "#",
	    					"RECIPIENT" => "");

    // Keep Template
    $eximFilter["keep"]["template"] = '%STATUS%deliver %RECIPIENT% # KEEP';
    $eximFilter["keep"]["regex"] = '/^(.*)deliver (.*) # KEEP$/i';
    $eximFilter["keep"]["values"] = array("STATUS" => "#",
	    					"RECIPIENT" => "");

    // Spamfilter Template
    $eximFilter["spamfilter"]["template"] = '%STATUS%if $header_X-Spam-Flag: contains "YES" then %FILTERACTION% endif # SPAMFILTER %ACTION%';
    $eximFilter["spamfilter"]["regex"] = '/^(.*)if \$header_X-Spam-Flag: contains "YES" then (.*) endif # SPAMFILTER (.*)$/i';
    $eximFilter["spamfilter"]["values"] = array("STATUS" => "#",
                                                "FILTERACTION" => "",
                                                "ACTION" => "MARK");

    // Vacation Template
    $eximFilter["vacation"]["template"] = '%STATUS%if personal then mail from $local_part@$domain to $reply_address subject "Re: $h_subject:" text "%MESSAGE%" once $home/.vacation.db once_repeat 7d endif # VACATION';
    $eximFilter["vacation"]["regex"] = '/^(.*)if personal then mail from \$local_part@\$domain to \$reply_address subject "Re: \$h_subject:" text "(.*)" once \$home\/\.vacation.db once_repeat 7d endif # VACATION$/i';
    $eximFilter["vacation"]["values"] = array("STATUS" => "#",
                                              "MESSAGE" => "");
    
    return $eximFilter;
}

function createEximFilter ( $eximFilterValues ) {
    $eximFilter = loadEximFilterTemplates();
    $eximFilterValues["filtertype"]["values"] = $eximFilter["filtertype"]["values"];

    if ( ! empty($eximFilterValues["spamfilter"]["values"]["ACTION"]) ) {
        switch ($eximFilterValues["spamfilter"]["values"]["ACTION"]) {
            case "DISCARD": $eximFilterValues["spamfilter"]["values"]["FILTERACTION"] = "seen finish";
                            break;
            case "REDIRECT": $eximFilterValues["spamfilter"]["values"]["FILTERACTION"] = "deliver spam@\$domain";
                            break;
            case "FOLDER": $eximFilterValues["spamfilter"]["values"]["FILTERACTION"] = "save \$home/Maildir/.Spam/";
                            break;
        }
    }

    $eximFilterValues = array_set_as_first($eximFilterValues,"filtertype");
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
