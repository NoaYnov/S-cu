<?php
require("..\TestDal.php");
new TestDal(file_get_contents('php://input'), $_GET);