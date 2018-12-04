<?php

// Copyright 2018 Max Sprauer

$offsets = file_get_contents('input.txt');
$offsets = explode("\n", $offsets);

print_r($offsets);

$offset = 0;
$steps = 0;
do {
    $steps++;

    $oldOffset = $offset;

    // Jump to new offset
    $offset += $offsets[$offset];

    // Part 1
    // Increment old offset
    // $offsets[$oldOffset]++;

    // Part 2
    if ($offsets[$oldOffset] >= 3) {
        $offsets[$oldOffset]--;    
    } else {
        // Increment old offset
        $offsets[$oldOffset]++;
    }

} while ($offset < count($offsets));

print "Steps: $steps\n";
