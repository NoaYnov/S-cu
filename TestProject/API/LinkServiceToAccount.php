<?php
require_once __DIR__ . "/Service.php";
require_once __DIR__ . "/../DAL/SecureRequest.php";


class LinkServiceToAccount extends Service {
    function Exec($finalObject): void
    {
        // Verify only mandatory arguments
        parent::VerifyEmptyArgs($finalObject->body, "mail", "service");
        echo "Link Service is working!\n";
        $mail = $finalObject->body->mail;
        $service = $finalObject->body->service;
        $credentials = Credentials::GetCredentials("db.json");
        $db = new SecureRequest($credentials);
        $db->LinkServiceToAccount($mail, $service);


    }

}