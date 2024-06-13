<?php
require_once __DIR__ . "/Service.php";
require_once __DIR__ . "/../DAL/SecureRequest.php";


class SignUp extends Service {
    function Exec($finalObject):void
    {
        parent::VerifyEmptyArgs($finalObject->body, "uuid", "stretch", "password","mail");
        $uuid = $finalObject->body->uuid;
        $stretch = $finalObject->body->stretch;
        $password = $finalObject->body->password;
        $mail = $finalObject->body->mail;
        echo "Signup service is working!";
        $credentials = Credentials::GetCredentials("root.json");
        $db = new SecureRequest($credentials);
        //$db->AddAccount($uuid,$stretch,$password,$mail);
        //$db->CreateToken($uuid);
        //$db->TestConnectionpwd($uuid,$password);

    }
}