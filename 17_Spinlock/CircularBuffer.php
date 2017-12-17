<?php

// Copyright 2017 Max Sprauer


class CircularBuffer {

    // This would probably be better as a linked list
    private $buffer = array(0);
    private $curIndex = 0;
    private $STEPS;
    
    public function __construct($steps) {        
        $this->STEPS = $steps;
    }

    public function insert($x) {
        $len = count($this->buffer);
        $steps = $this->STEPS % $len;
        $this->curIndex = ($this->curIndex + $steps) % $len;
        array_splice($this->buffer, $this->curIndex + 1, 0, array($x));
        $this->curIndex++;
    }
    
    public function go($iterations) {
        for ($i = 1; $i <= $iterations; $i++) {
            $this->insert($i);
        }
    }

    public function printit() {
        $output = '';
        $len = count($this->buffer);

        for ($i = 0; $i <= $len; $i++) {
            if ($i == $this->curIndex) {
                $output .= "({$this->buffer[$i]}) ";
            } else {
                $output .= "{$this->buffer[$i]} ";
            }                        
        }
        
        $output .= "\n\n";
        return $output;
    }

}


$sample = new CircularBuffer(3);
$sample->go(9);
$output = $sample->printit();
print $output;
assert(trim($output) == '0 (9) 5 7 2 4 3 8 6 1');

$real = new CircularBuffer(348);
$real->go(2017);
$output = $real->printit();
print $output;

/*
$real = new CircularBuffer(348);
$real->go(50000000);
$output = $real->printit();
print $output;
*/


