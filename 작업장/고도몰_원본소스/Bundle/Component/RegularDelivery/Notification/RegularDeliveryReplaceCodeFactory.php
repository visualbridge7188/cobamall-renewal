<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\Notification;


class RegularDeliveryReplaceCodeFactory
{
    // 마이페이지 > 정기배송 관리 > 정기배송 신청 관리 페이지
    const MYPAGE_SUB_VIEW_URL = 'mypage/regular_delivery.php?type=regularOrder';
    // 마이페이지 > 정기배송 관리 >  결제카드 관리 페이지
    const MYPAGE_AUTO_CARD_URL = 'mypage/regular_delivery.php?type=paymentCard';
    // 마이페이지 > 주문 상세 정보 페이지
    const MYPAGE_ORDER_VIEW_URL = 'mypage/order_view.php?orderNo=';
    // 프론트 고객센터 페이지
    const SERVICE_VIEW_URL = 'service/index.php';
    // 관리자 - 정기배송 신청 리스트
    const ADMIN_SUB_LIST_URL = 'order/regular_order_list.php';
    // 관리자 - 결제완료 리스트
    const ADMIN_PAY_LIST_URL = 'order/order_list_pay.php';
    // 관리자 - 주문 상세 정보 페이지
    const ADMIN_ORDER_INFO_URL = 'order/order_view.php?orderNo=';

    /**
     * 정기배송 일시정지용 치환코드 데이터 생성
     * @param array $mallInfo
     * @param array $receiverInfo
     * @param array $applyGoodsInfo
     * @return array
     */
    public static function makePauseData(array $mallInfo, array $receiverInfo, array $applyGoodsInfo): array
    {
        return [
            'rc_mallNm' => $mallInfo['mallNm'],
            'subName' => $receiverInfo['applierName'],
            'shopUrl' => $mallInfo['mallDomain'],
            'subList' => self::getDomainUrl('admin') . self::ADMIN_SUB_LIST_URL,
            'subView' => self::getDomainUrl('mobile') . self::MYPAGE_SUB_VIEW_URL,
            'subGoodsNm' => $applyGoodsInfo['subGoodsNm']
        ];
    }

    /**
     * 정기배송 일시정지 해제용 치환코드 데이터 생성
     * @param array $mallInfo
     * @param array $receiverInfo
     * @param array $applyGoodsInfo
     * @return array
     */
    public static function makeResumeData(array $mallInfo, array $receiverInfo, array $applyGoodsInfo): array
    {
        return [
            'rc_mallNm' => $mallInfo['mallNm'],
            'subName' => $receiverInfo['applierName'],
            'shopUrl' => $mallInfo['mallDomain'],
            'subList' => self::getDomainUrl('admin') . self::ADMIN_SUB_LIST_URL,
            'subView' => self::getDomainUrl('mobile') . self::MYPAGE_SUB_VIEW_URL,
            'subDeliveryDate' => $applyGoodsInfo['deliveryDueDate'],
            'subPayRound' => $applyGoodsInfo['deliveryRound'] . '회차',
            'subGoodsNm' => $applyGoodsInfo['subGoodsNm'],
            'cardName' => $applyGoodsInfo['cardName'],
            'cardNo' => $applyGoodsInfo['cardNo']
        ];
    }

    /**
     * 회차 건너뛰기용 치환코드 데이터 생성
     * @param array $mallInfo
     * @param array $receiverInfo
     * @param array $applyGoodsInfo
     * @return array
     */
    public static function makeSkipDeliveryRoundData(array $mallInfo, array $receiverInfo, array $applyGoodsInfo): array
    {
        return [
            'rc_mallNm' => $mallInfo['mallNm'],
            'subName' => $receiverInfo['applierName'],
            'shopUrl' => $mallInfo['mallDomain'],
            'subList' => self::getDomainUrl('admin') . self::ADMIN_SUB_LIST_URL,
            'subView' => self::getDomainUrl('mobile') . self::MYPAGE_SUB_VIEW_URL,
            'subGoodsNm' => $applyGoodsInfo['subGoodsNm'],
            'subPayRound' => $applyGoodsInfo['deliveryRound'] . '회차',
            'subDeliveryDate' => $applyGoodsInfo['deliveryDueDate']
        ];
    }

