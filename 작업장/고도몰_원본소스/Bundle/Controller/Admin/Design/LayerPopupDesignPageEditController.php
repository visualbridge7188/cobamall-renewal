<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Design;

use Component\Design\SkinDesign;
use Component\Mall\Mall;
use Component\Validator\Validator;
use Framework\Debug\Exception\AlertCloseException;
use Framework\Enum\ExternalUrl;
use Session;

/**
 * 디자인 페이지 편집 팝업 (Front/Mobile 통합)
 */
class LayerPopupDesignPageEditController extends \Controller\Admin\Controller
{
    public function index()
    {
        $request = \App::getInstance('request');
        $getPageID = $request->get()->get('designPageId', 'default');
        $skinType = $request->get()->get('skinType', 'front');
        $skinCode = $request->get()->get('skinCode');

        try {
            // Validation
            if (Validator::designLinkid($getPageID, true) === false) {
                throw new \Exception(__('잘못된 디자인 화일 입니다.'));
            }

            if (empty($skinCode)) {
                throw new \Exception(__('스킨 코드가 빈값 입니다.'));
            }

            // 스킨 키 설정 (front → frontLive/frontWork, mobile → mobileLive/mobileWork)
            $skinLiveKey = $skinType . 'Live';
            $skinWorkKey = $skinType . 'Work';

            // 해외 상점 사용여부에 따른 설정
            $mall = new Mall();
            $mallSno = Session::get('mallSno') ?? 1;
            $mallData = $mall->getMall($mallSno, 'sno');
            $skinData = gd_policy('design.skin', $mallSno);

            // 작업 스킨 적용
            if ($skinData[$skinWorkKey] != $skinCode) {
                $skinData[$skinWorkKey] = $skinCode;
                gd_set_policy('design.skin', $skinData, true, $mallSno);
            }
            \Globals::set('gSkin.' . $skinType . 'SkinWork', $skinCode);

            // SkinDesign 정의
            $skinDesign = new SkinDesign($skinType);
            $skinInfo = $skinDesign->getSkinInfo($skinData[$skinWorkKey]);

            // 디자인 페이지 정보
            $designInfo = $skinDesign->getDesignPageInfo($getPageID);
            $designUrl = $skinDesign->getDesignPageUrl($designInfo['file']['form_type'], $getPageID);
            $designHistory = $skinDesign->getDesignHistoryFile($getPageID);

            // 치환코드
            $commonFuncCode = $commonVarCode = $designCode = '';
            if (strpos($getPageID, '.html') !== false) {
                $dCode = \App::load('\\Component\\Design\\DesignCode');
                $fileName = str_replace('.html', '', $getPageID);

                // 공통함수
                $getCommonFuncCode = $dCode->getDesignCode('common_function');
                $commonFuncCode = '
                    <table width="100%" class="design-code-tbl">
                        <tr>
                            <th>' . __('공통함수 치환코드') . '</th>
                        </tr>
                        ' . @gd_implode('', $getCommonFuncCode) . '
                    </table>
                ';

                // 공통변수
                $getCommonVarCode = $dCode->getDesignCode('common_variable');
                $commonVarCode = '
                    <table width="100%" class="design-code-tbl">
                        <tr>
                            <th>' . __('공통변수 치환코드') . '</th>
                        </tr>
                        ' . @gd_implode('', $getCommonVarCode) . '
                    </table>
                ';

                // 페이지별 치환코드
                $getDesignCode = $dCode->getDesignCode($fileName);
                $designCode = '
                    <table width="100%" class="design-code-tbl">
                        <tr>
                            <th>' . $getPageID . ' ' . __('치환코드') . '</th>
                        </tr>
                        ' . @gd_implode('', $getDesignCode) . '
                    </table>
                ';
            }
        } catch (\Exception $e) {
            throw new AlertCloseException($e->getMessage(), 0, null, 'window');
        }

        // 레이아웃 설정 (팝업용)
        $this->getView()->setDefine('layout', 'layout_popup_design.php');
        $this->getView()->setDefine('layoutContent', 'design/layer_popup_design_page_edit.php');

        // 에디터 페이지 템플릿 설정
        if ($getPageID === 'default') {
            $this->getView()->setDefine('designPageEditSection', 'design/design_page_default.php');
        } else {
            $this->getView()->setDefine('designPageEditSection', 'design/design_page_edit.php');
        }

        // 공통 레이아웃
        $this->getView()->setDefine('layoutTitleBar', 'design/layout_title_bar.php');
        $this->getView()->setDefine('layoutCurrentSkin', 'design/layout_current_skin.php');
        $this->getView()->setDefine('layoutDesignMap', 'design/layout_design_map_' . $skinDesign->skinType . '.php');
        $this->getView()->setDefine('layoutDesignForm', 'design/layout_design_form_' . $designInfo['file']['form_type'] . '.php');
        $this->getView()->setDefine('layoutDesignEditor', 'design/layout_design_editor.php');
        $this->getView()->setDefine('layoutFooter', 'footer.php');

        // CSS/JS
        $this->addCss([
            'design.css',
            '../script/jquery/colorpicker-master/jquery.colorpicker.css',
        ]);
        $this->addScript([
            'jquery/jstree/jquery.tree.js',
            'jquery/jstree/plugins/jquery.tree.contextmenu.js',
            'design/designTree.js',
            'design/design.js',
            'jquery/colorpicker-master/jquery.colorpicker.js',
        ]);

        // 데이터 전달
        $this->setData('getPageID', $getPageID);
        $this->setData('skinDevice', $skinType);
        $this->setData('skinType', $skinDesign->skinType);
        $this->setData('designInfo', $designInfo);
        $this->setData('designUrl', $designUrl);
        $this->setData('designHistory', $designHistory);
        $this->setData('pagePreviewUrl', $skinDesign->pagePreviewUrl);
        $this->setData('skinWorkName', $skinInfo['skin_name']);
        $this->setData('mallData', $mallData ?? null);
        $this->setData('currentLiveSkin', $skinData[$skinLiveKey]);
        $this->setData('currentWorkSkin', $skinData[$skinWorkKey]);
        $this->setData('commonFuncCode', $commonFuncCode);
        $this->setData('commonVarCode', $commonVarCode);
        $this->setData('designCode', $designCode);
        $this->setData('skinCode', $skinCode);
        $this->setData('popupMode', 'yes');
        $this->setData('guideUrl', ExternalUrl::BEGINNER_DESIGN_GUIDE_URL->getUrl('/design/skin_setting/pc#step-3'));
    }
}
