<?php
/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */

namespace Bundle\Controller\Front\Goods;

use Exception;
use Framework\Debug\Exception\WarningException;
use Framework\Utility\SkinUtils;
use Framework\Utility\StringUtils;
use Origin\Enum\RegularDelivery\RegularGoods\RegularGoodsAttribute;
use Request;
use Session;
use Util\Order\RegularOrderUtil;
use DTO\RegularDelivery\RegularGoods\RegularGoodsValidateDTO;
use Origin\Exception\RegularDelivery\RegularGoods\RegularGoodsValidateException;
use Component\Cart\RegularOrderCart;

/**
 * Class LayerDeliveryAddress
 *
 * @package Bundle\Controller\Front\Order
 * @author  su
 */
class LayerOptionController extends \Controller\Front\Controller
{

    const EXCEPTION_MESSAGES = [
        RegularGoodsAttribute::ERROR_REGULAR_GOODS_DELETE => "삭제된 상품입니다. 다시 선택해 주세요.",
        RegularGoodsAttribute::ERROR_REGULAR_GOODS_OUT_OF_STOCK => "품절된 상품입니다. 다시 선택해 주세요.",
        RegularGoodsAttribute::ERROR_REGULAR_GOODS_APPLY_DISABLED => "신청 불가한 상품입니다. 다시 선택해 주세요.",
        RegularGoodsAttribute::ERROR_REGULAR_GOODS_DELIVERY_DATA_CHANGE => "신청 불가한 상품입니다. 다시 선택해 주세요."
    ];

    /**
     * @inheritdoc
     */
    public function index()
    {
        try {
            //상품 품절 설정 코드 불러오기
            $code = \App::load('\\Component\\Code\\Code',$mallSno ?? null);
            $optionSoldOutCode = $code->getGroupItems('05002');
            $optionSoldOutCode['n'] = $optionSoldOutCode['05002002'];
            $this->setData('optionSoldOutCode', $optionSoldOutCode);

            //상품 배송지연 설정 코드 불러오기
            $code = \App::load('\\Component\\Code\\Code',$mallSno ?? null);
            $optionDeliveryDelayCode = $code->getGroupItems('05003');
            $this->setData('optionDeliveryDelayCode', $optionDeliveryDelayCode);

            if (!Request::isAjax()) {
                throw new WarningException('Ajax ' . __('전용 페이지 입니다.'));
            }

            $postValue = Request::post()->toArray();


            if($postValue['type'] =='wish'  && !Session::has('member') ) {
                $this->js("alert('" . __('로그인하셔야 해당 서비스를 이용하실 수 있습니다.') . "'); top.location.href = '../member/login.php';");
            }

            $goods = \App::load('\\Component\\Goods\\Goods');

            $selectGoodsFl = false;

            /**
             * 레이어 옵션은 장바구니, 찜목록, 장바구니 테마, 마이페이지에서 공용으로 사용되며, 사용처에 따라 허용 타입이 결정
             *  - goods : both, 장바구니 테마에서는 정기배송 상품의 옵션에 따라서 결정
             *  - cart : normal or regular, 장바구니에서는 regularOrderFl 에 따라서 결정
             *  - wish : normal, 찜목록에서는 일반배송만 가능
             *  - mypage : regular, 마이페이지 정기배송 관련 항목에서는 정기배송만 가능
             */
            switch ($postValue['type']) {
                case 'goods':
                    $deliveryOptionType = RegularGoodsAttribute::LAYER_OPTION_USE_BOTH;
                    break;
                case 'mypage':
                    $deliveryOptionType = RegularGoodsAttribute::LAYER_OPTION_USE_REGULAR;
                    break;
                default:
                    $deliveryOptionType = RegularGoodsAttribute::LAYER_OPTION_USE_NORMAL;
                    break;
            }

            $regularGoodsHandler = \App::getInstance(\Component\RegularDelivery\RegularGoods\RegularGoodsHandler::class);
            $regularGoods = \App::getInstance(\Component\RegularDelivery\RegularGoods\RegularGoods::class);

            $useRegularDeliveryFl = gd_policy('order.basic')['useRegularDelivery'] === 'y';

            if ($useRegularDeliveryFl) {
                // 정기배송 상품 신청 가능 여부 확인 및 상태 업데이트
                $regularGoodsHandler->validateRegularGoodsApplyStatus($postValue['goodsNo']);
            }

            // 상품 정보
            $goodsView = $goods->getGoodsView($postValue['goodsNo']);
            if ($goodsView['onlyAdultFl'] == 'y' && gd_check_adult() === false && $goodsView['onlyAdultImageFl'] =='n') {
                $goodsView['image']['detail']['thumb'][0] = SkinUtils::makeImageTag("/data/icon/goods_icon/only_adult_pc.png", '68');
            }

            if($postValue['sno']) {

                if($postValue['type'] =='wish') {
                    $wish = \App::Load(\Component\Wish\Wish::class);
                    $optionInfo = $wish->getWishInfo($postValue['sno']);
                } elseif ($postValue['type'] === 'regular_cart') {
                    /** @var RegularOrderCart $regularOrderCart */
                    $regularOrderCart = \App::getInstance(RegularOrderCart::class);
                    $optionInfo = $regularOrderCart->getCartInfo($postValue['sno']);

                    // 장바구니에서 정기배송 상품일 경우 정기배송 고정 처리
                    $deliveryOptionType = RegularGoodsAttribute::LAYER_OPTION_USE_REGULAR;

                    // 첫 배송예정일 계산
                    $firstDeliveryDate = RegularOrderUtil::calculateOrderDate(date('Y-m-d'), $optionInfo['deliveryCycleType'], $optionInfo['deliveryCycle'], $optionInfo['deliveryCycleDay'], true);
                    $this->setData('firstDeliveryDate', $firstDeliveryDate[0] ?? '');
                } else {
                    $cart = \App::Load(\Component\Cart\Cart::class);
                    $optionInfo = $cart->getCartInfo($postValue['sno']);
                }

                // xss 보안이슈 적용
                if ($optionInfo['memNo'] != Session::get('member.memNo')) {
                    throw new Exception(__('회원정보가 존재하지 않습니다.'));
                }

                if($optionInfo) {

                    if ($optionInfo['memberCouponNo']) {
                        throw new Exception(__('쿠폰 적용 취소 후 옵션 변경 가능합니다.'));
                    }

                    // 추가 상품 정보
                    if (empty($optionInfo['addGoodsNo']) === false) {
                        $optionInfo['addGoodsNo'] = json_decode($optionInfo['addGoodsNo']);
                        $optionInfo['addGoodsCnt'] = json_decode($optionInfo['addGoodsCnt']);

                    } else {
                        $optionInfo['addGoodsNo'] = '';
                        $optionInfo['addGoodsCnt'] = '';
                    }

                    // 텍스트 옵션 정보 (sno, value)
                    $optionInfo['optionTextSno'] = [];
                    $optionInfo['optionTextStr'] = [];
                    if (empty($optionInfo['optionText']) === false) {
                        $arrText = json_decode($optionInfo['optionText']);
                        foreach ($arrText as $key => $val) {
                            $optionInfo['optionTextSno'][] = $key;
                            $optionInfo['optionTextStr'][$key] = $val;
                        }
                    }
                    unset($optionInfo['optionText']);

                    if($goodsView['optionDisplayFl'] =='d' && $optionInfo['optionSno'] ) {
                        foreach($goodsView['option'] as $k => $v) {
                            if($v['sno'] == $optionInfo['optionSno']) {
                                for($i = 1; $i <= 5; $i++) {
                                    if(gd_isset($v['optionValue'.$i])) $optionName[] = $v['optionValue'.$i];
                                }
                                if (!empty($v['originOptionPrice'])) {
                                    $optionInfo['optionSnoText'] = $v['sno'].INT_DIVISION.gd_money_format($v['optionPrice'],false).MARK_DIVISION.gd_money_format($v['originOptionPrice'],false).INT_DIVISION.$v['mileageOption'].INT_DIVISION.$v['stockCnt'].STR_DIVISION.gd_implode("/",$optionName);
                                } else {
                                    $optionInfo['optionSnoText'] = $v['sno'].INT_DIVISION.gd_money_format($v['optionPrice'],false).INT_DIVISION.$v['mileageOption'].INT_DIVISION.$v['stockCnt'].STR_DIVISION.gd_implode("/",$optionName);
                                }
                                if($v['optionSellFl'] == 't') $optionInfo['optionSnoText'] .= STR_DIVISION . '' . $optionSoldOutCode[$v['optionSellCode']] . '';
                                if($v['optionDeliveryFl'] == 't' && $optionDeliveryDelayCode[$v['optionDeliveryCode']] != '') $optionInfo['optionSnoText'] .= STR_DIVISION  . '' . $optionDeliveryDelayCode[$v['optionDeliveryCode']] . '';
                            }
                        }
                    }

                    $this->setData('optionInfo', gd_isset($optionInfo));
                    $selectGoodsFl = true;

                }

            }

            // 이름 처리 추가 (태그가 들어간 이름의 경우 모바일에서 옵션이 제대로 출력안되는 오류가 있어 추가)
            $goodsNm = StringUtils::stripOnlyTags($goodsView['goodsNm']);
            if (empty($goodsNm) === false) {
                // htmlentities 상품명에 "만 들어가는 경우에 대한 처리를 위해 추가
                $goodsView['goodsNm'] = htmlentities($goodsNm);
            }

            // 분리형 옵션인 경우, 노출안함 처리된 1차 옵션값 제거 처리
            if ($goodsView['optionDisplayFl'] === 'd') {
                foreach ($goodsView['option'] as $k => $goodsOptionInfo) {
                    if ($goodsOptionInfo['optionViewFl'] !== 'y') {
                        unset($goodsView['option'][$k]);
                    }
                }
                foreach ($goodsView['option'] as $k => $goodsOptionInfo) {
                    $optionArr[$k] = $goodsOptionInfo['optionValue1'];
                }
                $goodsView['optionDivision'] = gd_array_unique($optionArr);
            }

            // default 구매 최소 수량
            $goodsView['defaultGoodsCnt'] = 1;
            if($goodsView['fixedOrderCnt'] == 'option') {
                $goodsView['defaultGoodsCnt'] = $goodsView['minOrderCnt'];
            }
            if($goodsView['fixedSales'] != 'goods' && ($goodsView['salesUnit'] > $goodsView['defaultGoodsCnt'])) {
                $goodsView['defaultGoodsCnt'] = $goodsView['salesUnit'];
            }

            $this->setData('goodsView', gd_isset($goodsView));
            $this->setData('mainSno', gd_isset($postValue['mainSno']));
            $this->setData('type', gd_isset($postValue['type']));
            $this->setData('page', gd_isset($postValue['page']));
            $this->setData('selectGoodsFl', $selectGoodsFl);

            //상품 노출 필드
            $displayField = gd_policy('display.goods');
            $this->setData('displayField', $displayField['goodsDisplayField']['pc']);
            $this->setData('displayAddField', $displayField['goodsDisplayAddField']['pc']);

            // 상품 옵션가 표시설정 config 불러오기
            $optionPriceConf = gd_policy('goods.display');
            $this->setData('optionPriceFl', gd_isset($optionPriceConf['optionPriceFl'], 'y')); // 상품 옵션가 표시설정

            // 레이어 옵션 타입
            $this->setData('deliveryOptionType', $deliveryOptionType);

            // 배송 방법 내 정기배송이 포함되고 신청가능 상태일 경우, 정기배송 관련 정보 조회 및 설정
            $isRegularDelivery = in_array($deliveryOptionType, [
                RegularGoodsAttribute::LAYER_OPTION_USE_REGULAR,
                RegularGoodsAttribute::LAYER_OPTION_USE_BOTH
            ]);

            // 배송 방법 내 정기배송이 포함될 경우, 정기배송 관련 정보 조회 및 설정
            if ($useRegularDeliveryFl && $isRegularDelivery) {
                $regularGoodsData = $regularGoods->getRegularGoodsData($postValue['goodsNo']);
                $this->setData('regularGoodsData', $regularGoodsData);

                // 정기결제(배송) 상품일 경우, 상태 확인
                if ($postValue['type'] === 'mypage') {
                    $regularGoodsValidateDTO = new RegularGoodsValidateDTO($postValue);
                    $regularGoods->validateGoodsStatus($regularGoodsValidateDTO);
                }
            }

        } catch (RegularGoodsValidateException $e) {
            $this->handleRegularGoodsException($e);
        } catch (Exception $e) {
            if (Request::isAjax()) {
                $this->json([
                    'error' => 0,
                    'message' => $e->getMessage(),
                ]);
            } else {
                throw $e;
            }
        }
    }

    private function handleRegularGoodsException($e)
    {
        $errorCode = $e->getCode();
        $message = self::EXCEPTION_MESSAGES[$errorCode] ?? $e->getMessage();

        $this->json([
            'error' => 0,
            'message' => $message
        ]);
    }
}
