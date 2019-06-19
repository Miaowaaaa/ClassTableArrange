<?php
session_start();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    $filepath = $_GET['filepath'];
    $file=fopen($filepath,"r");
    $filesize = filesize($filepath);
    $filecount = 0;
    $buffer=1024;  //设置一次读取的字节数，每读取一次，就输出数据（即返回给浏览器）
    header("Content-Type: application/octet-stream");
    header("Accept-Ranges: bytes");
    header("Accept-Length: ". $filesize);
    header("Content-Disposition: attachment; filename=$filepath");
    while(!feof($file) && $filecount<$filesize){
        $file_con=fread($file,$buffer);
        $filecount+=$buffer;
        echo $file_con;
    }
    fclose($file);
    unlink($filepath);              //删除文件
}
?>