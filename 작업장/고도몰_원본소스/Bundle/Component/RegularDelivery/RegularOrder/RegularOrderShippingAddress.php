<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\RegularOrder;

use Bundle\Component\Page\Page;
use Component\RegularDelivery\RegularOrder\RegularOrderLog;
use DTO\RegularDelivery\RegularOrder\RegularOrderLogDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderShippingAddressModifyDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderShippingAddressRegistDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderShippingAddressUpdateDTO;
use Framework\Log\Logger;
use Illuminate\Database\Capsule\Manager;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderLogActionType;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderShippingAddressRepository;
use Component\Policy\Policy;

class RegularOrderShippingAddress
{
    const DEFAULT_PAGE_SIZE = 5;

    /**
     * @var Manager
     */
    private $manager;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var RegularOrderShippingAddressRepository
     */
    private $regularOrderShippingAddressRepository;
    /**
     * @var RegularOrderGoodsRepository
     */
    private $regularOrderGoodsRepository;
    /**
     * @var RegularOrderLog
     */
    private $regularOrderLog;
    /**
     * @var RegularOrderRepository
     */
    private $regularOrderRepository;
    /**
     * @var Policy
     */
    private $policy;

    public function __construct(
        Logger $logger,
        Manager $manager,
        RegularOrderShippingAddressRepository $regularOrderShippingAddressRepository,
        RegularOrderGoodsRepository $regularOrderGoodsRepository,
        RegularOrderRepository $regularOrderRepository,
        RegularOrderLog $regularOrderLog,
        Policy $policy
    )
    {
        $this->logger = $logger;
        $this->manager = $manager;
        $this->regularOrderShippingAddressRepository = $regularOrderShippingAddressRepository;
        $this->regularOrderGoodsRepository = $regularOrderGoodsRepository;
        $this->regularOrderRepository = $regularOrderRepository;
        $this->regularOrderLog = $regularOrderLog;
        $this->policy = $policy;
    }

    /**
     * 정기결제 신청서 배송지 조회
     *
     * @param int $memNo
     * @param int $pageNum
     * @param int $pageSize
     * @return array
     */
    public function getRegularOrderShippingAddress(int $memNo, int $pageNum = 1, int $pageSize = self::DEFAULT_PAGE_SIZE): array
    {
        $shippingList = $this->regularOrderShippingAddressRepository->getShippingAddressListByMemNo($memNo, $pageNum, $pageSize);
        $totalShippingListCount = $this->regularOrderShippingAddressRepository->countTotalShippingAddressListByMemNo($memNo);

        // 페이징 처리
        $pager = new Page();
        $pager->setUrl(\Request::getQueryString());
        $pager->setTotal($totalShippingListCount);
        $pager->setCurrentPage($pageNum);
        $pager->setList($pageSize);
        $pager->setPage();

        return [
            'list' => gd_htmlspecialchars_stripslashes($shippingList),
            'pager' => $pager
        ];
    }

    /**
     * 배송지 변경
     *
     * @param RegularOrderShippingAddressUpdateDTO $addressUpdateDTO
     * @return void
     * @throws \Exception
     */
    public function updateRegularOrderShippingAddress(RegularOrderShippingAddressUpdateDTO $addressUpdateDTO)
    {
        $this->manager->getConnection()->beginTransaction();
        $applyNo = $addressUpdateDTO->getApplyNo();
        $shippingSno = $addressUpdateDTO->getShippingSno();
        $sessionType = $addressUpdateDTO->getSessionType();
        $sessionSno = $addressUpdateDTO->getSessionSno();

        // 배송지 유무 확인
        $shippingInfo = $this->regularOrderShippingAddressRepository->findShippingAddressBySno($shippingSno);
        if (empty($shippingInfo)) {
            throw new \Exception('삭제된 배송지입니다. 다시 선택해 주세요.');
        }

        try {
            // 배송지 업데이트
            $this->regularOrderGoodsRepository->updateRegularOrderShippingSnoByApplyNo($applyNo, $shippingSno);

            // 로그 추가
            $logDto = new RegularOrderLogDTO($applyNo, $sessionType, $sessionSno, RegularOrderLogActionType::DELIVERY_ADDRESS_CHANGE, '', '배송지 변경');
            $this->regularOrderLog->insertRegularOrderLog($logDto);

            $this->manager->getConnection()->commit();
        } catch (\Throwable $e) {
            $this->manager->getConnection()->rollBack();
            $this->logger->channel('regularDelivery')->warning('배송지변경 실패', [$e->getMessage(), $e->getTrace()]);
            throw new \Exception('배송지 변경처리에 실패하였습니다. 잠시 후 다시 시도해주세요.');
        }
    }


