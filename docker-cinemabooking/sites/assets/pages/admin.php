<?php
$location = "..";
include "../config/config.php";
$errors = [];

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
} 
else {
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

    unset($_POST);
    $_POST = array();
    header("Location: ../pages/admin.php");
}

if (isset($_POST["create-movie-submit"])) {
    $uploadDirectory = "../image/";

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

        //MYSLI REAL ESCAPE
        $fileName = $conn->real_escape_string($fileName);
        $movie_name = $conn->real_escape_string($movie_name);
        $fsk = $conn->real_escape_string($fsk);
        $description = $conn->real_escape_string($description);


        if ($didUpload) {
            //Safe Things to DB
            $sql_create_movie = "INSERT INTO movies (movies.name, movies.img_path, movies.fsk, movies.description) VALUES ('$movie_name', '$fileName', $fsk, '$description');";
            $conn->query($sql_create_movie);

            unset($_POST);
            $_POST = array();
            header("Location: ../pages/admin.php");
        } 
        else {
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

    $cols = $_POST['cols'];
    $rows = $_POST['rows'];

    $except_seats = json_decode($_POST['except-seats']);

    for ($i = 1; $i <= $rows; $i++) {
        for ($b = 1; $b <= $cols; $b++) {
            echo "seat_$i-$b: ";
            if(in_array("seat_$i-$b", $except_seats)){
                $except = 1;
            }
            else{
                $except = 0;
            }
            $sql_create_seats = "INSERT INTO `seats` (`row`, `col`, `except`, `FK_room`) VALUES ($i, $b, $except, $roomid);";
            $conn->query($sql_create_seats);
        }
    }

    unset($_POST);
    $_POST = array();
    header("Location: ../pages/admin.php");
}

if(isset($_POST['create-movie-times-submit'])){
    $room = $_POST['mv-room'];
    $movie = $_POST['mv-movie'];
    $time_start = $_POST['mv-start'];
    $time_end = $_POST['mv-end'];

    $sql_create_mv_time = "INSERT INTO movie_times (movie_times.FK_movie, movie_times.FK_room, movie_times.start, movie_times.end) VALUES ($movie, $room, '$time_start', '$time_end');";
    $conn->query($sql_create_mv_time);

    unset($_POST);
    $_POST = array();
    header("Location: ../pages/admin.php");
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
    <title>LedX - Admin Config</title>
</head>
<body>
    <script>
        function dragElement(elmnt) {
            var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
            if (document.getElementById(elmnt.id + "header")) {
                // if present, the header is where you move the DIV from:
                document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
            } 
            else {
                // otherwise, move the DIV from anywhere inside the DIV:
                elmnt.onmousedown = dragMouseDown;
                elmnt.style.top = '25%';
                elmnt.style.left = '25%';
            }

            function dragMouseDown(e) {
                e = e || window.event;
                e.preventDefault();
                // get the mouse cursor position at startup:
                pos3 = e.clientX;
                pos4 = e.clientY;
                document.onmouseup = closeDragElement;
                // call a function whenever the cursor moves:
                document.onmousemove = elementDrag;
            }

            function elementDrag(e) {
                e = e || window.event;
                e.preventDefault();
                // calculate the new cursor position:
                pos1 = pos3 - e.clientX;
                pos2 = pos4 - e.clientY;
                pos3 = e.clientX;
                pos4 = e.clientY;
                // set the element's new position:
                elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
                elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
            }

            function closeDragElement() {
                // stop moving when mouse button is released:
                document.onmouseup = null;
                document.onmousemove = null;
            }
        }
        function changeanchor(target){
            url = window.location.href;
            url = url.split("#");
            window.location.href = url[0]+target;
        }
        rm_seats = [];
        function setrmseat(objid){
            if(rm_seats.indexOf(objid) !== -1){
                //Get Index
                rm_seats.splice(rm_seats.indexOf(objid), 1);
                document.getElementById(objid).style.backgroundColor = "pink";
            }
            else{
                rm_seats.push(objid);
                document.getElementById(objid).style.backgroundColor = "blue";
            }

            document.getElementById('sdh-input-except-seats').value = JSON.stringify(rm_seats);
        }
    </script>
    <?php include "../page_addon/navbar.php"; ?>

    <div class="errors-container">
        <?php
            if(isset($_POST['create-movie-submit'])){
                foreach ($errors as $error) {
                    echo "<p class='error'><i class='fas fa-exclamation-circle'></i> " . $error . "</p>";
                }
            }
        ?>
    </div>

    <div id="create-user" class="create-user">
        <a href="admin.php"><i style="float: right; color: gray;" class="fas fa-times"></i></a>
        <h2>CREATE</h2>
        <form action="" method="post">
            <input class="ipf" placeholder="Username" name="username" type="username" required>
            <input class="ipf" placeholder="Passwort" name="password" type="password" required>
            <button class="ipf" name="create-user-submit" type="submit">CREATE</button>
        </form>
    </div>

    <div id="create-movie" class="create-movie">
        <a href="admin.php"><i style="float: right; color: gray;" class="fas fa-times"></i></a>
        <h2>CREATE</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <input class="ipf" placeholder="Film Poster" name="poster" type="file">
            <input class="ipf" placeholder="Filmtitel" name="movie-name" type="username" required>
            <input class="ipf" placeholder="FSK" name="fsk" type="number" required>
            <textarea class="ipf" style="text-align: left;" rows="10" placeholder="Beschreibung" name="description" rows="1" required></textarea>
            <button class="ipf" name="create-movie-submit" type="submit">CREATE</button>
        </form>
    </div>

    <?php if(isset($_GET['new-movie-time'])){ ?>
        <div id="create-mv-times" class="create-mv-times">
        <a href="admin.php"><i style="float: right; color: gray;" class="fas fa-times"></i></a>
        <h2>CREATE</h2>
        <form action="" method="post">
            <input class="ipf" placeholder="Kinosaal" name="mv-room" type="text" required>
            <input name="mv-movie" value="<?php echo $_GET['new-movie-time']; ?>" type="hidden" required>
            <input class="ipf" placeholder="Filmstart (yyyy-mm-dd hh-mm-ss)" name="mv-start" type="datetime" required>
            <input class="ipf" placeholder="Filmende (yyyy-mm-dd hh-mm-ss)" name="mv-end" type="datetime" required>
            <button class="ipf" name="create-movie-times-submit" type="submit">CREATE</button>
        </form>
    </div>
    <?php } ?>

    <div id="create-room" class="create-room">
        <a href="admin.php"><i style="float: right; color: gray;" class="fas fa-times"></i></a>
        <h2>CREATE</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <input onchange="showroom();" id="rows-input" class="ipf" name="rows" type="number" placeholder="Anzahl Reihen" required>
            <input onchange="showroom();" class="ipf" name="cols" id="cols-input" type="number" placeholder="Anzahl Sitze pro Reihe" required>
            <input class="ipf" name="room-nr" type="number" placeholder="Raumnummer" required>
            <input class="shadowinput" id="sdh-input-except-seats" name="except-seats" type="hidden">
            <h4>Sitze entfernen</h4>
            <p class="shadowinput" id="preview-info"></p>
            <table id="preview-container" style="width: min-content; margin:auto auto;">
                <tr colspan='100%'>
                    <td><div id='canvas'></div></td>
                </tr>
                <tr>
                    <td>
                        <table class="seats-in-table" id="seats-room-table">
                            <script>
                                showroom();
                                function showroom(){
                                    rows = document.getElementById('rows-input').value;
                                    cols = document.getElementById('cols-input').value;

                                    if(rows != "" && cols != ""){
                                        var info = document.getElementById("preview-info").style.display = "none";
                                        var table_seat = document.getElementById("seats-room-table");
                                        table_seat.innerHTML = "";

                                        for(let i=1; i<=rows; i++){
                                            let tr = document.createElement('tr');
                                            for(let b=1; b<=cols; b++){
                                                let td = document.createElement('td');

                                                let seat = document.createElement('div');

                                                //Set Attributes
                                                seat.classList.add('seat');
                                                seat.id = "seat_"+i+"-"+b;
                                                if(rm_seats.indexOf(seat.id) !== -1){
                                                    seat.style.backgroundColor = 'blue';
                                                }
                                                else{
                                                    seat.style.backgroundColor = 'pink';
                                                    
                                                    seat.style.cursor = 'pointer';
                                                }
                                                seat.setAttribute("onclick", "setrmseat(this.id);");

                                                td.appendChild(seat);
                                                tr.appendChild(td);
                                            }
                                            table_seat.appendChild(tr);
                                        }
                                    }
                                    else{
                                        var info = document.getElementById("preview-info");
                                        info.innerHTML = "Keine Vorschau verfügbar";
                                        info.classList.remove('shadowinput');
                                    }
                                }
                                $rows = document.getElementById
                            </script>
                        </table>
                    </td>
                </tr>
            </table>
            <button class="ipf" name="create-room-submit" type="submit">CREATE</button>
        </form>
    </div>

    <div class="thebigblock"></div>
    <div id="roomoverlay" class="showroomoverlay" style="cursor: pointer;padding: 10px;">
        <a href="admin.php"><i style="float: right; color: gray;" class="fas fa-times"></i></a>
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
        <script>dragElement(document.getElementById('roomoverlay'));</script>
    </div>
    <div class="admin-container">
        <a class="back-btn" href="../../index.php"><i class="fas fa-chevron-left"></i> Zurück</a>
        <div class="drops">
            <button type='button' class='collapsible'>Admin Users<i style='float:right' class='fas fa-chevron-down'></i></button>
            <div class='content'>
                <?php
                echo "<div><form action='admin.php#create-user' method='post'>";
                echo "<button type='submit' name='new-user' class='specialbutton'>CREATE NEW<br>";
                echo "</form></div>";
                foreach ($usersarr as $user) {
                    echo "<div> - " . $user->id . ": " . $user->username . "</div>";
                }
                ?>
            </div>
        </div>
        <div class="drops">
            <button type='button' class='collapsible'>Rooms<i style='float:right' class='fas fa-chevron-down'></i></button>
            <div style='cursor:pointer;' class='content'>
                <?php
                echo "<div><form action='admin.php#create-room' method='post'>";
                echo "<button type='submit' name='createroom.php' class='specialbutton'>CREATE NEW<br>";
                echo "</form></div>";
                foreach ($roomarr as $room) {
                    echo "<div><form action='admin.php?room=" . $room->id . "' method='post'>";
                    echo "<button type='submit' name='room-show'> - Kinoraum " . $room->number . "<br>";
                    echo "</form></div>";
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
                echo "<div><form action='admin.php#create-movie' method='post'>";
                echo "<button type='submit' name='add-movie' class='specialbutton'>CREATE NEW<br>";
                echo "</form></div>";
                foreach ($moviearr as $movie) {
                    echo "<div><form action='#" . urlencode($movie->name) . "' method='post'>";
                    echo "<button type='submit' name='show-movie'> - " . $movie->name . "<br>";
                    echo "</form></div>";
                }
                ?>
            </div>
        </div>
        <div class="drops">
            <button onclick="changeanchor('#reservations');" class='collapsible'>Reservations</button>
        </div>
        <div id="reservations">
                <a href="admin.php"><i style="float: right; color: gray;" class="fas fa-times"></i></a>
                <h2 style="padding-bottom: 10px;">Reservations</h2>
                <?php
                    foreach ($reservationarr as $reservation) {
                        echo "<div>" . $reservation->reservation_user->firstname . ", " . $reservation->reservation_user->lastname . " -> Movie: " . $reservation->movie->name . ", Time: " . $reservation->mv_time->start . " - " . $reservation->mv_time->end;
                        echo "<b>, Seats: </b>";
                        foreach ($reservation->reservated_seats as $res_seats) {
                            echo $res_seats->row . ";" . $res_seats->col . " / ";
                        }
                        echo "</div><br>";
                    }
                ?>
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
            echo "<script>dragElement(document.getElementById('".urlencode($movie->name)."'));</script>";
        }
        ?>
    </div>
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
</body>
</html>