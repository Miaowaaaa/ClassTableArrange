<?php
session_start();
$res = array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    $uid = $_SESSION['uid'];
    if (isset($_POST['g_ids'])) {
        $mysqli = new mysqli("localhost", "root", "scrovormysql", 'easyArranger');

        $gid = $_POST['g_ids'];
        $sql_delete_grade = "delete from grades where gid in($gid) and uid='$uid'";
        $sql_delete_class = "delete from classes where gid in($gid) and uid='$uid'";
        $sql_delete_subject = "delete from grade_subs where gid in($gid) and uid='$uid'";
        $sql_delete_arrange = "delete from arraged_classes where gid in($gid) and uid='$uid'";
        if (mysqli_connect_errno()) {
            $mysqli->close();
            file_put_contents("log.txt", "connect error", FILE_APPEND);
            $res['status'] = 'error';
        } else {
            if ($result = $mysqli->prepare($sql_delete_class)) {
                if ($result->execute()) {
                    $res['status_c'] = 'ok';
                } else {
                    $res['status_c'] = 'error';
                }
                $result->close();
            } else {
                file_put_contents("log.txt", "\nparepared error of delete class!", FILE_APPEND);
                $res['status_c'] = 'error';
            }
            if ($result = $mysqli->prepare($sql_delete_subject)) {
                if ($result->execute()) {
                    $res['status_s'] = 'ok';
                } else {
                    $res['status_s'] = 'error';
                }
                $result->close();
            } else {
                file_put_contents("log.txt", "\nparepared error of delete subject of grade!", FILE_APPEND);
                $res['status_s'] = 'error';
            }
            if ($result = $mysqli->prepare($sql_delete_grade)) {
                if ($result->execute()) {
                    $res['status_g'] = 'ok';
                } else {
                    $res['status_g'] = 'error';
                }
                $result->close();
            } else {
                file_put_contents("log.txt", "\nparepared error of delete grade!", FILE_APPEND);
                $res['status_g'] = 'error';
            }
            if ($result = $mysqli->prepare($sql_delete_arrange)) {
                if ($result->execute()) {
                    $res['status_a'] = 'ok';
                } else {
                    $res['status_a'] = 'error';
                }
                $result->close();
            } else {
                file_put_contents("log.txt", "\nparepared error of delete grade!", FILE_APPEND);
                $res['status_g'] = 'error';
            }
        }
        $mysqli->close();
    } else {
        $res['status'] = 'error';
        file_put_contents("log.txt", "no gid", FILE_APPEND);
    }
} else {
    $res['status'] = 'offline';
}
echo json_encode($res);
?>