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
        
        /*
		$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'        => BASEPATH . '../../logs/ReceiveData/' . date('Ymd') . '_data.log',
            'bLogable'        => true
        );

        $sLogFileId    = (!empty($logs['sLogFileId']) && isset($logs['sLogFileId'])) ? $logs['sLogFileId'] : time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = (!empty($logs['sLogPath']) && isset($logs['sLogPath'])) ? $logs['sLogPath'] : BASEPATH . '../../logs/ReceiveData/' . date('Ymd') . '_data.log';
        $bLogable    = (!empty($logs['bLogable']) && isset($logs['bLogable'])) ? $logs['bLogable'] : true;
		*/

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
        //writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        $receiveData = array();
        //$receiveHeader = apache_request_headers();
        // UnivCode는 헤더에서 받아오기로 함 2020-02-26 고병수차장 autoload->database.php에서 처리함.
        
		// json data Receive
        $receiveData = json_decode(file_get_contents('php://input'), true);  // json data name :Order
        $univcode = $_POST['Univcode'];

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
        /*
		$orderProducts = array();        // 2단계:복수
        $payments = array();             // 2단계:단수
        $orderProductOptions = array();  // 3단계:복수
        $cardPaymentDetail = array();    // 3단계:복수
        $couponPaymentDetail = array();  // 3단계:복수
		*/
        // 배열처리된 entity들을 모두 스트링으로 만들자.
        $receiveData['order']['additionalInfo'] = implode("",$receiveData['order']['additionalInfo']);
        // 순서상 orderProducts/payments/order 배열 먼저 분리(단수임)
        /*
		$orderProducts = $receiveData['order']['orderProducts'];
        unset($receiveData['order']['orderProducts']);
        $payments = $receiveData['order']['payments'];
        unset($receiveData['order']['payments']);
		*/
        //$order = $receiveData;// 이런경우는
		$order = $receiveData['order'];// 이런식으로 한차원 줄여서 가져 가는게 덜쓰고 좋습니다.
		
		/*
			$order에 추가 배열 정의
		*/
		$order['univcode'] = $univcode;

        // order 배열 model로 던져 DB에 넣자
        $insertOrder = $this->API->insertOrder($order);
        // 순서상 세번째 가장 하위(3차원) 배열 분리(복수 가능성)
        /*
		foreach($orderProducts as $key => $value){
            $orderProductOptions = $value['orderProductOptions'];
            unset($value['orderProductOptions']);
            //$value=orderProducts 복수 배열 model로 던져 DB에 넣자(단 널배열 처리방법)
            $this->API->insertDBorderProduct($value);
            //$orderProductOptions 애도 던져야 함.
            $this->API->insertDBorderProductOption($value['orderProductOptions']);
        }
		
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
                
		//writeLog("[{$sLogFileId}] result=" . json_encode($message), $sLogPath, $bLogable);
        //writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);

		echo json_encode($message);
        return;
    }

    //주문정보 receive api test
    public function receiveTest() {
        
        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );

        $receiveData = array();
        //$receiveHeader = apache_request_headers();
        // UnivCode는 헤더에서 받아오기로 함 2020-02-26 고병수차장 autoload->database.php에서 처리함.
        // json data Receive
        $receiveData = json_decode(file_get_contents('php://input'), true);  // json data name :Order
        $Univcode = $_POST['Univcode'];

        if (!$Univcode) {      
            $message['rCode'] = "0001";
            $message['error']['errorCode'] = "0001";
            $message['error']['errorMessage'] = "UnivCode가 Header에 존재하지 않습니다.";
            echo json_encode($message);
            exit;
        }

        //array dividing
        // 각 배열의 정의
        $order = array();                // 1단계기본배열:단수
        $order = $receiveData;

        //$order['order']['additionalInfo'] = implode("",$order['order']['additionalInfo']);
        unset($order['order']['additionalInfo']);
        $order['order']['univcode'] = $Univcode;

        // order 배열 model로 던져 DB에 넣자
        $insertOrderTest = $this->API->insertOrderTest($order);
        //$this->home_model->get_data();
        // 순서상 세번째 가장 하위(3차원) 배열 분리(복수 가능성)

        echo json_encode($message);
        return;
    }
    
}    
    
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */