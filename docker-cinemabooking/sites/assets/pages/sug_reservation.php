<?php
    $location = "..";
    include "../config/config.php";
    if(isset($_POST['set-res-btn'])){
        $movie = $_POST['mv_name'];
        $room = $_POST['room'];
        $date_start = explode(";", $_POST['date'])[0];
        $date_end = explode(";", $_POST['date'])[1];

        //Get Cinema Room to load
        foreach($roomarr as $rooms){
            if($rooms->id == $room){
                $curroom = $rooms;
            }
        }

        foreach($moviearr as $movies){
            if($movies->name == $movie){
                $currmov = $movies;
                foreach($movies->times as $movie_time){
                    if($movie_time->start == $date_start && $movie_time->end == $date_end){
                        $currmov_time = $movie_time;
                    }
                }
            }
        }


        //Get Seats to load in a 2 dimensianal array
        $roomseats = array();
        foreach($curroom->seats as $seat){
            if(!array_key_exists($seat->row, $roomseats)){
                $roomseats[$seat->row] = array();
            }
            $roomseats[$seat->row][$seat->col] = $seat;
        }
    }
    else if(isset($_POST['sub-order'])){
        $movie = $_POST['movie'];
        $room = $_POST['room'];
        $date_start = explode(";", $_POST['date'])[0];
        $date_end = explode(";", $_POST['date'])[1];
        $res_seats = json_decode($_POST['res-seats']);
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        
        //Create Reservation User
        $new_res_usr = new Reservation_User($firstname, $lastname);

        //Find Room Object
        foreach($roomarr as $rooms){
            if($rooms->id == $room){
                $curroom = $rooms;
            }
        }

        //Find Reservation Seats and push to Reservation Array 
        $res_seats_arr = array();
        foreach($curroom->seats as $seat){
            foreach($res_seats as $res_seat){
                $rowcol = explode("_", $res_seat)[1];
                $row = explode("-", $rowcol)[0];
                $col = explode("-", $rowcol)[1];
                if($row == $seat->row && $col == $seat->col){
                    if(!$seat->reservated){  
                        $new_res_seat = $seat;
                        array_push($res_seats_arr, $new_res_seat);
                    }
                    else{
                        foreach($seat->reservated_mv_times as $res_mv_time){
                            if($res_mv_time[0]->name != $movie || ($res_mv_time[1]->start != $date_start && $res_mv_time[1]->end != $date_end)){
                                $new_res_seat = $seat;
                                array_push($res_seats_arr, $new_res_seat);
                            }
                        }
                    }
                }
            }
        }

        //Find Movie & Movie Times Object
        foreach($moviearr as $movies){
            if($movies->name == $movie){
                $currmov = $movies;
                foreach($movies->times as $movie_time){
                    if($movie_time->start == $date_start && $movie_time->end == $date_end){
                        $currmov_time = $movie_time;
                    }
                }
            }
        }

        //Create Reservation
        $new_res = new Reservation($currmov, $currmov_time, $new_res_usr);
        $new_res->reservated_seats = $res_seats_arr;

        //Create DB Connection
        $conntwo = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_errno) {
            die("Failed to connect to MySQL: " . $conn->connect_error);
        }
        
        $new_res->safetodb($conntwo);
        header("Location: ../../index.php");

    }
    else{
        header("Location: ../../index.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/register.css">
    <?php include "../page_addon/allheadfiles.php"; ?>
    <title>LedX - Reservation</title>
</head>
<body>
    <?php include "../page_addon/navbar.php"; ?>
    <div class="order-container">
        <a class="back-btn" onclick="history.back()"><i class="fas fa-chevron-left"></i> Zurück</a>
        <div id="order">
            <form action="" method="POST">
                <h2>Ticketkauf abschliessen für <?php echo $movie ?> am <?php echo explode(" ", $date_start)[0]." um ".explode(" ", $date_start)[1]." Uhr" ?></h2>
                <h4>Kinosaal <?php echo $curroom->number ?></h4>
                <div class="input-seats">
                    <table>
                        <tr colspan='100%'><div id='canvas'></div></tr>
                        <tr>
                            <table class="seats-in-table">
                                <?php
                                    if(isset($_POST['set-res-btn'])){
                                        foreach($roomseats as $seatrow){
                                            echo "<tr>";
                                            foreach($seatrow as $seat){
                                                echo "<td>";
                                                if(!$seat->except){
                                                    if($seat->reservated){
                                                        foreach($seat->reservated_mv_times as $res_mv_time_seat){
                                                            if($res_mv_time_seat[0] == $currmov && $res_mv_time_seat[1] == $currmov_time){
                                                                echo "<div id='avail-seat_".$seat->row."-".$seat->col."' onclick='setseat(this.id);' class='seat' style='cursor:pointer;background-color: red'></div>";
                                                                $checker_time_room = false;
                                                                break;
                                                            }
                                                            else{
                                                                $checker_time_room = true;
                                                            }
                                                        }
                                                        if($checker_time_room){
                                                            echo "<div id='avail-seat_".$seat->row."-".$seat->col."' onclick='setseat(this.id);' class='seat' style='cursor:pointer;background-color: green'></div>";
                                                        }
                                                    }
                                                    else{
                                                        echo "<div id='avail-seat_".$seat->row."-".$seat->col."' onclick='setseat(this.id);' class='seat' style='cursor:pointer;background-color: green'></div>";
                                                    }
                                                }
                                                else{
                                                    echo "<div class='seat-empty' ></div>";
                                                }
                                                echo "</td>";
                                            }
                                            echo "</tr>";
                                        }
                                    }
                                ?>
                            </table>
                        </tr>
                    </table>
                </div>
                <p>*Die Leinwand befindet sich vorne und die Eingänge hinten links und rechts. <br>Die Notausgänge befinden sich auf beiden <br>Seiten der Leinwand und sind mit einem grünen Schild markiert.</p>
                <input class="shadowinput" id="res-seats" name="res-seats">
                <input class="shadowinput" id="movie" name="movie">
                <input class="shadowinput" id="date" name="date">
                <input class="shadowinput" id="room" name="room">
                <input id="get-lastname" type="text" placeholder="Name" name="lastname">
                <input id="get-firstname" placeholder="Vorname" name="firstname">
                <button type="submit" id="sub-btn-order" name="sub-order">Abschliessen</button>
            </form>
        </div>
    </div>
    <script>
        var reservationmovname;
        var reservationstarttime;
        var reservationendtime;
        var res_seatsarr = [];

        function setseat(objid){
            res_seatsarr.push(objid);
            document.getElementById(objid).style.backgroundColor = "blue";

            document.getElementById('movie').value = '<?php echo $movie; ?>';
            document.getElementById('room').value = '<?php echo $room; ?>';
            document.getElementById('date').value = '<?php echo $date_start.";".$date_end; ?>';
            document.getElementById('res-seats').value = JSON.stringify(res_seatsarr);
        }
    </script>
</body>