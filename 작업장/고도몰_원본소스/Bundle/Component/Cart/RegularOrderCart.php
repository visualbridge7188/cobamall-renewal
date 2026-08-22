<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\Cart;

use Component\Database\DBTableField;
use Component\Mall\Mall;
use Component\RegularDelivery\Exception\RegularGoodsDuplicateOptionException;
use Component\Validator\Validator;
use Bundle\Component\Member\Util\MemberUtil;
use Cookie;
use Exception;
use Framework\Utility\ArrayUtils;
use Framework\Utility\NumberUtils;
use Repository\Order\CartRepository;
use Repository\Order\RegularOrderCartRepository;
use Repository\Goods\GoodsOptionTextRepository;
use Repository\Goods\GoodsOptionRepository;
use Repository\RegularDelivery\RegularGoods\RegularGoodsRepository;
use Framework\Http\Session\SessionManager;
use Util\Order\RegularOrderUtil;
use Framework\Utility\SkinUtils;
use Repository\Goods\GoodsRepository;
use Framework\Utility\Globals;
use Repository\Goods\AddGoodsRepository;
use Framework\Log\Logger;
use Component\Policy\Policy;
use Framework\Http\Request;
use Component\Goods\Goods;
use Component\Member\Member;

/**
 * 정기결제 장바구니 클래스
 * Cart 클래스와 완전히 분리된 독립 클래스
 *
 * @package Bundle\Component\Cart
 */
class RegularOrderCart
{
    /**
     * 정기결제(배송) 주문 필드 정의
     */
    const REGULAR_ORDER_FIELD = ['regularGoodsNo', 'deliveryCycleType', 'deliveryCycle', 'deliveryCycleDay', 'maxDeliveryRound'];

    /**
     * @var array 로그인한 Session의 회원 정보
     */
    protected $members = [];

    /**
     * @var string 사이트 키
     */
    protected $siteKey = '';

    /**
     * @var bool 로그인 여부
     */
    protected $isLogin = false;

    /**
     * @var array 장바구니 정책
     */
    protected $cartPolicy = [];

    /**
     * @var bool 묶음상품 체크 여부
     */
    protected $checkUseBundle = false;

    /**
     * @var array 묶음상품 갯수
     */
    protected $useBundleCnt = [];

    /**
     * @var array 현재 주문 중인 장바구니 SNO
     */
    public $cartSno = [];

    /**
     * @var int 장바구니 상품 갯수
     */
    public $cartCnt = 0;

    /**
     * @var int 장바구니 상품 수량 합계
     */
    public $cartGoodsCnt = 0;

    /**
     * @var string 쇼핑 계속하기 URL
     */
    public $shoppingUrl = '';

    /**
     * @var array 배송비 정보
     */
    public $setDeliveryInfo = [];

    /**
     * @var int 장바구니 공급사 갯수
     */
    public $cartScmCnt = 0;

    /**
     * @var array 장바구니 공급사별 상품 갯수
     */
    public $cartScmGoodsCnt = [];

    /**
     * @var array 장바구니 공급사 정보
     */
    public $cartScmInfo = [];

    /**
     * @var int 총 상품 금액
     */
    public $totalGoodsPrice = 0;

    /**
     * @var int 총 배송비
     */
    public $totalDeliveryCharge = 0;

    /**
     * @var int 총 상품 마일리지
     */
    public $totalGoodsMileage = 0;

    /**
     * @var int 총 회원 마일리지
     */
    public $totalMemberMileage = 0;

    /**
     * @var int 총 마일리지
     */
    public $totalMileage = 0;

    /**
     * @var bool 주문 가능 여부
     */
    public $orderPossible = true;

    /**
     * @var string 주문 불가 메시지
     */
    public $orderPossibleMessage = '';

    /**
     * @var array 결제수단 제한
     */
    public $payLimit = [];

    /**
     * @var array 공급사별 상품 가격
     */
    public $totalScmGoodsPrice = [];

    /**
     * @var array 공급사별 배송비
     */
    public $totalScmGoodsDeliveryCharge = [];

    /**
     * @var array 배송정책별 총 배송금액
     */
    public $totalGoodsDeliveryPolicyCharge = [];

    /**
     * @var array 지역별 추가배송비
     */
    public $totalGoodsDeliveryAreaPrice = [];

    /**
     * @var int 총 상품 할인 가격
     */
    public $totalGoodsDcPrice = 0;

    /**
     * @var int 총 회원 할인 가격
     */
    public $totalMemberDcPrice = 0;

    /**
     * @var int 총 회원 중복 할인 가격
     */
    public $totalMemberOverlapDcPrice = 0;

    /**
     * @var int 총 결제 금액
     */
    public $totalSettlePrice = 0;

    /**
     * @var array 마일리지 지급 정보
     */
    public $mileageGiveInfo = [];


    public function __construct(
        private readonly RegularGoodsRepository $regularGoodsRepository,
        private readonly GoodsOptionRepository $goodsOptionRepository,
        private readonly GoodsOptionTextRepository $goodsOptionTextRepository,
        private readonly RegularOrderCartRepository $regularOrderCartRepository,
        private readonly GoodsRepository $goodsRepository,
        private readonly CartRepository $cartRepository,
        private readonly AddGoodsRepository $addGoodsRepository,
        private readonly Mall $mall,
        private readonly SessionManager $session,
        private readonly Logger $logger,
        private readonly Policy $policy,
        private readonly Request $request,
        private readonly Member $member
    )
    {
        $this->initMemberInfo();
        // 마일리지 지급 정보
        $this->mileageGiveInfo = gd_mileage_give_info();
    }

    /**
     * 회원 정보 초기화
     */
    protected function initMemberInfo(): void
    {
        // 회원 로그인 여부
        $this->isLogin = gd_is_login();

        // 회원정보 생성
        $this->members = [
            'memNo' => $this->session->get('member.memNo') ?? 0,
            'groupSno' => $this->session->get('member.groupSno') ?? 0,
            'adultFl' => $this->session->get('member.adultFl') ?? 'n',
        ];

        // 사이트키 설정
        $this->siteKey = $this->session->get('siteKey') ?? '';

        // 장바구니 정책 설정
        $this->cartPolicy = $this->policy->getValue('order.cart');
        $this->memInfo = $this->member->getMemberInfo();
    }

    /**
     * 정기결제 장바구니 바로구매 상품 삭제
     * 정기결제는 회원만 가능하므로 회원 기준으로만 삭제
     *
     * @return void
     */
    public function setDeleteDirectCartCont(): void
    {
        if (empty($this->members['memNo'])) {
            return;
        }

        $this->regularOrderCartRepository->deleteDirectCartByMemNo($this->members['memNo']);
    }

    /**
     * 정기결제 장바구니 정보 조회
     *
     * @param int $cartSno 장바구니 sno
     * @param bool $dataArray 배열 반환 여부
     * @param bool $stripSlashesFl stripslashes 사용 여부
     * @return array 장바구니 정보
     */
    public function getCartInfo(int $cartSno, bool $dataArray = false, bool $stripSlashesFl = true)
    {
        // 정기결제는 회원만 가능
        if (empty($this->members['memNo'])) {
            return [];
        }

        $getData = $this->regularOrderCartRepository->findRegularOrderCartInfoBySno($this->members['memNo'], $cartSno);

        if ($stripSlashesFl === true) {
            if (count($getData) == 1 && $dataArray === false) {
                return gd_htmlspecialchars_stripslashes($getData[0]);
            }

            return gd_htmlspecialchars_stripslashes($getData);
        } else {
            if (count($getData) == 1 && $dataArray === false) {
                return $getData[0];
            }

            return $getData;
        }
    }

    /**
     * 정기결제 장바구니 상품 삭제
     *
     * @param array $getData 장바구니 sno 배열
     * @return bool 결과
     */
    public function setCartDelete(array $getData): bool
    {
        if (empty($getData) === true) {
            return false;
        }

        // 정기결제는 회원만 가능
        if (empty($this->members['memNo'])) {
            return false;
        }

        // 장바구니 sno로 goodsNo 조회 - 갯수 변경 처리를 위해 추출 (group by로 중복 제거)
        $changeGoodsNoArray = $this->regularOrderCartRepository->findGoodsNosBySnos($getData);

        // 크리마 사용일 경우 삭제 장바구니 sno 기록
        $crema = \App::load('Component\\Service\\Crema');
        $cremaBind = [
            'cartSno' => $getData
        ];
        $crema->insertDeletedCartData($cremaBind);

        // 장바구니 삭제
        $this->regularOrderCartRepository->deleteBySnos($getData, $this->members['memNo']);

        // 장바구니 갯수 변경 처리
        $goods = \App::load(\Component\Goods\Goods::class);
        foreach ($changeGoodsNoArray as $goodsNo) {
            $goods->setCartGoodsCount($goodsNo);
        }

        return true;
    }

    /**
     * 정기결제 장바구니에 상품 저장
     *
     * @param array $arrData 상품 정보
     * @return array 저장된 장바구니 sno 목록
     * @throws Exception
     */
    public function saveInfoCart(array $arrData): array
    {
        // 정기결제는 회원만 가능
        if (empty($this->members['memNo'])) {
            throw new Exception(__('로그인이 필요합니다.'));
        }
        $arrayRtn = [];
        // 장바구니 테이블 필드
        $arrExclude = [
            'siteKey',
            'memNo',
            'directCart',
        ];
        $fieldData = DBTableField::setTableField('tableRegularOrderCart', null, $arrExclude);

        // 상품 번호를 기준으로 장바구니에 담을 상품의 배열을 처리함
        foreach ($arrData['goodsNo'] as $goodsIdx => $goodsNo) {
            foreach ($fieldData as $field) {
                $getData[$field] = $arrData[$field][$goodsIdx];
            }

            // 정기상품 옵션 중복 검증 (바로구매가 아닌 경우만)
            if ($arrData['cartMode'] !== 'd') {
                $this->validateRegularGoodsData($goodsIdx, $goodsNo, $arrData);
            }

            $getData['mallSno'] = $this->mall->getSession('sno') ?: DEFAULT_MALL_NUMBER;
            $getData['cartMode'] = $arrData['cartMode'];
            $getData['linkMainTheme'] = $arrData['linkMainTheme'] ?? null;
            $getData['deliveryCollectFl'] = $arrData['deliveryCollectFl'] ?? null;
            $getData['deliveryMethodFl'] = $arrData['deliveryMethodFl'] ?? null;
            $getData['goodsPrice'] = $arrData['set_total_price'] ?? null;

            // 정기결제 관련 데이터 설정
            $getData['regularGoodsNo'] = $arrData['regularGoodsNo'] ?? null;
            $getData['deliveryCycleType'] = $arrData['deliveryCycleTypes'][$goodsIdx] ?? null;
            $getData['deliveryCycle'] = $arrData['deliveryCycles'][$goodsIdx] ?? null;
            $getData['deliveryCycleDay'] = $arrData['deliveryCycleDays'][$goodsIdx] ?? null;
            $getData['maxDeliveryRound'] = $arrData['maxDeliveryRounds'][$goodsIdx] ?? null;
            
            // 장바구니에 담기
            $arrayRtn[] = $this->saveGoodsToRegularCart($getData);
        }

        return $arrayRtn;
    }

    /**
     * 정기결제 장바구니에 상품 저장
     *
     * @param array $arrData 상품 정보
     * @return int 장바구니 sno
     * @throws Exception
     */
    protected function saveGoodsToRegularCart(array $arrData): int
    {
        // Validation - 상품 코드 체크
        if (Validator::required($arrData['goodsNo'], true) === false) {
            throw new Exception(__('상품번호를 확인 할 수 없어 처리되지 않았습니다.'));
        }

        // Validation - 상품 가격/옵션 체크
        if (Validator::required($arrData['optionSno'], true) === false) {
            //throw new Exception(__('상품 가격 코드를 확인할 수 없어 처리되지 않았습니다.'));
        }

        // Validation - 상품 수량 체크
        if (Validator::number($arrData['goodsCnt'], 1, null, true) === false) {
            throw new Exception(__('상품 수량 이상으로 장바구니에 해당 상품을 담을 수 없습니다.'));
        }

        // 바로구매의 경우 무게별 배송비를 사용하는 상품일 경우 범위제한을 체크하여 결제를 방지한다.
        if ($arrData['cartMode'] == 'd') {
            $checkGoodsWeightMessage = '';
            $checkGoodsWeightMessage = $this->checkGoodsWeight($arrData);
            if($checkGoodsWeightMessage !== ''){
                throw new Exception(__('무게가 %s 이상의 상품은 구매할 수 없습니다. (배송범위 제한)', $checkGoodsWeightMessage));
            }
        }

        // 금액 0원 상품 체크
        if ($this->cartPolicy['zeroPriceOrderFl'] === 'n' && intval($arrData['goodsPrice']) === 0) {
            throw new Exception(__('상품 가격이 없습니다. 확인해 주세요.'));
        }

        // 상품 텍스트 옵션
        if (gd_isset($arrData['optionText']) && empty($arrData['optionText']) === false) {
            $arrData['optionText'] = ArrayUtils::removeEmpty($arrData['optionText']);
            $arrData['optionText'] = json_encode($arrData['optionText'], JSON_UNESCAPED_UNICODE);
        } else {
            $arrData['optionText'] = '';
        }

        // 추가 상품
        if (gd_isset($arrData['addGoodsNo']) && empty($arrData['addGoodsNo']) === false) {
            // 자연수가 아닐 경우 예외 발생
            array_map(fn($cnt) => NumberUtils::checkNaturalNumber($cnt), $arrData['addGoodsCnt']);
            $arrData['addGoodsNo'] = json_encode(array_filter($arrData['addGoodsNo']));
            $arrData['addGoodsCnt'] = json_encode(array_filter($arrData['addGoodsCnt']));
        }

        // 사이트키 및 회원 번호
        $arrData['siteKey'] = $this->siteKey;
        if ($this->isLogin === true) {
            $arrData['memNo'] = $this->members['memNo'];
        } else {
            $arrData['memNo'] = 0;
        }

        // 바로 구매 체크
        if ($arrData['cartMode'] == 'd') {
            $arrData['directCart'] = 'y';
            $check['cnt'] = 0;

            // 바로 구매 쿠키 생성
            Cookie::set('isDirectCart', true, 0, '/');

        } else {
            // 장바구니 상품 갯수 체크 (중복상품이 있는 경우 무시)
            if ($this->cartPolicy['goodsLimitFl'] == 'y') {
                if ($this->getCartGoodsCnt() >= $this->cartPolicy['goodsLimitCnt']) {
                    throw new Exception(sprintf(__('장바구니 보관상품은 %d 개까지 보관하실 수 있습니다.'), $this->cartPolicy['goodsLimitCnt']));
                }
            }
            // 바로구매가 아님
            $arrData['directCart'] = 'n';

            // 바로 구매 쿠키 삭제
            if (Cookie::has('isDirectCart')) {
                Cookie::del('isDirectCart');
            }
        }

        // 장바구니 담기
        unset($arrData['cartMode'], $arrData['goodsPrice']);
        $return = $this->setInsertCart($arrData);

        return $return;
    }

