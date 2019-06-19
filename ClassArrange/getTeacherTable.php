<?php
include_once 'teacherTable.php';
session_start();
$res = array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    $uid = $_SESSION['uid'];
    if (isset($_POST['tid']) && "" != $_POST['tid']) {
        $tid = $_POST['tid'];
        $mysqli = new mysqli("localhost", "root", "scrovormysql", 'easyArranger');
        $sql_teacher_table = "select distinct sname,cname,did,perweek,perday from arraged_classes,grades,subjects,classes" 
                . " where arraged_classes.tid='$tid' and arraged_classes.uid='$uid' and arraged_classes.sid=subjects.sid and arraged_classes.gid=grades.gid"
                . " and arraged_classes.cid=classes.cid";
        if (mysqli_connect_errno()) {
            $mysqli->close();
            file_put_contents("log.txt", "[get Teacher connect error!]", FILE_APPEND);
        } else {
            if ($result = $mysqli->query($sql_teacher_table)) {
                $teacherTable = new Teachertable();
                $c = 0;
                while($row = mysqli_fetch_assoc($result)){
                    $teacherTable->perday = $row['perday'];
                    $teacherTable->perweek = $row['perweek'];
                    $teacherTable->teacher = 'is';
                    $sub = new TeacherSubject();
                    $sub->cname = $row['cname'];
                    $sub->sname = $row['sname'];
                    $sub->did = $row['did'];
                    $teacherTable->subjects[$c++] = $sub;   
                }
                $res['status'] = 'ok';
                $res['data'] = $teacherTable;
            }else{
                file_put_contents('log.txt', "query error in get Teacher table",FILE_APPEND);
                $res['status'] = 'error';
            }
        }
    }else{
        file_put_contents('log.txt', "tid param error in getTeacher table",FILE_APPEND);
        $res['status'] = 'error';
    }
}else{
    file_put_contents('log.txt', "offline",FILE_APPEND);
    $res['status'] = 'offline';
}
echo json_encode($res);
?>