<?php
include_once 'subjectInfo.php';
session_start();
$rows = array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    
    $mysqli = new mysqli("localhost","root","scrovormysql",'easyArranger');
    $sql  = "select * from subjects";
    if(mysqli_connect_errno()){
        $mysqli->close();
        file_put_contents("log.txt", "\nconnect error!", FILE_APPEND);
    }else{
        $c = 0;
        if($result = $mysqli->query($sql)){
            while($row = mysqli_fetch_assoc($result)){
                $sub = new simpleSubject();
                $sub->setId($row['sid']);
                $sub->setName($row['sname']);
                $rows[$c++] = $sub;
            }
            
        }
        
    }
    $mysqli->close();
}
echo json_encode($rows);
?>