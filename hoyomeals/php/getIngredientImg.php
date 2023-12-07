<?php
//Used in the Add Recipe Form as an XMLHttpRequest. It grabs the Blob data image from the database and displays it on the page next to the dropdown when an ingredient is selected. 
require '../dbConnect.php';

$ingredient_name = $_GET['ingredient_name'];

$sql = "SELECT ingredient_img FROM hoyomeals_ingredients WHERE ingredient_name = :ingredient_name";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':ingredient_name', $ingredient_name, PDO::PARAM_STR);
$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    //base64_encode used because image is stored as a blob datatype
    echo base64_encode($result['ingredient_img']); 
} 
//Displays a blank space if ingredient isn't found on the database. Which should never trigger, since the dropdown is populated by the contents of the ingredient database.
else { 
    echo '';
}
?>