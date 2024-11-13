<?php
session_start();
require 'db.php'; 
require 'csrf_token.php'; 
require 'error_handler.php'; // global error handling configuration


$error = ""; 

// Enable MySQL error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['token']) || !validateToken($_POST['token'])) {
        die("CSRF token validation failed");
    }

    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Input validation
    if (!preg_match("/^[A-Za-z]+$/", $username) || strlen($username) < 3) {
        $error = "Username should only contain alphabetic characters (a-z, A-Z).";
    }else if (strlen($password) < 6 || 
    !preg_match("/[a-z]/", $password) || 
    !preg_match("/[A-Z]/", $password) || 
    !preg_match("/[0-9]/", $password) || 
    !preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $password)) {
    $error = "Password must be at least 6 characters long, contain at least one lowercase letter, one uppercase letter, one number, and one special character.";
    }else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }else {        
        // check user already exist or not
        $query = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username/Email already taken, please choose another.";
        } else {
            // if user already not exist, will intsert new users  
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param('sss', $username, $hashed_password, $email);

            try {
                if ($stmt->execute()) {
                    header("Location: login.php");
                    exit();
                } else {
                    // If execution fails, print the error code and message
                    $error =("Error executing query: " . $stmt->erro);
                    error_log($error);
                }
            } catch (mysqli_sql_exception $e) {
                $error = ("Error executing query: " . $e->getMessage());
                error_log($error);
            }
        }
    }
}
if (!empty($error)) {
    require 'error_model.php'; 
}

?>

<!-- Register page UI -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>

<div class="form-container">
    <h2>Register</h2>
    <form action="register.php" method="POST">
        <input type="hidden" name="token" value="<?= generateToken() ?>"> 
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="email" name="email" placeholder="Email" required>
        <div class="button-container">
        <button type="submit">Register</button>
</div>
    </form>
</div>
</body>
</html> 
