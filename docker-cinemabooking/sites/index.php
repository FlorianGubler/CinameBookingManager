<?php
    include "config.php";

    if(isset($_POST['login-submit'])){
        foreach($usersarr as $user){
            if($user->logincheck($_POST['login-usrname'],$_POST['login-passwd'])){
                setcookie("session-id", hash("sha256", $user->id), 0);
                header("Location: pages/admin.php");
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/navbar.css">
    <?php include "allheadfiles.php"; ?>
    <title>Bookingmanager - Home</title>
</head>
<body>
    <div id="login" class="login">
        <form action="" method="post">
            <a href="#"><i class="fas fa-times"></i></a>
            <h2>Login</h2>
            <input placeholder="Username" name="login-usrname" type="username">
            <input placeholder="Password" name="login-passwd" type="password">
            <button name="login-submit" type="submit">Login</button>
        </form>
    </div>
    <?php include "navbar.php"; ?>
    <div class="dashboard">
        <?php
            foreach($moviearr as $movie){
                echo "<a href='pages/register.php?mov=".urlencode($movie->name)."' class='moviediv'><img src='".$movie->img."' alter='Title Picture ".$movie->name."' ><p>".$movie->name."</p></a>";
            }
        ?>
    </div>
</body>