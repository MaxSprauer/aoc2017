//
//  main.c
//  Spiral_Memory
//
//  Created by Max Sprauer on 12/4/17.
//  Copyright © 2017 Max Sprauer. All rights reserved.
//

#include <stdio.h>
#include <assert.h>
#include <sys/param.h>

typedef unsigned int uint;

uint sumOfSeries(int n) {
    uint total = 0;
    for (int i = n; i > 0; i--) {
        total += i;
    }
    return total;
}

uint north(uint ring) {
    return 4 + (11 * (ring - 1)) + (8 * sumOfSeries(ring - 2));
}

uint south(uint ring) {
    return 1 + (7 * ring) + (8 * sumOfSeries(ring - 1));
}

uint east(uint ring) {
    return 2 + (9 * (ring - 1)) + (8 * sumOfSeries(ring - 2));
}

uint west(uint ring) {
    return 1 + (5 * sumOfSeries(ring)) + (3 * sumOfSeries(ring - 1));
    //return (7 * (ring - 1)) + (8 * sumOfSeries(ring - 1));
}

uint ringStart(uint ring) {
    // return east(ring) - ring + 1;
    return 2 + (8 * sumOfSeries(ring - 1));
}

// Each "ring" of the spiral is defined as starting straight east of the center (1)
// This only really is meaningful for the numbers at the four compass points.
uint xringForCell(const uint cell) {
    uint ring = 1;
    uint currentEast = east(1);
    uint nextEast = east(2);
    
    // The real start of the ring is the southeast corner of each spiral, which
    // adds one to the distance from center.
    uint ringStart = currentEast - ring + 1;
    uint nextRingStart = nextEast - (ring + 1) + 1;
    
    
    // Iterate eastward thru cell numbers until we find two that are around our target cell
    while (!((currentEast <= cell) && (cell < nextEast))) {
        ring++;
        currentEast = nextEast;
        nextEast = east(ring + 1);
        ringStart = currentEast - ring + 1;
        nextRingStart = nextEast - (ring + 1) + 1;
    }
    
    return ring;
}

uint ringForCell(const uint cell) {
    uint ring = 0;
    uint currentStart;
    uint nextStart;
    
    // Iterate thru starting cells until we find two that are around our target cell
    do {
        ring++;
        currentStart = ringStart(ring);
        nextStart = ringStart(ring + 1);
    } while (!((currentStart <= cell) && (cell < nextStart)));
    
    return ring;
}

uint distanceForCell(const uint cell) {
    uint ring = ringForCell(cell);
    int n, s, e, w;
    int nDiff, sDiff, eDiff, wDiff;
    int distToCP;
    
    // Distance is the smallest of the distance to the nearest compass point
    // plus that compass point's ring number.
    
    // We can also figure out compass point distances by dividing the distance between two rings.
    n = north(ring);
    s = south(ring);
    e = east(ring);     // Since we start the ring on the east compass point
    w = west(ring);
    
    nDiff = abs(n - (int) cell);
    sDiff = abs(s - (int) cell);
    wDiff = abs(w - (int) cell);
    eDiff = abs(e - (int) cell);
    
    distToCP = MIN(nDiff, MIN(sDiff, MIN(wDiff, eDiff)));
    
    return distToCP + ring;
}

int main(int argc, const char * argv[]) {
    assert(east(4) == 53);
    assert(north(4) == 61);
    assert(west(4) == 69);
    assert(south(4) == 77);
    assert(west(5) == 106);
    assert(west(2) == 19);
    
    
    assert(ringForCell(21) == 2);
    assert(ringForCell(65) == 4);
    assert(ringForCell(116) == 5);
    assert(ringForCell(53) == 4);
    assert(ringForCell(86) == 5);
    assert(ringForCell(52) == 4);
    assert(ringForCell(25) == 2);
    assert(ringForCell(26) == 3);
    assert(ringForCell(27) == 3);

    
    assert(distanceForCell(44) == 5);
    assert(distanceForCell(48) == 5);
    assert(distanceForCell(49) == 6);
    assert(distanceForCell(21) == 4);
    assert(distanceForCell(5) == 2);
    assert(distanceForCell(101) == 10);
    assert(distanceForCell(28) == 3);
    assert(distanceForCell(54) == 5);
    assert(distanceForCell(52) == 5);
    assert(distanceForCell(1024) == 31);


    printf("277678 => %u\n", distanceForCell(277678));
    
    return 0;
}
