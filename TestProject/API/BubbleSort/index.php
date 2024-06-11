<?php
require("..\BubbleSort.php");
new BubbleSort(file_get_contents('php://input'), $_GET);