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

    function GenerateRandomuuid(): int
    {
        $uuid = random_int(100000000, 999999999);
        return $uuid;

    }

    function AddAccount(string $password, string $mail): bool
    {
        $device = exec("hostname");
        if ($this->TestMail($mail)) {
            return false;
        }
        if ($this->TestPasswordFormat($password)==false) {
            return false;
        }
        if ($this->MailExistTmp($mail)) {
            echo "Mail already exist!\n";
            return false;
        }
        $uuid = $this->GenerateRandomuuid();
        if ($this->TestuuidExist($uuid)) {
            $this->AddAccount($password, $mail);
        }
        $password = hash('sha512', $password);

        try {
            $query = "INSERT INTO accounttmp (uuid, password,mail) VALUES (?, ?,?);";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $stmt->bindValue(2, $password, PDO::PARAM_STR);
            $stmt->bindValue(3, $mail, PDO::PARAM_STR);
            $result = $stmt->execute();
            if ($result) {
                //$this->CreateToken($mail);
                echo "Account added!\n";
            } else {
                echo "Failed to add account!\n";
            }
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Error adding account: " . $e->getMessage());
        }

    }

    function MailExistTmp($mail):bool
    {
        try {
            $query = "SELECT mail FROM accounttmp WHERE mail = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $mail, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                return true;
            } else {
                return false;
            }

        } catch (PDOException $e) {
            throw new PDOException("Error testing connection: " . $e->getMessage());
        }
    }

    function AccountExist($uuid):bool
    {
        try {
            $query = "SELECT uuid FROM account WHERE uuid = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                return true;
            } else {
                return false;
            }

        } catch (PDOException $e) {
            throw new PDOException("Error testing connection: " . $e->getMessage());
        }
    }

    function GetUuidtmp($mail):int
    {
        try {
            $query = "SELECT uuid FROM accounttmp WHERE mail = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $mail, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                return $result['uuid'];
            } else {
                return 0;
            }

        } catch (PDOException $e) {
            throw new PDOException("Error testing connection: " . $e->getMessage());
        }

    }



    function VerifyAccount($password, $mail): bool
    {
        $device = exec("hostname");
        $uuid = $this->GetUuidtmp($mail);
        $password = hash('sha512', $password);
        if (!$uuid){
            echo "Account does not exist!\n";
            return false;
        }
        if($this->AccountExist($uuid)){
            echo "Account already exist!\n";
            return false;
        }

        try {
            $query = "INSERT INTO account (uuid, password) VALUES (?, ?);";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $stmt->bindValue(2, $password, PDO::PARAM_STR);
            $this->AddUser($uuid, $mail);
            $result = $stmt->execute();
            $this->DeletetmpAccount($mail);
            $this->AddSignedIn($uuid,$device);
            if ($result) {
                echo "Account added!\n";
            } else {
                echo "Failed to add account!\n";
            }
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Error adding account: " . $e->getMessage());
        }

    }

    function DeletetmpAccount($mail)
    {
        try {
            $uuid = $this->UuidFinder($mail);
            $query = "DELETE FROM accounttmp WHERE uuid = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $result = $stmt->execute();
            if ($result) {
                echo "Account deleted!\n";
            } else {
                echo "Failed to delete account!\n";
            }
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Error deleting account: " . $e->getMessage());
        }

    }


    function AddSignedIn($uuid,$device):bool
    {
        try {
            $query = "INSERT INTO signedin (uuid,is_connected,device) VALUES (?,false,?);";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $stmt->bindValue(2, $device, PDO::PARAM_STR);
            $result = $stmt->execute();
            if ($result) {
                echo "Signed in added!\n";
            } else {
                echo "Failed to add signed in!\n";
            }
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Error adding signed in: " . $e->getMessage());
        }
    }



    function TestConnectionpwd($mail, $password): bool
    {
        try {
            $uuid = $this->UuidFinder($mail);
            $query = "SELECT password FROM account WHERE uuid = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $request = $stmt->execute();
            $compare = hash('sha512', $password);
            $result = $stmt->fetch();
            if ($result){
                if ($result['password'] == $compare) {
                    echo "Connection is working!\n";
                    return true;
                } else {
                    echo "Password Issue\n";
                    return false;
                }
            } else {
                echo "Password Issue\n";
                return false;
            }
        } catch (PDOException $e) {
            throw new PDOException("Error testing connection: " . $e->getMessage());
        }
    }

    function TestConnectionToken($mail,$device): bool
    {
        try {

            $this->TokenDeleteExpired();
            $uuid = $this->UuidFinder($mail);
            $query = "SELECT otp FROM accountotp WHERE uuid = ? AND validity > NOW() AND device = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $stmt->bindValue(2, $device, PDO::PARAM_STR);
            $request = $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                echo "Connection is working!\n";
                return true;
            } else {
                echo "Token error\n";
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
                return true;
            } else {
                return false;
            }

        } catch (PDOException $e) {
            throw new PDOException("Error testing connection: " . $e->getMessage());
        }
    }


    function AddUser($uuid, $mail): bool
    {
        if (!$this->UserExist($mail)) {

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
        } else {
            echo "User Creation failed!\n";
            return false;
        }
    }

    function CreateToken($mail,$device): bool
    {
        if ($this->TestConnectionToken($mail,$device)) {
            echo "Token already exists!\n";
            return false;
        }
        try {
            $uuid = $this->UuidFinder($mail);
            $query = "INSERT INTO accountotp (uuid, otp, device, validity) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE));";
            $stmt = $this->connection->dbh->prepare($query);
            $token = bin2hex(random_int(100000, 999999));
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $stmt->bindValue(2, $token, PDO::PARAM_STR);
            $stmt->bindValue(3, $device, PDO::PARAM_STR);
            $result = $stmt->execute();
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

    function DeleteToken($mail,$device):bool
    {
        try {
            $uuid = $this->UuidFinder($mail);
            $query = "DELETE FROM accountotp WHERE uuid = ? AND device = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $stmt->bindValue(2, $device, PDO::PARAM_STR);
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

    function UserExist($mail):bool
    {
        try {
            $query = "SELECT mail FROM user WHERE mail = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $mail, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                return true;
            } else {
                return false;
            }

        } catch (PDOException $e) {
            throw new PDOException("Error testing connection: " . $e->getMessage());
        }
    }
    function UuidFinder(string $mail):int
    {
      try {
            $query = "SELECT uuid FROM user WHERE mail = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $mail, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                return $result['uuid'];
            } else {
                return 0;
            }

        } catch (PDOException $e) {
            throw new PDOException("Error testing connection: " . $e->getMessage());
        }

    }



    function TokenDeleteExpired():bool
    {
        try {
            $query = "DELETE FROM accountotp WHERE validity < NOW()";
            $stmt = $this->connection->dbh->prepare($query);
            $result = $stmt->execute();
            if ($result) {
            } else {
            }
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Error deleting token: " . $e->getMessage());
        }

    }

    function AddAccountAttempts($mail,$validate,$device)
    {
        try {
            $uuid = $this->UuidFinder($mail);
            $query = "INSERT INTO accountattempts (uuid, a_time,validate,device) VALUES (?, NOW(),?,?);";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $stmt->bindValue(2, $validate, PDO::PARAM_BOOL);
            $stmt->bindValue(3, $device, PDO::PARAM_STR);
            $result = $stmt->execute();
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Error adding account attempts: " . $e->getMessage());
        }
    }

    function ChangeConnexionState($mail,$device,$state):bool
    {
        try {
            $uuid = $this->UuidFinder($mail);
            $query = "UPDATE signedin SET is_connected = ? WHERE uuid = ? AND device = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $state, PDO::PARAM_BOOL);
            $stmt->bindValue(2, $uuid, PDO::PARAM_INT);
            $stmt->bindValue(3, $device, PDO::PARAM_STR);
            $result = $stmt->execute();
            if ($result) {
                echo "Connection state changed!\n";
            } else {
                echo "Failed to change connection state!\n";
            }
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Error changing connection state: " . $e->getMessage());
        }
    }

    function CheckConnexionState($mail,$device):bool
    {
        try {
            $uuid = $this->UuidFinder($mail);
            $query = "SELECT is_connected FROM signedin WHERE uuid = ? AND device = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $stmt->bindValue(2, $device, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                return $result['is_connected'];
            } else {
                return false;
            }
        } catch (PDOException $e) {
            throw new PDOException("Error checking connection state: " . $e->getMessage());
        }
    }




    function Connection($password, $mail,$memorized,$device): bool
    {
        if ($this->UserExist($mail) == false) {
            echo "User does not exist!\n";
            return false;
        }
        if ($this->CheckConnexionState($mail,$device)) {
            echo "You are already connected\n";
            $this->AddAccountAttempts($mail, true, $device);
            return false;
        }
        else {
            try {

                if ($this->TestConnectionToken($mail, $device)) {
                    echo "Token found!\n";
                    echo "VOUS ETES CONNECTE\n";
                    $this->AddAccountAttempts($mail, true, $device);
                    $this->ChangeConnexionState($mail, $device, true);
                    return true;
                } else {
                    $connect = $this->TestConnectionpwd($mail, $password);
                    if ($connect) {
                        echo "YOU ARE CONNECTED\n";
                        $this->AddAccountAttempts($mail, true, $device);
                        $this->ChangeConnexionState($mail, $device, true);
                        if ($memorized) {
                            $this->CreateToken($mail, $device);
                        }
                        return true;
                    } else {
                        echo "Connection is not working!\n";
                        $this->AddAccountAttempts($mail, false, $device);
                        return false;
                    }
                }

            } catch (PDOException $e) {
                throw new PDOException("Error connecting: " . $e->getMessage());
            }
        }

    }

    function Disconnect($mail,$device):bool
    {
        if (!$this->CheckConnexionState($mail,$device)){
            echo "You are already disconnected\n";
            return false;
        }
        else {
            try {
                $connect = $this->CheckConnexionState($mail, $device);
                if ($connect) {
                    $this->ChangeConnexionState($mail, $device, false);
                    echo "YOU ARE DISCONNECTED\n";
                    return true;
                } else {
                    echo "You are already disconnected\n";
                    return false;
                }
            } catch (PDOException $e) {
                throw new PDOException("Error disconnecting: " . $e->getMessage());
            }
        }
    }

    function ChangePassword($mail,$password,$newPassword):bool
    {
        $device = exec("hostname");
        if ($this->CheckConnexionState($mail,$device)) {
            if ($this->TestPasswordFormat($newPassword)) {
                $uuid = $this->UuidFinder($mail);
                $newPassword = hash('sha512', $newPassword);
                try {
                    $password = hash('sha512', $password);
                    $query = "UPDATE account SET password = ? WHERE uuid = ? AND password = ?";
                    $stmt = $this->connection->dbh->prepare($query);
                    $stmt->bindValue(1, $newPassword, PDO::PARAM_STR);
                    $stmt->bindValue(2, $uuid, PDO::PARAM_INT);
                    $stmt->bindValue(3, $password, PDO::PARAM_STR);
                    $result = $stmt->execute();
                    if ($result) {
                        echo "Password changed!\n";
                    } else {
                        echo "Failed to change password!\n";
                    }
                    return $result;
                } catch (PDOException $e) {
                    throw new PDOException("Error changing password: " . $e->getMessage());
                }
            } else {
                echo "Password is not valid!\n";
                return false;
            }
        } else {
            echo "You are not connected\n";
            return false;
        }
    }

    function StarDestroyer($mail,$password):bool
    {
        $uuid = $this->UuidFinder($mail);
        $device = exec("hostname");
        if ($this->CheckConnexionState($mail,$device)) {
            $this->LinkDestroyer($uuid);
            $this->Disconnect($mail,$device);
            $this->DeleteToken($mail,$device);
            $this->AttemptDestroyer($mail);
            $this->SignedInDestroyer($mail);
            $this->UserDestroyer($mail);
            $this->AccountDestroyer($uuid,$password);
            return true;
        }else {
            echo "You are not connected\n";
            return false;
        }
    }


    function AccountDestroyer($uuid,$password):bool
    {
        $password = hash('sha512', $password);
        try {
            $query = "DELETE FROM account WHERE uuid = ? AND password = ?;";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $stmt->bindValue(2, $password, PDO::PARAM_STR);
            $result = $stmt->execute();
            if ($result) {
                echo "Account destroyed!\n";
            } else {
                echo "Failed to destroy account!\n";
            }
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Error destroying account: " . $e->getMessage());
        }
    }

    function UserDestroyer($mail):bool
    {
        $uuid = $this->UuidFinder($mail);
        try {
            $query = "DELETE FROM user WHERE uuid = ?;";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $result = $stmt->execute();
            if ($result) {
                echo "User destroyed!\n";
            } else {
                echo "Failed to destroy user!\n";
            }
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Error destroying user: " . $e->getMessage());
        }
    }

    function AttemptDestroyer($mail):bool
    {
        $uuid = $this->UuidFinder($mail);
        try {
            $query = "DELETE  FROM accountattempts WHERE uuid = ?;";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $result = $stmt->execute();
            if ($result) {
                echo "Attempts destroyed!\n";
            } else {
                echo "Failed to destroy attempts!\n";
            }
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Error destroying attempts: " . $e->getMessage());
        }

    }

    function SignedInDestroyer($mail):bool
    {
        $uuid = $this->UuidFinder($mail);
        try {
            $query = "DELETE  FROM signedin WHERE uuid = ?;";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $result = $stmt->execute();
            if ($result) {
                echo "Signed in destroyed!\n";
            } else {
                echo "Failed to destroy signed in!\n";
            }
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Error destroying signed in: " . $e->getMessage());
        }
    }

    function LinkDestroyer($uuid):bool
    {
        try {
            $query = "DELETE  FROM accountservice WHERE uuid = ?;";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $result = $stmt->execute();
            if ($result) {
                echo "Link destroyed!\n";
            } else {
                echo "Failed to destroy link!\n";
            }
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Error destroying link: " . $e->getMessage());
        }
    }



    function AddService($name,$link,$description):bool{
        $uuid_service = $this->GenerateRandomuuid();
        if (!$this->TestServiceExist($name,$uuid_service)) {
            return false;
        }
        try {
            $query = "INSERT INTO publicservice (name, link, description,uuid_service) VALUES (?, ?, ?, ?);";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $name, PDO::PARAM_STR);
            $stmt->bindValue(2, $link, PDO::PARAM_STR);
            $stmt->bindValue(3, $description, PDO::PARAM_STR);
            $stmt->bindValue(4, $uuid_service, PDO::PARAM_INT);
            $result = $stmt->execute();
            if ($result) {
                echo "Service added!\n";
            } else {
                echo "Failed to add service!\n";
            }
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Error adding service: " . $e->getMessage());
        }
    }

    function TestuuidExistService($uuid_service): bool
    {
        try {
            $query = "SELECT uuid_service FROM publicservice WHERE uuid_service = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid_service, PDO::PARAM_INT);
            $request = $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                return true;
            } else {
                return false;
            }

        } catch (PDOException $e) {
            throw new PDOException("Error testing connection: " . $e->getMessage());
        }
    }

    function TestNameExistService($name): bool
    {
        try {
            $query = "SELECT name FROM publicservice WHERE name = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $name, PDO::PARAM_STR);
            $request = $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                return true;
            } else {
                return false;
            }

        } catch (PDOException $e) {
            throw new PDOException("Error testing connection: " . $e->getMessage());
        }
    }

    function TestServiceExist($name,$uuid_service): bool
    {
        if ($this->TestNameExistService($name)) {
            echo "Service already exist!\n";
            return false;
        }else if ($this->TestuuidExistService($uuid_service)) {
            $this->TestServiceExist($name,$uuid_service);
        }
        return true;
    }


    function LinkServiceToAccount($mail,$name):bool
    {
        $uuid = $this->UuidFinder($mail);
        $uuid_service = $this->GetServiceuuid($name);

        if ($this->LinkExist($mail,$name)) {
            echo "Link already exist!\n";
            return false;
        }
        if (!$this->TestuuidExist($uuid)) {
            echo "Account does not exist!\n";
            return false;
        }
        if (!$this->TestuuidExistService($uuid_service)) {
            echo "Service does not exist!\n";
            return false;
        }
        if ($this->TestuuidExistService($uuid_service)) {
            try {
                $query = "INSERT INTO accountservice (uuid, uuid_service) VALUES (?, ?);";
                $stmt = $this->connection->dbh->prepare($query);
                $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
                $stmt->bindValue(2, $uuid_service, PDO::PARAM_INT);
                $result = $stmt->execute();
                if ($result) {
                    echo "Service linked!\n";
                } else {
                    echo "Failed to link service!\n";
                }
                return $result;
            } catch (PDOException $e) {
                throw new PDOException("Error linking service: " . $e->getMessage());
            }
        } else {
            echo "Service does not exist!\n";
            return false;
        }
    }

    function GetServiceuuid($name):int
    {
        try {
            $query = "SELECT uuid_service FROM publicservice WHERE name = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $name, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                return $result['uuid_service'];
            } else {
                return 0;
            }

        } catch (PDOException $e) {
            throw new PDOException("Error testing connection: " . $e->getMessage());
        }

    }


    function LinkExist($mail,$name):bool
    {
        $uuid = $this->UuidFinder($mail);
        $uuid_service = $this->GetServiceuuid($name);
        try {
            $query = "SELECT uuid FROM accountservice WHERE uuid = ? AND uuid_service = ?";
            $stmt = $this->connection->dbh->prepare($query);
            $stmt->bindValue(1, $uuid, PDO::PARAM_INT);
            $stmt->bindValue(2, $uuid_service, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                return true;
            } else {
                return false;
            }

        } catch (PDOException $e) {
            throw new PDOException("Error testing connection: " . $e->getMessage());
        }
    }







}