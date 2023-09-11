<?php

$schoolName = "DMACC";

echo $schoolName;

function displaySchool(){
    global $schoolName;
    $cityName = "Ankeny";
    echo $schoolName;
    echo " Located in " . $cityName;
}

function displayCity($inCity) {
    echo $inCity;
}

function formatmmddyyyDate() {
    return date("n/j/y");
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
    <h1>WDV341 Intro PHP</h1>
    <h2>PHP Functions - Examples</h2>
    <h3>This course is offered by <?php echo $schoolName; ?></h3>
    <h3>Your course: <?php displaySchool(); ?></h3>
    <h3>The school is located in <?php displayCity("West Des Moines"); ?></h3>
    <h3>The date is <?php echo formatmmddyyyDate(); ?></h3>
    
</body>
</html>