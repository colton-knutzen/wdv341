<?php
    //form processing/server side form processing
    //get the form data from the client
    // -validate form - active user, make sure this form was submitted by a person not a bot 
    // -process the form data

    //get the data from the $_POST - associative array of the name-value pairs from client form
    $firstName = $_POST["firstName"];
    $mName = $_POST['mName'];
    $lastName = $_POST['lastName'];
    $title = $_POST['inTitle'];

    /* honeypot-hidden field - a way to protect your form from bots/automated entry

        UI will have a hidden field - User cannot see it so no data should be in it
                                    - bots read ALL fields, so a bot will put data in the field
        Server - if the hidden field has content then a bot submitted the form, DO NOT PROCESSS
    */

    if( empty($title) ){
        //a real person entered the form data
        $message = "A user entered this data.";
    }
    else{
        //a bot entered this data
        $message = "A bot entered this data. Ignore the form.";
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h3>Response From Server</h3>
    <div>
        <p>
            First Name: <?php echo $firstName; ?>
        </p>
        <p>
            Middle Name: <?php echo $mName; ?>
        </p>
        <p>
            Last Name: <?php echo $lastName; ?>
        </p>
        <p>
            Title: <?php echo $message ?>
        </p>
    </div>
</body>
</html>