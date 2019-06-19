<?php
session_start();
$res = array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    $uid = $_SESSION['uid'];
    if (isset($_POST['s_ids'])) {
        $mysqli = new mysqli("localhost", "root", "scrovormysql", 'easyArranger');

        $sid = $_POST['s_ids'];
        file_put_contents("log.txt", $sid, FILE_APPEND);
        $sql_insert = "delete from subjects where sid in($sid) and uid='$uid'";
        file_put_contents("log.txt", $sql_insert, FILE_APPEND);
        if (mysqli_connect_errno()) {
            $mysqli->close();
            file_put_contents("log.txt", "connect error", FILE_APPEND);
            $res['status'] = 'error';
        } else {
            if ($result = $mysqli->prepare($sql_insert)) {
                if ($result->execute()) {
                    $res['status'] = 'ok';
                } else {
                    $res['status'] = 'error';
                }
                $result->close();
            } else {
                file_put_contents("log.txt", "\nparepared error!", FILE_APPEND);
                $res['status'] = 'error';
            }
        }
        $mysqli->close();
    } else {
        $res['status'] = 'error';
        file_put_contents("log.txt", "no sid", FILE_APPEND);
    }
} else {
    $res['status'] = 'offline';
}
echo json_encode($res);
?>