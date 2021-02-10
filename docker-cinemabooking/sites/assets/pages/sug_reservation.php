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

        //Get Seats to load in a 2 dimensianal array
        $roomseats = array();
        foreach($curroom->seats as $seat){
            if(!array_key_exists($seat->row, $roomseats)){
                $roomseats[$seat->row] = array();
            }
            $roomseats[$seat->row][$seat->col] = $seat;
        }
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
    <title>Bookingmanager - Reservation</title>
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
                                    foreach($roomseats as $seatrow){
                                        echo "<tr>";
                                        foreach($seatrow as $seat){
                                            echo "<td>";
                                            if(!$seat->except){
                                                if($seat->reservated){
                                                    echo "<div class='seat' style='background-color: red'></div>";
                                                }
                                                else{
                                                    echo "<div class='seat' style='background-color: green'></div>";
                                                }
                                            }
                                            else{
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
                <p>*Die Leinwand befindet sich vorne und die Eingänge hinten links und rechts. <br>Die Notausgänge befinden sich auf beiden <br>Seiten der Leinwand und sind mit einem grünen Schild markiert.</p>
                <input type="text" placeholder="Name" name="lastname">
                <input type="text" placeholder="Vorname" name="firstname">
                <button type="submit" name="sub-oder">Abschliessen</button>
            </form>
        </div>
    </div>
    <script>
        var reservationmovname;
        var reservationstarttime;
        var reservationendtime;

        function getresinfo(){
            xmlreq = new XMLHttpRequest;
            xmlreq.open("POST", "", true);
            xmlreq.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xmlreq.onreadystatechange = function(){
                if(this.readyState === XMLHttpRequest.DONE && this.status === 200){
                    if(func == "del" && this.response == "success"){
                        
                    }
                    else if(this.response != "success"){
                        alert("Error occured: '"+this.response+"'");
                    }
                }
            }
        }
    </script>
</body>