<?php
$mysqli = new mysqli("localhost","root","scrovormysql",'easyArranger');
$name = $_GET['username'];
$sql  = "select * from user where uid='$name'";
$res = array();
if(mysqli_connect_errno()){
    $mysqli->close();
    $res['status'] = 'error';
}else{
    if($result = $mysqli->query($sql)){
        if($result->num_rows>0){
            $res['status'] = 'error';
            file_put_contents("log.txt", "2222faafsd",FILE_APPEND);
        }else{
            $res['status'] = 'ok';
        }
    }else{
        $res['status'] = 'error';
        file_put_contents("log.txt", "2222err",FILE_APPEND);
        
    }
}
$mysqli->close();
echo json_encode($res);
?>