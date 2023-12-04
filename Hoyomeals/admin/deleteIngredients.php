<?php
session_start();

//Only admins are allowed
if (!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== 'valid') {
    header('Location: login.php');
    exit();
};

$confirmMessage = false;

require '../dbConnect.php';

$sql = "SELECT ingredient_id, ingredient_name, ingredient_img, ingredient_description FROM hoyomeals_ingredients";
$stmt = $conn->prepare($sql);
$stmt->execute();
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

//Deletes the ingredient when the associated button is pressed
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ingredient_id = $_POST['ingredient_id'];

    $sql_delete = "DELETE FROM hoyomeals_ingredients WHERE ingredient_id = :ingredient_id";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bindParam(':ingredient_id', $ingredient_id);
    $stmt_delete->execute();

    $confirmMessage = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/hoyomeals/css/styles.css" rel="stylesheet" type="text/css">
    <link href="/hoyomeals/css/stylesForm.css" rel="stylesheet" type="text/css">
    <!-- jquery is used for the .load function used to import the structure of the site from an external file. -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="/hoyomeals/script/script.js"></script>

    <title>Hoyomeals: Delete Ingredients</title>
</head>

<body>
    <!-- Start of site skeleton structure -->
    <div class="grid-container">
        <div id="inc-header"></div>
        <script>
            $(function() {
                $("#inc-header").load("/hoyomeals/structure/header.html")
            });
        </script>
        <div id="inc-top-nav"></div>
        <script>
            $(function() {
                $("#inc-top-nav").load("/hoyomeals/structure/top_nav.html")
            });
        </script>
        <div id="inc-side-nav"></div>
        <script>
            $(function() {
                $("#inc-side-nav").load("/hoyomeals/structure/side_nav.html")
            });
        </script>
        <div id="inc-left-ad">
            <div class="left-ad"><img src="/hoyomeals/img/icon/left-ad1.jpg" alt=""></div>
        </div>
        <main onclick="hideSideNav()">
            <div id="head-content-ad">
                <div class="content-ad"><img src="/hoyomeals/img/icon/ad1.png" alt="header_ad"></div>
            </div>
            <div class="disclaimerHeader">
                This is an academic site.
                <br>All recipes are fictional. DO NOT ATTEMPT!
            </div>
            <!-- End of site skeleton structure -->

            <div class="main-content-div">
                <?php
                if ($confirmMessage) {
                ?>
                    <div class="confirmMessage">
                        <h2>Thank you. The Ingredient has been Deleted.</h2>
                    </div>
                <?php
                } else {
                ?>
                    <div class="page-title">
                        <h1 style="text-align: center;">Delete Ingredients</h1>
                    </div>
                    <?php foreach ($ingredients as $ingredient) : ?>
                        <div class="ingredient_delete_grid dbTableDisplay">

                            <!-- ID -->
                            <div class="ID grid_title gridBorder_top_left_right">ID</div>
                            <div class="id_data grid_content gridBorder_bottom_left_right"><?= $ingredient['ingredient_id'] ?></div>

                            <!-- Name -->
                            <div class="Name grid_title gridBorder_top_right">Name</div>
                            <div class="name_data grid_content gridBorder_bottom_right"><?= $ingredient['ingredient_name'] ?></div>

                            <!-- Image -->
                            <div class="Image grid_title gridBorder_top_right">Image</div>
                            <div class="image_data grid_content gridBorder_bottom_right"><img src="data:image/jpeg;base64,<?= base64_encode($ingredient['ingredient_img']) ?>" alt="Recipe Image"></div>

                            <!-- Description -->
                            <div class="Description grid_title gridBorder_top_right">Description</div>
                            <div class="description_data grid_content gridBorder_bottom_right"><?= $ingredient['ingredient_description'] ?></div>

                            <!-- Delete Button -->
                            <div class="delete_button button_style">
                                <form action="deleteIngredients.php" method="post" onsubmit="return confirm('Are you sure you want to DELETE this INGREDIENT from the database? IT WILL BE GONE FOREVER!');">
                                    <input type="hidden" name="ingredient_id" value="<?= $ingredient['ingredient_id'] ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php
                }
                ?>
            </div>

            <!-- Start of site skeleton structure -->
            <div id="tail-content-ad">
                <div class="content-ad"><img src="/hoyomeals/img/icon/ad2.jpg" alt="tail_ad"></div>
            </div>
        </main>
        <div id="inc-right-ad">
            <div class="right-ad"><img src="/hoyomeals/img/icon/right-ad1.jpg" alt="right_ad"></div>
        </div>
        <div id="inc-footer"></div>
        <script>
            $(function() {
                $("#inc-footer").load("/hoyomeals/structure/footer.html")
            });
        </script>
    </div>
    <!-- End of site skeleton structure -->
</body>

</html>