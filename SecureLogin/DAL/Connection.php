<?php

require_once __DIR__ . "/../SecurityLib/Credentials.php";
class Connection {
    public mysqli $mysqli;
    public PDO $pdo;
    public PDO $dbh;
    public $dbname;

    public function __construct($credentials=NULL) {
        if (!isset($credentials)) $credentials = Credentials::GetCredentials("db.json");

        $this->dbname = $credentials->dbname;
        $this->mysqli = new \mysqli($credentials->servername, $credentials->username, $credentials->password, $credentials->dbname, $credentials->port);
        $this->pdo = Connection::PDO($credentials);
        $this->dbh = Connection::PDO($credentials, $this->dbname);
    }
    static function PDO($credentials, $dbname=NULL): PDO {
        if (isset($dbname)) {
            $dsn = "mysql:host=$credentials->servername;dbname=$credentials->dbname;port=$credentials->port;charset=$credentials->charset";
        }
        else {
            $dsn = "mysql:host=$credentials->servername;port=$credentials->port";
        }

        try {
            $dbh = new \PDO($dsn, $credentials->username, $credentials->password);
            $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
        return $dbh;
    }
}