    /**
     * 해지용 치환코드 데이터 생성
     * @param array $mallInfo
     * @param array $receiverInfo
     * @param array $applyGoodsInfo
     * @return array
     */
    public static function makeCancelData(array $mallInfo, array $receiverInfo, array $applyGoodsInfo): array
    {
        return [
            'rc_mallNm' => $mallInfo['mallNm'],
            'subName' => $receiverInfo['applierName'],
            'shopUrl' => $mallInfo['mallDomain'],
            'subList' => self::getDomainUrl('admin') . self::ADMIN_SUB_LIST_URL,
            'subGoodsNm' => $applyGoodsInfo['subGoodsNm'],
            'cancelReason' => $applyGoodsInfo['cancelReason']
        ];
    }

    /**
     * 결제 예정 시 발송 데이터 생성
     * @param array $mallInfo
     * @param array $receiverInfo
     * @param array $regularOrderInfo
     * @return array
     */
    public static function makePaymentScheduledData(array $mallInfo, array $receiverInfo, array $regularOrderInfo): array
    {
        return [
            'rc_mallNm' => $mallInfo['mallNm'],
            'subName' => $receiverInfo['applierName'],
            'autoPayDate' => $regularOrderInfo['orderCreateDate'],
            'cardName' => $regularOrderInfo['cardName'],
            'cardNo' => $regularOrderInfo['cardNo'],
            'shopUrl' => $mallInfo['mallDomain'],
            'subList' => self::getDomainUrl('admin') . self::ADMIN_SUB_LIST_URL,
            'subView' => self::getDomainUrl('mobile') . self::MYPAGE_SUB_VIEW_URL,
            'subGoodsNm' => $regularOrderInfo['subGoodsNm']
        ];
    }
    /**
     * 결제 실패용 치환코드 데이터 생성
     * @param array $mallInfo
     * @param array $receiverInfo
     * @param array $regularOrderInfo
     * @return array
     */
    public static function makePaymentFailData(array $mallInfo, array $receiverInfo, array $regularOrderInfo): array
    {
        return [
            'rc_mallNm' => $mallInfo['mallNm'],
            'subName' => $receiverInfo['applierName'],
            'shopUrl' => $mallInfo['mallDomain'],
            'subView' => self::getDomainUrl('mobile') . self::MYPAGE_SUB_VIEW_URL,
            'autoCard' => self::getDomainUrl('mobile') . self::MYPAGE_AUTO_CARD_URL,
            'autoPayDate' => $regularOrderInfo['orderCreateDate'],
            'cardName' => $regularOrderInfo['cardName'],
            'cardNo' => $regularOrderInfo['cardNo'],
            'subGoodsNm' => $regularOrderInfo['subGoodsNm']
        ];
    }

    /**
     * 신청 완료용 치환코드 데이터 생성
     * @param array $mallInfo
     * @param array $receiverInfo
     * @param array $regularApplyInfo
     * @return array
     */
    public static function makeApplyCompleteData(array $mallInfo, array $receiverInfo, array $regularApplyInfo): array
    {
        return [
            'rc_mallNm' => $mallInfo['mallNm'],
            'subName' => $receiverInfo['applierName'],
            'shopUrl' => $mallInfo['mallDomain'],
            'subList' => self::getDomainUrl('admin') . self::ADMIN_SUB_LIST_URL,
            'subView' => self::getDomainUrl('mobile') . self::MYPAGE_SUB_VIEW_URL,
            'subDeliveryCycle' => $regularApplyInfo['deliveryCycle'],
            'subAddress' => mb_substr($regularApplyInfo['deliveryAddress'], 0, 23, 'UTF-8'),
            'cardName' => $regularApplyInfo['cardName'],
            'cardNo' => $regularApplyInfo['cardNo'],
            'subGoodsNm' => $regularApplyInfo['goodsNm'],
            'subDeliveryDate' => $regularApplyInfo['deliveryDueDate'],
            'endPayRound' => $regularApplyInfo['endPayRound'] == 0 ? '무제한' : $regularApplyInfo['endPayRound'] . '회차'
        ];
    }

