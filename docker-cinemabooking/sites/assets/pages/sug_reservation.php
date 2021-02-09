<?php
    $location = "..";
    include "../config/config.php";
    if(isset($_GET['res'])){
        foreach($moviearr as $movie){
            if($movie->name == urldecode($_GET['mov'])){
                $currmov = $movie;
            }
        }
    }
    else{
        header("HTTP/1.0 404 Not Found");
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
    <title>Bookingmanager - Register</title>
</head>
<body>
    <?php include "../page_addon/navbar.php"; ?>
        <div id="order">
            <form action="" method="POST">
                <h2>Ticket wählen für --Film--</h2>
                <div class="input-seats">
                    <table>
                        <?php
                            
                        ?>
                    </table>
                </div>
                <input type="text" placeholder="Vorname" name="firstname">
                <input type="text" placeholder="Nachname" name="lastname">
                <button type="submit" name="sub-oder">
            </form>
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
                            element = document.getElementById("list_"+personid);
                            element.parentNode.removeChild(element);
                        }
                        else if(this.response != "success"){
                            alert("Error occured: '"+this.response+"'");
                        }
                    }
                }
            }
            function setmovtime($movname){
                
            }

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