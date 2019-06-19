<?php
include_once 'gradeInfo.php';
session_start();
$res = array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    $uid = $_SESSION['uid'];
    $mysqli = new mysqli("localhost","root","scrovormysql",'easyArranger');
    $sql  = "select distinct grades.gid,grades.gname,grades.gcn,grades.perday,grades.perweek,grades.mtime from grades".
            " where grades.uid='$uid'";
    $sql_subs = "select distinct grade_subs.gid,sname from grade_subs,subjects where grade_subs.sid=subjects.sid and subjects.uid='$uid'";
    if(mysqli_connect_errno()){
        $mysqli->close();
        file_put_contents("log.txt","[get Teacher connect error!]",FILE_APPEND);
    }else{
        if($result = $mysqli->query($sql)){
            $tmp = array();
            $c = 0;
            //file_put_contents("log.txt","111111" ,FILE_APPEND);
            while($row = mysqli_fetch_assoc($result)){
                   $t = new Grade();
                   $t->setRid(++$c);
                   $t->setId($row['gid']);
                   $t->setName($row['gname']);
                   $t->setCnum($row['gcn']);
                   $t->setDnum($row['perweek']);
                   $t->setSnum($row['perday']);
                   $t->setMtime($row['mtime']);
                   $t->setArrange("<a href='userworkspace.html?gid=". $row['gid'] . "&gname=" . urlencode($row['gname']) . "'>进行排课</a>");
                   $tmp[$row['gid']] = $t;
            }
            $subs = $mysqli->query($sql_subs);
            //file_put_contents("log.txt","[query2222222222222 error!]",FILE_APPEND);
            while($sub = mysqli_fetch_assoc($subs)){
                if(isset($tmp[$sub['gid']])){
                    if(null!=$tmp[$sub['gid']]->getSubs() ||strlen($tmp[$sub['gid']]->getSubs())){
                        $tmp[$sub['gid']]->appendSub($sub['sname']);
                    }else{
                        $tmp[$sub['gid']]->setSubs($sub['sname']);
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
//file_put_contents("log.txt",json_encode($res),FILE_APPEND);
?>