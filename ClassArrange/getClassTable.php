<?php
include_once 'teacherTable.php';
session_start();
$res = array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    $uid = $_SESSION['uid'];
    if (isset($_POST['tid']) && "" != $_POST['tid']) {
        $cid = $_POST['tid'];
        $mId = 0;
        $gid = substr($cid, 0,2);
        $mysqli = new mysqli("localhost", "root", "scrovormysql", 'easyArranger');
        $sql_table = "select distinct sname,tname,did,perweek,perday from arraged_classes,grades,subjects,classes,teacher"
            . " where arraged_classes.cid='$cid' and arraged_classes.uid='$uid' and arraged_classes.sid=subjects.sid and arraged_classes.gid=grades.gid"
            . " and arraged_classes.cid=classes.cid and arraged_classes.tid=teacher.tid";
        $sql_mtime = "select perweek,mtime from grades where gid='$gid' and uid='$uid'";
        if (mysqli_connect_errno()) {
            $mysqli->close();
            file_put_contents("log.txt", "[get Class table connect error!]", FILE_APPEND);
        } else {
            if ($result = $mysqli->query($sql_mtime)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $perweek = $row['perweek'];
                    $mtime = $row['mtime'];
                    $r = explode('-', $mtime);
                    $mId = $r[0] + ($r[1]-1)*$perweek;
                }
            } else {
            
            }
            if ($result = $mysqli->query($sql_table)) {
                $teacherTable = new Teachertable();
                $c = 0;
                while($row = mysqli_fetch_assoc($result)){
                    $teacherTable->perday = $row['perday'];
                    $teacherTable->perweek = $row['perweek'];
                    $sub = new TeacherSubject();
                    $sub->sname = $row['sname'];
                    $sub->cname = $row['tname'];
                    $sub->did = $row['did'];
                    $teacherTable->subjects[$c++] = $sub;
                }
                $sub = new TeacherSubject();
                $sub->sname = "班会";
                $sub->did = $mId;
                $teacherTable->subjects[$c++] = $sub;
                $res['status'] = 'ok';
                $res['data'] = $teacherTable;
            }else{
                file_put_contents('log.txt', "query error in get Class table",FILE_APPEND);
                $res['status'] = 'error';
            }
        }
    }else{
        file_put_contents('log.txt', "tid param error in get Class table",FILE_APPEND);
        $res['status'] = 'error';
    }
}else{
    file_put_contents('log.txt', "offline",FILE_APPEND);
    $res['status'] = 'offline';
}
echo json_encode($res);
?>