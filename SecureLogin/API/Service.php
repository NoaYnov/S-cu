<?php
abstract class Service {
    function __construct($body, $args) {
        // method verification
        $this->VerifyMethod();
        // all user data sent is organized and stored in this object
        $finalObject = (object) [
            "body" => json_decode($body),
            "args" => $args,
            "method" => $_SERVER["REQUEST_METHOD"],
            "url" => $_SERVER["REQUEST_URI"],
            "header" => getallheaders()
        ];
        // service execution
        $this->Exec($finalObject);
    }

    abstract function Exec($finalObject): void;

    private function VerifyMethod(): void {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") { // if the request method is not the one expected
            http_response_code(405);     // 405: Method Not Allowed
            die();                                    // stop the script
        }
    }

    static function VerifyEmptyArgs($object, ...$args): void { // verify if the arguments are empty or if they don't exist
        foreach ($args as $arg) {                      // for each argument
            if (empty($object->$arg)) {                // if the argument is empty
                http_response_code(400);  // 400: Bad Request
                echo json_encode(["missing" => $arg]); // return the missing argument
                die();
            }
        }
    }
}