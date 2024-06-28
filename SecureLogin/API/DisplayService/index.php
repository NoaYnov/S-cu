<?php
require("..\DisplayService.php");
new DisplayService(file_get_contents('php://input'), $_GET);