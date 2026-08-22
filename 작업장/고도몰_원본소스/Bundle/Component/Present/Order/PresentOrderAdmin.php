<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\Present\Order;

use Framework\Log\Logger;
use Illuminate\Database\Capsule\Manager as CapsuleManager;
use Repository\Order\OrderInfoRepository;
use Repository\Present\Order\PresentReceiverInfoRepository;
use Component\Present\PresentDAO;
use Component\Member\MemberMasking;
use DTO\Present\ReceiverInfoUpdateDTO;

class PresentOrderAdmin
{
    // 배송 메모 옵션
    public const DELIVERY_MEMO_LEAVE_AT_DOOR = '문 앞에 놓아주세요';
    public const DELIVERY_MEMO_CONTACT_IF_ABSENT = '부재시 연락 부탁드려요';
    public const DELIVERY_MEMO_CONTACT_BEFORE_DELIVERY = '배송 전 미리 연락해 주세요';

    public function __construct(
        private PresentReceiverInfoRepository $presentReceiverInfoRepository,
        private OrderInfoRepository $orderInfoRepository,
        private CapsuleManager $dbManager,
        private Logger $logger,
        private PresentDAO $presentDAO,
        private MemberMasking $memberMasking
    ) {}

    /**
     * 선물하기 수령자 정보 리스트 조회
     *
     * @param string $orderNo
     * @return array
     */
    public function getPresentReceiverList(string $orderNo): array
    {
        // 수령자 정보 조회
        $receiverList = $this->presentReceiverInfoRepository->findPresentReceiverListByOrderNo($orderNo);
        
        // 수령자 정보 포맷팅
        $formattedList = [];
        foreach ($receiverList as $receiver) {
            $formattedList[] = $this->formatReceiverInfo($receiver);
        }
        
        return $formattedList;
    }

    /**
     * 수령자 정보 포맷팅
     *
     * @param array $receiver
     * @return array
     */
    protected function formatReceiverInfo(array $receiver): array
    {
        $receiver['orderGoodsNo'] = $receiver['orderGoodsNo'] ?? '-';
        $receiver['receiverName'] = $receiver['receiverName'] ?? '';
        $receiver['phone'] = $receiver['phone'] ?? '';
        $receiver['cellPhone'] = $receiver['cellPhone'] ?? '';
        $receiver['zipcode'] = $receiver['zipcode'] ?? '';
        $receiver['address'] = $receiver['address'] ?? '';
        $receiver['addressSub'] = $receiver['addressSub'] ?? '';
        $receiver['acceptFl'] = $receiver['acceptFl'] ?? 'r';
        $receiver['sendSmsFl'] = $receiver['sendSmsFl'] ?? 'n';
        
        // 주소 포맷팅
        $zipcode = $receiver['zipcode'];
        $address = $receiver['address'];
        $addressSub = $receiver['addressSub'];
        
        if (empty($zipcode) && empty($address) && empty($addressSub)) {
            $receiver['formattedAddress'] = '주소 입력 전';
        } else {
            $receiver['formattedAddress'] = '[' . $zipcode . '] ' . $address . ' ' . $addressSub;
        }

        // 전화번호 포맷팅
        $receiver['formattedPhone'] = !empty($receiver['phone']) ? $receiver['phone'] : '-';
        $receiver['formattedCellPhone'] = !empty($receiver['cellPhone']) ? $receiver['cellPhone'] : '-';

        return $receiver;
    }

    /**
     * 선물하기 수령자 정보 수정
     * es_presentReceiverInfo와 es_orderInfo 테이블 모두 업데이트
     *
     * @param string $orderNo
     * @param array $receiverData
     * @return void
     * @throws \InvalidArgumentException 
     * @throws \Throwable
     */
    public function updateReceiverInfo(string $orderNo, array $receiverData, bool $withNoFilter = false): void
    {
        if (empty($orderNo) || empty($receiverData) || empty($receiverData['sno'])) {
            throw new \InvalidArgumentException('필수 파라미터가 없습니다.');
        }
        
        $dto = new ReceiverInfoUpdateDTO([
            'receiverName' => $receiverData['receiverName'] ?? '',
            'phone' => $receiverData['phone'] ?? '',
            'cellPhone' => $receiverData['cellPhone'] ?? '',
            'zipcode' => $receiverData['zipcode'] ?? '',
            'zonecode' => $receiverData['zipcode'] ?? '',
            'address' => $receiverData['address'] ?? '',
            'addressSub' => $receiverData['addressSub'] ?? '',
            'orderMemo' => $receiverData['orderMemo'] ?? '',
            'acceptFl' => $receiverData['acceptFl'] ?? '',
        ]);
        
        try {
            $this->dbManager->getConnection()->beginTransaction();
            
            // 1. es_orderInfo 테이블 업데이트
            $this->orderInfoRepository->updateReceiverInfo($orderNo, $dto->toOrderInfoArray(withNoFilter: $withNoFilter));

            // 2. es_presentReceiverInfo 테이블 업데이트
            $this->presentReceiverInfoRepository->updateReceiverInfo($orderNo, $dto->toPresentReceiverInfoArray(withNoFilter: $withNoFilter));

            $this->dbManager->getConnection()->commit();
        } catch (\Throwable $e) {
            $this->dbManager->getConnection()->rollBack();
            
            $this->logger->channel('presentOrder')->warning(
                __METHOD__ . ', ' . $e->getFile() . '[' . $e->getLine() . '], ' . $e->getMessage(),
                [
                    'orderNo' => $orderNo,
                    'receiverData' => $receiverData,
                    'trace' => $e->getTrace()
                ]
            );
            
            throw $e;
        }
    }

