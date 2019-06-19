<?php
session_start();
$res = array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    //file_put_contents("log.txt", "\n has session", FILE_APPEND);
    if (isset($_POST['per_day']) && isset($_POST['per_week']) 
        && isset($_POST['gid']) 
        && isset($_POST['gname'])
        && isset($_POST['gsubs'])
        && isset($_POST['mtime'])
        && isset($_POST['cnum'])) {
        $mysqli = new mysqli("localhost", "root", "scrovormysql", 'easyArranger');
        $sql_insert_grade = "insert into grades(gid,gname,uid,gcn,perday,perweek,mtime) values (?,?,?,?,?,?,?)";
        $sql_insert_subs = "insert into grade_subs(uid,gid,sid) values(?,?,?)";
        if (mysqli_connect_errno()) {
            $mysqli->close();
            file_put_contents("log.txt", "connect error in addGrade!", FILE_APPEND);
        } else {
            if ($result1 = $mysqli->prepare($sql_insert_grade)) {
                $result1->bind_param("sssiiis", $gid, $gname, $uid,$gcn,$pd,$pw,$mtime);
                $gid = $_POST['gid'];
                $gname = $_POST['gname'];
                $uid = $_SESSION['uid'];
                $gcn = $_POST['cnum'];
                $pd = $_POST['per_day'];
                $pw = $_POST['per_week'];
                $mtime = $_POST['mtime'];
                if ($result1->execute()) {
                    $res['status_g'] = 'ok';
                } else {
                    $res['status_g'] = 'error';
                }
                $result1->close();
            } else {
                file_put_contents("log.txt", "\nparepared error!t", FILE_APPEND);
            }
            if ($result = $mysqli->prepare($sql_insert_subs)) {
                $subs = explode(";", $_POST['gsubs']);
               // file_put_contents("log.txt", json_encode($subs), FILE_APPEND);
                for ($i = 0; $i < count($subs); $i ++) {
                    $result->bind_param("sss", $uid, $gid, $sid);
                    $uid = $_SESSION['uid'];
                    $gid = $_POST['gid'];
                    $sid = $subs[$i];
                    if ($result->execute()) {
                        $res['status_s'] = 'ok';
                    } else {
                        $res['status_s'] = 'error';
                    }
                }
                $result->close();
                //file_put_contents("log.txt", "\ninsert into teach", FILE_APPEND);
            } else {
                file_put_contents("log.txt", "\nparepared error!s", FILE_APPEND);
            }
        }
        $mysqli->close();
    }else{
        $res['status'] = 'error';
    }
}else{
    $res['status'] = 'offline';
}
print json_encode($res);
?>