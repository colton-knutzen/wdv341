<?php
$yourName = "Colton";
$number1 = 10;
$number2 = 20;
$total = $number1 + $number2;

$PHPLangArray = array("PHP", "HTML", "JavaScript");

/* Old. Used Json
$javaScriptArray = json_encode($indexedLangArray);
*/
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<script>
    function displayArray() {
        let JSLangArray = [<?php foreach ($PHPLangArray as $value) {
                                echo "'$value',";
                            } ?>]
        let ul = document.querySelector("ul");

        for (let i = 0; i < JSLangArray.length; i++) {
            let li = document.createElement("li");
            li.textContent = JSLangArray[i];
            ul.appendChild(li);
        }
    }
    /* Old. Used Javascript Loop rather than PHP loop.
    let langArray = ;

    function displayArray() {
        let ul = document.querySelector("ul");

        for (let i = 0; i < langArray.length; i++) {
            let li = document.createElement("li");
            li.textContent = langArray[i];
            ul.appendChild(li);
        }
    }*/
</script>

<body>
    <?php echo "<h1>WDV341 2-1: PHP Basics</h1>"; ?>
    <h2><?php echo $yourName; ?></h2>

    <p>
        Number 1: <?php echo $number1; ?>
        <br>Number 2: <?php echo $number2; ?>
        <br>Total: <?php echo $total; ?>
    </p>

    <ul id="displayArray">
        <script>
            displayArray();
        </script>
    </ul>

</body>

</html>