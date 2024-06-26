<?php
require("..\LinkServiceToAccount.php");
new LinkServiceToAccount(file_get_contents('php://input'), $_GET);