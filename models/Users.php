<?php

require_once 'BaseModel.php';

class User extends BaseModel
{
    public $Username;
    public $FullName;
    public $Role;
    private $Email;
    private $Password;
    

    function getTableName()
    {
        return 'users';
    }

    protected function addNewRec()
    {
        // Hash the Password before storing it
        $this->Password = Password_hash($this->Password, PASSWORD_DEFAULT);

        $param = array(
            ':Username' => $this->Username,
            ':Password' => $this->Password,
            ':Role' => $this->Role,
            ':Email' => $this->Email,
            ':FullName' => $this->FullName,
            );

        return $this->pm->run("INSERT INTO " . $this->getTableName() . "(Username, Password,Role,Email,FullName) values(:Username, :Password,:Role,:Email,:FullName)", $param);
    }

    protected function updateRec()
    {
        // Check if the new Username or Email already exists (excluding the current user's record)
        $existingUser = $this->getUserByUsernameOrEmailWithId($this->Username, $this->Email, $this->id);
        if ($existingUser) {
            // Handle the error (return an appropriate message or throw an exception)
            return false; // Or throw an exception with a specific error message
        }

        // Hash the Password if it is being updated
        if (!empty($this->Password)) {
            $this->Password = Password_hash($this->Password, PASSWORD_DEFAULT);
        }

        $param = array(
            ':Username' => $this->Username,
            ':Password' => $this->Password,
            ':Role' => $this->Role,
            ':Email' => $this->Email,
            ':FullName' => $this->FullName,
            ':id' => $this->id
        );
        return $this->pm->run(
            "UPDATE " . $this->getTableName() . " 
            SET 
                Username = :Username, 
                Password = :Password,
                Role = :Role,  
                Email = :Email,
                FullName = :FullName
            WHERE ID = :id",
            $param
        );
    }

    public function getUserByUsernameOrEmailWithId($Username, $Email, $excludeUserId = null)
    {
        // Validate inputs
        if (empty($Username) && empty($Email)) {
            return false; // No input to search for
        }
    
        $param = array(':Username' => $Username, ':Email' => $Email);
    
        // Build query
        $query = "SELECT ID, Username, Email FROM " . $this->getTableName() . " 
                  WHERE (Username = :Username OR Email = :Email)";
    
        if ($excludeUserId !== null) {
            $query .= " AND ID != :excludeUserId";
            $param[':excludeUserId'] = $excludeUserId;
        }
    
        // Execute query with error handling
        try {
            $result = $this->pm->run($query, $param);
        } catch (Exception $e) {
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    
        return $result; // Return the user if found, or false if not
    }
    

    public function getUserByUsernameOrEmail($Username, $Email)
    {
        $param = array(
            ':Username' => $Username,
            ':Email' => $Email
        );

        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE Username = :Username OR Email = :Email";
        $result = $this->pm->run($sql, $param);

        if (!empty($result)) {  // Check if the array is not empty
            $user = $result[0]; // Assuming the first row contains the user data
            return $user;
        } else {
            return null;
        }
    }


    function createUser($Username, $Password, $Role, $Email, $FullName)
    {
        $userModel = new User();

        // Check if Username or email already exists
        $existingUser = $userModel->getUserByUsernameOrEmail($Username, $Email);
        if ($existingUser) {
            // Handle the error (return an appropriate message or throw an exception)
            return false; // Or throw an exception with a specific error message
        }

        $user = new User();
        $user-> FullName= $FullName;
        $user->Username = $Username;
        $user->Password = $Password;
        $user->Role = $Role;
        $user->Email = $Email;
        $user->addNewRec();

        if ($user) {
            return $user; // User created successfully
        } else {
            return false; // User creation failed (likely due to database error)
        }
    }

    function updateUser($id, $Username, $Role, $Email,$FullName)
    {
        $userModel = new User();

        // Check if Username or Email already exists
        $existingUser = $userModel->getUserByUsernameOrEmailWithId($Username, $Email, $id);
        if ($existingUser) {
            // Handle the error (return an appropriate message or throw an exception)
            return false; // Or throw an exception with a specific error message
        }

        $user = new User();
        $user->id = $id;
        $user->Username = $Username;
        $user->Role = $Role;
        $user->Email = $Email;
        $user-> FullName= $FullName;
        $user->updateRec();

        if ($user) {
            return true; // User udapted successfully
        } else {
            return false; // User update failed (likely due to database error)
        }
    }

    function deleteUser($id)
    {
        $user = new User();
        $user->deleteRec($id);

        if ($user) {
            return true; // User udapted successfully
        } else {
            return false; // User update failed (likely due to database error)
        }
    }

    public function getLastInsertedUserId()
    {
        $result = $this->pm->run('SELECT MAX(id) as lastInsertedId FROM users', null, true);
        return $result['lastInsertedId'] ?? 100;
    }
    public function getUserWithId($id)
    {
        // Validate input
        if ($id <= 0) {
            error_log("Invalid user ID provided: $id");
            return false; // Invalid user ID
        }
    
        $param = array(':id' => $id);
    
        try {
            // Fetch specific columns
            $result = $this->pm->run("
                SELECT * FROM " . $this->getTableName() . " 
                WHERE ID = :id
            ", $param, true);
    
            if (!$result) {
                error_log("No user found with ID $id");
            }
    
            return $result;
        } catch (PDOException $e) {
            // Handle database errors
            error_log("Error fetching user with ID $id: " . $e->getMessage());
            return false;
        }
    }
    
}

