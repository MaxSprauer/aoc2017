<?php

/* Copyright 2018 Max Sprauer
 * I'm in the car in on the way to Niagara Falls.  Ironic that this is the traveling
 * salesman problem. */

$programs = array();
$lines = file_get_contents('input');
$lines = explode("\n", $lines);

foreach ($lines as $line) {
    if (!empty($line)) {    
        if (!preg_match("/^(\d+) <-> (.+)$/", $line, $matches)) {
            error_log('Bad line: ' . $line);
            exit(1);
        }

        $programs[$matches[1]] = array_map('trim', explode(',', $matches[2]));
    }
}

# print_r($programs);

// I think we need a BFS that does not visit nodes that have been visited
$queue = array(0);
$index = 0;

while ($index < count($queue)) {
    $index = BFS($index, $queue);
   // print "$index, " . print_r($queue, true) . "\n";
}

print("Total in 0's group: " . count($queue) . "\n");

function BFS($index, &$queue) {
    global $programs;
    $node = $queue[$index]; 

    // Find children not visited
    $not_in_path = array_diff($programs[$node], $queue);
    if (!empty($not_in_path)) {
        foreach($not_in_path as $child) {
            $queue[] = $child;
        }
    }
    
    return $index + 1;
}
