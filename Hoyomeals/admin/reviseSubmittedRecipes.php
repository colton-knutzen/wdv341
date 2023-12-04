<?php
session_start();

//only admins are allowed
if (!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== 'valid') {
    header('Location: login.php');
    exit();
};

$confirmMessage = false;

require '../dbConnect.php';

//Display user submitted recipes from temp database
$sql = "SELECT recipe_idTemp, recipe_categoryTemp, recipe_nameTemp, recipe_descriptionTemp, recipe_imgTemp, recipe_difficultyTemp, recipe_prepTimeTemp, recipe_cookTimeTemp, recipe_servingSizeTemp, recipe_caloriesTemp, recipe_allergyTemp, recipe_ingredientsTemp, recipe_stepsTemp, recipe_authorNameTemp, recipe_authorEmailTemp, recipe_dateModifiedTemp, recipe_dateEnteredTemp FROM hoyomeals_recipes_temp";
$stmt = $conn->prepare($sql);
$stmt->execute();
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipe_id = $_POST['recipe_id'];

    //Retrive data of the selected recipe from the temporary database
    $sql_select = "SELECT recipe_categoryTemp, recipe_nameTemp, recipe_descriptionTemp, recipe_imgTemp, recipe_difficultyTemp, recipe_prepTimeTemp, recipe_cookTimeTemp, recipe_servingSizeTemp, recipe_caloriesTemp, recipe_allergyTemp, recipe_ingredientsTemp, recipe_stepsTemp, recipe_authorNameTemp, recipe_authorEmailTemp, recipe_dateModifiedTemp, recipe_dateEnteredTemp FROM hoyomeals_recipes_temp WHERE recipe_idTemp = :recipe_id";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bindParam(':recipe_id', $recipe_id);
    $stmt_select->execute();
    $recipe_data = $stmt_select->fetch(PDO::FETCH_ASSOC);

    //Insert into the live database
    $sql_insert = "INSERT INTO hoyomeals_recipes";
    $sql_insert .= "(recipe_category, recipe_name, recipe_description, recipe_img, recipe_difficulty, recipe_prepTime, recipe_cookTime, recipe_servingSize, recipe_calories, recipe_allergy, recipe_ingredients, recipe_steps, recipe_authorName, recipe_authorEmail, recipe_dateModified, recipe_dateEntered)";
    $sql_insert .= " VALUES ";
    $sql_insert .= "(:category, :name, :description, :img, :difficulty, :prepTime, :cookTime, :servingSize, :calories, :allergy, :ingredients, :steps, :authorName, :authorEmail, :dateModified, :dateEntered)";

    $stmt_insert = $conn->prepare($sql_insert);

    $today = date("Y-m-d");

    $stmt_insert->bindParam(':category', $recipe_data['recipe_categoryTemp']);
    $stmt_insert->bindParam(':name', $recipe_data['recipe_nameTemp']);
    $stmt_insert->bindParam(':description', $recipe_data['recipe_descriptionTemp']);
    $stmt_insert->bindParam(':img', $recipe_data['recipe_imgTemp']);
    $stmt_insert->bindParam(':difficulty', $recipe_data['recipe_difficultyTemp']);
    $stmt_insert->bindParam(':prepTime', $recipe_data['recipe_prepTimeTemp']);
    $stmt_insert->bindParam(':cookTime', $recipe_data['recipe_cookTimeTemp']);
    $stmt_insert->bindParam(':servingSize', $recipe_data['recipe_servingSizeTemp']);
    $stmt_insert->bindParam(':calories', $recipe_data['recipe_caloriesTemp']);
    $stmt_insert->bindParam(':allergy', $recipe_data['recipe_allergyTemp']);
    $stmt_insert->bindParam(':ingredients', $recipe_data['recipe_ingredientsTemp']);
    $stmt_insert->bindParam(':steps', $recipe_data['recipe_stepsTemp']);
    $stmt_insert->bindParam(':authorName', $recipe_data['recipe_authorNameTemp']);
    $stmt_insert->bindParam(':authorEmail', $recipe_data['recipe_authorEmailTemp']);
    $stmt_insert->bindParam(':dateModified', $today);
    $stmt_insert->bindParam(':dateEntered', $recipe_data['recipe_dateEnteredTemp']);

    $stmt_insert->execute();

    //Delete from temporary database
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

    <title>Hoyomeals: Revise Submitted Recipes</title>
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
                        <h2>Thank you. Recipe has been Approved, Deleted from the Temporary Database, and Added to the Live Database.</h2>
                    </div>
                <?php
                } else {
                ?>
                    <div class="page-title">
                        <h1 style="text-align: center;">Approve or Decline User Submitted Recipes</h1>
                    </div>
                    <?php foreach ($recipes as $recipe) : ?>
                        <div class="revise_user_submitted_recipes dbTableDisplay">
                            <!-- ID -->
                            <div class="ID grid_title gridBorder_top_left_right">ID</div>
                            <div class="id_data grid_content gridBorder_left_right"><?= $recipe['recipe_idTemp'] ?></div>

                            <!-- Date Entered -->
                            <div class="Date-Entered grid_title gridBorder_top_left_right">Date Entered</div>
                            <div class="date_entered_data grid_content gridBorder_left_right"><?= $recipe['recipe_dateEnteredTemp'] ?></div>

                            <!-- Date Modified -->
                            <div class="Date-Modified grid_title gridBorder_top_left_right"> Date Modified</div>
                            <div class="date_modified_data grid_content gridBorder_left_right"><?= $recipe['recipe_dateModifiedTemp'] ?></div>

                            <!-- Author Name -->
                            <div class="Author-Name grid_title gridBorder_top_left_right">Author Name</div>
                            <div class="author_name_data grid_content gridBorder_left_right"><?= ucfirst($recipe['recipe_authorNameTemp']) ?></div>

                            <!-- Author Email -->
                            <div class="Author-Email grid_title gridBorder_top_left_right">Author Email</div>
                            <div class="author_email_data grid_content gridBorder_left_right"><?= $recipe['recipe_authorEmailTemp'] ?></div>

                            <!-- Allergy. Decode from json and display each on it's own line -->
                            <div class="Allergies grid_title gridBorder_right">Allergies</div>
                            <div class="allergies_data grid_content gridBorder_bottom_right">
                                <?php
                                $allergyData = json_decode($recipe['recipe_allergyTemp'], true);
                                if (isset($allergyData['allergies']) && is_array($allergyData['allergies'])) {
                                    foreach ($allergyData['allergies'] as $allergy) {
                                        echo ucfirst($allergy) . '<br>';
                                    }
                                }
                                ?></div>

                            <!-- Category -->
                            <div class="Category grid_title gridBorder_top_right">Category</div>
                            <div class="category_data grid_content gridBorder_right"><?= ucfirst($recipe['recipe_categoryTemp']) ?></div>

                            <!-- Name -->
                            <div class="Name grid_title gridBorder_top_right">Recipe Name</div>
                            <div class="name_data grid_content gridBorder_right"><?= ucfirst($recipe['recipe_nameTemp']) ?></div>

                            <!-- Description -->
                            <div class="Description grid_title gridBorder_top_right">Description</div>
                            <div class="description_data grid_content gridBorder_bottom_right"><?= ucfirst($recipe['recipe_descriptionTemp']) ?></div>

                            <!-- Image -->
                            <div class="recipe_img grid_content gridBorder_top_right_bottom"><img src="data:image/jpeg;base64,<?= base64_encode($recipe['recipe_imgTemp']) ?>" alt="Recipe Image"></div>

                            <!-- Difficulty -->
                            <div class="Difficulty grid_title">Difficulty</div>
                            <div class="difficulty_data grid_content "><?= $recipe['recipe_difficultyTemp'] ?></div>

                            <!-- Serving Size -->
                            <div class="Serving-Size grid_title gridBorder_right">Serving Size</div>
                            <div class="serving_size_data grid_content gridBorder_bottom_right"><?= $recipe['recipe_servingSizeTemp'] ?> oz</div>

                            <!-- Prep Time -->
                            <div class="Prep-Time grid_title gridBorder_right">Prep Time</div>
                            <div class="prep_time_data grid_content gridBorder_bottom_right"><?= $recipe['recipe_prepTimeTemp'] ?> min</div>

                            <!-- Cook Time -->
                            <div class="Cook-Time grid_title gridBorder_right">Cook Time</div>
                            <div class="cook_time_data grid_content gridBorder_bottom_right"><?= $recipe['recipe_cookTimeTemp'] ?> min</div>

                            <!-- Calories -->
                            <div class="Calories grid_title gridBorder_right">Calories</div>
                            <div class="calories_data grid_content gridBorder_right_left"><?= $recipe['recipe_caloriesTemp'] ?></div>

                            <!-- Ingredients. Decode from json and display each with name and quantity on own line -->
                            <div class="Ingredients grid_title gridBorder_top_left_right">Ingredients</div>
                            <div class="ingredients_data gridBorder_bottom_left_right">
                                <table>
                                    <?php
                                    $ingredientsData = json_decode($recipe['recipe_ingredientsTemp'], true);
                                    if (isset($ingredientsData['ingredients']) && is_array($ingredientsData['ingredients'])) {
                                        foreach ($ingredientsData['ingredients'] as $ingredients) {
                                            echo '<tr class="tr_bottom_border">';
                                            echo '<td>' . ucfirst($ingredients['name']) . '</td>';
                                            echo '<td>x' . $ingredients['quantity'] . '</td>';
                                            echo '</tr>';
                                        }
                                    } ?>
                                </table>
                            </div>

                            <!-- Steps. Decode from json and display each with title and description on own line -->
                            <div class="Steps grid_title gridBorder_top_right">Steps</div>
                            <div class="step_data gridBorder_bottom_right">
                                <table>
                                    <?php
                                    $stepsData = json_decode($recipe['recipe_stepsTemp'], true);
                                    if (isset($stepsData['steps']) && is_array($stepsData['steps'])) {
                                        foreach ($stepsData['steps'] as $steps) {
                                            echo '<tr class="tr_bottom_border">';
                                            echo '<td>' . ucfirst($steps['title']) . '</td>';
                                            echo '<td>' . ucfirst($steps['description']) . '</td>';
                                            echo '</tr>';
                                        }
                                    }
                                    ?>
                                </table>
                            </div>

                            <!-- Approve Button. This is a self-posting form -->
                            <div class="commit_button button_style">
                                <form action="reviseSubmittedRecipes.php" method="post" onsubmit="return confirm('Are you sure you want to APPROVE this recipe? This will ADD it to the LIVE DATABASE and DELETE it from the TEMPORARY DATABASE?');">
                                    <input type="hidden" name="recipe_id" value="<?= $recipe['recipe_idTemp'] ?>">
                                    <button type="submit">Approve</button>
                                </form>
                            </div>

                            <!-- Decline Button. This goes to an external php form. -->
                            <div class="delete_button button_style">
                                <form action="/hoyomeals/php/deleteRowFromTempDB.php" method="post" onsubmit="return confirm('Are you sure you want to DECLINE this recipe? This will DELETE it from the TEMPORARY DATABASE?');">
                                    <input type="hidden" name="recipe_id" value="<?= $recipe['recipe_idTemp'] ?>">
                                    <button type="submit" style="color: red; cursor: pointer;">Decline</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php
                }
                ?>
            </div>
        </main>
        <!-- Start of site skeleton structure -->
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