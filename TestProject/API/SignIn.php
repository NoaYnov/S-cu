<?php
require_once __DIR__ . "/Service.php";
require_once __DIR__ . "/../DAL/SecureRequest.php";


class SignIn extends Service {
    function Exec($finalObject): void
    {
        // Verify only mandatory arguments
        parent::VerifyEmptyArgs($finalObject->body, "password", "mail");

        $password = $finalObject->body->password;
        $mail = $finalObject->body->mail;
        $memorized = isset($finalObject->body->memorized) && !empty($finalObject->body->memorized) ? $finalObject->body->memorized : false;

        echo "SignIn service is working!\n";
        $credentials = Credentials::GetCredentials("db.json");
        $db = new SecureRequest($credentials);
        $device = exec("hostname");
        $db->Connection($password, $mail, $memorized, $device);
    }

}