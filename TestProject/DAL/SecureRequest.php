<?php

//require the connection file that is in /DAL/Connection.php
require_once("Connection.php");


class SecureRequest
{
    private Connection $connection;
    private $credentials;

    function __construct($credentials = NULL)
    {
        try {
            $this->connection = new Connection($credentials);
            $this->credentials = $credentials;
        } catch (PDOException $e) {
            throw new PDOException("Database connection error: " . $e->getMessage());
        }
    }

    function AllSelect(): array
    {
        $query = "SELECT * FROM account";
        $stmt = $this->connection->dbh->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function GenerateRandomuuid(): int
    {
        $uuid = random_int(100000000, 999999999);
        return $uuid;

    }

    function AddAccount(string $password, string $mail): bool
    {
        if ($this->TestMail($mail)) {
            return false;
        }
        if ($this->TestPasswordFormat($password)) {
            return false;
        }
        $uuid = $this->GenerateRandomuuid();
        if ($this->TestuuidExist($uuid)) {
            $this->AddAccount($password, $mail);
        }
        $password = hash('sha512', $password);
        try {
            $query = "INSERT INTO account (uuid, password) VALUES (?, ?);";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $stmt->bindValue(2, $password, PDO::PARAM_STR);
            $this->AddUser($uuid, $mail);
            $result = $stmt->execute();
            if ($result) {
                echo "Account added!\n";
                $this->CreateToken($uuid);
            } else {
                echo "Failed to add account!\n";
            }
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Error adding account: " . $e->getMessage());
        }

    }


    function TestConnectionpwd($uuid, $password): bool
    {
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
        } catch (PDOException $e) {
            throw new PDOException("Error testing connection: " . $e->getMessage());
        }
    }

    function TestConnectionToken($uuid): bool
    {
        try {
            $query = "SELECT otp FROM accountotp WHERE uuid = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $request = $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                echo "Connection is working!\n";
                return true;
            } else {
                echo "Connection is not working!\n";
                return false;
            }

        } catch (PDOException $e) {
            throw new PDOException("Error testing connection: " . $e->getMessage());
        }
    }

    function TestMail($mail): bool
    {
        try {
            if ($this->MailFormat($mail)) {
                //echo "Mail is valid!\n";
            } else {
                echo "Mail is not valid!\n";
                return true;
            }
            $query = "SELECT mail FROM user WHERE mail = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $mail, PDO::PARAM_STR);
            $request = $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                echo "Mail already exist!\n";
                return true;
            } else {
                return false;
            }

        } catch (PDOException $e) {
            throw new PDOException("Error testing connection: " . $e->getMessage());
        }
    }

    function MailFormat($mail):bool
    {
        if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    function TestPasswordFormat($password):bool
    {
        if (preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,20}$/', $password)) {
            return true;
        } else {
            echo "Password is not valid!\n";
            return false;
        }
    }



    function TestuuidExist($uuid): bool
    {
        try {
            $query = "SELECT uuid FROM account WHERE uuid = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $request = $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                echo "Connection is working!\n";
                return true;
            } else {
                echo "Connection is not working!\n";
                return false;
            }

        } catch (PDOException $e) {
            throw new PDOException("Error testing connection: " . $e->getMessage());
        }
    }


    function AddUser($uuid, $mail): bool
    {
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
        } catch (PDOException $e) {
            throw new PDOException("Error adding user: " . $e->getMessage());
        }
    }

    function CreateToken($uuid): bool
    {
        if ($this->TestConnectionToken($uuid)) {
            echo "Token already exists!\n";
            return false;
        }
        try {
            $query = "INSERT INTO accountotp (uuid, otp,validity) VALUES (?, ?,600);";
            $stmt = $this->connection->dbh->prepare($query);
            $token = bin2hex(random_bytes(32));
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $stmt->bindValue(2, $token, PDO::PARAM_STR);
            $result = $stmt->execute();
            //$this->DeleteToken($uuid);
            if ($result) {
                echo "Token added!\n";
            } else {
                echo "Failed to add token!\n";
            }
            return $result;
        } catch (PDOException$e) {
            throw new PDOException("Error adding token: " . $e->getMessage());
        }
    }

    //fonction who wait 600 secondes before delete the token
    function DeleteToken($uuid): bool
    {
        try {
            // Attendre 600 secondes (10 minutes)
            echo "Waiting 600 seconds before deleting token...\n";
            sleep(10);
            echo "Deleting token...\n";

            $query = "DELETE FROM accountotp WHERE uuid = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result) {
                echo "Token deleted!\n";
            } else {
                echo "Failed to delete token!\n";
            }
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Error deleting token: " . $e->getMessage());
        }
    }

    /*function Connection($mail, $password): bool
    {
        $query = "SELECT uuid FROM user WHERE mail = ?";


    }*/








}