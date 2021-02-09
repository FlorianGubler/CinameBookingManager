<?php
    $location = "assets";
    include "assets/config/config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <?php include "assets/page_addon/allheadfiles.php"; ?>
    <title>Bookingmanager - Home</title>
</head>
<body>
    <?php include "assets/page_addon/navbar.php"; ?>
    <div class="dashboard">
        <?php
            foreach($moviearr as $movie){
                echo "<a href='assets/pages/register.php?mov=".urlencode($movie->name)."' class='moviediv'><img src='assets/image/".$movie->img."' alter='Title Picture ".$movie->name."' ><p>".$movie->name."</p></a>";
            }
        ?>
    </div>
</body>