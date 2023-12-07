<?php
session_start();

if (!isset($_SESSION['validUser']) || $_SESSION['validUser'] !== true) {
    $loginMessage = false;
} else {
    $loginMessage = true;
}

$invalidLogin = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    require '../../dbConnect.php';

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT event_user_name, event_user_password FROM wdv341_event_users WHERE event_user_name = :username AND event_user_password = :password";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['validUser'] = true;
        header("Location: adminPanel.php");
    } else {
        $invalidLogin = true;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            text-align: center;
        }
    </style>

</head>

<body onload="pageSetup()">
    <?php
    if ($loginMessage) {
    ?>
        You're already signed in
        <p><a href="adminPanel.php">Admin Panel</a></p>
    <?php
    } else {
    ?>
        <form method="post" action="login.php">
            <p>
                <label for="username">USERNAME</label>
                <input type="text" id="username" name="username" required>
            </p>

            <p>
                <label for="password">PASSWORD</label>
                <input type="password" id="password" name="password" required>
            </p>

            <p>
                <button type="submit" name='submit'>Login</button>
                <?php if ($invalidLogin) {
                    echo "<div style='color: red; text-align: center'>Password or Username is invalid. Please try again.</div>";
                } ?>
            </p>

            <p>
                Credentials: wdv341
            </p>
        </form>
    <?php
    }
    ?>
</body>

</html>