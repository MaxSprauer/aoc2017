//
//  main.c
//  Stress_Test
//
//  Created by Max Sprauer on 12/5/17.
//  Copyright © 2017 Max Sprauer. All rights reserved.
//

#include <stdio.h>
#include <stdlib.h>
#include <assert.h>

typedef unsigned int uint32;
typedef int int32;

#define ARRAY_WIDTH 10000
#define START_OFFSET_X (ARRAY_WIDTH/2)
#define START_OFFSET_Y (ARRAY_WIDTH/2)

typedef enum move {
    North,
    West,
    South,
    East,
} move;


uint32 arrayOffset(int32 x, int32 y) {
    uint32 offset = (START_OFFSET_X + x) * ARRAY_WIDTH + START_OFFSET_Y + y;
    assert(offset < ARRAY_WIDTH * ARRAY_WIDTH);
    return offset;
}

uint32 buildSpiral(uint32 target) {
    uint32 *spiral = calloc(ARRAY_WIDTH * ARRAY_WIDTH, sizeof(uint32));
    assert(spiral);
    
    // Initial state
    move dir = East;
    uint32 ring = 0;
    uint32 toGo = 1;
    uint32 cellValue = 1;
    int32 x = 0;
    int32 y = 0;
    
    spiral[arrayOffset(x, y)] = 1;

    do {
        // Move to next cell
        switch (dir) {
            case North:
                y += 1;
                break;
                
            case West:
                x -= 1;
                break;
                
            case South:
                y -= 1;
                break;
                
                
            case East:
                x += 1;
                break;
        }

        // Sum of each neighbor
        cellValue = 0;
        cellValue += spiral[arrayOffset(x    , y + 1)];
        cellValue += spiral[arrayOffset(x + 1, y + 1)];
        cellValue += spiral[arrayOffset(x + 1, y    )];
        cellValue += spiral[arrayOffset(x + 1, y - 1)];
        cellValue += spiral[arrayOffset(x    , y - 1)];
        cellValue += spiral[arrayOffset(x - 1, y - 1)];
        cellValue += spiral[arrayOffset(x - 1, y    )];
        cellValue += spiral[arrayOffset(x - 1, y + 1)];
        
        spiral[arrayOffset(x, y)] = cellValue;

        toGo--;
        
        if (toGo == 0) {
            // Find next direction and distance
            if (dir == East) {
                dir = North;
                ring++;
                toGo = (ring * 2) - 1;
            } else {
                dir++;
                toGo = ring * 2;
                if (dir == East) {
                    toGo++;     // Go one extra to start the next ring
                }
            }
        }
        
    } while (cellValue < target);
    
    free(spiral);
    return cellValue;
}




int main(int argc, const char * argv[]) {
    assert(buildSpiral(44) == 54);
    assert(buildSpiral(370) == 747);
    
    uint32 cv = buildSpiral(277678);
    printf("Cell Value %u\n\n", cv);
    return 0;
}
