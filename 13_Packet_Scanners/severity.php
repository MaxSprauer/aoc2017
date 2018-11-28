<?php

// Copyright 2018 Max Sprauer

$layers = array();
$lines = file_get_contents('input');
$lines = explode("\r\n", $lines);

foreach ($lines as $line) {
    if (!empty($line)) {    
        if (!preg_match("/^(\d+): (\d+)$/", $line, $matches)) {
            error_log('Bad line: ' . $line);
            exit(1);
        }

        $layers[$matches[1]] = $matches[2];
    }
}

$lastLayer = $matches[1];

// print_r($layers);

$severity = 0;

for ($ps = 0, $layer = 0; $ps <= $lastLayer; $ps++, $layer++) {
    if (0 == scannerPositionAtPS($ps, $ps)) {
        print "Hit at layer depth $layer, range of {$layers[$layer]}\n";
        $severity += ($layers[$layer] * $ps);
    }
}

print "Total severity: $severity\n";

// PART 2 There's probably a better way to do this, but this is the least code
$startPS = -1;

do {
    $startPS++;
//   print "Start time: $startPS\n"; 
} while (!trySneaking($startPS, $lastLayer));

print "Snuck through starting at $startPS.\n";

function trySneaking($startPS, $lastLayer)
{
    global $layers;

    for ($layer = 0, $ps = $startPS; $layer <= $lastLayer; $layer++, $ps++) {
        if (0 == scannerPositionAtPS($layer, $ps)) {
  //          print "  Hit at layer depth $layer\n";
            return false;
        }
    }
    
    return true;
}

function scannerPositionAtPS($layer, $ps)
{
    global $layers;

    if (isset($layers[$layer])) {
        $range = $layers[$layer];
        $maxValue = $range - 1;

        // A slot is the index in the ascending + descending sequence the scanner is.  The sequence
        // for a layer with a range of 4 is 0, 1, 2, 3, 2, 1. 
        $slot = $ps % (2 * $maxValue);

        // The distance from the middle is the abs value of the range minus slot
        $dist = abs($maxValue - $slot);

        // The position is the abs value of the difference between the max value and the distance
        $pos = abs($maxValue - $dist);

        return $pos;
    }

    return -1;
}
