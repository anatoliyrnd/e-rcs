<?php

namespace mainSRC\calls;

use mainSRC\main;

class closeCall extends main{
    protected $call_id;
    protected $staff_id;
    protected $alowed_close;

    public function __construct()
    {
        parent::__construct();

    }
    public function closureCall(){
        if ($this->checkCallClosed()) {
            return false;}
 if (!$user_permission =$this->getUserPermission()[2]){

 }

    }

    /**
     * @param int $call_id
     */
    public function setCallId(int $call_id)
    {
        $this->call_id = $call_id;
    }

    private function checkStaff($staff_id){
        $staff_id=(int)$staff_id;
        $staff=(int)$this->DB->single("SELECT call_staff FROM lift_calls WHERE call_id=:id",array("id"=>$this->call_id));
        return $staff===$staff_id;
    }
    public function checkCallClosed(){
        $call_id=(int)$this->call_id??0;
        return (boolean)$this->DB->single("SELECT call_status FROM lift_calls WHERE call_id=:id",array("id"=>$this->call_id));
    }


}