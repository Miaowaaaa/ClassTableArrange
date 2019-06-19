<?php
include_once 'gradeInfo.php';
include_once 'classInfo.php';
include_once 'arrangeTables.php';
$res = Array();
session_start();
function fake_array_rand($array,$num,$dnum,$snum,$cnstr=null){
    $arr = Array();
    $rid = Array();
    $want = false;
    if($cnstr!=null){
        $pattern = "/\d+/";
        preg_match_all($pattern,$cnstr,$arr);
        $rid[count($rid)] = ($arr[0][1] - 1) *$dnum+$arr[0][0];
        if($arr[0][2] == 1){
            $want = true;
        }
    }
    
    $resArray = Array();
    $tmp = Array();           //可以安排的时间
    $flag = Array();
    $res_c = 0;
    $array = array_values($array);
    for($i = 0;$i<$dnum;$i++){
        $tmp[$i] = Array();
        for($j = 1;$j<=$snum;$j++){
            $tmp[$i][$j-1] = $i%$dnum+1+$dnum*($j-1);
        }
        $tmp[$i] = array_intersect($tmp[$i],$array);
    }
    file_put_contents('log.txt', "sdfdsf" . json_encode($array) . PHP_EOL . json_encode($tmp),FILE_APPEND);
    for($i = 0;$i<$dnum;$i++){
        if(count($tmp[$i])<1){
            continue;
        }
        $flag[$i] = $i;        //可以安排的时间标志
    }
    $had = Array();            //已经安排的时间标志
    $wait =Array();            //候补，
    for($i = 0;$i<$dnum;$i++){
        $wait[$i] = Array();
    }
    $had_c = 0;
    $i = 0;
    for(;;){
        if($res_c == $num) break;
        if($i == 0 && $cnstr!=null){
            if($want && count(array_intersect($tmp[$arr[0][0]-1], $rid))>0){
                $resArray[$res_c++] = $rid[0];    
                $had[$had_c++] = $arr[0][0]-1;
            }else if(!$want){
                $wait[$arr[0][0]-1] = array_merge($wait[$arr[0][0]-1],$rid);//加入候补
                $tmp[$arr[0][0]-1] = array_diff($tmp[$arr[0][0]-1], $rid);//删除
            }
            $i++;
            continue;    
        }
        $t = array_diff($flag, $had);   //获取没有被安排的天的标志
       
        $pos = array_rand($t,1);
        $had[$had_c++] = $t[$pos];
        if(count($tmp[$flag[$pos]])<1){
            if(count($wait[$flag[$pos]])>0){
                $tmp[$flag[$pos]] = $wait[$flag[$pos]];
            }else{
                continue;
            }
        }
        $tmp[$flag[$pos]] = array_diff($tmp[$flag[$pos]], $resArray);
        $pos2 = array_rand($tmp[$flag[$pos]],1);
        $resArray[$res_c++] = $tmp[$flag[$pos]][$pos2];
        if(count($had) == count($flag)){
            $had = Array();
        }
        
        
        $i++;
        if($i>500) break;
    }
    return $resArray;
}

