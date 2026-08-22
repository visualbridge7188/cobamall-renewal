<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\Board\Editor;

use Framework\File\FileHandler;
use Framework\Http\Request;
use Framework\Http\Session\SessionManager;
use Framework\Log\Logger;
use Component\Policy\Policy;
use Component\Storage\Storage;
use Component\Scm\ScmAdmin;
use Component\Validator\Validator;

class FroalaEditorFileUploader extends AbstractFileUploader
{
    private Logger $logger;

    public function __construct(
        Request $request,
        SessionManager $session,
        Logger  $logger,
        Policy  $policy,
        Storage $storage,
        ScmAdmin $scmAdmin,
        Validator $validator,
        FileHandler $fileHandler
    ) {
        parent::__construct(
            $request,
            $session,
            $policy,
            $storage,
            $scmAdmin,
            $validator,
            $fileHandler
        );

        $this->logger = $logger;
    }

    /**
     * Froala Editor 이미지 업로드 처리
     *
     * @param string|null $currentDate
     * @param string|null $currentTime
     * @param string|null $uniqueId
     * @return array
     * @throws \Throwable
     */
    public function fileUpload(): array
    {
        try {
            // 업로드된 파일 정보 가져오기
            $fileInfo = $this->getUploadedFileInfo();

            // 업로드된 파일 유효성 검사
            $this->validateUploadedFile($fileInfo);

            // 업로드 식별자 생성: 파라미터가 없으면 기본값(현재 날짜, 시간, 고유 ID)을 생성하고, 값이 있으면 해당 값을 사용 (테스트 용도 포함)
            $identifiers = $this->generateUploadIdentifiers();

            // 업로드 경로 정보 가져오기
            $uploadInfo = $this->getUploadPathInfo($identifiers['currentDate']);

            // 저장할 파일 이름 생성
            $saveFileName = $this->generateSaveFileName($fileInfo['name'], $identifiers['uniqueId'], $identifiers['currentTime']);

            // 파일 저장
            $savedFilePath = $this->saveFile($fileInfo, $uploadInfo, $saveFileName);

            return [
                'location' => $this->getRelativePath($savedFilePath)
            ];
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__ . ' 에디터 이미지 업로드 실패 ' . $e);
            throw $e;
        }
    }

    /**
     * 파일 저장
     *
     * @param array $fileInfo 업로드된 파일 정보
     * @param array $uploadInfo 업로드 경로 정보
     * @param string $saveFileName 저장할 파일 이름
     * @return string 저장된 파일의 전체 경로
     * @throws \RuntimeException
     */
    public function saveFile(array $fileInfo, array $uploadInfo, string $saveFileName): string
    {
        $path = $uploadInfo['path'];
        $uploadCode = $uploadInfo['uploadPath'];
        $storageInfo = $this->getStorageInfo();

        // 외부 스토리지에 업로드
        if ($path === 'goods' && $storageInfo['type'] !== 'local') {
            return $this->uploadToExternalStorage($fileInfo['tmp_name'], $saveFileName, $storageInfo, $path);
        }

        // 로컬 스토리지에 업로드
        return $this->uploadToLocalStorage($fileInfo['tmp_name'], $saveFileName, $uploadCode);
    }
}
