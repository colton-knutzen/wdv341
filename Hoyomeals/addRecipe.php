<?php
//Only registered users are allowed to submit a recipe
session_start();

if (!isset($_SESSION['userLoggedIn']) || $_SESSION['userLoggedIn'] !== 'valid') {
    header('Location: login.php');
    exit();
};

//Connect to db to grab ingredient images for dropdown
require 'dbConnect.php';

$sql = "SELECT ingredient_name FROM hoyomeals_ingredients";
$stmt = $conn->prepare($sql);
$stmt->execute();
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

//success submission message
$confirmMessage = false;

//failed a specific input messages
$invalidAuthorName = false;
$invalidEmail = false;
$invalidRecipeName = false;
$invalidRecipeImage = false;
$invalidRecipeDescription = false;
$invalidPrepTime = false;
$invalidCookTime = false;
$invalidServingSize = false;
$invalidCalories = false;
$invalidAllergy = false;
$invalidQuantity = false;
$invalidIngredientName = false;
$invalidStepTitle = false;
$invalidStepDescription = false;
$invalidRecipeSteps = false;

//Global Variables to maintain state in form
$inAuthorName = "";
$inAuthorEmail = "";
$inRecipeName = "";
$inDescription = "";
$inPrepTime = "";
$inCookTime = "";
$inServingSize = "";
$inCalories = "";
$inDifficulty = 0;
$inCategory = "";

