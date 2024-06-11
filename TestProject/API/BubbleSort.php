<?php
require_once __DIR__ . "/Service.php";
require_once __DIR__ . "/ArrayLib.php";

class BubbleSort extends Service {
    function Exec($finalObject): void {
        //verifying the arguments
        parent::VerifyEmptyArgs($finalObject->body, "array");
        //initializing the array
        $array = $finalObject->body->array;
        //sorting the array
        $array = ArrayLib::BubbleSort($array);
        //returning the array to the user in json format
        echo json_encode(["array" => $array]);
    }
}