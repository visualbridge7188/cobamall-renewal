<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\Member\Company;

use Bundle\Enum\Member\Company\DocumentType;
use DTO\Member\CompanyCertificationInsertDTO;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use Repository\Member\CompanyCertificationRepository;
use Component\Policy\Policy;
use Framework\Log\Logger;
use Framework\ObjectStorage\Service\ImageUploadService;

/**
 * 사업자 > 등록증 관련 클래스
 */
class CompanyCertification
{
    const DEFAULT_COMPANY_CERTIFICATION_FILE_SIZE = '2'; // MB
    const CERTIFICATION_IMAGE_UPLOAD_PATH = '/member/company/certification/%s';
    /** 주 사업자등록증의 정책(member.joinitem) file key (DB documentType=registration 과 표현이 다름) */
    const REGISTRATION_FILE_KEY = 'comCertification';
    const ALLOW_MIME_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/bmp',
        'image/webp',
        'image/svg+xml'
    ];

    public function __construct(
        private readonly Logger $logger,
        private readonly Policy $policy,
        private readonly ImageUploadService $imageUploadService,
        private readonly CompanyCertificationRepository $companyCertificationRepository
    )
    {
    }

    /**
     * 사업자 등록증(주 서류) 파일 크기 설정
     * 주 사업자등록증(comCertification) 기준으로 최대 파일 크기를 설정한다.
     * 튜닝 메소드 시그니처 호환용
     *
     * @param int $size 최대 파일 크기 (MB)
     * @return bool
     */
    public function setCompanyCertificationFileSize(int $size): bool
    {
        return $this->setCompanyCertificationFileSizeByKey($size, $this->toFileKey(DocumentType::REGISTRATION));
    }

    /**
     * 사업자 등록증 / 추가서류 파일 크기 설정
     * key 에 해당하는 서류의 최대 파일 크기를 설정한다.
     *
     * @param int $size 최대 파일 크기 (MB)
     * @param string $key 정책 file key (comCertification / comAddiCert{n})
     * @exception \Exception 허용되지 않은 key 인 경우
     * @return bool
     */
    public function setCompanyCertificationFileSizeByKey(int $size, string $key): bool
    {
        if ($this->tryFromFileKey($key) === null) {
            throw new \Exception("허용되지 않은 key Type : $key");
        }

        $joinItem = $this->policy->getValue('member.joinitem');
        $joinItem[$key][$this->fileSizeField($key)] = $size;

        return $this->policy->setValue('member.joinitem', $joinItem);
    }

    /**
     * 사업자 등록증(주 서류) 파일 크기 조회
     * 주 사업자등록증(comCertification) 기준 최대 파일 크기를 반환한다.
     * 튜닝 메소드 시그니처 호환용
     *
     * @return int
     */
    public function getCompanyCertificationFileSize(): int
    {
        return $this->getCompanyCertificationFileSizeByKey($this->toFileKey(DocumentType::REGISTRATION));
    }

    /**
     * 사업자 등록증 / 추가서류 파일 크기 조회
     * key 에 해당하는 서류의 최대 파일 크기를 반환한다. 미설정 시 기본값.
     *
     * @param string $key 정책 file key (comCertification / comAddiCert{n})
     * @exception \Exception 허용되지 않은 key 인 경우
     * @return int
     */
    public function getCompanyCertificationFileSizeByKey(string $key): int
    {
        if ($this->tryFromFileKey($key) === null) {
            throw new \Exception("허용되지 않은 key Type : $key");
        }

        $joinItem = $this->policy->getValue('member.joinitem');
        return $joinItem[$key][$this->fileSizeField($key)] ?? self::DEFAULT_COMPANY_CERTIFICATION_FILE_SIZE;
    }

    /**
     * 사업자 추가 제출 서류 최대 개수
     * @return int
     */
    public function getCompanyAddiCertificationFileCount(): int
    {
        // 사업자등록증(주 서류) 제외한 추가 제출 서류 개수
        return count(DocumentType::cases()) - 1;
    }

    /**
     * 서류 종류별 정책(member.joinitem)에 보관된 파일 크기 하위 키를 반환한다.
     * 사업자등록증(comCertification)은 maxSize, 추가서류(comAddiCert{n})는 certificationFileSize 로 보관된다.
     *
     * @param string $key 정책 file key
     * @return string 파일 크기 하위 키
     */
    private function fileSizeField(string $key): string
    {
        return $key === self::REGISTRATION_FILE_KEY ? 'maxSize' : 'certificationFileSize';
    }

    /**
     * 서류 종류(DB documentType)로부터 정책(member.joinitem) file key 로 변환한다.
     * 주 사업자등록증만 'comCertification'(REGISTRATION_FILE_KEY) 으로 표현이 다르고,
     * 추가서류는 documentType backing value 와 동일하다.
     *
     * @param DocumentType $type
     * @return string 정책 file key
     */
    private function toFileKey(DocumentType $type): string
    {
        return match ($type) {
            DocumentType::REGISTRATION => self::REGISTRATION_FILE_KEY,
            default => $type->value,
        };
    }

    /**
     * 정책(member.joinitem) file key 로부터 서류 종류를 조회한다. 허용되지 않은 key 면 null.
     *
     * @param string $fileKey 정책 file key
     * @return DocumentType|null
     */
    private function tryFromFileKey(string $fileKey): ?DocumentType
    {
        return match ($fileKey) {
            self::REGISTRATION_FILE_KEY => DocumentType::REGISTRATION,
            // 'registration'(DB documentType)은 정책 file key 가 아니므로 제외한다
            DocumentType::REGISTRATION->value => null,
            default => DocumentType::tryFrom($fileKey),
        };
    }

    /**
     * 사업자 등록증(주 서류) 이미지 OBS 업로드
     *
     * @param array $certificationFiles 업로드 파일 정보 (tmp_name, name, size 등)
     * @param int $memNo
     * @return void
     * @throws Exception|\Throwable
     */
    public function upload(array $certificationFiles, int $memNo): void
    {
        $this->uploadCertificationFile($certificationFiles, $memNo, null);
    }

    /**
     * 사업자 추가 제출 서류 이미지 OBS 업로드
     *
     * @param array $certificationFiles 업로드 파일 정보 (tmp_name, name, size 등)
     * @param int $memNo
     * @param int $index 추가서류 인덱스 (comAddiCert{index} 타입으로 저장)
     * @return void
     * @throws Exception|\Throwable
     */
    public function uploadAdditional(array $certificationFiles, int $memNo, int $index): void
    {
        $this->uploadCertificationFile($certificationFiles, $memNo, "comAddiCert$index");
    }

    /**
     * 사업자 등록증 / 추가서류 공통 업로드 처리
     *
     * @param array $certificationFiles 업로드 파일 정보 (tmp_name, name, size 등)
     * @param int $memNo
     * @param string|null $certType null이면 주 사업자등록증(comCertification), 값이 있으면 추가서류 타입(comAddiCert{index})
     * @return void
     * @throws Exception|\Throwable
     */
    private function uploadCertificationFile(array $certificationFiles, int $memNo, ?string $certType): void
    {
        $fileCount = count($certificationFiles['tmp_name']);
        if ($fileCount !== 1) {
            throw new \Exception('파일은 1개만 업로드 가능합니다.');
        }

        // certType이 있으면 추가서류, 없으면 주 사업자등록증 기준으로 기존 정보 조회
        if ($certType !== null) {
            $certificationInfo = $this->companyCertificationRepository->findCertificationInfoByMemNoAndType($memNo, $certType);
        } else {
            $certificationInfo = $this->companyCertificationRepository->findCertificationInfoByMemNo($memNo);
        }

        // 기존파일이 있으면 삭제부터 진행.
        if (!empty($certificationInfo)) {
            $deleteResult = $this->imageUploadService->deleteImage($certificationInfo['imageFilePath']);
            if ($deleteResult === false) {
                \Logger::channel('member')->error("기존 사업자 등록 서류 삭제에 실패했습니다. memNo : {$memNo}");
            }
            $this->companyCertificationRepository->deleteCertificationInfo($certificationInfo['sno']);
        }

        // 파일 업로드 최대 사이즈 (주 사업자등록증 / 추가서류 각 서류별 설정값 사용)
        $fileKey = $certType ?? $this->toFileKey(DocumentType::REGISTRATION);
        $maxFileSize = $this->getCompanyCertificationFileSizeByKey($fileKey);
        $result = $this->uploadToObs($maxFileSize, $certificationFiles, $memNo);
        $this->logger->channel('adminLog')->info('OBS IMAGE UPLOAD RESULT : ', $result);

        // 업로드 결과 처리
        if ($result['result']) {
            $this->insertCertificationInfo($memNo, $result, $certType); // 성공시 insert
        } else {
            throw new \Exception('사업자 등록증 이미지 업로드에 실패했습니다.');
        }
    }

    /**
     * 결과 정보 저장
     * @param int $memNo
     * @param array $result
     * @param string|null $certType 추가서류 타입(comAddiCert{index}), null이면 주 사업자등록증
     * @return void
     * @throws \Throwable
     */
    private function insertCertificationInfo(int $memNo, array $result, ?string $certType = null): void
    {
        // DB 저장
        try {
            $dto = new CompanyCertificationInsertDTO(
                $memNo,
                [
                    'filePath' => $result['saveFileNm'], // img full url
                    'fileName' => $result['uploadFileNm'] // file name
                ],
                $certType
            );
            $this->companyCertificationRepository->insertCertificationInfo($dto);
        } catch (\Throwable $e) {
            // DB 저장 실패 시 OBS에서 파일 삭제
            $this->imageUploadService->deleteImage($result['saveFileNm']);
            throw $e;
        }
    }

    /**
     * 사업자 등록증 정보 조회
     * @param int $memNo
     * @return array
     */
    public function getCertification(int $memNo): array
    {
        return $this->companyCertificationRepository->findCertificationInfoByMemNo($memNo);
    }

    public function getCertificationByMemNoAndType(int $memNo, string $type): array
    {
        return $this->companyCertificationRepository->findCertificationInfoByMemNoAndType($memNo, $type);
    }

    /**
     * 사업자 등록증 다운로드
     * @param int $sno
     * @throws AlertBackException
     */
    public function download(int $sno): void
    {
        $certification = $this->companyCertificationRepository->findCertificationInfoBySno($sno);
        if (empty($certification)) {
            throw new Exception('찾을 수 없는 사업자등록증입니다.');
        }

        $this->imageUploadService->download($certification['imageFileNm'], $certification['imageFilePath']);
    }

    /**
     * 사업자 등록증 삭제
     * @param int $sno
     * @return void
     */
    public function delete(int $sno): void
    {
        // OBS에서 파일 삭제
        $this->logger->channel('adminLog')->info('DELETE CERTIFICATION SNO: ' . $sno);
        $certification = $this->companyCertificationRepository->findCertificationInfoBySno($sno);
        if (!empty($certification)) {
            $result = $this->imageUploadService->deleteImage($certification['imageFilePath']);

            if ($result) {
                // DB에서 삭제
                $this->companyCertificationRepository->deleteCertificationInfo($sno);
            }
        }
    }

    public function getAdditionalData(): array
    {
        $joinItem = $this->policy->getValue('member.joinitem');
        $additionalData = [];

        foreach ($joinItem as $key => $value) {
            if (str_contains($key, 'comAddiCert')) {
                $additionalData[$key] = $value;
            }
        }

        return $additionalData;
    }

    /**
     * 사업자등록증을 OBS에 업로드하는 함수
     * @param int $maxFileSize
     * @param array $certificationFiles
     * @param int $memNo
     * @return array
     * @throws \Exception
     */
    private function uploadToObs(int $maxFileSize, array $certificationFiles, int $memNo): array
    {
        // 이미지 업로드 서비스에 허용되는 MIME 타입 설정
        $this->imageUploadService->setAllowMimeTypes(self::ALLOW_MIME_TYPES);

        // 파일 업로드
        $tempName = $certificationFiles['tmp_name'][0];
        $name = $certificationFiles['name'][0];
        $fileSize = (int)$certificationFiles['size'][0];

        // file 사이즈 초과 시 에러코드
        $fileSizeToMB = $fileSize / 1024 / 1024;
        $errorCode = $fileSizeToMB > $maxFileSize ? UPLOAD_ERR_INI_SIZE : UPLOAD_ERR_OK;

        // OBS 이미지 업로드
        $uploadFile = [
            'tmp_name' => $tempName,
            'name' => $name,
            'error' => $errorCode,
        ];
        return $this->imageUploadService->uploadImage(
            $uploadFile,
            sprintf(self::CERTIFICATION_IMAGE_UPLOAD_PATH, $memNo),
            false,
            $maxFileSize
        );
    }
}
