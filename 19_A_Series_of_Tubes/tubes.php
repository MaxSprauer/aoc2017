<?php

// Copyright 2017 Max Sprauer

class Maze {
    const NORTH = 0;
    const EAST = 1;
    const SOUTH = 2;
    const WEST = 3;
    
    private $curX = 0;
    private $curY = 0;
    private $curDir = self::SOUTH;
    private $maze = array();
    public $path = "";
    public $steps = 0;
    
    
    public function __construct($filename) 
    {
        $rows = file_get_contents($filename);
        $rows = explode("\n", $rows);
        
        $x = 0;
        $y = count($rows) - 1;
        
        foreach ($rows as $row) {            
            for ($i = 0; $i < strlen($row); $i++) {
                if ($y == count($rows) - 1 && $row[$i] == '|') {
                    $this->curX = $x;
                    $this->curY = $y;                    
                }                                

                $this->maze[$x++][$y] = $row[$i];                
            }
            
            $x = 0;
            $y--;            
        }                
    }
  
    public function turn()
    {
        $done = true;
        $origDir = $this->curDir;
        
        switch ($this->curDir) {
            case self::NORTH:
            case self::SOUTH:
                // Try W and E
                $this->curDir = self::WEST;
                if (!$this->forward()) {
                    return false;
                }                                
                
                $this->curDir = self::EAST;
                if (!$this->forward() && !$this->forward()) {
                    return false;
                }                                
                
                $this->curDir = $origDir;
                break;
            
            case self::EAST:
            case self::WEST:
                // Try N and S
                $this->curDir = self::NORTH;
                if (!$this->forward()) {
                    return false;
                }
                
                $this->curDir = self::SOUTH;
                if (!$this->forward() && !$this->forward()) {
                    return false;
                }                                
                
                $this->curDir = $origDir;                
                break;

            default:
                abort("Unknown direction");            
        }
                
        return $done;        
    }
  
    public function forward()
    {
        $done = false;
        
        switch ($this->curDir) {
            case self::NORTH:
                $this->curY++;
                break;
            
            case self::EAST:
                $this->curX++;
                break;

            case self::SOUTH:
                $this->curY--;
                break;

            case self::WEST:
                $this->curX--;
                break;
            
            default:
                abort("Unknown direction");            
        }
        
        if ($this->curX < 0 || $this->curX >= count($this->maze)) {
            return true;
        }
        
        if ($this->curY < 0 || $this->curY >= count($this->maze[0])) {
            return true;
        }
        
        if ($this->maze[$this->curX][$this->curY] == ' ') {
            $done = true;
        }        
        
        return $done;
    }
  
    public function cycle()
    {
        $current = $this->maze[$this->curX][$this->curY];
        $done = false;
        print $current;
        
        if (preg_match("/[A-Z]/", $current)) {
            $this->path .= $current;
            // Keep going unless done
            $done = $this->forward();
            $this->steps++;                        
        } else if ($current == '|' || $current == '-') {
            // Keep going unless done
            $done = $this->forward();
            $this->steps++;            
        } else if ($current == '+') {
            // Must turn unless done
            $done = $this->turn();
            $this->steps++;                        
        } else {
            abort("Illegal character: " . $current);
        }
        
        return $done;
    }
    
}


$sample = new Maze("sample.txt");
while (!$sample->cycle());
print("\n{$sample->path}\n\n");
print("Steps: {$sample->steps}\n");
assert($sample->path == "ABCDEF");
assert($sample->steps == 38);

$input = new Maze("input.txt");
while (!$input->cycle());
print("\n" . $input->path . "\n\n");
print("Steps: {$input->steps}\n");




