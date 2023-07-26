<?php

/**
 * 
 * Use $steps to debug
 * 
 */
function cleanJSON($json, $missingKeyName = 'details', &$steps = [])
{
        $steps = [];

        $json = preg_replace('/\xc2\xa0/', ' ', $json); // clean spaces like &nbsp;
        $json = preg_replace('/[\x00-\x1F\x7F-\xA0\xAD]/u', ' ', $json); // clean non printing chars
        
        $steps[] = $json; // 0

        $json = str_replace(['“', '”', '`'], '"', $json);
        $json = str_replace([':-)', ' :D ', ' :P ', ' :p '], '', $json); // We may find emojis in comments that gonna create ambiguities with properties semicol...
        $json = preg_replace('/\/\/([^"\/]+)[\s]{5,}/', '', $json); // Clean obvious comments  `  // Lorem ipsum    
        $json = preg_replace('/"[\s]*,[\s]*\.\.\.[\s]*\]/', '"]', $json); // Clean lost ellipsis at the end of an array  `", ...]`
        $json = preg_replace('/\/\/([\s]*Ex(a|e)*(mple)*)[\s]*:/', '// Ambigous comment', $json); // Pre-clean ambigous comments  `  // Example: ... ` for which we could not say if "Example" is a property;

        $steps[] = $json; // 1

        $json = preg_replace('/""([\wA-zÀ-ú])/', '"$1', $json); // Clean : ` ""Lorem ipsum dolor sit amet.. `
        $json = preg_replace('/[\s+]\.[\s]*"/', ' , "', $json); // Clean  `  .  "location": ... ` When it outputs dot instead of comma

        $steps[] = $json; // 2  etc.

        $json = preg_replace('/,[\s]+(\w+)"[\s]*:/', ',"$1":', $json); // Clean:  ` , ... location": ... 
        $json = preg_replace('/"[\s]*\+[\s]*"/', ' ', $json); // Clean: ` "  +  " ` in bullet points texts
        $steps[] = $json;
        $json = preg_replace('/([\s]+)(\w+)[\s]*:([\wA-zÀ-ú\-\(\)’\'\ \,\.\;]+)\}/', '$1"$2": "$3"}', $json); // Clean: `  location : Paris  ... }   `
        $steps[] = $json;
        // A-Za-z  !=  A-z   because A-z includes [, ] etc.
        $json = preg_replace('/\"(\w+)\"[\s]*:((?![\s]*(false|true))[\wA-Za-zÀ-ú\_`\-\(\)’\'\ \,\.\;]+)\}/', '"$1": "$2"}', $json); // Clean: `  "location" : Paris  ... }  ` before the ending '}' but dont replace false, true and []
        $steps[] = $json;
        $json = preg_replace('/([\s]+)(\w+)[\s]*:([\wA-zÀ-ú\-\(\)’\'\ \.\;]+)\,[\s+]*"/', '$1"$2": "$3", "', $json); // Clean: `  location : Paris  ... ,  "another"...`
        $steps[] = $json;
        $json = preg_replace('/\"(\w+)\"[\s]*:([\wA-zÀ-ú\-\(\)’\'\ \.\;]+)\,/', '"$1": "$2",', $json); // Clean: `  "location" : Paris  ... ,  `
        $steps[] = $json;
        $json = preg_replace('/([\s]{0,1})"(\w+)"[\s]*:([\wA-zÀ-ú\-\(\)’\'\ \.\;]+)\"[\s]*(,|})/', '$1"$2": "$3" $4', $json); // Clean: `  "location" : Paris ..."  `

        $steps[] = $json;

        $hasBadStartedKeys = 1;
        while ($hasBadStartedKeys > 0) {
            $json = preg_replace('/([\s]{2,}|\"[\s]*,[\s]+)(\w+)[\s]*:[\s]*\"([^"]+)\"[\s]*(,|})/', '$1"$2": "$3"$4', $json, -1, $hasBadStartedKeys); // Clean: `  location: "Paris ..."  `
        }

        $json = preg_replace('/([\s]{2,})(\w+)[\s]*:([\wA-zÀ-ú\-\(\)’\'\ \.\;]+)\"[\s]*(,|})/', '$1"$2": "$3"$4', $json); // Clean: `  location : Paris ..."  ` but MUST not be inside a text value (with 2 space before, we conclude a chatgpt mistake)

        $steps[] = $json;
        
        $hasUnfinishedValues = 1;
        while ($hasUnfinishedValues > 0) {
            $json = preg_replace('/:[\s]*"([^"}]+)(}|,[^"}]*"(\w+)":)/', ':"$1"$2', $json, -1, $hasUnfinishedValues); // Clean : ` "location": "Lorem ipsum,  "another": " `  or  ` "location": "Lorem ipsum } ` (missing end quote of a value)
        }

        $steps[] = $json;

        $hasComments = 1;
        while ($hasComments > 0) {
            $json = preg_replace('/\/\/([\wA-zÀ-ú0-9\/\@\-\(\)’\'\s\,\:\!\<\;\.]+)(\"|\})/', '$2', $json, -1, $hasComments); // Clean ambigous comments delimited with quote of the next property (or with })
        }

        $steps[] = $json;

        $hasDelimitedGarbage = 1;
        while ($hasDelimitedGarbage > 0) {
            $json = preg_replace('/([^":\s]+)"[\s]*,[^"]{2,}"/', '$1", "', $json, -1, $hasDelimitedGarbage); // Clean any garbage between two properties
        }
        $json = preg_replace('/([^":\s]+)"[\s]*,[^"]{2,}\}/', '$1"}', $json); // Clean any garbage in the json end before }
        $json = preg_replace('/"[\s]*,[\s]*"[\s]*,[\s]*"/', '", "', $json); // Clean lost quotes
        $json = preg_replace('/"[\s]*,[\s]*"[\s]*}/', '"}', $json); // Clean lost quotes at the end
        $json = preg_replace('/\}[\s]*,[\s]*\]/', '}]', $json); // Clean misc garbage

        $steps[] = $json;

        $json = preg_replace('/",[\s]+-/', '", "' . $missingKeyName . '": "', $json); // Ugly fix to handle bug ` ", .... - lorem ipsum", ... ` (missing json key before the value when tirets)

        $json = rtrim($json, '.');
        $json = trim($json, "\n\r\t\v\x00");

        $steps[] = $json;

        return $json;

}
