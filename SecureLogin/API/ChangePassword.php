<?php
require_once __DIR__ . "/Service.php";
require_once __DIR__ . "/../DAL/SecureRequest.php";


class ChangePassword extends Service {
    function Exec($finalObject): void
    {
        // Verify only mandatory arguments
        parent::VerifyEmptyArgs($finalObject->body, "password", "mail", "newPassword");

        $password = $finalObject->body->password;
        $mail = $finalObject->body->mail;
        $newPassword = $finalObject->body->newPassword;
        echo "ChangePassword service is working!\n";
        $credentials = Credentials::GetCredentials("db.json");
        $db = new SecureRequest($credentials);
        $db->ChangePassword($mail, $password,$newPassword);

    }

}