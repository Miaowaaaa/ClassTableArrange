<?php
session_start();
$res = array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    $uid = $_SESSION['uid'];
    if (isset($_POST['c_ids'])) {
        $mysqli = new mysqli("localhost", "root", "scrovormysql", 'easyArranger');

        $cid = $_POST['c_ids'];
        $ids = explode(",",$cid);
        $delete_class = "delete from classes where cid in($cid) and uid='$uid'";
        $delete_class_table = "delete from arraged_classes where cid in($cid) and uid='$uid'";
        if (mysqli_connect_errno()) {
            $mysqli->close();
            file_put_contents("log.txt", "connect error", FILE_APPEND);
            $res['status'] = 'error';
        } else {
            if ($result = $mysqli->prepare($delete_class)) {
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
            if ($result = $mysqli->prepare($delete_class_table)) {
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