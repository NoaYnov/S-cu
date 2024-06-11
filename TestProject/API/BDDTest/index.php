<?php
require("..\BDDTest.php");
new BDDTest(file_get_contents('php://input'), $_GET);