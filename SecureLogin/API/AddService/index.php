<?php
require("..\AddService.php");
new AddService(file_get_contents('php://input'), $_GET);