    /**
     * 장바구니 상품 DB insert
     *
     * @param array $arrData 상품 정보
     * @return int 장바구니 sno
     */
    protected function setInsertCart(array $arrData): int
    {
        // 해당 상품의 구매가능 수량
        $arrData['goodsCnt'] = $this->getBuyableStock($arrData['goodsNo'], $arrData['goodsCnt'], $arrData['useBundleGoods'] ?? '');

        // 등록일시 설정
        $arrData['regDt'] = date('Y-m-d H:i:s');

        // 장바구니 저장
        $cartSno = $this->regularOrderCartRepository->saveCart($arrData);

        // 장바구니 변경 갯수 상품 업데이트
        $goods = \App::load(\Component\Goods\Goods::class);
        $goods->setCartGoodsCount($arrData['goodsNo']);

        return $cartSno;
    }

    /**
     * 최소 구매수량 / 최대 구매 수량 체크해서 실제 구매가능한 수량을 반환
     *
     * @param int $goodsNo 상품 번호
     * @param int $goodsCnt 해당 상품이 현재 장바구니에 담긴 수량
     * @param string $useBundleGoods 묶음상품 사용 여부
     * @return int 상품 수량
     */
    protected function getBuyableStock(int $goodsNo, int $goodsCnt, string $useBundleGoods = ''): int
    {
        $getData = $this->goodsRepository->findSalesInfoByGoodsNo($goodsNo);

        if ($this->checkUseBundle !== true) {
            $this->useBundleCnt = $this->regularOrderCartRepository->countUseBundleGoods();
            $this->checkUseBundle = true;
        }

        //묶음주문패치시 해당 내용 적용 안함
        if (empty($useBundleGoods) === true) {
            if ($this->useBundleCnt == 0) {
                // 최대 구매수량이 있는경우
                if ($getData['maxOrderCnt'] > 0) {
                    if ($goodsCnt > $getData['maxOrderCnt']) { // 최대 구매수량 보다 큰 경우 수량은 최대 구매수량으로
                        $goodsCnt = $getData['maxOrderCnt'];
                    }
                }

                if ($goodsCnt < $getData['minOrderCnt']) { // 최소 구매수량 보다 작은 경우 수량은 최소 구매수량으로
                    $goodsCnt = $getData['minOrderCnt'];
                }
            }
        }
        if ($goodsCnt <= 0) {
            $goodsCnt = 1;
        }

        return $goodsCnt;
    }

    /**
     * 무게별 배송비 체크
     *
     * @param array $arrData 상품 정보
     * @return string 무게 제한 메시지
     */
    protected function checkGoodsWeight(array $arrData): string
    {
        $getData = $this->goodsRepository->findDeliveryWeightInfoByGoodsNo($arrData['goodsNo']);

        // 무게별 배송비를 사용중이고 범위 제한을 사용하는경우 체크
        if ($getData['fixFl'] === 'weight' && $getData['rangeLimitFl'] === 'y'){
            if (($arrData['goodsCnt'] * $getData['goodsWeight']) > $getData['rangeLimitWeight']) {
                $weight = \Globals::get('gWeight');

                return $getData['rangeLimitWeight'] . $weight['unit'];
            }
        }

        return '';
    }

    /**
     * 장바구니 상품 갯수 조회
     *
     * @return int 장바구니 상품 갯수
     */
    protected function getCartGoodsCnt(): int
    {
        return $this->regularOrderCartRepository->getCartCount($this->members['memNo']);
    }

    /**
     * 정기결제(배송) 을 사용하는지 (설정 및 로그인 유무)
     * @return bool
     */
    public function getRegularDeliveryUseFl(): bool
    {
        // 정기결제 설정 켜져 있고 로그인 되어 있는지 + 국내몰만
        $mallSno = $this->mall->getSession('sno');
        $mallSno = gd_isset($mallSno, DEFAULT_MALL_NUMBER);
        if ($mallSno !== DEFAULT_MALL_NUMBER) {
            return false; // 국내몰이 아닌 경우
        }
        $orderBasicPolicy = $this->policy->getValue('order.basic');
        return $orderBasicPolicy['useRegularDelivery'] === 'y' && MemberUtil::isLogin();
    }

    /**
     * 카트에 담긴 상품에 대한 배송예정일 구하기
     * @param array $regularCartInfo
     * @return array
     * @throws \Exception
     */
    public function getDeliveryDueDates(array $regularCartInfo): array
    {
        $deliveryInfo = [];
        $today = date('Y-m-d');

        foreach ($regularCartInfo as $cartData) {
            foreach ($cartData as $deliveryData) {
                foreach ($deliveryData as $delivery) {
                    $deliveryInfo[$delivery['sno']][$delivery['regularGoodsNo']] = RegularOrderUtil::calculateOrderDate(
                        $today, $delivery['deliveryCycleType'], $delivery['deliveryCycle'], $delivery['deliveryCycleDay'], true
                    )[0]; // 0 : 배송예정일 / 1 : 주문생성일
                }
            }
        }

        return $deliveryInfo;
    }

    /**
     * 정기결제 상품 가격 반환
     *
     * @param array $regularCartInfo
     * @return array
     * @throws \Exception
     */
    public function getRegularGoodsPrice(array $regularCartInfo): array
    {
        // 카트 테이블에서 가져온 정보를 토대로 상품가격을 계산해야 함
        $regularGoodsPriceList = []; // 장바구니 cart sno 별 상품 가격
        $regularGoodsPriceOptionList = []; // 장바구니 cart sno 별 옵션 가격
        $regularGoodsPriceOptionTextList = []; // 장바구니 cart sno 별 텍스트 옵션 가격
        $regularGoodsPriceOptionTextTotalList = []; // 장바구니 cart sno 별 텍스트 옵션 총 가격
        $regularAddGoodsPriceList = []; // 장바구니 cart sno + 추가상품 번호 별 추가상품 가격
        $regularTotalPriceList = []; // 장바구니 cart sno 별 총 가격
        $regularScmGoodsPriceList = []; // 공급사 번호 별 공급사 총 가격
        $regularGoodsDiscountUseFl = []; // 장바구니 cart sno 별 정기결제 할인 사용 여부
        $totalPrice = 0;
        foreach ($regularCartInfo as $scmNo => $cartInfoList) {
            $regularScmGoodsPriceList[$scmNo] = 0;
            foreach ($cartInfoList as $cartInfo) {
                foreach ($cartInfo as $cartData) {

                    // 상품가 (이미 정기결제 할인 붙은 가격)
                    $regularGoods = $this->regularGoodsRepository->findRegularGoodsInfoByRegularGoodsNoList($cartData['regularGoodsNo']);
                    if (empty($regularGoods)) {
                        throw new \Exception('정기결제 상품 정보가 없습니다. regularGoodsNo: ' . $cartData['regularGoodsNo']);
                    }
                    $regularPrice = $regularGoods['regularPrice'];
                    $discountFl = $regularGoods['discountUseFl'];

                    // 옵션 가
                    $optionInfo = $this->goodsOptionRepository->findGoodsOptionByOptionSno($cartData['optionSno']);
                    $regularOptionPrice = 0;
                    if (!empty($optionInfo)) {
                        // 정기결제 할인 계산
                        $regularOptionPrice = RegularOrderUtil::calculateRegularOptionPrice(
                            $optionInfo['optionPrice'], $regularGoods['discountUseFl'], $regularGoods['discountType'], $regularGoods['discountRate']);
                    }

                    // 텍스트 옵션 가
                    $regularOptionTextPrice = 0;
                    if (!empty($cartData['optionText'])) {
                        foreach ($cartData['optionText'] as $optionTextInfo) {
                            // 텍스트 옵션 정기결제 할인 계산
                            $optionTextPrice = RegularOrderUtil::calculateRegularOptionPrice(
                                $optionTextInfo['optionTextPrice'] ?? 0, $regularGoods['discountUseFl'], $regularGoods['discountType'], $regularGoods['discountRate']);
                            // 텍스트 옵션 정기결제 할인 총 금액
                            $regularOptionTextPrice += $optionTextPrice;
                            $regularGoodsPriceOptionTextList[$optionTextInfo['optionSno']] = $optionTextPrice;
                        }
                    }
                    // 정기결제 상품가 + 옵션가 + 텍스트 옵션가
                    $regularGoodsPriceList[$cartData['sno']] = $regularPrice;
                    $regularGoodsPriceOptionList[$cartData['sno']] = $regularOptionPrice;
                    $regularGoodsPriceOptionTextTotalList[$cartData['sno']] = $regularOptionTextPrice;
                    $regularGoodsDiscountUseFl[$cartData['sno']] = $discountFl;

                    // 추가상품가 (추가상품은 별도로 변수에 저장)
                    $regularAddGoodsPrice = 0;
                    if (!empty($cartData['addGoods'])) {
                        foreach ($cartData['addGoods'] as $addGoodsInfo) {
                            $regularAddGoodsPriceList[$cartData['sno']][$addGoodsInfo['addGoodsNo']] = $addGoodsInfo['addGoodsPrice'];
                            $regularAddGoodsPrice += ($addGoodsInfo['addGoodsPrice'] * $addGoodsInfo['addGoodsCnt']);
                        }
                    }

                    // 주문 상품별 총 가격
                    $regularTotalPriceList[$cartData['sno']] = ((($regularPrice + $regularOptionPrice + $regularOptionTextPrice) * $cartData['goodsCnt']) + $regularAddGoodsPrice);

                    // 모든 주문 상품을 합친 총 가격
                    $totalPrice += ((($regularPrice + $regularOptionPrice + $regularOptionTextPrice) * $cartData['goodsCnt']) + $regularAddGoodsPrice);

                    // 공급사 번호에 총 가격 할당
                    $regularScmGoodsPriceList[$scmNo] += (($regularPrice + $regularOptionPrice + $regularOptionTextPrice) * $cartData['goodsCnt']) + $regularAddGoodsPrice;
                }
            }
        }

        return [
            'regularGoodsPriceList' => $regularGoodsPriceList,
            'regularGoodsPriceOptionList' => $regularGoodsPriceOptionList,
            'regularGoodsPriceOptionTextList' => $regularGoodsPriceOptionTextList,
            'regularGoodsPriceOptionTextTotalList' => $regularGoodsPriceOptionTextTotalList,
            'regularAddGoodsPriceList' => $regularAddGoodsPriceList,
            'regularTotalPriceList' => $regularTotalPriceList,
            'regularTotalPrice' => $totalPrice,
            'regularScmGoodsPriceList' => $regularScmGoodsPriceList,
            'regularGoodsDiscountUseFl' => $regularGoodsDiscountUseFl,
        ];
    }

    /**
     * 장바구니 정기결제 탭의 활성화를 위해
     * 1. 장바구니에 마지막에 담긴 상품이 정기결제 상품인지 (cart vs regularOrderCart 비교)
     * 2. 신청서 페이지에서 넘어 왔는지
     *
     * @param int $memNo
     * @return bool
     */
    public function isRegularOrderTabActive(int $memNo): bool
    {
        // 신청서 페이지에서 넘어온 경우 정기결제 탭 활성화
        $referer = $this->request->getReferer();
        $isFromRegularOrderPage = (str_contains($referer, 'order/regular_order') || str_contains($referer, 'order/regular_order_ps'));
        if ($isFromRegularOrderPage) {
            return true;
        }

        // 각 장바구니의 마지막 날짜 조회 (modDt 없으면 regDt)
        $cartLastDate = $this->cartRepository->findLastCartDate($memNo);
        $regularCartLastDate = $this->regularOrderCartRepository->findLastCartDate($memNo);

        // regularOrderCart만 있으면 true
        if (empty($cartLastDate) && !empty($regularCartLastDate)) {
            return true;
        }

        // cart만 있으면 false
        if (!empty($cartLastDate) && empty($regularCartLastDate)) {
            return false;
        }

        // 둘다 없으면 false
        if (empty($cartLastDate) && empty($regularCartLastDate)) {
            return false;
        }

        // 둘 다 있으면 날짜 비교 (regularOrderCart가 더 최근이면 true)
        return strtotime($regularCartLastDate) >= strtotime($cartLastDate);
    }