if (isset($_POST['submit'])) {
    $recipeNumber = $_POST['recipeNumber'];
    if (empty($recipeNumber)) {

        //This variable is used rather than exit when one of the validations fails. By using this, I continue with the entire form validation, display all invalid messages that fail validation, but not processing the form. And using exit creates a blank screen.
        $proceedWithFormProcessing = true;

        //Variables used for Maintaining form state but not validation
        $inCategory = $_POST['recipeCategory'];
        $inDifficulty = $_POST['difficultyRating'];

        //I would love to have maintained the state of allergy, ingredient, step title and description but I'm unsure how to do that with the current set up of allowing the user to add an infinte amount of new inputs with these

        //Same , but these are used for validation
        $inAuthorName = $_POST['authorName'];
        $inAuthorEmail = $_POST['email'];
        $inRecipeName = $_POST['recipeName'];
        $inDescription = $_POST['recipeDescription'];
        $inPrepTime = $_POST['prepTime'];
        $inCookTime = $_POST['cookTime'];
        $inServingSize = $_POST['servingSize'];
        $inCalories = $_POST['calories'];

        //Form Validation
        //Full Name/Username Validation - only accepts letters and numbers
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $inAuthorName)) {
            $invalidAuthorName = true;
            $proceedWithFormProcessing = false;
        };

        //Email Validation - only accepts letters, numbers, -, _, @, and .
        if (!preg_match('/^[a-zA-Z0-9-_.]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/', $inAuthorEmail)) {
            $invalidEmail = true;
            $proceedWithFormProcessing = false;
        };

        //Recipe Name Validation - accepts everything except { }. This is an attempt to prevent code from entering.
        if (strpbrk($inRecipeName, '{}') !== false) {
            $invalidRecipeName = true;
            $proceedWithFormProcessing = false;
        };

        //Image Validation - accepts only .webP files
        if (
            isset($_FILES["recipeImage"]) && $_FILES["recipeImage"]["error"] == 0 &&
            exif_imagetype($_FILES["recipeImage"]["tmp_name"]) !== IMAGETYPE_WEBP
        ) {
            $invalidRecipeImage = true;
            $proceedWithFormProcessing = false;
        };

        //Recipe Description Validation - accepts everything except { }. This is an attempt to prevent code from entering.
        if (strpbrk($inDescription, '{}') !== false) {
            $invalidRecipeDescription = true;
            $proceedWithFormProcessing = false;
        };

        //Prep Time Validation - accepts 0 minutes to 24 hours (1440 minutes)
        if (!is_numeric($inPrepTime) || $inPrepTime < 0 || $inPrepTime > 1440) {
            $invalidPrepTime = true;
            $proceedWithFormProcessing = false;
        };

        //Cook Time Validation - accepts 0 minutes to 24 hours (1440 minutes)
        if (!is_numeric($inCookTime) || $inCookTime < 0 || $inCookTime > 1440) {
            $invalidCookTime = true;
            $proceedWithFormProcessing = false;
        };


        //Serving Size Validation - accepts 1 to 100 oz
        if (!is_numeric($inServingSize) || $inServingSize < 1 || $inServingSize > 100) {
            $invalidServingSize = true;
            $proceedWithFormProcessing = false;
        };


        //Calorie Validation - accepts 5 to 3000 calories
        if (!is_numeric($inCalories) || $inCalories < 5 || $inCalories > 3000) {
            $invalidCalories = true;
            $proceedWithFormProcessing = false;
        };

        //Allergy Validation - accepts letters, spaces, and an empty array. Foreach is used because the Post will be an array, so it loops through and checks each
        if (isset($_POST['allergy'])) {
            foreach ($_POST['allergy'] as $allergy) {
                if (!preg_match('/^[A-Za-z\s]*$/', $allergy)) {
                    $invalidAllergy = true;
                    $proceedWithFormProcessing = false;
                    break;
                }
            }
        };

        //Ingredient Quantity Validation - accepts 1 to 10 ingredient quantity. Does not check for ingredient name since that is a drop down created by the ingredient database. Foreach is used because the Post will be an array, so it loops through each quantity and checks each
        foreach ($_POST['quantity'] as $quantity) {
            if (!is_numeric($quantity) || $quantity < 1 || $quantity > 10) {
                $invalidQuantity = true;
                $proceedWithFormProcessing = false;
                break;
            }
        };

        //Ingredient Quantity and Ingredient Name Validation - This checks if a quantity greater than 1 is entered, then it must also have an ingredientName value. This prevents users from entering in a number but failing to select an ingredient from the dropdown
        for ($i = 0; $i < count($_POST['ingredient']); $i++) {
            $ingredientName = $_POST['ingredient'][$i];
            $quantity = (int)$_POST['quantity'][$i]; // Cast to integer

            if ($quantity > 1 && empty($ingredientName)) {
                $invalidIngredientName = true;
                $proceedWithFormProcessing = false;
                break;
            }
        };

        //Step Title Validation - accepts everything except { }. This is an attempt to prevent code from entering. Foreach is used because the Post will be an array, so it loops through each stepTitle and checks each 
        foreach ($_POST['recipeSteps'] as $stepTitle) {
            if (strpbrk($stepTitle, '{}')) {
                $invalidStepTitle = true;
                $proceedWithFormProcessing = false;
                break;
            }
        };

        //Step Description Validation - accepts everything except { }. This is an attempt to prevent code from entering. Foreach is used because the Post will be an array, so it loops through each stepDescription and checks each 
        foreach ($_POST['recipeDescriptions'] as $stepDescription) {
            if (strpbrk($stepDescription, '{}')) {
                $invalidStepDescription = true;
                $proceedWithFormProcessing = false;
                break;
            }
        };

        //Step Title and Description Validation  - this checks if a description is entered, then it must also have a stepTitle value. This prevents users from entering a description but failing to enter a title. 
        //Just having a title with not description is allowed
        for ($i = 0; $i < count($_POST['recipeSteps']); $i++) {
            $stepTitles = $_POST['recipeSteps'][$i];
            $stepDescriptions = $_POST['recipeDescriptions'][$i];

            if (!empty($stepDescriptions) && empty($stepTitles)) {
                $invalidRecipeSteps = true; // You can customize the error flag name
                $proceedWithFormProcessing = false;
                break;
            }
        };

        //Form Processing - if proceedWithFormProcessing is true, aka, the data has passed all the above validations, it will proceed with the Form Processing
        if ($proceedWithFormProcessing) {
            $inCategory = $_POST['recipeCategory'];
            $inDifficulty = $_POST['difficultyRating'];
            $inAuthorName = $_POST['authorName'];
            $inAuthorEmail = $_POST['email'];
            $inRecipeName = $_POST['recipeName'];
            $inDescription = $_POST['recipeDescription'];
            $inPrepTime = $_POST['prepTime'];
            $inCookTime = $_POST['cookTime'];
            $inServingSize = $_POST['servingSize'];
            $inCalories = $_POST['calories'];

            //Cooking Steps handling - creates an array with recipeStep as the key and recipeDescription as the value. Since the steps can be an undefined amount, it loops through each until done. After that, its converts it to a json object to be stored in the database.
            $recipeSteps = [];
            if (isset($_POST['recipeSteps'])) {
                $stepTitles = $_POST['recipeSteps'];
                $stepDescriptions = $_POST['recipeDescriptions'];

                foreach ($stepTitles as $index => $stepTitle) {
                    $stepDescription = $stepDescriptions[$index];
                    $recipeSteps[] = [
                        "title" => $stepTitle,
                        "description" => $stepDescription
                    ];
                }
            }
            $stepsJSON = json_encode(['steps' => $recipeSteps]);

            //Ingredient handling - creates an array with ingredient as the key and quantity as the value. Since the ingredients can be an undefined amount, it loops through each until done. After that, its converts it to a json object to be stored in the database.
            $recipeIngredients = [];
            if (isset($_POST['ingredient'])) {
                $ingredientNames = $_POST['ingredient'];
                $quantities = $_POST['quantity'];

                foreach ($ingredientNames as $index => $ingredientName) {
                    $quantity = (int)$quantities[$index];
                    $recipeIngredients[] = [
                        "name" => $ingredientName,
                        "quantity" => $quantity
                    ];
                }
            }
            $ingredientsJSON = json_encode(['ingredients' => $recipeIngredients]);


            //Allergy handling - creates an array with allergies. Since the allergies can be an undefined amount, it loops through each until done. After that, it converts it to a json object to be stored in the database.
            $allergies = [];
            if (isset($_POST['allergy'])) {
                $selectedAllergy = $_POST['allergy'];

                foreach ($selectedAllergy as $allergy) {
                    $allergies[] = $allergy;
                }
            };
            $allergiesJSON = json_encode(['allergies' => $allergies]);


            //Image handling - checks if there are any errors associated with the image. It's already been validated to be a webP type above.
            if (isset($_FILES["recipeImage"]) && $_FILES["recipeImage"]["error"] == 0) {
                $imageContent = file_get_contents($_FILES["recipeImage"]["tmp_name"]);
            } else {
                $invalidRecipeImage = true;
                exit;
            };

            $sql = "INSERT INTO hoyomeals_recipes_temp";
            $sql .= "(recipe_categoryTemp, recipe_nameTemp, recipe_descriptionTemp, recipe_imgTemp, recipe_difficultyTemp, recipe_prepTimeTemp, recipe_cookTimeTemp, recipe_servingSizeTemp, recipe_caloriesTemp, recipe_allergyTemp, recipe_ingredientsTemp, recipe_stepsTemp, recipe_authorNameTemp, recipe_authorEmailTemp, recipe_dateModifiedTemp, recipe_dateEnteredTemp)";
            $sql .= " VALUES ";
            $sql .= "(:category, :recipeName, :description, :imageContent, :difficulty, :prepTime, :cookTime, :servingSize, :calories, :allergies, :ingredients, :steps, :authorName, :authorEmail, :dateModified, :dateEntered)";

            $stmt = $conn->prepare($sql);

            $today = date("Y-m-d");

            $stmt->bindParam(':category', $inCategory);
            $stmt->bindParam(':recipeName', $inRecipeName);
            $stmt->bindParam(':description', $inDescription);
            $stmt->bindParam(':difficulty', $inDifficulty);
            $stmt->bindParam(':prepTime', $inPrepTime);
            $stmt->bindParam(':cookTime', $inCookTime);
            $stmt->bindParam(':servingSize', $inServingSize);
            $stmt->bindParam(':calories', $inCalories);
            $stmt->bindParam(':allergies', $allergiesJSON);
            $stmt->bindParam(':ingredients', $ingredientsJSON);
            $stmt->bindParam(':steps', $stepsJSON);
            $stmt->bindParam(':authorName', $inAuthorName);
            $stmt->bindParam(':authorEmail', $inAuthorEmail);
            $stmt->bindParam(':dateModified', $today);
            $stmt->bindParam(':dateEntered', $today);
            $stmt->bindParam(':imageContent', $imageContent, PDO::PARAM_LOB);

            $stmt->execute();

            $confirmMessage = true;
        }
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

    <title>Hoyomeals: Add a Recipe</title>
    <script>
        function pageSetup() {
            //Event Listeners
            document.querySelector("#add_allergy").addEventListener("click", addAllergy);
            document.querySelector('#difficultyStar1').addEventListener("click", () => setRating(1));
            document.querySelector('#difficultyStar2').addEventListener("click", () => setRating(2));
            document.querySelector('#difficultyStar3').addEventListener("click", () => setRating(3));
            document.querySelector('#difficultyStar4').addEventListener("click", () => setRating(4));
            document.querySelector("#add_ingredient").addEventListener("click", addIngredient);
            document.querySelector("#add_recipe_step").addEventListener("click", addRecipeStep);
            document.querySelector('#ingredientSelect_1').addEventListener("change", () => displayIngredient('ingredientSelect_1', 'ingredientImg_1'));

            //Maintain State of Form
            maintainDifficultyRating('<?php echo $inDifficulty; ?>');
            maintainCategoryState('<?php echo $inCategory ?>')
        };

        //Difficulty Star Rating - when the onclick is triggered, it passing in a number. Then it selects all the .star and adds the class X times to X stars, which changes them to yellow. Then it changes the hidden input to the value
        function setRating(rating) {
            let stars = document.querySelectorAll('.star');
            for (let i = 0; i < stars.length; i++) {
                if (i < rating) {
                    stars[i].classList.add('selected');
                } else {
                    stars[i].classList.remove('selected');
                }
            }
            document.querySelector('#difficultyRating').value = rating;
        };

        //Make an XMLHttpRequest to getIngredientImg.php to pull the .ingredientImg data stored in the ingredient database based on the ingredients name. The image on the database is a blob data type, so it returns a long string url. That url codes is then written into the html, which is then displayed on the page.
        function displayIngredient(selectId, imageContainerId) {

            //the individual select and image container ID is passed in so when new ingredient fields are added by the user, the image will be added to the correct space
            let ingredientSelect = document.querySelector(`#${selectId}`);
            let imageContainer = document.querySelector(`#${imageContainerId}`);

            //removes the previous image before adding a new one
            imageContainer.innerHTML = "";

            if (ingredientSelect.value !== "") {
                let xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        let imgData = xhr.responseText;
                        imageContainer.innerHTML = `<img src="data:image/jpeg;base64,${imgData}" alt="Ingredient Image">`;
                    }
                };

                xhr.open("GET", "php/getIngredientImg.php?ingredient_name=" + ingredientSelect.value, true);
                xhr.send();
            } else {
                //if the default value is selected, then the image space turns blank
                imageContainer.innerHTML = "";
            }
        };

        //Adds a new ingredient input for the user to input as many ingredients and quantities as they would like
        function addIngredient() {
            let ingredientContainer = document.querySelector('#ingredient_container');

            //selects all the current ingredientImg_, finds it's length, then adds 1 on top of it to be stored in the idCount
            let existingIngredientImgDiv = ingredient_container.querySelectorAll('[id^="ingredientImg_"]');
            let idCount = existingIngredientImgDiv.length + 1;

            //Creates all the neccessary html 
            let newRow = document.createElement('div');
            newRow.classList.add('form_row');

            let imageContainer = document.createElement('div');
            imageContainer.id = "ingredientImg_" + idCount;
            imageContainer.classList.add('ingrediantIconColumn');

            let ingrediantSelectColumn = document.createElement('div');
            ingrediantSelectColumn.classList.add('ingrediantSelectColumn');

            let newIngredientSelectLabel = document.createElement("label");
            newIngredientSelectLabel.htmlFor = "ingredientSelect_" + idCount;
            ingrediantSelectColumn.appendChild(newIngredientSelectLabel);

            let selectElement = document.createElement('select');
            selectElement.id = "ingredientSelect_" + idCount;
            selectElement.name = 'ingredient[]';
            selectElement.addEventListener('change', () => {
                displayIngredient("ingredientSelect_" + idCount, "ingredientImg_" + idCount);
            });

            let defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Select an Ingredient';
            selectElement.appendChild(defaultOption);

            <?php
            foreach ($ingredients as $ingredient) {
                echo "selectElement.options.add(new Option('{$ingredient['ingredient_name']}'));";
            }
            ?>

            ingrediantSelectColumn.appendChild(selectElement);

            let ingrediantQuantityColumn = document.createElement('div');
            ingrediantQuantityColumn.classList.add('ingrediantQuantityColumn');

            let newQuantityLabel = document.createElement('label');
            newQuantityLabel.htmlFor = "quantity_" + idCount;

            let newQuantityInput = document.createElement('input');
            newQuantityInput.type = 'number';
            newQuantityInput.id = "quantity_" + idCount;
            newQuantityInput.name = 'quantity[]';
            newQuantityInput.placeholder = 'Qt';


            //appends the individual above objects together
            ingrediantQuantityColumn.appendChild(newQuantityLabel);
            ingrediantQuantityColumn.appendChild(newQuantityInput);

            newRow.appendChild(imageContainer);
            newRow.appendChild(ingrediantSelectColumn);
            newRow.appendChild(ingrediantQuantityColumn);

            //appends the whole object to the html
            ingredientContainer.appendChild(newRow);
        };

        //Adds a new step input, title and description, for the user to input as many steps as they would like
        function addRecipeStep() {
            let recipeStepsContainer = document.querySelector('#recipeStep_container');

            //selects all the current recipeStep_, finds it's length, then adds 1 on top of it to be stored in the idCount
            let existingRecipeSteps = recipeStepsContainer.querySelectorAll('[id^="recipeStep_"]');
            let idCount = existingRecipeSteps.length + 1;

            //Creates all the neccessary html 
            //For Title
            let newRecipeStepFormRowDiv = document.createElement("div");
            newRecipeStepFormRowDiv.classList.add("form_row");
            newRecipeStepFormRowDiv.style.paddingBottom = "0px";

            let newRecipeStepInput = document.createElement("div");
            newRecipeStepInput.classList.add("one_column");

            let recipeStepLabel = document.createElement("label");
            recipeStepLabel.htmlFor = "recipeStep_" + idCount;

            let recipeStepInput = document.createElement("input");
            recipeStepInput.type = "text";
            recipeStepInput.name = "recipeSteps[]";
            recipeStepInput.id = "recipeStep_" + idCount;
            recipeStepInput.placeholder = "Title";

            newRecipeStepInput.appendChild(recipeStepLabel);
            newRecipeStepInput.appendChild(recipeStepInput);

            newRecipeStepFormRowDiv.appendChild(newRecipeStepInput);

            //For Description
            let newRecipeDescriptionFormRowDiv = document.createElement("div");
            newRecipeDescriptionFormRowDiv.classList.add("form_row");

            let newRecipeDescriptionInput = document.createElement("div");
            newRecipeDescriptionInput.classList.add("one_column");

            let recipeDescriptionLabel = document.createElement("label");
            recipeDescriptionLabel.htmlFor = "recipeDescription_" + idCount;

            let recipeDescriptionTextarea = document.createElement("textarea");
            recipeDescriptionTextarea.name = "recipeDescriptions[]";
            recipeDescriptionTextarea.id = "recipeDescription_" + idCount;
            recipeDescriptionTextarea.placeholder = "Description";

            //appends everything together
            newRecipeDescriptionInput.appendChild(recipeDescriptionLabel);
            newRecipeDescriptionInput.appendChild(recipeDescriptionTextarea);

            newRecipeDescriptionFormRowDiv.appendChild(newRecipeDescriptionInput);

            let recipeStepContainer = document.querySelector('#recipeStep_container');

            //appends to the html
            recipeStepContainer.appendChild(newRecipeStepFormRowDiv);
            recipeStepContainer.appendChild(newRecipeDescriptionFormRowDiv);
        };

        //Adds a new allergy input for the user to input as many steps as they would like
        function addAllergy() {
            let allergyContainer = document.querySelector('#allergy_container');

            //selects all the current allergy_, finds it's length, then adds 1 on top of it to be stored in the idCount
            let existingAllergyInputs = allergyContainer.querySelectorAll('[id^="allergy_"]');
            let allergyCount = existingAllergyInputs.length + 1;

            //Creates all the neccessary html 
            let newAllergyFormRowDiv = document.createElement("div");
            newAllergyFormRowDiv.classList.add("form_row");
            newAllergyFormRowDiv.style.paddingBottom = "0px";

            let newAllergyDiv = document.createElement("div");
            newAllergyDiv.classList.add("one_column");

            let newAllergyLabel = document.createElement("label");
            newAllergyLabel.htmlFor = "allergy_" + allergyCount;

            let newAllergyInput = document.createElement("input");
            newAllergyInput.type = "text";
            newAllergyInput.id = "allergy_" + allergyCount;
            newAllergyInput.name = "allergy[]";

            //appends everything together
            newAllergyDiv.appendChild(newAllergyLabel);
            newAllergyDiv.appendChild(newAllergyInput);

            newAllergyFormRowDiv.appendChild(newAllergyDiv);

            //appends to the html
            allergyContainer.appendChild(newAllergyFormRowDiv);
        };

        //This is a runtime script, ran right after the recipeCategory section is loaded. It takes the value stored in the php variable, and changes the dropdown value to match. Used to maintain the state of the category dropdown when/if any part of the form fails validation
        function maintainCategoryState(categoryState) {
            let categoryDropdown = document.querySelector('#recipeCategory');
            categoryDropdown.value = categoryState;
        };

        //This is a runtime script, ran right after the recipeDifficult section is loaded. It takes whatever the current php value is, and depending on the value, adds a class to however many IDs and changes the hidden input value. Used to maintain the state of the diffifculty when/if any part of the form fails validation
        function maintainDifficultyRating(inDifficulty) {
            if (inDifficulty == "1") {
                document.querySelector('#difficultyStar1').classList.add('selected');
                document.querySelector('#difficultyRating').value = "1";
            };
            if (inDifficulty == "2") {
                document.querySelector('#difficultyStar1').classList.add('selected');
                document.querySelector('#difficultyStar2').classList.add('selected');
                document.querySelector('#difficultyRating').value = "2";
            };
            if (inDifficulty == "3") {
                document.querySelector('#difficultyStar1').classList.add('selected');
                document.querySelector('#difficultyStar2').classList.add('selected');
                document.querySelector('#difficultyStar3').classList.add('selected');
                document.querySelector('#difficultyRating').value = "3";
            };
            if (inDifficulty == "4") {
                document.querySelector('#difficultyStar1').classList.add('selected');
                document.querySelector('#difficultyStar2').classList.add('selected');
                document.querySelector('#difficultyStar3').classList.add('selected');
                document.querySelector('#difficultyStar4').classList.add('selected');
                document.querySelector('#difficultyRating').value = "4";
            };
        };
    </script>
