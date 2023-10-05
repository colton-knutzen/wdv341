<?php 
//database connection work flow. Every time
// 1. Connect to the database 
// 2. Create your SQL command 
// 3. Prepare your Statement PDO Prepared Statements 
// 4. Bind any parameters as needed 
// 5. Execute your SQL command/prepared statement 
// 6. Process your result-set/object

//include an external PHP file into this file
    // include
    // require

//1. link dbConnect
require '../dbConnect.php';


//2. create the SQL command
$sql = "SELECT events_name FROM wdv341_events WHERE events_id=:recId";

//3. prepare out statement object PDO Prepared Statements
$stmt = $conn->prepare($sql); // -> is used instead of a . for object->property or object method


//4. Parameter - this is called a named parameter
$recID = 3;
$stmt = $conn->bindParam(':recID', $recID);

//5. Execute the statement
$stmt->execute(); //runs the prepared statement, stores the results within the statement object


//6. 
$stmt->setFetchMode(PDO::FETCH_ASSOC); //setting ALL fetch commands to return associative arrays

//$row = $stmt->fetch(); //should get the first row from the result set within the statement
//echo $row["events_name"];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<h1>WDV341 Intro to PHP</h1>
<h2>Unit 7: Select data from Events table</h2>
<h3>Event Names</h3>
<?php 
    while ($row = $stmt->fetch()){
        echo "<p>";
        echo $row["events_name"];
        echo "</p>";
    }
?>    
</body>
</html>