if(isset($_SESSION['uid']) && null != $_SESSION['uid']){
    $uid = $_SESSION['uid'];
    if(null != $_POST['params'] && isset($_POST['params'])){

        //file_put_contents('log.txt',$_POST['params'],FILE_APPEND); //打印接收到的信息
        $data = json_decode($_POST['params'],true);
        $gid = $data['gid'];                         //年级id
        $subs = $data['subs'];                       //课程信息和教师信息
        $grade = new Grade();                        //存储当前年级信息
        $classinfo = Array();                        //存储班级相关信息
        $subject = Array();                          //课程name id 映射 
        $teacher = Array();                          //教师id name 映射
        $classname = Array();                        //班级id name 映射
        $class_flags = Array();                      //班级课表排课标志
        $class_cnstr = Array();                      //班级约束       
        $teacher_cnstr = Array();                    //教师约束
        $subject_cnstr = Array();                    //课程约束
        $tidIndex = Array();                         //教师id字典
        $cidIndex = Array();                         //班级id字典
        $_TC = 0;                                    //一周总课程数
        $mId = 0;                                    //班会时间点
        
        $teacher_class = Array();                     //老师-课时记录
        ####################################################
        #
        #               获取数据库中的数据                                              
        #
        #####################################################
        $mysqli = new mysqli("localhost","root","scrovormysql",'easyArranger');
        $sql_sel_grade = "select gname,gcn,perday,perweek,mtime from grades where uid='$uid' and gid='$gid'"; //获取年级信息
        $sql_sel_classes =  "select cid,cname from classes where uid='$uid' and gid='$gid'";                  //获取班级信息
        $sql_sel_subjects = "select sid,sname from subjects where uid='$uid'";
        $sql_sel_teacher = "select tid,constr,tname from teacher where uid='$uid'";
        if(mysqli_connect_errno()){
            $mysqli->close();
            $res['status'] = 'error';
        }else{
            if($result = $mysqli->query($sql_sel_grade)){
                while($row = mysqli_fetch_assoc($result)){
                    $grade->setMtime($row['mtime']);
                    $grade->setName($row['gname']);
                    $grade->setCnum($row['gcn']);
                    $grade->setDnum($row['perweek']);
                    $grade->setSnum($row['perday']);
                }
                $grade->setId($gid);
                $_TC = $grade->dnum *$grade->snum;
                $ms = explode("-", $grade->mtime);
                $mId = ($ms[1]-1)*$grade->dnum + $ms[0];
            }else{
                file_put_contents('log.txt',"query grade error in arrange.php\n",FILE_APPEND);
                $res['status'] = 'error';
            }
            if($result = $mysqli->query($sql_sel_classes)){
                while($row = mysqli_fetch_assoc($result)){
//                     $c = new ClassInfo();
//                     $c->setCname($row['cname']);
//                     $c->setId($row['cid']);
//                     $classinfo[$row['cid']] = $c;
//                     $class_flags[$row['cid']] = 1;                         //初始化各班排课标志
                    $classname[$row['cid']] = $row['cname'];
                }
            }else{
                file_put_contents('log.txt',"query classes error in arrange.php\n",FILE_APPEND);
                $res['status'] = 'error';
            }
            if($result = $mysqli->query($sql_sel_subjects)){
                while($row = mysqli_fetch_assoc($result)){
                    $subject[$row['sname']] = $row['sid'];
                    if($row['sname'] == '体育'){
                        $subject_cnstr[$row['sid']] = "(0,1,0)";             //体育课每天第一节都不可以
                    }
                }
            }else{
                file_put_contents('log.txt',"query classes error in arrange.php\n",FILE_APPEND);
                $res['status'] = 'error';
            }
            if($result = $mysqli->query($sql_sel_teacher)){
                while($row = mysqli_fetch_assoc($result)){
                    if(strlen($row['constr'])>0){
                        $teacher_cnstr[$row['tid']] = $row['constr'];
                    }
                    $teacher[$row['tid']] = $row['tname'];
                }
            }else{
                file_put_contents('log.txt',"query classes error in arrange.php\n",FILE_APPEND);
                $res['status'] = 'error';
            }
        }

           ########################################################################
           #
           #                初始化teacher-classes表,
           #                   [tid][cid]{[sname](number),[snum](tnumber)}
           #
           ########################################################################
           $tid_c = 0;
           $cid_c = 0;
           for($i = 0;$i<count($subs);$i++){
               $tchs = $subs[$i]['tchs'];                             //每个课程的老师列表
               $t_snum = $subs[$i]['snum'];                           //当前课程课时数
               $t_sname = $subs[$i]['sname'];                         //课程名
               for($j = 0;$j<count($tchs);$j++){
                   $t_tid = $tchs[$j]['tid'];
                   $tclasses = $tchs[$j]['tclass'];                   //该教师教授的班级
                   $tclass = explode("&", $tclasses);                 //解析班级
                   if(!isset($tidIndex[$t_tid])){
                       $tidIndex[$t_tid] = $tid_c;                    //记录tid对应的位置
                       $teacher_class[$tid_c] = Array();              //初始该教师对应的班级列
                       for($k = 0;$k<$grade->cnum;$k++){
                           $teacher_class[$tid_c][$k] = Array();       //为该教师开辟班级数组
                           $teacher_class[$tid_c][$k]['dId'] = Array(); //初始排课为空
                           $teacher_class[$tid_c][$k]['snum'] = 0;     //初始为该班级上课为0
                           $teacher_class[$tid_c][$k]['sname'] = Array();     //初始为该班级上课为0
                       }
                       $tid_c++;
                   }
                   $now_j = $tidIndex[$t_tid];
                   
                   for($k = 0;$k<count($tclass);$k++){
                       $t_cid = $tclass[$k];                          //临时存储该班级id
                       if(!isset($cidIndex[$t_cid])){
                           $cidIndex[$t_cid] = $cid_c;                //构造班级id字典
                           $cid_c++;
                       }
                       $teacher_class[$now_j][$cidIndex[$t_cid]]['sname'][$t_sname] = $t_snum; //该课程上几节;
                       $teacher_class[$now_j][$cidIndex[$t_cid]]['snum'] += $t_snum; //则加课数;
                   }
               }
           }
           ####################################################################
           #
           #                为teacher-class表安排数据
           #
           ####################################################################
           $t_cidIndex = array_flip($cidIndex);
           $t_tidIndex = array_flip($tidIndex);                               //两个id字典键值翻转
           ########初始化标志表##########
           $t_flags = Array();                                            //每节课由唯一id标记
           $t_flags_no1 = Array();                                        //不含每周第一节课的id
           $cc = 1;
           for($k = 0;$k<$grade->dnum*$grade->snum-1;$k++){
                if($cc>$grade->dnum && $cc !=$mId){
                    $t_flags_no1[$k] = $cc;
                }
                if($cc != $mId){
                    $t_flags[$k] = $cc;
                }else{
                    $cc++;
                    $t_flags[$k] = $cc;    //初始化标志表
                }
                $cc++;
           }
           for($i =0;$i<count($teacher_class);$i++){                          //教师循环

               ########初始随机老师的排课########
               for($j = 0;$j<$grade->cnum;$j++){
                   $tmp_union = Array();
                   for($m = $i-1;$m>=0;$m--){
                       $tmp_union = array_merge($teacher_class[$m][$j]['dId'],$tmp_union); //不同老师之间的并集
                   }
                   for($m = $j-1;$m>=0;$m--){
                       $tmp_union = array_unique(array_merge($teacher_class[$i][$m]['dId'],$tmp_union)); //不同班级之间的并集
                   }
                   $rand_num  = $teacher_class[$i][$j]['snum'];
                   if($rand_num > 0){
                       file_put_contents('log.txt', json_encode($teacher_class[$i][$j]['sname']) . PHP_EOL,FILE_APPEND);
                       if(!isset($teacher_class[$i][$j]['sname']['体育']))
                           $diff = array_diff($t_flags,$tmp_union);           //计算差集
                       else{
                           $diff = array_diff($t_flags_no1,$tmp_union);       //计算差集
                           file_put_contents('log.txt', "tiyu" . json_encode($diff) . PHP_EOL,FILE_APPEND);
                       }
                       if(isset($teacher_cnstr[$t_tidIndex[$i]]) && $teacher_cnstr[$t_tidIndex[$i]] !=null && $teacher_cnstr[$t_tidIndex[$i]]!="")
                           $tmp = fake_array_rand($diff, $rand_num,$grade->dnum,$grade->snum,$teacher_cnstr[$t_tidIndex[$i]]);//从差集中随机选出几个结果
                       else
                           $tmp = fake_array_rand($diff, $rand_num,$grade->dnum,$grade->snum);//从差集中随机选出几个结果
                       $teacher_class[$i][$j]['dId'] = $tmp;
                                           
                   }
               }
           }
           
           #####################排课结束,构造返回数据#################################
           $res_tables = Array();
           $res_cids = Array();
           $class_count = 0;
           for($i =0;$i<$cid_c;$i++){
               $class = new ArrangeTables();
               $class->cid = $t_cidIndex[$i];                                //cid
               $class->cname = $classname[$class->cid];                      //cname
               $class->perday = $grade->snum;                                //perday
               $class->perweek = $grade->dnum;                               //perweek
               $res_cids[$class_count++] = $class->cid;                      //cid_keys
               $subs_count = 0;
               for($j = 0;$j<count($teacher_class);$j++){
                   
                   $sub_keys = array_keys($teacher_class[$j][$i]['sname']);  //有哪些课程
                   $t_union = Array();
                   $ttmp = $teacher_class[$j][$i]['dId'];
                   for($l = 0;$l<count($sub_keys);$l++){
                      $t_sname = $sub_keys[$l];                                  //课程名
                      $t_sid  = $subject[$t_sname];                              //课程sid
                      $t_cnum = $teacher_class[$j][$i]['sname'][$t_sname];       //课程节数
                      $t_diff = array_diff($ttmp, $t_union);                     //差集
                      $t_union = array_merge($t_union,$t_diff);                  //保存并集
                      $s_did = array_rand($t_diff,$t_cnum);                      //随机选出的位置索引
                      if(is_array($s_did)){
                          foreach($s_did as $a){
                              $sub = new ArrangeSubs();
                              $sub->tid = $t_tidIndex[$j];                              //tid
                              $sub->sname = $t_sname;                                   //sname
                              $sub->sid = $t_sid;                                       //sid
                              $sub->did = $t_diff[$a];                                  //did
                              $sub->tname = $teacher[$sub->tid];                        //tname
                              $class->subjects[$subs_count++] = $sub;                   //subjects
                          }
                      }else{
                          $sub = new ArrangeSubs();
                          $sub->tid = $t_tidIndex[$j];                              //tid
                          $sub->sname = $t_sname;                                   //sname
                          $sub->sid = $t_sid;                                       //sid
                          $sub->did = $t_diff[$s_did];                              //did
                          $sub->tname = $teacher[$sub->tid];                        //tname
                          $class->subjects[$subs_count++] = $sub;                   //subjects
                      }
                      $ttmp = $t_diff;
                  }
                  
               }
               $sub = new ArrangeSubs();
               $sub->sname = '班会';
               $sub->did = $mId;
               $class->subjects[$subs_count++] = $sub;                              //班会
               $res_tables[$class->cid] = $class;
           }
        $res['status'] = 'ok';
        $res['tables'] = $res_tables;
        $res['cids'] = $res_cids;
        $res['gid'] = $gid;
    }else{
        $res['status'] = 'error';
    }
    
}else{
    $res['status'] = 'offline';
}
echo json_encode($res);
?>
