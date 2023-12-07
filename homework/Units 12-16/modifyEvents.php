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
    $eventsID = $_POST['event_id'];

    $eventsName = $_POST['events_name_' . $eventsID];
    $eventsDescription = $_POST['events_description_' . $eventsID];
    $eventsPresenter = $_POST['events_presenter_' . $eventsID];
    $eventsDate = $_POST['events_date_' . $eventsID];
    $eventsTime = $_POST['events_time_' . $eventsID];

    $sql = "UPDATE wdv341_events SET 
            events_name = :eventsName,
            events_description = :eventsDescription,
            events_presenter = :eventsPresenter,
            events_date = :eventsDate,
            events_time = :eventsTime,
            events_date_updated = NOW()
            WHERE events_id = :eventsID";

    $stmt_update = $conn->prepare($sql);
    $stmt_update->bindParam(':eventsName', $eventsName);
    $stmt_update->bindParam(':eventsDescription', $eventsDescription);
    $stmt_update->bindParam(':eventsPresenter', $eventsPresenter);
    $stmt_update->bindParam(':eventsDate', $eventsDate);
    $stmt_update->bindParam(':eventsTime', $eventsTime);
    $stmt_update->bindParam(':eventsID', $eventsID);
    $stmt_update->execute();

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
            <h2>Thank you. The Event has been Modified.</h2>
            <p><a href="adminPanel.php">Return to Admin Panel</a></p>
        </div>
    <?php
    } else {
    ?>

        <?php foreach ($events as $event) :
            $eventsID = $event['events_id'];
        ?>
            <div class='eventBox'>
                <form action="modifyEvents.php" method="post">
                    <h3>Event ID: <?php echo $eventsID ?></h3>

                    <p>
                        <label class='boldEvent'>Event Name: </label><input type="text" name="events_name_<?php echo $eventsID ?>" id="events_name_<?php echo $eventsID ?>" value="<?= $event['events_name'] ?>">
                    </p>

                    <p>
                        <label class='boldEvent'>Event Description: </label><textarea name="events_description_<?php echo $eventsID ?>" id="events_description_<?php echo $eventsID ?>"><?= $event['events_description'] ?></textarea>
                    </p>

                    <p>
                        <label class='boldEvent'>Event Presenter: </label><input type="text" name="events_presenter_<?php echo $eventsID ?>" id="events_presenter_<?php echo $eventsID ?>" value="<?= $event['events_presenter'] ?>">
                    </p>

                    <p>
                        <label class='boldEvent'>Event Date: </label><input type="date" name="events_date_<?php echo $eventsID ?>" id="events_date_<?php echo $eventsID ?>" value="<?= $event['events_date'] ?>">
                    </p>

                    <p>
                        <label class='boldEvent'>Event Time: </label><input type="time" name="events_time_<?php echo $eventsID ?>" id="events_time_<?php echo $eventsID ?>" value="<?= $event['events_time'] ?>">
                    </p>

                    <p>
                        <input type="hidden" name="event_id" value="<?php echo $event['events_id']; ?>">
                        <button type="submit">Modify</button>
                    </p>

                </form>
            </div>
        <?php endforeach; ?>

    <?php
    }
    ?>

</body>

</html>