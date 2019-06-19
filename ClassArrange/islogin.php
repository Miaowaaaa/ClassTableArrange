<?php
session_start();
$res = array();
if(isset($_SESSION['uid']) && $_SESSION['uid']!=null){
    $res['uid'] = $_SESSION['uid'];
    $res['status'] = 'ok';
}else{
    $res['status'] = 'error';
}
echo json_encode($res);
?>
