<?php
include __DIR__ . '/../config.php';
include __DIR__ . '/../helpers/AppManager.php';

$pm = AppManager::getPM();
$sm = AppManager::getSM();

$email = trim($_POST['email']);
$password = trim($_POST['password']);

// Check if fields are empty
if (empty($email) || empty($password)) {
    $sm->setAttribute("error", 'Please fill all required fields!');
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// Fetch user data from database
$param = array(':email' => $email);
$user = $pm->run("SELECT * FROM users WHERE Email = :email", $param, true);

if ($user) {
    // Verify hashed password
    // if (password_verify($password, $user['Password'])) {
        // Set session attributes
        $sm->setAttribute("UserId", $user['ID']);
        $sm->setAttribute("username", $user['Username']);
        $sm->setAttribute("role", $user['Role']);

        // Redirect to dashboard
        header('Location: ../index.php');
        exit;
    } else {
        $sm->setAttribute("error", 'Invalid username or password!');
    }
//  else {
//     $sm->setAttribute("error", 'Invalid username or password!');
// }

// Redirect back to login page
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