    /**
     * 결제 완료용 치환코드 데이터 생성
     * @param array $mallInfo
     * @param array $receiverInfo
     * @param array $regularOrderInfo
     * @return array
     */
    public static function makePaymentCompleteData(array $mallInfo, array $receiverInfo, array $regularOrderInfo): array
    {
        return [
            'rc_mallNm' => $mallInfo['mallNm'],
            'subName' => $receiverInfo['memNm'],
            'subPayRound' => $regularOrderInfo['deliveryRound'] . '회차',
            'subDeliveryDate' => $regularOrderInfo['deliveryDueDate'],
            'shopUrl' => $mallInfo['mallDomain'],
            'orderNo' => $regularOrderInfo['orderNo'],
            'subGoodsNm' => $regularOrderInfo['subGoodsNm'],
            'settlePrice' => gd_currency_display((float)$regularOrderInfo['totalPrice']),
            'payList' => self::getDomainUrl('admin') . self::ADMIN_PAY_LIST_URL,
            'subView' => self::getDomainUrl('mobile') . self::MYPAGE_SUB_VIEW_URL,
            'orderView' => self::getDomainUrl('mobile') . self::MYPAGE_ORDER_VIEW_URL . $regularOrderInfo['orderNo'], 
        ];
    }

    /**
     * 정기배송 안내용 치환코드 데이터 생성
     * @param array $mallInfo
     * @param array $orderData
     * @param array $orderGoodsInfo
     * @return array
     */
    public static function makeRegularDeliveryNoticeData(array $mallInfo, array $orderData, array $orderGoodsInfo): array
    {
        return [
            'rc_mallNm' => $mallInfo['mallNm'],
            'subName' => $orderData['orderName'],
            'shopUrl' => $mallInfo['mallDomain'],
            'orderInfo' => self::getDomainUrl('admin') . self::ADMIN_ORDER_INFO_URL . $orderData['orderNo'],
            'orderView' => self::getDomainUrl('mobile') . self::MYPAGE_ORDER_VIEW_URL . $orderData['orderNo'], 
            'subPayRound' => $orderData['deliveryRound'] . '회차',
            'orderNo' => $orderData['orderNo'],
            'subGoodsNm' => $orderGoodsInfo['goodsNm'],
            'deliveryName' => $orderGoodsInfo['deliveryName'],
            'invoiceNo' => $orderGoodsInfo['invoiceNo']
        ];
    }

    /**
     * 정기배송 일정 변경 시 발송 데이터 생성 후 발송
     * @param array $changeData
     * @return array
     */
    public static function makeRegularDeliveryInfoChangeData(array $mallInfo, array $changeData): array
    {   
        return [
            'rc_mallNm' => $mallInfo['mallNm'],
            'subName' => $changeData['applierName'],
            'shopUrl' => $mallInfo['mallDomain'],
            'subList' => self::getDomainUrl('admin') . self::ADMIN_SUB_LIST_URL,
            'subView' => self::getDomainUrl('mobile') . self::MYPAGE_SUB_VIEW_URL,
            'subDeliveryCycle' => $changeData['deliveryCycle'],
            'subDeliveryCycleDay' => $changeData['deliveryCycleDay'],
            'subDeliveryDate' => $changeData['deliveryDueDate'],
            'subGoodsNm' => $changeData['regularGoodsNm'],
            'subPayRound' => $changeData['deliveryRound'] . '회차',
            'endPayRound' => $changeData['maxDeliveryRound'] == 0 ? '무제한' : $changeData['maxDeliveryRound'] . '회차'
        ];
    }

    /**
     * 이메일 발송용 기본정보 치환코드 데이터 생성
     * @param array $mallInfo
     * @param array $receiverInfo
     * @return array
     */
    public static function makeBasicInfoForMail(array $mallInfo, array $receiverInfo): array
    {
        return [
            'rc_mallNm' => $mallInfo['mallNm'],
            'memNm' => $receiverInfo['applierName'],
            'email' => $receiverInfo['applierEmail'],
            'subView' => DS . self::MYPAGE_SUB_VIEW_URL,
            'serviceView' => DS . self::SERVICE_VIEW_URL,
        ];
    }

    /**
     * 이메일 발송용 신청내역 치환코드 데이터 생성
     * @param array $receiverInfo
     * @param string $regDt
     * @param array $cardInfo
     * @return array
     */
    public static function makeApplyInfoForMail(array $receiverInfo, string $regDt, array $cardInfo): array
    {   
        return [
            'subName' => $receiverInfo['memNm'],
            'subDt' => $regDt,
            'cardName' => $cardInfo['cardName'] ?? '',
            'cardNo' => $cardInfo['cardLastNum'] ?? ''
        ];
    }
    
