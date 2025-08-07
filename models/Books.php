<?php
require_once 'BaseModel.php';

class Books extends BaseModel
{
    public $Title;
    public $Author;
    public $CategoryID;
    public $ISBN;
    public $Quantity;
    public $CreatedAt;
    public $photo;
   

    protected function getTableName()
    {
        return "books";
    }
    protected function addNewRec()
    {
        // Validate the title to ensure it is unique
        if ($this->isTitleDuplicate($this->Title)) {
            throw new Exception("The title '{$this->Title}' already exists. Please choose a different title.");
        }
    
        // Prepare parameters
        $param = array(
            ':Title' => $this->Title,
            ':Author' => $this->Author,
            ':CategoryID' => $this->CategoryID,
            ':ISBN' => $this->ISBN,
            ':Quantity' => $this->Quantity,
            ':CreatedAt' => $this->CreatedAt,
            ':photo' => $this->photo
        );
    
        // Insert record
        try {
            $result = $this->pm->run(
                "INSERT INTO books (Title, Author, CategoryID, ISBN, Quantity, CreatedAt, photo) 
                 VALUES (:Title, :Author, :CategoryID, :ISBN, :Quantity, :CreatedAt, :photo)",
                $param
            );
    
            if ($result) {
                return "Record added successfully!";
            } else {
                throw new Exception("Failed to add the record.");
            }
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
    
    /**
     * Checks if the given title already exists in the database.
     *
     * @param string $title The title to check for duplicates.
     * @return bool True if the title exists, false otherwise.
     */
    private function isTitleDuplicate($title)
    {
        $query = "SELECT COUNT(*) as count FROM books WHERE Title = :Title";
        $param = array(':Title' => $title);
    
        $result = $this->pm->run($query, $param);
    
        // Assuming $this->pm->run() returns an associative array for SELECT queries
        return $result[0]['count'] > 0;
    }
    

    protected function updateRec()
    {
        $param = array(
            ':Title' => $this->Title,
            ':Author' => $this->Author,
            ':CategoryID' => $this->CategoryID,
            ':ISBN' => $this->ISBN,
            ':Quantity' => $this->Quantity,
            ':CreatedAt' => $this->CreatedAt,
            ':ID' => $this->id
        );

        return $this->pm->run("UPDATE books SET Title = :Title, Author = :Author, CategoryID = :CategoryID, ISBN = :ISBN, Quantity = :Quantity,CreatedAt = :CreatedAt WHERE ID = :ID", $param);
    }


    function createBooks($Title, $Author, $CategoryID, $ISBN , $Quantity,$photo )
    {
        $booksModel = new Books();
        $booksModel->Title = $Title;
        $booksModel->Author = $Author;
        $booksModel->CategoryID = $CategoryID;
        $booksModel->Quantity = $Quantity;
        $booksModel->ISBN = $ISBN;
        $booksModel->photo = $photo;
        $booksModel->save();

        if ($booksModel) {
            return true; // Books created successfully
        } else {
            return false; // Books creation failed (likely due to database error)
        }
    }

    function updateBooks($id, $Title, $Author, $CategoryID, $ISBN , $Quantity , $CreatedAt)
    {
        // Initialize the Books model
        $booksModel = new Books();

        // Retrieve the books by ID
        $existingBooks = $booksModel->getBooksById($id); // Assuming findById method exists

        if (!$existingBooks) {
            // Handle the error (return an appropriate message or throw an exception)
            return false; // Or throw an exception with a specific error message
        }

        $Books = new Books();
        $Books->id = $id;
        $Books->Title = $Title;
        $Books->Author = $Author;
        $Books->CategoryID = $CategoryID;
        $Books->ISBN = $ISBN;
        $Books->Quantity = $Quantity;
        $Books->CreatedAt = $CreatedAt;
        $Books->updateRec();

        if ($Books) {
            return true; // book udapted successfully
        } else {
            return false; // book update failed (likely due to database error)
        }
    }

    function deletebook($id)
    {
        $Books = new Books();
        $Books->deleteRec($id);

        if ($Books) {
            return true; // User udapted successfully
        } else {
            return false; // User update failed (likely due to database error)
        }
    }
   public function getBooksById($id)
    {
        $param = array(':id' => $id);
        return $this->pm->run("SELECT * FROM " . $this->getTableName() . " WHERE ID = :id", $param, true);
    }
}
