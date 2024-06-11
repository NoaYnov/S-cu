<?php

class ArrayLib {
    static function QuickSort($tab) { // This sort is recursive.
        $size = count($tab);
        if ($size <= 1) { // Recursion base case
            return $tab;
        }

        $tmp = $tab[0]; // Store the value of the first element in a temporary variable (tmp).
        $left = array(); // Create an empty array (left).
        $right = array(); // Create an empty array (right).
        for ($i = 1; $i < $size; $i++) { // For i ranging from 1 to the size of the array - 1 (the last element).
            if ($tab[$i] < $tmp) { // If the value of array[i] is less than the value of tmp
                $left[] = $tab[$i]; // Add the value of array[i] to the end of the left array.
            } else { // Otherwise
                $right[] = $tab[$i]; // Add the value of array[i] to the end of the right array.
            }
        }
        // This results in a left array with all values less than the value of tmp and a right array with all values greater than the value of tmp.
        return array_merge(self::QuickSort($left), array($tmp), self::QuickSort($right)); // Return the sorted left array, the value of tmp, and the sorted right array.
    }

    static function GenerateArray($size): array { // This function generates an array of size $size with random values.
        $tab = [];
        for ($i = 0; $i < $size; $i++) {
            $tab[$i] = rand(0, 100);
        }
        return $tab;
    }

    static function BubbleSort($tab) { // This sort is iterative.
        $size = count($tab); // Get the size of the array.
        for ($i = 0; $i < $size; $i++) { // For i ranging from 0 to the size of the array - 1 (the last element).
            for ($j = 0; $j < $size - 1; $j++) { // For j ranging from 0 to the size of the array - 2 (the second to last element).
                if ($tab[$j] > $tab[$j + 1]) { // If the value of array[j] is greater than the value of array[j+1]
                    $tmp = $tab[$j];
                    $tab[$j] = $tab[$j + 1]; // Swap the values of array[j] and array[j+1].
                    $tab[$j + 1] = $tmp;
                }
            }
        }
        return $tab;
    }
}
