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
use Component\Policy\Policy;
use Component\Storage\Storage;
use Component\Scm\ScmAdmin;
use Component\Validator\Validator;

abstract class AbstractFileUploader
{
    protected const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'tiff', 'png', 'bmp', 'gif', 'svg'];

    private Request $request;
    private SessionManager $session;
    private Policy $policy;
    private Storage $storage;
    private ScmAdmin $scmAdmin;
    private Validator $validator;
    private FileHandler $fileHandler;

    public function __construct(
        Request $request,
        SessionManager $session,
        Policy  $policy,
        Storage $storage,
        ScmAdmin $scmAdmin,
        Validator $validator,
        FileHandler $fileHandler
    ) {
        $this->request = $request;
        $this->session = $session;
        $this->policy = $policy;
        $this->storage = $storage;
        $this->scmAdmin = $scmAdmin;
        $this->validator = $validator;
        $this->fileHandler = $fileHandler;
    }

    /**
     * 파일 업로드 처리 (abstract, 구현 필수)
     *
     * @return array
     */
    abstract public function fileUpload(): array;

    /**
     * 파일 저장 처리 (abstract, 구현 필수)
     *
     * @param array $fileInfo 업로드된 파일 정보
     * @param array $uploadInfo 업로드 경로 정보
     * @param string $saveFileName 저장할 파일 이름
     * @return string 저장된 파일의 전체 경로
     */
    abstract public function saveFile(array $fileInfo, array $uploadInfo, string $saveFileName): string;

    /**
     * 업로드할 스토리지 정보 가져오기
     *  - type: 스토리지 종류
     *  - savePath: 파일 저장 경로
     *  - httpUrl: 파일 접근 URL
     *
     * @return array 스토리지 정보 [type, savePath, httpUrl]
     */
    protected function getStorageInfo(): array
    {
        $storageConf = $this->policy->getValue('basic.storage');
        $httpUrl = 'local';
        $savePath = '';

        // 스토리지 설정에서 상품 이미지 저장소 정보 조회
        if (!empty($storageConf['storageDefault'])) {
            foreach ($storageConf['storageDefault'] as $index => $item) {
                if (in_array('goods', $item)) {
                    $httpUrl = $storageConf['httpUrl'][$index] ?? 'local';
                    $savePath = $storageConf['savePath'][$index] ?? '';
                    break;
                }
            }
        }

        // 공급사일 경우 SCM 정보에서 이미지 저장소 경로 조회
        $manager = $this->session->get('manager');
        $scmNo = $this->session->get('manager.scmNo');
        if (isset($manager) && $scmNo != DEFAULT_CODE_SCMNO) {
            $scmInfo = $this->scmAdmin->getScm($scmNo);
            $httpUrl = $scmInfo['imageStorage'] ?? $httpUrl;
        }

        $ftpStorage = $this->request->post()->get('ftpStorage');
        if (!empty($ftpStorage)) {
            $httpUrl = $ftpStorage;
        }

        return ['type' => $httpUrl, 'savePath' => $savePath, 'httpUrl' => $httpUrl];
    }

    /**
     * 저장할 파일 이름 생성
     *
     * @param string $sourceFileName 소스 파일 이름
     * @param string $uniqueId 고유 ID
     * @param string $currentTime 'His' 형식의 현재 시간
     * @return string 생성된 저장 파일 이름
     */
    protected function generateSaveFileName(string $sourceFileName, string $uniqueId, string $currentTime): string
    {
        $ext = strtolower(pathinfo($sourceFileName, PATHINFO_EXTENSION));
        $fileName = pathinfo($sourceFileName, PATHINFO_FILENAME);
        return hash('sha256', $fileName . $uniqueId) . '_' . $currentTime . '.' . $ext;
    }

    /**
     * 외부 스토리지에 파일 업로드
     *
     * @param string $tmpFile 임시 파일 경로
     * @param string $saveFileName 저장할 파일 이름
     * @param array $storageInfo 스토리지 정보 [type, savePath, httpUrl]
     * @param string $path 업로드 경로
     * @return string 저장된 파일의 전체 경로
     * @throws \RuntimeException
     */
    protected function uploadToExternalStorage(string $tmpFile, string $saveFileName, array $storageInfo, string $path): string
    {
        // 외부 스토리지 업로드 경로 설정
        $uploadPathInStorage = 'editor/' . date('ymd') . '/' . urlencode($saveFileName);

        // 스토리지 디스크 생성 및 파일 업로드
        $storageDisk = $this->storage->disk($this->storage::PATH_CODE_GOODS, $storageInfo['httpUrl']);
        $storageDisk->upload($tmpFile, $uploadPathInStorage);

        // 업로드된 파일의 전체 경로 반환
        $fullUploadPath = implode('/', array_filter([$storageInfo['savePath'], $path, $uploadPathInStorage]));
        return $storageInfo['httpUrl'] . '/' . $fullUploadPath;
    }

    /**
     * 로컬 스토리지에 파일 업로드
     *
     * @param string $tmpFile 임시 파일 경로
     * @param string $saveFileName 저장할 파일 이름
     * @param string $uploadCode 업로드 경로 코드
     * @return string 저장된 파일의 전체 경로
     * @throws \RuntimeException
     */
    protected function uploadToLocalStorage(string $tmpFile, string $saveFileName, string $uploadCode): string
    {
        // 업로드 디렉토리 생성
        $uploadDir = $this->request->server()->get('DOCUMENT_ROOT') . $uploadCode;
        if (!$this->fileHandler->isDirectory($uploadDir)) {
            if (!$this->fileHandler->makeDirectory($uploadDir, 0755, true)) {
                throw new \RuntimeException('디렉토리를 생성하는 데 실패했습니다.');
            }
        }

        // 파일 이동
        $newPath = $uploadDir . urlencode($saveFileName);
        if (!$this->fileHandler->moveUploadedFile($tmpFile, $newPath)) {
            throw new \RuntimeException('업로드된 파일을 이동하는 데 실패했습니다.');
        }

        // 파일 권한 설정
        $this->fileHandler->chmod($newPath, 0707);

        return $newPath;
    }

    /**
     * 업로드된 파일 정보 가져오기
     *
     * @return array
     */
    public function getUploadedFileInfo(): array
    {
        return [
            'tmp_name' => $this->request->files()->get('Filedata.tmp_name'),
            'name'     => $this->request->files()->get('Filedata.name'),
            'error'    => $this->request->files()->get('Filedata.error'),
        ];
    }

    /**
     * 업로드된 파일 유효성 검사
     *
     * @param array $fileInfo
     * @throws \Exception
     */
    public function validateUploadedFile(array $fileInfo)
    {
        // 파일 업로드 취약점 조치
        if ($this->validator->validateIncludeEval($fileInfo['tmp_name']) === false) {
            throw new \Exception('업로드 할 수 없는 파일입니다.');
        }

        // 업로드 가능 여부 확인
        if (!is_uploaded_file($fileInfo['tmp_name'])) {
            switch ($fileInfo['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $errorMsg = sprintf(__('업로드 용량이 %1$s MByte(s) 를 초과했습니다.'), MAX_UPLOAD_SIZE);
                    break;
                default:
                    $errorMsg = __('알 수 없는 오류입니다.') . ' (ERROR CODE : ' . $fileInfo['error'] . ')';
                    break;
            }
            throw new \Exception($errorMsg);
        }

        // 허용된 확장자 확인
        $extension = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            throw new \Exception('허용되지 않은 확장자입니다.');
        }
    }

    /**
     * 업로드 식별자 초기화
     *
     * @param string|null $currentDate
     * @param string|null $currentTime
     * @param string|null $uniqueId
     * @return array
     *
     * 식별자 데이터 구조
     *  format : '/data/editor/' . $path . '/' . $currentDate . '/' . hash('sha256', $fileName . $uniqueId) . '_' . $currentTime . '.' . $ext;
     *  sample : '/data/editor/board/251010/a96bf39ee760d708f6a912d5c4e83dde_121212.jpg'
     */
    public function generateUploadIdentifiers(
        ?string $currentDate = null,
        ?string $currentTime = null,
        ?string $uniqueId = null
    ): array
    {
        return [
            'currentDate' => $currentDate ?? date('ymd'),
            'currentTime' => $currentTime ?? date('His'),
            'uniqueId' => $uniqueId ?? uniqid(),
        ];
    }

    /**
     * 업로드 경로 정보 가져오기
     *
     * @param string $currentDate 'ymd' 형식의 현재 날짜
     * @return array
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * Example return value:
     *  [
     *     'path' => 'board',
     *     'uploadPath' => '/data/editor/board/251010/',
     *  ]
     */
    public function getUploadPathInfo(string $currentDate): array
    {
        // 에디터 경로
        $editorUrl = $this->request->post()->get('editorUrl');

        // 요청 URL에서 경로 정보 추출
        $pathChunk = parse_url($editorUrl);
        if ($pathChunk === false || !isset($pathChunk['path'])) {
            throw new \InvalidArgumentException('유효하지 않은 요청 URL입니다.');
        }

        // 경로를 '/'로 분할하여 배열로 변환
        $pathArray = explode('/', trim($pathChunk['path'], '/'));
        if (empty($pathArray)) {
            throw new \RuntimeException('경로 정보가 비어 있습니다.');
        }

        // 쿼리 문자열에서 게시판 bdId이 있을 경우 추출
        $bdId = null;
        if (isset($pathChunk['query']) && is_string($pathChunk['query'])) {
            parse_str($pathChunk['query'], $queryChunkResult);
            if (isset($queryChunkResult['bdId'])) {
                $bdId = basename($queryChunkResult['bdId']);
            }
        }

        // 경로 배열에서 첫 번째와 마지막 요소 제거 후 마지막 요소 추출
        array_pop($pathArray);
        $path = end($pathArray);
        if ($path === false) {
            throw new \RuntimeException('경로에서 유효한 디렉토리를 추출할 수 없습니다.');
        }

        // 업로드 경로 설정
        if ($path == 'board' && $bdId) {
            $uploadPath = PATH_EDITOR_RELATIVE . '/' . $path . '/' . $bdId . '/';
        } else {
            $uploadPath = PATH_EDITOR_RELATIVE . '/' . $path . '/' . $currentDate . '/';
        }

        // 경로 정보 반환
        return ['path' => $path, 'uploadPath' => $uploadPath];
    }

    /**
     * 전체 경로에서 상대 경로를 추출
     *
     * @param string $fullPath 전체 파일 경로
     * @return string 상대 경로
     */
    public function getRelativePath(string $fullPath): string
    {
        $docRoot = $this->request->server()->get('DOCUMENT_ROOT');
        return str_replace($docRoot, '', $fullPath);
    }
}
