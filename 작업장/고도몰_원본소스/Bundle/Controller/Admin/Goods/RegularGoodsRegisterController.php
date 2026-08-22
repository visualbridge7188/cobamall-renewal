<?php

namespace Bundle\Controller\Admin\Goods;

use Component\RegularDelivery\RegularGoods\RegularGoods;
use Exception;
use Origin\Enum\ManagerTutorialType;
use Origin\Enum\RegularDelivery\RegularGoods\RegularGoodsAttribute;
use Origin\Service\Admin\Member\ManagerTutorial\HelperPopupExposureAvailabilityService;
use Request;
use Session;

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * @copyright ⓒ 2025, NHN COMMERCE Corp.
 *
 * 정기결제(배송) 상품 등록 / 수정 페이지
 */
class RegularGoodsRegisterController extends \Controller\Admin\Controller
{
    /**
     * index
     * @return void
     * @throws Exception
     */
    public function index()
    {
        $getValue = Request::get()->toArray();

        try {
            $regularGoods = \App::getInstance(RegularGoods::class);

            $toggle = gd_policy('display.toggle');
            $sessionScmNo = Session::get('manager.scmNo');

            $regularGoodsInfo = [];
            $mode = '';
            if (isset($getValue['sno'])) {
                // 정기결제(배송) 상품 수정 기능
                $mode = 'modify';

                // --- 메뉴 설정
                $this->callMenu('goods', 'goods', 'regularGoodsModify');

                // find : 수정 화면에 표시할 정기결제(배송) 상품 정보
                $regularGoodsInfo = $regularGoods->getModificationFormInfo($getValue['sno']);
            } else {
                // 정기결제(배송) 상품 등록 기능

                // --- 메뉴 설정
                $this->callMenu('goods', 'goods', 'regularGoodsRegister');
            }

            // 결제 가능한 최소 가격
            $minPrice = RegularGoodsAttribute::MIN_PRICE;

            // 정기결제(배송) 할인 방법
            $discountType = RegularGoodsAttribute::DISCOUNT_TYPE;

            // 정기결제(배송) 상품의 최대 사은품 직접 입력 가능 회차
            $maxFixGiftRound = RegularGoodsAttribute::MAX_FIX_GIFT_ROUND;

            // 튜토리얼 연계 모달 경로 조회
            $helperPopupExposureAvailabilityService = \App::getInstance(HelperPopupExposureAvailabilityService::class);
            $tutorialLinkModalUrl = $helperPopupExposureAvailabilityService->getTutorialUrl(ManagerTutorialType::SUBSCRIPTION_PRODUCT_REGISTER->value);
            $this->setData('tutorialLinkModalUrl', $tutorialLinkModalUrl);
            $this->setData('tutorialCode', ManagerTutorialType::SUBSCRIPTION_PRODUCT_REGISTER->value);

            // 튜토리얼 스텝가이드 즉시 실행 여부
            $tutorialMode = $getValue['tutorialMode'] ?? 'false';
            $this->setData('tutorialMode', $tutorialMode);

            // 튜토리얼 링크 모달 사이즈 및 스텝가이드 단계 설정
            $tutorialConfig = \App::getConfig('tutorial.regularDelivery')->toArray()[ManagerTutorialType::SUBSCRIPTION_PRODUCT_REGISTER->value];
            $this->setData('tutorialLinkModalSize', $tutorialConfig['linkModalSize']);
            $this->setData('tutorialStepGuideSteps', $tutorialConfig['stepGuideSteps']);

            // 튜토리얼 진행 현황 조회
            $tutorialStepGuideLinks = $helperPopupExposureAvailabilityService->getTutorialProgress(ManagerTutorialType::SUBSCRIPTION_PRODUCT_REGISTER->value);
            $this->setData('tutorialStepGuideLinks', $tutorialStepGuideLinks);

            $this->setData('data', $regularGoodsInfo);
            $this->setData('toggle', $toggle);
            $this->setData('sessionScmNo', $sessionScmNo);
            $this->setData('discountType', $discountType);
            $this->setData('minPrice', $minPrice);
            $this->setData('maxFixGiftRound', $maxFixGiftRound);
            $this->setData('mode', $mode);
            $this->addScript([
                'jquery/jquery.multi_select_box.js',
            ]);
            $this->getView()->setPageName('goods/regular_goods_register.php');
        } catch (Exception $e) {
            throw $e;
        }

    }
}
