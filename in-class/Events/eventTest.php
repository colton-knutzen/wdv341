<?php
/*
    This program will prove the Events class
    Create an event object from the Events class
    Run ALL methods of the new events object to verify they work as expected
*/

    include 'Events.php'; //imports (copy/paste) all the content in Events.php right here

    //create a new events object
    $eventsObject = new Events();

    $eventsObject->set_event_description("Event Description for WDV341");
    echo $eventsObject->get_event_description("WDV341");

    echo "<br>";
    $eventsObject->set_event_name("WDV341");
    echo $eventsObject->get_event_name("WDV341");

    echo json_encode($eventsObject);
    
?>