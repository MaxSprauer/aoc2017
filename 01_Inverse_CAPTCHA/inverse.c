/* Copyright 2017 Max Sprauer */

#include <stdlib.h>
#include <stdio.h>
#include <string.h>

/*
The captcha requires you to review a sequence of digits (your puzzle input) and find the sum of all digits that match the 
next digit in the list. The list is circular, so the digit after the last digit is the first digit in the list.
*/

unsigned int sum(const char *inputStr)
{
    unsigned int inputLen = strlen(inputStr);
    unsigned int i;
    unsigned int sum = 0;
    
    for (i = 0; i < inputLen; i++) {        
        if (inputStr[i] == inputStr[(i + 1) % inputLen]) {
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