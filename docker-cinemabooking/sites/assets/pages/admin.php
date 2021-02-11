<?php
$location = "..";
include "../config/config.php";
if (isset($_COOKIE['session-id'])) {
    foreach ($usersarr as $user) {
        if (hash("sha256", $user->id) == $_COOKIE['session-id']) {
            $check = true;
            $conn = new mysqli($servername, $username, $password, $dbname);
        }
    }
    if ($check != true) {
        header("Location: ../../index.php");
    }
} else {
    header("Location: ../../index.php");
}
$showroom = FALSE;
if (isset($_POST["room-show"])) {
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
if (isset($_POST["create-user-submit"])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $password = hash("sha256", $username . $password);

    $sql_create_user = "INSERT INTO users (users.username, users.password) VALUES ('$username', '$password');";
    $result_create_user = $conn->query($sql_create_user);

    header("Location: ../pages/admin.php");
}

if (isset($_POST["create-movie-submit"])) {
    $uploadDirectory = "../image/";

    $errors = []; // Store errors here

    $fileExtensionsAllowed = ['jpeg', 'jpg', 'png']; // These will be the only file extensions allowed 

    $fileName = $_FILES['poster']['name'];
    $fileSize = $_FILES['poster']['size'];
    $fileTmpName  = $_FILES['poster']['tmp_name'];
    $fileType = $_FILES['poster']['type'];

    $movie_name = $_POST['movie-name'];
    $fsk = $_POST['fsk'];
    $description = $_POST['description'];

    if (empty($movie_name) || empty($fsk) || empty($description)) {
        $errors[] = "Es wurden nicht alle nötigen Infos angegeben";
    }

    $fileExtension = strtolower(end(explode('.', $fileName)));

    $uploadPath = $currentDirectory . $uploadDirectory . basename($fileName);

    if (!in_array($fileExtension, $fileExtensionsAllowed)) {
        $errors[] = "Diese File Art ist nicht erlaubt. Bitte nutze ein JPG oder PNG File.";
    }

    if ($fileSize > 4000000) {
        $errors[] = "Das Bild ist zu gross (Max. 4GB)";
    }

    if (empty($errors)) {
        $didUpload = move_uploaded_file($fileTmpName, $uploadPath);

        if ($didUpload) {
            //Safe Things to DB
            $sql_create_movie = "INSERT INTO movies (movies.name, movies.img_path, movies.fsk, movies.description) VALUES ('$movie_name', '$fileName', '$fsk', '$description');";
            $conn->query($sql_create_movie);

            header("Location: ../pages/admin.php");
        } else {
            $errors[] =  "Ein unbekannter Fehler ist aufgetreten. Bitte kontaktiere einen Administrator.";
        }
    }
}

if (isset($_POST["create-room-submit"])) {
    $roomnr = $_POST['room-nr'];
    $sql_create_room = "INSERT INTO `rooms` (rooms.number) VALUES ($roomnr);";
    $conn->query($sql_create_room);

    //Get Rooms ID
    $sql_get_roomid = "SELECT id FROM rooms WHERE rooms.number=$roomnr;";
    $result_get_roomid = $conn->query($sql_get_roomid);
    $roomid = $result_get_roomid->fetch_assoc()['id'];

    $cols = $_POST['rows'];
    $rows = $_POST['cols'];

    for ($i = 1; $i <= $rows; $i++) {
        for ($b = 1; $b <= $cols; $b++) {
            $sql_create_seats = "INSERT INTO `seats` (`row`, `col`, `except`, `FK_room`) VALUES ($i, $b, 0, $roomid);";
            $conn->query($sql_create_seats);
        }
    }

    header("Location: ../pages/admin.php");
}
//SET was here
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
    <title>LedX - Admin Config</title>
</head>

<body>
    <?php include "../page_addon/navbar.php"; ?>
    <div id="create-user" class="create-user">
        <a href="admin.php"><i style="float: right; color: gray;" class="fas fa-times"></i></a>
        <h2>CREATE</h2>
        <form action="" method="post">
            <input class="ipf" placeholder="Username" name="username" type="username">
            <input class="ipf" placeholder="Passwort" name="password" type="password">
            <button class="ipf" name="create-user-submit" type="submit">CREATE</button>
        </form>
    </div>
    <div class="errors-container">
        <?php
        foreach ($errors as $error) {
            echo "<p class='error'>" . $error . "</p>";
        }
        ?>
    </div>

    <div id="create-movie" class="create-movie">
        <a href="admin.php"><i style="float: right; color: gray;" class="fas fa-times"></i></a>
        <h2>CREATE</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <input class="ipf" placeholder="Film Poster" name="poster" type="file">
            <input class="ipf" placeholder="Filmtitel" name="movie-name" type="username">
            <input class="ipf" placeholder="FSK" name="fsk" type="">
            <textarea class="ipf" class="movie-desc" placeholder="Beschreibung" name="description" rows="1"></textarea>
            <button class="ipf" name="create-movie-submit" type="submit">CREATE</button>
        </form>
    </div>

    <div id="create-room" class="create-room">
        <a href="admin.php"><i style="float: right; color: gray;" class="fas fa-times"></i></a>
        <h2>CREATE</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <input class="ipf" name="rows" type="number" placeholder="Anzahl Reihen">
            <input class="ipf"name="cols" type="number" placeholder="Anzahl Sitze pro Reihe">
            <input class="ipf" name="room-nr" type="number" placeholder="Raumnummer">
            <button class="ipf" name="create-room-submit" type="submit">CREATE</button>
        </form>
    </div>

    <div class="thebigblock"></div>
    <div class="showroomoverlay">
        <a href=""><i style="float: right; color: gray;" class="fas fa-times"></i></a>
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
    <div class="admin-container">

        <a class="back-btn" href="../../index.php"><i class="fas fa-chevron-left"></i> Zurück</a>
        <div class="drops">

            <button type='button' class='collapsible'>Users<i style='float:right' class='fas fa-chevron-down'></i></button>
            <div class='content'>
                <?php

                echo "<div><form action='#create-user' method='post'>";
                echo "<button type='submit' name='new-user' class='specialbutton'>CREATE<br>";
                echo "</form></div>";
                foreach ($usersarr as $user) {
                    echo "<div>" . $user->id . ": " . $user->username . "</div>";
                }
                ?>
            </div>
        </div>
        <div class="drops">
            <button type='button' class='collapsible'>Rooms<i style='float:right' class='fas fa-chevron-down'></i></button>
            <div style='cursor:pointer;' class='content'>
                <?php
                echo "<div><form action='#create-room' method='post'>";
                echo "<button type='submit' name='createroom.php' class='specialbutton'>CREATE<br>";
                echo "</form></div>";
                foreach ($roomarr as $room) {
                    echo "<form action='admin.php?room=" . $room->id . "' method='post'>";
                    echo "<button type='submit' name='room-show'>Room " . $room->number . "<br>";
                    echo "</form>";
                }
                ?>
            </div>
        </div>
        <?php
        if ($showroom == TRUE) {
            echo "<style>.showroomoverlay{display: block}</style>";
        } else {
            echo "<style>.showroomoverlay{display: none}</style>";
        }
        ?>
        <div class="drops">

            <button type='button' class='collapsible'>Movies<i style='float:right' class='fas fa-chevron-down'></i></button>
            <div style='cursor:pointer;' class="content">
                <?php
                echo "<div><form action='#create-movie' method='post'>";
                echo "<button type='submit' name='add-movie' class='specialbutton'>CREATE<br>";
                echo "</form></div>";
                foreach ($moviearr as $movie) {
                    echo "<div><form action='#" . urlencode($movie->name) . "' method='post'>";
                    echo "<button type='submit' name='show-movie'>" . $movie->name . "<br>";
                    echo "</form></div>";
                }
                ?>
            </div>
        </div>
        <?php
        foreach ($moviearr as $movie) {

            echo "<div class='movieoverlay' id='" . urlencode($movie->name) . "'>";
            echo "<a href='admin.php'><i style='float: right; color: gray;' class='fas fa-times'></i></a>";
            echo "<h2>" . $movie->name . "</h2>";
            echo "<a class='new-movie-time' href='admin.php?new-movie-time=".$movie->id."'>CREATE MOVIE TIME</a><br>";
            foreach ($movie->times as $times) {
                echo "Room: " . $times->room . " -> " . $times->start . " - " . $times->end . "<br>";
            }
            echo "</div>";
        }
        ?>
        <!--
        <h2>Reservations</h2>
        <ul>
            <?php/*
            foreach ($reservationarr as $reservation) {
                echo "<li>" . $reservation->reservation_user->firstname . ", " . $reservation->reservation_user->lastname . " -> Movie: " . $reservation->movie->name . ", Time: " . $reservation->mv_time->start . " - " . $reservation->mv_time->end;
                echo "<li><b>Seats: </b>";
                foreach ($reservation->reservated_seats as $res_seats) {
                    echo $res_seats->row . ";" . $res_seats->col . " / ";
                }
                echo "</li>";
                echo "</li>";
            }*/
            ?>
        </ul>-->
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