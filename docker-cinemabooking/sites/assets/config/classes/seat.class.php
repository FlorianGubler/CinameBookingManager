<?php
    class Seat{
        public $row;
        public $col;
        public $except;
        public $reservated;

        public function __construct($row, $col, $except)
        {
            $this->row = $row;
            $this->col = $col;
            $this->except = $except;
        }
    }
?>