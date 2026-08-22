<?php

namespace Bundle\Controller\Admin\Goods;

use Framework\Debug\Exception\LayerException;
use Component\RegularDelivery\RegularGoods\RegularGoods;
use Component\RegularDelivery\RegularGoods\RegularGoodsHandler;
use Origin\Enum\ManagerTutorialType;
use Origin\Service\Admin\Member\ManagerTutorial\HelperPopupExposureAvailabilityService;
use Origin\Service\Admin\Member\ManagerTutorial\ManagerTutorialTopicService;
use Repository\RegularDelivery\RegularGoods\RegularGoodsRepository;
use DTO\RegularDelivery\RegularGoods\RegularGoodsAdminDTO;
use DTO\RegularDelivery\RegularGoods\RegularGoodsDeleteDTO;
use DTO\RegularDelivery\RegularGoods\RegularGoodsValidateDTO;
use Util\Order\RegularOrderUtil;
use Origin\Enum\RegularDelivery\RegularGoods\RegularGoodsAttribute;
use Origin\Exception\RegularDelivery\RegularGoods\RegularGoodsValidateException;
use Exception;
use Request;

class RegularGoodsPsController extends \Controller\Admin\Controller
{
    const EXCEPTION_MESSAGES = [
        RegularGoodsAttribute::ERROR_REGULAR_GOODS_DELETE => "삭제된 상품입니다. 다시 선택해 주세요.",
        RegularGoodsAttribute::ERROR_REGULAR_GOODS_OUT_OF_STOCK => "품절된 상품입니다. 다시 선택해 주세요.",
        RegularGoodsAttribute::ERROR_REGULAR_GOODS_APPLY_DISABLED => "신청 불가한 상품입니다. 다시 선택해 주세요.",
        RegularGoodsAttribute::ERROR_REGULAR_GOODS_DELIVERY_DATA_CHANGE => "신청 불가한 상품입니다. 다시 선택해 주세요."
    ];

