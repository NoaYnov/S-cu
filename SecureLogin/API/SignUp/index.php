<?php
require("..\SignUp.php");
new SignUp(file_get_contents('php://input'), $_GET);