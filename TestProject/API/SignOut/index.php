<?php
require("..\SignOut.php");
new SignOut(file_get_contents('php://input'), $_GET);