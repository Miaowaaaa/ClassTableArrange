<?php
require_once "/phpword/PHPWord.php";
session_start();
$res = array();
function getNumText($num){
    switch ($num){
        case 1:return "一";
        case 2:return "二";
        case 3:return "三";
        case 4:return "四";
        case 5:return "五";
        case 6:return "六";
        case 7:return "日";
    }
}
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    $uid = $_SESSION['uid'];
    if (isset($_POST['table']) && $_POST['table'] != "") {
        $content = json_decode($_POST['table'],true);
        $perday = $content['perday'];
        $perweek = $content['perweek'];
        $cname = isset($content['cname'])?$content['cname']:"";
        $cid = isset($content['cid'])?$content['cid']:time();
        $addTeacher = isset($content['teacher'])?true:false;
        $subjects = $content['subjects'];
        $PHPWord = new PHPWord();
        date_default_timezone_set("Asia/Shanghai"); // 设置一个时区
                                                    // New portrait section
        $section = $PHPWord->createSection();
        $styleTable = array(
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80
        );
        $styleFirstRow = array(
            'borderBottomSize' => 18,
            'borderBottomColor' => '0000ff',
            'bgColor' => '66bbff'
        );
        // 定义单元格样式数组
        $styleCell = array(
            'valign' => 'center'
        );
        //标题居中
        $titleStyle = array(
            'align' => 'center'
        );
        // 定义第一行的字体
        $titleFontStyle = array(
            'bold' => true,
            'size' => 30,
            'align' => 'center'
        );
        $cnameStyle = array(
            'align'=>'right'
        );
        $cnameFontStyle = array(
            'bold' => true,
            'size' => 15,
            'align' => 'center'
        );
        // cell 标题样式
        $cellTitleFontStyle = array(
            'bold' => true,
            'size' => 18,
            'align' => 'center'
        );
        $cellSubFontStyle = array(
            'bold' => false,
            'size' => 18,
            'align' => 'center'
        );
        // 添加表格样式
        $PHPWord->addTableStyle('myOwnTableStyle', $styleTable);
        $section->addTextBreak(3);
        $section->addText("课 程 表",$titleFontStyle,$titleStyle);
        $section->addTextBreak(2);
        $section->addText($cname,$cnameFontStyle,$cnameStyle);
        $section->addTextBreak(1);
        // 添加表格
        $table = $section->addTable('myOwnTableStyle');
        for($r = 0; $r <$perday+1; $r++) { // Loop through rows
            // Add row
            if($r == 0){
                $table->addRow(1500);
            }else{
                $table->addRow(1000);
            }
            for($c = 0; $c < 8; $c++) { // Loop through cells
                // Add Cell
                $flag_cell = false;
                if($c == 0 && $r!=0){
                    $table->addCell(650,$styleCell)->addText("$r",$cellTitleFontStyle);
                }else if($r == 0 && $c!=0){
                    $table->addCell(1500,$styleCell)->addText("周 " . getNumText($c),$cellTitleFontStyle);
                }else{
                    foreach($subjects as $sub){
                        if($c<=$perweek && $c>0 && $r>0&&$r<=$perday && $sub['did'] == $c+($r-1)*$perweek){
                            if($addTeacher){
                                $name = substr($sub['cname'],6,strlen($sub['cname']));
                                $table->addCell(1500,$styleCell)->addText($sub['sname'] . " ($name)",$cellSubFontStyle);
                            }else{
                                $table->addCell(1500,$styleCell)->addText($sub['sname'],$cellSubFontStyle);
                            }
                            $flag_cell = true;
                            break;
                        }
                    }
                    if(!$flag_cell) $table->addCell(1500,$styleCell)->addText("",$cellSubFontStyle);
                    
                }
            }
        }
        $objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
        $objWriter->save($uid . "" . $cid . '.docx');
        $res['filepath'] = $uid . "" . $cid . '.docx';
        $res['status'] = 'ok';
    } else {
        $res['status'] = 'error';
    }
} else {
    $res['status'] = 'offline';
}
echo json_encode($res);
?>
