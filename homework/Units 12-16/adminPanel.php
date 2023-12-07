<?php
session_start();

if (!isset($_SESSION['validUser']) || $_SESSION['validUser'] !== true) {
    header('Location: login.php');
    exit();
};


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            text-align: center;
        }
    </style>
</head>

<body>
    <h2>Admin Panel</h2>
    <p><a href="logout.php">Logout</a></p>
    <p><a href="eventsForm.php">Add an Event</a></p>
    <p><a href="deleteEvents.php">Delete Events</a></p>
    <p><a href="modifyEvents.php">Modify Events</a></p>
</body>

</html>