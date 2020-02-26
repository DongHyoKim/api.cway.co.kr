<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** 
 * $t1 기준시간(없으면 현재시간으로 대체) 
 * $t2 비교시간
 * PHP 5.3 이상. 
 */ 
ini_set('display_errors', 1); // php 에러 표시하기

function passwdEncrypt($pw){
	// md5 + sha1 128 x5
	return sha1(md5(sha1(md5(sha1(md5(sha1(md5(sha1(md5($pw))))))))));
}

function DateDiff($dt1, $dt2) {
	/** 사용법
	echo DateDiff('2010-07-27 12:11:13', '2319-09-11 21:52:05'); // 309년 후
	echo DateDiff('2010-07-27 12:11:13', '2010-07-21 15:38:42'); // 5일 전
	echo DateDiff(null, '2010-06-11 15:38:42'); // (현재시간 기준) 1개월 전
	echo DateDiff(null, '2010-07-27 08:38:42'); // (현재시간 기준) 25분 전
	*/	 
	if(!$dt2) return; 
	//$trans = array('y' => '년', 'm' => '개월', 'd' => '일', 'h' => '시간', 'i' => '분', 's' => '초');
	//$ago = array(' 후', ' 전'); 
	$dt1 = new DateTime($dt1); 
	$dt2 = new DateTime($dt2); 
	$t1 = $dt1->diff($dt2);

	// $to_time = strtotime($dt1);
	// $from_time = strtotime($dt2); 
	// $minutes = round(abs($to_time - $from_time) / 60, 2);

	if($t1->y == 0 && $t1->m == 0 && $t1->d == 0 && $t1->h == 0 && $t1->i <= 1)
		return "방금 전";
	else if($t1->y == 0 && $t1->m == 0 && $t1->d == 0 && $t1->h == 0 && $t1->i <= 60 )
		return $t1->i . "분 전";
	else if($t1->y == 0 && $t1->m == 0 && $t1->d == 0 && $t1->h >= 1)
		return "{$t1->h}시간 전";
	else if($t1->y == 0 && ( $dt1->format('Y') == $dt2->format('Y') ) )
		return $dt2->format('m.d A h:i');
	else
		return $dt2->format('Y.m.d');
} 

// PRINT STREAM
function stripString($string){
	$string = htmlentities($string, ENT_QUOTES, 'UTF-8');
	$string = nl2br($string);
	$string = stripslashes(str_replace(array("\\r\\n", "\\r", "\\n"), "<br />", $string));
	return $string;
}

function stripString_HTML($string){
	$string = stripslashes(str_replace(array("\\r\\n", "\\r", "\\n"), "<br />", $string));
	return $string;
}

// AUTOLINK STREAM
function autoLink($text){
	//$pattern = "/(((http[s]?:\/\/)|(www\.))(([a-z][-a-z0-9]+\.)?[a-z][-a-z0-9]+\.[a-z]+(\.[a-z]{2,2})?)\/?[a-z0-9.,_\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";
	$pattern = "/(((http[s]?:\/\/))?(([-a-z0-9]+\.)?[-a-z0-9]+\.[a-z]+(\.[a-z]{2,2})?)\/?[a-z0-9.,_\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";
	$pattern = "/(((http[s]?:\/\/))(([-a-z0-9]+\.)?[-a-z0-9]+\.[a-z]+(\.[a-z]{2,2})?)\/?[a-z0-9.,_\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";
	$text = preg_replace($pattern, " <a href='$1' target='_blank' class='link'>$1</a>", $text);
	$text = preg_replace("/href='www/", "href='http://www", $text);
	return $text;
}

function writeLog($str='', $filepath='' )
{
    /**
    * 기존 common_service/write_log
    */    

    $CI =& get_instance();
    $CI->load->helper('file');
    $sLogPath = (empty($filepath)) ? $_SERVER['DOCUMENT_ROOT'].'/../logs/'.date('Ymd', time()).'.log' : $filepath;
    $sLog = '['.date('Y-m-d H:i:s').'] '.$str."\n";
    
    log_auto_file_delete($sLogPath);
    return write_file($sLogPath, $sLog, 'a+');
}

function log_auto_file_delete($dir)
{
    if(is_dir($dir)) {
        if($dh = opendir($dir)) {
            while(($entry = readdir($dh)) !== false) {
                if($entry == '.' || $entry == '..')
                    continue;
                $subdir = $dir.'/'.$entry;
                if(is_dir($subdir)) {
                    log_auto_file_delete($subdir);
                } else {
                    if($entry == 'index.php')
                        continue;
                    $sfile = $dir.'/'.$entry;
                    $mtime = @filemtime($sfile);
                    // 최종수정일이 30일 이상인 파일만 삭제
                    if(file_exists($sfile) && (time() - $mtime <= 24*60*60*LOG_FILE_AUTO_DELETE_DATE))
                        continue;
                    // 파일삭제
                    @unlink($sfile);
                }
            }
            closedir($dh);
        }
    }
}

