<?php
session_start();
$res = array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    $uid = $_SESSION['uid'];
    file_put_contents('log.txt',$_POST['tables'] . PHP_EOL, FILE_APPEND);
    if (isset($_POST['tables'])) {
        $mysqli = new mysqli("localhost", "root", "scrovormysql", 'easyArranger');
        $data = json_decode($_POST['tables'],true);
        $gid = $data['gid'];
        $sql_insert_tableArrange = "insert into arraged_classes(gid,cid,did,sid,tid,uid) values('$gid',?,?,?,?,'$uid')";
        $sql_delete_tableArrange = "delete from arraged_classes where gid='$gid' and uid='$uid'";
        if (mysqli_connect_errno()) {
            $mysqli->close();
            file_put_contents("log.txt", "connect error!", FILE_APPEND);
            $res['status'] = 'error';
        } else {
            if ($result = $mysqli->prepare($sql_delete_tableArrange)) {
                if(!$result->execute()){
                    $res['status'] = 'error';
                    file_put_contents('log.txt', "delete error!" . PHP_EOL, FILE_APPEND);
                }
            }else{
                $res['status'] = 'error';
                file_put_contents('log.txt', $sql_delete_tableArrange . PHP_EOL, FILE_APPEND);
            }
            $cids = $data['cids'];
            $tables = $data['tables'];
            for ($l = 0; $l < count($cids); $l++) {
                $class = $tables[$cids[$l]];
                $subjects = $class['subjects'];
                for ($j = 0; $j < count($subjects); $j ++) {  
                    if (isset($subjects[$j]['tid']) && $subjects[$j]['tid'] != "") {
                        if ($result = $mysqli->prepare($sql_insert_tableArrange)) {
                            $result->bind_param("siss", $cid, $did, $sid, $tid);
                            $cid = $class['cid'];
                            $did = $subjects[$j]['did'];
                            $sid = $subjects[$j]['sid'];
                            $tid = $subjects[$j]['tid'];
                            if (!$result->execute()) {
                                file_put_contents('log.txt', 'execute insert to arraged_classess error' . PHP_EOL, FILE_APPEND);
                                $res['status'] = 'error';
                            } else {
                                $res['status'] = 'ok';
                                file_put_contents('log.txt', 'insert to arraged_classess success' . PHP_EOL, FILE_APPEND);
                            }
                        } else {
                            file_put_contents('log.txt', 'prepare insert to arraged_classess error' . PHP_EOL, FILE_APPEND);
                            file_put_contents('log.txt', $sql_insert_tableArrange . PHP_EOL, FILE_APPEND);
                            $res['status'] = 'error';
                        }
                    }
                }
            }
        }
    } else {
        $res['status'] = 'error';
        file_put_contents('log.txt', 'not params' . PHP_EOL, FILE_APPEND);
    }
} else {
    $res['status'] = 'offline';
}
echo json_encode($res);
?>