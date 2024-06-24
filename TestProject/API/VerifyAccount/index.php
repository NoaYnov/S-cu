<?php
require("..\VerifyAccount.php");
new VerifyAccount(file_get_contents('php://input'), $_GET);