<?php

// Copyright 2017 Max Sprauer


class Grid {
    const NORTH = 0;
    const EAST = 1;
    const SOUTH = 2;
    const WEST = 3;
    
    private $curX = 0;
    private $curY = 0;
    private $curDir = self::NORTH;
    private $grid = array();
    
    public $infCount = 0;
    
    public function __construct($filename) 
    {
        $rows = file_get_contents($filename);
        $rows = explode("\n", $rows);
        $width = strlen($rows[0]);
        $height = count($rows);
        
        $startX = 0 - (int)($width / 2); 
        $startY = (int)($height / 2);

        $x = $startX;
        $y = $startY;
        
        foreach ($rows as $row) {            
            for ($i = 0; $i < strlen($row); $i++) {
                $this->grid[$x++][$y] = $row[$i];
            }
            
            $x = $startX;
            $y--;            
        }        
    }
    
    public function dump()
    {        
        // Find bounds that we need to print
        $xs = array_keys($this->grid);
        $minX = $minY = $maxX = $maxY = 0;
        
        foreach ($xs as $x) {
            if ($minX > $x)
                $minX = $x;
                
            if ($maxX < $x)
                $maxX = $x;
                
            $ys = array_keys($this->grid[$x]);
            
            foreach ($ys as $y) {
                if ($minY > $y)
                    $minY = $y;

                if ($maxY < $y)
                    $maxY = $y;                                                
            }                                        
        }
        
        // print "$minX = $minY = $maxX = $maxY\n";
        // print_r($this->grid);
        
        // Print grid
        for ($y = $maxY; $y >= $minY; $y--) {        
            for ($x = $minX; $x <= $maxX; $x++) {
                $block = (isset($this->grid[$x][$y])) ? $this->grid[$x][$y] : '.';

                if (($x == $this->curX) && ($y == $this->curY)) {
                    print "[$block]";                    
                } else {
                    print " $block ";
                }
            }  
            
            print "\n";                      
        }
        
        print "\n";                                    
    }
    
    public function burst()
    {
        // If infected, turn right.  Otherwise, turn left.
        if ($this->grid[$this->curX][$this->curY] == '#') {
            // print "Turn right\n";
            $this->curDir = abs(($this->curDir + 1) % 4);
        } else {
            // print "Turn left\n";
            $this->curDir--;
            if ($this->curDir < 0) {
                $this->curDir = 3;
            }
        }
        
        // If infected, make clean.  Vice versa.
        if ($this->grid[$this->curX][$this->curY] == '#') {
            $this->grid[$this->curX][$this->curY] = '.';                    
        } else {
            $this->grid[$this->curX][$this->curY] = '#';
            $this->infCount++;            
        }
        
        // Move forward
        switch ($this->curDir) {
            case self::NORTH:
                // print "Move north\n";
                $this->curY++;
                break;
                
            case self::EAST:
                // print "Move east\n";
                $this->curX++;
                break;
                
            case self::SOUTH:
                // print "Move south\n";
                $this->curY--;
                break;
                
            case self::WEST:
                // print "Move west\n";
                $this->curX--;
                break;
                                
            default:
                error_log("Bad Direction: {$this->curDir}");
                exit(1);            
        }                
    }

    public function burst2()
    {
        /*
        If it is clean, it turns left.
        If it is weakened, it does not turn, and will continue moving in the same direction.
        If it is infected, it turns right.
        If it is flagged, it reverses direction, and will go back the way it came.
        */

        if (!isset($this->grid[$this->curX][$this->curY]) || ($this->grid[$this->curX][$this->curY] == '.')) {
            $this->curDir--;
            if ($this->curDir < 0) {
                $this->curDir = 3;
            }
        } else if ($this->grid[$this->curX][$this->curY] == '#') {
            $this->curDir = abs(($this->curDir + 1) % 4);
        } else if ($this->grid[$this->curX][$this->curY] == 'F') {
            $this->curDir = abs(($this->curDir + 2) % 4);
        } else if ($this->grid[$this->curX][$this->curY] == 'W') {
            // nop
        } else {
            assert ("Bad state");
            exit(1);
        }

        if (!isset($this->grid[$this->curX][$this->curY]) || ($this->grid[$this->curX][$this->curY] == '.')) {
            // Clean nodes become weakened.
            $this->grid[$this->curX][$this->curY] = 'W';
        } else if ($this->grid[$this->curX][$this->curY] == '#') {
            // Infected nodes become flagged.
            $this->grid[$this->curX][$this->curY] = 'F';
        } else if ($this->grid[$this->curX][$this->curY] == 'F') {
            // Flagged nodes become clean.
            $this->grid[$this->curX][$this->curY] = '.';
        } else if ($this->grid[$this->curX][$this->curY] == 'W') {
            // Weakened nodes become infected.
            $this->grid[$this->curX][$this->curY] = '#';
            $this->infCount++;
        } else {
            assert ("Bad state");
            exit(1);
        }

        // Move forward
        switch ($this->curDir) {
            case self::NORTH:
                // print "Move north\n";
                $this->curY++;
                break;

            case self::EAST:
                // print "Move east\n";
                $this->curX++;
                break;

            case self::SOUTH:
                // print "Move south\n";
                $this->curY--;
                break;

            case self::WEST:
                // print "Move west\n";
                $this->curX--;
                break;

            default:
                error_log("Bad Direction: {$this->curDir}");
                exit(1);            
        }                
    }

}

$sample = new Grid("sample.txt");
$sample->dump();

for ($i = 0; $i < 70; $i++) {
    $sample->burst();
}
$sample->dump();

for ($i = 0; $i < 9930; $i++) {
    $sample->burst();
}

assert($sample->infCount == 5587);

$input = new Grid("input.txt");

for ($i = 0; $i < 10000; $i++) {
    $input->burst();
}
// $input->dump();

print "Part One count: {$input->infCount}\n";

print "Part Two\n";
$part2 = new Grid("sample.txt");
$part2->dump();
for ($i = 0; $i < 100; $i++) {
    $part2->burst2();
}

assert($part2->infCount == 26);

$part2 = new Grid("input.txt");
$part2->dump();

for ($i = 0; $i < 10000000; $i++) {
    $part2->burst2();
    if ($i % 1000 == 0) {
        print "$i: {$part2->infCount}\n";
    }
}
$part2->dump();
print "Part Two count: {$part2->infCount}\n";



