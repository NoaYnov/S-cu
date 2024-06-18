<?php
require_once __DIR__ . "/Service.php";
require_once __DIR__ . "/../DAL/SecureRequest.php";


class SignOut extends Service {
    function Exec($finalObject): void
    {
        // Verify only mandatory arguments
        parent::VerifyEmptyArgs($finalObject->body,  "mail");

        $mail = $finalObject->body->mail;
        echo "SignOut service is working!\n";
        $credentials = Credentials::GetCredentials("db.json");
        $db = new SecureRequest($credentials);
        $device = exec("hostname");
        $db->Disconnect($mail, $device);
    }

}