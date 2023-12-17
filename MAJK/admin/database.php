<?php
    $servername = "localhost"; // Replace with your MySQL server name
    $username = "root"; // Replace with your MySQL username
    $password = ""; // Replace with your MySQL password

    // Create connection
    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create the database
    $sql = "CREATE DATABASE IF NOT EXISTS majk_db";
    if (!($conn->query($sql))) {
        echo "Error creating database: " . $conn->error;
    }

    $conn->select_db("majk_db");


    

    $sql_employee = "CREATE TABLE IF NOT EXISTS `employee` (
        `employee_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `firstname` varchar(50) NOT NULL,
        `middlename` varchar(20) NOT NULL,
        `lastname` varchar(50) NOT NULL,
        `position` varchar(100) NOT NULL
      )";

    if (!($conn->query($sql_employee))) {
        echo "Error creating table: " . $conn->error;
    };

    $sql_users = "CREATE TABLE IF NOT EXISTS `users` (
        `id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `username` varchar(30) NOT NULL,
        `password` varchar(100) NOT NULL,
        `firstname` varchar(100) NOT NULL,
        `lastname` varchar(100) NOT NULL
      )";

    if (!($conn->query($sql_users))) {
        echo "Error creating table: " . $conn->error;
    };

    $sql_insertUser = "REPLACE INTO `users`(
        `id`, `username`, `password`, `firstname`, `lastname`) 
        VALUES (1, 'admin', 'admin', 'Admin', 'admin')";

    if (!($conn->query($sql_insertUser))) {
        echo "Error inserting user: " . $conn->error;
    };

    
    $sql_attendance = "CREATE TABLE IF NOT EXISTS attendance (
        atlog_id INT(11) AUTO_INCREMENT PRIMARY KEY,
        employee_id INT(11) NOT NULL,
        atlog_date DATE NOT NULL,
        am_in TIME,
        am_out TIME,
        pm_in TIME,
        pm_out TIME,
        am_late VARCHAR(10),
        am_undertime VARCHAR(10),
        pm_late VARCHAR(10),
        pm_undertime VARCHAR(10),
        FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
    )";
    
    if (!($conn->query($sql_attendance))) {
        echo "Error creating table: " . $conn->error;
    }

   

?>