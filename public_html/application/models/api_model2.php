<?php
class Api_model2 extends CI_Model {

    public function __construct(){
        parent::__construct();        
    }

    // insertDB
    //function insertDB($order,$products,$options,$payments,$cards,$coupons) {
	function insertDB($order, $products, $options, $payments, $cards, $coupons) {

		global $db;

        // sql쿼리문을 만들자!
		// 기본쿼리문 배열
		$sp_arr = array(
			'order'     => "[VENDINGM].[dbo].[SP_ITMS_ORDER];1 ",
			'products'  => "[VENDINGM].[dbo].[SP_ITMS_ORDERPRODUCT];1 ",
			'options'   => "[VENDINGM].[dbo].[SP_ITMS_ORDERPRODUCTOPTION];1 ",
			'payments'  => "[VENDINGM].[dbo].[SP_ITMS_PAYMENTS];1 ",
			'cards'     => "[VENDINGM].[dbo].[SP_ITMS_CARDPAYMENTSDETAIL];1 ",
			'coupons'   => "[VENDINGM].[dbo].[SP_ITMS_COUPONPAYMENTSDETAIL];1 ",
		);
		// '?'를 배열의 키갯수 만큼 붙이자!!
		$questionmark = '';
		for ($i = 0;$i < count(array_keys($order))-1;$i++) { 
			$questionmark .= '?, '; 
		}
		$questionmark .= '? '; 
        $sp_order = $sp_arr['order'].$questionmark;

		$questionmark = '';
		for ($i = 0;$i < count(array_keys($products['0']))-1;$i++) { 
			$questionmark .= '?, '; 
		}
		$questionmark .= '? '; 
        $sp_products = $sp_arr['products'].$questionmark;
   		
		$questionmark = '';
		for ($i = 0;$i < count(array_keys($options['0']['0']))-1;$i++) { 
			$questionmark .= '?, ';
		}
		$questionmark .= '? ';   // 34
        $sp_options = $sp_arr['options'].$questionmark;

		$questionmark = '';
		for ($i = 0;$i < count(array_keys($payments['0']))-1;$i++) { 
			$questionmark .= '?, '; 
		}
		$questionmark .= '? '; 
        $sp_payments = $sp_arr['payments'].$questionmark;

        if(!empty($cards)) {
			$questionmark = '';
		    for ($i = 0;$i < count(array_keys($cards))-1;$i++) { 
			    $questionmark .= '?, '; 
		    }
		    $questionmark .= '? '; 
            $sp_cards = $sp_arr['cards'].$questionmark;
		} else {
            $sp_cards = '';
		}

        //echo $sp_cards;
		//print_r($cards);
		//exit;

        if(!empty($coupons)) {
    		$questionmark = '';
		    for ($i = 0;$i < count(array_keys($coupons))-1;$i++) { 
			    $questionmark .= '?, '; 
		    }
		    $questionmark .= '? '; 
            $sp_coupons = $sp_arr['coupons'].$questionmark;
		} else {
            $sp_coupons = '';
		}
		
		// 자~~ 이제 들어갑니다. 시작~~
		// transaction start
		$this->db->trans_start();
        // insert DataBase
        $this->db->query($sp_order,$order);
		for ($i = 0;$i < count($products);$i++) { 
			$this->db->query($sp_products,$products[$i]); 
		}
		if (is_array($options)) {
			for ($i = 0;$i < count($options);$i++) {
	            for ($j = 0;$j < count($options[$i]);$j++) {
			        $this->db->query($sp_options,$options[$i][$j]);
			    }
		    }
		}
		for ($i = 0;$i < count($payments);$i++) { 
			$this->db->query($sp_payments,$payments[$i]); 
		}
        if(is_array($cards)) $this->db->query($sp_cards,$cards);
        if(is_array($coupons)) $this->db->query($sp_coupons,$coupons);
        // transaction end
		$this->db->trans_complete();

		return $this->db->trans_status()? "0000" : -1;
    }

}

/* End of file api_model2.php */
/* Location: ./application/models/api_model2.php */