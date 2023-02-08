<?php
require_once 'db.php';
// $insert = mysqli_query($db, "INSERT INTO users (display_name, username,password) VALUES ('علی', 'alipk','12345678')");
// mysqli_query($db, 'SET NAMES utf8');

// if($insert){
//     echo 'done';
// }else {
//     echo 'error';
// }
$username = 'alipk';

$checkUser = mysqli_query($db, "SELECT * FROM users WHERE username='$username'");
if (mysqli_num_rows($checkUser) > 0){
    echo 'نام کاربری از قبل وجود دارد';
}else {
   
}
?>