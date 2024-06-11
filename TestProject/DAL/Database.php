<?php

//require the connection file that is in /DAL/Connection.php
require_once("Connection.php");
class Database {
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

    // Database functions

    /**
     * Creates a new database if it doesn't already exist.
     *
     * @param string|null $name - The name of the database to be created. If not provided, it uses the current database name.
     *
     * @return bool - True on success, false on failure.
     *
     * @throws Exception - If there is an error in the database operation.
     */
    function CreateDatabase(string $name = null): bool {
        $name = $name ?? $this->connection->dbname;

        try {
            $stmt = $this->connection->pdo->prepare("CREATE DATABASE IF NOT EXISTS $name");
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new PDOException("Error creating database: " . $e->getMessage());
        }
    }

    /**
     * Creates a new database user if it doesn't already exist.
     *
     * @param string $user - The username of the new user.
     * @param string $pwd - The password for the new user.
     *
     * @return bool - True on success, false on failure.
     *
     * @throws Exception - If there is an error in the database operation.
     */
    function CreateUser(string $user, string $pwd): bool {
        try {
            $stmt = $this->connection->pdo->prepare("CREATE USER IF NOT EXISTS '$user'@'localhost' IDENTIFIED BY ?");
            $stmt->execute([$pwd]);
            return true;
        } catch (PDOException $e) {
            throw new PDOException("Error creating user: " . $e->getMessage());
        }
    }

    /**
     * Grants all privileges on a specific database to a user.
     *
     * @param string $user - The username for which privileges will be granted.
     * @param string $db - The database for which privileges will be granted.
     *
     * @return bool - True on success, false on failure.
     *
     * @throws Exception - If there is an error in the database operation.
     */
    function CreatePrivileges(string $user, string $db): bool {
        try {
            // Select the database before granting privileges
            $this->connection->pdo->exec("USE `$db`");

            $stmt = $this->connection->pdo->prepare("GRANT ALL PRIVILEGES ON `$db`.* TO '$user'@'localhost'");
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new PDOException("Error creating privileges: " . $e->getMessage());
        }
    }

    // Tables functions

    /**
     * Selects a database table to work with, updating the active connection.
     *
     * @param string $table - The name of the table to select.
     *
     * @return bool - True if the table exists and is successfully selected, false otherwise.
     *
     * @throws Exception - If there is an error in the database operation.
     */
    function SelectTable(string $table): bool {
        if ($this->TableExists($table)) {
            $this->connection->dbname = $table;
            $this->connection->pdo = Connection::PDO($this->credentials, $this->connection->dbname);
            return true;
        }
        return false;
    }

    /**
     * Checks if a table exists in the selected database.
     *
     * @param string $table - The name of the table to check for existence.
     *
     * @return bool - True if the table exists, false otherwise.
     *
     * @throws Exception - If there is an error in the database operation.
     */
    function TableExists(string $table): bool {
        $stmt = $this->connection->pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $result = $stmt->fetchAll();
        return count($result) > 0;
    }

