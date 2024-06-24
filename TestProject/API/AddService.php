<?php
require_once __DIR__ . "/Service.php";
require_once __DIR__ . "/../DAL/SecureRequest.php";


class AddService extends Service {
    function Exec($finalObject): void
    {
        // Verify only mandatory arguments
        parent::VerifyEmptyArgs($finalObject->body, "name");
        echo "Add Service is working!\n";
        $name = $finalObject->body->name;
        $credentials = Credentials::GetCredentials("db.json");
        $link = isset($finalObject->body->link) && !empty($finalObject->body->link) ? $finalObject->body->link : "nothing";
        $description = isset($finalObject->body->description) && !empty($finalObject->body->description) ? $finalObject->body->description : "nothing";
        $db = new SecureRequest($credentials);
        $db->AddService($name, $link,$description);


    }

}