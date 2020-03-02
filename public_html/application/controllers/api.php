<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CT_Controller {

    private $json_array = array();

    public function __construct(){
		parent::__construct();
		$this->load->model('Api_model','API');
    }	
    
    public function index(){

    }

    //주문정보 receive api
	public function receivedata() {
        
        
		$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'        => BASEPATH . '../../logs/receivedata/' . date('Ymd') . '_data.log',
            'bLogable'        => true
        );

        $sLogFileId    = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/receivedata/' . date('Ymd') . '_data.log';
        $bLogable    =  true;
		

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        $receiveData = array();
        //$receiveHeader = apache_request_headers();
        // UnivCode는 헤더에서 받아오기로 함 2020-02-26 고병수차장 autoload->database.php에서 처리함.
        
		// json data Receive
        $receiveData = json_decode(file_get_contents('php://input'), true);  // json data name :Order
        $univcode = $_POST['Univcode'];

       // print_r($receiveData);
       //exit;

    	if (!$univcode) {      
            $message['rCode'] = "0001";
            $message['error']['errorCode'] = "0001";
            $message['error']['errorMessage'] = "UnivCode가 Header에 존재하지 않습니다.";
            //writeLog("[{$sLogFileId}] errorCode=" . json_encode($message['error']['errorCode']) . " errorMessage=" . json_encode($message['error']['errorMessage']), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
        //writeLog("[{$sLogFileId}] univcode=" . json_encode($univcode), $sLogPath, $bLogable);

        //array dividing
        // 각 배열의 정의
        $order = array();                // 1단계기본배열:단수
		$orderProducts = array();        // 2단계:복수
        $orderProductOptions = array();  // 3단계:복수
		/*
		$payments = array();             // 2단계:단수
        $cardPaymentDetail = array();    // 3단계:복수
        $couponPaymentDetail = array();  // 3단계:복수
		*/

        //$order = $receiveData;// 이런경우는
		$order = $receiveData['order'];
		// $order에 추가 배열 정의
		$order['univcode'] = $univcode;

		// 배열처리된 entity들을 모두 스트링으로 만들자.
        //$receiveData['order']['additionalInfo'] = implode("",$receiveData['order']['additionalInfo']);
        // 순서상 orderProducts/payments/order 배열 먼저 분리(단수임)
        
        /*
		$payments = $receiveData['order']['payments'];
        unset($receiveData['order']['payments']);
		*/
		
		/*
		// 순서상 복수 가능성 있는 배열 처리
		foreach($orderProducts as $key => $value) {

			foreach($value['orderProductOptions'] as $subKey => $subvalue) {
                $orderProductOptions = $subvalue['orderProductOptions'];
                //print_r($orderProductOptions);
				//exit;

				$orderProductOptions_param = array_param($orderProductOptions,"orderProductOptions");
			
			}
			//$value=orderProducts 복수 배열 model로 던져 DB에 넣자(단 널배열 처리방법)
            $this->API->insertDBorderProduct($value);
            //$orderProductOptions 애도 던져야 함.(예도 복수가능 foreach한번더 돌아야함.)
            $this->API->insertDBorderProductOption($value['orderProductOptions']);
        }

		 unset($value['orderProductOptions']); 나중에
		
		*/

        //$order = $receiveData;// 이런경우는
		$order = $receiveData['order'];

		// order배열 및 모든 배열을 model로 던져 DB에 넣자
        $insertOrder = $this->API->insertOrder($order);
		

		/*
        // payments 배열 model로 던져 DB에 넣자
        $this->API->insertDBpayment($payments);
        // 순서상 세번째 가장 하위(3차원) 배열 분리(복수 가능성)
        foreach($payments as $key => $value){
            $cardPaymentDetail = $value['cardPaymentDetail'];
            unset($$value['cardPaymentDetail']);
            //cardPaymentDetail 배열 model로 던져 DB에 넣자(단 널배열 처리방법)
            $this->API->insertDBcardPaymentdetail($cardPaymentDetail);
        
        }
		// 순서상 세번째 가장 하위(3차원) 배열 분리(복수 가능성)
        foreach($payments as $key => $value){
            $couponPaymentDetail = $value['couponPaymentDetail'];                
            unset($$value['couponPaymentDetail']);
            //couponPaymentDetail 배열 model로 던져 DB에 넣자(단 널배열 처리방법)
            $this->API->insertDBcouponPaymentdetail($cardPaymentDetail);
        }
		*/
        /*
		writeLog("[{$sLogFileId}] order=" . json_encode(implode( '/', $order )), $sLogPath, $bLogable);
        writeLog("[{$sLogFileId}] orderProducts=" . json_encode(implode( '/', $orderProducts )), $sLogPath, $bLogable);
        writeLog("[{$sLogFileId}] orderProductOptions=" . json_encode(implode( '/', $orderProductOptions )), $sLogPath, $bLogable);
        writeLog("[{$sLogFileId}] payments=" . json_encode(implode( '/', $payments )), $sLogPath, $bLogable);
        writeLog("[{$sLogFileId}] cardPaymentDetail=" . json_encode(implode( '/', $cardPaymentDetail )), $sLogPath, $bLogable);
        writeLog("[{$sLogFileId}] couponPaymentDetail=" . json_encode(implode( '/', $couponPaymentDetail )), $sLogPath, $bLogable);
		*/
                
		writeLog("[{$sLogFileId}] result=" . json_encode($message), $sLogPath, $bLogable);
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);

		echo json_encode($message);
        return;
    }

    //주문정보 receive api test
    public function receivetest() {
        
		$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/receivedata/' . date('Ymd') . '_data.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/receivedata/' . date('Ymd') . '_data.log';
        $bLogable    = true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        $receivejson = array();
        //$receiveHeader = apache_request_headers();
        // UnivCode는 헤더에서 받아오기로 함 2020-02-26 고병수차장 autoload->database.php에서 처리함.
        
		// json data Receive
        $receivejson = json_decode(file_get_contents('php://input'), true);  // json data name :order
        $univcode = $_POST['Univcode'];
        print_r($receivejson);
        exit;

        if (!$univcode) {      
            $message['rCode'] = "0001";
            $message['error']['errorCode'] = "0001";
            $message['error']['errorMessage'] = "univcode가 Header에 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'])." eMessage=".json_encode($message['error']['errorMessage']), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
        writeLog("[{$sLogFileId}] univcode=" . json_encode($univcode), $sLogPath, $bLogable);

        //array dividing
        // 각 배열의 정의
        $order = array();    // 1단계기본배열:단수
		$products = array(); // 2단계:복수
        $options = array();  // 3단계:복수
		
		$payments = array();             // 2단계:단수
        $card = array();    // 3단계:복수
        $coupon = array();  // 3단계:복수
        
        // 순서상 orderProducts/payments/order 배열 먼저 분리(단수임)
		$order = $receivejson['order'];
		$products = $order['orderProducts'];
		$payments = $order['payments'];

		// 순서상 복수인 배열 처리
		foreach($products as $key => $value) {
			foreach($value['options'] as $sKey => $svalue) {
                $options = $svalue['options'];
                $options['univcode'] = $univcode;
                $options_param = arrange_param($options,"options");
			}
            unset($value['options']);
            $products['univcode'] = $univcode;
            $Products_param = arrange_param($products,"products");
        }
        // 복수인 payments배열 및 하위 card,coupon처리
		foreach($payments as $key => $value) {
			foreach($value['card'] as $sKey => $svalue) {
                $card = $svalue['card'];
                $card['univcode'] = $univcode;
                $card_param = arrange_param($card,"card");
            }
            foreach($payments['coupon'] as $sKey => $svalue) {
                $coupon = $svalue['coupon'];
                $coupon['univcode'] = $univcode;
                $coupon_param = arrange_param($coupon,"coupon");
			}
            unset($value['card']);
            unset($value['coupon']);
            $payments['univcode'] = $univcode;
            $payments_param = arrange_param($payments,"payments");
        }        
        unset($order['orderProducts']);
        unset($order['payments']);
        $order['univcode'] = $univcode;
        $order_param = arrange_param($order,"order");

        //model로 던져 DB에 트랜잭션 처리를 위해 한방에 처리(단 널배열 처리방법 고민)
        $insertDB = $this->API->insertDB($order_param, $Products_param, $options_param, $payments_param, $card_param, $coupon_param);

        if ($insertDB !== RES_CODE_SUCCESS) {
            $message['rCode'] = "0002";
            $message['error']['errorCode'] = "0002";
            $message['error']['errorMessage'] = "InsertDB 처리실패!!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'])." eMessage=".json_encode($message['error']['errorMessage']), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		writeLog("[{$sLogFileId}] order=" . json_encode(implode( '/', $options_param )), $sLogPath, $bLogable);
        writeLog("[{$sLogFileId}] orderProducts=" . json_encode(implode( '/', $products )), $sLogPath, $bLogable);
        writeLog("[{$sLogFileId}] orderProductOptions=" . json_encode(implode( '/', $options )), $sLogPath, $bLogable);
        //writeLog("[{$sLogFileId}] payments=" . json_encode(implode( '/', $payments )), $sLogPath, $bLogable);
        //writeLog("[{$sLogFileId}] cardPaymentDetail=" . json_encode(implode( '/', $cardPaymentDetail )), $sLogPath, $bLogable);
        //writeLog("[{$sLogFileId}] couponPaymentDetail=" . json_encode(implode( '/', $couponPaymentDetail )), $sLogPath, $bLogable);
        
		writeLog("[{$sLogFileId}] result=" . json_encode($message), $sLogPath, $bLogable);
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);

		echo json_encode($message);
        return;
    }
    
}    
    
/* End of file api.php */
/* Location: ./application/controllers/api.php */