    /**
     * 정기결제 장바구니 상품 정보 조회
     *
     * @param array|null $cartIdx 조회할 장바구니 sno 목록
     * @param array|null $address 배송지 정보
     * @return array 장바구니 상품 정보
     * @throws Exception
     */
    public function getCartGoodsData(?array $cartIdx = null, ?array $address = null): array
    {
        // 회원 로그인 체크 - 정기결제는 회원만 가능
        if ($this->isLogin !== true || empty($this->members['memNo'])) {
            return [];
        }

        // 바로 구매 여부 확인
        $isDirectCart = false;
        if (Cookie::has('isDirectCart')
            && $this->request->getFileUri() != 'cart.php'
        ) {
            $isDirectCart = true;
        } else {
            if (Cookie::has('isDirectCart')) {
                Cookie::del('isDirectCart');
            }
        }

        // 장바구니 sno 배열로 변환
        $cartIdxArray = null;
        if (!empty($cartIdx)) {
            $cartIdxArray = is_array($cartIdx) ? $cartIdx : [$cartIdx];
        }

        // 장바구니 상품 정보 조회
        $cartGoodsData = $this->regularOrderCartRepository->findCartGoodsWithDetails(
            $this->members['memNo'],
            $cartIdxArray,
            $isDirectCart
        );

        // 상품리스트가 없는 경우
        if (empty($cartGoodsData) && $this->request->getFileUri() != 'cart.php') {
            throw new Exception(__('장바구니에 상품이 없습니다.'));
        }

        // 삭제 상품에 대한 cartNo
        $delCartSno = [];

        // 품절상품 설정
        if ($this->request->isMobile()) {
            $soldoutDisplay = $this->policy->getValue('soldout.mobile');
        } else {
            $soldoutDisplay = $this->policy->getValue('soldout.pc');
        }

        // 상품 가격 노출 관련
        $goodsPriceDisplayFl = $this->policy->getValue('goods.display')['priceFl'];

        $goodsKey = [];
        $prevGoodsNo = [];
        $goods = \App::load(\Component\Goods\Goods::class);
        $goodsBenefit = \App::load('\\Component\\Goods\\GoodsBenefit');
        $delivery = \App::load('\\Component\\Delivery\\Delivery');
        $getData = [];

        $tmpStock = [];
        foreach ($cartGoodsData as $data) {
            // 상품혜택 사용시 해당 변수 재설정
            $data = $goodsBenefit->goodsDataFrontConvert($data);

            // stripcslashes 처리
            $aCheckKey = ['optionText'];
            foreach ($data as $k => $v) {
                if (!in_array($k, $aCheckKey)) {
                    $data[$k] = gd_htmlspecialchars_stripslashes($v);
                }
            }

            // 전체상품 수량
            $this->cartGoodsCnt += $data['goodsCnt'];

            // 기준몰 상품명 저장 (무조건 기준몰 상품명이 저장되도록)
            $data['goodsNmStandard'] = $data['goodsNm'];

            // 상품 카테고리 정보
            $data['cateAllCd'] = $goods->getGoodsLinkCategory($data['goodsNo']);

            // 상품 삭제 여부에 따른 처리
            if ($data['delFl'] === 'y') {
                $delCartSno[] = $data['sno'];
                unset($data);
                continue;
            } else {
                unset($data['delFl']);
            }

            // 텍스트옵션 상품 정보
            $goodsOptionText = $goods->getGoodsOptionText($data['goodsNo']);
            if (empty($data['optionText']) === false && gd_isset($goodsOptionText)) {
                $optionTextKey = gd_array_keys(json_decode($data['optionText'], true));
                foreach ($goodsOptionText as $goodsOptionTextInfo) {
                    if (gd_in_array($goodsOptionTextInfo['sno'], $optionTextKey) === true) {
                        $data['optionTextInfo'][$goodsOptionTextInfo['sno']] = [
                            'optionSno' => $goodsOptionTextInfo['sno'],
                            'optionName' => $goodsOptionTextInfo['optionName'],
                            'baseOptionTextPrice' => $goodsOptionTextInfo['addPrice'],
                        ];
                    }
                }
            }

            // 추가 상품 정보
            $data['addGoodsMustFl'] = $mustFl = json_decode(gd_htmlspecialchars_stripslashes($data['addGoods']), true);
            if ($data['addGoodsFl'] === 'y' && empty($data['addGoodsNo']) === false) {
                $data['addGoodsNo'] = json_decode($data['addGoodsNo'], true);
                $data['addGoodsCnt'] = json_decode($data['addGoodsCnt'], true);
            } else {
                $data['addGoodsNo'] = '';
                $data['addGoodsCnt'] = '';
            }

            // 텍스트 옵션 정보 (sno, value)
            $data['optionTextSno'] = [];
            $data['optionTextStr'] = [];
            if ($data['optionTextFl'] === 'y' && empty($data['optionText']) === false) {
                $arrText = json_decode($data['optionText']);
                foreach ($arrText as $key => $val) {
                    $data['optionTextSno'][] = $key;
                    $data['optionTextStr'][$key] = $val;
                }
            }

            // 텍스트옵션 필수 사용 여부
            if ($data['optionTextFl'] === 'y') {
                if (gd_isset($goodsOptionText)) {
                    foreach ($goodsOptionText as $k => $v) {
                        if ($v['mustFl'] == 'y' && !in_array($v['sno'], $data['optionTextSno'])) {
                            $data['optionTextEnteredFl'] = 'n';
                        }
                    }
                }
            }

            unset($optionText);

            // 상품 구매 가능 여부 체크
            $data = $this->checkOrderPossible($data, $delCartSno);

            // 삭제 대상인 경우 스킵
            if ($data === null) {
                continue;
            }

            //구매불가 대체 문구 관련
            if($data['goodsPermissionPriceStringFl'] =='y' && $data['goodsPermission'] !='all' && (($data['goodsPermission'] =='member'  && $this->isLogin === false) || ($data['goodsPermission'] =='group'  && !in_array($this->memInfo['groupSno'],explode(INT_DIVISION,$data['goodsPermissionGroup']))))) {
                $data['goodsPriceString'] = $data['goodsPermissionPriceString'];
            }

            // 품절일경우 가격대체 문구 설정
            if (($data['soldOutFl'] === 'y' || ($data['soldOutFl'] === 'n' && $data['stockFl'] === 'y' && ($data['totalStock'] <= 0 || $data['totalStock'] < $data['goodsCnt']))) && $soldoutDisplay['soldout_price'] != 'price') {
                if ($soldoutDisplay['soldout_price'] == 'text') {
                    $data['goodsPriceString'] = $soldoutDisplay['soldout_price_text'];
                } elseif ($soldoutDisplay['soldout_price'] == 'custom') {
                    $data['goodsPriceString'] = "<img src='" . $soldoutDisplay['soldout_price_img'] . "'>";
                }
            }

            $data['goodsPriceDisplayFl'] = 'y';
            if (empty($data['goodsPriceString']) === false && $goodsPriceDisplayFl == 'n') {
                $data['goodsPriceDisplayFl'] = 'n';
            }

            // 정책설정에서 품절상품 보관설정의 보관상품 품절시 자동삭제로 설정한 경우
            if ($this->cartPolicy['soldOutFl'] == 'n' && $data['orderPossibleCode'] == 'SOLD_OUT') {
                $delCartSno[] = $data['sno'];
                unset($data);
                continue;
            }

            // 배송방식에 관한 데이터
            $data['goodsDeliveryMethodFl'] = $data['deliveryMethodFl'];
            $data['goodsDeliveryMethodFlText'] = gd_get_delivery_method_display($data['deliveryMethodFl']);
            $data['deliveryMethodVisitArea'] = '';
            $deliveryData = $delivery->getSnoDeliveryBasic($data['deliverySno']);
            $data['deliveryMethodVisitArea'] = $delivery->getVisitAddress($data['deliverySno'], true);
            $data['goodsDeliveryFl'] = $deliveryData['goodsDeliveryFl'];
            $data['sameGoodsDeliveryFl'] = $deliveryData['sameGoodsDeliveryFl'];

            // 옵션명 설정
            $tmpOptionName = [];
            for ($optionKey = 1; $optionKey <= 5; $optionKey++) {
                if (empty($data['optionValue' . $optionKey]) === false) {
                    $tmpOptionName[] = $data['optionValue' . $optionKey];
                }
            }
            $data['optionNm'] = @gd_implode('/', $tmpOptionName);
            unset($tmpOptionName);

            if (gd_in_array($data['goodsNo'], $goodsKey) === false) {
                $goodsKey[] = $data['goodsNo'];
            }
            $data['goodsKey'] = gd_array_search($data['goodsNo'], $goodsKey);

            // 현재 주문 중인 장바구니 SNO
            $this->cartSno[] = $data['sno'];

            // 쇼핑 계속하기 주소 처리
            if ($data['cateCd'] && empty($this->shoppingUrl) === true) {
                $this->shoppingUrl = $data['cateCd'];
            }

            if (gd_in_array($data['goodsNo'], $prevGoodsNo) === false) {
                $data['equalGoodsNo'] = true;
                $prevGoodsNo[] = $data['goodsNo'];
            }

            // 상품 이미지 처리
            $imageSize = SkinUtils::getGoodsImageSize('list');
            $imageConf = $this->policy->getValue('goods.image');

            if ($this->request->isMobile() || $imageConf['imageType'] != 'fixed') {
                $imageSize['size1'] = '40';
                $imageSize['hsize1'] = '';
            }

            if ($data['onlyAdultFl'] == 'y' && gd_check_adult() === false && $data['onlyAdultImageFl'] == 'n') {
                if ($this->request->isMobile()) {
                    $data['goodsImageSrc'] = "/data/icon/goods_icon/only_adult_mobile.png";
                } else {
                    $data['goodsImageSrc'] = "/data/icon/goods_icon/only_adult_pc.png";
                }
                $data['goodsImage'] = SkinUtils::makeImageTag($data['goodsImageSrc'], $imageSize['size1']);
            } else {
                $data['goodsImage'] = gd_html_preview_image($data['goodsImageStorage'] == 'obs' ? $data['imageUrl'] : $data['imageName'], $data['imagePath'], $data['imageStorage'], $imageSize['size1'], 'goods', $data['goodsNm'], 'class="imgsize-s"', false, false, $imageSize['hsize1']);
            }

            unset($data['imageStorage'], $data['imagePath'], $data['imageName'], $data['imageUrl']);

            // 상품결제 수단에 따른 주문페이지 결제수단 표기용 데이터
            if ($data['payLimitFl'] == 'y' && gd_isset($data['payLimit'])) {
                $payLimit = explode(\STR_DIVISION, $data['payLimit']);
                $data['payLimit'] = $payLimit;

                if (is_array($payLimit) && $this->payLimit) {
                    $this->payLimit = gd_array_intersect($this->payLimit, $payLimit);
                    if (empty($this->payLimit) === true) {
                        $this->payLimit = ['false'];
                    }
                } else {
                    $this->payLimit = $payLimit;
                }
            }

            // 중복 상품 재고 체크
            $data['duplicationGoods'] = 'n';
            if (isset($tmpStock[$data['goodsNo']][$data['optionSno']][$data['optionText']]) === false) {
                $tmpStock[$data['goodsNo']][$data['optionSno']][$data['optionText']] = $data['goodsCnt'];
            } else {
                $data['duplicationGoods'] = 'y';
                $chkStock = $tmpStock[$data['goodsNo']][$data['optionSno']][$data['optionText']] + $data['goodsCnt'];
                if ($data['stockFl'] == 'y' && $data['stockCnt'] < $chkStock) {
                    $this->orderPossible = false;
                    $data['stockOver'] = 'y';
                }
            }

            // 상품구분 초기화
            $data['goodsType'] = 'goods';

            // 혜택 제외 플래그 초기화
            $data['goodsMileageExcept'] = 'n';
            $data['couponBenefitExcept'] = 'n';
            $data['memberBenefitExcept'] = 'n';

            $getData[] = $data;
            unset($data);
        }

        // 삭제 상품이 있는 경우 해당 장바구니 삭제
        if (empty($delCartSno) === false) {
            $this->regularOrderCartRepository->deleteBySnos($delCartSno);
        }

        // 쇼핑계속하기 버튼
        if (empty($this->shoppingUrl) === true) {
            $this->shoppingUrl = URI_OVERSEAS_HOME;
        } else {
            $this->shoppingUrl = URI_OVERSEAS_HOME . 'goods/goods_list.php?cateCd=' . $this->shoppingUrl;
        }

        // 장바구니 상품에 대한 계산된 정보
        $getCart = $this->getCartDataInfo($getData);

        // 배송비 정보 계산
        if (is_array($getCart) && !empty($getCart)) {
            $getCart = $this->getDeliveryDataInfo($getCart, gd_array_column($getData, 'deliverySno'), $address);
        }

        // 장바구니 SCM 정보
        if (is_array($getCart)) {
            $scmClass = \App::load(\Component\Scm\Scm::class);
            $this->cartScmCnt = gd_count($getCart);
            $this->cartScmInfo = $scmClass->getCartScmInfo(gd_array_keys($getCart));
        }

        // 장바구니 갯수
        $this->cartCnt = count($this->cartSno);

        // 총 결제 금액
        $this->totalSettlePrice = $this->totalGoodsPrice + $this->totalDeliveryCharge - $this->totalGoodsDcPrice - $this->totalMemberDcPrice - $this->totalMemberOverlapDcPrice;
        if ($this->totalSettlePrice < 0) {
            $this->totalSettlePrice = 0;
        }

        // 총 적립 마일리지 (상품별 총 상품 마일리지 + 회원 그룹 총 마일리지)
        $this->totalMileage = $this->totalGoodsMileage + $this->totalMemberMileage;

        unset($getData);

        return $getCart;
    }

    /**
     * 장바구니 상품 정보 (배송비와 공급사 기준으로 재정의)
     *
     * @param array $getData 장바구니 기본 정보
     * @return array 가공된 장바구니 상품 정보
     */
    protected function getCartDataInfo(array $getData): array
    {
        if (empty($getData)) {
            return [];
        }

        // 상품데이터를 이용해 상품번호, 배송번호, 추가상품, 텍스트옵션 별도 추출
        $arrTmp = [];
        foreach (ArrayUtils::removeEmpty(gd_array_column($getData, 'addGoodsNo')) as $val) {
            foreach ($val as $cKey => $cVal) {
                $arrTmp['addGoodsNo'][] = $cVal;
            }
        }
        foreach (ArrayUtils::removeEmpty(gd_array_column($getData, 'optionTextSno')) as $key => $val) {
            foreach ($val as $cKey => $cVal) {
                $arrTmp['optionText'][] = $cVal;
            }
        }

        // 추가상품 정보
        $arrAddGoods = [];
        if (empty($arrTmp['addGoodsNo']) === false) {
            $arrAddGoods = $this->getAddGoodsInfo($arrTmp['addGoodsNo']);
        }

        // 텍스트옵션 정보
        $arrOptionText = [];
        if (empty($arrTmp['optionText']) === false) {
            $arrOptionText = $this->getOptionTextInfo($arrTmp['optionText']);
        }

        // 절사 정책
        $truncGoods = \Globals::get('gTrunc.goods');

        // 장바구니 재정의
        $getCart = [];
        foreach ($getData as $key => $data) {
            $scmNo = $data['scmNo'];
            $deliverySno = $data['deliverySno'];

            // 공급사별 상품 갯수
            if (isset($this->cartScmGoodsCnt[$scmNo]) === false) {
                $this->cartScmGoodsCnt[$scmNo] = 0;
            }
            $this->cartScmGoodsCnt[$scmNo]++;

            // 옵션 가격 계산
            $data['optionPrice'] = $data['optionPrice'] ?? 0;

            // 상품 할인 금액 계산
            $goodsDcPrice = 0;
            if ($data['goodsDiscountFl'] === 'y' && $data['goodsDiscount'] > 0) {
                if ($data['goodsDiscountUnit'] === '%') {
                    $goodsDcPrice = gd_number_figure(($data['goodsPrice'] * $data['goodsDiscount'] / 100), $truncGoods['unitPrecision'], $truncGoods['unitRound']);
                } else {
                    $goodsDcPrice = $data['goodsDiscount'];
                }
            }

            // 상품별 금액 계산
            $data['price'] = [
                'goodsPrice' => $data['goodsPrice'],
                'optionPrice' => $data['optionPrice'],
                'optionPriceSum' => $data['optionPrice'] * $data['goodsCnt'],
                'goodsPriceSum' => $data['goodsPrice'] * $data['goodsCnt'],
                'goodsDcPrice' => $goodsDcPrice * $data['goodsCnt'],
                'goodsPriceSubtotal' => (($data['goodsPrice'] + $data['optionPrice']) * $data['goodsCnt']) - ($goodsDcPrice * $data['goodsCnt']),
                'goodsPriceTotal' => (($data['goodsPrice'] + $data['optionPrice']) * $data['goodsCnt']) - ($goodsDcPrice * $data['goodsCnt']),
            ];

            // 텍스트 옵션 처리
            $data['optionText'] = [];
            $optionTextPriceSum = 0;
            $data['price']['baseOptionTextPrice'] = 0;
            $data['price']['optionTextPrice'] = 0;
            if (empty($data['optionTextSno']) === false && empty($arrOptionText) === false) {
                foreach ($data['optionTextSno'] as $key => $textSno) {
                    if (isset($arrOptionText[$textSno])) {
                        $tmp = $arrOptionText[$textSno];

                        // 텍스트 옵션 기본 금액 합계
                        $data['price']['baseOptionTextPrice'] += $tmp['baseOptionTextPrice'];

                        // 텍스트 옵션 가격
                        $tmp['optionTextPrice'] = $tmp['baseOptionTextPrice'];
                        $data['price']['optionTextPrice'] += $tmp['optionTextPrice'];

                        // 옵션 입력값
                        $tmp['optionValue'] = $data['optionTextStr'][$textSno] ?? '';

                        $data['optionText'][$key] = $tmp;

                        // 텍스트 옵션 총 금액
                        $optionTextPriceSum += $tmp['optionTextPrice'] * $data['goodsCnt'];
                        unset($tmp);
                    }
                }
            }
            $data['price']['optionTextPriceSum'] = $optionTextPriceSum;
            $data['price']['goodsPriceSubtotal'] += $optionTextPriceSum;
            $data['price']['goodsPriceTotal'] += $optionTextPriceSum;

            // 추가상품 처리
            $data['addGoods'] = [];
            $addGoodsPriceSum = 0;
            if ($data['addGoodsFl'] === 'y' && empty($data['addGoodsNo']) === false) {
                foreach ($data['addGoodsNo'] as $addKey => $addGoodsNo) {
                    $addGoodsInfo = $arrAddGoods[$addGoodsNo] ?? [];
                    $addGoodsCnt = $data['addGoodsCnt'][$addKey] ?? 1;
                    $addGoodsInfo['addGoodsCnt'] = $addGoodsCnt;
                    $addGoodsInfo['addGoodsPrice'] = $addGoodsInfo['goodsPrice'] ?? 0;
                    $addGoodsPriceSum += $addGoodsInfo['addGoodsPrice'] * $addGoodsCnt;

                    // 추가상품이 DB에 없거나 삭제된 경우 (판매중지)
                    if (empty($addGoodsInfo['addGoodsNm'])) {
                        $data['orderPossible'] = 'n';
                        $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'SELL_NO';
                        $data['orderPossibleMessage'] = $data['orderPossibleMessageList'][] = __('판매중지 추가상품');
                        $this->orderPossible = false;
                    }

                    // 추가상품 재고체크 처리 후 재고 없으면 구매불가 처리
                    if (($addGoodsInfo['soldOutFl'] ?? 'n') === 'y' || (($addGoodsInfo['soldOutFl'] ?? 'n') === 'n' && ($addGoodsInfo['stockUseFl'] ?? '0') === '1' && (($addGoodsInfo['stockCnt'] ?? 0) == 0 || ($addGoodsInfo['stockCnt'] ?? 0) - $addGoodsCnt < 0))) {
                        $data['orderPossible'] = 'n';
                        $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'SOLD_OUT';
                        $data['orderPossibleMessage'] = $data['orderPossibleMessageList'][] = __('추가상품 재고부족');
                        $this->orderPossible = false;
                    }

                    $data['addGoods'][] = $addGoodsInfo;
                }
            }

            // 해당 상품에 대한 추가 상품 종류 수 저장
            $data['addGoodsListCount'] = count($data['addGoods']);

            $data['price']['addGoodsPriceSum'] = $addGoodsPriceSum;
            $data['price']['goodsPriceSubtotal'] += $addGoodsPriceSum;
            $data['price']['goodsPriceTotal'] += $addGoodsPriceSum;

            // 옵션 정보
            $data['option'] = [];
            $tmpOptionName = explode(STR_DIVISION, $data['optionName']);
            for ($i = 1; $i <= 5; $i++) {
                if (empty($data['optionValue' . $i]) === false) {
                    $data['option'][] = [
                        'optionName' => $tmpOptionName[$i - 1] ?? '',
                        'optionValue' => $data['optionValue' . $i],
                    ];
                }
            }

            // 총 상품 가격 합산
            $this->totalGoodsPrice += $data['price']['goodsPriceSubtotal'];
            $this->totalGoodsDcPrice += $data['price']['goodsDcPrice'];

            if (empty($data['goodsPriceString'])) {
                // 공급사별 상품 가격
                if (isset($this->totalScmGoodsPrice[$scmNo]) === false) {
                    $this->totalScmGoodsPrice[$scmNo] = 0;
                }
                $this->totalScmGoodsPrice[$scmNo] += $data['price']['goodsPriceSubtotal'];
            }
            $getCart[$scmNo][$deliverySno][] = $data;
        }

        return $getCart;
    }

