<!-- Ajax call from the recipe pages. When a user selects a recipe from the gallery, it auto-populates this pages contents to the already existing page, saving load time and allowing the user to switch between recipes quicker. -->

<?php
require '../dbConnect.php';

if (isset($_POST['recID'])) {
    $recID = $_POST['recID'];
    $categoryMiniNav = $_POST['categoryMiniNav'];

    $sqlRecipe = "SELECT recipe_id, recipe_name, recipe_description, recipe_img, recipe_difficulty, recipe_prepTime, recipe_cookTime, recipe_servingSize, recipe_calories, recipe_allergy, recipe_ingredients, recipe_steps, recipe_authorName, recipe_authorEmail, recipe_dateModified, recipe_dateEntered FROM hoyomeals_recipes WHERE recipe_id=:recID";
    $stmtRecipe = $conn->prepare($sqlRecipe);
    $stmtRecipe->bindParam(':recID', $recID);
    $stmtRecipe->execute();
    $recipe = $stmtRecipe->fetch(PDO::FETCH_ASSOC);

    $totalTime = $recipe['recipe_prepTime'] + $recipe['recipe_cookTime'];
?>

    <!-- HTML Returned to the recipe_div -->
    <script>
        function pageSetup() {
            //Event Listeners
            document.querySelector("#recipeCategoryNav").addEventListener("click", showRecipeGallery);
            document.querySelector("#instructionsH2").addEventListener("click", hideInstructions);
            document.querySelector("#servings_select").addEventListener("change", updateServings);
            document.querySelector("#ingredientsH2").addEventListener("click", hideIngredients);

            // Runtime script to display the yield on pageload. Or else it would be blank until the user interacts with the Servings dropdown.
            updateServings();
        };

        //When the user changes the number of servings, this function updates the Yield and the number of ingredients needed.
        function updateServings() {
            let selectedServings = document.querySelector('#servings_select').value;
            let servingSize = <?php echo $recipe['recipe_servingSize']; ?>;

            let yieldValue = servingSize * selectedServings;
            document.querySelector('#yieldValue').innerText = yieldValue + ' oz';

            let ingredients = <?php echo json_encode($recipe['recipe_ingredients']); ?>;
            ingredients = JSON.parse(ingredients);
            ingredients.ingredients.forEach((ingredient, index) => {
                let newQuantity = ingredient.quantity * selectedServings;
                document.querySelector('#quantity_' + index).innerText = newQuantity;
            });
        };

        //Hides the ingredients section when the header is clicked
        function hideIngredients() {
            let ingredientsArticle = document.querySelector('#ingredientsArticle');

            if (ingredientsArticle.style.display === 'none' || ingredientsArticle.style.display === '') {
                ingredientsArticle.style.display = 'block';
            } else {
                ingredientsArticle.style.display = 'none';
            }
        };

        //Hides the instructions when the header is clicked
        function hideInstructions() {
            let instructionsArticle = document.querySelector('#instructionsArticle');

            if (instructionsArticle.style.display === 'none' || instructionsArticle.style.display === '') {
                instructionsArticle.style.display = 'block';
            } else {
                instructionsArticle.style.display = 'none';
            }
        }
    </script>

    <!-- mini-naviagation. Can return to select another recipe from the same category or return to the homepage to select another category -->
    <div style="margin-bottom: 15px;"><a href="/hoyomeals/index.html">Hoyomeals</a> > <a href="#" id="recipeCategoryNav"><?php echo $categoryMiniNav ?></a></div>

    <!-- Recipe name and description -->
    <div class="content-header">
        <div class="page-title-article">
            <h1><?php echo $recipe['recipe_name'] ?></h1>
            <em class="opening-quote"><?php echo $recipe['recipe_description'] ?></em>
        </div>

        <!-- Info Box -->
        <div class="info-box">
            <table class="table1-info">

                <!-- Name -->
                <tr>
                    <td class="info-box-title" id="colorTheme"><?php echo $recipe['recipe_name'] ?></td>
                </tr>

                <!-- Img -->
                <tr>
                    <td class="info-box-img"><img src="data:image/jpeg;base64,<?php echo base64_encode($recipe['recipe_img']) ?>" alt="Recipe Image">
                    </td>
                </tr>
            </table>

            <table class="table2-info">

                <!-- Difficulty Stars -->
                <tr>
                    <td class="info-box-label">Difficulty</td>
                    <td class="info-box-value">
                        <!-- Loops and creates x number of images depending on the value of recipe_difficulty -->
                        <?php
                        $difficulty = $recipe['recipe_difficulty'];
                        for ($i = 0; $i < $difficulty; $i++) {
                            echo '<img src="../img/icon/difficulty_star.webp" class="icon_drop_shadow" alt="alt_text">';
                        }
                        ?>
                    </td>
                </tr>

                <!-- Total Time -->
                <tr>
                    <td class="info-box-label info-box_thick-border">Total Time:</td>
                    <td class="info-box-value info-box_thick-border"><?php echo $totalTime ?> min</td>
                </tr>

                <!-- Prep Time -->
                <tr>
                    <td class="info-box-label">Prep Time:</td>
                    <td class="info-box-value"><?php echo $recipe['recipe_prepTime'] ?> min</td>
                </tr>

                <!-- Cook Time -->
                <tr>
                    <td class="info-box-label">Cook Time:</td>
                    <td class="info-box-value"><?php echo $recipe['recipe_cookTime'] ?> min</td>
                </tr>

                <!-- Serving Size -->
                <tr>
                    <td class="info-box-label info-box_thick-border">Serving Size:</td>
                    <td class="info-box-value info-box_thick-border"><?php echo $recipe['recipe_servingSize'] ?> oz</td>
                </tr>

                <!-- User Interaction Serving Amount -->
                <tr>
                    <td class="info-box-label">Servings:</td>
                    <td class="info-box-value"><select id="servings_select">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </td>
                </tr>

                <!-- Yield -->
                <tr>
                    <td class="info-box-label">Yield</td>
                    <td class="info-box-value" id="yieldValue"> oz</td>
                </tr>

                <!-- Calories -->
                <tr style="line-height: normal;">
                    <td class="info-box-label info-box_thick-border">Calories <br><span class="per-serving_change">(per serving)</span></td>
                    <td class="info-box-value info-box_thick-border"><?php echo $recipe['recipe_calories'] ?></td>
                </tr>

                <!-- Allergies -->
                <tr>
                    <td class="info-box-label">Allergies</td>
                    <td class="info-box-value">
                        <?php
                        $allergyData = json_decode($recipe['recipe_allergy'], true);
                        if (isset($allergyData['allergies']) && is_array($allergyData['allergies'])) {
                            foreach ($allergyData['allergies'] as $allergy) {
                                echo ucfirst($allergy) . '<br>';
                            }
                        }
                        ?>
                    </td>
                </tr>

                <!-- Author -->
                <tr>
                    <td class="info-box-label info-box_thick-border">Author</td>
                    <td class="info-box-value info-box_thick-border"><?php echo $recipe['recipe_authorName'] ?></td>
                </tr>

                <!-- Published -->
                <tr>
                    <td class="info-box-label">Published</td>
                    <td class="info-box-value"><?php echo $recipe['recipe_dateEntered'] ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Ingredients Section -->
    <div class="parallelogram-title" id="colorTheme">
        <h2 id="ingredientsH2">Ingrediants</h2>
    </div>

    <div id="ingredientsArticle">
        <?php
        //Ingredient data is a json and an array. This converts it from a json, and loops through each, giving every ingredient their own block, pulling the image and description with a matching name from local storage to display on the page
        $ingredientsData = json_decode($recipe['recipe_ingredients'], true);
        if (isset($ingredientsData['ingredients']) && is_array($ingredientsData['ingredients'])) :

            //Used to give each block a unique ID, for displaying the ingredient image and description in the correct places
            $idNumber = 0;
            foreach ($ingredientsData['ingredients'] as $ingredients) :
                $ingredientName = $ingredients['name'];
                $ingredient_img = 'ingredient_img_' . $ingredientName;
                $ingredient_description = 'ingredient_description_' . $ingredientName;

                //gives the first ingredient a special class, removing the spacing on top. There's probably a cleaner way to do it avoiding the class echo in every ingredient block
                $class = ($idNumber == 0) ? 'first-h3' : '';
        ?>
                <div class="article">
                    <h3 class="<?php echo $class ?>"><?php echo ucfirst($ingredientName) ?> x<span id="quantity_<?php echo $idNumber ?>"><?php echo $ingredients['quantity']; ?></span></h3>
                    <div class="content-article">
                        <div class="h3-block" id="colorTheme"></div>
                        <div class="content-text">
                            <div>

                                <!-- Ingredient Image. Runtime script to grab the image data from local storage and display it here. Passes in the ingredient name with replaced spaces with _ and a capital first letter, and the unique idNumber of this block -->
                                <span id="displayIngredientImg_<?php echo $idNumber ?>"></span>
                                <script>
                                    displayIngredientImg("<?php echo str_replace(' ', '_', ucfirst($ingredientName)) ?>", <?php echo $idNumber ?>);
                                </script>

                                <!-- Ingredient Description. Same as the above, but pulls the description from local storage -->
                                <span id="displayIngredientDescription_<?php echo $idNumber ?>"></span>
                                <script>
                                    displayIngredientDescription("<?php echo str_replace(' ', '_', ucfirst($ingredientName)) ?>", <?php echo $idNumber ?>);
                                </script>

                            </div>
                        </div>
                    </div>
                </div>
        <?php
                $idNumber++;
            endforeach;
        endif;
        ?>
    </div>
    <div id="article-ad-one"></div>

    <!-- Instructions Section -->
    <div class="parallelogram-title" id="colorTheme">
        <h2 id="instructionsH2">Instructions</h2>
    </div>

    <div id="instructionsArticle">
        <?php
        //Ingredient data is a json and an array. This converts it from a json, and loops through each, giving every ingredient their own block, pulling the image and description with a matching name from local storage to display on the page
        $stepsData = json_decode($recipe['recipe_steps'], true);
        if (isset($stepsData['steps']) && is_array($stepsData['steps'])) :
            $stepNumber = 1;
            foreach ($stepsData['steps'] as $steps) :
                $class = ($stepNumber == 1) ? 'first-h3' : '';
        ?>
                <div class="article">
                    <h3 class="<?php echo $class ?>">Step <?php echo $stepNumber ?>: <?php echo ucfirst($steps['title']) ?></h3>
                    <div class="content-article">
                        <div class="h3-block" id="colorTheme"></div>
                        <div class="content-text"><?php echo ucfirst($steps['description']) ?></div>
                    </div>
                </div>
        <?php
                $stepNumber++;
            endforeach;
        endif;
        ?>

    </div>
<?php
} else {
    echo "Recipe ID not provided."; //Shouldn't ever trigger since the recipe gallery is populated by the database
}
?>

<!-- Since there is no body to attached an onload to, I've thrown the pageSetup at the bottom to behavior similarly  -->
<script>pageSetup()</script>