    /**
     * 정기 상품 관련 처리 페이지
     * [관리자 모드] 정기 상품 관련 처리 페이지
     */
    public function index()
    {
        // 폼으로 받은 데이터
        $postValue = Request::post()->toArray();
        try {
            switch ($postValue['mode']) {
                case 'calculateOrderDate':
                    // 배송 타입
                    $deliveryType = $postValue['deliveryType'];
                    // 배송주기(1~6개월/주)
                    $deliveryCycle = $postValue['selectedCycle'];
                    // 배송주기(일/요일)
                    $deliveryCycleDay = $postValue['selectedCycleDay'];

                    // 첫 배송예정일 계산
                    $firstDeliveryDate = RegularOrderUtil::calculateOrderDate(date('Y-m-d'), $deliveryType, $deliveryCycle, $deliveryCycleDay, true);
                    $result['firstDeliveryDate'] = $firstDeliveryDate[0];

                    $this->json([
                        'status' => 'success',
                        'message' => '첫 배송예정일 계산 완료',
                        'data' => $result
                    ]);
                    break;
                case 'register':
                    // 정기 결제(배송) 상품 : 등록

                    $regularGoodsHandler = \App::getInstance(RegularGoodsHandler::class);

                    // 정기 결제(배송) 상품 등록 관련 공통 정보 DTO에 세팅
                    $regularGoodsAdminDTO = new RegularGoodsAdminDTO($postValue);

                    // 정기 결제(배송) 유효성 검사
                    $regularGoodsHandler->validate($postValue['mode'], $regularGoodsAdminDTO);

                    // 정기 결제(배송) 상품 등록
                    $regularGoodsHandler->create($regularGoodsAdminDTO);

                    // 정기결제 설정 완료 카프카 토픽 전송
                    $managerTutorialTopicService = \App::getInstance(ManagerTutorialTopicService::class);
                    $managerTutorialTopicService->produceTutorialStatusChanged(
                        ManagerTutorialType::SUBSCRIPTION_PRODUCT_REGISTER->value,
                        true
                    );

                    $this->layer(__('저장이 완료되었습니다.'));
                    break;
                case 'modify':
                    // 정기 결제(배송) 상품 : 수정

                    $regularGoodsHandler = \App::getInstance(RegularGoodsHandler::class);

                    // 정기 결제(배송) 상품 등록 관련 공통 정보 DTO에 세팅
                    $regularGoodsAdminDTO = new RegularGoodsAdminDTO($postValue);

                    // 정기 결제(배송) 유효성 검사
                    $regularGoodsHandler->validate($postValue['mode'], $regularGoodsAdminDTO);

                    // 정기 결제(배송) 상품 수정
                    $regularGoodsHandler->update($regularGoodsAdminDTO);

                    $this->layer(__('저장이 완료되었습니다.'));
                    break;
                case 'update_regular_goods_admin_memo' :
                    $regularGoodsHandler = \App::getInstance(RegularGoodsHandler::class);
                    $regularGoodsHandler->updateAdminMemo($postValue);

                    $this->layer(__('저장이 완료되었습니다.'));
                    break;
                case 'delete_state':
                    $regularGoodsHandler = \App::getInstance(RegularGoodsHandler::class);

                    // 선택한 정기결제(배송) 상품 삭제
                    $regularGoodsDeleteDTO = new RegularGoodsDeleteDTO([
                        'regularGoodsSnoList' => $postValue['sno'],
                        'modifier' => 'admin',
                        'modifierNo' => \Session::get('manager.sno')
                    ]);
                    $isSuccess = $regularGoodsHandler->deleteRegularGoods($regularGoodsDeleteDTO);

                    $message = $isSuccess
                        ? __('선택한 상품이 삭제되었습니다.')
                        : __('선택 상품 삭제에 실패했습니다. 잠시 후 다시 시도해주세요.');

                    $this->layer($message);
                    break;
                case 'updateApplyStatus':
                    $regularGoodsHandler = \App::getInstance(RegularGoodsHandler::class);

                    $snoList = json_decode($postValue['dataForm'], true);

                    // 정기 결제(배송) 상품 리스트 : 신청 상태 수정
                    $updateResult = $regularGoodsHandler->updateApplyStatusBySno($snoList, $postValue['applyStatus']);

                    switch ($updateResult) {
                        case 'success':
                            $message = __('신청상태 일괄 수정이 완료되었습니다.');
                            break;
                        case 'errorMinPrice':
                            $message = __('정기결제(배송) 신청이 가능한 상품의 최소 금액은 500원입니다. <br>일반 상품의 판매가 혹은 정기결제(배송) 상품의 할인 금액을 수정 후 신청 상태를 변경해주세요.');
                            break;
                        case 'errorStatusUpdateNotAllowed':
                            $message = __('신청상태 일괄 수정이 불가한 상품이 포함되어 있습니다. 자세한 내용은 매뉴얼을 참고하시기 바랍니다.');
                            break;
                        case 'error':
                            $message = __('신청상태 일괄 수정에 실패했습니다. 잠시 후 다시 시도해주세요.');
                            break;
                    }

                    $this->layer($message);
                    break;
                case 'validateRegularDeliveryOption':
                    $regularGoods = \App::getInstance(RegularGoods::class);
                    $regularGoodsValidateDTO = new RegularGoodsValidateDTO($postValue);
                    $regularGoods->validateGoodsStatus($regularGoodsValidateDTO);
                    break;
            }

        } catch (RegularGoodsValidateException $e) {
            $this->handleRegularGoodsException($e, $postValue);
        } catch (Exception $e) {
            \Logger::channel('regularGoods')->warning(__CLASS__ . ' ' . $e->getMessage(), $postValue);

            throw new LayerException($e->getMessage());
        }
    }

    private function handleRegularGoodsException($e, $postValue)
    {
        \Logger::channel('regularGoods')->warning(__CLASS__ . ' ' . $e->getMessage(), $postValue);

        $errorCode = $e->getCode();
        $message = self::EXCEPTION_MESSAGES[$errorCode] ?? $e->getMessage();

        $this->json([
            'status' => 'error',
            'code' => $errorCode,
            'message' => $message,
            'data' => []
        ]);
    }
}
