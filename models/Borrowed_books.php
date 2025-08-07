<?php

require_once 'BaseModel.php';

class Borrowed_Books extends BaseModel
{
    public $id;
    public $User_id;
    public $book_id;
    public $BorrowStatus;
    public $BorrowDate;
    public $DueDate;
    public $ReturnDate;
    public $request_status;
    public $fine_status;
    public $fine_amount;
    public $paid_date;
    public $Paid_status;


    protected function getTableName()
    {
        return "borrowedbooks";
    }

    public function getById($id)
{
    $param = array(':id' => $id);
    return $this->pm->run(
        "SELECT *, 
            u.Username AS user_name, 
            b.Title AS book_name, 
            bb.ID AS id, 
            f.FineStatus AS fine_status, 
            f.FineAmount AS fine_amount,
            f.PaidDate AS paid_date   -- Add PaidDate to the SELECT clause
        FROM borrowedbooks AS bb
        JOIN Users AS u ON u.ID = bb.UserID
        JOIN Books AS b ON b.ID = bb.BookID
        LEFT JOIN Fines AS f ON f.BorrowID = bb.ID
        WHERE bb.ID = :id;",
        $param,
        true
    );
}

    
protected function addNewRec()
{
    try {
        // Validate required fields
        if (empty($this->User_id) || empty($this->book_id)) {
            throw new Exception('User ID and Book ID are required fields.');
        }

        // Check if the user exists
        $userCheck = $this->pm->run("SELECT ID FROM Users WHERE ID = :user_id", [':user_id' => $this->User_id]);

        // Check if the book exists
        $bookCheck = $this->pm->run("SELECT ID FROM Books WHERE ID = :book_id", [':book_id' => $this->book_id]);

        if (empty($userCheck) || empty($bookCheck)) {
            return ['success' => false, 'message' => 'User or Book not found.'];
        }

        // Prepare parameters
        $params = array(
            ':User_id' => $this->User_id,
            ':book_id' => $this->book_id,
            ':BorrowStatus' => $this->BorrowStatus,
            ':BorrowDate' => $this->BorrowDate ?? date('Y-m-d H:i:s'),
            ':DueDate' => $this->DueDate ?? date('Y-m-d H:i:s', strtotime('+30 days')),
            ':ReturnDate' => $this->ReturnDate ?? null,
        );

        // Execute the query
        $result = $this->pm->run(
            "INSERT INTO borrowedbooks(UserID, BookID, BorrowDate, BorrowStatus, Duedate, ReturnDate) 
             VALUES(:User_id, :book_id, :BorrowDate, :BorrowStatus, :DueDate, :ReturnDate)",
            $params
        );

        // Check if the insertion was successful
        if ($result > 0) {
            return ['success' => true, 'message' => 'Record added successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to add record. No rows affected.'];
        }
    } catch (PDOException $e) {
        // Handle database errors
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    } catch (Exception $e) {
        // Handle validation or other errors
        return ['success' => false, 'message' => $e->getMessage()];
    }
}




    protected function updateRec()
    {
        $params = array(
            ':ReturnDate' => $this->ReturnDate,
            ':BorrowStatus' => $this->BorrowStatus,
            ':id' => $this->id
        );

        return $this->pm->run(
            "UPDATE borrowedbooks
            SET 
                ReturnDate = :ReturnDate,
                BorrowStatus = :BorrowStatus
            WHERE id = :id",
            $params
        );
    }
    public function getBorrowStatusById($id)
{
    $param = array(':id' => $id);
    return $this->pm->run(
        "SELECT borrowedstatus 
         FROM borrowedbooks
         WHERE id = :id",
        $param,
        true
    );
    }
    public function getAllBorrowedBooksWithStatus()
{
    return $this->pm->run(
        "SELECT bb.id AS borrowed_id, u.Username AS user_name, b.Title AS book_name, bb.borrowedstatus
         FROM borrowedbooks AS bb
         INNER JOIN Users AS u ON bb.User_id = u.UserID
         INNER JOIN Books AS b ON bb.BookID = b.BookID
         ORDER BY bb.id DESC"
    );
}

public function getBorrowStatusByUserId($User_id)
{
    $param = array(':User_id' => $User_id);
    return $this->pm->run(
        "SELECT bb.id AS borrowed_id, b.Title AS book_name, bb.borrowedstatus
         FROM borrowedbooks AS bb
         INNER JOIN Books AS b ON bb.book_id = b.BookID
         WHERE bb.User_id = :User_id
         ORDER BY bb.id DESC",
        $param
    );
}

