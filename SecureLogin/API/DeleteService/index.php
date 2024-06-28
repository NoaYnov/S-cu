<?php
require("..\DeleteService.php");
new DeleteService(file_get_contents('php://input'), $_GET);