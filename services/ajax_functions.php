<?php
session_start();
if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
}

require_once '../config.php';
require_once '../helpers/AppManager.php';
require_once '../models/Users.php';
require_once '../models/Books.php';
require_once '../models/Borrowed_books.php';
$target_dir = "../assets/uploads/";

// Create user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_user') {

    try {
        $Username = $_POST['user_name'];
        $FullName = $_POST['full_name'];
        $Email = $_POST['email'];
        $Password = $_POST['password'];
        $Role = $_POST['role'];

        if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
            exit;
        }

        $userModel = new User();
        $created =  $userModel->createUser($Username, $Password, $Role, $Email, $FullName);
        if ($created) {
            echo json_encode(['success' => true, 'message' => "User created successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create user. May be user already exist!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
// Get user by id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id']) && isset($_GET['action']) &&  $_GET['action'] == 'get_user') {

    try {
        $user_id = $_GET['user_id'];
        $userModel = new User();
        $user = $userModel->getUserWithId($user_id);
        if ($user) {
            echo json_encode(['success' => true, 'message' => "User created successfully!", 'data' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create user. May be user already exist!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
//delete user

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['user_id']) && isset($_GET['action']) && $_GET['action'] == 'delete_user') {
    try {
        $id = $_GET['user_id'];

        $userModel = new user();
        // Proceed to delete the Members if doctor deletion was successful or not needed
        $userDeleted = $userModel->deleteUser($id);

        if ($userDeleted) {
            echo json_encode(['success' => true, 'message' => 'Members deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete Members.']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

//update user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_user') {
    try {
        // Sanitize inputs
        $Username = $_POST['user_name'] ?? '';
        $FullName = $_POST['full_name'] ?? '';
        $Email = $_POST['email'] ?? '';
        $Role = $_POST['role'] ?? '';
        $id = $_POST['id'] ?? '';

        // Validate inputs
        if (empty($Username) || empty($Email) || empty($Role) || empty($FullName)) {
            echo json_encode(['success' => false, 'message' => 'Required fields are missing!']);
            exit;
        }

        if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address!']);
            exit;
        }

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID!']);
            exit;
        }
        // Perform database update
        $userModel = new User();
        $updated = $userModel->updateUser($id, $Username, $Role, $Email, $FullName);

        if ($updated) {
            echo json_encode(['success' => true, 'message' => "User updated successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update user. Username or email may already exist!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// create book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_book') {
    try {
        // Retrieve and validate form data
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $CategoryID = trim($_POST['CategoryID']);
        $isbn = trim($_POST['isbn']);
        $quantity = intval($_POST['quantity']);
        // Get file information
        $image = $_FILES["image"] ?? null;
        $imageFileName = null;

        // Define target directory
        $target_dir = "../assets/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Create directory if it doesn't exist
        }

        // Check if file is uploaded
        if (isset($image) && !empty($image)) {
            // Check if there are errors
            if ($image["error"] > 0) {
                echo json_encode(['success' => false, 'message' => "Error uploading file: " . $image["error"]]);
                exit;
            } else {
                // Check if file is an image
                if (getimagesize($image["tmp_name"]) !== false) {
                    // Check file size (optional)
                    if ($image["size"] < 500000) { // 500kb limit
                        // Generate unique filename
                        $new_filename = uniqid() . "." . pathinfo($image["name"])["extension"];

                        // Move uploaded file to target directory
                        if (move_uploaded_file($image["tmp_name"], $target_dir . $new_filename)) {
                            $imageFileName = $new_filename;
                        } else {
                            echo json_encode(['success' => false, 'message' => "Error moving uploaded file."]);
                            exit;
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => "File size is too large."]);
                        exit;
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => "Uploaded file is not an image."]);
                    exit;
                }
            }
        }

        // Call the model to create the book with the file name
        $bookModel = new Books();
        $created = $bookModel->createBooks($title, $author, $CategoryID, $isbn, $quantity,  $imageFileName);

        if ($created) {
            echo json_encode(['success' => true, 'message' => "Book created successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create book. Book may already exist!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

//Get book by id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['book_id']) && isset($_GET['action']) &&  $_GET['action'] == 'get_book') {

    try {
        $book_id = $_GET['book_id'];
        $bookModel = new Books();
        $book = $bookModel->getBooksById($book_id);
        if ($book) {
            echo json_encode(['success' => true, 'message' => "Book update successfully!", 'data' => $book]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create user. May be user already exist!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

//update book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_book') {

    try {
        $Title = trim($_POST['title']);
        $Author = trim($_POST['author']);
        $CategoryID = trim($_POST['categoryid']);
        $ISBN = trim($_POST['isbn']);
        $Quantity = intval($_POST['quantity']);
        $created_at = date('Y-m-d H:i:s'); // Current timestamp
        $id = $_POST['id'];

        // die;
        // Validate inputs
        if (empty($Title) || empty($Author) || empty($CategoryID) || empty($ISBN) || empty($Quantity)) {
            echo json_encode(['success' => false, 'message' => 'Required fields are missing!']);
            exit;
        }

        $bookModel = new Books();
        $updated =  $bookModel->updateBooks($id, $Title, $Author, $CategoryID, $ISBN, $Quantity, $created_at);

        if ($updated) {
            echo json_encode(['success' => true, 'message' => "Book updated successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update Book. May be Book already exist!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// delete book
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['book_id']) && isset($_GET['action']) && $_GET['action'] == 'delete_book') {
    try {
        $book_id = $_GET['book_id'];

        $bookModel = new Books();

        $bookDeleted = $bookModel->deletebook($book_id);

        if ($bookDeleted) {
            echo json_encode(['success' => true, 'message' => 'Book deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete Book.']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}


// Check if the request method is POST and if the action and request_id are provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['request_id'])) {
    $requestId = $_POST['request_id'];
    $action = $_POST['action']; // 'approve' or 'reject'

    $bookRequestModel = new Borrowed_Books();
    // Call the processRequest method
    $response = $bookRequestModel->processRequest($requestId, $action);

    // Send JSON response back to the client
    echo json_encode($response);
    exit;
}

// Update borrowed book

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'Borrowed_Books_update') {
    try {
        // Get POST parameters
        $id = $_POST['id'] ?? "";
        $returned_at = $_POST['returned_at'] ?? date('Y-m-d H:i:s'); // Default to current date/time
        $paid_status = $_POST['Paid_status'] ?? "";
        $paid_date = $_POST['paid_date'] ?? null; // Can be null if not provided
        $fine_reason = $_POST['fine_reason'] ?? "";


        // Create an instance of the Borrowed_Books model
        $Borrowed_BooksModel = new Borrowed_Books();

        // Fetch the borrowed book data by ID
        $borrowedBookData = $Borrowed_BooksModel->getByBorrowId($id);

        if (!empty($borrowedBookData)) {
            // Calculate fine based on fine status
            $fineAmount = 0;
            if ($fine_reason === 'Late Return') {
                $fineAmount = 10; // Example fine for late return
            } elseif ($fine_reason === 'Damaged') {
                $fineAmount = 20; // Example fine for damaged book
            } elseif ($fine_reason === 'Lost') {
                $fineAmount = 50; // Example fine for lost book
            }
            // Update the return date, fine, and status in the database
            // $updateFineStatus = $Borrowed_BooksModel->updateFineStatus($id,$fine_reason,$paid_date);
            $updateSuccess = $Borrowed_BooksModel->updateReturnDateAndFine($id,$paid_date, $returned_at,$fineAmount, $paid_status,$fine_reason);
            if ($updateSuccess) {
                echo json_encode(['success' => true, 'message' => "Borrowed book updated successfully!"]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update borrowed book.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update borrowed book. Record may not exist!']);
        }
    } catch (PDOException $e) {
        // Handle database errors
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    } catch (Exception $e) {
        // Handle any other errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}



// Add borrowed book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_borrowed') {
    try {
        // Retrieve and validate form data
        $user_id = trim($_POST['user_id']);
        $book_id = $_POST['book_id'];
        $borrowedstatus = $_POST['borrow_status'];
        $borrowDate = $_POST['BorrowDate'] ?? date('Y-m-d H:i:s');
        $due_date = $_POST['due_date'] ?? date('Y-m-d H:i:s', strtotime('+30 days'));
        $returned_at = $_POST['returned_at'] ?? null;

        // Call the model to create the book
        $Borrowed_BooksModel = new Borrowed_Books();
        $message = $Borrowed_BooksModel->borrowBook($book_id);

        if ($message == "Book All Borrowed") {
            echo json_encode([
                'success' => false,
                'message' => $message,
                'borrowDate' => $borrowDate,
                'borrowStatus' => $borrowedstatus
            ]);
        } else {
            $created = $Borrowed_BooksModel->add_borrowed_book($user_id, $book_id, $borrowedstatus, $borrowDate, $due_date, $returned_at);

            if ($created) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Book Borrowed Success...',
                    'borrowDate' => $borrowDate,
                    'borrowStatus' => $borrowedstatus
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to create member. Member may already exist!',
                    'borrowDate' => $borrowDate,
                    'borrowStatus' => $borrowedstatus
                ]);
            }
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'borrowDate' => $borrowDate ?? null,
            'borrowStatus' => $borrowedstatus ?? null
        ]);
    }

    exit;
}