</head>

<body onload="pageSetup()">
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
                        <h2>Thank you. We have recieved your Recipe. After the Admins have approved it, it'll become public on Hoyomeals for everyone to enjoy!.</h2>
                    </div>
                <?php
                } else {
                ?>
                    <div class="page-title">
                        <h1 style="text-align: center;">Add Recipe Form</h1>
                        <div style="text-align: center;">Submit your Recipe here. It will become public on Hoyomeals for everyone to enjoy after being approved by the Admins!
                            <br>Double check what you enter carefully. You're recipe will be rejected if there are any errors. The Admins will not correct any mistakes.
                        </div>
                        <br>* = required
                    </div>

                    <!-- The php echos in the values are used to maintain the state of the form when/if it fails validation. The values are set to " " at first, but after each submit, the values are caught in their respective php variable, and then entered back in the when form is reloaded -->
                    <form class="submit_recipe_form_styles" method="post" action="addRecipe.php" enctype="multipart/form-data" accept-charset="">

                        <!-- FULL NAME/USERNAME, EMAIL GROUP -->
                        <div class="form_group">

                            <!-- FULL NAME/USERNAME -->
                            <div class="form_row">
                                <div class="one_column">
                                    <label for="authorName">FULL NAME/USERNAME*</label>
                                    <input type="text" id="authorName" name="authorName" placeholder="Full Name/Username" value="<?php echo $inAuthorName ?>" required>
                                    <?php if ($invalidAuthorName) {
                                        echo "<div style='color: red;'>Please enter a valid Full Name or Username. Spaces and special characters other than _ are not allowed.</div>";
                                    } ?>
                                </div>
                            </div>

                            <!-- EMAIL -->
                            <div class="form_row">
                                <div class="one_column">
                                    <label for="email">EMAIL*</label>
                                    <input type="email" id="email" name="email" placeholder="YourEmail@gmail.com" value="<?php echo $inAuthorEmail ?>" required>
                                    <?php if ($invalidEmail) {
                                        echo "<span style='color: red;'>Please enter a valid Email. Spaces and specials characters other than @ and . are not allowed.</span>";
                                    } ?>
                                </div>
                            </div>
                        </div>

                        <!-- RECIPE NAME, CATEGORY, IMAGE, DESCRIPTION, NUMBER GROUP -->
                        <div class="form_group">

                            <!-- RECIPE NAME -->
                            <div class="form_row">
                                <div class="one_column">
                                    <label for="recipeName">RECIPE NAME*</label>
                                    <input type="text" id="recipeName" name="recipeName" placeholder="Outrider's Champion Steak!" value="<?php echo $inRecipeName ?>" required>
                                    <?php if ($invalidRecipeName) {
                                        echo "<span style='color: red;'>Please enter a valid Recipe Name. { and } are not allowed.</span>";
                                    } ?>
                                </div>
                            </div>

                            <!-- RECIPE CATEGORY -->
                            <div class="form_row">
                                <div class="one_column">
                                    <label for="recipeCategory">RECIPE CATEGORY*</label>
                                    <select type="text" id="recipeCategory" name="recipeCategory" required>
                                        <option value="">Select a Category</option>
                                        <option value="appetizer">Appetizer</option>
                                        <option value="breakfast">Breakfast</option>
                                        <option value="dessert">Dessert</option>
                                        <option value="drink">Drink</option>
                                        <option value="meat">Meat</option>
                                        <option value="pasta">Pasta</option>
                                        <option value="pizza">Pizza</option>
                                        <option value="salad">Salad</option>
                                        <option value="seafood">Seafood</option>
                                        <option value="soup">Soup</option>
                                    </select>
                                </div>
                            </div>

                            <!-- RECIPE IMAGE -->
                            <div class="form_row">
                                <div class="one_column">
                                    <label for="recipeImage">RECIPE IMAGE*</label>
                                    <input type="file" id="recipeImage" name="recipeImage" accept="image/*" required>
                                    <?php if ($invalidRecipeImage) {
                                        echo "<span style='color: red;'>Please upload a valid Ingredient Image. Only .WebP types are allowed.</span>";
                                    } ?>
                                </div>
                            </div>

                            <!-- RECIPE DESCRIPTION -->
                            <div class="form_row">
                                <div class="one_column">
                                    <label for="recipeDescription">RECIPE DESCRIPTION*</label>
                                    <textarea name="recipeDescription" id="recipeDescription" placeholder='"One side is obviously uncooked. The other side gives off a subtle scent of something burnt. Close your eyes and have a big mouthful, just to keep Amber happy if nothing else."' required><?php echo $inDescription ?></textarea>
                                    <?php if ($invalidRecipeDescription) {
                                        echo "<span style='color: red;'>Please enter a valid Recipe Description. { and } are not allowed.</span>";
                                    } ?>
                                </div>
                            </div>

                            <!-- RECIPE NUMBER -->
                            <div class="recipeNumber">
                                <div class="one_column">
                                    <label for="recipeNumber">RECIPE NUMBER*</label>
                                    <input type="number" id="recipeNumber" name="recipeNumber" placeholder="Number of recipes submitted">
                                </div>
                            </div>
                        </div>

                        <!-- DIFFICULTY, PREP TIME, COOK TIME, SERVING SIZE, CALORIES GROUP -->
                        <div class="form_group">

                            <!-- DIFFICULTY -->
                            <div class="form_row-difficulty">
                                <label for="difficultyRating" class="difficultyRating-label">DIFFICULTY*</label>
                                <div class="difficultyRating">
                                    <span id="difficultyStar1" class="star">★</span>
                                    <span id="difficultyStar2" class="star">★</span>
                                    <span id="difficultyStar3" class="star">★</span>
                                    <span id="difficultyStar4" class="star">★</span>
                                </div>
                                <input type="hidden" id="difficultyRating" name="difficultyRating" required>
                            </div>

                            <!-- PREP TIME -->
                            <div class="form_row">
                                <div class="two_columns">
                                    <label for="prepTime">TOTAL PREP TIME* (in minutes)</label>
                                    <input type="number" id="prepTime" name="prepTime" placeholder="20" value="<?php echo $inPrepTime ?>" required>
                                    <?php if ($invalidPrepTime) {
                                        echo "<span style='color: red;'>Please enter a valid Prep Time. Only 0 to 1440 minutes are allowed.</span>";
                                    } ?>
                                </div>

                                <!-- COOK TIME -->
                                <div class="two_columns">
                                    <label for="cookTime">TOTAL COOK TIME* (in minutes)</label>
                                    <input type="number" id="cookTime" name="cookTime" placeholder="45" value="<?php echo $inCookTime ?>" required>
                                    <?php if ($invalidCookTime) {
                                        echo "<span style='color: red;'>Please enter a valid Cook Time. Only 0 to 1440 minutes are allowed.</span>";
                                    } ?>
                                </div>
                            </div>

                            <!-- SERVING SIZE -->
                            <div class="form_row">
                                <div class="two_columns">
                                    <label for="servingSize">SERVING SIZE* (in ounces)</label>
                                    <input type="number" id="servingSize" name="servingSize" placeholder="6" value="<?php echo $inServingSize ?>" required>
                                    <?php if ($invalidServingSize) {
                                        echo "<span style='color: red;'>Please enter a valid Serving Size. Only 1 to 100 ounces are allowed.</span>";
                                    } ?>
                                </div>

                                <!-- CALORIES -->
                                <div class="two_columns">
                                    <label for="calories">CALORIES* (per serving)</label>
                                    <input type="number" id="calories" name="calories" placeholder="425" value="<?php echo $inCalories ?>" required>
                                    <?php if ($invalidCalories) {
                                        echo "<span style='color: red;'>Please enter a valid Calorie. Only 5 to 3000 are allowed.</span>";
                                    } ?>
                                </div>
                            </div>
                        </div>

                        <!-- ALLERGIES GROUP -->
                        <div class="form_group">
                            <div id="allergy_container">
                                <div class="form_row" style="padding-bottom: 0px;">
                                    <div class="one_column">
                                        <?php if ($invalidAllergy) {
                                            echo "<span style='color: red;'>Please enter valid Allergies. Only letters, spaces, and an empty field are allowed.</span>";
                                        } ?>
                                        <label for="allergy_1">ALLERGIES</label>
                                        <input type="text" name="allergy[]" id="allergy_1" placeholder="Peanuts">
                                    </div>
                                </div>
                            </div>
                            <div class="form_row" style="padding-top: 30px;">
                                <button type="button" id="add_allergy">+ Add another Allergy</button>
                            </div>
                        </div>

                        <!-- INGREDIENTS GROUP -->
                        <div class="form_group">
                            <div id="ingredient_container">
                                <?php if ($invalidQuantity) {
                                    echo "<span style='color: red;'>Please enter a valid Ingredient Quantity. Only 1 to 10 are allowed.</span>";
                                } ?>

                                <?php if ($invalidIngredientName) {
                                    echo "<span style='color: red;'>Please select a valid Ingredient. If the Quantity is greater than 0, an Ingredient must be selected.</span>";
                                } ?>
                                <label for="ingredientSelect_1">INGREDIENTS* (For 1 Serving)</label>
                                <div class="form_row">
                                    <div id="ingredientImg_1" class="ingrediantIconColumn"></div>
                                    <div class="ingrediantSelectColumn">
                                        <select name="ingredient[]" id="ingredientSelect_1" required>
                                            <option value="">Select an Ingredient</option>
                                            <?php
                                            foreach ($ingredients as $ingredient) {
                                                echo "<option value='{$ingredient['ingredient_name']}'>{$ingredient['ingredient_name']}</option>";
                                            } ?>
                                        </select>
                                    </div>
                                    <div class="ingrediantQuantityColumn">
                                        <label for="quantity_1"></label>
                                        <input type="number" name="quantity[]" id="quantity_1" placeholder="Qt">
                                    </div>
                                </div>
                            </div>

                            <div class="form_row" style="padding-top: 30px;">
                                <button type="button" id="add_ingredient">+ Add another
                                    Ingredient</button>
                            </div>
                        </div>

                        <!-- RECIPE STEPS GROUP -->
                        <div class="form_group">
                            <div id="recipeStep_container">
                                <div class="form_row" style="padding-bottom: 0px;">
                                    <div class="one_column">
                                        <?php if ($invalidStepTitle) {
                                            echo "<span style='color: red;'>Please enter valid Step Titles. { and } are not allowed.</span>";
                                        } ?>

                                        <?php if ($invalidStepDescription) {
                                            echo "<span style='color: red;'>Please enter valid Step Descriptions. { and } are not allowed.</span>";
                                        } ?>

                                        <?php if ($invalidRecipeSteps) {
                                            echo "<span style='color: red;'>Please enter valid Step Title. If Step Description has a value, Step Title must also have a value.</span>";
                                        } ?>
                                        <label for="recipeStep_1">RECIPE STEPS*: TITLE & DESCRIPTION</label>
                                        <input type="text" name="recipeSteps[]" id="recipeStep_1" placeholder="Cut the Carrots" required>
                                    </div>
                                </div>
                                <div class="form_row">
                                    <div class="one_column">
                                        <label for="recipeDescription_1"></label>
                                        <textarea name="recipeDescriptions[]" id="recipeDescription_1" placeholder="Wash and cut the carrots into small, bite-sized pieces." required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form_row" style="padding-top: 30px;">
                                <button type="button" id="add_recipe_step">+ Add another
                                    Step</button>
                            </div>
                        </div>

                        <!-- SUBMIT AND RESET BUTTONS -->
                        <div class="form_group">
                            <div class="form_row">
                                <div class="two_columns reset-button">
                                    <input type="reset" value="Reset">
                                </div>
                                <div class="two_columns submit-button">
                                    <input type="submit" name='submit' value="Submit">
                                </div>
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