    /**
     * Copies the structure and data from an existing table to a new table.
     *
     * @param string $table - The name of the existing table to copy.
     * @param string $newName - The name of the new table to create.
     *
     * @return bool - True if the copy is successful, false otherwise.
     *
     * @throws Exception - If there is an error in the database operation.
     */
    function CopyTable(string $table, string $newName): bool {
        if ($this->TableExists($table) && !$this->TableExists($newName)) {
            try {
                $sql = "CREATE TABLE $newName LIKE $table";
                $this->connection->pdo->exec($sql);
                $sql = "INSERT INTO $newName SELECT * FROM $table";
                $this->connection->pdo->exec($sql);
                return true;
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
        return false;
    }

    /**
     * Creates a new table in the database with an optional list of columns.
     *
     * @param string $table - The name of the table to create.
     * @param array|null $columns - An optional list of columns for the new table.
     *
     * @return bool - True if the table is successfully created, false if the table already exists.
     *
     * @throws Exception - If there is an error in the database operation.
     */
    function CreateTable(string $table, array $columns = NULL): bool {
        if ($this->TableExists($table)) {
            return false;
        } else {
            try {
                // Construct the SQL query to create the table
                $sql = "CREATE TABLE $table (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY";

                // Add columns to the SQL query using placeholders
                if (!empty($columns)) {
                    foreach ($columns as $columnName => $columnType) {
                        $sql .= ", $columnName $columnType";
                    }
                }

                $sql .= ")";

                // Execute the SQL query to create the table
                $stmt = $this->connection->pdo->prepare($sql);

                // Bind values for each column (if any)

                $stmt->execute();

                return true;
            } catch (PDOException $e) {
                throw new Exception("Error creating table: " . $e->getMessage());
            }
        }
    }

    /**
     * Deletes a table from the database.
     *
     * @param string $table - The name of the table to delete.
     *
     * @return bool - True if the table is successfully deleted, false if the table doesn't exist.
     *
     * @throws Exception - If there is an error in the database operation.
     */
    function DeleteTable(string $table): bool {
        if ($this->TableExists($table)) {
            try {
                $sql = "DROP TABLE $table";
                $this->connection->pdo->exec($sql);
                return true;
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
        return false;
    }

    /**
     * Clears all records from a table while keeping the table structure.
     *
     * @param string $table - The name of the table to clear.
     *
     * @return bool - True if the table is successfully cleared, false if the table doesn't exist.
     *
     * @throws Exception - If there is an error in the database operation.
     */
    function ClearTable(string $table): bool {
        if ($this->TableExists($table)) {
            try {
                $sql = "TRUNCATE TABLE $table";
                $this->connection->pdo->exec($sql);
                return true;
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
        return false;
    }

    /**
     * Renames a table in the database.
     *
     * @param string $table - The current name of the table.
     * @param string $newName - The new name for the table.
     *
     * @return bool - True if the table is successfully renamed, false if the table doesn't exist or the new name already exists.
     *
     * @throws Exception - If there is an error in the database operation.
     */
    function RenameTable(string $table, string $newName): bool {
        if ($this->TableExists($table) && !$this->TableExists($newName)) {
            try {
                $sql = "RENAME TABLE $table TO $newName";
                $this->connection->pdo->exec($sql);
                return true;
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
        return false;
    }

    /**
     * Imports records into a table.
     *
     * @param string $table - The name of the table to import records into.
     * @param array $records - An array of records to import.
     *
     * @return bool - True if the import is successful, false if the table doesn't exist or the records are empty.
     *
     * @throws Exception - If there is an error in the database operation.
     */
    function ImportTable(string $table, array $records): bool {
        if ($this->TableExists($table) && !empty($records)) {
            try {
                $sql = "INSERT INTO $table VALUES ";
                $placeholders = rtrim(str_repeat("?,", count($records[0])), ",");
                $sql .= "($placeholders)";
                $stmt = $this->connection->pdo->prepare($sql);
                foreach ($records as $record) {
                    $stmt->execute($record);
                }
                return true;
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
        return false;
    }

    /**
     * Exports records from a table.
     *
     * @param string $table - The name of the table to export records from.
     * @param array|null $columns - An optional array of column names to export. If not provided, exports all columns.
     *
     * @return array|bool - An array of records if the export is successful, false if the table doesn't exist.
     *
     * @throws Exception - If there is an error in the database operation.
     */
    function ExportTable(string $table, array $columns = NULL): array|bool {
        if ($this->TableExists($table)) {
            try {
                // Construct the SQL query for exporting records
                $columnNames = !empty($columns) ? implode(", ", $columns) : "*";
                $sql = "SELECT $columnNames FROM $table";

                // Execute the SQL query to export records
                $stmt = $this->connection->pdo->query($sql);

                // Fetch all records as an associative array
                $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

                return $records;
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
        return false;
    }


    // Records functions

    /**
     * Adds a new record to the specified table.
     *
     * @param string $table - The name of the table to which the record will be added.
     * @param array|null $columns - Optional. An array of values for each column in the table.
     *                             If provided, the values will be inserted into the corresponding columns.
     *                             If not provided, an empty record will be inserted.
     *
     * @return bool - True if the record is successfully added, false otherwise.
     *
     * @throws Exception - If there is an error in the database operation.
     */
    function AddRecord(string $table, array $columns = NULL): bool {
        if ($this->TableExists($table)) {
            try {
                if (is_array($columns) && !empty($columns)) {
                    // If specific columns are provided, construct placeholders and execute prepared statement
                    $columnNames = implode(', ', array_keys($columns));
                    $columnValues = implode(', ', array_fill(0, count($columns), '?'));

                    $sql = "INSERT INTO $table ($columnNames) VALUES ($columnValues)";
                    $stmt = $this->connection->pdo->prepare($sql);
                    $stmt->execute(array_values($columns));
                } else {
                    // If no columns provided, insert an empty record
                    $sql = "INSERT INTO $table VALUES ()";
                    $this->connection->pdo->exec($sql);
                }

                return true;
            } catch (PDOException $e) {
                throw new Exception("Error adding record: " . $e->getMessage());
            }
        }
        return false;
    }

    /**
     * Deletes records from the specified table based on given conditions.
     *
     * @param string $table - The name of the table from which records will be deleted.
     * @param array|null $column - Optional. An associative array representing conditions for deletion.
     *                            If provided, records matching these conditions will be deleted.
     *                            If not provided, all records in the table will be deleted.
     *
     * @return bool - True if records are successfully deleted, false otherwise.
     *
     * @throws Exception - If there is an error in the database operation.
     */
    function DeleteRecords(string $table, array $column = NULL): bool {
        if ($this->TableExists($table)) {
            try {
                if (is_array($column) && !empty($column)) {
                    // If specific conditions are provided, construct WHERE clause and execute prepared statement
                    $conditions = implode(' AND ', array_map(function ($key) {
                        return "$key = ?";
                    }, array_keys($column)));

                    $sql = "DELETE FROM $table WHERE $conditions";
                    $stmt = $this->connection->pdo->prepare($sql);
                    $stmt->execute(array_values($column));
                } else {
                    // If no conditions provided, delete all records in the table
                    $sql = "DELETE FROM $table";
                    $this->connection->pdo->exec($sql);
                }

                return true;
            } catch (PDOException $e) {
                throw new Exception("Error deleting records: " . $e->getMessage());
            }
        }
        return false;
    }

    /**
     * Updates records in the specified table based on given column values and filter conditions.
     *
     * @param string $table - The name of the table to update records in.
     * @param array|null $column - Optional. An associative array representing column-value pairs to be updated.
     *                            If provided, records will be updated with these new values.
     * @param array|null $filter - Optional. An associative array representing conditions for updating records.
     *                            If provided, only records matching these conditions will be updated.
     *
     * @return bool - True if records are successfully updated, false otherwise.
     *
     * @throws Exception - If there is an error in the database operation.
     */
    function UpdateRecord(string $table, array $column = NULL, array $filter = NULL): bool {
        if ($this->TableExists($table)) {
            try {
                $sql = "UPDATE $table SET ";

                if (is_array($column) && !empty($column)) {
                    // If specific columns and values provided, construct SET clause
                    $setClause = implode(', ', array_map(function ($key) {
                        return "$key = ?";
                    }, array_keys($column)));
                    $sql .= $setClause;
                } else {
                    throw new Exception("No columns provided for update.");
                }

                if (is_array($filter) && !empty($filter)) {
                    // If specific conditions provided, construct WHERE clause
                    $conditions = implode(' AND ', array_map(function ($key) {
                        return "$key = ?";
                    }, array_keys($filter)));
                    $sql .= " WHERE $conditions";
                } else {
                    throw new Exception("No filter conditions provided for update.");
                }

                $stmt = $this->connection->pdo->prepare($sql);

                // Bind values for SET clause
                $i = 1;
                foreach ($column as $value) {
                    $stmt->bindValue($i++, $value);
                }

                // Bind values for WHERE clause
                foreach ($filter as $value) {
                    $stmt->bindValue($i++, $value);
                }

                $stmt->execute();

                return true;
            } catch (PDOException $e) {
                throw new Exception("Error updating records: " . $e->getMessage());
            }
        }
        return false;
    }

    /**
     * Retrieves records from the specified table based on given conditions, order, grouping, and limit.
     *
     * @param string $table - The name of the table to retrieve records from.
     * @param array|null $column - Optional. An associative array representing conditions for retrieving records.
     *                             If provided, only records matching these conditions will be retrieved.
     * @param array|null $order - Optional. An associative array representing columns and their order for sorting.
     *                            If provided, records will be sorted based on these columns and orders.
     * @param array|null $group - Optional. An associative array representing columns and their order for grouping.
     *                            If provided, records will be grouped based on these columns and orders.
     * @param int|null $limit - Optional. The maximum number of records to retrieve. If provided, limits the result set.
     *
     * @return array|bool - An array of records if successful, false otherwise.
     *
     * @throws Exception - If there is an error in the database operation.
     */
    function SelectRecord(string $table, array $column = NULL, array $order = NULL, array $group = NULL, int $limit = NULL): array|bool {
        if ($this->TableExists($table)) {
            try {
                $sql = "SELECT * FROM $table";

                if (is_array($column) && !empty($column)) {
                    // If specific conditions provided, construct WHERE clause
                    $conditions = implode(' AND ', array_map(function ($key) {
                        return "$key = ?";
                    }, array_keys($column)));
                    $sql .= " WHERE $conditions";
                }

                if (is_array($order) && !empty($order)) {
                    // If specific columns and orders provided, construct ORDER BY clause
                    $orderClause = implode(', ', array_map(function ($key, $value) {
                        return "$key $value";
                    }, array_keys($order), $order));
                    $sql .= " ORDER BY $orderClause";
                }

                if (is_array($group) && !empty($group)) {
                    // If specific columns and orders provided, construct GROUP BY clause
                    $groupClause = implode(', ', array_map(function ($key, $value) {
                        return "$key $value";
                    }, array_keys($group), $group));
                    $sql .= " GROUP BY $groupClause";
                }

                if ($limit != NULL) {
                    // If limit provided, add LIMIT clause
                    $sql .= " LIMIT $limit";
                }

                if (isset($conditions)) {
                    // If conditions are present, use prepared statement
                    $stmt = $this->connection->pdo->prepare($sql);
                    $stmt->execute(array_values($column));
                    return $stmt->fetchAll();
                } else {
                    // If no conditions, use simple query
                    $stmt = $this->connection->pdo->query($sql);
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            } catch (PDOException $e) {
                throw new Exception("Error selecting records: " . $e->getMessage());
            }
        }
        return false;
    }
}