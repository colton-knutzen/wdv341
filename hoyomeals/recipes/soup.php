<?php
//Anyone can access these pages, regardless of login status

require '../dbConnect.php';

//Grabs all the recipe data for this category
$sqlRecipeGallery = "SELECT recipe_id, recipe_category, recipe_name, recipe_img FROM hoyomeals_recipes WHERE recipe_category=:recID";
$stmtRecipeGallery = $conn->prepare($sqlRecipeGallery);
$recID = "soup";
$stmtRecipeGallery->bindParam(':recID', $recID);
$stmtRecipeGallery->execute();
$recipeGallery = $stmtRecipeGallery->fetchAll(PDO::FETCH_ASSOC);

//Grabs all the ingredients from their database
$sqlIngredient = "SELECT ingredient_name, ingredient_img, ingredient_description FROM hoyomeals_ingredients";
$stmtIngredient = $conn->prepare($sqlIngredient);
$stmtIngredient->execute();
$ingredients = $stmtIngredient->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../css/styles.css" rel="stylesheet" type="text/css">
    <link href="../css/stylesForm.css" rel="stylesheet" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="../script/script.js"></script>

    <title>Hoyomeals: Soup</title>

    <script>
        //When the mini-navigation above the recipe is clicked, it hides the current recipe and displays the gallery. Another recipe can be selected to overwrite the current one
        function showRecipeGallery() {
            let recipeSelectionDiv = document.querySelector('.recipeSelection_div');
            let recipeDiv = document.querySelector('.recipe_div');

            if (recipeSelectionDiv && recipeDiv) {
                recipeSelectionDiv.style.display = 'block';
                recipeDiv.style.display = 'none';
            }
        };

        //Ajax call to display the selected recipe without loading a new page, and hiding the recipe gallery
        function showRecipe(recipeId) {
            $.ajax({
                type: 'POST',
                url: '../php/getRecipe.php',
                data: {
                    recID: recipeId,
                    categoryMiniNav: 'Soup'
                },
                success: function(response) {
                    $('.recipe_div').html(response);
                },
                error: function(error) {
                    console.log(error);
                }
            });

            let recipeSelectionDiv = document.querySelector('.recipeSelection_div');
            let recipeDiv = document.querySelector('.recipe_div');

            if (recipeSelectionDiv && recipeDiv) {
                recipeSelectionDiv.style.display = 'none';
                recipeDiv.style.display = 'block';
            }
        };

        //This stores all the ingredients in the ingredient database in local storage. This gives the page access to the name, description, and image data rather than using another ajax call to populate them in the displayed recipe.
        let ingredientDescription = "";
        let ingredientImg = "";

        <?php foreach ($ingredients as $ingredient) : ?>
            //Ingredient Image. If a variable in local storage called ingredient_img_(Ingredient's Name) doesn't exist, it creates a variable with that as it's key and the encoded blob data type as it's value. Only creating the variable when one doesn't exist saves time and space rather than it creating a new one every time a recipe is loaded
            if (localStorage.getItem("ingredient_img_<?php echo str_replace(' ', '_', $ingredient['ingredient_name']); ?>") === null) {
                ingredientImg = "<?php echo base64_encode($ingredient['ingredient_img']); ?>";
                localStorage.setItem("ingredient_img_<?php echo str_replace(' ', '_', $ingredient['ingredient_name']); ?>", ingredientImg);
            };

            //Ingredient Description. Does the same but stores the description in a variable called ingredient_description_(Ingredient's Name)
            if (localStorage.getItem("ingredient_description_<?php echo str_replace(' ', '_', $ingredient['ingredient_name']); ?>") === null) {
                ingredientDescription = <?php echo json_encode($ingredient['ingredient_description']); ?>;
                localStorage.setItem("ingredient_description_<?php echo str_replace(' ', '_', $ingredient['ingredient_name']); ?>", ingredientDescription);
            };
        <?php endforeach; ?>

        //Takes the ingredients name, adds ingredient_img_ to it, checks local storage, and display the image at the correct id place if it exists
        function displayIngredientImg(ingredientName, ingredientDisplayID) {
            let key = "ingredient_img_" + ingredientName;
            let imgData = localStorage.getItem(key);

            if (imgData) {
                document.querySelector("#displayIngredientImg_" + ingredientDisplayID).innerHTML =
                    "<img class='article-icons' alt='alt_text' src='data:image/*;base64," + imgData + "' alt='" + ingredientName + "'>";
            } 
            //The only time this will run is if an ingredient is added to a recipe, and then said ingredient is deleted from the ingredient database
            else {
                document.querySelector("#displayIngredientImg_" + ingredientDisplayID).innerHTML = "Image not found for " + ingredientName + ".";
            }
        };

        //Same as above but with the description
        function displayIngredientDescription(ingredientName, ingredientDisplayID) {
            let key = "ingredient_description_" + ingredientName;
            let description = localStorage.getItem(key);

            if (description) {
                document.querySelector("#displayIngredientDescription_" + ingredientDisplayID).innerHTML = description;
            } else {
                document.querySelector("#displayIngredientDescription_" + ingredientDisplayID).innerHTML = "Description not found for " + ingredientName + ".";
            }
        };
    </script>
</head>

<body>
    <!-- Start of site skeleton structure -->
    <div class="grid-container">
        <div id="inc-header"></div>
        <script>
            $(function() {
                $("#inc-header").load("../structure/header.html")
            });
        </script>
        <div id="inc-top-nav"></div>
        <script>
            $(function() {
                $("#inc-top-nav").load("../structure/top_nav.html")
            });
        </script>
        <div id="inc-side-nav"></div>
        <script>
            $(function() {
                $("#inc-side-nav").load("../structure/side_nav.html")
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

                <div class="recipeSelection_div">
                    <div class="page-title">
                        <h1 style="text-align: center;">Soups</h1>
                    </div>

                    <!-- Gallery -->
                    <div class="recipeGallery">

                        <!-- Displays the name and image of every recipe that is this category -->
                        <?php foreach ($recipeGallery as $recipeGallerys) : ?>
                            <figure>
                                <div class="imgGallery">
                                    <img src="data:image/jpeg;base64,<?= base64_encode($recipeGallerys['recipe_img']) ?>" alt="Recipe Image" onclick="showRecipe(<?= $recipeGallerys['recipe_id'] ?>)">
                                </div>
                                <figcaption><?= $recipeGallerys['recipe_name'] ?></figcaption>
                            </figure>
                        <?php endforeach; ?>

                    </div>
                </div>

                <!-- Recipe -->
                <div class="recipe_div">
                </div>

            </div>
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
                $("#inc-footer").load("/Hoyomeals/structure/footer.html")
            });
        </script>
    </div>
    <!-- End of site skeleton structure -->
</body>

</html>