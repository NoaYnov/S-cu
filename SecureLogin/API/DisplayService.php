<?php
require_once __DIR__ . "/Service.php";
require_once __DIR__ . "/../DAL/SecureRequest.php";


class DisplayService extends Service {
    function Exec($finalObject): void
    {
        // Verify only mandatory arguments
        parent::VerifyEmptyArgs($finalObject->body, "mail");
        echo "Display Service is working!\n";
        $mail = $finalObject->body->mail;
        $credentials = Credentials::GetCredentials("db.json");
        $db = new SecureRequest($credentials);
        $db->DisplayServiceMail($mail);


    }

}