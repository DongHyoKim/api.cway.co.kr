<?php
class Pos_api_model extends CI_Model {

  public function __construct(){
		parent::__construct();
	}
  
 //전체 마일리지 조회
 function getTotalMileage($saletotal_member){
    global $db;
    $sp = "{$db['default']['database']}.dbo.sp_TicketMachineMileage ? ";
    $params = array('saletotal_member' => $saletotal_member);  
    $result = $this->db->query($sp,$params);    
    return $result->result_array();
 }

 //전체 조합원여부 확인 : 조합원번호,이름,포인트잔액
 function getCopartnerMember($params){
    global $db;
    $sp = "[CPT".$params['UNIVCODE'].$params['SUBUNIVCODE']."].dbo.sp_service_pos_cptsel ?, ?, ? ";
    $result = $this->db->query($sp,$params);    
    return $result->result_array();
 }  
  
  //마일리지 insert 
  function insertSaleTotalMileage($params){

    global $db;

    $sp = "[CPT".$params['UNIVCODE'].$params['SUBUNIVCODE']."].[dbo].[sp_service_pos_cptmilins] ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ";

    $result = $this->db->query($sp,array($params['BARCODENO'],
			                       $params['USEDATE'],
     			                   $params['DEPTCODE'],
			                       $params['POSNO'],
			                       $params['BILLNUMBER'],
			                       $params['USEMILEAGE'],
			                       $params['AMOUNT'],
			                       $params['AMOUNTSAVE'],
			                       $params['REMARK'],
			                       $params['UNIVCODE'])); 
    if ($result) {
//		return  $this->db->affected_rows();    
        return 1;
	} else {
		retrun -1;
	}
  }

  function insertMileage($UnivCode, $saletotal_date, $saletotal_store, $saletotal_posid, $saletotal_billnumber, $saletotal_member,  $mileage_type , $amount) {

	global $db;
    $this->db->trans_start();
    $sp = "VENDINGM.dbo.sp_MileageInsert ?, ?, ?, ?, ?, ?, ?, ? ";
    $params = array(
                'UnivCode'              => $UnivCode,
                'saletotal_date'        => $saletotal_date,
                'saletotal_store'       => $saletotal_store,
                'saletotal_posid'       => $saletotal_posid,
                'saletotal_billnumber'  => $saletotal_billnumber,
                'saletotal_member'      => $saletotal_member,
	            'mileage_type'          => $mileage_type,
                'amount'                => $amount,
              );  
    $this->db->query($sp,$params); 
    $this->db->trans_complete();	

    return  $this->db->trans_status()==TRUE?1:-1;    
  }

}
?>
