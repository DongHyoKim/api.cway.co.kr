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

    
    // insertDBorder
    function insertOrder($order) {

		global $db;

        $univcode               = $order['univcode'];                        // 대학코드*           s5
        $createdAt              = trim($order['createdAt']);                 // 등록일              s30
        $updatedAt              = trim($order['updatedAt']);                 // 수정일              s30
        $billNo                 = trim($order['billNo']);                    // 영수번호*           s30
        $headOfficeId           = trim($order['headOfficeId']);              // 본사id              s30
        $franchiseId            = trim($order['franchiseId']);               // 가맹점id            s30
        $storeCode              = trim($order['franchiseCd']);               // 지점코드            s30  연동처리
        $deviceId               = trim($order['deviceId']);                  // 기기id              s30 
        $deviceSeq              = $order['deviceSeq'];                       // 기기번호            n
        $posNo                  = trim($order['posNo']);                     // 포스번호*           s5   연동 기기번호
        $channelType            = trim($order['channelType']);               // 채널구분            s10  ch01:kiosk
        $saleDay                = $order['saleDay'];                         // 영업일*             s10  YYYY-MM-DD
        $outerBillno            = trim($order['outerBillno']);               // 외부연동영수번호    s30
        $tradeType              = trim($order['tradeType']);                 // 거래구분            s2   S:매출 C:취소
        $serviceType            = trim($order['serviceType']);               // 매장/포장           s2   S:매장 P:포장
        $salesTarget            = trim($order['salesTarget']);               // 서비스대상          s2   G:일반 S:직원
        $totalAmount            = $order['totalAmount'];                     // 총주문금액          n
        $paymentAmount          = $order['paymentAmount'];                   // 결재금액            n
        $discountAmount         = $order['discountAmount'];                  // 총할인금액          n
        $couponAmount           = $order['couponAmount'];                    // 쿠폰금액            n
        $cashableAmount         = $order['cashableAmount'];                  // 현금화금액          n
        $taxationAmount         = $order['taxationAmount'];                  // 과세대상금액        n
        $dutyAmount             = $order['dutyAmount'];                      // 면세금액            n
        $totalTax               = $order['totalTax'];                        // 부가세액            n
        $tableNo                = trim($order['tableNo']);                   // 테이블번호          s3
        $orgBillNo              = trim($order['orgBillNo']);                 // 원거래영수번호      s30  반품건원거래번호
        $orderStatus            = trim($order['orderStatus']);               // 주문상태            s5   1001주문중 9999주문취소 1000픽업주문취소 1003주문접수 1005주문확인 2007상품준비중 2009픽업대기 2020픽업완료 2085픽업지연 2090픽업지연완료 2099픽업미완료
        $paymentStatus          = trim($order['paymentStatus']);             // 결재상태            s2   S성공 F실패(부분) F결재시 부분실패
        $cancelBillNo           = trim($order['cancelBillNo']);              // 취소영수번호        s30  원거래건의취소영수번호
        $receiptPrintCountType  = trim($order['receiptPrintCountType']);     // 영수증출력갯수타입  s20
        $exchangePrintCountType = trim($order['exchangePrintCountType']);    // 교환건출력갯수타입  s20
        $additionalInfo         = trim($order['additionalInfo']);            // 부가정보            json
        $filler1                = trim($order['filler1']);                   // 비고1               s500
        $filler2                = trim($order['filler2']);                   // 비고2               s500
        $filler3                = trim($order['filler3']);                   // 비고3               s500
        $filler4                = trim($order['filler4']);                   // 비고4               s500
        $closeYn                = trim($order['closeYn']);                   // 마감처리여부        s255
        $closeDate              = trim($order['closeDate']);                 // 마감일자            s255
        $salesDaySeq            = $order['salesDaySeq'];                     // 영업일자순분        n

        $sp = "VENDINGM.dbo.SP_ITMS_ORDER;01 ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ";

        $params = array(
            'univcode'               => $univcode,
            'saleDay'                => $saleDay,
            'storeCode'              => $storeCode,
            'posNo'                  => $posNo,
            'billNo'                 => $billNo,
    		'createdAt'              => $createdAt,
            'updatedAt'              => $updatedAt,
            'headOfficeId'           => $headOfficeId,
            'franchiseId'            => $franchiseId,
            'deviceId'               => $deviceId,
            'deviceSeq'              => $deviceSeq,
            'channelType'            => $channelType,
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
            'filler1'                => $filler1,
            'filler2'                => $filler2,
            'filler3'                => $filler3,
            'filler4'                => $filler4,
            'closeYn'                => $closeYn,
            'closeDate'              => $closeDate,
            'salesDaySeq'            => $salesDaySeq,
        );
		$this->db->trans_start();  
        //이부분에 쿼리 넣어 주시면 됩니다.
		$this->db->query($sp,$params); 
		//이부분에 쿼리 넣어 주시면 됩니다.

	    $this->db->trans_complete();   
		
		return $this->db->trans_status()? "0000" : -1;    
	}

    /*
	// insertDBorderProduct
    function insertDBorderProduct($receiveDetailarray) {
        global $db;
        foreach($receiveDetailarray as $k => $v) {
            if(isset($v['UnivCode'])) $UnivCode = trim($v['UnivCode']);      // 대학코드            s5
            $createdAt              = trim($v['createdAt']);                 // 등록일              s30
            $updatedAt              = trim($v['updatedAt']);                 // 수정일              s30
            $orderProductSeq        = $v['orderProductSeq'];                 // 주문상품순번        n
            $channelType            = trim($v['channelType']);               // 채널구분            s10  ch01:kiosk
            $billNo                 = trim($v['billNo']);                    // 영수번호            s30
            $saleDay                = $v['saleDay'];                         // 영업일              s10  YYYY-MM-DD
            $headOfficeId           = trim($v['headOfficeId']);              // 본사id              s30
            $franchiseId            = trim($v['franchiseId']);               // 가맹점id            s30
            $deviceId               = trim($v['deviceId']);                  // 기기id              s30 
            $tradeType              = trim($v['tradeType']);                 // 거래구분            s2   S:매출 C:취소
            $orderProductType       = trim($v['orderProductType ']);         // 주문상품구분        s255 P:영수(payment) C:쿠폰(coupon) F:사은품(free)
            if(isset($v['salesTarget'])) $salesTarget = trim($v['salesTarget']); // 서비스대상          s255 G:일반 S:직원
            $serviceType            = trim($v['serviceType']);               // 매장/포장           s255 S:매장 P:포장
            if(isset($v['mediaNo'])) $mediaNo = trim($v['mediaNo']);         // 미디어번호          s255 쿠폰일경우 쿠폰번호
            $categoryId             = trim($v['categoryId']);                // 카테고리아이디      s255
            $categoryName           = trim($v['categoryName']);              // 카테고리아이디      s255
            $categoryMgrName        = trim($v['categoryMgrName']);           // 카테고리관리명      s255
            if(isset($v['categoryExtrCd'])) $categoryExtrCd = trim($v['categoryExtrCd']); // 카테고리외부연동코드s20
            $productId              = trim($v['productId']);                 // 상품아이디          s255
            $productName            = trim($v['productName']);               // 상품명              s255
            $productMgrName         = trim($v['productMgrName']);            // 상품관리명          s255
            if(isset($v['extrCd'])) $extrCd = trim($v['extrCd']);            // 상품외부연동코드    s20
            if(isset($v['extr2Cd'])) $extr2Cd = trim($v['extr2Cd']);         // 상품외부연동코드2   s20
            if(isset($v['extr3Cd'])) $extr3Cd = trim($v['extr3Cd']);         // 상품외부연동코드3   s20
            $primeCost              = $v['primeCost'];                       // 상품원가            n
            $price                  = $v['price'];                           // 상품단가            n
            $taxAmount              = $v['taxAmount'];                       // 부가세              n
            $useTax                 = $v['useTax'];                          // 과세상품여부        b
            if(isset($v['baseSaleQty'])) $baseSaleQty = $v['baseSaleQty'];   // 기본수량            n
            $productQty             = $v['productQty'];                      // 주문상품수량        n
            $amount                 = $v['amount'];                          // 합계금액            n
            if(isset($v['orgBillNo'])) $orgBillNo = trim($v['orgBillNo']);   // 원거래영수번호      s255
            if(isset($v['itrPrinterAlias'])) $itrPrinterAlias = trim($v['itrPrinterAlias']); // 내부프린트정보      o
            if(isset($v['etrPrinterAlias'])) $etrPrinterAlias = trim($v['etrPrinterAlias']); // 외부프린트정보      o
            if(isset($v['productPrintName'])) $productPrintName = trim($v['productPrintName']); // 주방출력상품명      s255 있을때만 출력
            if(isset($v['outputQty'])) $outputQty = $v['outputQty'];         // 테켓출력수량        n
            $salesDaySeq            = $v['salesDaySeq'];                     // 영업일자순분        n
            if(isset($v['additionalInfo'])) $additionalInfo = $v['additionalInfo']; // 부가정보            s    JSON형식
            if(isset($v['filler1'])) $filler1 = trim($v['filler1']);         // 비고1               s500
            if(isset($v['filler2'])) $filler2 = trim($v['filler2']);         // 비고2               s500
            if(isset($v['filler3'])) $filler3 = trim($v['filler3']);         // 비고3               s500
            if(isset($v['filler4'])) $filler4 = trim($v['filler4']);         // 비고4               s500
        }
        $sp = "VENDINGM.dbo.SP_ITMS_ORDERPRODUCT;01 ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ";
        $params = array(
            'UnivCode'               => $UnivCode,
            'createdAt'              => $createdAt,
            'updatedAt'              => $updatedAt,
            'orderProductSeq'        => $orderProductSeq,
            'channelType'            => $channelType,
            'billNo'                 => $billNo,
            'saleDay'                => $saleDay,
            'headOfficeId'           => $headOfficeId,
            'franchiseId'            => $franchiseId,
            'deviceId'               => $deviceId,
            'tradeType'              => $tradeType,
            'orderProductType'       => $orderProductType,
            'salesTarget'            => $salesTarget,
            'serviceType'            => $serviceType,
            'mediaNo'                => $mediaNo,
            'categoryId'             => $categoryId,
            'categoryName'           => $categoryName,
            'categoryMgrName'        => $categoryMgrName,
            'categoryExtrCd'         => $categoryExtrCd,
            'productId'              => $productId,
            'productName'            => $productName,
            'productMgrName'         => $productMgrName,
            'extrCd'                 => $extrCd,
            'extr2Cd'                => $extr2Cd,
            'extr3Cd'                => $extr3Cd,
            'primeCost'              => $primeCost,
            'price'                  => $price,
            'taxAmount'              => $taxAmount,
            'useTax'                 => $useTax,
            'baseSaleQty'            => $baseSaleQty,
            'productQty'             => $productQty,
            'amount'                 => $amount,
            'orgBillNo'              => $orgBillNo,
            'itrPrinterAlias'        => $itrPrinterAlias,
            'etrPrinterAlias'        => $etrPrinterAlias,
            'productPrintName'       => $productPrintName,
            'outputQty'              => $outputQty,
            'salesDaySeq'            => $salesDaySeq,
            'additionalInfo'         => $additionalInfo,
            'filler1'                => $filler1,
            'filler2'                => $filler2,
            'filler3'                => $filler3,
            'filler4'                => $filler4,
        );
        $this->db->query($sp,$params); 
        return  $this->db->affected_rows();    
    }
    // insertDBorderProductOption
    function insertDBorderProductOption($receiveDetailarray) {
        global $db;
        foreach($receiveDetailarray as $k => $v) {
            if(isset($v['UnivCode'])) $UnivCode = trim($v['UnivCode']);      // 대학코드            s5
            $createdAt              = trim($v['createdAt']);                 // 등록일              s30
            $updatedAt              = trim($v['updatedAt']);                 // 수정일              s30
            $orderProductOptionSeq  = $v['orderProductOptionSeq'];           // 주문옵션순번        n
            $headOfficeId           = trim($v['headOfficeId']);              // 본사아이디          s30
            $franchiseId            = trim($v['franchiseId']);               // 가맹점아이디        s30
            $deviceId               = trim($v['deviceId']);                  // 기기아이디          s30
            $orderProductSeq        = $v['orderProductSeq'];                 // 주문상품순번        n
            $optionGroupId          = trim($v['optionGroupId']);             // 옵션그룹아이디      s255
            $OptionGroupExtrCd      = trim($v['OptionGroupExtrCd']);         // 옵션그룹외부연동코드s20
            $OptionGroupName        = trim($v['OptionGroupName']);           // 옵션그룹명          s255
            $optionGroupMgrName     = trim($v['optionGroupMgrName']);        // 옵션그룹관리명      s255
            $optionId               = trim($v['optionId']);                  // 옵션아이디          s255
            $optionName             = trim($v['optionName']);                // 옵션명              s255
            $productMgrName         = trim($v['productMgrName']);            // 옵션관리명          s255
            if(isset($v['optionPrintName'])) $optionPrintName = trim($v['optionPrintName']); // 옵션명출력명 s255
            if(isset($v['extrCd'])) $extrCd = trim($v['extrCd']);            // 외부연결코드1        s20
            if(isset($v['extr2Cd'])) $extr2Cd = trim($v['extr2Cd']);         // 외부연결코드2        s20
            if(isset($v['extr3Cd'])) $extr3Cd = trim($v['extr3Cd']);         // 외부연결코드3        s20
            $price                  = $v['price'];                           // 단가                 n
            $primeCost              = $v['primeCost'];                       // 원가                 n
            $taxAmount              = $v['taxAmount'];                       // 부가세               n
            $useTax                 = $v['useTax'];                          // 과세여부             s1 1:과세 0:면세
            if(isset($v['baseSaleQty'])) $baseSaleQty = $v['baseSaleQty'];   // 기분수량             n
            $productQty             = $v['productQty'];                      // 주문수량             n
            $amount                 = $v['amount'];                          // 주문금액             n
            if(isset($v['additionalInfo '])) $additionalInfo = trim($v['additionalInfo ']); // 부가정보 JSON
            if(isset($v['filler1'])) $filler1 = trim($v['filler1']);         // 비고1                s500
            if(isset($v['filler2'])) $filler2 = trim($v['filler2']);         // 비고2                s500
            if(isset($v['filler3'])) $filler3 = trim($v['filler3']);         // 비고3                s500
            if(isset($v['filler4'])) $filler4 = trim($v['filler4']);         // 비고4                s500
        }
        $sp = "VENDINGM.dbo.SP_ITMS_ORDERPRODUCTOPTION;01 ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ";
        $params = array(
            'UnivCode'               => $UnivCode,
            'createdAt'              => $createdAt,
            'updatedAt'              => $updatedAt,
            'orderProductOptionSeq'  => $orderProductOptionSeq,
            'headOfficeId'           => $headOfficeId,
            'franchiseId'            => $franchiseId,
            'deviceId'               => $deviceId,
            'orderProductSeq'        => $orderProductSeq,
            'optionGroupId'          => $optionGroupId,
            'OptionGroupExtrCd'      => $OptionGroupExtrCd,
            'OptionGroupName'        => $OptionGroupName,
            'optionGroupMgrName'     => $optionGroupMgrName,
            'optionId'               => $optionId,
            'optionName'             => $optionName,
            'productMgrName'         => $productMgrName,
            'optionPrintName'        => $optionPrintName,
            'extrCd'                 => $extrCd,
            'extr2Cd'                => $extr2Cd,
            'extr3Cd'                => $extr3Cd,
            'price'                  => $price,
            'primeCost'              => $primeCost,
            'taxAmount'              => $taxAmount,
            'useTax'                 => $useTax,
            'baseSaleQty'            => $baseSaleQty,
            'productQty'             => $productQty,
            'amount'                 => $amount,
            'additionalInfo'         => $additionalInfo,
            'filler1'                => $filler1,
            'filler2'                => $filler2,
            'filler3'                => $filler3,
            'filler4'                => $filler4,
        );
        $this->db->query($sp,$params); 
        return  $this->db->affected_rows();    
    }
    // insertDBpayment
    function insertDBpayment($receiveDetailarray) {
        global $db;
        foreach($receiveDetailarray as $k => $v) {
            if(isset($v['UnivCode'])) $UnivCode = trim($v['UnivCode']);      // 대학코드            s5
            $orgRegiDateTime        = trim($v['orgRegiDateTime']);           // 원거래등록일        s30
            $createdAt              = trim($v['createdAt']);                 // 등록일              s30
            $updatedAt              = trim($v['updatedAt']);                 // 수정일              s30
            $paymentSeq             = $v['paymentSeq'];                      // 결재순번            n
            $headOfficeId           = trim($v['headOfficeId']);              // 본사아이디          s255
            $franchiseId            = trim($v['franchiseId']);               // 가맹점아이디        s255
            $deviceId               = trim($v['deviceId']);                  // 기기아이디          s255
            $billNo                 = trim($v['billNo']);                    // 영수번호            s255
            $channelType            = trim($v['channelType']);               // 채널                s255 ch01:kiosk
            $paymentPlatform        = trim($v['paymentPlatform']) ;          // 결재플랫폼          s255 결재플랫폼 KIOSK, PAY:간편결재
            $paymentMethod          = trim($v['paymentMethod']);             // 결재방법            s255 CASH:현금 CASH_RECEIPT:현금영수증 CARD:카드 COUPON:쿠폰 POINT-USE:사용포인트 POINT-SAVE:적립포인트 PAYCO:페이코 KAKAOPAY:카카오페이
            $saleDay                = trim($v['saleDay']);                   // 영업일              s10
            $tradeType              = trim($v['tradeType']);                 // 거래타입            s255
            $moduleId               = trim($v['moduleId']);                  // 모듈아이디          s255
            $payAmount              = $v['payAmount'];                       // 결재금액            n
            $dutyAmount             = $v['dutyAmount'];                      // 면세금액            n
            $supplyAmount           = $v['supplyAmount'];                    // 공급가액            n
            $taxAmount              = $v['taxAmount'];                       // 부가세              n
            if(isset($v['mediaType'])) $mediaType = trim($v['mediaType']);   // 현금영수증매체타입  s255 INDIVIDUAL:개인 CORPORATION:법인
            if(isset($v['mediaNo'])) $mediaNo = trim($v['mediaNo']);         // 현금영수증매체번호  s255 (주민번호,핸드폰번호,사업자번호,카드번호)
            $appNo                  = trim($v['appNo']);                     // 승인번호            s255
            $appDate                = trim($v['appDate']);                   // 승인일자            s255
            if(isset($v['orgPaymentSeq'])) $orgPaymentSeq = $v['orgPaymentSeq']; // 원거래결재순번  n
            if(isset($v['orgAppNo'])) $orgAppNo = $v['orgAppNo'];            // 원거래승인번호      s255
            if(isset($v['orgAppDate'])) $orgAppDate = $v['orgAppDate'];      // 원거래승인일자      s255
            if(isset($v['orgBillNo'])) $orgBillNo = $v['orgBillNo'];         // 원거래영수번호      s255
            if(isset($v['approvalInfo'])) $approvalInfo = $v['approvalInfo'];// 승인정보            o
            if(isset($v['paymentStatus'])) $paymentStatus = $v['paymentStatus'];// 결재상태         s255 S:성공 F:실패
            $salesDaySeq            = $v['salesDaySeq'];                     // 영업일자순번        n
            if(isset($v['cashableAmount'])) $cashableAmount = $v['cashableAmount'];// 현금영수증 가용금액 n
            if(isset($v['cashInSeq'])) $cashInSeq = $v['cashInSeq'];         // 현금투입금순번      n
            if(isset($v['cashOutSeq'])) $cashOutSeq = $v['cashOutSeq'];      // 현금방출금순번      n
            if(isset($v['closeYn'])) $closeYn = trim($v['closeYn']);         // 마감여부            s255
            if(isset($v['closeDate'])) $closeDate = trim($v['closeDate']);   // 마감일시            s255
            if(isset($v['errorMessage'])) $errorMessage = trim($v['errorMessage']); // 에러메시지   t
            if(isset($v['filler1'])) $filler1 = trim($v['filler1']);         // 비고1                s500
            if(isset($v['filler2'])) $filler2 = trim($v['filler2']);         // 비고2                s500
            if(isset($v['filler3'])) $filler3 = trim($v['filler3']);         // 비고3                s500
            if(isset($v['filler4'])) $filler4 = trim($v['filler4']);         // 비고4                s500
        }
        $sp = "VENDINGM.dbo.SP_ITMS_PAYMENTS;01 ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?";
        $params = array(
            'UnivCode'               => $UnivCode,
            'orgRegiDateTime'        => $orgRegiDateTime,
            'createdAt'              => $createdAt,
            'updatedAt'              => $updatedAt,
            'paymentSeq'             => $paymentSeq,
            'headOfficeId'           => $headOfficeId,
            'franchiseId'            => $franchiseId,
            'deviceId'               => $deviceId,
            'billNo'                 => $billNo,
            'channelType'            => $channelType,
            'paymentPlatform'        => $paymentPlatform,
            'paymentMethod'          => $paymentMethod,
            'saleDay'                => $saleDay,
            'tradeType'              => $tradeType,
            'moduleId'               => $moduleId,
            'payAmount'              => $payAmount,
            'dutyAmount'             => $dutyAmount,
            'supplyAmount'           => $supplyAmount,
            'taxAmount'              => $taxAmount,
            'mediaType'              => $mediaType,
            'mediaNo'                => $mediaNo,
            'appNo'                  => $appNo,
            'appDate'                => $appDate,
            'orgPaymentSeq'          => $orgPaymentSeq,
            'orgAppNo'               => $orgAppNo,
            'orgAppDate'             => $orgAppDate,
            'orgBillNo'              => $orgBillNo,
            'approvalInfo'           => $approvalInfo,
            'paymentStatus'          => $paymentStatus,
            'salesDaySeq'            => $salesDaySeq,
            'cashableAmount'         => $cashableAmount,
            'cashInSeq'              => $cashInSeq,
            'cashOutSeq'             => $cashOutSeq,
            'closeYn'                => $closeYn,
            'closeDate'              => $closeDate,
            'errorMessage'           => $errorMessage,
            'filler1'                => $filler1,
            'filler2'                => $filler2,
            'filler3'                => $filler3,
            'filler4'                => $filler4,
        );
        $this->db->query($sp,$params); 
        return  $this->db->affected_rows();    
    }
    // insertDBcardPaymentdetail
    function insertDBcardPaymentdetail($receiveDetailarray) {
        global $db;
        foreach($receiveDetailarray as $k => $v) {
            if(isset($v['UnivCode'])) $UnivCode = trim($v['UnivCode']);      // 대학코드            s5
            $createdAt              = trim($v['createdAt']);                 // 등록일              s30
            $updatedAt              = trim($v['updatedAt']);                 // 수정일              s30
            $paymentSeq             = $v['paymentSeq'];                      // 결재순번            n
            $billNo                 = trim($v['billNo']);                    // 영수번호            s255
            if(isset($v['vanCd'])) $vanCd = trim($v['vanCd']);               // 밴코드              s255
            $issueCd                = trim($v['issueCd']);                   // 발급사코드          s255
            $issueName              = trim($v['issueName']);                 // 발급사명            s255
            $acquirerCd             = trim($v['acquirerCd']);                // 매입사코드          s255            
            $acquirerName           = trim($v['acquirerName']);              // 매입사명            s255
            $storeNo                = trim($v['storeNo']);                   // 가맹점번호          s255
            $installment            = trim($v['installment']);               // 할부개월            s255
            $cardNo                 = trim($v['cardNo']);                    // 카드번호            s255
            if(isset($v['additionalInfo'])) $additionalInfo = trim($v['additionalInfo']); // 부가정보 t JSON
            if(isset($v['filler1'])) $filler1 = trim($v['filler1']);         // 비고1                s500
            if(isset($v['filler2'])) $filler2 = trim($v['filler2']);         // 비고2                s500
            if(isset($v['filler3'])) $filler3 = trim($v['filler3']);         // 비고3                s500
        }
        $sp = "VENDINGM.dbo.SP_ITMS_CARDPAYMENTSDETAIL;01 ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?";
        $params = array(
            'UnivCode'               => $UnivCode,
            'createdAt'              => $createdAt,
            'updatedAt'              => $updatedAt,
            'paymentSeq'             => $paymentSeq,
            'billNo'                 => $billNo,
            'vanCd'                  => $vanCd,
            'issueCd'                => $issueCd,
            'issueName'              => $issueName,
            'acquirerCd'             => $acquirerCd,
            'acquirerName'           => $acquirerName,
            'storeNo'                => $storeNo,
            'installment'            => $installment,
            'cardNo'                 => $cardNo,
            'additionalInfo'         => $additionalInfo,
            'filler1'                => $filler1,
            'filler2'                => $filler2,
            'filler3'                => $filler3,
        );
        $this->db->query($sp,$params); 
        return  $this->db->affected_rows();    
    }

    // insertDBcouponPaymentdetail
    function insertDBcouponPaymentdetail($receiveDetailarray) {
        global $db;
        foreach($receiveDetailarray as $k => $v) {
            if(isset($v['UnivCode'])) $UnivCode = trim($v['UnivCode']);      // 대학코드            s5
            $createdAt              = trim($v['createdAt']);                 // 등록일              s30
            $updatedAt              = trim($v['updatedAt']);                 // 수정일              s30
            $paymentSeq             = $v['paymentSeq'];                      // 결재순번            n
            $billNo                 = trim($v['billNo']);                    // 영수번호            s255
            $affiliateCd            = trim($v['affiliateCd']);               // 제휴사코드          s255 OMNITEL:옵니텔 PAYCO:페이코 ZLGOON:즐거운 PAYS:페이즈 OKCASHBAG:오케이캐쉬백 TOUCHING:터칭포인트
            if(isset($v['couponType'])) $couponType = trim($v['couponType']); // 쿠폰구분           s255 EXCHANGE:교환권 AMOUNT:금액권 POINT:포인트(SK,OKCASHBAK,단골,터칭)
            if(isset($v['couponExplanation'])) $couponExplanation = trim($v['couponExplanation']); // 쿠폰설명 s255
            if(isset($v['mediaNo'])) $mediaNo = trim($v['mediaNo']);          // 인증코드           s255
            if(isset($v['usePoint'])) $usePoint = $v['usePoint'];             // 사용포인트         n
            if(isset($v['occurPoint'])) $occurPoint = $v['occurPoint'];       // 발생포인트         n
            if(isset($v['remainPoint'])) $remainPoint = $v['remainPoint'];    // 남은포인트         n
            if(isset($v['availablePoint'])) $availablePoint = $v['availablePoint']; // 유효포인트   n
            if(isset($v['additionalInfo'])) $additionalInfo = trim($v['additionalInfo']); // 부가정보 t JSON
            if(isset($v['filler1'])) $filler1 = trim($v['filler1']);         // 비고1                s500
            if(isset($v['filler2'])) $filler2 = trim($v['filler2']);         // 비고2                s500
            if(isset($v['filler3'])) $filler3 = trim($v['filler3']);         // 비고3                s500
        }
        $sp = "VENDINGM.dbo.SP_ITMS_COUPONPAYMENTSDETAIL;01 ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?";
        $params = array(
            'UnivCode'               => $UnivCode,
            'createdAt'              => $createdAt,
            'updatedAt'              => $updatedAt,
            'paymentSeq'             => $paymentSeq,
            'billNo'                 => $billNo,
            'affiliateCd'            => $affiliateCd,
            'couponType'             => $couponType,
            'couponExplanation'      => $couponExplanation,
            'mediaNo'                => $mediaNo,
            'usePoint'               => $usePoint,
            'occurPoint'             => $occurPoint,
            'remainPoint'            => $remainPoint,
            'availablePoint'         => $availablePoint,
            'additionalInfo'         => $additionalInfo,
            'filler1'                => $filler1,
            'filler2'                => $filler2,
            'filler3'                => $filler3,
        );
        $this->db->query($sp,$params); 
        return  $this->db->affected_rows();    
    }     */

    // insertOrderTest
    function insertOrderTest($order) {
    
        global $db;  
        //$CI =& get_instance();
		//print_r($db);
		//exit;
        
        $univcode               = $order['order']['univcode'];                  // 대학코드*           s5
        $createdAt              = trim($order['order']['createdAt']);           // 등록일              s30
        $updatedAt              = trim($order['order']['updatedAt']);           // 수정일              s30
        $billNo                 = trim($order['order']['billNo']);              // 영수번호*           s30
        $storeCode              = trim($order['order']['franchiseCd']);         // 지점코드            s30  연동처리
        $posNo                  = trim($order['order']['posNo']);               // 포스번호*           s5   연동 기기번호
        $saleDay                = $order['order']['saleDay'];                   // 영업일*             s10  YYYY-MM-DD
        $salesDaySeq            = $order['order']['salesDaySeq'];               // 영업일자순분        n

        //{$db['default']['database']}.dbo.
        //$sp = "VENDINGM.dbo.SP_ITMS_ORDER;01 ?, ?, ?, ?, ?, ?, ?, ? ";

        $params = array(
            'univcode'               => $univcode,
            'saleDay'                => $saleDay,
            'storeCode'              => $storeCode,
            'posNo'                  => $posNo,
            'billNo'                 => $billNo,
            'createdAt'              => $createdAt,
            'updatedAt'              => $updatedAt,
            'salesDaySeq'            => $salesDaySeq,
        );
		//$this->db->trans_start();  
        //이부분에 쿼리 넣어 주시면 됩니다.
		//$this->db->query($sp,$params); 
		//이부분에 쿼리 넣어 주시면 됩니다.

	    //$this->db->trans_complete();   
		
		return $this->db->trans_status()? "0000" : -1;
    }

}