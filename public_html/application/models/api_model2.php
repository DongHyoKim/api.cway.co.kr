<?php
class Api_model2 extends CI_Model {

    public function __construct(){
        parent::__construct();        
    }

    // insertDB
    //function insertDB($order,$products,$options,$payments,$cards,$coupons) {
	function insertDB($order, $products) {

		global $db;

        
		$sp_arr = array(
			'order'     => "[VENDINGM].[dbo].[SP_ITMS_ORDER];1 ",
			'products'  => "[VENDINGM].[dbo].[SP_ITMS_ORDERPRODUCT];1 ",
			'options'   => "VENDINGM.dbo.SP_ITMS_ORDERPRODUCTOPTION;01 ",
			'payments'  => "VENDINGM.dbo.SP_ITMS_PAYMENTS;01 ",
			'cards'     => "VENDINGM.dbo.SP_ITMS_CARDPAYMENTSDETAIL;01 ",
			'coupons'   => "VENDINGM.dbo.SP_ITMS_COUPONPAYMENTSDETAIL;01 ",
		);
		$questionmark = '';
		for ($i = 0;$i < count(array_keys($order))-1;$i++) { 
			$questionmark .= "?, "; 
		}
		$questionmark .= "? "; 
        $sp_order = $sp_arr['order'].$questionmark;

		$questionmark = '';
		for ($i = 0;$i < count(array_keys($products['0']))-1;$i++) { 
			$questionmark .= "?, "; 
		}
		$questionmark .= "? "; 
        $sp_products = $sp_arr['products'].$questionmark;
        //echo $sp_products;
		//print_r($products);
		//echo count(array_keys($products['0']));
		//exit;
        /*
		for ($i = 0;$i <= count($options)/COUNT_OF_OPTIONS;$i++) { $questionmark .= "? "; } $questionmark .= '"';  // 33
        $sp_options = $sp_arr['options'].$questionmark;
        for ($i = 0;$i <= count($payments)/COUNT_OF_PAYMENTS;$i++) { $questionmark .= "? "; } $questionmark .= '"';  // 33
        $sp_payments = $sp_arr['payments'].$questionmark;
        for ($i = 0;$i <= count($card)/COUNT_OF_CARD;$i++) { $questionmark .= "? "; } $questionmark .= '"';  // 33
        $sp_card = $sp_arr['card'].$questionmark;
        for ($i = 0;$i <= count($coupon)/COUNT_OF_COUPON;$i++) { $questionmark .= "? "; } $questionmark .= '"';  // 33
        $sp_coupon = $sp_arr['coupon'].$questionmark;
        */
		// transaction start
		$this->db->trans_start();
        // insert DataBase
        $this->db->query($sp_order,$order);
		for ($i = 0;$i < count($products);$i++) { 
			$this->db->query($sp_products,$products[$i]); 
		}
		/*
        for ($i = 0;$i <count($options)/COUNT_OF_OPTIONS;$i++) { $this->db->query($sp_options,$options[$i]); }
        for ($i = 0;$i <count($payments)/COUNT_OF_PAYMENTS;$i++) { $this->db->query($sp_payments,$payments[$i]); }
        for ($i = 0;$i <count($card)/COUNT_OF_CARD;$i++) { $this->db->query($sp_card,$card[$i]); }
        for ($i = 0;$i <count($coupon)/COUNT_OF_COUPON;$i++) { $this->db->query($sp_coupon,$coupon[$i]); }
        */
        // transaction end
		$this->db->trans_complete();

		return $this->db->trans_status()? "0000" : -1;
		//return;
    }

}

/* End of file api_model2.php */
/* Location: ./application/models/api_model2.php */