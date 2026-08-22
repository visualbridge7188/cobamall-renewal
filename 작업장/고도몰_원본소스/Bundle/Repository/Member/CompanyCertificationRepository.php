<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Member;

use DTO\Member\CompanyCertificationInsertDTO;
use Origin\Model\Member\CompanyCertification;

class CompanyCertificationRepository
{
    public function insertCertificationInfo(CompanyCertificationInsertDTO $dto)
    {
        // DB 컬럼에는 enum 의 backing value(예: documentType='registration')가 저장되어야 하므로 useEnumName=false 로 직렬화한다.
        CompanyCertification::create($dto->toRecursiveArray(false));
    }

    /**
     * 사업자 등록증(기본 서류) 정보를 memNo 로 조회
     * 기본 인증 서류 종류(registration) 기준으로 조회한다.
     *
     * @param int $memNo
     * @return array
     */
    public function findCertificationInfoByMemNo(int $memNo): array
    {
        return $this->findCertificationInfoByMemNoAndType($memNo, 'registration');
    }

    /**
     * 사업자 등록증 정보를 memNo 와 인증 서류 종류로 조회
     *
     * @param int $memNo
     * @param string $type 인증 서류 종류
     * @return array
     */
    public function findCertificationInfoByMemNoAndType(int $memNo, string $type): array
    {
        $certification = CompanyCertification::query()
            ->where('memNo', $memNo)
            ->where('documentType', $type)
            ->first();

        return $certification ? $certification->toArray() : [];
    }

    /**
     * 사업자 등록증 정보를 sno로 조회
     * @param int $sno
     * @return array
     */
    public function findCertificationInfoBySno(int $sno): array
    {
        $certification = CompanyCertification::find($sno);

        return $certification ? $certification->toArray() : [];
    }

    /**
     * 사업자 등록증 정보를 sno로 삭제
     * @param int $sno
     * @return void
     */
    public function deleteCertificationInfo(int $sno): void
    {
        CompanyCertification::find($sno)->delete();
    }
}
