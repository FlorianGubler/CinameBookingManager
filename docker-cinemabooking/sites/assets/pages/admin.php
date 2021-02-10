<?php
$location = "..";
include "../config/config.php";
if (isset($_COOKIE['session-id'])) {
    foreach ($usersarr as $user) {
        if (hash("sha256", $user->id) == $_COOKIE['session-id']) {
            $check = true;
        }
    }
    if ($check != true) {
        header("Location: ../../index.php");
    }
} else {
    header("Location: ../../index.php");
}
$showroom = FALSE;
if(isset($_POST["room-show"])) {
    $room = $_GET["room"];

    foreach ($roomarr as $rooms) {
        if ($rooms->id == $room) {
            $curroom = $rooms;
        }
    }

    $roomseats = array();
    foreach ($curroom->seats as $seat) {
        if (!array_key_exists($seat->row, $roomseats)) {
            $roomseats[$seat->row] = array();
        }
        $roomseats[$seat->row][$seat->col] = $seat;
    }
    $showroom = TRUE;
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <?php include "../page_addon/allheadfiles.php"; ?>
    <title>Bookingmanager - Admin Config</title>
</head>

<body>
    <?php include "../page_addon/navbar.php"; ?>
    <div class="admin-container">
        <a class="back-btn" onclick="history.go(-2)"><i class="fas fa-chevron-left"></i> Zurück</a>
        <h2>Users</h2>
        <ul>
            <?php
            foreach ($usersarr as $user) {
                echo "<li>" . $user->id . ": " . $user->username . "</li>";
            }
            ?>
        </ul>
        <div class="roominfo">
            <h2>Rooms</h2>
            <button type='button' class='collapsible'>Rooms<i style='float:right' class='fas fa-chevron-down'></i></button>
            <div style='cursor:pointer;' class='content'>
                <?php
                foreach ($roomarr as $room) {
                    echo "<form action='admin.php?room=" . $room->id . "' method='post'>";
                    echo "<button type='submit' name='room-show'>Room " . $room->number . "<br>";
                    echo "</form>";
                }
                ?>
            </div>
        </div>
        <?php
        if($showroom==TRUE){
            echo "<style>.showroomoverlay{display: block}</style>";
        } else {
            echo "<style>.showroomoverlay{display: none}</style>";
        }
        ?>
        <div class="showroomoverlay">
            <a href="admin.php" ><i style="float: right; color: gray;"class="fas fa-times"></i></a>
            <table>
                <tr colspan='100%'>
                    <div id='canvas'></div>
                </tr>
                <tr>
                    <table class="seats-in-table">
                        <?php
                        foreach ($roomseats as $seatrow) {
                            echo "<tr>";
                            foreach ($seatrow as $seat) {
                                echo "<td>";
                                if (!$seat->except) {
                                    echo "<div class='seat' style='background-color: pink'></div>";
                                } else {
                                    echo "<div class='seat-empty' ></div>";
                                }
                                echo "</td>";
                            }
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </tr>
            </table>
        </div>
        <h2>Movies</h2>
        <ul>
            <?php
            foreach ($moviearr as $movie) {
                echo "<li><b>" . $movie->name . ":</b> ";
                echo "<ul>";
                foreach ($movie->times as $times) {
                    echo "<li>Room: " . $times->room . " -> " . $times->start . " - " . $times->end . "</li>";
                }
                echo "</ul>";
                echo "</li>";
            }
            ?>
        </ul>
        <h2>Reservations</h2>
        <ul>
            <?php
            foreach ($reservationarr as $reservation) {
                echo "<li>".$reservation->reservation_user->firstname . ", " . $reservation->reservation_user->lastname . " -> Movie: " . $reservation->movie->name . ", Time: " . $reservation->mv_time->start . " - " . $reservation->mv_time->end;
                echo "<li><b>Seats: </b>";
                foreach ($reservation->reservated_seats as $res_seats) {
                    echo $res_seats->row . ";" . $res_seats->col . " / ";
                }
                echo "</li>";
                echo "</li>";
            }
            ?>
        </ul>
    </div>
</body>
<script>
    var coll = document.getElementsByClassName("collapsible");
    var i;
    for (i = 0; i < coll.length; i++) {
        coll[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var content = this.nextElementSibling;
            if (content.style.display === "block") {
                content.style.display = "none";
            } else {
                content.style.display = "block";
            }
        });
    }
</script>

</html>