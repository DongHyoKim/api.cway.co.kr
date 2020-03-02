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

    // insertDB
    function insertDB($order,$products,$options,$payments,$card,$coupon) {

		global $db;

		$sp_arr = array(
			'order'     => "VENDINGM.dbo.SP_ITMS_ORDER;01 ",
			'products'  => "VENDINGM.dbo.SP_ITMS_ORDERPRODUCT;01 ",
			'options'   => "VENDINGM.dbo.SP_ITMS_ORDERPRODUCTOPTION;01 ",
			'payments'  => "VENDINGM.dbo.SP_ITMS_PAYMENTS;01 ",
			'card'      => "VENDINGM.dbo.SP_ITMS_CARDPAYMENTSDETAIL;01 ",
			'coupon'    => "VENDINGM.dbo.SP_ITMS_COUPONPAYMENTSDETAIL;01 ",
		);

		for ($i = 0;$i <= count($order)/COUNT_OF_ORDER;$i++) { $questionmark .= "? "; } $questionmark .= '"'; // 38
        $sp_order = $sp_arr['order'].$questionmark;
		for ($i = 0;$i <= count($products)/COUNT_OF_PRODUCTS;$i++) { $questionmark .= "? "; } $questionmark .= '"'; // 42
        $sp_products = $sp_arr['products'].$questionmark;
        for ($i = 0;$i <= count($options)/COUNT_OF_OPTIONS;$i++) { $questionmark .= "? "; } $questionmark .= '"';  // 33
        $sp_options = $sp_arr['options'].$questionmark;
        for ($i = 0;$i <= count($payments)/COUNT_OF_PAYMENTS;$i++) { $questionmark .= "? "; } $questionmark .= '"';  // 33
        $sp_payments = $sp_arr['payments'].$questionmark;
        for ($i = 0;$i <= count($card)/COUNT_OF_CARD;$i++) { $questionmark .= "? "; } $questionmark .= '"';  // 33
        $sp_card = $sp_arr['card'].$questionmark;
        for ($i = 0;$i <= count($coupon)/COUNT_OF_COUPON;$i++) { $questionmark .= "? "; } $questionmark .= '"';  // 33
        $sp_coupon = $sp_arr['coupon'].$questionmark;

		// transaction start
		$this->db->trans_start();
        // insert DataBase
        for ($i = 0;$i <count($products)/COUNT_OF_ORDER;$i++) { $this->db->query($sp_order,$order[$i]); }
        for ($i = 0;$i <count($products)/COUNT_OF_PRODUCTS;$i++) { $this->db->query($sp_products,$products[$i]); }
        for ($i = 0;$i <count($products)/COUNT_OF_OPTIONS;$i++) { $this->db->query($sp_options,$options[$i]); }
        for ($i = 0;$i <count($payments)/COUNT_OF_PAYMENTS;$i++) { $this->db->query($sp_payments,$payments[$i]); }
        for ($i = 0;$i <count($card)/COUNT_OF_CARD;$i++) { $this->db->query($sp_card,$card[$i]); }
        for ($i = 0;$i <count($coupon)/COUNT_OF_COUPON;$i++) { $this->db->query($sp_coupon,$coupon[$i]); }
        // transaction end
		$this->db->trans_complete();

		return $this->db->trans_status()? "0000" : -1;
    }

}

/* End of file api_model.php */
/* Location: ./application/models/api_model.php */