<?php
class Api_model extends CI_Model {

    public function __construct(){
        parent::__construct();        
    }

    //전체 마일리지 조회
    function getTotalMileage($saletotal_member) {
        global $db;
        $sp = "{$db['default']['database']}.dbo.sp_web_totalmileage ? ";
        $params = array('saletotal_member' => $saletotal_member);  
        $result = $this->db->query($sp,$params);    
        return $result->result_array();
    }

    //마일리지 insert 
    function insertSaleTotalMileage($UnivCode, $saletotal_date, $saletotal_store, $saletotal_posid, $saletotal_billnumber, $saletype,  $cashcredit, $saletotal_cardvan, $saletotal_joinno, $pointtype, $saletotal_member, $saletotal_profit, $amount, $realdatetime){
        global $db;
        $sp = "VENDINGM.dbo.sp_SaleTotalMileageInsert ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ";
        $params = array(
            'UnivCode'              => $UnivCode,
            'saletotal_date'        => $saletotal_date,
            'saletotal_store'       => $saletotal_store,
            'saletotal_posid'       => $saletotal_posid,
            'saletotal_billnumber'  => $saletotal_billnumber,
            'saletype'              => $saletype,
            'cashcredit'            => $cashcredit,
            'saletotal_cardvan'     => $saletotal_cardvan,
            'saletotal_joinno'      => $saletotal_joinno,
            'pointtype'             => $pointtype,
            'saletotal_member'      => $saletotal_member,
            'saletotal_profit'      => $saletotal_profit,
            'amount'                => $amount,
            'realdatetime'          => $realdatetime
        );  
        $this->db->query($sp,$params); 
        return  $this->db->affected_rows();    
    }

/* End of file api_model.php */
/* Location: ./application/models/api_model.php */