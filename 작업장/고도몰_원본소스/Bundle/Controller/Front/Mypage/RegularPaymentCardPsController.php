<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Front\Mypage;

use Component\RegularDelivery\RegularOrder\RegularOrderPayment;
use Component\Mypage\RegularPaymentHandler;
use Framework\Http\Response;

class RegularPaymentCardPsController extends \Controller\Front\Controller
{
    public function __construct()
    {
        try {
            $postValue = \Request::request()->toArray();
            $mode = $postValue['mode'];
            
            $regularOrderPayment = \App::getInstance(RegularOrderPayment::class);
            $memNo = \Session::get('member.memNo');

            switch ($mode) {
                case 'updateCard':
                    $cardOrder = $postValue['cardOrder'];
                    $regularOrderPayment->changeMainCardAndPriority($memNo, $cardOrder);
                    $message = '카드 정보가 저장되었습니다.';
                    break;
                case 'deleteCard':
                    $cardSno = $postValue['sno'];
                    $regularPaymentFl = $postValue['regularPaymentFl'];
                    $mainCardFl = $postValue['mainCardFl'];

                    // 메인카드 사용 여부 확인
                    if ($mainCardFl == 'y') {
                        throw new \Exception('메인카드는 삭제할 수 없습니다.');
                    }

                    // 정기배송 신청 여부 검증
                    $hasActiveRegularOrderGoods = $regularOrderPayment->hasActiveRegularOrderGoods($memNo, $cardSno);
                    if ($regularPaymentFl == 'y' || $hasActiveRegularOrderGoods) {
                        throw new \Exception('현재 정기배송에 사용 중인 카드입니다. 카드를 삭제하려면 먼저 정기배송 신청을 해지해 주세요.');
                    }

                    $regularOrderPayment->deleteCard($memNo, $cardSno);
                    $message = '카드 정보가 삭제되었습니다.';
                    break;
               default:
                   throw new \Exception('잘못된 모드입니다.');
           }

           $this->json(['success' => true, 'message' => $message]);
        } catch (\Throwable $e) {
            $message = empty($e->getMessage()) ? '일시적인 오류로 처리에 실패하였습니다. 잠시 후 다시 시도해주세요.' : $e->getMessage();
            \Logger::channel('regularOrder')->warning(
                __CLASS__ . ' 마이페이지 카드 정보 처리 실패 : ', 
                [
                    'message' => $message,
                    'type' => get_class($e)
                ]
            );

            $this->json(['success' => false, 'message' => $message]);
        }
    }

}