 public function updateBookStatus()
    {
        $query = "UPDATE borrowedbooks
        SET 
            BorrowStatus = 
                CASE 
                    WHEN ReturnDate IS NOT NULL AND ReturnDate > DueDate THEN 'returned'
                    WHEN ReturnDate IS NOT NULL AND ReturnDate <= DueDate THEN 'returned'
                    WHEN DueDate < CURDATE() AND ReturnDate IS NULL THEN 'due time over'
                    WHEN DueDate >= CURDATE() AND ReturnDate IS NULL THEN 'borrowed'
                    ELSE BorrowStatus
                END
        WHERE BorrowStatus IN ('borrowed', 'due time over');";

        $this->pm->run($query);
    }

    public function getAllWithBookAndUser()
    {
        return $this->pm->run(
            "SELECT bb.*, u.Username AS user_name, b.Title AS book_name 
            FROM borrowedbooks AS bb
            INNER JOIN Users AS u ON bb.UserID = u.ID
            INNER JOIN Books AS b ON bb.BookID = b.ID
            ORDER BY bb.ID DESC"
        );
    }

    public function getAllWithBookAndUserByUserId($User_id)
    {
        $param = array(':User_id' => $User_id);
        return $this->pm->run(
            "SELECT bb.*, u.Username AS user_name, b.Title AS book_name 
            FROM borrowedbooks AS bb
            INNER JOIN Users AS u ON bb.UserID = u.ID
            INNER JOIN Books AS b ON bb.BookID = b.ID
            WHERE bb.UserID = :User_id
            ORDER BY bb.ID DESC",
            $param
        );
    }

    public function add_borrowed_book($User_id, $Book_id, $BorrowStatus, $BorrowDate, $DueDate, $ReturnDate)
    {
        $Borrowed_Books = new Borrowed_Books();
        $Borrowed_Books->User_id = $User_id;
        $Borrowed_Books->book_id = $Book_id;
        $Borrowed_Books->BorrowStatus = $BorrowStatus;
        $Borrowed_Books->BorrowDate = $BorrowDate;
        $Borrowed_Books->DueDate = $DueDate;
        $Borrowed_Books->ReturnDate = $ReturnDate;
        $Borrowed_Books->addNewRec();

        if ($Borrowed_Books) {
            return $Borrowed_Books; // Borrowed_books created successfully
        } else {
            return false; // Borrowed_books creation failed
        }
    }

    public function borrowBook($bookId)
    {
        $query = "SELECT ID, Quantity FROM books WHERE ID = :bookId";

        $book = $this->pm->run($query, [':bookId' => $bookId], true);

        if (!$book || $book['Quantity'] <= 0) {
            return "Book All Borrowed";
        }

        $this->pm->run("UPDATE books SET Quantity = Quantity - 1 WHERE ID = :bookId", [':bookId' => $bookId]);
        return "Book borrowed successfully!";
    }

  

    public function getPendingRequests() {
        $sql = "SELECT br.ID, u.Username AS member_name, b.Title AS book_name, br.RequestStatus, br.RequestDate 
                FROM bookrequests AS br
                INNER JOIN Users AS u ON br.UserID = u.ID
                INNER JOIN Books AS b ON br.BookID = b.ID
                WHERE br.RequestStatus = 'Pending'
                ORDER BY br.RequestDate DESC";
        return $this->pm->run($sql);
    }
    
    public function getRequestsByUser($userId) {
        $sql = "SELECT br.ID, b.Title AS book_name, br.RequestStatus, br.RequestDate 
                FROM bookrequests AS br
                INNER JOIN Books AS b ON br.BookID = b.ID
                WHERE br.UserID = :userId
                ORDER BY br.RequestDate DESC";
        $params = [':userId' => $userId];
        return $this->pm->run($sql, $params);
    }

