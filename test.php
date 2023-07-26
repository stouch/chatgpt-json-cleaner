<?php

require_once('./index.php');

$content = file_get_contents('./test.json.file');
$strings = explode("$$$", $content); // Separator must be really custom

foreach ($strings as $k => $string) {
    $old = $strings[$k];
    $strings[$k] = cleanJSON($string);
    $new = $strings[$k];

    // Check if new is a valid JSON, but does not check its consistency 
    $test = (json_decode($new, true) ? '1' : '0');
    echo "********************\n";
    echo "$k :\n";
    if ($test == 0) {
        echo "FAIL !\n";
        echo $old;
        echo "\n";
        echo "\n";
        echo $new;
        echo "\n";
    } else {
        echo "OK\n";
    }
    usleep(200000);
}
