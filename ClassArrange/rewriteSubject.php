<?php
session_start();
$res = array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    $uid = $_SESSION['uid'];
    if (isset($_POST['s_id']) && isset($_POST['s_name']) && isset($_POST['s_prim'])) {
        $mysqli = new mysqli("localhost", "root", "scrovormysql", 'easyArranger');
        
        $sid = $_POST['s_id'];
        $sname = $_POST['s_name'];
        if ($_POST['s_prim'] == '是')
            $prim = 1;
        else
            $prim = 0;
        $sql_insert = "update subjects set sname='$sname',prim=$prim where sid='$sid' and uid='$uid'";
        if (mysqli_connect_errno()) {
            $mysqli->close();
            file_put_contents("log.txt", "222233", FILE_APPEND);
            $res['status'] = 'error';
        } else {
            if ($result = $mysqli->prepare($sql_insert)) {
                if ($result->execute()) {
                    $res['status'] = 'ok';
                } else {
                    $res['status'] = 'error';
                }
                file_put_contents("log.txt", $sname, FILE_APPEND);
                $result->close();
            } else {
                file_put_contents("log.txt", "\nparepared error!", FILE_APPEND);
                $res['status'] = 'error';
            }
        }
        $mysqli->close();
    } else {
        $res['status'] = 'error';
    }
} else {
    $res['status'] = 'offline';
}
echo json_encode($res);
?>