    public function checkExistingRequest($userId, $bookId) {
        $sql = "SELECT ID 
                FROM bookrequests 
                WHERE UserID = :userId AND BookID = :bookId AND RequestStatus = 'pending'";
        $params = [
            ':userId' => $userId,
            ':bookId' => $bookId
        ];
    
        // Run the query and check if any record exists
        $result = $this->pm->run($sql, $params);
    
        // If the result is not empty, an existing request exists
        return !empty($result);
    }
    

    public function addBookRequest($userId, $bookId)
    {
        $sql = "INSERT INTO BookRequests (UserID, BookID, RequestDate, RequestStatus) 
                VALUES (:userId, :bookId, CURRENT_DATE, 'pending')";
    
        $params = [
            ':userId' => $userId,
            ':bookId' => $bookId,
        ];
    
        try {
            $stmt = $this->pm->run($sql, $params);
    
            // Check if rows were actually inserted
            if ($stmt && $stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }
    public function processRequest($requestId, $action)
{
    try {
        $this->pm->beginTransaction(); // Start transaction

        // Update the request status
        $sql = "UPDATE bookrequests 
                SET RequestStatus = :status 
                WHERE ID = :requestId AND RequestStatus = 'Pending'";
        $params = [':status' => ucfirst($action), ':requestId' => $requestId];
        $this->pm->run($sql, $params);

        if ($action === 'approve') {
            $requestDetails = $this->pm->run(
                "SELECT UserID, BookID FROM bookrequests WHERE ID = :requestId",
                [':requestId' => $requestId],
                true
            );

            if (!empty($requestDetails)) {
                $borrowDate = date('Y-m-d');
                $dueDate = date('Y-m-d', strtotime('+7 days'));
                $insertSql = "INSERT INTO borrowedbooks (UserID, BookID, BorrowDate, DueDate, BorrowStatus) 
                              VALUES (:userId, :bookId, :borrowDate, :dueDate, 'Borrowed')";
                $insertParams = [
                    ':userId' => $requestDetails['UserID'],
                    ':bookId' => $requestDetails['BookID'],
                    ':borrowDate' => $borrowDate,
                    ':dueDate' => $dueDate
                ];

                $this->pm->run($insertSql, $insertParams);
            }
        }

        $this->pm->commit(); // Commit transaction
        return ['success' => true, 'message' => 'Request ' . ucfirst($action) . 'd successfully!'];
    } catch (PDOException $e) {
        $this->pm->rollBack(); // Rollback transaction on error
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}
   public function getAllWithBookAndMember()
{
    return $this->pm->run("SELECT bb.*, u.Username AS user_name, b.Title AS book_name, bb.BorrowStatus
                           FROM borrowedbooks AS bb
                           INNER JOIN Users AS u ON bb.UserID = u.ID
                           INNER JOIN Books AS b ON bb.BookID = b.ID
                           ORDER BY bb.ID DESC");
}

// Method to insert fine details into Fines table
public function updateReturnDateAndFine($borrowId, $paidDate, $returnedAt,$fineAmount, $Paid_status,$fine_reason) {
    try {
        // Update the return date and fine details in the borrowed_books table
        $sql = "UPDATE borrowedbooks 
                SET ReturnDate = :returnedAt, FineAmount = :fineAmount, PaidStatus = :Paid_status, PaidDate = :paidDate,FineStatus = :fine_reason,
                 BorrowStatus = 'Returned'
                WHERE ID = :borrowId";
        $params = [
            ':returnedAt' => $returnedAt,
            ':fine_reason' => $fine_reason,
            ':fineAmount' => $fineAmount,
            ':Paid_status' => $Paid_status,
            ':paidDate' => $paidDate,
            ':borrowId' => $borrowId
        ];
        $this->pm->run($sql, $params); // Execute the update query

        return [
            'success' => true,
            'message' => 'Return date and fine details updated successfully!'
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}
public function updateFineStatus($id, $fineStatus, $updatedAt) {
    try {
        // Log parameters for debugging
        error_log("Updating fine status: ID = $id, FineStatus = $fineStatus, UpdatedAt = $updatedAt");

        // Prepare and run the update query
        $query = "UPDATE borrowedbooks SET FineStatus = :fineStatus, PaidDate = :updatedAt WHERE ID = :id";
        $params = [
            ':fineStatus' => $fineStatus,
            ':updatedAt' => $updatedAt,
            ':id' => $id
        ];

        // Check if the query executes successfully
        $result = $this->pm->run($query, $params);

        if ($result) {
            return true;
        } else {
            // Log if the query fails
            error_log("Failed to execute query. Query: $query");
            return false;
        }
    } catch (PDOException $e) {
        // Log any exceptions
        error_log("PDO Exception: " . $e->getMessage());
        return false;
    }
}

public function insertFine($borrowId, $fineAmount, $fineStatus) {
    try {
        // Insert fine details into borrowed_books table if not already inserted
        $sql = "INSERT INTO borrowedbooks (ID, FineAmount, FineStatus, PaidStatus, BorrowStatus)
                VALUES (:borrowId, :fineAmount, :fineStatus, 'Unpaid', 'Borrowed')";
        $params = [
            ':borrowId' => $borrowId,
            ':fineAmount' => $fineAmount,
            ':fineStatus' => $fineStatus
        ];
        $this->pm->run($sql, $params); // Execute the insert query

        return [
            'success' => true,
            'message' => 'Fine applied successfully!'
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}

public function getByBorrowId($borrowId) {
    try {
        // Get the borrowed book details along with related user and book information
        $sql = "SELECT bb.*, u.Username AS user_name, b.Title AS book_name
                FROM borrowedbooks AS bb
                JOIN Users AS u ON u.ID = bb.UserID
                JOIN Books AS b ON b.ID = bb.BookID
                WHERE bb.ID = :borrowId";
        $params = [':borrowId' => $borrowId];
        return $this->pm->run($sql, $params, true); // Fetch the record
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}
public function getBorrowedBooksWithFines() {
        try {
            // SQL query to fetch borrowed book details and related fine details, including PaidStatus
            $sql = "SELECT 
                        bb.ID AS borrow_id,
                        bb.BorrowDate, 
                        bb.DueDate, 
                        bb.ReturnDate, 
                        bb.FineAmount, 
                        bb.FineStatus, 
                        bb.PaidStatus, 
                        bb.PaidDate, 
                        u.Username AS user_name, 
                        b.Title AS book_name
                    FROM borrowedbooks AS bb
                    JOIN Users AS u ON bb.UserID = u.ID
                    JOIN Books AS b ON bb.BookID = b.ID
                    ORDER BY bb.BorrowDate DESC"; // Ordering by borrow date

            // Execute the query and return the result
            return $this->pm->run($sql);
        } catch (PDOException $e) {
            // Return error message if the query fails
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
public function getBorrowedBooksWithFinesByUser($userId) {
    try {
        // SQL query to fetch borrowed book and fine details for a specific user
        $sql = "SELECT 
                    bb.ID AS borrow_id,
                    bb.BorrowDate, 
                    bb.DueDate, 
                    bb.ReturnDate, 
                    bb.FineAmount, 
                    bb.FineStatus, 
                    bb.PaidStatus, 
                    bb.PaidDate, 
                    u.Username AS user_name, 
                    b.Title AS book_name
                FROM borrowedbooks AS bb
                JOIN Users AS u ON bb.UserID = u.ID
                JOIN Books AS b ON bb.BookID = b.ID
                WHERE u.ID = :userId
                ORDER BY bb.BorrowDate DESC"; // Ordering by borrow date

        // Execute the query with the provided user ID
        $params = [':userId' => $userId];
        return $this->pm->run($sql, $params);
    } catch (PDOException $e) {
        // Return error message if the query fails
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

   public function  user_dropdown()
   {
       return $this->pm->run("SELECT ID ,Username FROM users WHERE role='member';");
   }
   // book dropdown
   public function  book_dropdown()
   {
       return $this->pm->run("SELECT ID ,Title FROM books ;");
   }


}