    /**
     * 정기결제 신청서 배송지 단일 정보 조회
     *
     * @param int $shippingSno
     * @param int $memNo
     * @return array
     */
    public function getShippingAddressInfo(int $shippingSno, int $memNo): array
    {
        $shippingInfo = $this->regularOrderShippingAddressRepository->getShippingAddressInfoBySno($shippingSno, $memNo);

        return gd_htmlspecialchars_stripslashes($shippingInfo);
    }

    /**
     * 배송지 신규 등록
     *
     * @param RegularOrderShippingAddressRegistDTO $dto
     * @return int
     * @throws \Exception
     */
    public function registerShippingAddress(RegularOrderShippingAddressRegistDTO $dto): int
    {
        $this->manager->getConnection()->beginTransaction();
        try {
            if ($dto->defaultFl === 'y') {
                $defaultFlSno = $this->regularOrderShippingAddressRepository->findDefaultFlSno($dto->memNo);
                // 기존 기본 배송지가 존재하는 경우 기존 기본 배송지 업데이트 처리
                if ($defaultFlSno !== null) {
                    $this->regularOrderShippingAddressRepository->updateDefaultFl($defaultFlSno, 'n');
                }
            }

            $shippingSno = $this->regularOrderShippingAddressRepository->registerShippingAddress($dto);
            $this->manager->getConnection()->commit();
            return $shippingSno;
        } catch (\Exception $e) {
            $this->manager->getConnection()->rollBack();
            throw $e;
        }
    }

    /**
     * 배송지 업데이트
     *
     * @param RegularOrderShippingAddressModifyDTO $dto
     * @return bool
     * @throws \Exception
     */
    public function modifyShippingAddress(RegularOrderShippingAddressModifyDTO $dto): bool
    {
        // 변경하려는 배송지를 사용하는 신청서 중에 '이용중, 일시정지' 상태 있는 신청서가 있는지
        $shippingSno = $dto->sno;
        $memNo = $dto->memNo;
        // 정기배송 신청서가 해당 배송지로 신청된 건이 있는지 확인
        $this->hasActiveOrPausedOrdersUsingShippingAddress($shippingSno, $memNo);

        $this->manager->getConnection()->beginTransaction();
        try {
            if ($dto->defaultFl === 'y') {
                $defaultFlSno = $this->regularOrderShippingAddressRepository->findDefaultFlSno($memNo);
                $this->regularOrderShippingAddressRepository->updateDefaultFl($defaultFlSno, 'n');
            }
            $result = $this->regularOrderShippingAddressRepository->modifyShippingAddress($dto);
            $this->manager->getConnection()->commit();
            return $result;
        } catch (\Exception $e) {
            $this->manager->getConnection()->rollBack();
            throw $e;
        }
    }

    /**
     * 배송지 삭제
     *
     * @param int $shippingSno
     * @param int $memNo
     * @return bool
     * @throws \Exception
     */
    public function deleteShippingAddress(int $shippingSno, int $memNo): bool
    {
        // 변경하려는 배송지를 사용하는 신청서 중에 '이용중, 일시정지' 상태 있는 신청서가 있는지
        try {
            $this->hasActiveOrPausedOrdersUsingShippingAddress($shippingSno, $memNo);
        } catch (\Exception $e) {
            throw new \Exception('해당 배송 주소로 신청된 정기배송 건이 있어 삭제 불가합니다. 정기배송 해지 후 삭제 해주시기 바랍니다.');
        }

        return $this->regularOrderShippingAddressRepository->deleteShippingAddress($shippingSno);
    }

