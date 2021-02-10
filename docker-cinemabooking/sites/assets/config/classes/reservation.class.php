<?php
    class Reservation{
        public $id;
        public $mv_time;
        public $reservation_user;
        public $movie;
        public $reservated_seats = array();

        public function __construct($id, $movie, $mv_time, $reservation_user)
        {
            $this->mv_time = $mv_time;
            $this->movie = $movie;
            $this->id = $id;
            $this->reservation_user = $reservation_user;
        }

        public function safetodb($dbconnection){
            
        }
    }

    class Reservation_User{
        public $firstname;
        public $lastname;
        public $reservation_seats = array();

        public function __construct($firstname, $lastname)
        {
            $this->firstname = $firstname;
            $this->lastname = $lastname;
        }
    }
?>