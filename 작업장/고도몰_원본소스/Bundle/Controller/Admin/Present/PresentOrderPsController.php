<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Present;

use App;


use Framework\Http\Response;
use Respect\Validation\Validator;
use Respect\Validation\Exceptions\ValidationException;
use Component\Order\OrderNew;
use Component\Present\Confirm\PresentConfirm;
use Component\Present\Notification\PresentNotification;
use Component\Present\Order\PresentOrderAdmin;
use Component\Sms\Code;
/**
 * 선물하기 주문 처리 페이지
 */
class PresentOrderPsController extends \Controller\Admin\Controller
{
    /**
     * {@inheritdoc}
     */
    public function index()
    {
        $request = \App::getInstance('request');
        $mode = $request->post()->get('mode');
        $orderNo = $request->post()->get('orderNo');

        switch ($mode) {
            // 선물메세지 재발송
            case 'resendPresentMessage':
                try {
                    $sno = $request->post()->get('sno');
                    
                    // sno 검증
                    Validator::notEmpty()->assert($sno);
                    Validator::notEmpty()->assert($orderNo);

                    /** @var \Bundle\Component\Present\Order\PresentOrderAdmin $presentOrderAdmin */
                    $presentOrderAdmin = App::getInstance(PresentOrderAdmin::class);
                    /** @var \Bundle\Component\Present\Notification\PresentNotification $presentNotification */
                    $presentNotification = App::getInstance(PresentNotification::class);
                    /** @var \Bundle\Component\Order\OrderNew $orderNew */
                    $orderNew = App::load(OrderNew::class);

                    // 주문 상태 검증
                    $orderData = $orderNew->getOrderData($orderNo) ?? [];
                    $presentReceiver = $presentOrderAdmin->getPresentReceiverList($orderNo)[0] ?? [];
                    if (empty($orderData) || $orderData['orderStatus'] !== 'p1') {
                        throw new \Exception('선물메시지 재발송 - 결제완료 상태가 아니거나 주문데이터가 존재하지 않음');
                    }
                    if (empty($presentReceiver)) {
                        throw new \Exception('선물메시지 재발송 - 선물 수령자 데이터 조회 실패');
                    }

                    // 수령자 정보 초기화
                    $receiverData = [
                        'sno' => $presentReceiver['sno'],
                        'receiverName' => $presentReceiver['receiverName'],
                        'phone' => $presentReceiver['phone'],
                        'cellPhone' => $presentReceiver['cellPhone'],
                        'zipcode' => "",
                        'address' => "",
                        'addressSub' => "",
                        'orderMemo' => "",
                        'acceptFl' => PresentConfirm::PRESENT_ACCEPT_READY
                    ];
                    $presentOrderAdmin->updateReceiverInfo($orderNo, $receiverData, true);

                    // 선물메세지 발송
                    $result = $presentNotification->sendPresentInfo(Code::PRESENT, $orderNo);
                    if(!$result) {
                        throw new \Exception('선물메시지 재발송 실패');
                    }

                    $this->json(['result' => 'success', 'message' => __('재발송에 성공했습니다.')], Response::HTTP_OK);
                } catch (\Throwable $e) {
                    $this->handleError(
                        $e,
                        '선물메시지 재발송',
                        ['sno' => $sno ?? null, 'orderNo' => $orderNo ?? null],
                        '재발송에 실패했습니다. 다시 시도해주세요.'
                    );
                }
                break;

            default:
                $this->json([
                    'result' => 'error',
                    'message' => __('지원하지 않는 모드입니다.')
                ], Response::HTTP_BAD_REQUEST);
                break;
        }
    }

    /**
     * 에러 처리 공통 함수
     *
     * @param \Exception $e 예외 객체
     * @param string $logPrefix 로그 메시지 prefix
     * @param array $context 로그에 포함할 컨텍스트 데이터
     * @param string $errorMessage 사용자에게 보여줄 에러 메시지
     * @return void
     */
    protected function handleError(\Throwable $e, string $logPrefix, array $context, string $errorMessage): void
    {
        $logContext = array_merge($context, [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        if ($e instanceof ValidationException) {
            \Logger::channel('presentOrder')->warning($logPrefix . ' 검증 실패', $logContext);
            $this->json(['result' => 'error', 'message' => $errorMessage], Response::HTTP_BAD_REQUEST);
        } elseif ($e instanceof \InvalidArgumentException) {
            \Logger::channel('presentOrder')->warning($logPrefix . ' 파라미터 오류', $logContext);
            $this->json(['result' => 'error', 'message' => $errorMessage], Response::HTTP_BAD_REQUEST);
        } else {
            $logContext['trace'] = $e->getTraceAsString();
            \Logger::channel('presentOrder')->warning($logPrefix . ' 실패', $logContext);
            $this->json(['result' => 'error', 'message' => $errorMessage], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
