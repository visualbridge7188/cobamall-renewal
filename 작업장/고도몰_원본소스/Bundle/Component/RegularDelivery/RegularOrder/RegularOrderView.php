<?php

namespace Bundle\Component\RegularDelivery\RegularOrder;

use Repository\RegularDelivery\RegularGoods\RegularGoodsRepository;
use Component\Agreement\BuyerInform;
use Component\Agreement\BuyerInformCode;
use Component\Design\ReplaceCode;
use Component\Scm\ScmAdmin;
use Framework\Log\Logger;
use Framework\Security\Encryptor;
use Repository\Goods\GiftRepository;
use Repository\RegularDelivery\RegularGoods\RegularGiftPresentRepository;
use Repository\RegularDelivery\RegularGoods\RegularGiftPresentInfoRepository;

/**
 * 정기결제 신청서 화면 처리 class
 */
class RegularOrderView
{
    /**
     * @var ScmAdmin
     */
    private $scmAdmin;

    /**
     * @var BuyerInform
     */
    private $buyerInform;

    /**
     * @var ReplaceCode
     */
    private $replaceCode;

    /**
     * @var RegularGiftPresentRepository
     */
    private $regularGiftPresentRepository;
    /**
     * @var RegularGiftPresentRepository
     */
    private $regularGiftPresentInfoRepository;
    /**
     * @var Encryptor
     */
    private $encryptor;

    /**
     * @var GiftRepository
     */
    private $giftRepository;

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var RegularGoodsRepository
     */
    private $regularGoodsRepository;

    public function __construct(
        ScmAdmin $scmAdmin,
        BuyerInform $buyerInform,
        ReplaceCode $replaceCode,
        RegularGiftPresentRepository $regularGiftPresentRepository,
        RegularGiftPresentInfoRepository $regularGiftPresentInfoRepository,
        GiftRepository $giftRepository,
        RegularGoodsRepository $regularGoodsRepository,
        Encryptor $encryptor,
        Logger $logger
    )
    {
        $this->scmAdmin = $scmAdmin;
        $this->buyerInform = $buyerInform;
        $this->replaceCode = $replaceCode;
        $this->regularGiftPresentRepository = $regularGiftPresentRepository;
        $this->regularGiftPresentInfoRepository = $regularGiftPresentInfoRepository;
        $this->giftRepository = $giftRepository;
        $this->regularGoodsRepository = $regularGoodsRepository;
        $this->encryptor = $encryptor;
        $this->logger = $logger;
    }

