<?php

// Copyright 2017 Max Sprauer


/**
 * @param string $filename
 * @param array $dancers
 * @param int $iterations
 * @return string
 */
function promenade($filename, $dancers, $iterations = 1) {

    $moves = file_get_contents($filename);
    $moves = explode(',', $moves);

    $states = array();
    
    for (; $iterations > 0; $iterations--) {
        foreach ($moves as $move) {
            switch ($move[0]) {
                case 's':
                    // Spin, written sX, makes X programs move from the end to the front, but maintain their order otherwise.
                    sscanf($move, "s%d", $x);
                    for ($i = 0; $i < $x; $i++) {
                        $program = array_pop($dancers);
                        array_unshift($dancers, $program);
                    }
                    break;

                case 'x':
                    // Exchange, written xA/B, makes the programs at positions A and B swap places.
                    sscanf($move, "x%d/%d", $a, $b);
                    $program = $dancers[$a];
                    $dancers[$a] = $dancers[$b];
                    $dancers[$b] = $program;
                    break;

                case 'p':
                    // Partner, written pA/B, makes the programs named A and B swap places.
                    sscanf($move, "p%c/%c", $a, $b);

                    $keyA = array_search($a, $dancers);
                    $keyB = array_search($b, $dancers);

                    $program = $dancers[$keyA];
                    $dancers[$keyA] = $dancers[$keyB];
                    $dancers[$keyB] = $program;
                    break;
            }
        }

        $str = implode('', $dancers);
        $states[$iterations] = $str;

        $key = array_search($str, $dancers);
        if (FALSE !== $key) {
            printf("%s: %d -> %d\n", $iterations, $key);
        }
    }

    return implode('', $dancers);
}

$start = time();
assert('baedc' == promenade('sample.txt', range('a', 'e')));
print "One dance: " . promenade('input.txt', range('a', 'p')) . "\n";
$states = array();
print "100 thousand dances: " . promenade('input.txt', range('a', 'p'), 100000, $states) . "\n";
$duration = time() - $start;
print "$duration seconds\n";








