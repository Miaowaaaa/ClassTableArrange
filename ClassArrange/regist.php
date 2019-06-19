<?php
$mysqli = new mysqli("localhost","root","scrovormysql",'easyArranger');
$sql_insert = "insert into user(uid,unm,pwd,scl,phb,eml) values (?,?,?,?,?,?)";
$res = array();
if(mysqli_connect_errno()){
    $mysqli->close();
}else{
    file_put_contents("log.txt", "2222",FILE_APPEND);
    if($result = $mysqli->prepare($sql_insert)){
        $result->bind_param("ssssss",$uid,$unm,$pwd,$scl,$phb,$eml);
        $uid = $_POST['name'];
        $unm = $_POST['school'];
        $pwd = $_POST['password'];
        $scl = $_POST['school'];
        $phb = $_POST['phone'];
        $eml = $_POST['email'];
        if($result->execute()){
            $res['status'] = 'ok';
            file_put_contents("log.txt", "successR",FILE_APPEND);
        }else{
            $res['status'] = 'error';
            file_put_contents("log.txt", "faildR",FILE_APPEND);
        }
        $result->close();
    }
}
$mysqli->close();
file_put_contents("log.txt", "successR222",FILE_APPEND);
echo json_encode($res);
?>