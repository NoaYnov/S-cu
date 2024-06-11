<?php
require("..\GenerateArray.php");
new GenerateArray(file_get_contents('php://input'), $_GET);