<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pos extends CT_Controller {

    private $json_array = array();

    public function __construct(){
		parent::__construct();
		$this->load->model('Pos_api_model','API');
    }	
    
    public function index(){

    }

    //주문정보 receive api
	public function receive() {
        
        $logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'        => BASEPATH . '../../logs/ReceiveData/' . date('Ymd') . '_data.log',
            'bLogable'        => true
        );

        $sLogFileId    = (!empty($logs['sLogFileId']) && isset($logs['sLogFileId'])) ? $logs['sLogFileId'] : time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = (!empty($logs['sLogPath']) && isset($logs['sLogPath'])) ? $logs['sLogPath'] : BASEPATH . '../../logs/ReceiveData/' . date('Ymd') . '_data.log';
        $bLogable    = (!empty($logs['bLogable']) && isset($logs['bLogable'])) ? $logs['bLogable'] : true;

        $message = array(
            'rCode'         => RES_CODE_SUCCESS,
            'errorCode'     => null,
            'errorMessage'  => null,
        );
        $this->common_lib->writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        try {
        
            $order = array();
            $order = $this->input->post('order',true);  // 왕창 받아오자!
    
            if (!$order) {      
                $message['rCode'] = "0001";
                $message['errorCode'] = "0001";
                $message['errorMessage'] = "정상적으로 수신되지 않았습니다.";
                echo json_encode($message);
                exit;
            }
            //쪼개서 넣기
            // 1.order 테이블
            $insertDB = "";
            $insertDB = $this->API->insertDBorder($receiveArray['order']);
            if($insertDB !== RES_CODE_SUCCESS) {
                $message['rCode'] = "0011";
                $message['errorCode'] = "0011";
                $message['errorMessage'] = "order 테이블이 처리되지 않았습니다.";
                echo json_encode($message);
                exit;
            }
            // 2. orderProduct 테이블
            $insertDB = "";
            $insertDB = $this->API->insertDBorderProduct($receiveArray['orderProduct']);
            if($insertDB !== RES_CODE_SUCCESS) {
                $message['rCode'] = "0012";
                $message['errorCode'] = "0012";
                $message['errorMessage'] = "orderProduct 테이블이 처리되지 않았습니다.";
                echo json_encode($message);
                exit;
            }
            // 3. orderProductOption 테이블
            $insertDB = "";
            $insertDB = $this->API->insertDBorderProductOption($receiveArray['orderProductOption']);
            if($insertDB !== RES_CODE_SUCCESS) {
                $message['rCode'] = "0013";
                $message['errorCode'] = "0013";
                $message['errorMessage'] = "orderProductOption 테이블이 처리되지 않았습니다.";
                echo json_encode($message);
                exit;
            }
            
            // 4. payment 테이블
            $insertDB = "";
            $insertDB = $this->API->insertDBpayment($receiveArray['payment']);
            if($insertDB !== RES_CODE_SUCCESS) {
                $message['rCode'] = "0014";
                $message['errorCode'] = "0014";
                $message['errorMessage'] = "payment 테이블이 처리되지 않았습니다.";
                echo json_encode($message);
                exit;
            }
    
            // 5. cardPaymentdetail 테이블
            $insertDB = "";
            $insertDB = $this->API->insertDBcardPaymentdetail($receiveArray['cardPaymentdetail']);
            if($insertDB !== RES_CODE_SUCCESS) {
                $message['rCode'] = "0015";
                $message['errorCode'] = "0015";
                $message['errorMessage'] = "cardPaymentdetail 테이블이 처리되지 않았습니다.";
                echo json_encode($message);
                exit;
            }
            
            // 6. couponPaymentdetail 테이블
            $insertDB = "";
            $insertDB = $this->API->insertDBcouponPaymentdetail($receiveArray['couponPaymentdetail']);
            if($insertDB !== RES_CODE_SUCCESS) {
                $message['rCode'] = "0016";
                $message['errorCode'] = "0016";
                $message['errorMessage'] = "couponPaymentdetail 테이블이 처리되지 않았습니다.";
                echo json_encode($message);
                exit;
            }
            
            echo json_encode($receiveArray);
            return
        } catch (File_exception $e) {
        
        }
        $this->common_lib->writeLog("[{$sLogFileId}] result=" . json_encode($results), $sLogPath, $bLogable);
        $this->common_lib->writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
        return $results;
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