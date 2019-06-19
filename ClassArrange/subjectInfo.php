<?php
class Result{
    public $total;
    public $rows;
    public function setTotal($t){
        $this->total = $t;
    }
    public function setRows($rs){
        $this->rows = $rs;
    }
}
class Subject{
    public $rid;
    public $id;
    public $name;
    public $prim;
    public function setRId($rid){
        $this->rid = $rid;
    }
    public function setId($id){
        $this->id = $id;
    }
    public function setName($name){
        $this->name = $name;
    }
    public function setPrim($prim){
        $this->prim = $prim;
    }
}
class simpleSubject{
    public $id;
    public $name;
    public function setId($id) {
        $this->id = $id;
    }
    public function setName($name){
        $this->name = $name;
    }
}
class subjectDetail{
    public $gid;
    public $subs = Array();
    public function appendSub($sub){
        $this->subs[count($this->subs)] = $sub;
    }
    
}
class DetailInfo{
    public $sname;
    public $snum;
    public $isLP;
    public $tchs = Array();
}
class tchsInfo{
    public $tid;
    public $tclass = "";
    public $tname;
    public function appendTclass($tid){
        if(strlen($this->tclass) == 0)
            $this->tclass = $this->tclass . "" . $tid;
        else
            $this->tclass = $this->tclass . "&" . $tid;
    }
}
class tableInfo{
    public $cid;
    public $cname;
    public $perday;
    public $perweek;
    public $subjects = Array();
}
?>