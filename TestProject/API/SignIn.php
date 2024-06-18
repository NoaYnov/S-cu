<?php
require_once __DIR__ . "/Service.php";
require_once __DIR__ . "/../DAL/SecureRequest.php";


class SignIn extends Service {
    function Exec($finalObject):void
    {
        parent::VerifyEmptyArgs($finalObject->body,  "password","mail","memorized");
        $password = $finalObject->body->password;
        $mail = $finalObject->body->mail;
        $memorized = $finalObject->body->memorized;
        echo "SignIn service is working!\n";
        $credentials = Credentials::GetCredentials("db.json");
        $db = new SecureRequest($credentials);
        // device = getmac+hostname
        $device = exec('getmac').gethostname();
        $db->Connection($password, $mail, $memorized,$device);


    }
}