    /**
     * 사은품 정보 조회
     * @param array $cartInfo 카트 정보
     * @return array
     */
    public function getGiftInfo(array $cartInfo): array
    {
        $result = [];

        foreach ($cartInfo as $cartList) {
            foreach ($cartList as $cartData) {
                foreach ($cartData as $cart) {
                    // 상품에 해당하는 사은품 지급 조건 조회
                    $giftPresent = $this->regularGiftPresentRepository->findGiftPresentInfoByRegularGoodsNo($cart['regularGoodsNo']);
                    if (empty($giftPresent)) {
                        continue;
                    }

                    // 지급 조건을 바탕으로
                    $giftPresentSno = $giftPresent['sno'];
                    // 수량제한 조건이 있으면 카트에 담긴 수량 체크 (추가상품이 있다면 추가상품 카운트도 더함)
                    $isQuantityLimited = $giftPresent['conditionType'] === 'quantityLimited';
                    $hasAddGoodsFl = $giftPresent['addGoodsFl'] === 'y';

                    $cnt = $isQuantityLimited ? $cart['goodsCnt'] : 0;
                    if ($isQuantityLimited && $hasAddGoodsFl && !empty($cart['addGoods'])) {
                        foreach ($cart['addGoods'] as $addGoodsInfo) {
                            $cnt += $addGoodsInfo['addGoodsCnt'];
                        }
                    }

                    // 지급 조건 번호로 받을 수 있는 사은품 관련 정보 조회
                    $presentInfo = $this->regularGiftPresentInfoRepository->findGiftPresentInfoByRegularGiftPresentSno($giftPresentSno, $cnt);
                    if (empty($presentInfo)) {
                        continue;
                    }
                    $presentInfoSno = $presentInfo['sno'];

                    // 사은품으로 받을 수 있는 상품 번호
                    $multiGiftNo = json_decode($presentInfo['multiGiftNo'], true);

                    // 사은품 상품 조회
                    $multiGift = $this->giftRepository->getAvailableGiftInfoByGiftNos($multiGiftNo);
                    if (empty($multiGift)) {
                        continue;
                    }
                    $totalCount = count($multiGift);

                    // 사은품 이미지 url 생성
                    array_walk($multiGift, function (&$giftInfo) {
                        $giftInfo['imageUrl'] = gd_html_preview_image($giftInfo['imageNm'], $giftInfo['imagePath'], $giftInfo['imageStorage'], 80, 'gift', $giftInfo['giftNm'], null, false);
                        unset($giftInfo['imageNm'], $giftInfo['imagePath'], $giftInfo['imageStorage']);
                    });

                    $result[$cart['sno']] = [
                        'regularGiftPresentInfoSno' => $presentInfoSno,
                        'goodsNo' => $cart['regularGoodsNo'],
                        'title' => $giftPresent['conditionTitle'],
                        'total' => $totalCount,
                        'gift' => [
                            [
                                'multiGiftNo' => $multiGift,
                                'selectCnt' => $presentInfo['selectCnt'],
                                'giveCnt' => $presentInfo['giveCnt'],
                                'total' => $totalCount
                            ]
                        ]
                    ];
                }
            }
        }

        $this->logger->channel('regularOrder')->info('Regular order gift info', [$result]);
        return $result;
    }

    /**
     * 공급사 관련 정보
     * @param array $cartInfo
     * @return array
     */
    public function regularOrderScmInfo(array $cartInfo): array
    {
        $scmNoList = array_keys($cartInfo);
        $hasScmGoods = false;
        $tmpScmList = [];
        foreach ($scmNoList as $scmNo) {
            if ($scmNo > 1) {
                $hasScmGoods = true;
                $tmpScmList[] = $scmNo;
            }
        }

        $scmNmList = [];
        $scmData = $this->scmAdmin->getScmSelectList(implode(INT_DIVISION, $tmpScmList));
        foreach ($scmData as $val) {
            $scmNmList[$val['scmNo']] = $val['companyNm'];
        }
        ksort($scmNmList);

        return [
            'hasScmGoods' => $hasScmGoods,
            'scmList' => $tmpScmList,
            'scmNameList' => $scmNmList
        ];
    }

    /**
     * 약관 정보
     * @param array $cartInfo
     * @return array
     */
    public function getAgreement(array $cartInfo): array
    {
        $privateAgreementInfo = $this->buyerInform->getAgreementWithReplaceCode(BuyerInformCode::PRIVATE_MEMBER_REGULAR_TERMS_CONSENT); // 정기결제(배송) 이용 약관
        $autoPgAgreementInfo = $this->buyerInform->getAgreementWithReplaceCode(BuyerInformCode::PRIVATE_MEMBER_AUTO_APPROVAL_CONSENT); // 자동결제 이용 약관
        $privateProvider = $this->buyerInform->getAgreementWithReplaceCode(BuyerInformCode::PRIVATE_PROVIDER); // 상품 공급사 개인정보 제공 동의

        $scmInfo = $this->regularOrderScmInfo($cartInfo);
        $scmNm['scmNm'] = implode(', ', $scmInfo['scmNameList']);

        $this->replaceCode->setReplaceCodeByScmAgreement($scmNm);
        $privateProvider = $this->replaceCode->replace($privateProvider);

        return [
            'privateAgreementInfo' => $privateAgreementInfo,
            'autoPgAgreementInfo' => $autoPgAgreementInfo,
            'privateProvider' => $privateProvider
        ];
    }
}
