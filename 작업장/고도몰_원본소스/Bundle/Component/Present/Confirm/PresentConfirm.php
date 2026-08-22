<?php

namespace Bundle\Component\Present\Confirm;

use Framework\Log\Logger;
use Framework\Security\License;
use Framework\Utility\DateTimeUtils;
use Component\Policy\Policy;
use Component\Order\OrderNew;
use Component\Order\OrderAdminNew;
use Component\Present\Notification\PresentNotification;
use Component\Sms\Code;
use Repository\Present\Order\PresentReceiverInfoRepository;
use Repository\Order\OrderInfoRepository;
use Repository\Agreement\BuyerInformRepository;
use Component\Agreement\BuyerInformCode;
use DTO\Present\ReceiverInfoUpdateDTO;
use Illuminate\Database\Capsule\Manager;
use Framework\Database\DBManager;
use DateTimeImmutable;
use Component\Present\PresentDAO;
use Component\Member\MemberMasking;
use Component\Present\Order\PresentOrderAdmin;
use Component\Present\Order\PresentOrder;
use Component\Present\Exception\PresentValidationException;
use Exception;

class PresentConfirm
{
    // 선물 수락 여부
    public const PRESENT_ACCEPT_READY = 'r';     // 수락 대기
    public const PRESENT_ACCEPT_COMPLETED = 'y';   // 수락 완료
    public const PRESENT_ACCEPT_REJECTED = 'n';   // 수락 거절

    public function __construct(
        protected readonly Logger $logger,
        protected readonly Manager $dbManager,
        protected readonly DBManager $db,
        protected readonly Policy $policy,
        protected readonly OrderNew $orderNew,
        protected readonly OrderAdminNew $orderAdminNew,
        protected readonly PresentNotification $presentNotification,
        protected readonly PresentDAO $presentDAO,
        protected readonly MemberMasking $memberMasking,
        protected readonly PresentOrderAdmin $presentOrderAdmin,
        protected readonly PresentReceiverInfoRepository $presentReceiverInfoRepository,
        protected readonly OrderInfoRepository $orderInfoRepository,
        protected readonly BuyerInformRepository $buyerInformRepository
    ) {
    }

    /**
     * 주문번호+주문상품번호에 대한 확인토큰 생성 및 저장
     *
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품번호
     * @return string|false 확인토큰 (실패시 false)
     */
    public function createConfirmToken($orderNo, $orderGoodsNo)
    {
        // 기존 확인토큰이 있는지 확인
        $existingToken = $this->presentReceiverInfoRepository->existsByOrderGoodsNo($orderGoodsNo);
        if ($existingToken) {
            $this->logger->channel('presentConfirm')->warning(__METHOD__ . ' confirmToken 생성 실패 : ', [
                'orderNo' => $orderNo,
                'orderGoodsNo' => $orderGoodsNo,
                'existingToken' => $existingToken
            ]);
            throw new \InvalidArgumentException('이미 토큰이 존재합니다.');
        }

        // orderNo + orderGoodsNo 조합으로 선물하기용 토큰 생성
        $confirmToken = $this->generateConfirmToken($orderNo, $orderGoodsNo);

        return $confirmToken;
    }

    /**
     * 확인토큰으로 주문번호+주문상품번호 조회
     *
     * @param string $confirmToken 확인토큰
     * @return array ['orderNo' => string, 'orderGoodsNo' => int]
     */
    public function getOrderInfoByConfirmToken(string $confirmToken): array
    {
        return $this->presentReceiverInfoRepository->findOrderInfoByConfirmToken($confirmToken);
    }

    /**
     * orderNo + orderGoodsNo 조합으로 선물하기용 토큰 생성
     *
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품번호
     * @return string 선물하기용 토큰
     */
    public function generateConfirmToken(string $orderNo, int $orderGoodsNo): string
    {
        // Secret key로 license key의 해시값 사용
        $secretKey = License::getLicenseKey(true); // md5 해시된 값

        // orderNo + orderGoodsNo 조합
        $data = $orderNo . '|' . $orderGoodsNo;

        // hash_hmac으로 토큰 생성 (SHA-256, 16바이트만 사용)
        $hash = substr(hash_hmac('sha256', $data, $secretKey, true), 0, 16);

        // base64 인코딩 후 특수문자(+, /, =) 제거하여 A-Z, a-z, 0-9만 사용, 20자리로 제한
        $confirmToken = substr(str_replace(['+', '/', '='], '', base64_encode($hash)), 0, 20);

        return $confirmToken;
    }

    /**
     * 주문상품번호로 선물하기 정보 조회
     *
     * @param int $orderGoodsNo 주문상품번호
     * @return array 선물하기 정보
     */
    public function getPresentInfoByOrderGoodsNo(int $orderGoodsNo): array
    {
        return $this->presentReceiverInfoRepository->findPresentInfoByOrderGoodsNo($orderGoodsNo);
    }

