<?php
include_once 'subjectInfo.php';
include_once 'arrangeTables.php';
session_start();
$res = Array();
if (isset($_SESSION['uid']) && $_SESSION['uid'] != null) {
    $uid = $_SESSION['uid'];
    $mysqli = new mysqli("localhost", "root", "scrovormysql", 'easyArranger');
    if (isset($_POST['gid']) && null != $_POST['gid']) {
        $gid = $_POST['gid'];
        $sql = "select * from subjects where uid='$uid' and sid in(select sid from grade_subs where gid" . "='$gid' and uid='$uid')";
        $sql_teach = "select subjects.sname,teacher.tid,teacher.tname,arraged_classes.cid,count(*) snum from" . " subjects,teacher,arraged_classes where arraged_classes.tid = teacher.tid and" . " arraged_classes.sid=subjects.sid and arraged_classes.gid='$gid' and arraged_classes.uid='$uid' group by tname,arraged_classes.cid";
        $sql_table = "select subjects.sid,sname,classes.cid,cname,teacher.tid,tname,perday,perweek,did from classes,subjects,grades,arraged_classes,teacher where classes.cid = arraged_classes.cid and arraged_classes.gid=grades.gid and arraged_classes.tid=teacher.tid and arraged_classes.sid=subjects.sid and grades.gid='$gid' and grades.uid='$uid'";
        $sql_mtime = "select perweek,mtime from grades where gid='$gid' and uid='$uid'";
        $mId = 0;
        $rows = array();
        $subDetial = new subjectDetail();
        $subDetial->gid = $gid;
        if (mysqli_connect_errno()) {
            $mysqli->close();
        } else {
            $subs = Array();
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
                    $rows[$c++] = $sub;
                }
                $res['subs'] = $rows;
            } else {
                
            }
            if ($result = $mysqli->query($sql_mtime)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $perweek = $row['perweek'];
                    $mtime = $row['mtime'];
                    $r = explode('-', $mtime);
                    $mId = $r[0] + ($r[1]-1)*$perweek;
                }
            } else {
            
            }
            if ($result = $mysqli->query($sql_teach)) {
                $tmp = Array();
                while ($row = mysqli_fetch_assoc($result)) {
                    $detail = new DetailInfo();
                    $detail->sname = $row['sname'];
                    $detail->snum = $row['snum'];
                    $detail->isLP = "";
                    
                    $tchs = new tchsInfo();
                    $tchs->tid = $row['tid'];
                    $tchs->tname = $row['tname'];
                    $tchs->appendTclass($row['cid']);
                    $detail->tchs[$row['tid']] = $tchs;
                    
                    if (isset($tmp[$row['sname']])) {
                        if (isset($tmp[$row['sname']]->tchs[$row['tid']])) {
                            $tmp[$row['sname']]->tchs[$row['tid']]->appendTclass($row['cid']);
                        } else {
                            $tmp[$row['sname']]->tchs[$row['tid']] = $tchs;
                        }
                    } else {
                        $sub = new Subject();
                        $sub->setName($row['sname']);
                        $subs[count($subs)] = $sub;
                        $tmp[$row['sname']] = $detail;
                    }
                }
                if($subs!=null)
                    $res['subs'] = $subs;
                $subjects = Array();
                $s_c = 0;
                foreach($tmp as $s){
                    $subjects[$s_c] = new DetailInfo();
                    $subjects[$s_c]->sname = $s->sname;
                    $subjects[$s_c]->snum = $s->snum;
                    $subjects[$s_c]->isLP = "";
                    $subjects[$s_c]->tchs = Array();
                    $t_c = 0;
                    foreach($s->tchs as $t){
                        $subjects[$s_c]->tchs[$t_c] = new tchsInfo();
                        $subjects[$s_c]->tchs[$t_c]->tid = $t->tid;
                        $subjects[$s_c]->tchs[$t_c]->tname = $t->tname;
                        $subjects[$s_c]->tchs[$t_c]->tclass = $t->tclass;
                        $t_c++;
                    }
                    $s_c++;
                }
                $subDetial->subs = $subjects;
                $res['subDetail'] = $subDetial;
            } else {
                file_put_contents('log.txt',"select Object error", FILE_APPEND);
            }
            if ($result = $mysqli->query($sql_table)) {
                $cids = Array();
                $tables = Array();
                $c_scount = Array();
                while ($row = mysqli_fetch_assoc($result)) {
                    if(!isset($cids[$row['cid']])){
                        $cids[$row['cid']] = $row['cid'];
                    }
                    if(isset($tables[$row['cid']])){
                        $aSub = new ArrangeSubs();
                        $aSub->did = $row['did'];
                        $aSub->sid = $row['sid'];
                        $aSub->tid = $row['tid'];
                        $aSub->sname = $row['sname'];
                        $aSub->tname = $row['tname'];
                        $tables[$row['cid']]->subjects[$c_scount[$row['cid']]] = $aSub;
                        $c_scount[$row['cid']]++;
                    }else{
                        $c_scount[$row['cid']] = 0;
                        $table = new tableInfo();
                        $table->cid = $row['cid'];
                        $table->cname = $row['cname'];
                        $table->perday = $row['perday'];
                        $table->perweek = $row['perweek'];
                        $aSub = new ArrangeSubs();
                        $aSub->did = $row['did'];
                        $aSub->sid = $row['sid'];
                        $aSub->tid = $row['tid'];
                        $aSub->sname = $row['sname'];
                        $aSub->tname = $row['tname'];
                        $table->subjects[$c_scount[$row['cid']]] = $aSub;
                        $tables[$row['cid']] = $table;
                        $c_scount[$row['cid']]++;
                    }
                }
                $cids = array_values($cids);
                foreach($cids as $cid){
                    $aSub = new ArrangeSubs();
                    $aSub->sname = '班会';
                    $aSub->did = $mId;
                    $tables[$cid]->subjects[$c_scount[$cid]] = $aSub;
                }
                $arrange_tables = Array();
                $arrange_tables['cids'] = $cids;
                $arrange_tables['tables'] = $tables;
                $arrange_tables['gid'] = $gid;
                $res['tables'] = $arrange_tables;
            } else {
            
            }
        }
        
        $mysqli->close();
    }
}
echo json_encode($res);
?>