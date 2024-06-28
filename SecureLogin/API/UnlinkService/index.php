<?php
require("..\UnlinkService.php");
new UnlinkService(file_get_contents('php://input'), $_GET);