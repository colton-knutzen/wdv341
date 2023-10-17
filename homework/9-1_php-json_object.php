<?php
require '../dbConnect.php';
try {
    //SQL command
    $sql = "SELECT events_name, events_description, events_presenter, events_date, events_time FROM wdv341_events WHERE events_id=:recId";

    //Prepare statement
    $stmt = $conn->prepare($sql);

    //Parameters
    $recID = 3;
    $stmt->bindParam(':recId', $recID);

    //Execute
    $stmt->execute();

    //Fetch into associated array
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<p> Error: ' . $e->getMessage() . '</p>';
}

//Manual input class and object
class Events_manaul
{
    public $events_name;
    public $events_description;
    public $events_presenter;
    public $events_date;
    public $events_time;

    function __construct(){}
}

$outputObj_manual = new Events_manaul();

$outputObj_manual->events_name = $result['events_name'];
$outputObj_manual->events_description = $result['events_description'];
$outputObj_manual->events_presenter = $result['events_presenter'];
$outputObj_manual->events_date = $result['events_date'];
$outputObj_manual->events_time = $result['events_time'];

$jsonOutput_manual = json_encode($outputObj_manual);


//Setters and getters class and object
class Events_setters
{
    private $events_name;
    private $events_description;
    private $events_presenter;
    private $events_date;
    private $events_time;

    function __construct(){}

    //since the properties are private, the json_encode can't access them when it's an object. This function converts the object into a string array right before the json_encode
    public function toArray() {
        return [
            'events_name' => $this->events_name,
            'events_description' => $this->events_description,
            'events_presenter' => $this->events_presenter,
            'events_date' => $this->events_date,
            'events_time' => $this->events_time,
        ];
    }

    public function set_events_name($inName){
        $this->events_name = $inName;
    }
    public function get_events_name(){
        return $this->events_name;
    }

    public function set_events_description($inDesc){
        $this->events_description = $inDesc;
    }
    public function get_events_description(){
        return $this->events_description;
    }

    public function set_events_presenter($inPres){
        $this->events_presenter = $inPres;
    }
    public function get_events_presenter(){
        return $this->events_presenter;
    }

    public function set_events_date($inDate){
        $this->events_date = $inDate;
    }
    public function get_events_date(){
        return $this->events_date;
    }

    public function set_events_time($inTime){
        $this->events_time = $inTime;
    }
    public function get_events_time(){
        return $this->events_time;
    }
}

$outputObj_setters = new Events_setters();

$outputObj_setters->set_events_name($result['events_name']);
$outputObj_setters->set_events_description($result['events_description']);
$outputObj_setters->set_events_presenter($result['events_presenter']);
$outputObj_setters->set_events_date($result['events_date']);
$outputObj_setters->set_events_time($result['events_time']);

$jsonOutput_setters = json_encode($outputObj_setters->toArray());

//Constructor class and object 
class Events_constructor
{
    private $events_name;
    private $events_description;
    private $events_presenter;
    private $events_date;
    private $events_time;

    public function __construct($events_name, $events_description, $events_presenter, $events_date, $events_time){
        $this->events_name = $events_name;
        $this->events_description = $events_description;
        $this->events_presenter = $events_presenter;
        $this->events_date = $events_date;
        $this->events_time = $events_time;
    } 

    //since the properties are private, the json_encode can't access them when it's an object. This function converts the object into a string array right before the json_encode
    public function toArray() {
        return [
            'events_name' => $this->events_name,
            'events_description' => $this->events_description,
            'events_presenter' => $this->events_presenter,
            'events_date' => $this->events_date,
            'events_time' => $this->events_time,
        ];
    }

}

$outputObj_constructor = new Events_constructor($result['events_name'], $result['events_description'], $result['events_presenter'], $result['events_date'], $result['events_time']);

$jsonOutput_constructor = json_encode($outputObj_constructor->toArray());

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .spacing{
            margin-top: 150px;
        }
    </style>
</head>

<body>
    <h1>WDV341: Intro to PHP</h1>
    <h2>9-1: PHP-JSON Object</h2>
    <?php
    //Fetch test
    /*while ($row = $stmt->fetch()) {      //$row is an associative array
        echo "<div><h3>";
        echo $row["events_name"];
        echo "</h3>";
        echo "<p>";
        echo $row["events_description"];
        echo "</p>";
        echo "<p>";
        echo $row["events_presenter"];
        echo "</p>";
        echo "<p>";
        echo $row["events_date"];
        echo "</p>";
        echo "<p>";
        echo $row["events_time"];
        echo "</p>";
        echo "</div>\n";        // \n puts the next on a new line
    }*/
    echo "<p><h3>This JSON object was created by having public properties and manually entering in the values.</h3> ";
    echo $jsonOutput_manual;
    echo "</p>";

    echo "<p class='spacing'><h3>This JSON object was created by having private properties and using setters and getters.</h3> ";
    echo $jsonOutput_setters;
    echo "</p>";

    echo "<p class='spacing'><h3>This JSON object was created by having private properties and using the constructor method.</h3> ";
    echo $jsonOutput_constructor;
    echo "</p>";

    ?>

</body>

</html>