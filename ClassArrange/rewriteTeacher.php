<?php
session_start();
$res = array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    $uid = $_SESSION['uid'];
    if (isset($_POST['t_id']) && isset($_POST['t_name']) && isset($_POST['t_sub'])&& isset($_POST['t_cnstr'])) {
        $mysqli = new mysqli("localhost", "root", "scrovormysql", 'easyArranger');

        $tid = $_POST['t_id'];
        $tname = $_POST['t_name'];
        $tsubs  = $_POST['t_sub'];
        $tcnstr = $_POST['t_cnstr'];
        $update_teacher = "update teacher set tid='$tid',tname='$tname',constr='$tcnstr' where tid='$tid' and uid='$uid'" ;
        $update_teach_delete = "delete from teach where tid='$tid' and uid='$uid'";
        $update_teach_insert = "insert into teach(uid,tid,sid,constr) values(?,?,?,?)";
        if (mysqli_connect_errno()) {
            $mysqli->close();
            file_put_contents("log.txt", "222233", FILE_APPEND);
            $res['status'] = 'error';
        } else {
            //update teacher info in teacher table
            if ($result = $mysqli->prepare($update_teacher)) {
                if ($result->execute()) {
                    $res['status_t'] = 'ok';
                } else {
                    $res['status_t'] = 'error';
                }
                $result->close();
            } else {
                file_put_contents("log.txt", "\nparepared error!", FILE_APPEND);
                $res['status_t'] = 'error';
            }
            
            //delete teacher's subs from teach
            if($result1 = $mysqli->prepare($update_teach_delete)){
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
            if($result2 = $mysqli->prepare($update_teach_insert)){
                $subs = explode(";", $_POST['t_sub']);
                    file_put_contents("log.txt", json_encode($subs), FILE_APPEND);
                    for ($i = 0; $i < count($subs); $i ++) {
                        $result2->bind_param("ssss", $u, $t, $s, $c);
                        $u = $uid;
                        $t = $tid;
                        $s = $subs[$i];
                        $c = $tcnstr;
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