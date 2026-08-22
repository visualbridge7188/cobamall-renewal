<?php
/**
 * 에디터 이미지 업로드 Class
 *
 * @author sj
 * @version 1.0
 * @since 1.0
 * @copyright ⓒ 2016, NHN godo: Corp.
 */
namespace Bundle\Component\File;

use Framework\Debug\Exception\Except;
use Framework\ObjectStorage\Service\ImageUploadService;
use Framework\Utility\ArrayUtils;
use Component\Database\DBTableField;

class EditorUpload
{
    /**
     * obs 이미지 업로드 및 컨텐츠 대체
     *
     * @param array $localImageSources 로컬 이미지 경로 배열
     * @param string $uploadPath obs 업로드 경로(디렉토리)
     * @return array 이미지를 obs로 대체한 에디터 컨텐츠
     */
    public function obsUploadByImageSources(array $localImageSources, string $uploadPath): array
    {
        $obsUploadResults = [];
        foreach ($localImageSources as $src) {
            $filePath = $this->removeQuotAndDomain($src);
            $result = $this->obsImageUpload($filePath, $uploadPath);
            $obsUploadResults[$src] = $result;
        }

        return $obsUploadResults;
    }

    /**
     * obs 이미지 업로드
     *
     * @param string $filePath 로컬이미지 경로
     * @return array
     */
    public function obsImageUpload(string $filePath, string $uploadPath): array
    {
        $result['result'] = false;
        try {
            if (file_exists($filePath)) {
                $saveFileName = ImageUploadService::generateSaveFileName($filePath, 16, true);
                $file = fopen($filePath, 'rb');
                $size = filesize($filePath);
                $binary_data = fread($file, $size);
                $result = (new ImageUploadService())->uploadBinaryImageToRealPath($binary_data, $uploadPath, $saveFileName, false);
            }
        } catch (\Throwable $e) {
            \Logger::channel('obs')->error(__METHOD__ . ' 에디터 obs 이미지 업로드 실패 ' . $e);
        }
        return $result;
    }

    /**
     * 에디터 로컬 이미지 경로 추출
     *
     * @param string $data
     * @return array 로컬 이미지 경로들 - "/data/editor*" or "{상점 도메인}/data/editor*" array
     */
    public function extractLocalImageSources(string $data): array
    {
        try {
            $pattern = '/<img[^>]*src=("(?:https?:\/\/[^"]*\/data\/editor\/[^"]*|\/data\/editor\/[^"]*)")/i';
            preg_match_all($pattern, $data, $matches);
            return array_unique($matches[1]);
        } catch (\Throwable $e) {
            $logger = \App::getInstance('logger');
            $logger->emergency(__METHOD__ . ' : ' . $e->getMessage(), $e->getTrace());
        }
        return [];
    }

    /**
     * 에디터 로컬 이미지 경로를 파일 상대 경로로 변환
     *
     * @param string $src "/data/editor/*" or "{상점 도메인}/data/editor/*"
     * @return string 파일 상대 경로 data/editor/*
     */
    public function removeQuotAndDomain(string $src): string
    {
        // 앞뒤 ", 앞 /, 상점 도메인,  제거
        $path = trim($src, '"');
        $position = strpos($path, 'data/editor/') ?: 0;
        return substr($path, $position);
    }

    /**
     * string contents의 로컬 이미지 경로를 obs 경로로 대체
     *
     * @param string $contents 에디터 컨텐츠
     * @param array $obsUploadResults [로컬 이미지 경로 배열 => obs 업로드 결과] 구조의 array
     * @return string 이미지 경로가 대체된 contents
     */
    public function replaceImageSource(string $contents, array $obsUploadResults): string
    {
        foreach ($obsUploadResults as $localImageSource => $obsUploadResult) {
            $contents = str_replace($localImageSource, '"' . $obsUploadResult['imageUrl']. '"', $contents);
        }
        return $contents;
    }

    /**
     * es_editor_attachments 테이블 저장
     *
     * @param array $obsUploadResults [로컬 이미지 경로 배열 => obs 업로드 결과] 구조의 array
     */
    public function saveEditorAttachments(array $obsUploadResults, string $uploadPath)
    {
        $db = \App::load('DB');
        foreach ($obsUploadResults as $localImageSource => $obsUploadResult) {
            $filePath = "/" . $this->removeQuotAndDomain($localImageSource);
            $arrData = [
                'contentsInfo' => '{ "contentsType": "mail" }',
                'imageFolder' => $obsUploadResult['imageFolder'],
                'imageUrl' => $obsUploadResult['imageUrl'],
                'obsDelFl' => 'n',
                'localPath' => $filePath,
                'localDelFl' => 'y',
            ];

            $arrBind = $db->get_binding(DBTableField::tableEditorAttachments(), $arrData, 'insert', null);
            $db->set_insert_db(DB_EDITOR_ATTACHMENTS, $arrBind['param'], $arrBind['bind'], 'y');
        }
    }



    /**
     * 에디터 로컬 이미지 삭제
     * 한 화면의 여러, 단일 에디터 내에서 ctrl+c, ctrl+v 하는 경우 -> 상점 도메인을 포함한 img 태그가 복사됨
     * 같은 로컬 파일을 바라보는 여러 img 태그가 있을 수 있어 모든 처리 이후 삭제 해야함
     *
     * @param array $imageSources "/data/editor/*" or "{상점 도메인}/data/editor/*" 리스트
     */
    public function removeLocalFileByImageSources(array $obsUploadResults)
    {
        foreach ($obsUploadResults as $localImageSource => $obsUploadResult) {
            $filePath = $this->removeQuotAndDomain($localImageSource);
            if (file_exists($filePath)) {
                //@unlink($filePath); // 안정화 이후 로컬이미지 삭제필요
            }
        }
    }

    /**
     * 에디터 obs 이미지 삭제
     *
     * @param array $obsUploadResults obs 업로드 결과 array
     */
    public function removeObsFileByUploadResults(array $obsUploadResults)
    {
        $imageService = new ImageUploadService();
        foreach($obsUploadResults as $obsUploadResult){
            $imageService->deleteImage($obsUploadResult['imageUrl']);
        }
    }

    /**
     * 에디터 attachments 테이블에 포함된 contentsType 매핑
     *
     * @param string $tableName 테이블명
     * @return string $contentsType
     */
    public static function getEditorContentsType(string $tableName): string
    {
        if(strpos($tableName, "es_bd_") === 0){
            return 'bd' . ucfirst(substr($tableName, 6));
        } else {
            return substr($tableName, 3);
        }
    }
}
