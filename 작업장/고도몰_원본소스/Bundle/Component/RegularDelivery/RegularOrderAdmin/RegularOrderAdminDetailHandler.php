<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\RegularOrderAdmin;

use DTO\RegularDelivery\RegularOrder\RegularOrderLogDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderApplierUpdateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderConsultMemoCreateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderConsultMemoUpdateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderAdminMemoCreateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderAdminMemoDeleteDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderAdminMemoUpdateDTO;
use Framework\Log\Logger;
use Framework\Http\Session\SessionManager as Session;
use Illuminate\Database\Capsule\Manager;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderLogActionType;
use Repository\RegularDelivery\RegularOrder\RegularOrderApplierRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderConsultRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderAdminMemoRepository;
use Component\RegularDelivery\RegularOrder\RegularOrderLog;
use Framework\Utility\StringUtils;

/**
 * 관리자 정기결제 신청서 상세 페이지 기능 핸들러
 */
class RegularOrderAdminDetailHandler
{
    /**
     * @var Session
     */
    private $session;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var RegularOrderApplierRepository
     */
    private $regularOrderApplierRepository;
    /**
     * @var RegularOrderConsultRepository
     */
    private $regularOrderConsultRepository;
    /**
     * @var RegularOrderAdminMemoRepository
     */
    private $regularOrderAdminMemoRepository;
    /**
     * @var RegularOrderLog
     */
    private $regularOrderLog;

    public function __construct(
        Logger $logger,
        Session $session,
        Manager $manager,
        RegularOrderApplierRepository $regularOrderApplierRepository,
        RegularOrderConsultRepository $regularOrderConsultRepository,
        RegularOrderAdminMemoRepository $regularOrderAdminMemoRepository,
        RegularOrderLog $regularOrderLog
    )
    {
        $this->logger = $logger;
        $this->session = $session;
        $this->manager = $manager;
        $this->regularOrderApplierRepository = $regularOrderApplierRepository;
        $this->regularOrderConsultRepository = $regularOrderConsultRepository;
        $this->regularOrderAdminMemoRepository = $regularOrderAdminMemoRepository;
        $this->regularOrderLog = $regularOrderLog;
    }

    /**
     * 신청자 정보 업데이트
     *
     * @param array $postValue
     * @return void
     * @throws \Throwable
     */
    public function updateApplierInfo(array $postValue)
    {
        $applyNo = $postValue['applyNo'];
        $updateValues = $postValue['info'];
        // 전화번호는 형식에 맞춰서 재 처리
        $updateValues['applierPhone'] = StringUtils::numberToPhone($updateValues['applierPhone']);
        $updateValues['applierCellPhone'] = StringUtils::numberToPhone($updateValues['applierCellPhone']);
        $currentApplierInfo = $this->regularOrderApplierRepository->findApplierInfoByApplyNo($applyNo);

        // 변경된 항목 식별
        $changedValuesWord = $this->getChangedFieldNames($currentApplierInfo, $updateValues);
        // 변경 항목 없으면 함수 종료
        if (empty($changedValuesWord)) {
            return;
        }

        try {
            $this->manager->getConnection()->beginTransaction();

            // 업데이트
            $this->logger->channel('regularOrderAdmin')->info('정기결제 신청서 상세페이지 신청자 정보 수정 : ', $updateValues);
            $dto = new RegularOrderApplierUpdateDTO($applyNo, $updateValues);
            $this->regularOrderApplierRepository->updateRegularOrderApplierByApplyNo($dto);

            // 로깅
            $managerSno = $this->session->get('manager')['sno'];
            $regularOrderLogDTO = new RegularOrderLogDTO(
                $postValue['applyNo'],
                'admin',
                $managerSno,
                RegularOrderLogActionType::APPLICANT_INFO_CHANGE,
                '',
                $changedValuesWord
            );
            $this->regularOrderLog->insertRegularOrderLog($regularOrderLogDTO);

            $this->manager->getConnection()->commit();
        } catch (\Throwable $e) {
            $this->manager->getConnection()->rollBack();
            throw $e;
        }
    }

