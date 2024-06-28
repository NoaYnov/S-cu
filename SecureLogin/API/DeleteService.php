<?php
require_once __DIR__ . "/Service.php";
require_once __DIR__ . "/../DAL/SecureRequest.php";


class DeleteService extends Service {
    function Exec($finalObject): void
    {
        parent::VerifyEmptyArgs($finalObject->body, "name");
        $name = $finalObject->body->name;
        echo "Delete Service is working!\n";
        $credentials = Credentials::GetCredentials("db.json");
        $db = new SecureRequest($credentials);
        $db->DeleteService($name);
    }

}