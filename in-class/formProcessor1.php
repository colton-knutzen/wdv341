<?php

$_POST 

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h3>Your form has been submitted. Thank you very much.</h3>
    <?php
    echo "<table border = '1'>";
    echo  "<tr><th>Field Name</th><th>Value of Field</th></tr>";

    foreach($_POST as $key => $value) {
        echo "<tr>";
        echo "<td>", $key, "</td>";
        echo "<td>", $value, "</td>";
        echo "</tr>";
    }
    ?>
    <h3>Thank you for registering for the Job Fair</h3>
    <p>Student First Name: <?php echo $_POST["firstName"]; ?></p>
    <p>Student Last Name: <?php echo $_POST["lastName"]; ?></p>
</body>

</html>