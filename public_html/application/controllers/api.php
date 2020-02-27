<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CT_Controller {

    private $json_array = array();

    public function __construct(){
		parent::__construct();
		//$this->load->model('Api_model','API');
    }	
    
    public function index(){

    }

    //주문정보 receive api
	public function receiveData() {
        
        $logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'        => BASEPATH . '../../logs/ReceiveData/' . date('Ymd') . '_data.log',
            'bLogable'        => true
        );

        $sLogFileId    = (!empty($logs['sLogFileId']) && isset($logs['sLogFileId'])) ? $logs['sLogFileId'] : time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = (!empty($logs['sLogPath']) && isset($logs['sLogPath'])) ? $logs['sLogPath'] : BASEPATH . '../../logs/ReceiveData/' . date('Ymd') . '_data.log';
        $bLogable    = (!empty($logs['bLogable']) && isset($logs['bLogable'])) ? $logs['bLogable'] : true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        try {
            
            $receiveData = array();
            //$receiveHeader = apache_request_headers();
            // UnivCode는 헤더에서 받아오기로 함 2020-02-26 고병수차장 autoload->database.php에서 처리함.
            // json data Receive
            $receiveData = json_decode(file_get_contents('php://input'), true);  // json data name :Order
            $UnivCode = $$_POST['UnivCode'];
            if (!$UnivCode) {      
                $message['rCode'] = "0001";
                $message['error']['errorCode'] = "0001";
                $message['error']['errorMessage'] = "UnivCode가 Header에 존재하지 않습니다.";
                writeLog("[{$sLogFileId}] errorCode=" . json_encode($message['error']['errorCode']) . " errorMessage=" . json_encode($message['error']['errorMessage']), $sLogPath, $bLogable);
                echo json_encode($message);
                exit;
            }
            writeLog("[{$sLogFileId}] UnivCode=" . json_encode($UnivCode), $sLogPath, $bLogable);

            //array dividing
            // 각 배열의 정의
            $order = array();                // 1단계기본배열:단수
            $orderProducts = array();        // 2단계:복수
            $payments = array();             // 2단계:단수
            $orderProductOptions = array();  // 3단계:복수
            $cardPaymentDetail = array();    // 3단계:복수
            $couponPaymentDetail = array();  // 3단계:복수
            // 배열처리된 entity들을 모두 스트링으로 만들자.
            //$receiveData['order']['additionalInfo'] = implode("",$receiveData['order']['additionalInfo']);
            // 순서상 orderProducts/payments/order 배열 먼저 분리(단수임)
            $orderProducts = $receiveData['order']['orderProducts'];
            unset($receiveData['order']['orderProducts']);
            $payments = $receiveData['order']['payments'];
            unset($receiveData['order']['payments']);
            $order = $receiveData;

            // order 배열 model로 던져 DB에 넣자
            $this->API->insertOrder($order);
            // 순서상 세번째 가장 하위(3차원) 배열 분리(복수 가능성)
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
            writeLog("[{$sLogFileId}] order=" . json_encode(implode( '/', $order )), $sLogPath, $bLogable);
            writeLog("[{$sLogFileId}] orderProducts=" . json_encode(implode( '/', $orderProducts )), $sLogPath, $bLogable);
            writeLog("[{$sLogFileId}] orderProductOptions=" . json_encode(implode( '/', $orderProductOptions )), $sLogPath, $bLogable);
            writeLog("[{$sLogFileId}] payments=" . json_encode(implode( '/', $payments )), $sLogPath, $bLogable);
            writeLog("[{$sLogFileId}] cardPaymentDetail=" . json_encode(implode( '/', $cardPaymentDetail )), $sLogPath, $bLogable);
            writeLog("[{$sLogFileId}] couponPaymentDetail=" . json_encode(implode( '/', $couponPaymentDetail )), $sLogPath, $bLogable);

        } catch (File_exception $e) {
            $message['rCode'] = "0002";
            $message['error']['errorCode'] = "0002";
            $message['error']['errorMessage'] = "정상적으로 처리되지 않았습니다.";
            writeLog("[{$sLogFileId}] errorCode=" . json_encode($message['error']['errorCode']) . " errorMessage=" . json_encode($message['error']['errorMessage']), $sLogPath, $bLogable);
            echo json_encode($message);
        }
        writeLog("[{$sLogFileId}] result=" . json_encode($message), $sLogPath, $bLogable);
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
        echo json_encode($message);
        return;
    }    

    public function ticket_machine_mileage_insert() {	     
        $UnivCode                     = $this->input->post('UnivCode',true);
        $saletotal_date               = $this->input->post('saletotal_date',true);  //--* 매출일자
        $saletotal_store              = $this->input->post('saletotal_store',true); //--* 매장코드
        $saletotal_posid              = $this->input->post('saletotal_posid',true); //--* 자판기코드
        $saletotal_billnumber         = $this->input->post('saletotal_billnumber',true); //--* 식권번호
        $saletype                     = $this->input->post('saletype',true);  //--  매출구분(매출1 / 반품-1)
        $cashcredit                   = $this->input->post('cashcredit',true);  //--  매출형태(현금1 / 신용카드2)
        $saletotal_cardvan            = $this->input->post('saletotal_cardvan',true); //--  신용카드밴사명(현금일 경우 space)
        $saletotal_joinno             = $this->input->post('saletotal_joinno',true); //--  밴사매입사코드(현금일 경우space)
        $pointtype                    = $this->input->post('pointtype',true); //--  포인트처리(적립001 / 사용002 / 미사용000)
        $saletotal_member             = $this->input->post('saletotal_member',true); //--  포인트처리 조합원번호(상지대7001 1700 1*** ***a:*는 연번/a는 체크sum, 미사용시 space)
        $saletotal_profit             = $this->input->post('saletotal_profit',true); //--  포인트사용시 사용포인트 금액, 미사용시 0.0
        $amount                       = $this->input->post('amount',true); //--  포인트사용시 사용포인트 금액, 미사용시 0.0
        $realdatetime                 = $this->input->post('realdatetime',true); //--  실판매시간
    
        
        if (!$UnivCode) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "대학코드가 존재하지 않습니다.";
            echo json_encode($json_array);      
            exit;
        }
    
        if (!$saletotal_date) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "매출일자가 존재하지 않습니다.";
            echo json_encode($json_array);      
            exit;
        }
    
        if (!$saletotal_store) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "매장코드가 존재하지 않습니다.";
            echo json_encode($json_array);      
            exit;
        }
    
        if (!$saletotal_posid) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "자판기 코드가 존재하지 않습니다.";
            echo json_encode($json_array);      
            exit;
        }
    
        if (!$saletotal_billnumber) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "식권번호가 존재하지 않습니다.";
            echo json_encode($json_array);      
            exit;
        }
        
        if (!$saletype) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "매출구분이 존재하지 않습니다.";
            echo json_encode($json_array);      
            exit;
        }
    
        if (!$cashcredit) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "매출형태가 존재하지 않습니다.";
            echo json_encode($json_array);      
            exit;
        }
    
        if (!$realdatetime) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "실판매시간이 존재하지 않습니다.";
            echo json_encode($json_array);      
            exit;
        }
    
        if (!$amount) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "판매금액이 존재하지 않습니다.";
            echo json_encode($json_array);      
            exit;
        }
        
        if ($cashcredit == "2" ) { // 카드일경우      
            if (!$saletotal_cardvan) {      
                $json_array['status'] = -1;    
                $json_array['message'] = "밴사명이 존재하지 않습니다.";
                echo json_encode($json_array);      
                exit;
            }
            if (!$saletotal_joinno) {      
                $json_array['status'] = -1;    
                $json_array['message'] = "밴매입사코드가 존재하지 않습니다.";
                echo json_encode($json_array);      
                exit;
            }        
        }
    
        if (!$pointtype) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "포인트처리 구분자가  존재하지 않습니다.";
            echo json_encode($json_array);      
            exit;
        }
    
        if ($pointtype != "000" ) { // 적립금 미사용이 아닌경우 	     
            if (!$saletotal_profit) {      
                $json_array['status'] = -1;    
                $json_array['message'] = "사용포인트 금액이  존재하지 않습니다.";
                echo json_encode($json_array);      
                exit;
            }
            if (!$amount) {      
                $json_array['status'] = -1;    
                $json_array['message'] = "사용포인트 금액이  존재하지 않습니다.";
                echo json_encode($json_array);      
                exit;
            }        
        } else {
            $saletotal_member = " " ; // 미사용시는 공백 
            $saletotal_profit = 0.0 ; // 미사용시는 0.0 
        }       
        
        $tmp_result = $this->API->insertSaleTotalMileage($UnivCode, $saletotal_date, $saletotal_store, $saletotal_posid, $saletotal_billnumber, $saletype,  $cashcredit, $saletotal_cardvan, $saletotal_joinno, $pointtype, $saletotal_member, $saletotal_profit, $amount, $realdatetime);
        
        if($tmp_result) {
            $json_array['status']  = 1; 
        } else {
            $json_array['status']  = -1; 
            $json_array['message'] = "DB에러가 발생하였습니다.";
        }    

        echo json_encode($json_array);      
        exit;    
	}
    

    
}    
    
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */