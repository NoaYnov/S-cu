<?php
require_once __DIR__ . "/Service.php";
require_once __DIR__ . "/../DAL/SecureRequest.php";


class VerifyAccount extends Service {
    function Exec($finalObject): void
    {
        // Verify only mandatory arguments
        parent::VerifyEmptyArgs($finalObject->body, "mail","password");
        echo "Add Verify Account is working!\n";
        $mail = $finalObject->body->mail;
        $password = $finalObject->body->password;
        $credentials = Credentials::GetCredentials("db.json");
        $description = isset($finalObject->body->description) && !empty($finalObject->body->description) ? $finalObject->body->description : "nothing";
        $db = new SecureRequest($credentials);
        $db->VerifyAccount($password, $mail);


    }

}