<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\Present\Order;

use Component\Present\Cart\PresentCart;
use Component\Present\PresentDAO;
use DTO\Present\Order\PresentReceiverInfoInsertDTO;
use DTO\Present\Order\PresentOrderCardInsertDTO;
use Repository\Present\Cart\PresentCartRepository;
use Framework\Log\Logger;
use Carbon\Carbon;
use Component\Present\Confirm\PresentConfirm;
use Repository\Present\Order\PresentReceiverInfoRepository;

class PresentOrder
{
    public function __construct(
        private readonly PresentCart $presentCart,
        private readonly PresentDAO $presentDAO,
        private readonly PresentCartRepository $presentCartRepository,
        private readonly Logger $logger,
        private readonly PresentConfirm $presentConfirm,
        private readonly PresentReceiverInfoRepository $presentReceiverInfoRepository
    ) {
    }

    /**
     * 선물하기 주문 데이터 준비
     * 선물하기 주문인 경우 수령자 정보를 추출하고, 장바구니 데이터를 수령자 수만큼 복제하여 1:1 매칭 구조로 변환
     * 
     * @param array $cartData 장바구니 데이터
     * @param array $orderInfo 주문 정보
     * @param string $orderNo 주문번호
     * @return array ['isPresentOrder' => bool, 'receiverInfo' => array|null, 'preparedCartData' => array]
     */
    public function preparePresentOrder(array $cartData, array $orderInfo, string $orderNo): array
    {
        // 선물하기 주문 여부 확인
        $isPresentOrder = $this->presentCart->isPresentCartOrder($cartData);
        
        // 선물하기 수령자 정보 추출
        $receiverInfo = $isPresentOrder 
            ? $this->extractReceiverInfo($orderInfo) 
            : null;
        
        $receiverCount = count($receiverInfo ?? []);

        // 선물하기 주문인 경우 카드 정보 저장
        if ($isPresentOrder && !empty($cartData)) {
            $this->saveOrderCard($cartData, $orderNo);
        }
        
        // 선물하기 주문인 경우, 수령자 수만큼 상품을 복제하여 1:1 매칭 구조로 변환
        $preparedCartData = $cartData;
        if ($isPresentOrder && $receiverCount > 0) {
            $preparedCartData = array_reduce($cartData, function ($carry, $gVal) use ($receiverCount) {
                for ($i = 0; $i < $receiverCount; $i++) {
                    $carry[] = $gVal;
                }
                return $carry;
            }, []);
        }
        
        return [
            'isPresentOrder' => $isPresentOrder,
            'receiverInfo' => $receiverInfo,
            'preparedCartData' => $preparedCartData
        ];
    }

    /**
     * 주문 정보에서 수령자 정보 배열 추출
     * 
     * @param array $orderInfo 주문 정보 배열
     * @return array 수령자 정보 배열
     */
    private function extractReceiverInfo(array $orderInfo): array
    {
        return is_array($orderInfo['presentReceivers']) ? $orderInfo['presentReceivers'] : [$orderInfo];
    }

