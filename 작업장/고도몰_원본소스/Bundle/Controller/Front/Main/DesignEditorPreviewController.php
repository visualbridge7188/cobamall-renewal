<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Front\Main;

use Origin\Service\Design\DesignEditorPreviewService;
use Origin\Service\Design\PreviewDelegate\GoodsViewPreviewDelegate;
use Origin\Service\Design\PreviewDelegate\GoodsListPreviewDelegate;
use Origin\Service\Design\PreviewDelegate\GoodsSearchPreviewDelegate;
use Origin\Service\Design\PreviewDelegate\CartPreviewDelegate;
use Origin\Service\Design\PreviewDelegate\OrderPreviewDelegate;
use Origin\Service\Design\PreviewDelegate\JoinPreviewDelegate;
use Origin\Service\Design\PreviewDelegate\LoginPreviewDelegate;
use Respect\Validation\Validator as v;

/**
 * 디자인 에디터 미리보기 컨트롤러
 *
 * 요청 파라미터:
 * - skinCode: 스킨 코드 (예: design_editor)
 * - assetCode: 에셋 코드 (예: goods--goods_view, order--cart)
 * - previewCode: 검증 코드 (hash('sha512', skinCode . '_' . assetCode . '_' . YYYYMMDD))
 * - extraCode: 추가 코드 (선택, 사용자별 파일 구분용)
 *
 * 새 페이지 Delegate 추가 시:
 * 1. PreviewDelegate 클래스 생성
 * 2. 아래 DELEGATE_MAP에 assetCode => 클래스 매핑 추가
 */
class DesignEditorPreviewController extends \Controller\Front\Controller
{
    private const INVALID_PREVIEW_PATH_MESSAGE = '유효하지 않은 미리보기 경로입니다.';
    private const ERROR_REDIRECT_PATH = '../main/index.php';

    /**
     * assetCode => PreviewDelegate 클래스 매핑
     */
    private const DELEGATE_MAP = [
        'goods--goods_view' => GoodsViewPreviewDelegate::class,
        'goods--goods_list' => GoodsListPreviewDelegate::class,
        'goods--goods_search' => GoodsSearchPreviewDelegate::class,
        'order--cart' => CartPreviewDelegate::class,
        'order--order' => OrderPreviewDelegate::class,
        'member--join' => JoinPreviewDelegate::class,
        'member--login' => LoginPreviewDelegate::class,
    ];

    public function index(): void
    {
        $skinCode = \Request::get()->get('skinCode', '');
        $assetCode = \Request::get()->get('assetCode', '');
        $previewCode = \Request::get()->get('previewCode', '');
        $extraCode = \Request::get()->get('extraCode', '');
        $previewService = \App::getInstance(DesignEditorPreviewService::class);

        // 필수 파라미터 검증
        if (!v::stringType()->notEmpty()->validate($skinCode) ||
            !v::stringType()->notEmpty()->validate($assetCode) ||
            !v::stringType()->notEmpty()->validate($previewCode)) {
            $this->alertInvalidPath();
            return;
        }

        // previewCode 검증
        $expectedCode = $previewService->generatePreviewCode($skinCode, $assetCode);
        if (!hash_equals($expectedCode, $previewCode)) {
            $this->alertInvalidPath();
            return;
        }

        // 스킨 디렉토리 검증
        $skinPath = $previewService->getSkinPath($skinCode);
        if (!v::directory()->validate($skinPath)) {
            $this->alertInvalidPath();
            return;
        }

        // 미리보기 파일 검증
        $previewFilePath = $previewService->getPreviewFilePath($skinCode, $extraCode);
        if (!v::file()->validate($previewFilePath)) {
            $this->alertInvalidPath();
            return;
        }

        // 페이지별 데이터 준비 (Delegate 사용)
        $pageData = $this->preparePageData($assetCode);
        foreach ($pageData as $key => $value) {
            $this->setData($key, $value);
        }

        // 컨트롤러가 없는 커스텀 페이지: assetTitle을 페이지 제목으로 설정
        if (!$this->hasDelegate($assetCode)) {
            $assetTitle = $previewService->getAssetTitle($skinCode, $assetCode);
            if ($assetTitle !== null) {
                $this->setData('gPageName', $assetTitle);
            }
        }

        // 템플릿 디렉토리 설정
        $this->getView()->setTemplateDir($skinPath);

        // 뷰 데이터 설정
        $this->setData('skinCode', $skinCode);
        $this->setData('assetCode', $assetCode);
        $this->setData('previewCode', $previewCode);
        $this->setData('designEditorPreviewFl', 'y');

        // 미리보기용 tpls 설정 (preview 폴더의 헤더/푸터 사용)
        $tpls = [
            'header_inc' => 'preview/outline--header--standard.html',
            'footer_inc' => 'preview/outline--footer--standard.html',
        ];
        $this->getView()->setDefine($tpls);
        $this->setData('tpls', $tpls);

        // 페이지 설정
        $this->getView()->setPageName($previewService->getPreviewPageName($extraCode));
    }

    /**
     * assetCode에 대응하는 컨트롤러(Delegate)가 존재하는지 확인
     */
    private function hasDelegate(string $assetCode): bool
    {
        return isset(self::DELEGATE_MAP[$assetCode]);
    }

    /**
     * assetCode에 해당하는 Delegate를 찾아 페이지 데이터 준비
     */
    private function preparePageData(string $assetCode): array
    {
        $delegateClass = self::DELEGATE_MAP[$assetCode] ?? null;

        if ($delegateClass === null) {
            return [];
        }

        return (new $delegateClass())->preparePreviewData();
    }

    private function alertInvalidPath(): void
    {
        $this->alert(self::INVALID_PREVIEW_PATH_MESSAGE, null, self::ERROR_REDIRECT_PATH);
    }
}