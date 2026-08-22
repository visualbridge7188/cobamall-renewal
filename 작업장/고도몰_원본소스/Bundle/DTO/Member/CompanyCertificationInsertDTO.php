<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */
namespace Bundle\DTO\Member;

use Bundle\Enum\Member\Company\DocumentType;

/**
 * 사업자 등록증 DB 저장 DTO
 */
class CompanyCertificationInsertDTO extends \Origin\DTO\AbstractDTO
{
    private int $memNo;
    private string $imageFileNm;
    private string $imageFilePath;
    private DocumentType $documentType;

    /**
     * @param int $memNo
     * @param array $result fileName, filePath 포함
     * @param string|null $documentType 인증 서류 종류(DocumentType value). null 이면 주 사업자등록증
     */
    public function __construct(
        int $memNo,
        array $result,
        ?string $documentType
    )
    {
        $this->memNo = $memNo;
        $this->imageFileNm = $result['fileName'] ?? '';
        $this->imageFilePath = $result['filePath'] ?? '';
        $this->documentType = $documentType !== null
            ? DocumentType::from($documentType)
            : DocumentType::REGISTRATION;
    }

    /**
     * @return int
     */
    public function getMemNo(): int
    {
        return $this->memNo;
    }

    /**
     * @return string
     */
    public function getImageFileNm(): string
    {
        return $this->imageFileNm;
    }

    /**
     * @return string
     */
    public function getImageFilePath(): string
    {
        return $this->imageFilePath;
    }

    /**
     * @return DocumentType
     */
    public function getDocumentType(): DocumentType
    {
        return $this->documentType;
    }

    /**
     * @param DocumentType $documentType
     * @return void
     */
    public function setDocumentType(DocumentType $documentType): void
    {
        $this->documentType = $documentType;
    }
}
