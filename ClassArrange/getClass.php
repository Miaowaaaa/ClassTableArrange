<?php
include_once 'classInfo.php';
session_start();
$res = array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    $uid = $_SESSION['uid'];
    $mysqli = new mysqli("localhost", "root", "scrovormysql", 'easyArranger');
    $sql = "select distinct gname,cid,cname from classes,grades where classes.gid=grades.gid and grades.uid='$uid'";
    if (mysqli_connect_errno()) {
        $mysqli->close();
        file_put_contents("log.txt", "[get Teacher connect error!]", FILE_APPEND);
    } else {
        if ($result = $mysqli->query($sql)) {
            $c = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $t = new ClassInfo();
                $t->setRid($c + 1);
                $t->setId($row['cid']);
                $tcid = $row['cid'];
                $t->setCname($row['cname']);
                $t->setGrade($row['gname']);
                $t->setTable("<a href='#$tcid?getClassTable.php' onclick='t_showTable(this)'>查看课表</a>");
                $res[$c++] = $t;
            }
        } else {
            file_put_contents("log.txt", "[query error!]", FILE_APPEND);
        }
    }
    $mysqli->close();
} else {
    file_put_contents("log.txt", "[get Teacher offline error!]", FILE_APPEND);
}
echo json_encode($res);
?>