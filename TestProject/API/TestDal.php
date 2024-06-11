<?php
require_once __DIR__ . "/Service.php";
require_once __DIR__ . "/../DAL/Database.php";

class TestDal extends Service {
    function Exec($finalObject): void {
        try {
            $credentials = Credentials::GetCredentials("root.json");
            $db = new Database($credentials);

            // Test CreateDatabase function
            $dbName = "test_database";
            $db->CreateDatabase($dbName);
            echo "Database created successfully: $dbName\n";

            // Test CreateUser function
            $username = "test_user";
            $password = "test_password";
            $db->CreateUser($username, $password);
            echo "User created successfully: $username\n";

            // Test CreatePrivileges function
            $db->CreatePrivileges($username, $dbName);
            echo "Privileges granted successfully for $username on $dbName\n";

            // Test CreateTable function
            $tableName = "example_table";
            $columns = [
                "column1" => "INT",
                "column2" => "INT",
                "column3" => "INT",
                "column4" => "INT",
            ];
            $db->CreateTable($tableName, $columns);
            echo "Table created successfully: $tableName\n";

            // Test AddRecord function
            $recordValues = ["column1" => "value1", "column2" => "value2", "column3" => "value3", "column4" => "value4"];
            $db->AddRecord($tableName, $recordValues);
            echo "Record added successfully to $tableName\n";

            // Test SelectRecord function
            $selectedRecords = $db->SelectRecord($tableName, ["column1" => 1]);
            print_r($selectedRecords);

            // Test UpdateRecord function
            $updateValues = ["column2" => "new_value"];
            $filterConditions = ["column1" => 1];
            $db->UpdateRecord($tableName, $updateValues, $filterConditions);
            echo "Record updated successfully in $tableName\n";

            // Test DeleteRecords function
            $deleteConditions = ["column1" => 1];
            $db->DeleteRecords($tableName, $deleteConditions);
            echo "Records deleted successfully from $tableName\n";

            // Test ExportTable function
            $exportedRecords = $db->ExportTable($tableName);
            print_r($exportedRecords);


        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}