    /**
     * 변경된 필드명 목록 반환
     *
     * @param array $currentInfo 현재 정보
     * @param array $updateValues 업데이트할 정보
     * @return string 변경된 필드 이름 목록
     */
    private function getChangedFieldNames(array $currentInfo, array $updateValues): string
    {
        $fieldNames = [
            'applierName' => '신청자명',
            'applierPhone' => '전화번호',
            'applierCellPhone' => '휴대폰번호',
            'applierEmail' => '이메일'
        ];

        $changedFields = [];
        foreach ($updateValues as $key => $value) {
            if ($currentInfo[$key] !== $value) {
                $changedFields[] = $fieldNames[$key] . '(' . $value . ')';
            }
        }

        return implode(', ', $changedFields);
    }

    /**
     * 상담메모 등록 및 수정
     * @param array $postValue
     * @return void
     */
    public function updateConsultMemo(array $postValue)
    {
        $this->logger->channel('regularOrderAdmin')->info('신청서 상세페이지 에서 상담 메모 변경 ', $postValue);
        $managerSno = $this->session->get('manager')['sno'];

        // 저장
        if (empty($postValue['consult']['sno'])) {
            $dto = new RegularOrderConsultMemoCreateDTO($postValue['applyNo'], $managerSno, $postValue['consult']);
            $this->regularOrderConsultRepository->insertRegularOrderConsultMemo($dto);
        } else {
            // 수정
            $dto = new RegularOrderConsultMemoUpdateDTO($postValue['consult']['sno'], $managerSno, $postValue['consult']);
            $this->regularOrderConsultRepository->updateRegularOrderConsultMemoBySno($dto);
        }
    }

    /**
     * 상담메모 삭제
     * @return void
     */
    public function deleteConsultMemo(array $postValue)
    {
        $this->logger->channel('regularOrderAdmin')->info('신청서 상세페이지 에서 상담 메모 삭제 ', $postValue);
        $this->regularOrderConsultRepository->deleteRegularOrderConsultBySno($postValue['sno']);
    }

    /**
     * 관리자 메모 저장
     * @param array $postValue
     * @return void
     * @throws \Exception
     */
    public function saveAdminMemo(array $postValue)
    {
        try {
            $managerSno = $this->session->get('manager')['sno'];
            $dto = new RegularOrderAdminMemoCreateDTO($postValue['applyNo'], $postValue['orderMemoCd'], $postValue['adminMemo'], $managerSno);
            $this->regularOrderAdminMemoRepository->insertAdminMemo($dto);
        } catch (\Throwable $e) {
            throw new \Exception('관리자 메모 저장에 일시적으로 실패하였습니다. 잠시 후 다시 시도해주세요.');
        }
    }

    /**
     * 관리자 메모 수정
     * @param array $postValue
     * @return void
     * @throws \Exception
     */
    public function updateAdminMemo(array $postValue)
    {
        try {
            $managerSno = $this->session->get('manager')['sno'];
            $dto = new RegularOrderAdminMemoUpdateDTO($postValue['adminMemoSno'], $postValue['orderMemoCd'], $postValue['adminMemo'], $managerSno);
            $this->regularOrderAdminMemoRepository->updateAdminMemo($dto);
        } catch (\Throwable $e) {
            throw new \Exception('관리자 메모 저장에 일시적으로 실패하였습니다. 잠시 후 다시 시도해주세요.');
        }
    }

    /**
     * 관리자 메모 삭제
     * @param array $postValue
     * @return void
     * @throws \Exception
     */
    public function deleteAdminMemo(array $postValue)
    {
        try {
            $managerSno = $this->session->get('manager')['sno'];
            $dto = new RegularOrderAdminMemoDeleteDTO($postValue['adminMemoSno'], $managerSno);
            $this->regularOrderAdminMemoRepository->deleteAdminMemo($dto);
        } catch (\Throwable $e) {
            throw new \Exception('관리자 메모 삭제에 일시적으로 실패하였습니다. 잠시 후 다시 시도해주세요.');
        }
    }
}
