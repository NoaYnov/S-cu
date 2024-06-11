<?php

class Credentials {
    public static function GetCredentials($fileName) {

        $path = $_SERVER['DOCUMENT_ROOT']."../../credentials/";
        
        return json_decode(file_get_contents($path . $fileName));
    }
}