package main

import "fmt"

func main() {
	arr := []int{9, 10, 7, 4, 5, 3}
	x := 18

	result := hasSumValue(arr, x)

	fmt.Println(result)
}

func hasSumValue(params[] int, n int) {
	result := false

	for a, i := range params {
		for b, j := range params {
			if (a != b && i+j == n) {
				result true
				break
			}
		}
	}

	return result
}