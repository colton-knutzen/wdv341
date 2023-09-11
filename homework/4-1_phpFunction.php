<?php
function mm_dd_yyyy($dateObject) {
    $formattedDate = date_format($dateObject, 'm/d/Y');
    echo $formattedDate;
}

function dd_mm_yyyy ($dateObject) {
    $formattedDate = date_format($dateObject, 'd/m/Y');
    echo $formattedDate;
}

/* These functions work, but I found a better way to pull the current date rather than using javaScript
function mm_dd_yyyy($m_d_y_Timestamp)
{
    $m_d_y_Timestamp = strtotime($m_d_y_Timestamp);
    $formattedDate = date('m/d/Y', $m_d_y_Timestamp); //format date to mm/dd/yyyy
    echo $formattedDate;
}

function dd_mm_yyyy($d_m_y_Timestamp)
{
    $d_m_y_Timestamp = strtotime($d_m_y_Timestamp);
    $formattedDate = date('d/m/Y', $d_m_y_Timestamp); //format date to dd/mm/yyyy
    echo $formattedDate;
}
*/

function stringManipulation($stringInput)
{
    $stringInput = trim($stringInput); //removes leading and trailing whitespace

    $stringLength = strlen($stringInput); //counts the number of characters in the string
    echo "There are $stringLength characters in the input";

    $lowercaseString = strtolower($stringInput); //string converted to all lowercase
    echo "<p>The string has been converted to lowercase: $lowercaseString </p>";

    $containDMACC = stripos($stringInput, "DMACC") !== false;

    if ($containDMACC) {
        echo "The string contains 'DMACC'";
    } else {
        echo "The string does not contain 'DMACC'";
    }
}

function phoneNumber($numberInput)
{
    $formattedPhoneNumber = preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', $numberInput);
    echo $formattedPhoneNumber;
}

function usCurrency($moneyNumInput)
{
    echo '$' . number_format($moneyNumInput, 2);
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4-1: PHP Functions</title>
    <script>
        /* Not used anymore
        const currentDate = new Date(); //pulls the date from the users console
        //const stringDate = String(currentDate);  //Tried to create a javascript variable to pass into php as a parameter, but it seems more advanced than we currently are

        function displayCurrentDate() {
            document.write("Today's Date is: " + currentDate.toDateString());
        }
        */
    </script>
</head>

<body>
    <h1>4-1: PHP Functions</h1>
    <p>PHP has converted the current date into a mm/dd/yyyy format: <?php mm_dd_yyyy(date_create()); ?></p> <!--Creates and passes in a current date object--> 
    <p>PHP has converted the current date into a dd/mm/yyyy format: <?php dd_mm_yyyy(date_create()); ?></p>
    <p>This string input is:' welcome to dMacc '</p>
    <?php stringManipulation(' welcome to dMacc '); ?>
    <p>This string input is:'CASTLES ARE SO COOL'</p>
    <?php stringManipulation('CASTLES ARE SO COOL'); ?>
    <p>The phone number is: <?php phoneNumber('1234567890'); ?></p>
    <p>The money is US currency is: <?php usCurrency('123456'); ?></p>
</body>

</html>