    /**
     * 배송비 부과방법이 상품별인 경우 와 배송비 조건별인 경우의 설정을 구분 짓고 상품별 배송비만 계산하고
     * 배송비 조건별은 별도의 변수에 담아서 루프 아래에서 처리
     *
     * @param array $getCart 장바구니 생성 데이터
     * @param array $deliverySnos 배송일련번호
     * @param array|null $address 주소
     * @return array 배송비가 계산된 장바구니 정보
     */
    protected function getDeliveryDataInfo(array $getCart, array $deliverySnos, ?array $address = null): array
    {
        // 배송비조건 정보 가져오기
        $delivery = \App::load('\\Component\\Delivery\\DeliveryCart');
        $getDeliveryInfo = $delivery->getDataDeliveryWithGoodsNo($deliverySnos);

        $weightConf = $this->policy->getValue('basic.weight');

        // 배송비조건별 데이터 초기화
        $setDeliveryCond = [];

        // 지번주소로 저장되어 있는 지역별 추가배송비 비교하기 위한 조회
        $result = null;
        if ($address !== null) {
            $postcode = new \Component\Godo\GodoCenterServerApi();
            $checkAddress = $postcode->getCurlDataPostcodeV2($address);
            $result = json_decode($checkAddress['true'], true);
        }

        // 만들어진 장바구니 데이터를 이용해 배송비 추출
        foreach ($getCart as $scmNo => $sVal) {
            foreach ($sVal as $deliverySno => $dVal) {
                $firstGoodsDeliveryCollectFl = '';
                $firstGoodsDeliveryMethodFl = '';
                $firstGoodsDeliveryMethodFlText = '';
                $visitAddress = $delivery->getVisitAddress($deliverySno, true);

                foreach ($dVal as $gKey => $gVal) {
                    $setDeliveryCharge = $getDeliveryInfo[$deliverySno]['charge'] ?? [];
                    if (($getDeliveryInfo[$deliverySno]['deliveryConfigType'] ?? '') == 'etc' && empty($getDeliveryInfo[$deliverySno]['charge'][$gVal['deliveryMethodFl']]) === false) {
                        $setDeliveryCharge = $getDeliveryInfo[$deliverySno]['charge'][$gVal['deliveryMethodFl']];
                    }

                    // 배송비 정보 초기화
                    $goodsDeliveryFl = $getDeliveryInfo[$deliverySno]['goodsDeliveryFl'] ?? 'free';
                    $sameGoodsDeliveryFl = $getDeliveryInfo[$deliverySno]['sameGoodsDeliveryFl'] ?? 'n';
                    $goodsDeliveryMethod = $getDeliveryInfo[$deliverySno]['method'] ?? '';
                    $goodsDeliveryFixFl = $getDeliveryInfo[$deliverySno]['fixFl'] ?? 'free';
                    $goodsDeliveryVisitPayFl = $getDeliveryInfo[$deliverySno]['deliveryVisitPayFl'] ?? 'n';
                    $goodsDeliveryVisitAddressUseFl = empty(trim($visitAddress)) === true ? 'n' : ($getDeliveryInfo[$deliverySno]['dmVisitAddressUseFl'] ?? 'n');
                    $goodsDeliveryWholeFreeFl = ($getDeliveryInfo[$deliverySno]['freeFl'] ?? '') === 'y' ?: 'n';
                    $goodsDeliveryCollectFl = empty($gVal['deliveryCollectFl']) === true ? 'pre' : $gVal['deliveryCollectFl'];
                    $goodsDeliveryMethodFl = empty($gVal['deliveryMethodFl']) === true ? 'delivery' : $gVal['deliveryMethodFl'];
                    $goodsDeliveryMethodFlText = gd_get_delivery_method_display($goodsDeliveryMethodFl);
                    $goodsDeliveryTaxFreeFl = $getDeliveryInfo[$deliverySno]['taxFreeFl'] ?? 'n';
                    $goodsDeliveryTaxPercent = $getDeliveryInfo[$deliverySno]['taxPercent'] ?? 0;

                    // 선결제, 배송방식 모두 가장최근에 담긴 상품의 정보로 업데이트 되어야 하므로 최초 한번의 정보만 담는다.
                    if (trim($firstGoodsDeliveryCollectFl) === '') {
                        $firstGoodsDeliveryCollectFl = $goodsDeliveryCollectFl;
                    }
                    if (trim($firstGoodsDeliveryMethodFl) === '') {
                        $firstGoodsDeliveryMethodFl = $goodsDeliveryMethodFl;
                        $firstGoodsDeliveryMethodFlText = $goodsDeliveryMethodFlText;
                    }

                    $goodsDeliveryPrice = 0;
                    $goodsDeliveryAreaPrice = 0;
                    $goodsDeliveryCollectPrice = 0;
                    $goodsDeliveryWholeFreePrice = 0;
                    $tmp = [];

                    // 해당 공급사의 배송비 정책이 있는 경우에만
                    if (empty($getDeliveryInfo[$deliverySno]) === false && ($getDeliveryInfo[$deliverySno]['scmNo'] ?? '') === $scmNo) {
                        switch ($goodsDeliveryFixFl) {
                            // 금액별 배송비 기준
                            case 'price':
                                $tmp['deliveryByStandard'] = $gVal['price']['goodsPriceSum'] ?? 0;

                                // 금액별 배송비 기준 (옵션가 + 추가상품가 + 텍스트옵션가)
                                if (empty($getDeliveryInfo[$deliverySno]['pricePlusStandard']) === false) {
                                    if (in_array('option', $getDeliveryInfo[$deliverySno]['pricePlusStandard']) === true) {
                                        $tmp['deliveryByStandard'] += $gVal['price']['optionPriceSum'] ?? 0;
                                    }
                                    if (in_array('add', $getDeliveryInfo[$deliverySno]['pricePlusStandard']) === true) {
                                        $tmp['deliveryByStandard'] += $gVal['price']['addGoodsPriceSum'] ?? 0;
                                    }
                                    if (in_array('text', $getDeliveryInfo[$deliverySno]['pricePlusStandard']) === true) {
                                        $tmp['deliveryByStandard'] += $gVal['price']['optionTextPriceSum'] ?? 0;
                                    }
                                }

                                // 금액별 배송비 기준 (- 상품할인가)
                                if (empty($getDeliveryInfo[$deliverySno]['priceMinusStandard']) === false) {
                                    if (in_array('goods', $getDeliveryInfo[$deliverySno]['priceMinusStandard']) === true) {
                                        $tmp['deliveryByStandard'] -= $gVal['price']['goodsDcPrice'] ?? 0;
                                    }
                                }
                                $tmp['deliveryByCalculate'] = 'y';
                                break;

                            // 무게별 배송비 기준
                            case 'weight':
                                $tmp['deliveryByStandard'] = ($gVal['goodsWeight'] ?? 0) * ($gVal['goodsCnt'] ?? 1);
                                if (($getDeliveryInfo[$deliverySno]['rangeLimitFl'] ?? '') === 'y' && $tmp['deliveryByStandard'] >= ($getDeliveryInfo[$deliverySno]['rangeLimitWeight'] ?? 0)) {
                                    $this->orderPossible = false;
                                    $this->orderPossibleMessage = __('무게가 %s%s 이상의 상품은 구매할 수 없습니다.', $getDeliveryInfo[$deliverySno]['rangeLimitWeight'], $weightConf['unit'] ?? '');
                                }
                                $tmp['deliveryByCalculate'] = 'y';
                                break;

                            // 수량별 배송비 기준
                            case 'count':
                                $deliveryGoodsCnt = $gVal['goodsCnt'] ?? 1;
                                if (($getDeliveryInfo[$deliverySno]['addGoodsCountInclude'] ?? '') === 'y') {
                                    $deliveryGoodsCnt += (int)array_sum(array_column($gVal['addGoods'] ?? [], 'addGoodsCnt'));
                                }
                                $tmp['deliveryByStandard'] = $deliveryGoodsCnt;
                                $tmp['deliveryByCalculate'] = 'y';
                                break;

                            // 고정 배송비 기준
                            case 'fixed':
                                $tmp['deliveryByStandard'] = 0;
                                $tmp['deliveryByCalculate'] = 'n';
                                $goodsDeliveryPrice = $setDeliveryCharge[0]['price'] ?? 0;
                                break;

                            // 무료 배송비 기준
                            case 'free':
                                $tmp['deliveryByStandard'] = 0;
                                $tmp['deliveryByCalculate'] = 'n';
                                $goodsDeliveryPrice = 0;
                                break;
                        }

                        // 지역 확인 후 배송비 설정
                        if ($address !== null && ($getDeliveryInfo[$deliverySno]['areaFl'] ?? '') == 'y') {
                            if (empty($getDeliveryInfo[$deliverySno]['areaGroupList']) === false) {
                                $grondAddress = $result['resultData']['addressData'][0]['groundAddress'] ?? '';
                                $roadAddress = $result['resultData']['addressData'][0]['roadAddress'] ?? '';

                                foreach ($getDeliveryInfo[$deliverySno]['areaGroupList'] as $areaDelivery) {
                                    if (stripos(str_replace(' ', '', $address), str_replace(' ', '', $areaDelivery['addArea'])) !== false ||
                                        stripos(str_replace(' ', '', $grondAddress), str_replace(' ', '', $areaDelivery['addArea'])) !== false ||
                                        stripos(str_replace(' ', '', $roadAddress), str_replace(' ', '', $areaDelivery['addArea'])) !== false) {
                                        if ($goodsDeliveryFl !== 'y') {
                                            $goodsDeliveryAreaPrice = $areaDelivery['addPrice'];
                                        } else {
                                            $setDeliveryCond[$deliverySno]['goodsDeliveryAreaPrice'] = $areaDelivery['addPrice'];
                                        }
                                        break;
                                    }
                                }
                            }
                        }

                        // 상품별조건 배송비 계산
                        if ($goodsDeliveryFl !== 'y') {
                            if ($sameGoodsDeliveryFl !== 'y') {
                                // 배송비 계산
                                if (($tmp['deliveryByCalculate'] ?? '') === 'y') {
                                    if (($getDeliveryInfo[$deliverySno]['rangeRepeat'] ?? '') === 'y') {
                                        // 범위 반복 설정이 되어있는 경우
                                        $deliveryStandardFinal = 0;
                                        $conditionRange = $setDeliveryCharge[0] ?? [];
                                        $conditionRepeat = $setDeliveryCharge[1] ?? [];

                                        if (($tmp['deliveryByStandard'] ?? 0) > 0) {
                                            $goodsDeliveryPrice = $conditionRange['price'] ?? 0;

                                            if (($conditionRepeat['unitEnd'] ?? 0) > 0) {
                                                $deliveryStandardNum = ($tmp['deliveryByStandard'] ?? 0) - ($conditionRepeat['unitStart'] ?? 0);
                                                if (!$deliveryStandardNum) {
                                                    $deliveryStandardNum = 0;
                                                }

                                                if ($deliveryStandardNum >= 0) {
                                                    $deliveryStandardFinal = ($deliveryStandardNum / $conditionRepeat['unitEnd']);
                                                    if (!$deliveryStandardFinal) {
                                                        $deliveryStandardFinal = 0;
                                                    }

                                                    if (preg_match('/\./', (string)$deliveryStandardFinal)) {
                                                        $deliveryStandardFinal = (int)ceil($deliveryStandardFinal);
                                                    } else {
                                                        $deliveryStandardFinal += 1;
                                                    }

                                                    $goodsDeliveryPrice += ($deliveryStandardFinal * ($conditionRepeat['price'] ?? 0));
                                                }
                                            }
                                        }
                                    } else {
                                        // 범위 반복 설정이 되어있지 않은 경우
                                        foreach ($setDeliveryCharge as $cVal) {
                                            $unitEnd = $cVal['unitEnd'] ?? 0;
                                            if ($unitEnd == 0) {
                                                $unitEnd = 999999999999;
                                            }
                                            if (($tmp['deliveryByStandard'] ?? 0) >= ($cVal['unitStart'] ?? 0) && ($tmp['deliveryByStandard'] ?? 0) < $unitEnd) {
                                                $goodsDeliveryPrice = $cVal['price'] ?? 0;
                                                continue;
                                            }
                                        }
                                    }
                                }

                                // 방문수령 배송비 무료 처리
                                if ($goodsDeliveryMethodFl == 'visit' && $goodsDeliveryVisitPayFl == 'n') {
                                    $goodsDeliveryPrice = 0;
                                }

                                // 배송비 결제방법
                                if (($getDeliveryInfo[$deliverySno]['collectFl'] ?? '') !== 'both') {
                                    $goodsDeliveryCollectFl = $getDeliveryInfo[$deliverySno]['collectFl'] ?? 'pre';
                                }

                                // scm별 무료인 경우 무료 배송 처리
                                if (($getDeliveryInfo[$deliverySno]['wholeFreeFl'] ?? '') === 'y') {
                                    $goodsDeliveryWholeFreeFl = 'y';
                                    $goodsDeliveryWholeFreePrice = $goodsDeliveryPrice;
                                    $goodsDeliveryPrice = 0;
                                }

                                // 배송비가 착불인 경우 배송비 0원
                                if ($goodsDeliveryCollectFl === 'later') {
                                    $goodsDeliveryCollectPrice = $goodsDeliveryPrice;
                                    $goodsDeliveryPrice = 0;
                                    if ($goodsDeliveryAreaPrice > 0) {
                                        $goodsDeliveryCollectPrice += $goodsDeliveryAreaPrice;
                                    }
                                }
                            } else {
                                // 상품별 - 동일상품일 경우 1회만 부과일 경우 배송비 초기화
                                $goodsDeliveryPrice = 0;

                                $setDeliveryCond[$deliverySno][$gVal['goodsNo']]['deliveryByStandard'] = ($setDeliveryCond[$deliverySno][$gVal['goodsNo']]['deliveryByStandard'] ?? 0) + ($tmp['deliveryByStandard'] ?? 0);
                                $setDeliveryCond[$deliverySno][$gVal['goodsNo']]['deliveryByCalculate'] = $tmp['deliveryByCalculate'] ?? 'n';
                                $setDeliveryCond[$deliverySno][$gVal['goodsNo']]['fixFl'] = $goodsDeliveryFixFl;
                                $setDeliveryCond[$deliverySno][$gVal['goodsNo']]['goodsDeliveryWholeFreeFl'] = $getDeliveryInfo[$deliverySno]['wholeFreeFl'] ?? 'n';
                                if (isset($setDeliveryCond[$deliverySno][$gVal['goodsNo']]['goodsDeliveryCollectFl']) === false || empty($setDeliveryCond[$deliverySno][$gVal['goodsNo']]['goodsDeliveryCollectFl']) === true) {
                                    $setDeliveryCond[$deliverySno][$gVal['goodsNo']]['goodsDeliveryCollectFl'] = $goodsDeliveryCollectFl;
                                }
                            }
                        } else {
                            // 배송비조건별 계산
                            $goodsDeliveryPrice = 0;

                            $setDeliveryCond[$deliverySno]['deliveryByStandard'] = ($setDeliveryCond[$deliverySno]['deliveryByStandard'] ?? 0) + ($tmp['deliveryByStandard'] ?? 0);
                            $setDeliveryCond[$deliverySno]['deliveryByCalculate'] = $tmp['deliveryByCalculate'] ?? 'n';
                            $setDeliveryCond[$deliverySno]['fixFl'] = $goodsDeliveryFixFl;
                            $setDeliveryCond[$deliverySno]['goodsDeliveryWholeFreeFl'] = $getDeliveryInfo[$deliverySno]['wholeFreeFl'] ?? 'n';
                            if (isset($setDeliveryCond[$deliverySno]['goodsDeliveryCollectFl']) === false || empty($setDeliveryCond[$deliverySno]['goodsDeliveryCollectFl']) === true) {
                                $setDeliveryCond[$deliverySno]['goodsDeliveryCollectFl'] = $firstGoodsDeliveryCollectFl;
                            }
                        }

                        if ($goodsDeliveryFl != 'y' && $sameGoodsDeliveryFl == 'y') {
                            $setDeliveryCond[$deliverySno][$gVal['goodsNo']]['scmNo'] = $scmNo;
                            $setDeliveryCond[$deliverySno][$gVal['goodsNo']]['goodsLineCnt'] = ($setDeliveryCond[$deliverySno][$gVal['goodsNo']]['goodsLineCnt'] ?? 0) + 1;
                            $setDeliveryCond[$deliverySno][$gVal['goodsNo']]['addGoodsLineCnt'] = ($setDeliveryCond[$deliverySno][$gVal['goodsNo']]['addGoodsLineCnt'] ?? 0) + gd_count($gVal['addGoods'] ?? []);
                            $setDeliveryCond[$deliverySno][$gVal['goodsNo']]['goodsDeliveryMethodFl'] = $goodsDeliveryMethodFl;
                            $setDeliveryCond[$deliverySno][$gVal['goodsNo']]['goodsDeliveryMethodFlText'] = $goodsDeliveryMethodFlText;
                            $setDeliveryCond[$deliverySno][$gVal['goodsNo']]['goodsDeliveryVisitPayFl'] = $goodsDeliveryVisitPayFl;
                            $setDeliveryCond[$deliverySno][$gVal['goodsNo']]['goodsDeliveryVisitAddressUseFl'] = $goodsDeliveryVisitAddressUseFl;
                            $setDeliveryCond[$deliverySno][$gVal['goodsNo']]['deliveryConfigType'] = $getDeliveryInfo[$deliverySno]['deliveryConfigType'] ?? '';
                        } else {
                            $setDeliveryCond[$deliverySno]['scmNo'] = $scmNo;
                            $setDeliveryCond[$deliverySno]['goodsLineCnt'] = ($setDeliveryCond[$deliverySno]['goodsLineCnt'] ?? 0) + 1;
                            $setDeliveryCond[$deliverySno]['addGoodsLineCnt'] = ($setDeliveryCond[$deliverySno]['addGoodsLineCnt'] ?? 0) + gd_count($gVal['addGoods'] ?? []);
                            $setDeliveryCond[$deliverySno]['goodsDeliveryMethodFl'] = $firstGoodsDeliveryMethodFl;
                            $setDeliveryCond[$deliverySno]['goodsDeliveryMethodFlText'] = $firstGoodsDeliveryMethodFlText;
                            $setDeliveryCond[$deliverySno]['goodsDeliveryVisitPayFl'] = $goodsDeliveryVisitPayFl;
                            $setDeliveryCond[$deliverySno]['goodsDeliveryVisitAddressUseFl'] = $goodsDeliveryVisitAddressUseFl;
                            $setDeliveryCond[$deliverySno]['deliveryConfigType'] = $getDeliveryInfo[$deliverySno]['deliveryConfigType'] ?? '';
                        }

                        $setDeliveryCond[$deliverySno]['goodsDeliveryFl'] = $goodsDeliveryFl;
                        $setDeliveryCond[$deliverySno]['sameGoodsDeliveryFl'] = $sameGoodsDeliveryFl;

                        unset($tmp);
                    } else {
                        $gVal['orderPossible'] = 'n';
                        $gVal['orderPossibleCode'] = 'DELIVERY_SNO_NO';
                        $gVal['orderPossibleMessage'] = __('지정된 배송비 없음');
                        $this->orderPossible = false;
                    }

                    $gVal['goodsDeliveryFl'] = $goodsDeliveryFl;
                    $gVal['goodsDeliveryFixFl'] = $goodsDeliveryFixFl;
                    $gVal['goodsDeliveryMethod'] = $goodsDeliveryMethod;
                    $gVal['goodsDeliveryCollectFl'] = $goodsDeliveryCollectFl;
                    $gVal['goodsDeliveryWholeFreeFl'] = $goodsDeliveryWholeFreeFl;
                    $gVal['goodsDeliveryTaxFreeFl'] = $goodsDeliveryTaxFreeFl;
                    $gVal['goodsDeliveryTaxPercent'] = $goodsDeliveryTaxPercent;
                    $gVal['goodsDeliveryVisitPayFl'] = $goodsDeliveryVisitPayFl;
                    $gVal['goodsDeliveryVisitAddressUseFl'] = $goodsDeliveryVisitAddressUseFl;

                    $gVal['price']['goodsDeliveryPrice'] = $goodsDeliveryPrice;
                    $gVal['price']['goodsDeliveryAreaPrice'] = $goodsDeliveryAreaPrice;
                    $gVal['price']['goodsDeliveryCollectPrice'] = $goodsDeliveryCollectPrice;
                    $gVal['price']['goodsDeliveryWholeFreePrice'] = $goodsDeliveryWholeFreePrice;

                    // 반환데이터 설정
                    $getCart[$scmNo][$deliverySno][$gKey] = $gVal;

                    // 지역별 배송비를 각 배송금액에 더해야 해서 없는 경우 0으로 초기화
                    gd_isset($this->totalGoodsDeliveryAreaPrice[$deliverySno], 0);
                    if ($goodsDeliveryCollectFl !== 'later') {
                        if ($sameGoodsDeliveryFl == 'y') {
                            if (empty($tmpTotalGoodsDeliveryAreaPrice[$gVal['goodsNo']])) {
                                $this->totalGoodsDeliveryAreaPrice[$deliverySno] += $goodsDeliveryAreaPrice;
                                $tmpTotalGoodsDeliveryAreaPrice[$gVal['goodsNo']] = 1;
                            }
                        } else {
                            $this->totalGoodsDeliveryAreaPrice[$deliverySno] += $goodsDeliveryAreaPrice;
                        }
                    }

                    // 상품 배송정책별 총 배송 금액
                    gd_isset($this->totalGoodsDeliveryPolicyCharge[$deliverySno], 0);
                    $this->totalGoodsDeliveryPolicyCharge[$deliverySno] += $goodsDeliveryPrice;

                    // 상품별배송비 조건 총 배송 금액
                    if ($goodsDeliveryCollectFl !== 'later') {
                        $this->totalDeliveryCharge += $goodsDeliveryPrice + $goodsDeliveryAreaPrice;
                    }

                    // scm 별 상품 총 배송 금액
                    if ($goodsDeliveryCollectFl !== 'later') {
                        gd_isset($this->totalScmGoodsDeliveryCharge[$scmNo], 0);
                        $this->totalScmGoodsDeliveryCharge[$scmNo] += $goodsDeliveryPrice + $goodsDeliveryAreaPrice;
                    }
                }
            }
        }

        // 배송비 부과방법이 배송비 조건별인 경우 별도 계산
        if (empty($setDeliveryCond) === false) {
            foreach ($setDeliveryCond as $dKey => $dVal) {
                if (($dVal['goodsDeliveryFl'] ?? '') != 'y' && ($dVal['sameGoodsDeliveryFl'] ?? '') == 'y') {
                    unset($dVal['goodsDeliveryFl'], $dVal['sameGoodsDeliveryFl']);
                    foreach ($dVal as $gKey => $gVal) {
                        $setDeliveryCond[$dKey][$gKey] = $this->getDeliveryCond($gVal, $getDeliveryInfo[$dKey] ?? [], $weightConf, $dKey);
                    }
                } else {
                    $setDeliveryCond[$dKey] = $this->getDeliveryCond($dVal, $getDeliveryInfo[$dKey] ?? [], $weightConf, $dKey);
                }
            }
        }

        $this->setDeliveryInfo = $setDeliveryCond;

        return $getCart;
    }

