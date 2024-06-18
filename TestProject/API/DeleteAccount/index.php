<?php
require("..\DeleteAccount.php");
new DeleteAccount(file_get_contents('php://input'), $_GET);