    /**
     * 이메일 발송용 상품 정보 치환코드 데이터 생성
     * @param array $goodsInfo
     * @return array
     */
    public static function makeGoodsInfoForMail(array $goodsInfo): array
    {
        return [
            'subGoodsNm' => $goodsInfo['regularGoodsNm'],
            'subGoodsCnt' => $goodsInfo['regularGoodsCnt'],
            'subGoodsPrice' => (float)$goodsInfo['regularGoodsPrice'],
            'subDeliveryCycle' => $goodsInfo['deliveryCycle'],
            'subDeliveryCycleDay' => $goodsInfo['deliveryCycleDay'],
            'subDeliveryDate' => $goodsInfo['deliveryDueDate'],
            'maxDeliveryRound' => $goodsInfo['maxDeliveryRound'],
            'totalGoodsPrice' => (float)$goodsInfo['totalGoodsPrice'],
            'subOptionInfo' => $goodsInfo['optionInfo'],
            'subOptionTextInfo' => $goodsInfo['optionTextInfo'],
            'subAddGoodsInfo' => $goodsInfo['addGoodsInfo'],
            'rowSpan' => $goodsInfo['rowSpan']
        ];
    }

    /**
     * 이메일 발송용 신청그룹별 상품 리스트 정보 치환코드 데이터 생성
     * @param array $goodsInfo
     * @return array 
     */
    public static function makeGoodsListInfoForMail(array $goodsListInfo): array
    {
        return [
            'subGoodsListInfo' => $goodsListInfo,
            'totalGoodsPrice' => (float)$goodsListInfo['totalGoodsPrice'],
            'totalDeliveryCharge' => (float)$goodsListInfo['totalDeliveryCharge'],
            'settlePrice' => (float)$goodsListInfo['settlePrice']
        ];
    }

    /**
     * 이메일 발송용 주문 생성 기준 상품 리스트 정보 치환코드 데이터 생성
     * @param array $goodsListInfo
     * @param array $priceInfo
     * @return array 
     */
    public static function makeGoodsListInfoByOrderForMail(array $goodsListInfo, array $priceInfo): array
    {
        return [
            'subGoodsListInfo' => $goodsListInfo,
            'totalGoodsPrice' => (float)$priceInfo['totalGoodsPrice'],
            'totalDeliveryCharge' => (float)$priceInfo['totalDeliveryCharge'],
            'settlePrice' => (float)$priceInfo['settlePrice']
        ];
    }

    /**
     * 이메일 발송용 회차 건너뛰기 정보 치환코드 데이터 생성
     * @param int $skipDeliveryRound
     * @param string $skipDeliveryDate
     * @return array
     */
    public static function makeSkipDeliveryRoundInfoForMail(int $skipDeliveryRound, string $skipDeliveryDate): array
    {
        return [
            'nextPayRound' => $skipDeliveryRound == 0 ? '무제한' : $skipDeliveryRound . '회차',
            'nextDeliveryDate' => $skipDeliveryDate
        ];
    }

    /**
     * 이메일 발송용 결제 완료용 치환코드 데이터 생성
     * @param array $receiverInfo
     * @param array $regularOrderInfo
     * @return array
     */
    public static function makePaymentCompleteDataForMail(array $receiverInfo, array $regularOrderInfo): array
    {
        return [
            'subView' => self::getDomainUrl() . self::MYPAGE_SUB_VIEW_URL,
            'orderNo' => $receiverInfo['orderNo'],
            'orderName' => $receiverInfo['orderName'],
            'subDeliveryDate' => $regularOrderInfo['deliveryDueDate'],
        ];
    }

    /**
     * 이메일 발송용 신청정보에 대한 사은품 정보 치환코드 데이터 생성
     * @param array $applyGiftInfo
     * @return array
     */
    public static function makeApplyGiftInfoForMail(array $applyGiftInfo): array
    {
        return [
            'applyGiftInfo' => $applyGiftInfo       
        ];
    }

    /**
     * 배송정보 치환코드 데이터 생성
     * @param array $deliveryInfo
     * @return array
     */
    public static function makeShippingInfoForMail(array $shippingInfo, $deliveryRound): array
    {
        return [
            'subPayRound' => $deliveryRound . '회차',
            'shippingName' => $shippingInfo['shippingName'],
            'shippingZonecode' => $shippingInfo['shippingZonecode'],
            'shippingAddress' => $shippingInfo['shippingAddress'] . ' ' . $shippingInfo['shippingAddressSub'],
            'shippingMessage' => $shippingInfo['shippingMessage'],
            'shippingPhone' => $shippingInfo['shippingPhone'],
            'shippingCellPhone' => $shippingInfo['shippingCellPhone'],
        ];
    }