// checkOrder
function checkOrder($Order) {
    $createdAt              = trim($Order['createdAt']);                     // 등록일              s30
    $updatedAt              = trim($Order['updatedAt']);                     // 수정일              s30
    $deletedAt              = trim($Order['deletedAt']);                     // 삭제일              s30
    $billNo                 = trim($Order['billNo']);                        // 영수번호*           s30
    $headOfficeId           = trim($Order['headOfficeId']);                  // 본사id              s30
    $franchiseId            = trim($Order['franchiseId']);                   // 가맹점id            s30
    $franchiseCd            = trim($Order['franchiseCd']);                   // 지점코드            s30  연동처리
    $deviceId               = trim($Order['deviceId']);                      // 기기id              s30 
    $deviceSeq              = $Order['deviceSeq'];                           // 기기번호            n
    $posNo                  = trim($Order['posNo']);                         // 포스번호*           s5   연동 기기번호
    $channelType            = trim($Order['channelType']);                   // 채널구분            s10  ch01:kiosk
    $saleDay                = $Order['saleDay'];                             // 영업일*             s10  YYYY-MM-DD
    $outerBillno            = trim($Order['outerBillno']);                   // 외부연동영수번호     s30
    $tradeType              = trim($Order['tradeType']);                     // 거래구분            s2   S:매출 C:취소
    $serviceType            = trim($Order['serviceType']);                   // 매장/포장           s2   S:매장 P:포장
    $salesTarget            = trim($Order['salesTarget']);                   // 서비스대상          s2   G:일반 S:직원
    $totalAmount            = $Order['totalAmount'];                         // 총주문금액          n
    $paymentAmount          = $Order['paymentAmount'];                       // 결재금액            n
    $discountAmount         = $Order['discountAmount'];                      // 총할인금액          n
    //$discountRate           = $Order['discountRate'];                        // 할인율?             s
    $couponAmount           = $Order['couponAmount'];                        // 쿠폰금액            n
    $cashableAmount         = $Order['cashableAmount'];                      // 현금화금액          n
    $taxationAmount         = $Order['taxationAmount'];                      // 과세대상금액        n
    $dutyAmount             = $Order['dutyAmount'];                          // 면세금액            n
    $totalTax               = $Order['totalTax'];                            // 부가세액            n
    $tableNo                = trim($Order['tableNo']);                       // 테이블번호          s3
    $orgBillNo              = trim($Order['orgBillNo']);                     // 원거래영수번호      s30  반품건원거래번호
    //$salesDayOrderSeq       = trim($Order['salesDayOrderSeq']);              // ?
    //$orgSalesDayOrderSeq    = trim($Order['orgSalesDayOrderSeq']);           // ?
    $orderStatus            = trim($Order['orderStatus']);                   // 주문상태            s5   1001주문중 9999주문취소 1000픽업주문취소 1003주문접수 1005주문확인 2007상품준비중 2009픽업대기 2020픽업완료 2085픽업지연 2090픽업지연완료 2099픽업미완료
    $paymentStatus          = trim($Order['paymentStatus']);                 // 결재상태            s2   S성공 F실패(부분) F결재시 부분실패
    $cancelBillNo           = trim($Order['cancelBillNo']);                  // 취소영수번호        s30  원거래건의취소영수번호
    $receiptPrintCountType  = trim($Order['receiptPrintCountType']);         // 영수증출력갯수타입  s20
    $exchangePrintCountType = trim($Order['exchangePrintCountType']);        // 교환건출력갯수타입  s20
    $additionalInfo         = trim($Order['additionalInfo']);                // 부가정보            json
    $filler1                = trim($Order['filler1']);                       // 비고1               s500
    $filler2                = trim($Order['filler2']);                       // 비고2               s500
    $filler3                = trim($Order['filler3']);                       // 비고3               s500
    $filler4                = trim($Order['filler4']);                       // 비고4               s500
    $closeYn                = trim($Order['closeYn']);                       // 마감처리여부        s255
    $closeDate              = trim($Order['closeDate']);                     // 마감일자            s255
    $salesDaySeq            = $Order['salesDaySeq'];                         // 영업일자순번        n

    $returnarr = array(
        //'UnivCode'               => $UnivCode,
        'createdAt'              => $createdAt,
        'updatedAt'              => $updatedAt,
        'billNo'                 => $billNo,
        'headOfficeId'           => $headOfficeId,
        'franchiseId'            => $franchiseId,
        'franchiseCd'            => $franchiseCd,
        'deviceId'               => $deviceId,
        'deviceSeq'              => $deviceSeq,
        'posNo'                  => $posNo,
        'channelType'            => $channelType,
        'saleDay'                => $saleDay,
        'outerBillno'            => $outerBillno,
        'tradeType'              => $tradeType,
        'serviceType'            => $serviceType,
        'salesTarget'            => $salesTarget,
        'totalAmount'            => $totalAmount,
        'paymentAmount'          => $paymentAmount,
        'discountAmount'         => $discountAmount,
        'couponAmount'           => $couponAmount,
        'cashableAmount'         => $cashableAmount,
        'taxationAmount'         => $taxationAmount,
        'dutyAmount'             => $dutyAmount,
        'totalTax'               => $totalTax,
        'tableNo'                => $tableNo,
        'orgBillNo'              => $orgBillNo,
        'orderStatus'            => $orderStatus,
        'paymentStatus'          => $paymentStatus,
        'cancelBillNo'           => $cancelBillNo,
        'receiptPrintCountType'  => $receiptPrintCountType,
        'exchangePrintCountType' => $exchangePrintCountType,
        'additionalInfo'         => $additionalInfo,
        'additionalInfo'         => $additionalInfo,
        'filler1'                => $filler1,
        'filler2'                => $filler2,
        'filler3'                => $filler3,
        'filler4'                => $filler4,
        'closeYn'                => $closeYn,
        'closeDate'              => $closeDate,
        'salesDaySeq'            => $salesDaySeq,
    );

    return $returnarr;
}