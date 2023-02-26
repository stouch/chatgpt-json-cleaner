<?php

function cleanJSON($json, $missingKeyName = 'details')
{
        $json = preg_replace('/\/\/([\wA-zÀ-ú\-\(\)\'\s\,\:\!\.\"]+)\n/s', '', $json); // Remove comments 

        $json = preg_replace('/[\s]+(\w+)[\s]*:/', '"$1":', $json); // clean: `  location: ... `
        $json = preg_replace('/,[\s]+(\w+)"[\s]*:/', ',"$1":', $json); // clean: ` , ... location": ... `
        $json = preg_replace('/"[\s]*\+[\s]*"/', ' ', $json); // remove the : ` "  +  " `  in lists
        $json = preg_replace('/[\s]+(\w+)[\s]*:([\wA-zÀ-ú\-\(\)\'\ \,\.]+)\}/', '"$1": "$2"}', $json); // clean: `  location : Paris  ... }   `
        $json = preg_replace('/\"(\w+)\"[\s]*:([\wA-zÀ-ú\-\(\)\'\ \,\.]+)\}/', '"$1": "$2"}', $json); // clean: `  "location" : Paris  ... }  `
        $json = preg_replace('/[\s]+(\w+)[\s]*:([\wA-zÀ-ú\-\(\)\'\ \.]+)\,/', '"$1": "$2",', $json); // clean: `  location : Paris  ... ,   `
        $json = preg_replace('/\"(\w+)\"[\s]*:([\wA-zÀ-ú\-\(\)\'\ \.]+)\,/', '"$1": "$2",', $json); // clean: `  "location" : Paris  ... ,  `
        $json = preg_replace('/",(([\s]+,)+)/', '",', $json); // Clean les bugs `  ",                              ,     , `

        $json = preg_replace('/",[\s]+-/', '", "' . $missingKeyName . '": "', $json); // Ugly fix to handle bug ` ", ....  - Some value lorem ipsum", ... ` (missing json key before the value when it contains tirets)

        return $json;
}
