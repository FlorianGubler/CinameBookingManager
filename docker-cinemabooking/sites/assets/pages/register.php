<?php
    $location = "..";
    include "../config/config.php";
    if(isset($_GET['mov'])){
        foreach($moviearr as $movie){
            if($movie->name == urldecode($_GET['mov'])){
                $currmov = $movie;
            }
        }
    }
    else{
        header("HTTP/1.0 404 Not Found");
    }

    $dates = array();
    foreach($currmov->times as $time){
        if(!in_array(explode(" ", $time->start)[0], $dates)){
            array_push($dates, explode(" ", $time->start)[0]);
        }
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
    <div class="movinfocontainer">
        <a class="back-btn" href="../../index.php"><i class="fas fa-chevron-left"></i> Zurück</a>
        <div class="movinfo">
            <img src="../image/<?php echo $currmov->img;?>">
            <p><b>Titel:</b> <?php echo $currmov->name;?></p>
            <p><b>Altersfreigabe:</b> FSK ab <?php echo $currmov->fsk;?> Jahren</p>
            <p><b>Beschreibung:</b> <?php echo $currmov->description;?></p>
        </div>
        <div class="movietimeinfo">
            <h2>Vorstellungen</h2>
            <?php
                foreach($dates as $date){
                    echo "<button type='button' class='collapsible'>".$date."<i style='float:right' class='fas fa-chevron-down'></i></button>";
                    echo "<div style='cursor:pointer;' class='content'>";
                    foreach($currmov->times as $time){
                        if(explode(" ", $time->start)[0] == $date){
                            echo "<form action='sug_reservation.php' method='post'>";
                            echo "<input class='shadowinput' value='".$currmov->name."' name='mv_name'>";
                            echo "<input class='shadowinput' value='".$time->room."' name='room'>";
                            echo "<input class='shadowinput' value='".$time->start.";".$time->end."' name='date'>";
                            echo "<button type='submit' name='set-res-btn'>".explode(" ", $time->start)[1]." in Kinosaal ".$time->room." | <b> Ticket wählen</b></button><br>";
                            echo "</form>";
                        }
                    }
                    echo "</div>";
                }
                if(count($dates) == 0){
                    echo "<p>Leider sind zurzeit keine Vorstellungen für diesen Film verfügbar</p>";
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
    </div>
</body>