    /**
     * 배송비 조건별 계산
     *
     * @param array $deliveryCond 배송비 조건
     * @param array $deliveryInfo 배송비 정보
     * @param array $weightConf 무게 설정
     * @param int $key 배송비 조건 키
     * @return array 계산된 배송비 조건
     */
    protected function getDeliveryCond(array $deliveryCond, array $deliveryInfo, array $weightConf, int $key): array
    {
        if (!isset($deliveryCond['deliveryByCalculate'])) {
            return $deliveryCond;
        }
        $setDeliveryCharge = $deliveryInfo['charge'] ?? [];
        if (($deliveryCond['deliveryConfigType'] ?? '') == 'etc' && empty($deliveryInfo['charge'][$deliveryCond['goodsDeliveryMethodFl']]) === false) {
            $setDeliveryCharge = $deliveryInfo['charge'][$deliveryCond['goodsDeliveryMethodFl']];
        }

        $scmNo = $deliveryCond['scmNo'] ?? 0;
        $rangeRepeatPrice = 0;

        // 배송비 계산
        if ($deliveryCond['deliveryByCalculate'] === 'y') {
            foreach ($setDeliveryCharge as $cKey => $cVal) {
                $unitEnd = $cVal['unitEnd'] ?? 0;
                if ($unitEnd == 0) {
                    $unitEnd = 999999999;
                }

                $checkZeroPrice = false;
                $conditionSatisfy = false;

                if (($deliveryInfo['rangeRepeat'] ?? '') === 'y') {
                    $deliveryStandardNum = 0;
                    $deliveryStandardFinal = 0;

                    if ($cKey === 0) {
                        if (($deliveryCond['deliveryByStandard'] ?? 0) > 0) {
                            $rangeRepeatPrice = $cVal['price'] ?? 0;
                            continue;
                        }
                    } else if ($cKey === 1) {
                        if (($deliveryCond['deliveryByStandard'] ?? 0) > 0) {
                            if (($cVal['unitEnd'] ?? 0) > 0) {
                                $deliveryStandardNum = ($deliveryCond['deliveryByStandard'] ?? 0) - ($cVal['unitStart'] ?? 0);
                                if (!$deliveryStandardNum) {
                                    $deliveryStandardNum = 0;
                                }

                                if ($deliveryStandardNum >= 0) {
                                    $deliveryStandardFinal = ($deliveryStandardNum / $cVal['unitEnd']);
                                    if (!$deliveryStandardFinal) {
                                        $deliveryStandardFinal = 0;
                                    }

                                    if (preg_match('/\./', (string)$deliveryStandardFinal)) {
                                        $deliveryStandardFinal = (int)ceil($deliveryStandardFinal);
                                    } else {
                                        $deliveryStandardFinal += 1;
                                    }
                                    $rangeRepeatPrice += ($deliveryStandardFinal * ($cVal['price'] ?? 0));
                                }
                            }
                        }
                        $cVal['price'] = $rangeRepeatPrice;
                    } else {
                        break;
                    }
                    $conditionSatisfy = true;
                } else {
                    if (($deliveryCond['deliveryByStandard'] ?? 0) >= ($cVal['unitStart'] ?? 0) && ($deliveryCond['deliveryByStandard'] ?? 0) < $unitEnd) {
                        $conditionSatisfy = true;
                    }
                }

                if ($conditionSatisfy === true) {
                    if (($deliveryInfo['fixFl'] ?? '') === 'weight') {
                        if (($deliveryInfo['rangeLimitFl'] ?? '') === 'y' && ($deliveryCond['deliveryByStandard'] ?? 0) >= ($deliveryInfo['rangeLimitWeight'] ?? 0)) {
                            $this->orderPossible = false;
                            $this->orderPossibleMessage = __('무게가 %s%s 이상의 상품은 구매할 수 없습니다.', $deliveryInfo['rangeLimitWeight'], $weightConf['unit'] ?? '');
                        }
                    }

                    $deliveryCond['goodsDeliveryMethod'] = $deliveryInfo['method'] ?? '';

                    if (($deliveryCond['goodsDeliveryWholeFreeFl'] ?? '') == 'y') {
                        $deliveryCond['goodsDeliveryWholeFreePrice'] = $cVal['price'] ?? 0;
                        $checkZeroPrice = true;
                    }

                    if (($deliveryCond['goodsDeliveryCollectFl'] ?? '') == 'later') {
                        $deliveryCond['goodsDeliveryCollectPrice'] = $cVal['price'] ?? 0;
                        $checkZeroPrice = true;
                        gd_isset($deliveryCond['goodsDeliveryAreaPrice'], 0);
                        if ($deliveryCond['goodsDeliveryAreaPrice'] > 0) {
                            $deliveryCond['goodsDeliveryCollectPrice'] += $deliveryCond['goodsDeliveryAreaPrice'];
                        }
                    }

                    if ($checkZeroPrice === true) {
                        $cVal['price'] = 0;
                    }

                    if (($deliveryCond['goodsDeliveryMethodFl'] ?? '') == 'visit' && ($deliveryCond['goodsDeliveryVisitPayFl'] ?? '') == 'n') {
                        $cVal['price'] = 0;
                    }

                    gd_isset($this->totalGoodsDeliveryPolicyCharge[$key], 0);
                    $this->totalGoodsDeliveryPolicyCharge[$key] += $cVal['price'] ?? 0;

                    $this->totalDeliveryCharge += $cVal['price'] ?? 0;

                    gd_isset($this->totalScmGoodsDeliveryCharge[$scmNo], 0);
                    $this->totalScmGoodsDeliveryCharge[$scmNo] += $cVal['price'] ?? 0;

                    $deliveryCond['goodsDeliveryPrice'] = $cVal['price'] ?? 0;

                    continue;
                }
            }
        } else {
            $deliveryCond['goodsDeliveryMethod'] = $deliveryInfo['method'] ?? '';

            if (($deliveryCond['fixFl'] ?? '') === 'fixed') {
                $checkZeroPrice = false;

                if (($deliveryCond['goodsDeliveryWholeFreeFl'] ?? '') === 'y') {
                    $deliveryCond['goodsDeliveryWholeFreePrice'] = $setDeliveryCharge[0]['price'] ?? 0;
                    $checkZeroPrice = true;
                }

                if (($deliveryCond['goodsDeliveryCollectFl'] ?? '') === 'later') {
                    $deliveryCond['goodsDeliveryCollectPrice'] = $setDeliveryCharge[0]['price'] ?? 0;
                    $checkZeroPrice = true;
                }

                if ($checkZeroPrice === true) {
                    $goodsDeliveryPrice = 0;
                } else {
                    $goodsDeliveryPrice = $setDeliveryCharge[0]['price'] ?? 0;
                }

                if (($deliveryCond['goodsDeliveryMethodFl'] ?? '') == 'visit' && ($deliveryCond['goodsDeliveryVisitPayFl'] ?? '') == 'n') {
                    $goodsDeliveryPrice = 0;
                }

                gd_isset($this->totalScmGoodsDeliveryCharge[$scmNo], 0);
                $this->totalScmGoodsDeliveryCharge[$scmNo] += $goodsDeliveryPrice;

                gd_isset($this->totalGoodsDeliveryPolicyCharge[$key], 0);
                $this->totalGoodsDeliveryPolicyCharge[$key] += $goodsDeliveryPrice;

                $this->totalDeliveryCharge += $goodsDeliveryPrice;

                $deliveryCond['goodsDeliveryPrice'] = $goodsDeliveryPrice;
            } else if (($deliveryCond['fixFl'] ?? '') === 'free') {
                $this->totalGoodsDeliveryPolicyCharge[$key] = 0;
                $this->totalDeliveryCharge += 0;
                gd_isset($this->totalScmGoodsDeliveryCharge[$scmNo], 0);
                $this->totalScmGoodsDeliveryCharge[$scmNo] += 0;
                $deliveryCond['goodsDeliveryPrice'] = 0;
            }
        }

        // 상품별 지역배송비 총 배송 금액
        gd_isset($this->totalGoodsDeliveryAreaPrice[$key], 0);
        if (($deliveryCond['goodsDeliveryCollectFl'] ?? '') != 'later') {
            $this->totalGoodsDeliveryAreaPrice[$key] += $deliveryCond['goodsDeliveryAreaPrice'] ?? 0;
        }

        // SCM별에 지역배송비 추가
        if (($deliveryCond['goodsDeliveryCollectFl'] ?? '') != 'later') {
            gd_isset($this->totalScmGoodsDeliveryCharge[$scmNo], 0);
            $this->totalScmGoodsDeliveryCharge[$scmNo] += $deliveryCond['goodsDeliveryAreaPrice'] ?? 0;
        }

        // 상품별 총 배송 금액에 지역배송비 추가
        if (($deliveryCond['goodsDeliveryCollectFl'] ?? '') != 'later') {
            $this->totalDeliveryCharge += $deliveryCond['goodsDeliveryAreaPrice'] ?? 0;
        }

        return $deliveryCond;
    }

