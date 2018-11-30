<?php
// Copyright 2018 Max Sprauer

require_once 'MyGenerator.class.php';

// Sample input
$a = new MyGenerator(16807, 65);
$b = new MyGenerator(48271, 8921);



// Real input
$a = new MyGenerator(16807, 116);
$b = new MyGenerator(48271, 299);
$iterations = 40000000;

// Part two
$a = new MyGenerator(16807, 116, 4);
$b = new MyGenerator(48271, 299, 8);
$iterations = 5000000;

$total = 0;
for ($i = 0; $i < $iterations; $i++) {
    $a->nextValue();
    $b->nextValue();
    if ($a->equals($b)) {
        $total++;
    }
}

print "Real total: $total\n";