    /**
     * 선물하기 주문인 경우 주문자 정보 수정 시 es_presentReceiverInfo 테이블도 업데이트
     *
     * @param string $orderNo 주문번호
     * @param array $orderInfoData updateOrderOrderInfo에서 전달받은 info 데이터 (수령자 정보 포함)
     * @param int $orderGoodsNo 주문상품번호 (필수)
     * @return void
     * @throws \InvalidArgumentException orderGoodsNo가 없거나 유효하지 않은 경우
     */
    public function updatePresentReceiverInfoByOrderInfo(string $orderNo, array $orderInfoData, int $orderGoodsNo): void
    {
        // DB_ORDER_INFO의 필드명을 ReceiverInfoUpdateDTO로 변환하여 DTO 초기화
        $dto = new ReceiverInfoUpdateDTO([
            'receiverName' => $orderInfoData['receiverName'] ?? '',
            'phone' => $orderInfoData['receiverPhone'] ?? '',
            'cellPhone' => $orderInfoData['receiverCellPhone'] ?? '',
            'zipcode' => $orderInfoData['receiverZipcode'] ?? '',
            'zonecode' => $orderInfoData['receiverZonecode'] ?? '',
            'address' => $orderInfoData['receiverAddress'] ?? '',
            'addressSub' => $orderInfoData['receiverAddressSub'] ?? '',
            'orderMemo' => $orderInfoData['orderMemo'] ?? '',
        ]);
        
        // modDt와 acceptFl는 제외하고 업데이트에 필요한 데이터만 반환
        $presentReceiverUpdateData = $dto->toPresentReceiverInfoArray(['modDt', 'acceptFl']);
        
        if (!empty($presentReceiverUpdateData)) {
            $this->presentDAO->updatePresentReceiverInfo($orderNo, $orderGoodsNo, $presentReceiverUpdateData);
        }
    }

    /**
     * 배송 메모 리스트 조회
     *
     * @return array
     */
    public function getDeliveryMemoList(): array
    {
        return [
            self::DELIVERY_MEMO_LEAVE_AT_DOOR,
            self::DELIVERY_MEMO_CONTACT_IF_ABSENT,
            self::DELIVERY_MEMO_CONTACT_BEFORE_DELIVERY
        ];
    }

    /**
     * 선물하기 주문의 수령자 정보 마스킹 처리
     * 주문 조회 시 프론트엔드에서 사용
     *
     * @param array $orderData 주문 데이터
     * @param array|null $multiOrderInfo 묶음배송 정보 (선택)
     * @return void
     */
    public function maskReceiverInfo(array &$orderData, ?array &$multiOrderInfo = null): void
    {
        // 수령자명 마스킹 (맨 앞자, 맨 끝자만 노출)
        if (!empty($orderData['receiverName'])) {
            $orderData['receiverName'] = $this->memberMasking->maskingRaw('name', $orderData['receiverName']);
        }

        // 휴대폰 번호 마스킹 (01012**34** 형식)
        if (!empty($orderData['receiverCellPhone'])) {
            $orderData['receiverCellPhone'] = $this->maskCellPhone($orderData['receiverCellPhone']);
        }

        // 묶음배송인 경우도 처리
        if (!empty($multiOrderInfo)) {
            foreach ($multiOrderInfo as &$multiInfo) {
                if (!empty($multiInfo['receiverName'])) {
                    $multiInfo['receiverName'] = $this->memberMasking->maskingRaw('name', $multiInfo['receiverName']);
                }
                if (!empty($multiInfo['receiverCellPhone'])) {
                    $multiInfo['receiverCellPhone'] = $this->maskCellPhone($multiInfo['receiverCellPhone']);
                }
            }
            unset($multiInfo);
        }
    }

    /**
     * 휴대폰 번호 마스킹 처리 (01012**34** 형식)
     *
     * @param string $cellPhone 휴대폰 번호
     * @return string 마스킹된 휴대폰 번호
     */
    public function maskCellPhone(string $cellPhone): string
    {
        $cellPhone = preg_replace('/[^0-9]/', '', $cellPhone); // 숫자만 추출
        $cellPhoneLen = strlen($cellPhone);
        
        if ($cellPhoneLen === 11) {
            // 01012345678 -> 01012**34**
            return substr($cellPhone, 0, 5) . '**' . substr($cellPhone, 7, 2) . '**';
        }
        
        // 11자리가 아닌 경우 로그 남기고 빈 문자열 반환
        $this->logger->channel('presentOrder')->warning(
            '선물 수령자 휴대폰 번호가 정책과 다릅니다. (정책: 11자리, 실제: ' . $cellPhoneLen . '자리)',
            ['cellPhone' => $cellPhone]
        );
        
        return '';
    }
}