    /**
     * 추가상품 정보 조회
     *
     * @param array $addGoodsNoArr 추가상품 번호 배열
     * @return array 추가상품 정보
     */
    protected function getAddGoodsInfo(array $addGoodsNoArr): array
    {
        if (empty($addGoodsNoArr)) {
            return [];
        }

        $addGoodsNoArr = array_unique($addGoodsNoArr);
        $data = $this->addGoodsRepository->findAddGoodsInfoForCartByAddGoodsNoList($addGoodsNoArr);

        $result = [];
        foreach ($data as $val) {
            // 상품 이미지 처리
            $val['addGoodsImage'] = gd_html_preview_image($val['imageNm'], $val['imagePath'], $val['imageStorage'], 40, 'add_goods', $val['addGoodsNm'], 'class="imgsize-s"', false, false);
            unset($val['imageStorage'], $val['imagePath'], $val['imageNm']);

            $result[$val['addGoodsNo']] = $val;
            $result[$val['addGoodsNo']]['addGoodsNo'] = $val['addGoodsNo'];
        }

        return $result;
    }

    /**
     * 텍스트옵션 정보 조회
     *
     * @param array $optionTextSnoArr 텍스트옵션 sno 배열
     * @return array 텍스트옵션 정보
     */
    protected function getOptionTextInfo(array $optionTextSnoArr): array
    {
        if (empty($optionTextSnoArr)) {
            return [];
        }

        $optionTextSnoArr = array_unique($optionTextSnoArr);
        $data = $this->goodsOptionTextRepository->findOptionTextInfoForCartBySnoList($optionTextSnoArr);

        $result = [];
        foreach ($data as $val) {
            $result[$val['sno']]['optionSno'] = $val['sno'];
            $result[$val['sno']]['optionName'] = $val['optionName'];
            $result[$val['sno']]['baseOptionTextPrice'] = $val['addPrice'];
        }

        return $result;
    }

    /**
     * 상품 구매 가능 여부 체크
     *
     * @param array $data 상품 정보
     * @param array $delCartSno 삭제할 장바구니 sno 참조
     * @return array|null 구매 가능 여부가 추가된 상품 정보 (삭제 대상인 경우 null)
     */
    protected function checkOrderPossible(array $data, array &$delCartSno): ?array
    {
        // 정기상품 정보와 배송주기 조회
        $tmpData = $this->regularGoodsRepository->findRegularGoodsWithDeliveryCycle($data['regularGoodsNo']);

        $deliveryCycleType = array_filter(array_column($tmpData, 'deliveryCycleType'), function ($value) {
            return $value !== 'all';
        });
        $monthCycle = array_filter(array_column($tmpData, 'monthCycle'));
        $weekCycle = array_filter(array_column($tmpData, 'weekCycle'));
        $weekDayCycle = array_values(array_filter(array_column($tmpData, 'weekDayCycle')));
        $maxDeliveryRound = array_values(array_filter(array_column($tmpData, 'maxDeliveryRounds'), function ($value) {
            return $value !== null;
        }));
        $regularGoodsDelFl = array_column($tmpData, 'delFl');
        $regularGoodsApplyStatus = array_column($tmpData, 'applyStatus');

        // 삭제 상품은 카트에서 삭제하기 위함
        if (in_array('y', $regularGoodsDelFl)) {
            $delCartSno[] = $data['sno'];
            return null;
        }

        $data['regularGoodsPossible'] = [
            'deliveryCycleType' => $deliveryCycleType ?? [],
            'monthCycle' => $monthCycle ?? [],
            'weekCycle' => $weekCycle ?? [],
            'weekDayCycle' => $weekDayCycle ?? [],
            'maxDeliveryRounds' => $maxDeliveryRound ?? [],
            'regularGoodsDelFl' => $regularGoodsDelFl ?? [],
            'regularGoodsApplyStatus' => $regularGoodsApplyStatus ?? []
        ];

        $data['orderPossible'] = 'y';
        $orderPossibleMessage = [];

        // 상품 판매 여부
        if ($data['goodsSellFl'] === 'n') {
            $data['orderPossible'] = 'n';
            $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'SELL_NO';
            $data['orderPossibleMessage'] = $orderPossibleMessage[] = __('판매중지 상품');
        }

        // 재고 없음 체크
        if ($data['soldOutFl'] === 'y' || ($data['soldOutFl'] === 'n' && $data['stockFl'] === 'y' && ($data['totalStock'] <= 0 || $data['totalStock'] < $data['goodsCnt']))) {
            $data['orderPossible'] = 'n';
            $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'SOLD_OUT';
            $data['orderPossibleMessage'] = $orderPossibleMessage[] = __('재고부족');
        }

        // 금액 0원 상품 체크
        $optionTextPrice = 0;
        if (is_array($data['optionTextInfo'] ?? null) === true) {
            foreach ($data['optionTextInfo'] as $val) {
                $optionTextPrice += $val['baseOptionTextPrice'];
            }
        }
        if ($this->cartPolicy['zeroPriceOrderFl'] === 'n' && intval($data['goodsPrice'] + $data['optionPrice'] + $optionTextPrice) === 0) {
            $data['orderPossible'] = 'n';
            $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'ZERO_PRICE';
            $data['orderPossibleMessage'] = $orderPossibleMessage[] = __('상품금액 없음');
        }

        // 가격 대체 문구가 있는 경우 판매금지
        if (empty($data['goodsPriceString']) === false) {
            $data['orderPossible'] = 'n';
            $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'PRICE_STRING';
            $data['orderPossibleMessage'] = $orderPossibleMessage[] = __('상품금액 없음');
        }

        // 묶음 단위 0인 경우 (db에서 임의로 수정한 경우)
        if (isset($data['salesUnit']) && $data['salesUnit'] == 0) {
            $data['orderPossible'] = 'n';
            $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'OPTION_QUANTITY_NOT_SELECT';
            $data['orderPossibleMessage'] = $orderPossibleMessage[] = __('옵션/수량 미선택');
        }

        // 옵션/수량 미선택 (옵션)
        if (($data['optionFl'] == 'y' && empty($data['optionSno'])) || ($data['optionTextEnteredFl'] ?? '') == 'n' || ($data['addGoodsSelectedFl'] ?? '') == 'n') {
            $data['orderPossible'] = 'n';
            $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'OPTION_QUANTITY_NOT_SELECT';
            $data['orderPossibleMessage'] = $orderPossibleMessage[] = __('옵션/수량 미선택');
        }

        // 옵션/수량 미선택 (수량)
        if (((($data['minOrderCnt'] == '1' && $data['maxOrderCnt'] == 0) == false) && ($data['goodsCnt'] < $data['minOrderCnt'] || ($data['maxOrderCnt'] > 0 && $data['goodsCnt'] > $data['maxOrderCnt']))) || (empty($data['salesUnit']) == false && $data['goodsCnt'] % $data['salesUnit'] != 0)) {
            // 옵션기준 묶음주문/구매수량 체크
            if (((($data['minOrderCnt'] == '1' && $data['maxOrderCnt'] == 0) == false) && ($data['fixedOrderCnt'] == 'option' && ($data['goodsCnt'] < $data['minOrderCnt'] || ($data['maxOrderCnt'] > 0 && $data['goodsCnt'] > $data['maxOrderCnt'])))) || ($data['fixedSales'] == 'option' && empty($data['salesUnit']) == false && $data['goodsCnt'] % $data['salesUnit'] != 0)) {
                $data['orderPossible'] = 'n';
                $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'OPTION_QUANTITY_NOT_SELECT';
                $data['orderPossibleMessage'] = $orderPossibleMessage[] = __('옵션/수량 미선택');
            }
        }

        // 옵션 출력여부에 따른 판매금지
        if ($data['optionFl'] == 'y' && $data['optionSno'] > 0 && ($data['optionViewFl'] ?? '') !== 'y') {
            $data['orderPossible'] = 'n';
            $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'OPTION_DISPLAY_NO';
            $data['orderPossibleMessage'] = $orderPossibleMessage[] = __('옵션 구매불가');
        }

        // 옵션 판매여부에 따른 판매금지
        if ($data['optionFl'] == 'y' && $data['optionSno'] > 0 && ($data['optionSellFl'] ?? '') !== 'y') {
            $data['orderPossible'] = 'n';
            $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'OPTION_SELL_NO';
            $data['orderPossibleMessage'] = $orderPossibleMessage[] = __('옵션 구매불가');
        }

        // 옵션 사용안함에 따른 판매금지 (단, 장바구니에 옵션번호가 있는 경우)
        if ($data['optionSno'] > 0 && !empty($data['optionValue1']) && !empty($data['optionName']) && $data['optionFl'] === 'n') {
            $data['orderPossible'] = 'n';
            $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'OPTION_NOT_AVAILABLE';
            $data['orderPossibleMessage'] = $orderPossibleMessage[] = __('옵션 구매불가');
        }

        // 상품 판매기간에 따른 판매금지
        if (empty($data['salesStartYmd']) === false && empty($data['salesEndYmd']) === false) {
            if ($data['salesStartYmd'] != '0000-00-00 00:00:00' && $data['salesStartYmd'] > date('Y-m-d H:i:s')) {
                $data['orderPossible'] = 'n';
                $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'SALE_DATE_START';
                $data['orderPossibleMessage'] = $orderPossibleMessage[] = __('판매시작 전');
            }
            if ($data['salesEndYmd'] != '0000-00-00 00:00:00' && $data['salesEndYmd'] < date('Y-m-d H:i:s')) {
                $data['orderPossible'] = 'n';
                $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'SALE_DATE_END';
                $data['orderPossibleMessage'] = $orderPossibleMessage[] = __('판매종료');
            }
        }

        // 상품 옵션 재고 관련
        $data['stockOver'] = 'n';
        if ($data['stockFl'] == 'y' && $data['stockCnt'] < $data['goodsCnt'] && $data['optionFl'] === 'y' && $data['optionSno'] > 0) {
            $data['orderPossible'] = 'n';
            $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'STOCK_OVER';
            $data['stockOver'] = 'y';

            // 타 옵션의 재고가 있는 경우
            if ($data['totalStock'] > $data['stockCnt']) {
                $data['orderPossibleMessage'] = $orderPossibleMessage[] = __('옵션 구매불가');
            } else {
                $data['orderPossibleMessage'] = $orderPossibleMessage[] = __('재고부족');
            }
        }

        // 옵션이 삭제된 경우
        if ($data['optionFl'] == 'y' && empty($data['stockCnt']) === true && empty($data['optionSellFl']) === true && empty($data['optionViewFl']) === true && ($data['orderPossibleCode'] ?? '') != 'OPTION_QUANTITY_NOT_SELECT') {
            $data['orderPossible'] = 'n';
            $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'ORDER_NO';
            $data['orderPossibleMessage'] = $orderPossibleMessage[] = __('옵션 구매불가');
            $data['stockOver'] = 'y';
        }

        // 배송비 정책이 없는 경우 판매금지
        if (empty($data['deliverySno']) === true) {
            $data['orderPossible'] = 'n';
            $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'DELIVERY_SNO_NO';
            $data['orderPossibleMessage'] = $orderPossibleMessage[] = __('지정된 배송비 없음');
        }

        // 성인인증 상품 관련 (정기결제는 회원만 가능)
        if ($data['onlyAdultFl'] == 'y') {
            if ((gd_use_ipin() || gd_use_auth_cellphone()) && $this->members['adultFl'] != 'y') {
                $data['orderPossible'] = 'n';
                $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'ONLY_ADULT';
                $data['orderPossibleMessage'] = $orderPossibleMessage[] = '성인 인증 필요 상품';
            }
        }

        // 회원등급 여부
        if ($data['goodsPermissionGroup']) {
            $group = explode(\INT_DIVISION, $data['goodsPermissionGroup']);
            if (!in_array($this->members['groupSno'], $group)) {
                $data['orderPossible'] = 'n';
                $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'ONLY_MEMBER_GROUP';
                $data['orderPossibleMessage'] = $orderPossibleMessage[] = __('특정 회원등급전용 상품');
            }
        }

        // 접근권한 제한
        if ($data['goodsAccessGroup']) {
            $group = explode(\INT_DIVISION, $data['goodsAccessGroup']);
            if (!in_array($this->members['groupSno'], $group)) {
                $data['orderPossible'] = 'n';
                $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'ACCESS_RESTRICTION';
                $data['orderPossibleMessage'] = $orderPossibleMessage[] = __('접근불가 상품');
            }
        }


        // 정기배송 주문 가능 여부 체크
        [$data, $orderPossibleMessage] = $this->canOrderRegularDelivery($data, $orderPossibleMessage);

        $data['orderPossibleMessageList'] = array_unique($orderPossibleMessage);

        // 구매 가능여부 체크
        if ($data['orderPossible'] === 'n') {
            $this->orderPossible = false;
        }

        return $data;
    }

