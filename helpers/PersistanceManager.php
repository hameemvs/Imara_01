<?php

class PersistanceManager
{
    private $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            // Create a tables if it doesn't exist
            $this->createTables();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function createTables()
    {
        $query_users = "CREATE TABLE IF NOT EXISTS Users (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL UNIQUE,
    FullName VARCHAR(100),
    Password VARCHAR(255) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Role ENUM('admin', 'member') NOT NULL DEFAULT 'member',
    CreatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  )ENGINE=InnoDB DEFAULT COLLATE=utf8mb4_unicode_520_ci;";

        $this->pdo->exec($query_users);

        $query_categories = "CREATE TABLE IF NOT EXISTS Categories (
    CategoryID INT AUTO_INCREMENT PRIMARY KEY,
    CategoryName VARCHAR(50) UNIQUE NOT NULL
)ENGINE=InnoDB DEFAULT COLLATE=utf8mb4_unicode_520_ci;";
        $this->pdo->exec($query_categories);

        $query_books = "CREATE TABLE IF NOT EXISTS Books (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Title VARCHAR(255) NOT NULL,
    CategoryID INT NOT NULL,
    ISBN VARCHAR(50) NOT NULL,
    Photo VARCHAR(255),
    Author VARCHAR(255) NOT NULL,
    Quantity INT NOT NULL,
    CreatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT COLLATE=utf8mb4_unicode_520_ci;";

        $this->pdo->exec($query_books);


        $query_borrowed_books = "CREATE TABLE IF NOT EXISTS BorrowedBooks (
      ID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    BookID INT NOT NULL,
    BorrowDate DATE NOT NULL,
    DueDate DATE NOT NULL,
    ReturnDate DATE,
    BorrowStatus ENUM('borrowed', 'returned', 'lost') NOT NULL DEFAULT 'borrowed',
    FineAmount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    FineStatus VARCHAR(50) DEFAULT 'None',
    PaidStatus ENUM('unpaid', 'paid') DEFAULT 'unpaid',
    PaidDate DATETIME,
    CreatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT COLLATE=utf8mb4_unicode_520_ci;";

        $this->pdo->exec($query_borrowed_books);

    }



    public function getCount($query, $param = null)
    {
        $result = $this->executeQuery($query, $param, true);
        return $result['c'];
    }

    public function run($query, $param = null, $fetchFirstRecOnly = false)
    {
        return $this->executeQuery($query, $param, $fetchFirstRecOnly);
    }

    public function insertAndGetLastRowId($query, $param = null)
    {
        return $this->executeQuery($query, $param, true, true);
    }

    private function executeQuery($query, $param = null, $fetchFirstRecOnly = false, $getLastInsertedId = false)
    {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($param);

            if ($getLastInsertedId) {
                return $this->pdo->lastInsertId();
            }

            if ($fetchFirstRecOnly)
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            else
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return -1;
        }
    }
}
