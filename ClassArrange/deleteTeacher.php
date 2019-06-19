<?php
session_start();
$res = array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    $uid = $_SESSION['uid'];
    if (isset($_POST['t_ids'])) {
        $mysqli = new mysqli("localhost", "root", "scrovormysql", 'easyArranger');

        $tid = $_POST['t_ids'];
        $sql_delete_teach = "delete from teach where tid in($tid) and uid='$uid'";
        $sql_delete_teacher = "delete from teacher where tid in($tid) and uid='$uid'";
        if (mysqli_connect_errno()) {
            $mysqli->close();
            file_put_contents("log.txt", "connect error", FILE_APPEND);
            $res['status'] = 'error';
        } else {
            if ($result = $mysqli->prepare($sql_delete_teach)) {
                if ($result->execute()) {
                    $res['status_s'] = 'ok';
                } else {
                    $res['status_s'] = 'error';
                }
                $result->close();
            } else {
                file_put_contents("log.txt", "\nparepared error of delete teach!", FILE_APPEND);
                $res['status_s'] = 'error';
            }
            if ($result = $mysqli->prepare($sql_delete_teacher)) {
                if ($result->execute()) {
                    $res['status_t'] = 'ok';
                } else {
                    $res['status_t'] = 'error';
                }
                $result->close();
            } else {
                file_put_contents("log.txt", "\nparepared error of delete teacher!", FILE_APPEND);
                $res['status_t'] = 'error';
            }
        }
        $mysqli->close();
    } else {
        $res['status_t'] = 'error';
        file_put_contents("log.txt", "no sid", FILE_APPEND);
    }
} else {
    $res['status'] = 'offline';
}
echo json_encode($res);
?>