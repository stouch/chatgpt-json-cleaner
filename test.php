<?php

require_once('./index.php');

$content = file_get_contents('./test.json.file');

// Replace manually when unit-testing :
// $content = '{     "many_cities": [] }';

$strings = explode("$$$", $content); // Separator must be really custom

foreach ($strings as $k => $string) {
    $steps = [];

    $old = $strings[$k];
    $strings[$k] = cleanJSON($string, 'details', $steps);
    $new = $strings[$k];

    // Check if new is a valid JSON, but does not check its consistency 
    $test = (json_decode($new, true) ? '1' : '0');
    echo "********************\n";
    echo "$k :\n";

    // Unit testing with details :
    if (count($strings) == 1) {
        $prevStep = null;
        foreach ($steps as $j => $step) {
            if ($step != $prevStep) {
                echo ">>>> " . $j . ':' . $step . "\n";
            }
            $prevStep = $step;
        }
    }

    if ($test == 0) {
        echo "NOT OK\n";
        echo $old;
        echo "\n";
        echo "\n";
        echo $new;
        echo "\n";
        echo "\n";
        echo "FAILED !\n";
        sleep(2);
    } else {
        echo "OK\n";
        echo "\n";
        usleep(200000);
    }
}