    /**
     * 선물 처리 가능 상태인지 검증
     *
     * @param int $orderGoodsNo 주문상품번호
     * @param string $mode 처리 모드 ('accept': 수락, 'changeDelivery': 배송지 변경, 'reject': 거절)
     * @return void
     * @throws PresentValidationException 상태 검증 실패 시 예외 발생
     */
    public function validatePresentStatus(int $orderGoodsNo, string $mode): void
    {
        $presentInfo = $this->getPresentInfoByOrderGoodsNo($orderGoodsNo);
        if (empty($presentInfo)) {
            throw new PresentValidationException('선물 정보를 찾을 수 없습니다.');
        }

        $acceptFl = $presentInfo['acceptFl'] ?? '';

        match ($mode) {
            'accept' => $acceptFl !== self::PRESENT_ACCEPT_READY
                ? throw new PresentValidationException('선물 대기 상태에서만 수락이 가능합니다.')
                : null,
            'reject' => (function () use ($acceptFl) {
                // 고객 교환/반품/환불신청 관리 신청 가능 여부 체크
                if (!$this->isUserHandleEnabled()) {
                    throw new PresentValidationException('선물 거절이 불가합니다.');
                }

                // 거절: 대기 상태에서만 가능
                if ($acceptFl !== self::PRESENT_ACCEPT_READY) {
                    throw new PresentValidationException('선물 대기 상태에서만 거절이 가능합니다.');
                }
            })(),
            'reject_failed' => $acceptFl !== self::PRESENT_ACCEPT_READY
                ? throw new PresentValidationException('선물 대기 상태에서만 거절 실패 처리가 가능합니다.')
                : null,
            'changeDelivery' => $acceptFl !== self::PRESENT_ACCEPT_COMPLETED
                ? throw new PresentValidationException('배송지 변경이 불가능한 상태입니다.')
                : null,
            default => throw new PresentValidationException('유효하지 않은 요청입니다.'),
        };
    }

    /**
     * 선물하기 정보를 화면 표시용으로 포맷팅
     *
     * @param array $presentInfo 선물하기 원본 정보
     * @return array $result 포맷팅된 선물 정보
     */
    public function formatPresentInfo(array $presentInfo): array
    {
        $result = [];

        $result['cardMessage'] = htmlspecialchars($presentInfo['cardMessage'], ENT_QUOTES, 'UTF-8') ?? '';
        $result['cardImageUrl'] = $presentInfo['imageUrl'] ?? '';
        $result['sendDate'] = !empty($presentInfo['sendDt']) ? date('Y-m-d', strtotime($presentInfo['sendDt'])) : '';
        $result['expiredDt'] = !empty($presentInfo['expireDt']) ? date('Y-m-d', strtotime($presentInfo['expireDt'])) : '';

        // 선물 거절 상태인 경우 카운트다운을 0으로 설정
        $acceptFl = $presentInfo['acceptFl'] ?? '';
        if ($acceptFl === self::PRESENT_ACCEPT_REJECTED) {
            $result['expireTimestamp'] = 0;
            $remainingTime = $this->getDefaultRemainingTime();
        } else {
            $result['expireTimestamp'] = !empty($presentInfo['expireDt']) ? strtotime($presentInfo['expireDt']) : 0;
            // 남은 기간 계산
            $remainingTime = $this->calculateRemainingTime($presentInfo['expireDt'] ?? '');
        }

        $result['remainingTime'] = $remainingTime;
        $result['remainingTimeJson'] = json_encode($remainingTime);
        $result['remainingTimeDisplay'] = $remainingTime['display'];

        return $result;
    }

    /**
     * 만료일 기준 남은 기간 계산
     *
     * 현재 시간과 만료 시간의 차이를 초 단위로 계산한 후,
     * 일(86400초), 시간(3600초), 분(60초), 초로 변환하여 반환
     *
     * @param string $expireDt 만료일
     * @return array $remainingTime 남은 기간 정보
     */
    public function calculateRemainingTime(string $expireDt): array
    {
        if (empty($expireDt)) {
            return $this->getDefaultRemainingTime();
        }

        $now = DateTimeUtils::dateFormat('Y-m-d H:i:s', 'now');
        $expireTimestamp = strtotime($expireDt);
        $nowTimestamp = strtotime($now);

        // 만료일이 지났으면 기본값 반환
        if ($expireTimestamp <= $nowTimestamp) {
            return $this->getDefaultRemainingTime();
        }

        $diffSeconds = $expireTimestamp - $nowTimestamp;

        $days = floor($diffSeconds / 86400);
        $hours = floor(($diffSeconds % 86400) / 3600);
        $minutes = floor(($diffSeconds % 3600) / 60);
        $seconds = $diffSeconds % 60;

        return [
            'days' => $days,
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds,
            'display' => sprintf('%d일 %02d:%02d:%02d 남음', $days, $hours, $minutes, $seconds)
        ];
    }

    /**
     * 만료된 경우 기본 남은 기간 정보 반환
     *
     * @return array 기본 남은 기간 정보
     */
    protected function getDefaultRemainingTime(): array
    {
        return [
            'days' => 0,
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0,
            'display' => '0일 남음'
        ];
    }

