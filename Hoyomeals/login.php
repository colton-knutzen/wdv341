<?php
session_start();

//Anyone can view the login page, but if they are already logged in, it will display the loginMessage rather than the form. Prevents users from logging in twice or as dual accounts
if (isset($_COOKIE['userLoggedIn']) && $_COOKIE['userLoggedIn'] == 'valid') {
    $loginMessage = true;
}

//success submission message
$loginMessage = false;

//username and password don't match on the server
$invalidLogin = false;

//failed a specific input messages
$invalidUsernameMessage = false;
$invalidPasswordMessage = false;

//Global Variables to maintain state in form. Username only
$username = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loginKeyword = $_POST['loginKeyword'];
    if (empty($loginKeyword)) {

        //This variable is used rather than exit when one of the validations fails. By using this, I continue with the entire form validation, display all invalid messages that fail validation, but not processing the form. And using exit creates a blank screen.
        $proceedWithFormProcessing = true;

        //Catch to maintain form state
        $username = $_POST['username'];
        $password = $_POST['password'];

        //Username validation - accepts everything except spaces
        if (strpos($username, ' ') !== false) {
            $invalidUsernameMessage = true;
            $proceedWithFormProcessing = false;
        };

        //Password validation - accepts everything except spaces
        if (strpos($password, ' ') !== false) {
            $invalidPasswordMessage = true;
            $proceedWithFormProcessing = false;
        };

        if ($proceedWithFormProcessing) {
            require 'dbConnect.php';

            //For some reason, if I don't catch the $_POST again, it fails the validation on the server. $username becomes root.
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Validate user credentials against the database
            $sql = "SELECT user_role, user_name, user_password FROM hoyomeals_users WHERE user_name = :username AND user_password = :password";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Valid user - sets a userLoggedIn cookie and session variable to valid
                setcookie('userLoggedIn', 'valid', time() + (7 * 24 * 3600), '/'); // Cookie valid for 1 week
                setcookie('username', $user['user_name'], time() + (7 * 24 * 3600), '/');
                $_SESSION['userLoggedIn'] = 'valid';

                //Valid admin - also sets an adminLoggedIn cookie and session variable to valid
                if ($user['user_role'] == 'admin') {
                    setcookie('adminLoggedIn', 'valid', time() + 3600, '/'); // Cookie valid for 1 week
                    $_SESSION['adminLoggedIn'] = 'valid';
                }

                //Redirects to homepage after valid login
                header('Location: index.html');
                exit;
            } else {
                //Invalid user - displays invalid message
                $invalidLogin = true; 
            }
        }
    } else {
        die("Suspicious activity has been detected. Further suspicious attempts will result in an IP ban.");
    }
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

    <title>Hoyomeals - Login</title>
    <script>
        //When the eye in the password field is clicked, is changes the password input type from password to text. And changes the eye icon to either open or close
        function displayPassword() {
            let eyeicon = document.querySelector("#eyeicon");
            let password = document.querySelector("#password");

            eyeicon.onclick = function() {
                if (password.type == "password") {
                    password.type = "text";
                    eyeicon.src = "img/icon/eye-open.png"
                } else {
                    password.type = "password";
                    eyeicon.src = "img/icon/eye-close.png"
                };
            };
        };
    </script>
</head>

<body onload="displayPassword()">
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
                if ($loginMessage) {
                ?>
                    <div class="confirmMessage">
                        <h2>You're already logged in.</h2>
                    </div>
                <?php
                } else {
                ?>
                    <form class="submit_recipe_form_styles" method="post" action="login.php" enctype="multipart/form-data" accept-charset="">
                        <div class="form_group">
                            <div class="form_row">
                                <div class="one_column">
                                    <label for="username">USERNAME</label>
                                    <input type="text" id="username" name="username" value="<?php echo $username ?>" required>
                                    <?php if ($invalidUsernameMessage) {
                                        echo "<div style='color: red;'>Please enter a valid Username. Spaces aren't allowed.</div>";
                                    } ?>
                                </div>
                            </div>

                            <div class="form_row">
                                <div class="one_column">
                                    <label for="password">PASSWORD</label>
                                    <div class="password-wrapper">
                                        <input type="password" id="password" name="password" required>
                                        <img class="passwordEye" src="img/icon/eye-close.png" id="eyeicon" alt="">
                                    </div>
                                    <?php if ($invalidPasswordMessage) {
                                        echo "<div style='color: red;'>Please enter a valid Password. Spaces aren't allowed.</div>";
                                    } ?>
                                </div>
                            </div>
                        </div>

                        <div class="loginKeyword">
                            <label for="loginKeyword">KEYWORD</label>
                            <input type="text" name="loginKeyword" id="loginKeyword">
                        </div>

                        <div class="form_row">
                            <div class="one_column">
                                <input style="color: green;" type="submit" name='submit' value="Login">
                            </div>
                        </div>
                        <?php if ($invalidLogin) {
                            echo "<div style='color: red; text-align: center'>Password or Username is invalid. Please try again.</div>";
                        } ?>
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