<?php

class Teacher
{

    public $rid;

    public $id;

    public $tname;

    public $subs;

    public $cnstr;

    public $table;

    /**
     *
     * @param
     *            !CodeTemplates.settercomment.paramtagcontent!
     */
    public function setRid($rid)
    {
        $this->rid = $rid;
    }

    /**
     *
     * @param
     *            !CodeTemplates.settercomment.paramtagcontent!
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param
     *            !CodeTemplates.settercomment.paramtagcontent!
     */
    public function setTname($tname)
    {
        $this->tname = $tname;
    }

    /**
     *
     * @param
     *            !CodeTemplates.settercomment.paramtagcontent!
     */
    public function setSubs($subs)
    {
        $this->subs = $subs;
    }

    /**
     *
     * @param
     *            !CodeTemplates.settercomment.paramtagcontent!
     */
    public function setCnstr($cnstr)
    {
        $this->cnstr = $cnstr;
    }

    /**
     *
     * @param
     *            !CodeTemplates.settercomment.paramtagcontent!
     */
    public function setTable($table)
    {
        $this->table = $table;
    }
    public function appendSub($subs){
        $this->subs =$this->subs.";".$subs;
    }
}
?>