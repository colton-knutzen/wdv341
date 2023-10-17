<?php
class Events {
    /* Object - is container that holds data, it has properties and methods (tools) that work on the data we can USE an object in our program. We can access/change/delete its contents, etc.

    Properties

    Methods

    Instatiation - creating a new object based upon a Class. Keyword "new"

    Constructor Method - does NOT make a new object.
        it is called when a new object creates and is used to define content on new object
        it often the same name as the class    would expect Event() for this class
    */


    


    /* This class will deine an event object based upon the data from the wdv341_events table
        - change history

        define properties the class will store
        define the constructor method
        define the setters/getters aka accessors/mutators methods
            setters/mutators - set an input into the property of the object/class
            getters/accessors - return the value of a property of an object/class

        define processing methods

    */
    public $event_description;
    public $event_name;
    private $event_presenter;

    //constructor method - this exact constructor setup is unqiue to PHP ! double __
    function __constructor(){}
        //empty constructor will set default values to properties
   
    function set_event_description($inDesc) {
        $this->event_description = $inDesc;
    }
    function get_event_description() {
        return $this->event_description;
    }

    function set_event_name($inName) {
        $this->event_name = $inName;
    }
    function get_event_name() {
        return $this->event_name;
    }

    //processing methods
        //function that will turn php object into JSON object, and return it
        

}//end Events Class
?>