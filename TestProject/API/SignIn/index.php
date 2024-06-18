<?php
require("..\SignIn.php");
new SignIn(file_get_contents('php://input'), $_GET);