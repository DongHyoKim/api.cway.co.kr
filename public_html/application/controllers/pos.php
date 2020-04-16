<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pos extends CT_Controller {
  
  private $json_array = array();

  public function __construct(){
		parent::__construct();
		$this->load->model('Pos_api_model','API');
  }	
  
  public function index(){

  }
  
  public function ticket_machine_mileage(){	

    $logs = array(
        'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
        'sLogPath'      => BASEPATH . '../../logs/receivedata/' . date('Ymd') . '_data.log',
        'bLogable'      => true
    );

    $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
    $sLogPath    = BASEPATH . '../../logs/copartner/success_log/' . date('Ymd') . '_data.log';
	$eLogPath    = BASEPATH . '../../logs/copartner/error_log/' . date('Ymd') . '_data.log';
    $bLogable    = true;

    $message = array(
        'rCode' => RES_CODE_SUCCESS,
        'error' => array (  'errorCode'     => null,
                            'errorMessage'  => null, ),
    );
    writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

	$UnivCode = $this->input->post('UnivCode',true);
	$saletotal_member = $this->input->post('saletotal_member',true);
		
	if (!$UnivCode) {      
	  $json_array['status'] = -1;    
	  $json_array['message'] = "대학코드가 존재하지 않습니다.";
      writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message']), $eLogPath, $bLogable);
	  echo json_encode($json_array);      
	  exit;
	}

	if (!$saletotal_member) {      
	  $json_array['status'] = -1;    
	  $json_array['message'] = "조합원번호가 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message']), $eLogPath, $bLogable);
	  echo json_encode($json_array);      
	  exit;
	}

	$tmp_result = $this->API->getTotalMileage($saletotal_member);
	$json_array['status']       = 1;
	$json_array['totalmileage'] = $tmp_result['0']['MILEAGE'];
	echo json_encode($json_array);
	writeLog("[{$sLogFileId}] univcode=".json_encode($UnivCode)." member=".json_encode($json_array), $sLogPath, $bLogable);
	writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
	exit;    
	}

  public function ticket_machine_mileage_insert() {

	$logs = array(
        'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
        'sLogPath'      => BASEPATH . '../../logs/receivedata/' . date('Ymd') . '_data.log',
        'bLogable'      => true
    );
    $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
    $sLogPath    = BASEPATH . '../../logs/copartner/success_log/' . date('Ymd') . '_data.log';
	$eLogPath    = BASEPATH . '../../logs/copartner/error_log/' . date('Ymd') . '_data.log';
    $bLogable    = true;
    $message = array(
        'rCode' => RES_CODE_SUCCESS,
        'error' => array (  'errorCode'     => null,
                            'errorMessage'  => null, ),
    );

    writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

	$UnivCode = $this->input->post('UnivCode',true);
    $BARCODENO = $this->input->post('BARCODENO',true);  //-- 조합원코드
	$params = array(
		'BARCODENO'   => $BARCODENO,
		'HPHONENO'    => '',
		'UNIVCODE'    => $UnivCode,
		'SUBUNIVCODE' => '001',
		);
	$tmp_result = $this->API->getCopartnerMember($params);
    if(empty($tmp_result)) {
		$json_array['status'] = -1;    
	    $json_array['message'] = "조합원번호가 존재하지 않습니다.";
	    writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message']), $eLogPath, $bLogable);
		writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
	    echo json_encode($json_array);      
	    exit;
    }
	unset($params);
    $params = array(
	    'BARCODENO'   => $BARCODENO,
        'USEDATE'     => $this->input->post('USEDATE',true),    //-- 영업일
        'DEPTCODE'    => $this->input->post('DEPTCODE',true),   //-- 매장코드
        'POSNO'       => $this->input->post('POSNO',true),      //-- 포스번호
        'BILLNUMBER'  => $this->input->post('BILLNUMBER',true), //-- 영수증번호
        'USEMILEAGE'  => $this->input->post('USEMILEAGE',true), //-- 포인트사용금액
        'AMOUNT'      => $this->input->post('AMOUNT',true),     //-- 이용고금액
        'AMOUNTSAVE'  => $this->input->post('AMOUNTSAVE',true), //-- 포인트적용금액
        'REMARK'      => $this->input->post('REMARK',true),     //-- 설명
        'UNIVCODE'    => $UnivCode,                             //-- 학교코드
		'SUBUNIVCODE' => '001',                                 //-- 캠퍼스코드
	);

	if (!$params['BARCODENO']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "조합원코드가 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message']), $eLogPath, $bLogable);
	  echo json_encode($json_array);
      exit;
    }
    if (!$params['USEDATE']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "사용일자가 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message']), $eLogPath, $bLogable);
      echo json_encode($json_array);
      exit;
    }
    if (!$params['DEPTCODE']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "매장코드가 존재하지 않습니다.";
      writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message']), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }
    if (!$params['POSNO']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "자판기 코드가 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message']), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }
    if (!$params['BILLNUMBER']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "식권번호가 존재하지 않습니다.";
      writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message']), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }
    /*
	if (!$params['USEMILEAGE']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "포인트사용액이 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message']), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }
    if (!$params['AMOUNT']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "이용금액이 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message']), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }
    if (!$params['AMOUNTSAVE']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "포인트적용액이 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message']), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }
    if (!$params['REMARK']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "비고 내용이 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message']), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }
	*/
    if (!$params['UNIVCODE']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "학교코드가 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message']), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
     }
	unset($tmp_result);
	$tmp_result = $this->API->insertSaleTotalMileage($params);
    
    if($tmp_result){
      $json_array['status']  = 1; 
    }else{
      $json_array['status']  = -1; 
      $json_array['message'] = "DB에러가 발생하였습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message']), $eLogPath, $bLogable);
    }    
 
    echo json_encode($json_array);
	writeLog("[{$sLogFileId}] results=".json_encode(implode("|",$params)), $sLogPath, $bLogable);
	writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
    exit;    
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */