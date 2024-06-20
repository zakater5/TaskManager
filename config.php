<?php

// Database configuration
$host = 'localhost';
$dbname = 'my_database'; // Use a more specific database name
$username = 'root';
$password = '';

// PDO connection to MySQL server
try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password); // Create a new PDO instance
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`"); // Properly escape the database name

    // Use the newly created database
    $pdo->exec("USE `$dbname`"); // Properly escape the database name

    // Create the users table if it doesn't exist
    $createUsersTable = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    )";
    $pdo->exec($createUsersTable);

    // Create the tasks table if it doesn't exist
    $createTasksTable = "
    CREATE TABLE IF NOT EXISTS tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        task VARCHAR(255) NOT NULL,
        is_completed BOOLEAN DEFAULT FALSE,
        priority INT DEFAULT 1,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $pdo->exec($createTasksTable);

    // Dodajanje uporabnika admin s geslol password kot default uporabnika
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
    $stmt->execute(['username' => 'admin']);
    $adminExists = $stmt->fetchColumn();

    // Ce ne obstaja ga lahko dodamo
    if ($adminExists == 0) {
        $password = 'password'; // Plain-text password
        $insertAdmin = "
        INSERT INTO users (username, password)
        VALUES ('admin', :password)";
        $stmt = $pdo->prepare($insertAdmin);
        $stmt->execute(['password' => $password]);
    }

} catch (PDOException $e) { // Catch and display any errors
    die("Connection failed: " . $e->getMessage());
}

return $pdo; // Return the PDO instance
?>
