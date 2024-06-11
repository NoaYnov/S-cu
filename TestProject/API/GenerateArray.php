<?php
require_once __DIR__ . "/Service.php";
require_once __DIR__ . "/ArrayLib.php";

class GenerateArray extends Service {
    function Exec($finalObject): void {
        //verifying the arguments
        parent::VerifyEmptyArgs($finalObject->body, "size");
        //initializing the array
        $size = $finalObject->body->size;
        //generating the array
        $array = ArrayLib::GenerateArray($size);
        //returning the array to the user in json format
        echo json_encode(["array" => $array]);
    }
}