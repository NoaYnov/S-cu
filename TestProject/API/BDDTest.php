<?php
require_once __DIR__ . "/Service.php";
require_once __DIR__ . "/../DAL/Database.php";

class BDDTest extends Service{
    function Exec($finalObject):void
    {
        try {
            $credentials = Credentials::GetCredentials("root.json");
            $db = New Database($credentials);





        }
        catch (Exception $e) {
            echo "Error: " . $e->getMessage();

        }
    }

}