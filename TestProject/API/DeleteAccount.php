<?php
require_once __DIR__ . "/Service.php";
require_once __DIR__ . "/../DAL/SecureRequest.php";


class DeleteAccount extends Service {
    function Exec($finalObject): void
    {
        // Verify only mandatory arguments
        parent::VerifyEmptyArgs($finalObject->body, "password", "mail");

        $password = $finalObject->body->password;
        $mail = $finalObject->body->mail;
        echo "Delete service is working!\n";
        $credentials = Credentials::GetCredentials("db.json");
        $db = new SecureRequest($credentials);
        $db->StarDestroyer($mail, $password);

    }

}