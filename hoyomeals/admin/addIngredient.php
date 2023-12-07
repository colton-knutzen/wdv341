<?php
session_start();

//Only admin is allowed on this page
if (!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== 'valid') {
    header('Location: login.php');
    exit();
};

//success submission message
$confirmMessage = false;

//failed a specific input messages
$invalidIngredientName = false;
$invalidIngredientDescription = false;
$invalidIngredientImage = false;

//Global Variables to maintain state in form
$inIngredientName = "";
$inIngredientDescription = "";

if (isset($_POST['submit'])) {
    $ingredientNumber = $_POST['ingredientNumber'];
    if (empty($ingredientNumber)) {

        //This variable is used rather than exit when one of the validations fails. By using this, I continue with the entire form validation, display all invalid messages that fail validation, but not processing the form. And using exit creates a blank screen.
        $proceedWithFormProcessing = true;

        //Catch to maintain form state
        $inIngredientName = $_POST['ingredientName'];
        $inIngredientDescription = $_POST['ingredientDescription'];

        //Name validation - accepts only letters
        if (!preg_match('/^[A-Za-z ]+$/', $inIngredientName)) {
            $invalidIngredientName = true;
            $proceedWithFormProcessing = false;
        };
        
        //Description validation - accepts everything except { }. This is an attempt to prevent code from entering.
        if (strpbrk($inIngredientDescription, '{}') !== false) {
            $invalidIngredientDescription = true;
            $proceedWithFormProcessing = false;
        };

        //Image Validation - accepts only .webP files
        if (
            isset($_FILES["ingredientImage"]) && $_FILES["ingredientImage"]["error"] == 0 &&
            exif_imagetype($_FILES["ingredientImage"]["tmp_name"]) !== IMAGETYPE_WEBP
        ) {
            $invalidIngredientImage = true;
            $proceedWithFormProcessing = false;
        };

        //Form Processing - if proceedWithFormProcessing is true, aka, the data has passed all the above validations, it will proceed with the Form Processing
        if ($proceedWithFormProcessing) {
            require '../dbConnect.php';

            //Image handling - checks if there are any errors associated with the image. It's already been validated to be a webP type above.
            if (isset($_FILES["ingredientImage"]) && $_FILES["ingredientImage"]["error"] == 0) {
                $imageContent = file_get_contents($_FILES["ingredientImage"]["tmp_name"]);
            } else {
                $invalidIngredientImage = true;
                exit;
            };

            //Data is inserted into the database
            $sql = "INSERT INTO hoyomeals_ingredients";
            $sql .= "(ingredient_name, ingredient_img, ingredient_description)";
            $sql .= " VALUES ";
            $sql .= "(:ingredientName, :ingredientImg, :ingredientDescription)";

            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':ingredientName', $inIngredientName);
            $stmt->bindParam(':ingredientDescription', $inIngredientDescription);
            $stmt->bindParam(':ingredientImg', $imageContent, PDO::PARAM_LOB);

            $stmt->execute();

            $confirmMessage = true;
        };
    } else {
        die("Suspicious activity has been detected. Further suspicious attempts will result in an IP ban.");
    };
};
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

    <title>Hoyomeals - Add an Ingredient</title>
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
                        <h2>Thank you. The Ingredient has been Submitted.</h2>
                    </div>
                <?php
                } else {
                ?>
                    <div class="page-title">
                        <h1 style="text-align: center;">Add an Ingredient</h1>
                        * = required
                    </div>

                    <!-- The php echos in the values are used to maintain the state of the form when/if it fails validation. The values are set to " " at first, but after each submit, the values are caught in their respective php variable, and then entered back in the when form is reloaded -->
                    <form class="submit_recipe_form_styles" method="post" action="addIngredient.php" enctype="multipart/form-data" accept-charset="">
                        <div class="form_group">
                            <?php if ($invalidIngredientName) {
                                echo "<span style='color: red;'>Please enter a valid Ingredient Name. Only letters are allowed.</span>";
                            } ?>
                            <div class="form_row">
                                <div class="one_column">
                                    <label for="ingredientName">INGREDIENT NAME*</label>
                                    <input type="text" id="ingredientName" name="ingredientName" placeholder="Potato" value="<?php echo $inIngredientName ?>" required>
                                </div>
                            </div>

                            <?php if ($invalidIngredientImage) {
                                echo "<span style='color: red;'>Please upload a valid Ingredient Image. Only .WebP types are allowed.</span>";
                            } ?>
                            <div class="form_row">
                                <div class="one_column">
                                    <label for="ingredientImage">INGREDIENT IMAGE*</label>
                                    <input type="file" id="ingredientImage" name="ingredientImage" accept="image/*" required>
                                </div>
                            </div>

                            <?php if ($invalidIngredientDescription) {
                                echo "<span style='color: red;'>Please enter a valid Ingredient Description. { and } are not allowed.</span>";
                            } ?>
                            <div class="form_row">
                                <div class="one_column">
                                    <label for="ingredientDescription">INGREDIENT DESCRIPTION*</label>
                                    <textarea name="ingredientDescription" id="ingredientDescription" placeholder='A chunky vegetable. A gift from the earth that you never tire of with its multitude of cooking methods.' required><?php echo $inIngredientDescription ?></textarea>
                                </div>
                            </div>

                            <div class="ingredientNumber">
                                <div class="one_column">
                                    <label for="ingredientNumber">INGREDIENT NUMBER</label>
                                    <input type="number" id="ingredientNumber" name="ingredientNumber">
                                </div>
                            </div>
                        </div>

                        <div class="form_row">
                            <div class="two_columns">
                                <input style="color: red; cursor: pointer;" type="reset" value="Reset">
                            </div>
                            <div class="two_columns">
                                <input style="color: green;" type="submit" name="submit" value="Submit">
                            </div>
                        </div>

                    </form>
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