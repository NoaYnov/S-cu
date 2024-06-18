<?php
require("..\SignedIn.php");
new SignedIn(file_get_contents('php://input'), $_GET);