<?php
require_once __DIR__ . "/Service.php";
require_once __DIR__ . "/../DAL/SecureRequest.php";


class SignUp extends Service {
    function Exec($finalObject):void
    {
        parent::VerifyEmptyArgs($finalObject->body,  "password","mail");
        $password = $finalObject->body->password;
        $mail = $finalObject->body->mail;
        echo "Signup tmp service is working!\n";
        $credentials = Credentials::GetCredentials("db.json");
        $db = new SecureRequest($credentials);
        $db->AddAccount($password,$mail);
    }
}