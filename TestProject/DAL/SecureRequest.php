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
        $password = hash('sha512', $password);
        try {
            $query = "INSERT INTO account (uuid, stretch, password) VALUES (?, ?, ?);";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $stmt->bindValue(2, $stretch, PDO::PARAM_INT);
            $stmt->bindValue(3, $password, PDO::PARAM_STR);
            $this->AddUser($uuid,$mail);
            $result = $stmt->execute();
            if ($result) {
                echo "Account added!\n";
                $this->CreateToken($uuid);
            } else {
                echo "Failed to add account!\n";
            }
            return $result;
        }
        catch (PDOException $e) {
            throw new PDOException("Error adding account: " . $e->getMessage());
        }

    }


    function TestConnectionpwd($uuid,$password):bool{
        try {
            $query = "SELECT password FROM account WHERE uuid = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $request = $stmt->execute();
            $compare = hash('sha512', $password);
            $result = $stmt->fetch();
            if ($result['password'] == $compare) {
                echo "Connection is working!\n";
                return true;
            } else {
                echo "Connection is not working!\n";
                return false;
            }
        }
        catch (PDOException $e) {
            throw new PDOException("Error testing connection: " . $e->getMessage());
        }
    }

    function TestConnectionToken($uuid,$token):void{
        try {
            $query = "SELECT otp FROM accountotp WHERE uuid = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $request = $stmt->execute();
            $result = $stmt->fetch();
            $compare = hash('sha512', $token);
            if ($result['otp'] == $compare) {
                echo "Connection is working!\n";
            } else {
                echo "Connection is not working!\n";
            }

        }
        catch (PDOException $e) {
            throw new PDOException("Error testing connection: " . $e->getMessage());
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
                echo "Account added!\n";
            } else {
                echo "Failed to add account!\n";
            }
            return $result;
        }
        catch (PDOException $e) {
            throw new PDOException("Error adding user: " . $e->getMessage());
        }
    }

    function CreateToken($uuid):bool{
        try {
            $query = "INSERT INTO accountotp (uuid, otp,validity) VALUES (?, ?,600);";
            $stmt = $this->connection->dbh->prepare($query);
            $token = bin2hex(random_bytes(32));
            $token = hash('sha512', $token);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $stmt->bindValue(2, $token, PDO::PARAM_STR);
            $result = $stmt->execute();
            if ($result) {
                echo "Token added!\n";
            } else {
                echo "Failed to add token!\n";
            }
            return $result;
        }
        catch (PDOException $e) {
            throw new PDOException("Error adding token: " . $e->getMessage());
        }
    }





}