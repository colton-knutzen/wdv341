<?php
session_start();

if (!isset($_SESSION['validUser']) || $_SESSION['validUser'] !== true) {
    header('Location: login.php');
    exit();
};

$confirmMessage = false;

require '../../dbConnect.php';

$sql = "SELECT events_id, events_name, events_description, events_presenter, events_date, events_time, events_date_entered, events_date_updated FROM wdv341_events";
$stmt = $conn->prepare($sql);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $events_id = $_POST['events_id'];

    $sql_delete = "DELETE FROM wdv341_events WHERE events_id = :events_id";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bindParam(':events_id', $events_id);
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
    <style>
        body {
            text-align: center;
        }

        .eventBox {
            border: thin solid black;
            margin-bottom: 20px;
        }

        .boldEvent {
            font-weight: bold;
        }
    </style>

</head>

<body>

    <?php
    if ($confirmMessage) {
    ?>
        <div class="confirmMessage">
            <h2>Thank you. The Event has been Deleted.</h2>
            <p><a href="adminPanel.php">Return to Admin Panel</a></p>
        </div>
    <?php
    } else {
    ?>

        <?php foreach ($events as $event) : ?>
            <div class='eventBox'>
                <h3><?= $event['events_name'] ?></h3>

                <p><span class='boldEvent'>Event ID: </span><?= $event['events_id'] ?></p>
                <p><span class='boldEvent'>Event Description: </span><?= $event['events_description'] ?></p>
                <p><span class='boldEvent'>Event Presenter: </span><?= $event['events_presenter'] ?></p>
                <p><span class='boldEvent'>Event Date: </span><?= $event['events_date'] ?></p>
                <p><span class='boldEvent'>Event Time: </span><?= $event['events_time'] ?></p>
                <p><span class='boldEvent'>Event Date Entered: </span><?= $event['events_date_entered'] ?></p>
                <p><span class='boldEvent'>Event Date Updated: </span><?= $event['events_date_updated'] ?></p>

                <p><form action="deleteEvents.php" method="post" onsubmit="return confirm('Are you sure you want to DELETE this EVENT from the database? IT WILL BE GONE FOREVER!');">
                    <input type="hidden" name="events_id" value="<?= $event['events_id'] ?>">
                    <button type="submit">Delete</button>
                </form></p>
            </div>
        <?php endforeach; ?>

    <?php
    }
    ?>

</body>

</html>