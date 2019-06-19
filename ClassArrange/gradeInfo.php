<?php

class Grade
{

    public $rid;

    public $id;

    public $name;

    public $subs;

    public $cnum;

    public $dnum;

    public $snum;
 // 节数
    public $mtime;

    public $arrange;

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
    public function setName($name)
    {
        $this->name = $name;
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
    public function setCnum($cnum)
    {
        $this->cnum = $cnum;
    }

    /**
     *
     * @param
     *            !CodeTemplates.settercomment.paramtagcontent!
     */
    public function setDnum($dnum)
    {
        $this->dnum = $dnum;
    }

    /**
     *
     * @param
     *            !CodeTemplates.settercomment.paramtagcontent!
     */
    public function setSnum($snum)
    {
        $this->snum = $snum;
    }

    /**
     *
     * @param
     *            !CodeTemplates.settercomment.paramtagcontent!
     */
    public function setMtime($mtime)
    {
        $this->mtime = $mtime;
    }

    /**
     *
     * @param
     *            !CodeTemplates.settercomment.paramtagcontent!
     */
    public function setArrange($arrange)
    {
        $this->arrange = $arrange;
    }
    public function appendSub($sub){
        $this->subs = $this->subs.";".$sub;
    }
    public function getSubs(){
        return $this->subs;
    }
}
?>