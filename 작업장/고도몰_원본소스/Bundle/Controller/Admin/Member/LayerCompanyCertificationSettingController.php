<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Member;

use Component\Member\Company\CompanyCertification;
use Bundle\Controller\Admin\Controller;
use Framework\Http\Request;

/**
 * 사업자 등록증 설정 관련 컨트롤러
 */
class LayerCompanyCertificationSettingController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function index()
    {
        try {

            /** @var Request $request */
            $request = \App::getInstance('request');
            /** @var \Bundle\Component\Member\Company\CompanyCertification $companyCertification */
            $companyCertification = \App::getInstance(CompanyCertification::class);

            $key = $request->get()->get('type') ?? "comCertification";
            $index = $request->get()->get('index') ?? "";
            $fileSize = $companyCertification->getCompanyCertificationFileSizeByKey($key.$index);

            $this->setData('fileSize', $fileSize);
            $this->setData('key', $key.$index);

            $this->getView()->setDefine('layout', 'layout_layer.php');
            $this->getView()->setPageName('member/layer_company_certification_setting.php');

        } catch (\Throwable $e) {
            \Logger::warning('사업자 회원 파일등록 페이지 로딩중 오류 발생 : ' . $e->getMessage());
            throw $e;
        }
    }
}
