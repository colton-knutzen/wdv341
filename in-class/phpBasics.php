<?php
/*
    MVC - a very simple way to do it within a page
    Model - data, variables usually put it near top of the page
    Controller - the rest of the top of the page, under the page
    View - HTML section; keep as much PHP code OUT of the view as possible
*/

    $studentName = "Mary";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script>
        let studentName = "Dan";

        let studentName2 = "<?php echo $studentName; ?>";

        let cars = ["Chevy", "Ford"];
    </script>

    <p>Welcome <?php echo $studentName; ?></p>
    <p>JavaScript thinks your name is <script>document.write(studentName2);</script> </p>
</head>
<body>
    
</body>
</html>