    /**
     * 선물하기 주문인 경우 수령자 정보 처리
     * orderGoodsNo와 함께 DTO를 생성하여 저장
     * 
     * @param bool $isPresentOrder 선물하기 주문 여부
     * @param array|null $receiverInfo 수령자 정보 배열
     * @param int $receiverIndex 수령자 인덱스
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품 번호
     * @return int 다음 수령자 인덱스
     */
    public function processReceiver(
        bool $isPresentOrder,
        ?array $receiverInfo,
        int $receiverIndex,
        string $orderNo,
        int $orderGoodsNo
    ): int {
        if ($isPresentOrder && $receiverInfo !== null && isset($receiverInfo[$receiverIndex])) {
            $receiver = $receiverInfo[$receiverIndex];
            
            // 선물 확인용 토큰 생성
            $confirmToken = $this->presentConfirm->createConfirmToken($orderNo, $orderGoodsNo);

            $receiverInfoDTO = new PresentReceiverInfoInsertDTO(
                orderNo: $orderNo,
                orderGoodsNo: $orderGoodsNo,
                receiverName: $receiver['receiverName'] ?? $receiver['name'],
                phone: $receiver['phone'] ?? $receiver['receiverPhone'],
                cellPhone: $receiver['cellPhone'] ?? $receiver['receiverCellPhone'],
                address: $receiver['address'] ?? $receiver['receiverAddress'],
                addressSub: $receiver['addressSub'] ?? $receiver['receiverAddressSub'],
                zipcode: $receiver['zipcode'] ?? $receiver['receiverZipcode'],
                orderMemo: $receiver['orderMemo'] ?? $receiver['receiverMemo'],
                acceptFl: $receiver['acceptFl'] ?? PresentConfirm::PRESENT_ACCEPT_READY,
                confirmToken: $confirmToken,
                sendSmsFl: $receiver['sendSmsFl'] ?? 'n',
                sendDt: $receiver['sendDt'],
                regDt: Carbon::now()
            );
            
            // 저장
            $this->saveReceiverInfo($receiverInfoDTO);
            return $receiverIndex + 1;
        }
        
        return $receiverIndex;
    }

    /**
     * 선물하기 수령자 정보 저장
     * 
     * @param PresentReceiverInfoInsertDTO $receiverInfoDTO 수령자 정보 DTO
     * @return void
     */
    public function saveReceiverInfo(PresentReceiverInfoInsertDTO $receiverInfoDTO): void
    {
        $presentReceiverInfo = $receiverInfoDTO->toArray();
        
        // PresentDAO를 사용하여 저장
        $this->presentDAO->insertPresentReceiverInfo($presentReceiverInfo);
    }

    /**
     * 선물하기 주문 카드 정보 저장
     * 
     * @param array $cartData 장바구니 데이터
     * @param string $orderNo 주문번호
     * @return void
     */
    public function saveOrderCard(array $cartData, string $orderNo): void
    {
        // cartSno 추출
        $cartSno = $this->presentCart->extractCartSno($cartData);
        if ($cartSno === null) {
            return;
        }

        // es_presentCart에서 카드 정보 조회
        $presentCartInfo = $this->presentCartRepository->findPresentCartReceiverInfoByCartSno($cartSno);

        if (empty($presentCartInfo)) {
            $this->logger->channel('presentOrder')->warning('선물하기 카드 정보가 없습니다. cartSno: ' . $cartSno);
            throw new \InvalidArgumentException('선물하기 카드 정보가 없습니다.');
        }

        // 모든 수령자의 카드 정보는 동일하므로, 첫 번째 항목의 카드 정보만 사용
        $cardInfo = $presentCartInfo[0];
        $cardSno = $cardInfo['cardSno'] ?? null;
        $cardMessage = $cardInfo['cardMessage'] ?? null;

        // cardSno는 필수값
        if (empty($cardSno)) {
            $this->logger->channel('presentOrder')->warning('선물하기 카드 번호가 없습니다. cardInfo: ', [$cardInfo]);
            throw new \InvalidArgumentException('선물하기 카드 번호가 없습니다.');
        }

        // DTO 생성
        $orderCardDTO = PresentOrderCardInsertDTO::createForInsert($orderNo, $cardSno, $cardMessage);
        $orderCardData = $orderCardDTO->toArray();

        // PresentDAO를 사용하여 저장
        $this->presentDAO->insertPresentOrderCard($orderCardData);
    }

    /**
     * 선물하기 주문 여부
     *
     * @param string $orderNo 주문 번호
     * @return bool 선물하기 주문 여부
     */
    public function isPresentOrder(string $orderNo): bool
    {
        if (empty($orderNo) || $orderNo == 'Array') {
            $this->logger->channel('presentOrder')->warning(__METHOD__ . '선물주문 체크 비어있거나 올바르지 않은 주문번호 타입', ['orderNo' => $orderNo]);
            return false;
        }
        return $this->presentReceiverInfoRepository->existsByOrderNo((string)$orderNo);
    }
}
