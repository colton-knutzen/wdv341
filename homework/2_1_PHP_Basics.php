<?php
$yourName = "Colton";
$number1 = 10;
$number2 = 20;
$total = $number1 + $number2;
$indexedLangArray_1 = ["PHP", "HTML", "JavaScript"];
$indexedLangArray_2 = array("PHP", "HTML", "JavaScript");

// Convert the PHP array to a JavaScript array
$javaScriptArray = json_encode($indexedLangArray_1);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<script>
    // Define langArray in the global JavaScript scope
    let langArray = <?php echo $javaScriptArray; ?>;

    function displayArray() {
        let ul = document.querySelector("ul");

        for (let i = 0; i < langArray.length; i++) {
            let li = document.createElement("li");
            li.textContent = langArray[i];
            ul.appendChild(li);
        }
    }
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
