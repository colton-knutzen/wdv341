<?php
$confirmMessage = false;

if (isset($_POST['submit'])) {

    $event_location = $_POST['event_location']; //Honeypot 
    if (empty($event_location)) {

        //process form data into PHP variables only when submit button has been pressed
        $inEventName = $_POST['events_name'];
        $inEventDesc = $_POST['events_description'];
        $inEventPresenter = $_POST['events_presenter'];
        $inEventDate = $_POST['events_date'];
        $inEventTime = $_POST['events_time'];

        //connect to database
        require '../dbConnect.php';

        //mySQL command
        $sql = "INSERT INTO wdv341_events";
        $sql .= "(events_name, events_description, events_presenter, events_date, events_time, events_date_entered, events_date_updated)";
        $sql .= " VALUES ";
        $sql .= "(:eventName, :eventDesc, :eventPresenter, :eventDate, :eventTime, :eventDateEntered,  :eventDateUpdated)";

        //prepared statement
        $stmt = $conn->prepare($sql);

        //current date object
        $today = date("Y-m-d");

        //bind what was inputed with columns in table
        $stmt->bindParam(':eventName', $inEventName);
        $stmt->bindParam(':eventDesc', $inEventDesc);
        $stmt->bindParam(':eventPresenter', $inEventPresenter);
        $stmt->bindParam(':eventDate', $inEventDate);
        $stmt->bindParam(':eventTime', $inEventTime);
        $stmt->bindParam(':eventDateEntered', $today);
        $stmt->bindParam(':eventDateUpdated', $today);

        $stmt->execute();

        $confirmMessage = true;
    } else {
        echo    "<div class='confirmMessage'>
                    <h2>We're sorry, there was an error. Please try submitting again.</h2>
                </div>";
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .confirmMessage {
            width: 500px;
            background-color: skyblue;
            margin-left: auto;
            margin-right: auto;
        }

        .event_location {
            display: none;
        }
    </style>
</head>

<body>
    <h1>WDV341: Intro to PHP</h1>
    <h2>11-1: Self Posting Input Event form with INSERT</h2>

    <?php
    if ($confirmMessage) {
    ?>
        <div class="confirmMessage">
            <h2>Thank you. We have input your information.</h2>
        </div>
    <?php
    } else {
    ?>
        <form method="post" action="11-1_self_posting_input_event_form.php">
            <p>
                <label for="events_name">Event Name: </label>
                <input type="text" name="events_name" id="events_name">
            </p>

            <p>
                <label for="events_description">Event Description: </label>
                <input type="text" name="events_description" id="events_description">
            </p>

            <p>
                <label for="events_presenter">Event Presenter: </label>
                <input type="text" name="events_presenter" id="events_presenter">
            </p>

            <p class="event_location">
                <label for="event_location">Event Location:</label>
                <input type="text" name="event_location" id="event_location">
            </p>

            <p>
                <label for="events_date">Event Date: </label>
                <input type="date" name="events_date" id="events_date">
            </p>

            <p>
                <label for="events_time">Event Time: </label>
                <input type="time" name="events_time" id="events_time">
            </p>

            <p>
                <input type="submit" name="submit" value="Submit">
                <input type="reset">
            </p>
        <?php
    }
        ?>

        </form>


</body>

</html>