    /**
     * 주문 상품 정보를 화면 표시용으로 포맷팅
     *
     * @param array $orderGoodsDataList 주문 상품 원본 데이터
     * @return array 포맷팅된 주문 상품 데이터
     */
    public function formatOrderGoodsInfo(array $orderGoodsDataList): array
    {
        $result = [];
        foreach ($orderGoodsDataList as $orderGoodsData) {
            // 옵션 정보 정리
            $optionInfo = $this->formatOptionInfo($orderGoodsData['optionInfo']);
            $optionTextInfo = $this->formatOptionTextInfo(
                is_array($orderGoodsData['optionTextInfo'] ?? null) ? $orderGoodsData['optionTextInfo'] : []
            );
            $orderGoodsData['optionDisplay'] = $this->mergeOptionDisplay($optionInfo, $optionTextInfo);

            // 이미지 설정 및 상품 타입에 따른 분류
            $orderGoodsData['goodsImage'] = preg_replace('/width="\d+"/', 'width="auto"', $orderGoodsData['goodsImage']);
            if (($orderGoodsData['goodsType'] ?? '') === 'goods') {
                $result['goods'] = $orderGoodsData;
            } elseif (($orderGoodsData['goodsType'] ?? '') === 'addGoods') {
                $result['addGoods'][] = $orderGoodsData;
            }
        }
        return $result;
    }

    /**
     * optionInfo JSON을 문자열로 변환
     *
     * @param array $optionInfo 옵션 정보 배열
     * @return string 옵션 정보 문자열
     */
    protected function formatOptionInfo(array $optionInfo): string
    {
        $optionParts = array_filter(
            array_map(function ($option) {
                return (is_array($option) && isset($option['optionName']) && isset($option['optionValue']))
                    ? $option['optionName'] . ' : ' . $option['optionValue']
                    : null;
            }, $optionInfo)
        );

        return implode(', ', $optionParts);
    }

    /**
     * optionTextInfo JSON을 문자열로 변환
     *
     * @param array $optionTextInfo 텍스트 옵션 정보 배열
     * @return string 텍스트 옵션 정보 문자열
     */
    protected function formatOptionTextInfo(array $optionTextInfo): string
    {
        $optionTextParts = array_filter(
            array_map(function ($textOption) {
                return (is_array($textOption) && isset($textOption['optionName']) && isset($textOption['optionValue']))
                    ? $textOption['optionName'] . ' : ' . $textOption['optionValue']
                    : null;
            }, $optionTextInfo)
        );

        return implode(', ', $optionTextParts);
    }

    /**
     * 옵션 정보와 텍스트 옵션 정보를 합치기
     *
     * @param string $optionInfo 옵션 정보 문자열
     * @param string $optionTextInfo 텍스트 옵션 정보 문자열
     * @return string 합쳐진 옵션 표시 문자열
     */
    protected function mergeOptionDisplay(string $optionInfo, string $optionTextInfo): string
    {
        return implode(', ', array_filter([$optionInfo, $optionTextInfo]));
    }

    /**
     * 회원 정보와 배송지 정보를 배송 정보 배열로 변환
     *
     * @param array|null $memberInfo 회원 정보
     * @param array|null $defaultShipping 기본 배송지 정보
     * @param string $acceptFl 수락 상태
     * @return array $deliveryInfo 배송 정보
     */
    public function formatDeliveryInfo(?array $memberInfo = null, ?array $defaultShipping = null, string $acceptFl = ''): array
    {
        // 기본값 설정
        $deliveryInfo = [
            'deliveryName' => '',
            'deliveryPhone' => '',
            'deliveryZipcode' => '',
            'deliveryAddress' => '',
            'deliveryAddressSub' => '',
            'deliveryMessage' => ''
        ];

        if (!empty($defaultShipping)) {
            // 기본 배송지가 있으면 기본 배송지 사용
            $deliveryInfo = [
                'deliveryName' => $defaultShipping['shippingName'] ?? '',
                'deliveryPhone' => $defaultShipping['shippingCellPhone'] ?? $defaultShipping['shippingPhone'] ?? '',
                'deliveryZipcode' => $defaultShipping['shippingZonecode'] ?? $defaultShipping['shippingZipcode'] ?? '',
                'deliveryAddress' => $defaultShipping['shippingAddress'] ?? '',
                'deliveryAddressSub' => $defaultShipping['shippingAddressSub'] ?? '',
                'deliveryMessage' => ''
            ];
        } elseif (!empty($memberInfo)) {
            // 기본 배송지가 없으면 회원 기본 정보 사용
            $deliveryInfo = [
                'deliveryName' => $memberInfo['memNm'] ?? '',
                'deliveryPhone' => $memberInfo['cellPhone'] ?? $memberInfo['phone'] ?? '',
                'deliveryZipcode' => $memberInfo['zonecode'] ?? $memberInfo['zipcode'] ?? '',
                'deliveryAddress' => $memberInfo['address'] ?? '',
                'deliveryAddressSub' => $memberInfo['addressSub'] ?? '',
                'deliveryMessage' => ''
            ];
        }

        $deliveryInfo = array_map(
            fn($value) => htmlspecialchars($value, ENT_QUOTES, 'UTF-8'),
            $deliveryInfo
        );

        // 수정 불가능 상태(acceptFl === 'y')일 때 배송 정보 마스킹 처리
        if ($acceptFl === self::PRESENT_ACCEPT_COMPLETED) {
            // 원본 데이터 저장 (편집 모드에서 사용)
            $deliveryInfo['originalDeliveryName'] = $deliveryInfo['deliveryName'];
            $deliveryInfo['originalDeliveryPhone'] = $deliveryInfo['deliveryPhone'];
            $deliveryInfo['originalDeliveryZipcode'] = $deliveryInfo['deliveryZipcode'];
            $deliveryInfo['originalDeliveryAddress'] = $deliveryInfo['deliveryAddress'];
            $deliveryInfo['originalDeliveryAddressSub'] = $deliveryInfo['deliveryAddressSub'];

            // 마스킹 적용
            $deliveryInfo = $this->maskDeliveryInfo($deliveryInfo);
        }

        return $deliveryInfo;
    }

