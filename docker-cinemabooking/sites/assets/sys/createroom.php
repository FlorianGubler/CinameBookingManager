<?php
    $servername = "mysql";
    $username = "usr1";
    $password = "secretpswd";
    $dbname = "bookingmgr";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_errno) {
        die("Failed to connect to MySQL: " . $conn->connect_error);
    }

    $rows = 6;
    $cols = 15;
    $room = 2;

    for($i=1; $i <= $rows; $i++){
        for($b=1; $b <= $cols; $b++){
            /*if($i <= round($rows/3)){
                if($b <= round($cols/4) || $b >= ($cols - round($cols/4))){
                    echo "G";
                }
                else{
                    echo "O";
                }
            }*/
            $sql = "INSERT INTO `seats` (`row`, `col`, `except`, `FK_room`) VALUES ($i, $b, 0, $room);";
            //$conn->query($sql);
            echo $sql."<br>";
        }
    }
?>