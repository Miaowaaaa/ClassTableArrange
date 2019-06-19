<?php
include_once 'teacherInfo.php';
session_start();
$res = array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    $uid = $_SESSION['uid'];
    $mysqli = new mysqli("localhost","root","scrovormysql",'easyArranger');
    $sql  = "select teacher.tid,teacher.tname,teacher.constr from teacher where ".
            "teacher.uid='$uid'";
    $sql_subs = "select distinct teach.tid,sname from teach,subjects where teach.sid=subjects.sid and teach.uid='$uid'";
    if(mysqli_connect_errno()){
        $mysqli->close();
        file_put_contents("log.txt","[get Teacher connect error!]",FILE_APPEND);
    }else{
        if($result = $mysqli->query($sql)){
            $tmp = array();
            $c = 0;
            //file_put_contents("log.txt","111111" ,FILE_APPEND);
            while($row = mysqli_fetch_assoc($result)){
                    $t = new Teacher();
                    $t->setRid(++$c);
                    $t->setId($row['tid']);
                    $tt = $row['tid'];
                    $t->setCnstr($row['constr']);
                    $t->setTname($row['tname']);
                    $t->setTable("<a href='#$tt?getTeacherTable.php' onclick='t_showTable(this)'>查看课表</a>");
                    file_put_contents("log.txt","<a href='#$tt?getTeacherTable.php' onclick='t_showTable(this)'>查看课表</a>",FILE_APPEND);
                    $tmp[$row['tid']] = $t;
                
            }
            $subs = $mysqli->query($sql_subs);
            while($sub = mysqli_fetch_assoc($subs)){
                if(isset($tmp[$sub['tid']])){
                    if(null!=$tmp[$sub['tid']]->subs || strlen($tmp[$sub['tid']]->subs)){
                        $tmp[$sub['tid']]->appendSub($sub['sname']);
                    }else{
                        $tmp[$sub['tid']]->setSubs($sub['sname']);
                    }
                }
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