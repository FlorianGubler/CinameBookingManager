<?php
    include "config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Config</title>
</head>
<body>
    <h2>Users</h2>
    <ul>
        <?php
            foreach($usersarr as $user){
                echo "<li>".$user->id.": ".$user->username."</li>";
            }
        ?>
    </ul>
    <h2>Rooms</h2>
    <ul>
        <?php
            foreach($roomarr as $room){
                echo "<li>".$room->number.": ";
                echo "<ul>";
                foreach($room->seats as $seat){
                    if($seat->except != 1){
                        echo "<li>Seat: ".$seat->row.";".$seat->col."</li>";
                    }
                }
                echo "</ul>";
                echo "</li>";
            }
        ?>
    </ul>
    <h2>Movies</h2>
    <ul>
        <?php
            foreach($moviearr as $movie){
                echo "<li>".$movie->name.": ";
                echo "<ul>";
                foreach($movie->times as $times){
                    echo "<li>Room: ".$times->room." -> ".$times->start." - ".$times->end."</li>";
                }
                echo "</ul>";
                echo "</li>";
            }
        ?>
    </ul>
    <h2>Reservations</h2>
    <ul>
        <?php
            foreach($reservationarr as $reservation){
                echo "<li>".$reservation->id.": ".$reservation->reservation_user->firstname.", ".$reservation->reservation_user->lastname." -> Movie: ".$reservation->movie->name.", Time: ".$reservation->mv_time->start." - ".$reservation->mv_time->end;
                echo "<ul>";
                foreach($reservation->reservated_seats as $res_seats){
                    echo "<li>Seat: ".$res_seats->row.";".$res_seats->col."</li>";
                }
                echo "</ul>";
                echo "</li>";
            }
        ?>
    </ul>
</body>
</html>