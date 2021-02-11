<?php
    class Reservation{
        public $mv_time;
        public $reservation_user;
        public $movie;
        public $reservated_seats = array();

        public function __construct($movie, $mv_time, $reservation_user)
        {
            $this->mv_time = $mv_time;
            $this->movie = $movie;
            $this->reservation_user = $reservation_user;
        }

        public function safetodb($dbconnection){
            //Find Movie Time ID
            $sql_find_mov = "SELECT id FROM movie_times WHERE start='".$this->mv_time->start."' AND end='".$this->mv_time->end."';";
            $result_find_mov = $dbconnection->query($sql_find_mov);
            $movid = $result_find_mov->fetch_assoc()['id'];

            //Check if Reservation Users already exist
            $sql_check_resusr = "SELECT * from reservation_users WHERE firstname='".$this->reservation_user->firstname."' AND lastname='".$this->reservation_user->lastname."';";
            $result_check_resusr = $dbconnection->query($sql_check_resusr);
            $row_check_resusr = $result_check_resusr->fetch_assoc();

            //Create Reservation User
            if($row_check_resusr == "" || $row_check_resusr == " "){
                $sql_create_resusr = "INSERT INTO reservation_users (firstname, lastname) VALUES ('".$this->reservation_user->firstname."', '".$this->reservation_user->lastname."');";
                $dbconnection->query($sql_create_resusr);

                //Get Res Users ID
                $sql_find_resusr = "SELECT id from reservation_users WHERE firstname='".$this->reservation_user->firstname."' AND lastname='".$this->reservation_user->lastname."';";
                $result_find_resusr = $dbconnection->query($sql_find_resusr);
                $resuserid = $result_find_resusr->fetch_assoc()['id'];
            }
            else{
                $resuserid = $row_check_resusr['id'];
            }
            // ! User cannot make two Reservations for 1 Movie

            //Create Reservation
            $sql_create_reservation = "INSERT INTO reservations (FK_reservation_user, FK_movie_times) VALUES (".$resuserid.", ".$movid.");";
            $dbconnection->query($sql_create_reservation);

            //Get Reservation ID
            $sql_find_resid = "SELECT id from reservations WHERE FK_reservation_user=".$resuserid." AND FK_movie_times=".$movid.";";
            $result_find_resid = $dbconnection->query($sql_find_resid);
            $resid = $result_find_resid->fetch_assoc()['id'];

            //Create Reservated Seats
            foreach($this->reservated_seats as $this_res_seats){
                //Get Seat ID
                $sql_find_seatid = "SELECT * from seats WHERE seats.row=".$this_res_seats->row." AND seats.col=".$this_res_seats->col.";";
                $result_find_seatid = $dbconnection->query($sql_find_seatid);
                while($row_find_seatid = $result_find_seatid->fetch_assoc()){
                    if($row_find_seatid['FK_room'] == $this->mv_time->room){
                        $seatid = $row_find_seatid['id'];
                        break;
                    }
                }
                $sql_create_resseats = "INSERT INTO reservated_seats (FK_reservation, FK_seat) VALUES ($resid, $seatid);";
                $dbconnection->query($sql_create_resseats);
            }
        }
    }

    class Reservation_User{
        public $firstname;
        public $lastname;

        public function __construct($firstname, $lastname)
        {
            $this->firstname = $firstname;
            $this->lastname = $lastname;
        }
    }
?>