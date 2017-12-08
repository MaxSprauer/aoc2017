<?php

// Copyright 2017 Max Sprauer

function process($filename) {
    $regs = array();
    $highest = 0;
    
    $rows = file_get_contents($filename);
    $rows = explode("\n", $rows);
    foreach ($rows as $row) {
        list($register, $op, $amount,, $left, $comparator, $right) = explode(' ', $row);
        $eval = sprintf("return (%d %s %d);", isset($regs[$left]) ? $regs[$left] : 0, $comparator, $right);
        // print("$eval\n");
        $pass = eval($eval);                        
        if ($pass) {
            if (!isset($regs[$register])) {
                $regs[$register] = 0;
            }
            
            switch ($op) {
                case 'inc':
                    $regs[$register] += $amount;
                    break;
                case 'dec':
                    $regs[$register] -= $amount;                
                    break;
            }
            
            if ($regs[$register] > $highest) {
                $highest = $regs[$register];
            }            
        }
    }
    
    return array($highest, $regs);
}


list($highest, $regs) = process('testInput.txt');
assert($highest == 10);
print_r($regs);

list($highest, $regs) = process('input.txt');
print_r($regs);

$answer = array_reduce($regs, function($carry, $item) {
    return ($carry > $item) ? $carry : $item;    
});

print("$answer, $highest\n");