    /**
     * 수령자 정보 저장/업데이트
     *
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품번호
     * @param ReceiverInfoUpdateDTO $dto 수령자 정보 DTO
     * @param string $mode 처리 모드 ('accept': 수락, 'changeDelivery': 배송지 변경)
     * @return array 업데이트된 수령자 정보 배열
     * @throws Exception 상태 검증 실패 시 예외 발생
     * @throws \Throwable
     */
    public function saveReceiverInfo(string $orderNo, int $orderGoodsNo, ReceiverInfoUpdateDTO $dto, string $mode): array
    {
        // 상태 검증
        $this->validatePresentStatus($orderGoodsNo, $mode);

        $presentReceiverUpdateData = $dto->toPresentReceiverInfoArray();
        $orderInfoData = $dto->toOrderInfoArray();

        $this->dbManager->getConnection()->beginTransaction();
        $updatedReceiverInfo = null;
        try {
            // es_presentReceiverInfo 테이블 업데이트
            $updatedReceiverInfo = $this->presentReceiverInfoRepository->updateReceiverInfo($orderNo, $presentReceiverUpdateData);

            // es_orderInfo 테이블 업데이트
            $this->orderInfoRepository->updateReceiverInfo($orderNo, $orderInfoData);

            $this->dbManager->getConnection()->commit();
        } catch (\Throwable $e) {
            $this->dbManager->getConnection()->rollback();

            $this->logger->channel('presentConfirm')->warning(__METHOD__ . ' 수령자 정보 저장 실패', [
                'orderNo' => $orderNo,
                'orderGoodsNo' => $orderGoodsNo,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }

        // SMS 발송 처리 (DB 작업 성공 시에만, 트랜잭션 영향 받지 않도록 별도 처리)
        if ($updatedReceiverInfo !== null) {
            try {
                $this->sendSmsByMode($orderNo, $mode);
            } catch (\Throwable $smsException) {
                $this->logger->channel('presentConfirm')->warning(__METHOD__ . ' SMS 발송 중 예외 발생', [
                    'orderNo' => $orderNo,
                    'orderGoodsNo' => $orderGoodsNo,
                    'mode' => $mode,
                    'error' => $smsException->getMessage()
                ]);
            }
        }

        return $updatedReceiverInfo;
    }

    /**
     * 선물 거절 상태 업데이트
     *
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품번호
     * @param ReceiverInfoUpdateDTO $dto 수령자 정보 DTO
     * @return void
     * @throws \Throwable 선물 거절 상태 업데이트 실패 시 예외 발생
     */
    public function updatePresentRejectStatus(string $orderNo, int $orderGoodsNo, ReceiverInfoUpdateDTO $dto): void
    {
        $presentReceiverUpdateData = $dto->toPresentReceiverInfoArray();

        // es_presentReceiverInfo: acceptFl만 업데이트
        $presentReceiverUpdateData = array_filter($presentReceiverUpdateData, function ($key) {
            return $key === 'acceptFl';
        }, ARRAY_FILTER_USE_KEY);

        try {
            // es_presentReceiverInfo 테이블 업데이트
            $this->presentDAO->updatePresentReceiverInfo($orderNo, $orderGoodsNo, $presentReceiverUpdateData);
        } catch (\Throwable $e) {
            $this->logger->channel('presentConfirm')->warning(__METHOD__ . ' 수령자 정보 저장 실패', [
                'orderNo' => $orderNo,
                'orderGoodsNo' => $orderGoodsNo,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * 주문번호와 주문상품번호로 수령자 정보 조회
     *
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품번호
     * @return array 수령자 정보
     */
    public function getReceiverInfo(string $orderNo, int $orderGoodsNo): array
    {
        $receiverInfo = $this->presentReceiverInfoRepository->findReceiverInfoByOrderInfo($orderNo, $orderGoodsNo);

        if (empty($receiverInfo)) {
            $this->logger->channel('presentConfirm')->warning(__METHOD__ . ' 수령자 정보 조회 실패', [
                'orderNo' => $orderNo,
                'orderGoodsNo' => $orderGoodsNo
            ]);
            throw new Exception('배송지 입력 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
        }

        return $receiverInfo;
    }

    /**
     * 선물 수락 성공 메시지 생성
     *
     * @param array $receiverInfo 수령자 정보 배열
     * @return string 성공 메시지
     */
    public function formatAcceptMessage(array $receiverInfo): string
    {
        // 배송지 주소 포맷팅
        $deliveryAddress = trim('[' . ($receiverInfo['zipcode'] ?? '') . '] ' . ($receiverInfo['address'] ?? '') . ' ' . ($receiverInfo['addressSub'] ?? ''));

        // 선물 수락 성공 메시지
        $successMsg = "아래 정보로 선물을 받으실 수 있습니다.\n\n"
            . ($receiverInfo['receiverName'] ?? '') . "\n"
            . $deliveryAddress;

        return $successMsg;
    }

    /**
     * 주문번호와 주문상품번호로 수령자 정보 조회
     *
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품번호
     * @return array 수령자 정보
     */
    public function getReceiverInfoByOrderInfo(string $orderNo, int $orderGoodsNo): array
    {
        return $this->presentReceiverInfoRepository->findReceiverInfoByOrderInfo($orderNo, $orderGoodsNo);
    }

    /**
     * 수령자 정보를 배송 정보 배열로 변환
     *
     * @param array $receiverInfo 수령자 정보
     * @param string $acceptFl 수락 여부
     * @return array 배송 정보
     */
    public function formatReceiverInfoToDeliveryInfo(array $receiverInfo, string $acceptFl = ''): array
    {
        $deliveryInfo = [
            'deliveryName' => $receiverInfo['receiverName'] ?? '',
            'deliveryPhone' => $receiverInfo['cellPhone'] ?? $receiverInfo['phone'] ?? '',
            'deliveryZipcode' => $receiverInfo['zipcode'] ?? '',
            'deliveryAddress' => $receiverInfo['address'] ?? '',
            'deliveryAddressSub' => $receiverInfo['addressSub'] ?? '',
            'deliveryMessage' => $receiverInfo['orderMemo'] ?? ''
        ];

        $deliveryInfo = array_map(
            fn($value) => htmlspecialchars($value, ENT_QUOTES, 'UTF-8'),
            $deliveryInfo
        );

        // 선물 수락 상태일 때 배송 정보 마스킹 처리
        if ($acceptFl === self::PRESENT_ACCEPT_COMPLETED) {
            // 원본 데이터 저장 (편집 모드에서 사용)
            $deliveryInfo['originalDeliveryName'] = $deliveryInfo['deliveryName'];
            $deliveryInfo['originalDeliveryPhone'] = $deliveryInfo['deliveryPhone'];
            $deliveryInfo['originalDeliveryZipcode'] = $deliveryInfo['deliveryZipcode'];
            $deliveryInfo['originalDeliveryAddress'] = $deliveryInfo['deliveryAddress'];
            $deliveryInfo['originalDeliveryAddressSub'] = $deliveryInfo['deliveryAddressSub'];

            // 마스킹 적용
            $deliveryInfo = $this->maskDeliveryInfo($deliveryInfo);
        }

        return $deliveryInfo;
    }

    /**
     * 배송 정보 마스킹 처리
     *
     * @param array $deliveryInfo 배송 정보
     * @return array 마스킹된 배송 정보
     */
    protected function maskDeliveryInfo(array $deliveryInfo): array
    {
        // 이름 마스킹
        if (!empty($deliveryInfo['deliveryName'])) {
            $deliveryInfo['deliveryName'] = $this->memberMasking->maskingRaw('name', $deliveryInfo['deliveryName']);
        }

        // 휴대폰 번호 마스킹
        if (!empty($deliveryInfo['deliveryPhone'])) {
            $deliveryInfo['deliveryPhone'] = $this->presentOrderAdmin->maskCellPhone($deliveryInfo['deliveryPhone']);
        }

        // 우편번호 마스킹 (앞 2자리만 노출)
        if (!empty($deliveryInfo['deliveryZipcode'])) {
            $zipcodeLen = mb_strlen($deliveryInfo['deliveryZipcode']);
            if ($zipcodeLen > 2) {
                $deliveryInfo['deliveryZipcode'] = mb_substr($deliveryInfo['deliveryZipcode'], 0, 2) . str_repeat('*', $zipcodeLen - 2);
            }
        }

        // 주소 마스킹
        if (!empty($deliveryInfo['deliveryAddress'])) {
            $deliveryInfo['deliveryAddress'] = $this->memberMasking->maskingRaw('address', $deliveryInfo['deliveryAddress']);
        }

        // 상세주소 마스킹
        if (!empty($deliveryInfo['deliveryAddressSub'])) {
            $deliveryInfo['deliveryAddressSub'] = $this->memberMasking->maskingRaw('address', $deliveryInfo['deliveryAddressSub']);
        }

        return $deliveryInfo;
    }

    /**
     * 배송지 변경 가능 여부 확인
     *
     * @param string $acceptFl 수락 여부 (PRESENT_ACCEPT_READY: 수락 대기, PRESENT_ACCEPT_COMPLETED: 수락 완료, PRESENT_ACCEPT_REJECTED: 수락 거절)
     * @param string $expireDt 만료일시 (datetime 문자열)
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품번호
     * @return bool 배송지 변경 가능 여부
     */
    public function isDeliveryChangeable(string $acceptFl, string $expireDt, string $orderNo, int $orderGoodsNo): bool
    {
        // 선물 수락 상태가 아닌 경우, 또는 만료일이 없는 경우 배송지 변경 불가
        if ($acceptFl !== self::PRESENT_ACCEPT_COMPLETED || empty($expireDt)) {
            return false;
        }

        // 주문 상태가 결제완료(p1)가 아닌 경우 배송지 변경 불가
        if (!$this->isPaymentCompleted($orderNo, $orderGoodsNo)) {
            return false;
        }

        $expireTimestamp = strtotime($expireDt);
        return $expireTimestamp > 0 && $expireTimestamp > time();
    }

    /**
     * 주문 상품의 결제완료 여부 확인
     *
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품번호
     * @return bool 결제완료 상태이면 true, 아니면 false
     */
    public function isPaymentCompleted(string $orderNo, int $orderGoodsNo): bool
    {
        // 주문 상품 정보 조회
        $targetOrderGoods = $this->getOrderGoodsByOrderGoodsNo($orderNo, $orderGoodsNo, ['orderStatus']);
        if (empty($targetOrderGoods)) {
            return false;
        }

        $orderStatus = $targetOrderGoods['orderStatus'] ?? '';
        return $orderStatus === 'p1';
    }

    /**
     * 주문번호와 주문상품번호로 주문 상품 정보 조회
     *
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품번호
     * @param array|null $arrInclude 포함할 필드 배열
     * @return array 주문 상품 정보
     */
    protected function getOrderGoodsByOrderGoodsNo(string $orderNo, int $orderGoodsNo, ?array $arrInclude = null): array
    {
        $orderGoodsData = $this->orderAdminNew->getOrderGoods($orderNo, $orderGoodsNo, null, $arrInclude);
        if (empty($orderGoodsData)) {
            $this->logger->channel('presentConfirm')->warning(__METHOD__ . ' 주문 상품 정보 조회 실패', [
                'orderNo' => $orderNo,
                'orderGoodsNo' => $orderGoodsNo
            ]);
            return [];
        }

        // 전달한 orderGoodsNo와 일치하는 항목 찾기
        $targetOrderGoods = array_filter($orderGoodsData, fn ($orderGoods) => isset($orderGoods['sno']) && (int)$orderGoods['sno'] === $orderGoodsNo);

        if (empty($targetOrderGoods)) {
            $this->logger->channel('presentConfirm')->warning(__METHOD__ . ' 주문 상품 번호 불일치', [
                'orderNo' => $orderNo,
                'orderGoodsNo' => $orderGoodsNo
            ]);
            return [];
        }

        return reset($targetOrderGoods);
    }

    /**
     * 처리 모드에 따른 SMS 발송 처리
     *
     * @param string $orderNo 주문번호
     * @param string $mode 처리 모드 ('accept': 수락, 'update': 배송지 변경, 'reject': 거절)
     * @return void
     */
    protected function sendSmsByMode(string $orderNo, string $mode): void
    {
        $smsCode = match ($mode) {
            'accept' => Code::PRESENT_ACCEPT,
            'changeDelivery' => Code::PRESENT_CHANGE_DEST,
            'reject' => Code::PRESENT_REJECT,
            'reject_failed' => Code::PRESENT_REJECT_FAIL,
            default => null,
        };

        if ($smsCode !== null) {
            $this->presentNotification->sendPresentInfo($smsCode, $orderNo);
        }
    }

    /**
     * 배송지 입력 기한이 지났는지 확인
     *
     * @param string $expireDt 배송지 입력 만료일시
     * @return bool 배송지 입력 기한이 지났으면 true, 아니면 false
     */
    public function isDeliveryInputExpired(string $expireDt): bool
    {
        if (empty($expireDt)) {
            return false;
        }

        try {
            $expireDateTime = new DateTimeImmutable($expireDt);
            $nowDateTime = new DateTimeImmutable();

            return $expireDateTime <= $nowDateTime;
        } catch (Exception $e) {
            $this->logger->channel('presentConfirm')->warning(__METHOD__ . ' 날짜 파싱 실패', [
                'expireDt' => $expireDt,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * 사용자 처리(환불/반품/교환) 신청 사용 여부 확인
     *
     * @return bool 사용자 직접 처리 신청 사용 여부
     */
    public function isUserHandleEnabled(): bool
    {
        return $this->policy->getValue('order.basic')['userHandleFl'] === 'y';
    }

    /**
     * 선물하기 이용약관 정보 조회
     *
     * @return string 이용약관 내용
     */
    public function getPresentAgreementContent(): string
    {
        $presentAgreement = $this->buyerInformRepository->findByInformCd(BuyerInformCode::PRIVATE_PRESENT);
        return nl2br($presentAgreement['content'] ?? '');
    }

    /**
     * 주문 상품 정보 추출 (본상품 + 추가상품 포함)
     *
     * @param array $orderData 주문 상품 정보 배열
     * @return array 주문 상품 정보 [상품번호 => 수량]
     */
    protected function extractOrderGoodsData(array $orderData): array
    {
        $param = [];

        foreach ($orderData as $goodsGroup) {
            // 주문 내 전체 상품(본상품 + 추가상품)을 환불 대상으로 설정
            $param[$goodsGroup['sno']] = $goodsGroup['goodsCnt'];
        }

        return $param;
    }

    /**
     * 결제 완료건 환불 처리
     *
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품번호
     * @return void
     * @throws Exception 환불 처리 실패 시 예외 발생
     */
    protected function refundOrder(string $orderNo, int $orderGoodsNo): void
    {
        // 환불 가능 여부 체크 (주문 상태, 환불 처리 상태 및 환불 신청 여부)
        if (!$this->checkRefundRequestExists($orderNo, $orderGoodsNo)) {
            throw new Exception('환불 신청이 불가능한 주문입니다.');
        }

        // 환불할 상품 정보 추출 (본상품 + 추가상품 포함)
        $orderGoodsData = $this->orderAdminNew->getOrderGoods($orderNo, null, null, ['sno', 'goodsCnt']);
        if (empty($orderGoodsData)) {
            throw new Exception('주문 상품 정보 조회에 실패했습니다.');
        }

        $refundGoods = $this->extractOrderGoodsData($orderGoodsData);
        if (empty($refundGoods)) {
            throw new Exception('환불할 상품 정보를 추출할 수 없습니다.');
        }

        // 환불 신청 데이터 구성
        $bundleData = $this->buildRefundRequestData($refundGoods);

        // 환불 신청 등록 (본상품 + 추가상품 전체)
        $orderGoodsNos = array_keys($refundGoods);
        $userHandleSno = $this->registerRefundRequest($orderNo, $orderGoodsNos, $bundleData);
        if (empty($userHandleSno)) {
            throw new Exception('환불 신청 등록에 실패했습니다.');
        }

        // 자동 환불 처리 시도 (본상품 + 추가상품 전체)
        $result = $this->processAutoRefund($orderNo, $refundGoods, $userHandleSno);

        // 결과 처리
        if (!$this->handleRefundResult($orderNo, $orderGoodsNo, $result)) {
            throw new Exception('환불 처리 결과 처리에 실패했습니다.');
        }
    }

    /**
     * 환불 가능 여부 체크
     * 주문 상태, 환불 처리 상태 및 환불 신청 여부를 확인
     *
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품번호
     * @return bool 환불 불가능한 경우 false, 정상인 경우 true
     */
    protected function checkRefundRequestExists(string $orderNo, int $orderGoodsNo): bool
    {
        $orderGoodsData = $this->orderAdminNew->getOrderGoods($orderNo, $orderGoodsNo, null, ['userHandleSno', 'orderStatus']);
        if (empty($orderGoodsData)) {
            $this->logger->channel('presentConfirm')->warning(__METHOD__ . ' 주문 상품 정보 조회 실패', [
                'orderNo' => $orderNo,
                'orderGoodsNo' => $orderGoodsNo
            ]);
            return false;
        }

        $orderStatus = $orderGoodsData[0]['orderStatus'] ?? '';
        $userHandleSno = $orderGoodsData[0]['userHandleSno'] ?? 0;

        // 주문 상태가 p1(결제완료)가 아닌 경우 환불 신청 불가
        if ($orderStatus !== 'p1') {
            $this->logger->channel('presentConfirm')->warning(__METHOD__ . ' 선물하기 주문 환불 신청 불가 (주문 상태)', [
                'orderNo' => $orderNo,
                'orderGoodsNo' => $orderGoodsNo,
                'orderStatus' => $orderStatus
            ]);
            return false;
        }

        // 이미 환불 처리되었거나 환불 신청된 주문인지 체크
        if (str_starts_with($orderStatus, 'r') || (!empty($userHandleSno) && $userHandleSno > 0)) {
            $this->logger->channel('presentConfirm')->warning(__METHOD__ . ' 이미 환불 처리되거나 환불 신청된 주문', [
                'orderNo' => $orderNo,
                'orderGoodsNo' => $orderGoodsNo,
                'orderStatus' => $orderStatus,
                'userHandleSno' => $userHandleSno,
            ]);
            return false;
        }

        return true;
    }

    /**
     * 환불 신청 데이터 구성
     *
     * @param array $refundGoods 환불할 상품 정보 [상품번호 => 수량]
     * @return array 환불 신청 데이터
     */
    protected function buildRefundRequestData(array $refundGoods): array
    {
        return [
            'userHandleReason' => '기타', // 환불 사유
            'userHandleDetailReason' => '수령자 선물거절로 환불 처리', // 환불 상세 사유
            'userRefundBankName' => '',
            'userRefundAccountNumber' => '',
            'userRefundDepositor' => '',
            'userHandleGoodsCnt' => $refundGoods,
        ];
    }

    /**
     * 환불 신청 등록
     *
     * @param string $orderNo 주문번호
     * @param array $orderGoodsNos 주문상품번호 배열
     * @param array $bundleData 환불 신청 데이터
     * @return array|string 환불 신청 번호 배열 또는 실패 시 빈 배열
     */
    protected function registerRefundRequest(string $orderNo, array $orderGoodsNos, array $bundleData): array|string
    {
        $userHandleSno = $this->orderAdminNew->requestUserHandle($orderNo, $orderGoodsNos, 'r', $bundleData);
        if ($userHandleSno === 'invalid_order' || empty($userHandleSno)) {
            $this->logger->channel('presentConfirm')->warning(__METHOD__ . ' 환불 신청 등록 실패', [
                'orderNo' => $orderNo,
                'orderGoodsNos' => $orderGoodsNos,
                'userHandleSno' => $userHandleSno,
            ]);
            return [];
        }
        return $userHandleSno;
    }

    /**
     * 자동 환불 처리 시도 (본상품 + 추가상품 전체)
     *
     * @param string $orderNo 주문번호
     * @param array $refundGoods 환불 대상 상품 정보 [상품번호 => 수량]
     * @param array|string $userHandleSno 환불 신청 번호
     * @return string 처리 결과 ('ok' 또는 기타 결과 코드)
     */
    protected function processAutoRefund(string $orderNo, array $refundGoods, array|string $userHandleSno): string
    {
        $processData = [
            'orderNo' => $orderNo,
            'orderGoodsNo' => array_keys($refundGoods),
            'claimGoodsCnt' => $refundGoods,
        ];
        $result = $this->orderNew->processAutoPgCancel($processData, $userHandleSno);
        
        // 자동 환불 처리가 성공한 경우 선물하기 confirmToken 값 파기
        if ($result === 'ok') {
            $this->clearConfirmToken($orderNo);
        }
        
        return $result;
    }

    /**
     * 환불 처리 결과 처리
     *
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품번호
     * @param string $result 처리 결과
     * @return bool 처리 성공 여부
     */
    protected function handleRefundResult(string $orderNo, int $orderGoodsNo, string $result): bool
    {
        // 자동 환불이 성공한 경우 (result == 'ok')
        if ($result === 'ok') {
            $this->logger->channel('presentConfirm')->info(__METHOD__ . ' 자동 환불 처리 완료', [
                'orderNo' => $orderNo,
                'orderGoodsNo' => $orderGoodsNo,
            ]);
            return true;
        }

        // 자동 환불이 불가능한 경우 (not_accepted_payment 등)
        // 환불 신청은 이미 등록되어 있으므로 성공으로 처리
        $this->logger->channel('presentConfirm')->info(__METHOD__ . ' 환불 신청 완료 (자동 환불 불가)', [
            'orderNo' => $orderNo,
            'orderGoodsNo' => $orderGoodsNo,
            'result' => $result,
        ]);
        return true;
    }

    /**
     * 선물하기 confirmToken 파기
     *
     * @param string $orderNo 주문번호
     * @return void
     */
    public function clearConfirmToken(string $orderNo): void
    {
        $context = ['orderNo' => $orderNo];
        try {
            $this->presentDAO->clearConfirmTokenByOrderNo($orderNo);
            $this->logger->channel('presentConfirm')->info(__METHOD__ . ' 선물하기 토큰 파기 완료', $context);
        } catch (Exception $e) {
            $context['error'] = $e->getMessage();
            $this->logger->channel('presentConfirm')->warning(__METHOD__ . ' 선물하기 토큰 파기 실패', $context);
        }
    }

    /**
     * 선물 거절 처리
     *
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품번호
     * @return void
     * @throws Exception 상태 검증 실패 또는 선물 거절 처리 실패 시 예외 발생
     */
    public function rejectPresent(string $orderNo, int $orderGoodsNo): void
    {

        // 상태 검증
        $this->validatePresentStatus($orderGoodsNo, 'reject');

        // 선물 거절 트랜잭션 처리
        $this->db->begin_tran();
        try {
            // 선물하기 주문건 환불 처리
            $this->refundOrder($orderNo, $orderGoodsNo);

            // 거절 처리 DTO 생성
            $rejectDTO = new ReceiverInfoUpdateDTO(['acceptFl' => self::PRESENT_ACCEPT_REJECTED]);
            $this->updatePresentRejectStatus($orderNo, $orderGoodsNo, $rejectDTO);

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollback();
            $this->logger->channel('presentConfirm')->warning(__METHOD__ . ' 선물 거절 처리 실패 : ', [
                'orderNo' => $orderNo,
                'orderGoodsNo' => $orderGoodsNo,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        // SMS 발송 처리 (DB 작업 성공 시에만, 트랜잭션 영향 받지 않도록 별도 처리)
        try {
            $this->sendSmsByMode($orderNo, 'reject');
        } catch (\Throwable $smsException) {
            $this->logger->channel('presentConfirm')->warning(__METHOD__ . ' SMS 발송 중 예외 발생', [
                'orderNo' => $orderNo,
                'orderGoodsNo' => $orderGoodsNo,
                'mode' => 'reject_failed',
                'error' => $smsException->getMessage()
            ]);
        }
    }
}
