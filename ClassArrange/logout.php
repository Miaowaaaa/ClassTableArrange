<?php
session_start();
$res = array();
if(isset($_SESSION['uid']) && $_SESSION['uid'] != null){
    session_destroy();
    $res['status'] = 'ok';
}
echo json_encode($res);
?>