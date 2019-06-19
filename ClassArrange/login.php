<?php
$res = array();
if(isset($_POST['username'])&&isset($_POST['password'])){
    $name = $_POST['username'];
    $pwd = $_POST['password'];
    $sql = "select * from user where uid='$name' and pwd='$pwd'";
    $mysqli = new mysqli("localhost","root","scrovormysql",'easyArranger');
    if(mysqli_connect_errno()){
        $mysqli->close();
        $res['status'] = 'error';
    }else{
        if($result = $mysqli->query($sql)){
            if($result->num_rows==1){
               session_start();
               $_SESSION['uid'] = $name; 
               $res['status'] = 'ok';
            }else{
               $res['status'] = 'error';
            }
        }
    }
    $mysqli->close();
}
echo json_encode($res);
?>