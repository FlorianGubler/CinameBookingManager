<?php
    class Seat{
        public $id;
        public $row;
        public $col;
        public $except;
        public $reservated;

        public function __construct($id, $row, $col, $except)
        {
            $this->id = $id;
            $this->row = $row;
            $this->col = $col;
            $this->except = $except;
        }
    }
?>