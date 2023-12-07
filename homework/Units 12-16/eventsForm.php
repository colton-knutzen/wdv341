<?php
session_start();

if (!isset($_SESSION['validUser']) || $_SESSION['validUser'] !== true) {
    header('Location: login.php');
    exit();
};

$confirmMessage = false;

require '../../dbConnect.php';

if (isset($_POST['submit'])) {
    if (empty($_POST['events_location'])) {

        $inEventsName = $_POST['events_name'];
        $inEventsDescription = $_POST['events_description'];
        $inEventsPresenter = $_POST['events_presenter'];
        $inEventsDate = $_POST['events_date'];
        $inEventsTime = $_POST['events_time'];

        $sql = "INSERT INTO wdv341_events";
        $sql .= "(events_name, events_description, events_presenter, events_date, events_time, events_date_entered, events_date_updated)";
        $sql .= " VALUES ";
        $sql .= "(:inEventsName, :inEventsDescription, :inEventsPresenter, :inEventsDate, :inEventsTime, :inDateEntered, :inDateModified)";

        $stmt = $conn->prepare($sql);

        $today = date("Y-m-d");

        $stmt->bindParam(':inEventsName', $inEventsName);
        $stmt->bindParam(':inEventsDescription', $inEventsDescription);
        $stmt->bindParam(':inEventsPresenter', $inEventsPresenter);
        $stmt->bindParam(':inEventsDate', $inEventsDate);
        $stmt->bindParam(':inEventsTime', $inEventsTime);
        $stmt->bindParam(':inDateEntered', $today);
        $stmt->bindParam(':inDateModified', $today);

        $stmt->execute();

        $confirmMessage = true;
    } else {
        die("Suspicious activity has been detected. Further suspicious attempts will result in an IP ban.");
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    .events_location {
        display: none;
    }
    body {
        text-align: center;
    }
</style>

<body>
    <?php
    if ($confirmMessage) {
    ?>
        <div class="confirmMessage">
            <h2>Thank you. The data has been added to the Events Table.</h2>
            <p><a href="adminPanel.php">Return to Admin Panel</a></p>
        </div>
    <?php
    } else {
    ?>
        <form method="post" action="eventsForm.php">
            <p>
                <label for="events_name">Event Name: </label>
                <input type="text" id="events_name" name="events_name" required>
            </p>

            <p>
                <label for="events_description">Event Description: </label>
                <textarea id="events_description" name="events_description" required></textarea>
            </p>

            <p>
                <label for="events_presenter">Event Presenter: </label>
                <input type="text" id="events_presenter" name="events_presenter" required>
            </p>

            <p class="events_location">
                <label for="events_location">Event Location: </label>
                <input type="text" id="events_location" name="events_location">
            </p>

            <p>
                <label for="events_date">Event Date: </label>
                <input type="date" id="events_date" name="events_date" required>
            </p>

            <p>
                <label for="events_time">Event Time: </label>
                <input type="time" id="events_time" name="events_time" required>
            </p>

            <p>
                <button type="submit" name="submit">Enter the Event</button>
            </p>
        </form>
    <?php
    }
    ?>
</body>

</html>