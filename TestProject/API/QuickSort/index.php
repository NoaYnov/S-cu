<?php
require("..\QuickSort.php");
new QuickSort(file_get_contents('php://input'), $_GET);