    /**
     * 기본 배송지 정보 조회
     *
     * @param int $memNo
     * @return array
     */
    public function findDefaultShippingInfo(int $memNo): array
    {
        $sno = $this->regularOrderShippingAddressRepository->findDefaultFlSno($memNo);
        // 등록된 기본 배송지 없음
        if ($sno === null) {
            return [];
        }
        $shippingInfo = $this->regularOrderShippingAddressRepository->getShippingAddressInfoBySno($sno, $memNo);
        return gd_htmlspecialchars_stripslashes($shippingInfo);
    }

    /**
     * 정기 결제(배송) 기능 사용 여부 체크
     *
     * @return bool
     */
    public function isUseRegularDelivery(): bool
    {
        return $this->policy->getValue("order.basic")['useRegularDelivery'] === 'y';
    }

    /**
     * 회원의 전체 배송지 목록 조회
     *
     * @param int $memNo
     * @return array
     */
    public function getAllShippingAddressList(int $memNo): array
    {
        $allShippingAddress = $this->regularOrderShippingAddressRepository->getAllShippingAddressListByMemNo($memNo);

        $shippingList = [];
        foreach ($allShippingAddress as $address) {
            $shippingList[] = [
                'shippingSno' => $address['sno'],
                'defaultFl' => $address['defaultFl'],
                'shippingTitle' => $this->truncateShippingTitleText(htmlspecialchars($address['shippingTitle'], ENT_QUOTES)),
                'shippingName' => htmlspecialchars($address['shippingName'], ENT_QUOTES),
                'shippingPhone' => $address['shippingPhone'],
                'shippingCellPhone' => $address['shippingCellPhone'],
                'shippingZoneCode' => $address['shippingZonecode'],
                'shippingAddress' => htmlspecialchars($address['shippingAddress'], ENT_QUOTES),
                'shippingAddressSub' => htmlspecialchars($address['shippingAddressSub'], ENT_QUOTES),
                'shippingMessage' => htmlspecialchars($address['shippingMessage'], ENT_QUOTES),
            ];
        }

        return $shippingList;
    }

    /**
     * 기본 배송지 설정
     *
     * @param int $shippingSno
     * @param int $memNo
     * @return bool
     * @throws \Exception
     */
    public function setDefaultShippingAddress(int $shippingSno, int $memNo): bool
    {
        $this->manager->getConnection()->beginTransaction();
        try {
            // 기본 배송지인 경우 설정 해제 처리
            $defaultFlSno = $this->regularOrderShippingAddressRepository->findDefaultFlSno($memNo);
            $this->regularOrderShippingAddressRepository->updateDefaultFl($defaultFlSno, 'n');

            // 기본 배송지 설정
            $result = $this->regularOrderShippingAddressRepository->updateDefaultFl($shippingSno, 'y');
            $this->manager->getConnection()->commit();
            return $result;
        } catch (\Exception $e) {
            $this->manager->getConnection()->rollBack();
            throw $e;
        }
    }

    /**
     * 정기배송 신청서가 해당 배송지로 신청된 건이 있는지 확인
     *
     * @param int $shippingSno
     * @param int $memNo
     * @return void
     * @throws \Exception
     */
    public function hasActiveOrPausedOrdersUsingShippingAddress(int $shippingSno, int $memNo)
    {
        if ($this->regularOrderRepository->hasActiveOrPausedOrdersUsingShippingAddress($shippingSno, $memNo)) {
            throw new \Exception('해당 배송 주소로 신청된 정기배송 건이 있어 수정 불가합니다. 정기배송 해지 후 수정 해주시기 바랍니다.');
        }
    }

    /**
     * 배송지명 절삭 처리(20자 초과 시 17자 + ... 으로 노출)
     *
     * @param string $text
     * @return string
     */
    private function truncateShippingTitleText(string $text): string
    {
        if (mb_strlen($text, 'UTF-8') > 20) {
            return mb_substr($text, 0, 17, 'UTF-8') . '...';
        }
        
        return $text;
    }
}
