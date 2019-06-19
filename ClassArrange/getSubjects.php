<?php
include_once 'subjectInfo.php';
session_start();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    $uid = $_SESSION['uid'];
    $mysqli = new mysqli("localhost", "root", "scrovormysql", 'easyArranger');
    $sql = "select * from subjects where uid='$uid'";
    if(isset($_POST['gid']) && null !=$_POST['gid']){
        $gid = $_POST['gid'];
        $sql = "select * from subjects where uid='$uid' and sid in(select sid from grade_subs where gid".
               "='$gid' and uid='$uid')";
    }
    $res = new Result();
    $rows = array();
    if (mysqli_connect_errno()) {
        $mysqli->close();
    } else {
        if ($result = $mysqli->query($sql)) {
            $count = 1;
            $c = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $sub = new Subject();
                $sub->setRId($count ++);
                $sub->setId($row['sid']);
                $sub->setName($row['sname']);
                if ($row['prim'] == 1)
                    $sub->setPrim("是");
                else
                    $sub->setPrim("否");
                $rows[$c ++] = $sub;
            }
        }
    }
    $mysqli->close();
}
echo json_encode($rows);
?>