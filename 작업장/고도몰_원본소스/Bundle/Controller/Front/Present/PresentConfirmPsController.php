<?php

/*
Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved

Unauthorized copying or redistribution of this file in source and binary forms via any medium
is strictly prohibited.
*/

namespace Bundle\Controller\Front\Present;

use Request;
use Logger;
use Component\Present\Confirm\PresentConfirm;
use DTO\Present\ReceiverInfoUpdateDTO;
use Framework\Http\Response;
use Respect\Validation\Validator;
use Respect\Validation\Exceptions\ValidationException;
use Component\Present\Exception\PresentValidationException;

/**
 * 선물수락/배송지입력 처리 컨트롤러
 *
 */
class PresentConfirmPsController extends \Controller\Front\Controller
{
    public function index()
    {

        try {
            $postValue = Request::post()->toArray();

            // mode 검증
            Validator::key('mode', Validator::in(['accept', 'changeDelivery', 'reject']))
                ->setTemplate('유효하지 않은 요청입니다.')
                ->assert($postValue);

            $mode = $postValue['mode'];

            // 토큰 검증
            $confirmToken = $postValue['token'] ?? '';
            Validator::notEmpty()
                ->setTemplate('유효하지 않은 링크입니다.')
                ->assert($confirmToken);

            // 확인토큰으로 주문번호+주문상품번호 조회
            $presentConfirm = \App::getInstance(PresentConfirm::class);
            $orderInfo = $presentConfirm->getOrderInfoByConfirmToken($confirmToken);

            Validator::not(Validator::falseVal())
                ->key('orderNo', Validator::notEmpty())
                ->key('orderGoodsNo', Validator::notEmpty())
                ->setTemplate('해당 URL은 파기되어 더 이상 접근할 수 없습니다.')
                ->assert($orderInfo);

            $orderNo = $orderInfo['orderNo'];
            $orderGoodsNo = $orderInfo['orderGoodsNo'];

            // mode에 따른 처리
            switch ($mode) {
                case 'accept':
                    // 선물 수락일 때만 개인정보 수집 이용 동의 검증
                    Validator::key('agreePrivacy', Validator::notEmpty()->equals('on'))
                        ->setTemplate('개인정보 수집 이용에 동의해주세요.')
                        ->assert($postValue);

                    // no break
                case 'changeDelivery':
                    // 배송 정보 검증
                    Validator::key('name', Validator::notEmpty())
                        ->key('cellPhone', Validator::notEmpty())
                        ->key('zonecode', Validator::notEmpty())
                        ->key('address', Validator::notEmpty())
                        ->key('addressSub', Validator::notEmpty())
                        ->setTemplate('배송정보를 모두 입력하셔야 선물을 받을 수 있습니다.')
                        ->assert($postValue);

                    // 배송 정보 DTO 생성
                    $receiverDTO = new ReceiverInfoUpdateDTO([
                        'receiverName' => $postValue['name'],
                        'phone' => '',
                        'cellPhone' => $postValue['cellPhone'],
                        'zonecode' => $postValue['zonecode'],
                        'zipcode' => $postValue['zipcode'] ?? $postValue['zonecode'],
                        'address' => $postValue['address'],
                        'addressSub' => $postValue['addressSub'],
                        'orderMemo' => $postValue['message'] ?? '',
                        'acceptFl' => PresentConfirm::PRESENT_ACCEPT_COMPLETED
                    ]);

                    // 배송 정보 저장 및 업데이트된 배송 정보 반환
                    $receiverInfo = $presentConfirm->saveReceiverInfo($orderNo, $orderGoodsNo, $receiverDTO, $mode);

                    if (empty($receiverInfo)) {
                        throw new \Exception('수령자 정보 저장에 실패했습니다.');
                    }

                    $result = [
                        'success' => true,
                        'msg' => $presentConfirm->formatAcceptMessage($receiverInfo),
                        'redirect' => './present_confirm.php?token=' . urlencode($confirmToken)
                    ];
                    break;

                case 'reject':
                    // 선물 거절 처리
                    $presentConfirm->rejectPresent($orderNo, $orderGoodsNo);

                    $result = [
                        'success' => true,
                        'msg' => '선물을 거절했습니다.',
                        'redirect' => './present_confirm.php?token=' . urlencode($confirmToken)
                    ];
                    break;
            }

            $this->json($result, Response::HTTP_OK);
        } catch (ValidationException $e) {
            Logger::channel('presentConfirm')->warning(__CLASS__ . ' 선물수락/배송지입력 처리 검증 실패 : ', [
                'error' => $e->getMainMessage(),
                'postValue' => $postValue ?? []
            ]);

            $this->json(['success' => false, 'msg' => '처리 중 오류가 발생했습니다.'], Response::HTTP_BAD_REQUEST);

        } catch (PresentValidationException $e) {
            Logger::channel('presentConfirm')->warning(__CLASS__ . ' 선물 상태 검증 실패 : ', [
                'error' => $e->getMessage(),
                'postValue' => $postValue ?? []
            ]);

            $this->json(['success' => false, 'msg' => $e->getMessage()], Response::HTTP_BAD_REQUEST);

        } catch (\Exception $e) {
            Logger::channel('presentConfirm')->warning(__CLASS__ . ' 선물수락/배송지입력 처리 예외 발생 : ', [
                'error' => $e->getMessage(),
                'postValue' => $postValue ?? []
            ]);

            $this->json(['success' => false, 'msg' => '처리 중 오류가 발생했습니다.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