    /**
     * 정기배송 주문 가능 여부 체크
     *
     * @param array $data 상품 정보
     * @param array $orderPossibleMessage 주문 불가 메시지 배열
     * @return array [$data, $orderPossibleMessage]
     */
    private function canOrderRegularDelivery(array $data, array $orderPossibleMessage): array
    {
        $currentCycleType = $data['regularGoodsPossible']['deliveryCycleType'];
        $currentMonthCycle = $data['regularGoodsPossible']['monthCycle'];
        $currentWeekCycle = $data['regularGoodsPossible']['weekCycle'];
        $currentWeekDayCycle = $data['regularGoodsPossible']['weekDayCycle'];
        $regularGoodsDelFl = $data['regularGoodsPossible']['regularGoodsDelFl'];
        $regularGoodsApplyStatus = $data['regularGoodsPossible']['regularGoodsApplyStatus'];

        $canRegularDelivery = true;
        $message = '';

        // 장바구니에서 들어온 정기상품 삭제 여부와 상태값 체크
        if (in_array('y', $regularGoodsDelFl)) {
            $canRegularDelivery = false;
            $message = __('정기배송 신청 불가 상품');
        }

        if (in_array('disabled', $regularGoodsApplyStatus)) {
            $canRegularDelivery = false;
            $message = __('정기배송 신청 불가 상품');
        }

        // 추가상품 있을때 삭제 여부
        $addGoodsNo = $data['addGoodsNo'] ?? null;
        // 현재 추가상품이 장바구니에 있는데
        if (!empty($addGoodsNo)) {
            $currentAddGoods = json_decode(stripslashes($data['addGoods'] ?? ''), true);
            $currentAddGoodsNo = !empty($currentAddGoods) ? array_merge(...array_column($currentAddGoods, 'addGoods')) : [];

                // 상품의 추가상품이 없어진 경우
                if (empty($currentAddGoodsNo) || !empty(array_diff($addGoodsNo, $currentAddGoodsNo))) {
                    $canRegularDelivery = false;
                    $message = __('판매중지 추가상품');
            }
        }

        // 장바구니에서 들어온 배송주기와 현재 상품의 배송 가능 주기 체크
        if (!empty($currentCycleType) && !in_array($data['deliveryCycleType'], $currentCycleType)) {
            $canRegularDelivery = false;
            $message = __('신청 불가 배송주기(종료회차)');
        }

        if ($data['deliveryCycleType'] === 'month') {
            if (!empty($currentMonthCycle) && !in_array($data['deliveryCycle'], $currentMonthCycle)) {
                $canRegularDelivery = false;
                $message = __('신청 불가 배송주기(종료회차)');
            }
        }

        if ($data['deliveryCycleType'] === 'week') {
            if (!empty($currentWeekCycle) && !in_array($data['deliveryCycle'], $currentWeekCycle)) {
                $canRegularDelivery = false;
                $message = __('신청 불가 배송주기(종료회차)');
            }

            if (!empty($currentWeekDayCycle) && !in_array($data['deliveryCycleDay'], $currentWeekDayCycle)) {
                $canRegularDelivery = false;
                $message = __('신청 불가 배송주기(종료회차)');
            }
        }

        // 종료회차 변경 체크
        if (!empty($data['regularGoodsPossible']['maxDeliveryRounds'])) {
            // 설정 -> 미설정으로 변경됨
            if ((int)$data['maxDeliveryRound'] > 0 && (int)max($data['regularGoodsPossible']['maxDeliveryRounds']) === 0) {
                $canRegularDelivery = false;
                $message = __('신청 불가 배송주기(종료회차)');
            }

            // 미설정 -> 설정으로 변경됨
            if ((int)$data['maxDeliveryRound'] === 0 && (int)max($data['regularGoodsPossible']['maxDeliveryRounds']) > 0) {
                $canRegularDelivery = false;
                $message = __('신청 불가 배송주기(종료회차)');
            }

            // 기존보다 작아졌음
            if ((int)$data['maxDeliveryRound'] > (int)max($data['regularGoodsPossible']['maxDeliveryRounds'])) {
                $canRegularDelivery = false;
                $message = __('신청 불가 배송주기(종료회차)');
            }
        }

        if (!$canRegularDelivery) {
            $data['orderPossible'] = 'n';
            $data['orderPossibleCode'] = $data['orderPossibleCodeArr'][] = 'REGULAR_DELIVERY_NO';
            $data['orderPossibleMessage'] = $orderPossibleMessage[] = $message;
            $this->orderPossible = false;
        }

        return [$data, $orderPossibleMessage];
    }

    /**
     * 정기배송 상품 데이터 유효성 검증
     * 동일한 옵션의 정기상품이 이미 장바구니에 있는지 확인
     *
     * @param int $goodsIdx 상품 인덱스
     * @param string $goodsNo 상품번호
     * @param array $goodsData 상품 데이터
     * @return void
     * @throws Exception
     */
    public function validateRegularGoodsData(int $goodsIdx, string $goodsNo, array $goodsData): void
    {
        // 정기상품은 동일한 옵션이 들어온 경우 장바구니에 담지 않도록 정책상 처리
        $optionText = !empty($goodsData['optionText'][$goodsIdx])
            ? json_encode($goodsData['optionText'][$goodsIdx], JSON_UNESCAPED_UNICODE)
            : '';
        $duplicateCount = $this->regularOrderCartRepository->countDuplicateRegularGoods(
            $this->members['memNo'],
            (int)$goodsNo,
            (int)$goodsData['optionSno'][$goodsIdx],
            $optionText
        );

        if ($duplicateCount > 0) {
            throw new RegularGoodsDuplicateOptionException('동일한 상품은 장바구니에 1개만 담을 수 있습니다. 장바구니로 이동합니다.');
        }

        // 배송주기 배열의 값 중 빈 값이 있는지 확인
        if (empty($goodsData['deliveryCycleTypes']) || empty($goodsData['deliveryCycleTypes'][$goodsIdx])) {
            throw new Exception('배송주기를 먼저 선택해주세요.');
        }

        // 배송주기 월/주 정보 배열의 값 중 빈 값이 있는지 확인
        if (empty($goodsData['deliveryCycles']) || empty($goodsData['deliveryCycles'][$goodsIdx])) {
            throw new Exception('배송주기의 월/주 정보가 누락되었습니다.');
        }

        // 배송주기 일자/요일 정보 배열의 값 중 빈 값이 있는지 확인
        if (empty($goodsData['deliveryCycleDays']) || empty($goodsData['deliveryCycleDays'][$goodsIdx])) {
            throw new Exception('배송주기의 일자/요일 정보가 누락되었습니다.');
        }
    }

    /**
     * 정기배송 상품의 배송주기 변경 처리
     *
     * @param array $postValue 변경할 데이터 (sno, deliveryCycleType, deliveryCycle, deliveryCycleDay, maxDeliveryRound)
     * @return void
     * @throws Exception
     */
    public function updateRegularDeliveryCycle(array $postValue): void
    {
        $requiredFields = ['sno', 'deliveryCycleType', 'deliveryCycle', 'deliveryCycleDay', 'maxDeliveryRound'];
        foreach ($requiredFields as $field) {
            if (!isset($postValue[$field]) || $postValue[$field] === '') {
                $this->logger->channel('order')->warning(__METHOD__ . ', Required parameter is missing: ', [$postValue]);
                throw new Exception('필수 값이 누락되었습니다.');
            }
        }

        // 정기배송 상품의 배송주기 정보 업데이트
        $cycleData = [
            'deliveryCycleType' => $postValue['deliveryCycleType'],
            'deliveryCycle' => $postValue['deliveryCycle'],
            'deliveryCycleDay' => $postValue['deliveryCycleDay'],
            'maxDeliveryRound' => $postValue['maxDeliveryRound'],
            'modDt' => date('Y-m-d H:i:s'),
        ];

        $this->regularOrderCartRepository->updateDeliveryCycle(
            (int)$postValue['sno'],
            $this->members['memNo'],
            $cycleData
        );
    }


    /**
     * 정기결제 장바구니 선택 상품의 총 결제금액 계산
     *
     * @param array|null $cartSno 선택한 장바구니 sno 목록
     * @return array 계산된 금액 정보
     * @throws Exception
     */
    public function calculateSelectedCartPrice(?array $cartSno): array
    {
        if (empty($cartSno)) {
            return [
                'cartCnt' => 0,
                'totalGoodsPrice' => 0,
                'totalGoodsMileage' => 0,
                'totalMemberMileage' => 0,
                'totalDeliveryCharge' => 0,
                'totalSettlePrice' => 0,
                'totalMileage' => 0,
            ];
        }

        $regularCartInfo = $this->getCartGoodsData($cartSno);
        $regularGoodsPriceInfo = $this->getRegularGoodsPrice($regularCartInfo);

        return [
            'cartCnt' => $this->cartCnt,
            'totalGoodsPrice' => $regularGoodsPriceInfo['regularTotalPrice'],
            'totalGoodsMileage' => $this->totalGoodsMileage,
            'totalMemberMileage' => $this->totalMemberMileage,
            'totalDeliveryCharge' => $this->totalDeliveryCharge,
            'totalSettlePrice' => $regularGoodsPriceInfo['regularTotalPrice'] + $this->totalDeliveryCharge,
            'totalMileage' => $this->totalMileage,
        ];
    }

    /**
     * 정기결제 장바구니 선택 상품 주문
     *
     * @param array $cartSno 선택한 장바구니 sno 목록
     * @return string URL 인코딩된 장바구니 인덱스
     */
    public function setRegularOrderSelect(array $cartSno): string
    {
        if (empty($cartSno)) {
            return '';
        }

        return urlencode(json_encode($cartSno));
    }

    /**
     * 데이터를 배열형태로 전환 처리
     *
     * @param string $getData
     *
     * @return array
     */
    public function getRegularOrderSelect(string $getData): array
    {
        if (empty($getData) === true) {
            return [];
        }

        return json_decode(urldecode($getData));
    }

