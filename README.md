# Basic PHP User Authentication System

## Requirements
- PHP 
- MySQL
- XAMPP for local environment

## Setup Instructions
Step 1: Clone the repository.
Step 2: Database Setup
    <!-- create database and table using below query -->
        CREATE DATABASE IF NOT EXISTS user_auth;
        USE user_auth;

        CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            remember_token VARCHAR(255) DEFAULT NULL,
            created_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
Step 3: Configuration:
        * Open config.php and db.php, then set your MySQL credentials (host, username, password, and database name).
        * CSRF Protection: Include csrf_token.php to handle CSRF token generation and validation.
Step 4: Start the xampp server
Step 5: Access `register.php` to create a new user(http://localhost/UserAuthenticationSystem/register.php), then log in at `login.php`.



## Code Structure:
    1.db.php: Database connection configuration.
    2.config.php: Set your MySQL credentials
    3.csrf_token.php: CSRF token generation and validation.
    4.error_handler.php: Configures global error handling for logging and error display.
    5.login.php: Handles user login, CSRF validation, password verification, and "Remember Me" functionality.
    6.register.php: Handles new user registration with password hashing.
    7.error_modal.php: Displays error messages in an accessible modal window.
    8.welcome.php: Page redirected to after successful login.
    9.styles.css:  Contains all CSS for the application.
    10.logout.php: log out a user by clearing their session and cookies.

## Security
- Passwords are hashed with `password_hash`.
- CSRF protection is enabled on forms.
- SQL Injection is prevented by escaping user inputs.

## Features
- Basic Authentication: Supports user registration, login, and logout.
- Secure "Remember Me": Optionally remembers users for easier login.