    /**
     * 이메일 발송용 자동 결제 정보 치환코드 데이터 생성
     * @param array $regularOrderInfo
     * @return array
     */
    public static function makeAutoPaymentInfoForMail(array $regularOrderInfo): array
    {
        return [
            'autoPayDate' => $regularOrderInfo['orderCreateDate'],
        ];
    }

    /**
     * 이메일 발송용 정기배송 안내 치환코드 데이터 생성
     * @param array $mallInfo
     * @param array $orderData
     * @param array $regularOrderGoodsInfo
     * @return array
     */
    public static function makeRegularDeliveryNoticeMail(array $mallInfo, array $orderData, array $regularOrderGoodsInfo): array
    {
        return [
            'rc_mallNm' => $mallInfo['mallNm'],
            'shopUrl' => $mallInfo['mallDomain'],
            'email' => $orderData['orderEmail'],
            'memNm' => $orderData['orderName'],
            'orderNo' => $orderData['orderNo'],
            'settlePrice' => (float)$orderData['settlePrice'],
            'goods' => $orderData['goods'],
            'gift' => $orderData['gift'],
            'totalGoodsPrice' => (float)$orderData['totalGoodsPrice'],
            'totalDeliveryCharge' => (float)$orderData['totalDeliveryCharge'],
            'receiverNm' => $orderData['receiverName'],
            'receiverZipcode' => $orderData['receiverZipcode'],
            'receiverZonecode' => $orderData['receiverZonecode'],
            'receiverAddress' => $orderData['receiverAddress'],
            'receiverAddressSub' => $orderData['receiverAddressSub'],
            'receiverPhone' => $orderData['receiverPhone'],
            'receiverCellPhone' => $orderData['receiverCellPhone'],
            'receiverMemo' => $orderData['receiverMemo'],
            'deliveryRound' => $regularOrderGoodsInfo['deliveryRound'],
            'deliveryDueDate' => $regularOrderGoodsInfo['deliveryDate']
        ];
    }

    /**
     * 이메일 발송용 정기배송 일정 변경 치환코드 데이터 생성
     * @param array $applyGoodsInfo
     * @return array
     */
    public static function makeRegularDeliveryInfoChangeMail(array $changeData): array
    {
        return [
            'endPayRound' => $changeData['maxDeliveryRound'] == 0 ? '무제한' : $changeData['maxDeliveryRound'] . '회차'
        ];
    }

    /**
     * 도메인 URL 조회 (모바일/관리자/홈)
     * @param string|null $type 'mobile', 'admin' 또는 null
     * @return string
     */
    public static function getDomainUrl(string $type = null): string
    {
        $request = \App::getInstance('request');

        $domainUrl = '';
        // 타입이 지정되지 않은 경우 모바일 기기인 경우 모바일 도메인 처리
        if ($type === null) {
            if ($request->isMobile()) { // 모바일 기기인 경우
                $type = 'mobile';
            } else {
                $domainUrl = URI_HOME; // PC인 경우 홈으로
            }
        }

        // 타입이 지정된 경우 (mobile 또는 admin)
        // 기본 도메인이 아닌 경우(=대표 도메인인 경우)
        if (!$request->isBasicDomain($request->getServerName())) {
            $domainUrl = $type === 'mobile' ? URI_MOBILE : URI_ADMIN;
        }

        // 하이픈 도메인인 경우
        if ($request->isHyphenSubdomain()) {
            $domainUrl = $type === 'mobile' ? URI_MOBILE : URI_ADMIN;
        }

        // 하이픈 도메인이 아닌 경우 하이픈 prefix 도메인 처리
        $usableDomain = HYPHEN_DOMAIN_USEABLE_LIST;
        $defaultDomain = $request->getDefaultHost();
        $domainUrl = $request->getDomainUrl($usableDomain[$type] . $defaultDomain) . DS;
        
        // http:// 또는 https:// 제거
        $domain = preg_replace('/^https?:\/\//', '', $domainUrl);
        
        return $domain ?? '';
    }
}
