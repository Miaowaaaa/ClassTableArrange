<?php
session_start();
$res = array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    
    if (isset($_POST['s_id']) && isset($_POST['s_name']) && isset($_POST['s_prim'])) {
        $mysqli = new mysqli("localhost", "root", "scrovormysql", 'easyArranger');
        $sql_insert = "insert into subjects(sid,uid,sname,prim) values (?,?,?,?)";
        if (mysqli_connect_errno()) {
            $mysqli->close();
            file_put_contents("log.txt", "222233", FILE_APPEND);
            $res['status'] = 'error';
        } else {
            //file_put_contents("log.txt", "2222", FILE_APPEND);
            if ($result = $mysqli->prepare($sql_insert)) {
                $result->bind_param("sssi", $sid, $uid, $sname, $prim);
                $uid = '1001';
                $sid = $_POST['s_id'];
                $sname = $_POST['s_name'];
                if ($_POST['s_prim'] == '是')
                    $prim = 1;
                else
                    $prim = 0;
                if ($result->execute()) {
                    $res['status'] = 'ok';
                } else {
                    $res['status'] = 'error';
                }
                //file_put_contents("log.txt", $sname, FILE_APPEND);
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