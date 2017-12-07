<?php

// Copyright 2017 Max Sprauer

class Program {
       
    public $name;
    public $weight;
    public $children;
    public $childWeight;
            
    function __construct($name, $weight, $children) {
        $this->name = $name;
        $this->weight = $weight;
        $this->children = $children;
        $this->childWeight = 0;                        
    }        
}

function parseInput($filename) {
    $programs = array();
    $rows = explode("\n", file_get_contents($filename));   
    
    foreach ($rows as $row) {
        $matches = array();
        $children = array();
    
        assert(preg_match('/^(\w+) \((\d+)\)/', $row, $matches) === 1); 
        list(, $name, $weight) = $matches;
        
        if (($pos = strpos($row, '->')) !== FALSE) {
            $children = explode(',', substr($row, $pos + 3));
            $children = array_map('trim', $children);    
        }

        // print "$name $weight " . implode(" ", $children) . "\n";                
        $programs[$name] = new Program($name, $weight, $children);     
    }
    
    return $programs;
}

// Recursively find bottom parent
function findParent($treeNodes, $currentName) {
    
    // Loop thru nodes until we find one with this as a child
    foreach ($treeNodes as $name => $treeNode) {
        if (!empty($treeNode->children) && in_array($currentName, $treeNode->children)) {
            return findParent($treeNodes, $name);            
        }
    }
    
    // If we've iterated the whole array and haven't found a parent, we're done.
    return $currentName;
}

function findBottom($treeNodes) {
    $topNode = null;
    
    // Loop thru nodes until we find one with no children.  This is a top node.  (The tree is bottom-up.)
    foreach ($treeNodes as $name => $treeNode) {
        if (empty($treeNode->children)) {
            $topNode = $name;
            break;
        }        
    }
    
    assert($topNode != null);
    
    // Climb down the tree until we find a node with no parent
    return findParent($treeNodes, $topNode);    
}

// Depth-first search to calculate weights including children
function totalChildrenWeights($treeNodes, $currentName) {
    
    if (!empty($treeNodes[$currentName]->children)) {
        foreach ($treeNodes[$currentName]->children as $childName) {
            $treeNodes[$currentName]->childWeight += totalChildrenWeights($treeNodes, $childName);            
        } 
    }
    
    // print  "$currentName -> " .  ($treeNodes[$currentName]->weight + $treeNodes[$currentName]->childWeight) . "\n";  
    return $treeNodes[$currentName]->weight + $treeNodes[$currentName]->childWeight;
}

function findMismatch($treeNodes, $children) {
    $valueCounts = array();
    
    foreach ($children as $childName) {
        $valueCounts[$treeNodes[$childName]->weight + $treeNodes[$childName]->childWeight]++;
    }
    
    $badValue = 0;
    foreach ($valueCounts as $value => $count) {
        if ($count == 1) {
            $badValue = $value;
            break;
        }        
    }
    
    foreach ($children as $childName) {
        if ($treeNodes[$childName]->weight + $treeNodes[$childName]->childWeight == $badValue) {
            return $childName;
        }        
    }

    return false;
}

// Depth-first search to find mismatch
function findOutlier($treeNodes, $currentName) {
    static $outlier = null;
    
    if (!empty($treeNodes[$currentName]->children)) {
//        $childCount = count($treeNodes[$currentName]->children);
        
        foreach ($treeNodes[$currentName]->children as $childName) {  
            // print "$childName " . ($treeNodes[$childName]->weight + $treeNodes[$childName]->childWeight) . ", $currentName " . $treeNodes[$currentName]->childWeight . "\n";           

/*  
            if ($treeNodes[$childName]->weight + $treeNodes[$childName]->childWeight != $treeNodes[$currentName]->childWeight / $childCount) {
                $outlier = $childName;
                print "Settung outlier to $childName\n";
                return findOutlier($treeNodes, $childName);                    
            }                            
*/

            $mismatch = findMismatch($treeNodes, $treeNodes[$childName]->children);
            if ($mismatch !== false) {
                $outlier = $mismatch;
                print "Settung outlier to $outlier\n";
                return findOutlier($treeNodes, $outlier);                                            
            }
            
        }
    }        
    
    return $outlier;
}

function printChildrenAndWeights($treeNodes, $currentName) {
    printf("%s: %d + %d = %d\n", $currentName, $treeNodes[$currentName]->weight, $treeNodes[$currentName]->childWeight, $treeNodes[$currentName]->weight + $treeNodes[$currentName]->childWeight);
    foreach ($treeNodes[$currentName]->children as $childName) {
        printf("\t%s: %d + %d = %d\n", $childName, $treeNodes[$childName]->weight, $treeNodes[$childName]->childWeight, $treeNodes[$childName]->weight + $treeNodes[$childName]->childWeight);        
    }    
}

function run($input) {
    $treeData = parseInput($input);
    $bottomNode = findBottom($treeData);
    print "Bottom Program: $bottomNode\n";
    $total = totalChildrenWeights($treeData, $bottomNode);
    print "Total Weight: $total\n";
    $outlier = findOutlier($treeData, $bottomNode);
    if ($outlier == null) {
        $outlier = $bottomNode;
    }
    print "Outlier: $outlier\n";
    printChildrenAndWeights($treeData, $outlier);
    print "\n\n";
    
    return $treeData;        
}

run('sample_input.txt');
$treeData = run('circus_input.txt');

printChildrenAndWeights($treeData, 'tlskukk');        

/*
Bottom Program: aapssr
Total Weight: 308568
Settung outlier to pkowhq
Outlier: pkowhq

pkowhq: 1187 + 5950 = 7137
    zfrsmm: 763 + 723 = 1486
    tlskukk: 1464 + 28 = 1492
    fqkbscn: 418 + 1068 = 1486
    mlafk: 38 + 1448 = 1486
tlskukk: 1464 + 28 = 1492
    ixoiuh: 14 + 0 = 14
    jdxth: 14 + 0 = 14

tlskukk weighs 6 more than its siblings, so the answer is 1464 - 6 = 1458.

*/  
    


