<?php
require_once __DIR__ . "/Service.php";
require_once __DIR__ . "/../DAL/SecureRequest.php";


class SignedIn extends Service {
    function Exec($finalObject): void
    {
        // Verify only mandatory arguments
        parent::VerifyEmptyArgs($finalObject->body,  "mail");

        $mail = $finalObject->body->mail;
        echo "SignedIn service is working!\n";
        $credentials = Credentials::GetCredentials("db.json");
        $db = new SecureRequest($credentials);
        $device = exec("hostname");
        $result = $db->CheckConnexionState($mail,$device);
        if ($result == 0)
        {
            echo "$mail is not connected\n";
        }
        else
        {
            echo "$mail is connected on $device\n";
        }
    }

}