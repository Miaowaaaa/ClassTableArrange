<?php
session_start();
$res = array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    $uid = $_SESSION['uid'];
    if (isset($_POST['per_day']) && isset($_POST['per_week']) 
        && isset($_POST['gid']) 
        && isset($_POST['gname'])
        && isset($_POST['gsubs'])
        && isset($_POST['mtime'])
        && isset($_POST['cnum'])) {
        $mysqli = new mysqli("localhost", "root", "scrovormysql", 'easyArranger');

        $gid = $_POST['gid'];
        $gname = $_POST['gname'];
        $mtime  = $_POST['mtime'];
        $cnum = $_POST['cnum'];
        $perday = $_POST['per_day'];
        $perweek = $_POST['per_week'];
        $gsubs = $_POST['gsubs'];
        $update_grade = "update grades set gid='$gid',gname='$gname',perday=$perday,perweek=$perweek,mtime='$mtime' where gid='$gid' and uid='$uid'" ;
        $update_subs_delete = "delete from grade_subs where gid='$gid' and uid='$uid'";
        $update_subs_insert = "insert into grade_subs(uid,gid,sid) values(?,?,?)";
        if (mysqli_connect_errno()) {
            $mysqli->close();
            file_put_contents("log.txt", "222233", FILE_APPEND);
            $res['status'] = 'error';
        } else {
            //update teacher info in teacher table
            if ($result = $mysqli->prepare($update_grade)) {
                if ($result->execute()) {
                    $res['status_g'] = 'ok';
                } else {
                    $res['status_g'] = 'error';
                }
                $result->close();
            } else {
                file_put_contents("log.txt", "\nparepared error!", FILE_APPEND);
                $res['status_g'] = 'error';
            }

            //delete teacher's subs from teach
            if($result1 = $mysqli->prepare($update_subs_delete)){
                if ($result1->execute()) {
                    $res['status_s'] = 'ok';
                } else {
                    $res['status_s'] = 'error';
                }
                $result1->close();
            }else {
                file_put_contents("log.txt", "\nparepared error in delete teach!", FILE_APPEND);
                $res['status_s'] = 'error';
            }

            //reinsert into teach table
            if($result2 = $mysqli->prepare($update_subs_insert)){
                $subs = explode(";", $gsubs);
                for ($i = 0; $i < count($subs); $i ++) {
                    $result2->bind_param("sss", $u, $g, $s);
                    $u = $uid;
                    $g = $gid;
                    $s = $subs[$i];
                    if ($result2->execute()) {
                        $res['status_s'] = 'ok';
                    } else {
                        $res['status_s'] = 'error';
                    }
                }
                $result2->close();
            }else {
                file_put_contents("log.txt", "\nparepared error in insert teach!", FILE_APPEND);
                $res['status_s'] = 'error';
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