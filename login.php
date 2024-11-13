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
    $remember_me = isset($_POST['remember_me']);

    if (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } else {
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($query);

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                if ($remember_me) {
                    $token = bin2hex(random_bytes(16));  // genrate a token 
                    $encrypted_token = openssl_encrypt($token, 'aes-256-cbc', $encryption_key, 0, $iv);

                    setcookie("remember_token", $encrypted_token, time() + (86400 * 30), "/"); 
                    $updateQuery = "UPDATE users SET remember_token = '$token' WHERE id = {$user['id']}";
                    if (!$conn->query($updateQuery)) {
                        $error = "Failed to set remember me token: " . $conn->error;
                        error_log($error);
                    }
                }
                header("Location: welcome.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Username does not exist.";
        }
    }
}
if (!empty($error)) {
    require 'error_model.php'; 
}
?>

<!-- Login page UI -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>

<div class="form-container">
    <h2>Login</h2>
    <form action="login.php" method="POST">
        <input type="hidden" name="token" value="<?= generateToken() ?>"> 
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <div class="remember-me-container">
            <label>
                <input type="checkbox" name="remember_me"> Remember Me
            </label>
        </div>
        <div class="button-container">
        <button type="submit">Submit</button>

</div>
    </form>

    <div class="register-link-container">
            <p>Don't have an account? <a href="register.php">Register here</a></p> 
    </div>



</body>
</html>
