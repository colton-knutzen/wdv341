
<?php
require '../dbConnect.php';

$ingredient_name = $_GET['ingredient_name'];

$sql = "SELECT ingredient_description FROM hoyomeals_ingredients WHERE ingredient_name = :ingredient_name";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':ingredient_name', $ingredient_name, PDO::PARAM_STR);
$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    echo $result['ingredient_description'];
} else {
    echo ''; //Display blank when ingredient isn't found
}
?>

