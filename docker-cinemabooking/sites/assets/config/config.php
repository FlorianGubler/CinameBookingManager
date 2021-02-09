<?php
    require_once "classes/movie.class.php";
    require_once "classes/reservation.class.php";
    require_once "classes/room.class.php";
    require_once "classes/seat.class.php";
    require_once "classes/user.class.php";

    $servername = "mysql";
    $username = "usr1";
    $password = "secretpswd";
    $dbname = "bookingmgr";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_errno) {
        die("Failed to connect to MySQL: " . $conn->connect_error);
    }

    //Get Users
    $usersarr = array();
    $sql_users = "SELECT * FROM users";
    if ($result_users = $conn->query($sql_users)) {
        while ($row_users = $result_users->fetch_assoc()) {
            $newusr = new User($row_users['id'], $row_users['username'], $row_users['password']);
            array_push($usersarr, $newusr);
        }
    }
    //Get Movies & Movie Times
    $moviearr = array();
    $sql_movie = "SELECT * FROM movies";
    if ($result_movie = $conn->query($sql_movie)) {
        while ($row_movie = $result_movie->fetch_assoc()) {
            $newmovie = new Movie($row_mv_times['id'], $row_movie['name'], $row_movie['img_path']);

            //Get Movie Times
            $sql_mv_times = "SELECT * FROM movie_times WHERE FK_movie=".$row_movie['id'].";";
            if ($result_mv_times = $conn->query($sql_mv_times)) {
                while ($row_mv_times = $result_mv_times->fetch_assoc()) {
                    $new_mv_times = new mv_times($row_mv_times['id'], $row_mv_times['FK_room'], $row_mv_times['start'], $row_mv_times['end']);
                    array_push($newmovie->times, $new_mv_times);
                }
            }

            array_push($moviearr, $newmovie);
        }
    }

    //Get Rooms
    $roomarr = array();
    $sql_room = "SELECT * FROM rooms";
    if ($result_room = $conn->query($sql_room)) {
        while ($row_room = $result_room->fetch_assoc()) {
            $newroom = new Room($row_room['id'], $row_room['number']);
            array_push($roomarr, $newroom);
        }
    }

    //Get Seats & Push to Room
    foreach($roomarr as $room){
        $sql_seat = "SELECT * FROM seats WHERE FK_room=".$room->id.";";
        if ($result_seat = $conn->query($sql_seat)) {
            while ($row_seat = $result_seat->fetch_assoc()) {
                $newseat = new Seat($row_seat['id'], $row_seat['row'], $row_seat['col'], $row_seat['except'], false);
                array_push($room->seats, $newseat);
            }
        }
    }

    //Get Reservations & Reservation Users
    $reservationarr = array();
    $sql_reservation = "SELECT * FROM reservations";
    if ($result_reservation = $conn->query($sql_reservation)) {
        while ($row_reservation = $result_reservation->fetch_assoc()) {

            //Get Reservation User 
            $sql_reservation_usr = "SELECT * FROM reservation_users WHERE id=".$row_reservation['FK_reservation_user'].";";
            if ($result_reservation_usr = $conn->query($sql_reservation_usr)) {
                $row_reservation_usr = $result_reservation_usr->fetch_assoc();
                $newreservation_usr = new Reservation_User($row_reservation_usr['firstname'], $row_reservation_usr['lastname']);
            }

            //Get Reservation MV_Time 
            foreach($moviearr as $movie){
                foreach($movie->times as $mv_times){
                    if($mv_times->id == $row_reservation['FK_movie_times']){
                        $newres_mv_times = $mv_times;
                        $newres_movie = $movie;
                    }
                }
            }

            $newreservation = new Reservation($row_reservation['id'], $newres_movie, $newres_mv_times, $newreservation_usr);

            //Get Reservated Seats
            $sql_res_seats = "SELECT * FROM reservated_seats WHERE FK_reservation=".$row_reservation['id'].";";
            if ($result_res_seats = $conn->query($sql_res_seats)) {
                $row_res_seats = $result_res_seats->fetch_assoc();
            }
            foreach($roomarr as $room){
                foreach($room->seats as $seat){
                    if($seat->id == $row_res_seats['FK_seat']){
                        array_push($newreservation->reservated_seats, $seat);
                    }
                }
            }

            array_push($reservationarr, $newreservation);
        }
    }
?>