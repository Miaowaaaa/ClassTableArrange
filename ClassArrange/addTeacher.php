<?php
session_start();
$res = array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    if (isset($_POST['t_id']) && isset($_POST['t_name']) && isset($_POST['t_sub']) && isset($_POST['t_cnstr'])) {
        $mysqli = new mysqli("localhost", "root", "scrovormysql", 'easyArranger');
        $sql_insert = "insert into teacher(tid,uid,pwd,tname,constr) values (?,?,?,?,?)";
        $sql_insert_teach = "insert into teach(uid,tid,sid,constr) values(?,?,?,?)";
        if (mysqli_connect_errno()) {
            $mysqli->close();
            file_put_contents("log.txt", "connect error!", FILE_APPEND);
        } else {
            if ($result1 = $mysqli->prepare($sql_insert)) {
                $result1->bind_param("sssss", $tid, $uid, $pwd, $tname, $tcnstr);
                $uid = $_SESSION['uid'];
                $tid = $_POST['t_id'];
                $pwd = $_POST['t_id'];
                $tname = $_POST['t_name'];
                $tcnstr = $_POST['t_cnstr'];
                if ($result1->execute()) {
                    $res['status_t'] = 'ok';
                } else {
                    $res['status_t'] = 'error';
                }
                $result1->close();
            } else {
                file_put_contents("log.txt", "\nparepared error!t", FILE_APPEND);
            }
            if ($result = $mysqli->prepare($sql_insert_teach)) {
                $subs = explode(";", $_POST['t_sub']);
                //file_put_contents("log.txt", json_encode($subs), FILE_APPEND);
                for ($i = 0; $i < count($subs); $i ++) {
                    $result->bind_param("ssss", $uid, $tid, $sid, $tcnstr);
                    $uid = $_SESSION['uid'];
                    $tid = $_POST['t_id'];
                    $sid = $subs[$i];
                    $tcnstr = $_POST['t_cnstr'];
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
//file_put_contents("log.txt", json_encode($res), FILE_APPEND);
?>
   