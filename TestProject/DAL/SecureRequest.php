<?php

//require the connection file that is in /DAL/Connection.php
require_once("Connection.php");

class SecureRequest{
    private Connection $connection;
    private $credentials;

    function __construct($credentials = NULL) {
        try {
            $this->connection = new Connection($credentials);
            $this->credentials = $credentials;
        } catch (PDOException $e) {
            throw new PDOException("Database connection error: " . $e->getMessage());
        }
    }

    function AllSelect():array{
        $query = "SELECT * FROM account";
        $stmt = $this->connection->dbh->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function AddAccount(int $uuid, int $stretch, string $password,string $mail): bool {
        try {
            $query = "INSERT INTO account (uuid, stretch, password) VALUES (?, ?, ?);";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $stmt->bindValue(2, $stretch, PDO::PARAM_INT);
            $stmt->bindValue(3, $password, PDO::PARAM_STR);
            $this->AddUser($uuid,$mail);
            $result = $stmt->execute();
            if ($result) {
                echo "Account added!";
            } else {
                echo "Failed to add account!";
            }
            return $result;
        }
        catch (PDOException $e) {
            throw new PDOException("Error adding account: " . $e->getMessage());
        }

    }

    function AddUser($uuid,$mail):bool{
        try {
            $query = "INSERT INTO user (uuid, mail) VALUES (?, ?);";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $stmt->bindValue(2, $mail, PDO::PARAM_STR);
            $result = $stmt->execute();
            if ($result) {
                echo "Account added!";
            } else {
                echo "Failed to add account!";
            }
            return $result;
        }
        catch (PDOException $e) {
            throw new PDOException("Error adding user: " . $e->getMessage());
        }
    }



}