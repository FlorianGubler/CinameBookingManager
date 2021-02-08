<?php
    class Room{
        public $id;
        public $number;
        public $seats = array();

        public function __construct($id, $number)
        {
            $this->id = $id;
            $this->number = $number;
        }
    }
?>