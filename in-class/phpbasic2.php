<?php 

$carType = "sedan";
$cars = array("Chevy", "Honday", "Acura");

echo json_encode($cars);
echo implode('/', $cars);
echo serialize($cars);

echo "Car number 1: $cars[0]";

$studentName = "Carl";

$carOne = $cars[1];

array_push($cars, "Ford");

echo "cars array after push Ford: " . implode("/", $cars); //. is concatenate. Concatenate means add or join. So it's "text" + cars array

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
    <script>
        let schoolName = "DMACC"

        <?php 
            echo "let studentName = 'Mary'";
        
        ?>


    </script>
</head>
<body>
<h1>WDV341 Intro to PHP</h1>
    <h2>PHP Basics and examples</h2>
    <h3>Welcome <?php echo "new student " ?>; </h3>

<?php 
    echo "<h3>This is an output from PHP on the server</h3>";
    echo "This is a test";
    ?>

<?php
    echo "let studentName = 'Mary';";

?>

</body>
</html>