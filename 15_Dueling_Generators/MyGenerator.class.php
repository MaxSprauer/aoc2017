<?php

// Copyright 2018 Max Sprauer

class MyGenerator {
    public $factor;
    public $number;
    public $divisor;

    function __construct($factor, $start, $divisor = 1) {
        $this->factor = $factor;
        $this->number = $start;
        $this->divisor = $divisor;
    }

    function equals(MyGenerator $g) {
        return ($this->number & 0xFFFF) == ($g->number & 0xFFFF);
    }

    function nextValue() {
        do {
            $this->number = ($this->number * $this->factor) % 2147483647;
        } while ($this->number % $this->divisor != 0);
    }
}