    /**
     * 장바구니 수정 (상품코드/옵션코드/상품수량)
     *
     * @param array $arrData 상품 정보 [mode, scmNo, cartMode, goodsNo[], optionSno[], goodsCnt[], couponApplyNo[]]
     *
     */
    public function updateInfoCart(array $arrData): void
    {
        \Logger::channel('order')->info(__METHOD__ . ' updateInfoCart param $arrData : ', [$arrData]);
        // Validation - 상품 수량 체크
        foreach ($arrData['goodsCnt'] as $goodsCnt) {
            if (Validator::number($goodsCnt, 1, null, true) === false) {
                throw new Exception(__('상품 수량 이상으로 장바구니에 해당 상품을 담을 수 없습니다.'));
            }
        }

        // 상품상세의 쿠폰 필드명을 장바구니 쿠폰 필드명으로 변경
        $arrData['memberCouponNo'] = $arrData['couponApplyNo'];
        unset($arrData['couponApplyNo']);
        unset($arrData['useBundleGoods']);

        // 정이결제 장바구니 테이블 필드
        $arrExclude = [
            'siteKey',
            'memNo',
            'directCart',
            'memberCouponNo',
            'scmNo',
            'cartMode',
            'linkMainTheme',
        ];

        $fieldData = DBTableField::setTableField('tableRegularOrderCart', null, $arrExclude);

        $goods = \App::load(\Component\Goods\Goods::class);

        // 상품 번호를 기준으로 장바구니에 담을 상품의 배열을 처리함
        foreach ($arrData['goodsNo'] as $goodsIdx => $goodsNo) {
            foreach ($fieldData as $field) {
                if (in_array($field, ['deliveryCollectFl', 'deliveryMethodFl'])) {
                    if (empty($arrData[$field]) === false) {
                        $getData[$field] = gd_isset($arrData[$field]);
                    } else {
                        unset($fieldData[$field]);
                    }
                } elseif (in_array($field, self::REGULAR_ORDER_FIELD)) {
                    switch ($field) {
                        case 'deliveryCycleType':
                            $getData[$field] = $arrData['deliveryCycleTypes'][$goodsIdx];
                            break;
                        case 'deliveryCycle':
                            $getData[$field] = $arrData['deliveryCycles'][$goodsIdx];
                            break;
                        case 'deliveryCycleDay':
                            $getData[$field] = $arrData['deliveryCycleDays'][$goodsIdx];
                            break;
                        case 'maxDeliveryRound':
                            $getData[$field] = $arrData['maxDeliveryRounds'][$goodsIdx];
                            break;
                        default:
                            $getData[$field] = $arrData[$field];
                            break;
                    }
                } else {
                    $getData[$field] = gd_isset($arrData[$field][$goodsIdx]);
                }
            }

            if (gd_isset($getData['optionText']) && empty($getData['optionText']) === false) {
                $getData['optionText'] = ArrayUtils::removeEmpty($getData['optionText']);
                $getData['optionText'] = json_encode($getData['optionText'], JSON_UNESCAPED_UNICODE);
            }

            // 추가 상품
            if (gd_isset($getData['addGoodsNo']) && empty($getData['addGoodsNo']) === false) {
                // 자연수가 아닐 경우 예외 발생
                array_map(function ($cnt) {
                    return NumberUtils::checkNaturalNumber($cnt);
                }, $getData['addGoodsCnt']);
                $getData['addGoodsNo'] = json_encode(array_filter($getData['addGoodsNo']));
                $getData['addGoodsCnt'] = json_encode(array_filter($getData['addGoodsCnt']));
            }


            // 업데이트
            $getData['modDt'] = date('Y-m-d H:i:s');
            $this->regularOrderCartRepository->updateCartOptionBySno($arrData['sno'], $getData);

            // deliveryCollectFl, deliveryMethodFl 업데이트
            if (($arrData['goodsDeliveryFl'] == 'y' || ($arrData['goodsDeliveryFl'] != 'y' && $arrData['sameGoodsDeliveryFl'] == 'y')) && empty($getData['deliveryCollectFl']) === false && empty($getData['deliveryMethodFl']) === false) {
                unset($getData);
                $getData['deliveryCollectFl'] = gd_isset($arrData['deliveryCollectFl']);
                $getData['deliveryMethodFl'] = gd_isset($arrData['deliveryMethodFl']);
                $getData['modDt'] = date('Y-m-d H:i:s');
                $cartInfo = $this->getCartInfo($arrData['sno'], 'mallSno, siteKey, memNo, directCart, goodsNo');

                $this->regularOrderCartRepository->updateDeliveryMethodByConditions($cartInfo, $getData);
            }

            // 장바구니 변경 갯수 상품 업데이트
            $goods->setCartGoodsCount($goodsNo);
        }
    }

    /**
     * 상품 상세 혜택 계산
     * goodsViewBenefit
     *
     * @param array $getData
     *
     * @return array
     */
    public function regularGoodsViewBenefit(array $getData): array
    {
        if ($getData['goodsMileageExcept'] == 'y') {
            return [];
        }

        gd_isset($getData['goodsMileageExcept'], 'n');
        gd_isset($getData['couponBenefitExcept'], 'n');
        gd_isset($getData['memberBenefitExcept'], 'n');

        $goodsDcPriceFl = $this->mileageGiveInfo['basic']['goodsDcPrice'] === '1';

        $goods = new Goods();
        $goodsData = $goods->getGoodsInfo($getData['goodsNo'][0]);

        //상품 혜택 모듈
        $goodsBenefit = \App::load('\\Component\\Goods\\GoodsBenefit');
        //상품혜택 사용시 해당 변수 재설정
        $goodsData = $goodsBenefit->goodsDataFrontConvert($goodsData);

        if ($goodsDcPriceFl) {
            if (empty($getData['goodsPriceSum'])) {
                $getData['goodsPriceSum'][] = $getData['regularPrice'];
            }
        } else {
            if (empty($getData['goodsPriceSum'])) {
                $getData['goodsPriceSum'][] = $getData['set_goods_price'];
            } else {
                $getData['goodsPriceSum'] = [];
                foreach ($getData['goodsCnt'] as $count) {
                    $getData['goodsPriceSum'][] = $getData['set_goods_price'] * $count;
                }
            }
        }

        $data['goodsMileage'] = 0;
        $data['totalMileage'] = 0;
        $data['totalDcPrice'] = 0;

        $goodsCnt = array_sum($getData['goodsCnt'] ?? []);
        $price['goodsPriceSum'] = array_sum($getData['goodsPriceSum'] ?? []);
        $price['optionPriceSum'] = $goodsDcPriceFl ?  array_sum($getData['optionPriceSum'] ?? []) : array_sum($getData['originOptionPriceSum'] ?? []);
        $price['optionTextPriceSum'] = $goodsDcPriceFl ? array_sum($getData['optionTextPriceSum'] ?? []) : array_sum($getData['originOptionTextPriceSum'] ?? []);
        $price['addGoodsPriceSum'] = array_sum($getData['addGoodsPriceSum'] ?? []);

        $data['goodsMileage'] += $this->getGoodsMileageData($getData['mileageFl'], $getData['mileageGoods'], $getData['mileageGoodsUnit'], $goodsCnt, $price, $goodsData['mileageGroup'], $goodsData['mileageGroupInfo'], $goodsData['mileageGroupMemberInfo'], $getData['deliveryType']);

        $data['totalMileage'] = $data['goodsMileage'];

        return $data;
    }


    /**
     * 장바구니 상품 정보 - 상품/추가상품 마일리지 금액
     *
     * @param string $mileageFl        마일리지 설정 종류
     * @param int    $mileageGoods     마일리지 금액 or Percent
     * @param string $mileageGoodsUnit Percent or Price
     * @param int    $goodsCnt         상품 수량
     * @param array  $goodsPrice       상품 가격 정보
     * @param string $mileageGroup       마일리지 지급 대상
     * @param string $mileageGroupInfo       마일리지 지급 대상 회원그룹
     * @param json   $mileageGroupMemberInfo       마일리지 지급 대상 회원 정보
     *
     * @return int 상품 마일리지 금액
     */
    protected function getGoodsMileageData($mileageFl, $mileageGoods, $mileageGoodsUnit, $goodsCnt, $goodsPrice, $mileageGroup = null, $mileageGroupInfo = null, $mileageGroupMemberInfo = null)
    {
        // 상품 마일리지 금액
        $goodsMileage = 0;

        // 마일리지 지급을 사용하는 경우 마일리지 계산
        if ($this->mileageGiveInfo['info']['useFl'] === 'y') {
            // 마일리지 계산을 위한 기준 금액 처리
            $tmp['mileageByPrice'] = $goodsPrice['goodsPriceSum'];

            // 상품종류에 따른 기준 금액 재설정
            if ($this->mileageGiveInfo['basic']['optionPrice'] === '1') {
                $tmp['mileageByPrice'] = $tmp['mileageByPrice'] + $goodsPrice['optionPriceSum'];
            }
            if ($this->mileageGiveInfo['basic']['addGoodsPrice'] === '1') {
                $tmp['mileageByPrice'] = $tmp['mileageByPrice'] + $goodsPrice['addGoodsPriceSum'];
            }
            if ($this->mileageGiveInfo['basic']['textOptionPrice'] === '1') {
                $tmp['mileageByPrice'] = $tmp['mileageByPrice'] + $goodsPrice['optionTextPriceSum'];
            }
            if ($this->mileageGiveInfo['basic']['goodsDcPrice'] === '1') {
                $tmp['mileageByPrice'] = $tmp['mileageByPrice'] - $goodsPrice['goodsDcPrice'];
            }
            if ($this->mileageGiveInfo['basic']['memberDcPrice'] === '1') {
                $tmp['mileageByPrice'] = $tmp['mileageByPrice'] - $goodsPrice['memberDcPrice'] - $goodsPrice['memberOverlapDcPrice'];
            }
            if ($this->mileageGiveInfo['basic']['couponDcPrice'] === '1') {
                $tmp['mileageByPrice'] = $tmp['mileageByPrice'] - $goodsPrice['couponDcPrice'] - $goodsPrice['couponOrderDcPrice'];
            }

            // 통합 설정인 경우 마일리지
            if ($mileageFl == 'c') {
                // 마일리지 지급 여부
                $mileageGiveFl = true;
                if ($mileageGroup == 'group') { //마일리지 지급대상(특정회원등급)
                    $mileageGroupInfoData = explode(INT_DIVISION, $mileageGroupInfo);

                    $mileageGiveFl = gd_in_array($this->session->get('member.groupSno'), $mileageGroupInfoData);
                }

                if ($mileageGiveFl === true) {
                    if ($this->mileageGiveInfo['give']['giveType'] == 'priceUnit') { // 금액 단위별
                        $mileagePrice = floor($tmp['mileageByPrice'] / $this->mileageGiveInfo['give']['goodsPriceUnit']);
                        $goodsMileage = gd_number_figure($mileagePrice * $this->mileageGiveInfo['give']['goodsMileage'], $this->mileageGiveInfo['trunc']['unitPrecision'], $this->mileageGiveInfo['trunc']['unitRound']);
                    } else if ($this->mileageGiveInfo['give']['giveType'] == 'cntUnit') { // 수량 단위별 (추가상품수량은 제외)
                        $goodsMileage = gd_number_figure($goodsCnt * $this->mileageGiveInfo['give']['cntMileage'], $this->mileageGiveInfo['trunc']['unitPrecision'], $this->mileageGiveInfo['trunc']['unitRound']);
                    } else { // 구매금액의 %
                        $mileagePercent = $this->mileageGiveInfo['give']['goods'] / 100;
                        $goodsMileage = gd_number_figure($tmp['mileageByPrice'] * $mileagePercent, $this->mileageGiveInfo['trunc']['unitPrecision'], $this->mileageGiveInfo['trunc']['unitRound']);
                    }
                }
            }

            // 개별 설정인 경우 마일리지
            if ($mileageFl == 'g') {
                if ($mileageGroup == 'group') { //마일리지 지급대상(특정회원등급)
                    $mileageGroupMemberInfoData = json_decode($mileageGroupMemberInfo, true);
                    $mileageKey = gd_array_flip($mileageGroupMemberInfoData['groupSno'])[$this->memInfo['groupSno']];
                    if ($mileageKey >= 0) {
                        $mileageGoodsUnit = gd_isset($mileageGroupMemberInfoData['mileageGoodsUnit'][$mileageKey], $mileageGoodsUnit);
                        $mileagePercent = $mileageGroupMemberInfoData['mileageGoods'][$mileageKey] / 100;
                        if ($mileageGoodsUnit === 'percent') {
                            // 상품 마일리지
                            $goodsMileage = gd_number_figure($tmp['mileageByPrice'] * $mileagePercent, $this->mileageGiveInfo['trunc']['unitPrecision'], $this->mileageGiveInfo['trunc']['unitRound']);
                        } else {
                            // 상품 마일리지 (정액인 경우 해당 설정된 금액으로)
                            $goodsMileage = $mileageGroupMemberInfoData['mileageGoods'][$mileageKey] * $goodsCnt;
                        }
                    }
                } else {
                    $mileagePercent = $mileageGoods / 100;
                    if ($mileageGoodsUnit === 'percent') {
                        // 상품 마일리지
                        $goodsMileage = gd_number_figure($tmp['mileageByPrice'] * $mileagePercent, $this->mileageGiveInfo['trunc']['unitPrecision'], $this->mileageGiveInfo['trunc']['unitRound']);
                    } else {
                        // 상품 마일리지 (정액인 경우 해당 설정된 금액으로)
                        $goodsMileage = $mileageGoods * $goodsCnt;
                    }
                }
            }
        }

        // 상품 마일리지 적립이 0보다 작으면 0
        if ($goodsMileage < 0) {
            $goodsMileage = 0;
        }

        return $goodsMileage;
    }


    /**
     * 장바구니 상품 수량 변경
     *
     * @param array $getData 장바구니 데이터
     *
     * @throws Exception
     */
    public function setCartCnt(array $getData): void
    {
        // 장바구니 번호와 상품 번호가 없으면 오류
        if (empty($getData['cartSno']) || empty($getData['goodsNo'])) {
            throw new Exception(__('오류가 발생 하였습니다.'));
        }

        // 상품 수량 변경
        if (!empty($getData['goodsCnt'])) {
            // Validation - 상품 수량 체크
            if (Validator::number($getData['goodsCnt'], 1, null, true) === false) {
                throw new Exception(__('상품 수량 이상으로 장바구니에 해당 상품을 담을 수 없습니다.'));
            }

            // 해당 상품의 최대/최소 수량
            $checkCnt = $this->getBuyableStock($getData['goodsNo'], $getData['goodsCnt'], $getData['useBundleGoods']);

            // 수량 업데이트
            $this->regularOrderCartRepository->updateGoodsCntBySno($getData['cartSno'], $checkCnt);

            // 장바구니 변경 갯수 상품 업데이트
            $goods = \App::load(\Component\Goods\Goods::class);
            $goods->setCartGoodsCount($getData['goodsNo']);
        }

        // 추가 상품 수량 변경
        if (empty($getData['goodsCnt']) && empty($getData['addGoodsNo']) === false && empty($getData['addGoodsCnt']) === false) {
            // Validation - 추가 상품 수량 체크
            if (Validator::number($getData['addGoodsCnt'], 1, null, true) === false) {
                throw new Exception(__('상품 수량 이상으로 장바구니에 해당 상품을 담을 수 없습니다.'));
            }

            // 장바구니 상품 정보
            $getAddData = $this->regularOrderCartRepository->findAddGoodsBySno($getData['cartSno']);
            if (empty($getAddData['addGoodsNo']) === false) {
                $getAddData['addGoodsNo'] = json_decode(gd_htmlspecialchars_stripslashes($getAddData['addGoodsNo']), true);
                $getAddData['addGoodsCnt'] = json_decode(gd_htmlspecialchars_stripslashes($getAddData['addGoodsCnt']), true);
                $updateChk = false;
                foreach ($getAddData['addGoodsNo'] as $aKey => $aVal) {
                    // 해당 추가상품 코드의 배열이고 추가상품 수량의 변화가 있는 경우에만 업데이트 처리
                    if ($getData['addGoodsNo'] === strval($aVal) && $getAddData['addGoodsCnt'][$aKey] !== $getData['addGoodsCnt']) {
                        $getAddData['addGoodsCnt'][$aKey] = $getData['addGoodsCnt'];
                        $updateChk = true;
                        break;
                    }
                }

                // 추가 상품 수량 변경 처리
                if ($updateChk === true) {
                    $checkCnt = json_encode($getAddData['addGoodsCnt']);
                    $this->regularOrderCartRepository->updateAddGoodsCntBySno($getData['cartSno'], $checkCnt);
                }
            }
        }
    }
}
