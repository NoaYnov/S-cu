<?php
require("..\ChangePassword.php");
new ChangePassword(file_get_contents('php://input'), $_GET);