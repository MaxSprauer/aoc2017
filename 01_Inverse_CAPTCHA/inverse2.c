/* Copyright 2017 Max Sprauer */

#include <stdlib.h>
#include <stdio.h>
#include <string.h>

/*
Now, instead of considering the next digit, it wants you to consider the digit halfway around the circular list. That is, 
if your list contains 10 items, only include a digit in your sum if the digit 10/2 = 5 steps forward matches it. Fortunately, 
your list has an even number of elements.
*/

unsigned int sum(const char *inputStr)
{
    unsigned int inputLen = strlen(inputStr);
    unsigned int i;
    unsigned int sum = 0;
    
    for (i = 0; i < inputLen; i++) {        
        if (inputStr[i] == inputStr[(i + inputLen / 2) % inputLen]) {
            sum += inputStr[i] - '0';
        }                
    }
    
    return sum;    
}

int main (int argc, char const *argv[])
{
    
    if (argc < 2) {
        printf("Usage: %s <input>\n", argv[0]);
        return 1;
    }

    printf("%s\nSum: %u\n\n", argv[1], sum(argv[1]));
    
    /* code */
    return 0;
}