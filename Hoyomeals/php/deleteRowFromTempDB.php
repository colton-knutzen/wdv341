<!-- Deletes the selected recipes from the temporary database. Executed when declining a recipe. -->
<?php
$confirmMessage = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Retrieves the recipe ID from the hidden input field
    $recipe_id = $_POST['recipe_id'];

    require '../dbConnect.php';

    $sql_delete = "DELETE FROM hoyomeals_recipes_temp WHERE recipe_idTemp = :recipe_id";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bindParam(':recipe_id', $recipe_id);
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
                <?php if ($confirmMessage) { ?>
                    <div class="confirmMessage">
                        <h2>Thank you. Recipe has been Declined and Deleted from the Temporary Database.</h2>
                    </div>
                <?php } else { ?>
                    <div class="confirmMessage">
                        <h2>Something went wrong. Recipe was not Declined and Deleted from the Temporary Database.</h2>
                    </div>
                <?php } ?>
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