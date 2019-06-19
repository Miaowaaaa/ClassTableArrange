<?php
include_once 'SimpleTeacher.php';
session_start();
$res = array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    $uid = $_SESSION['uid'];
    $mysqli = new mysqli("localhost","root","scrovormysql",'easyArranger');
    $sql  = "select distinct teacher.tid,teacher.tname,subjects.sname from teacher,teach,subjects where".
            " teacher.tid=teach.tid and teach.sid=subjects.sid and teach.uid='$uid'";
    if(mysqli_connect_errno()){
        $mysqli->close();
        file_put_contents("log.txt","[get Teacher connect error!]",FILE_APPEND);
    }else{
        if($result = $mysqli->query($sql)){
            $tmp = array();
            //file_put_contents("log.txt","111111" ,FILE_APPEND);
            while($row = mysqli_fetch_assoc($result)){
                    $t = new simpleTeacher();
                    $t->setId($row['tid']);
                    $t->setName($row['tname']);
                    $t->setSname($row['sname']);
                    $tmp[$row['tid']] = $t; 
            }
            $c = 0;
            foreach($tmp as $t){
                $res[$c++] = $t;
            }
            //file_put_contents("log.txt","1111113333" ,FILE_APPEND);
        }else{
            file_put_contents("log.txt","[query error!]",FILE_APPEND);
        }
    }
    $mysqli->close();
}else{
    file_put_contents("log.txt","[get Teacher offline error!]",FILE_APPEND);
}
echo json_encode($res);
//file_put_contents("log.txt", json_encode($res),FILE_APPEND);
?>