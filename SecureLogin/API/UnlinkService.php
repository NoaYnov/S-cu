<?php
require_once __DIR__ . "/Service.php";
require_once __DIR__ . "/../DAL/SecureRequest.php";


class UnlinkService extends Service {
    function Exec($finalObject): void
    {
        parent::VerifyEmptyArgs($finalObject->body, "mail","name");
        $mail = $finalObject->body->mail;
        $name = $finalObject->body->name;
        echo "Unlink Service is working!\n";
        $credentials = Credentials::GetCredentials("db.json");
        $db = new SecureRequest($credentials);
        $db->DeleteServiceLink($mail, $name);
    }

}