<?php
/**
 * Created by IntelliJ IDEA.
 * User: max
 * Date: 12/17/17
 * Time: 12:49 PM
 * Copyright 2017 Max Sprauer
 */


class CircularBuffer {

    // This would probably be better as a linked list
    // private $buffer = array(0);
    private $bufferLen = 1;
    private $curIndex = 0;
    private $STEPS;

    public function __construct($steps) {
        $this->STEPS = $steps;
    }

    public function insert($x) {
        $steps = $this->STEPS % $this->bufferLen;
        $this->curIndex = ($this->curIndex + $steps) % $this->bufferLen;
        $this->curIndex++;
        $this->bufferLen++;

        if ($this->curIndex == 1) {
            print "$x\n";
        }
    }

    public function go($iterations) {
        for ($i = $this->bufferLen; $i <= $iterations; $i++) {
            $this->insert($i);
        }
    }

    public function printit($stop = 0) {
        $output = '';
        $len = ($stop > 0) ? $stop : count($this->buffer);

        for ($i = 0; $i <= $len; $i++) {
            if ($i == $this->curIndex) {
                $output .= "({$this->buffer[$i]}) ";
            } else {
                $output .= "{$this->buffer[$i]} ";
            }
        }

        $output .= "\n";
        return $output;
    }

}

$real = new CircularBuffer(348);
$real->go(50000000);

/*
for ($x = 1; $x < 200000; $x++) {
    $real->go($x);
   // $output = $real->printit(1);
   // print "$x: $output";
}
*/


