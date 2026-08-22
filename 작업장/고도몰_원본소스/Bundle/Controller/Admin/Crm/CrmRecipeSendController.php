<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Bundle\Component\Godo\GodoSmsServerApi;
use Bundle\Component\Sms\SmsAdmin;
use Component\Sms\Sms;
use Component\Validator\Validator;
use Framework\Debug\Exception\AlertBackException;
use Origin\DTO\ApiClient\Commerce\Crm\TargetsRepeatRuleStatusRequestDTO;
use Origin\Enum\ApiClient\Crm\RecipeType;
use Origin\Service\ApiClient\Commerce\Crm\CrmAdminApiClientService;
use Origin\Service\Crm\Message\MessageConfigService;

class CrmRecipeSendController extends \Controller\Admin\Controller
{
    protected $messageLogger;

    public function index()
    {
        $this->callMenu('crm', 'crm', 'crmRecipe');
        $this->setMenuCode('crm', 'crm', 'crmRecipe');

        $this->messageLogger = \Logger::channel('mobileMessage');

        $recipeType = (string) \Request::get()->get('recipeType', '');

        if (!empty($recipeType) && RecipeType::tryFromSlug($recipeType) === null) {
            throw new AlertBackException(__('잘못된 접근입니다.'));
        }

        $crmGroupNoRaw = \Request::get()->get('no', '');

        if (Validator::number($crmGroupNoRaw, 1) === false) {
            throw new AlertBackException(__('잘못된 접근입니다.'));
        }

        $crmGroupNo = (int) $crmGroupNoRaw;

        /** @var CrmAdminApiClientService $crmAdminApiClientService */
        $crmAdminApiClientService = \App::getInstance(CrmAdminApiClientService::class);

        $crmGroup = $crmGroupNo > 0
            ? $this->fetchSelectedCrmGroup($crmAdminApiClientService, $crmGroupNo)
            : [];

        $recipeDefault = [];
        if (!empty($recipeType)) {
            try {
                $recipeDefault = $crmAdminApiClientService->getRecipeDefault($recipeType)->toRecursiveArray();
            } catch (\Throwable $e) {
                $this->messageLogger->error('CRM 레시피 기본값 처리 실패', [$e->getMessage(), $recipeType, __METHOD__]);
                $recipeDefault = [];
            }
        }

        $recipeName = $recipeDefault['recipeTitle'] ?? '레시피';
        $conditionGuideView = $this->buildConditionGuideView($recipeDefault['conditionGuide'] ?? []);

        Sms::saveSmsPoint();

        /** @var SmsAdmin $smsAdmin */
        $smsAdmin = \App::load('Component\\Sms\\SmsAdmin');
        $smsAutoData = $smsAdmin->getSmsAutoData();

        /** @var GodoSmsServerApi $godoSms */
        $godoSms = \App::load('Component\\Godo\\GodoSmsServerApi');
        $smsPreRegister = $godoSms->checkSmsCallNumber($smsAutoData['smsCallNum']);

        $hasCallNumber = $smsPreRegister && !empty($smsAutoData['smsCallNum']);

        /** @var MessageConfigService $messageConfigService */
        $messageConfigService = \App::getInstance(MessageConfigService::class);
        $messageConfig = $messageConfigService->buildSendableMessageConfig($hasCallNumber);

        $this->setData('messageConfig', $messageConfig);
        $this->setData('smsPointEach', Sms::SMS_POINT);
        $this->setData('lmsPointEach', Sms::LMS_POINT);
        $this->setData('kakaoAlrimTalkPointEach', Sms::KAKAO_POINT);
        $this->setData('kakaoFriendTalkPointEach', Sms::KAKAO_FRIEND_TALK_POINT);
        $this->setData('recipeType', $recipeType);
        $this->setData('crmGroup', $crmGroup);
        $this->setData('recipeName', $recipeName);
        $this->setData('recipeDefault', $recipeDefault);
        $this->setData('conditionGuideView', $conditionGuideView);

        // 어드민 헤더·네비 없는 blank 레이아웃 (드로어용)
        $this->getView()->setDefine('layout', 'layout_blank_noiframe.php');
        $this->getView()->setDefine('layoutHeader', 'header_no_breadcrumb.php');
        $this->getView()->setPageName('crm/crm_recipe_send.php');

        $this->setData('adminBodyClass', trim($this->getData('adminBodyClass') . ' ncds'));
    }

    protected function fetchSelectedCrmGroup(CrmAdminApiClientService $crmAdminApiClientService, int $crmGroupNo): array
    {
        try {
            $crmGroup = $crmAdminApiClientService
                ->getNotificationTargetsRepeatRuleStatus(new TargetsRepeatRuleStatusRequestDTO(), $crmGroupNo)
                ->toRecursiveArray();
        } catch (\Throwable $e) {
            $this->messageLogger->error('CRM 그룹 상태 조회 실패', [$e->getMessage(), $crmGroupNo, __METHOD__]);
            return [];
        }

        if (empty($crmGroup)) {
            return [];
        }

        // status API 응답의 no 는 내부 레코드 PK라 crmGroupNo 로 덮어씀
        $crmGroup['no'] = $crmGroupNo;

        // status 응답엔 title 이 없어 규칙 단건 조회로 보강
        try {
            $crmGroup['title'] = $crmAdminApiClientService
                ->getNotificationTargetsRepeatRule($crmGroupNo)
                ->getTitle();
        } catch (\Throwable $e) {
            $this->messageLogger->error('CRM 그룹명 조회 실패', [$e->getMessage(), $crmGroupNo, __METHOD__]);
        }

        return $crmGroup;
    }

    protected function buildConditionGuideView(array $conditionGuide): array
    {
        $hasConditionGuide = !empty($conditionGuide['selectionGuide'])
            && !empty($conditionGuide['selectableValues'])
            && is_array($conditionGuide['selectableValues']);

        if (!$hasConditionGuide) {
            return ['hasConditionGuide' => false];
        }

        $selectableValues = $conditionGuide['selectableValues'];
        $currentValue = $conditionGuide['value'] ?? $selectableValues[0];
        $guideParts = explode('{no}', $conditionGuide['selectionGuide'], 2);

        // 미리보기 문구는 JS(updateConditionPreview)가 단독 계산·렌더 — 날짜 규칙 단일화
        return [
            'hasConditionGuide' => true,
            'selectableValues' => $selectableValues,
            'currentValue' => $currentValue,
            'guideParts' => $guideParts,
            'productSelectableCount' => (int) ($conditionGuide['productSelectableCount'] ?? 0),
        ];
    }
}
