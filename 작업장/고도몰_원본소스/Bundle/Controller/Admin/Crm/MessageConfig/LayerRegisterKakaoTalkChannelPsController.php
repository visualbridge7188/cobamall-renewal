<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MessageConfig;

use Bundle\Component\Member\KakaoAlrim;
use Bundle\Component\Policy\Policy;
use Framework\Debug\Exception\AlertCloseException;
use Framework\Http\Request;
use Framework\Http\Response;
use GuzzleHttp\Exception\GuzzleException;
use Origin\DTO\Member\KakaoAlrim\KakaoAlirmCloudRegisterDto;
use Origin\DTO\Member\KakaoAlrim\KakaoAlirmGetTokenDto;
use Origin\Service\Member\KakaoAlrim\KakaoAlrimCloudRegistService;
use Origin\Service\Member\KakaoAlrim\KakaoAlrimCloudSyncService;
use Origin\Util\RequestValidation;

class LayerRegisterKakaoTalkChannelPsController extends \Controller\Admin\Controller
{
    /**
     * @throws AlertCloseException
     * @throws GuzzleException
     */
    public function index()
    {
        /** @var Request $request */
        $request = \App::getInstance('request');
        $requestData = $request->request()->toArray();
        $mode = $requestData['mode'];

        switch ($mode) {
            case 'getToken':
                try {
                    switch ($requestData['sender']) {
                        case 'kakaoAlrimCloud':
                            RequestValidation::validate($requestData, ['plusId', 'phoneNumber', 'category']);

                            $dto = new KakaoAlirmGetTokenDto($requestData);

                            /** @var KakaoAlrimCloudRegistService $service */
                            $service = \App::getInstance(KakaoAlrimCloudRegistService::class);

                            $result = $service->getToken($dto);
                            $this->json(['success' => $result, 'message' => $result ? null : '인증 번호 발송 실패']);
                            break;
                        case 'kakaoAlrim':
                            $bizmRequestData = [
                                'yellowId' => $requestData['plusId'],
                                'phoneNumber' => $requestData['phoneNumber']
                            ];

                            $kakaoAlrim = new KakaoAlrim();
                            $result = $kakaoAlrim->getToken($bizmRequestData);

                            if (!empty($result['code']) && $result['code'] == 'success') {
                                $this->json(['success' => true, 'message' => '인증 번호 발송 성공']);
                            } else {
                                $this->json(['success' => false, 'message' => $result['message'] ?? '인증 번호 발송 실패']);
                            }
                            break;
                    }
                } catch (\Throwable $e) {
                    \Logger::channel('kakao')->warning($e->getMessage(), [__METHOD__, $e->getTraceAsString()]);
                    $this->json(['success' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
            case 'register':
                try {
                    RequestValidation::validate($requestData, ['sender', 'plusId', 'token', 'phoneNumber']);
                    $requestData['approvalFl'] = 'y';

                    switch ($requestData['sender']) {
                        case 'kakaoAlrimCloud':
                            $registerDto = new KakaoAlirmCloudRegisterDto($requestData);
                            /** @var KakaoAlrimCloudRegistService $registerService */
                            $registerService = \App::getInstance(KakaoAlrimCloudRegistService::class);
                            $result = $registerService->register($registerDto);
                            /** @var KakaoAlrimCloudSyncService $kakaoAlrimCloudSyncService */
                            $kakaoAlrimCloudSyncService = \App::getInstance(KakaoAlrimCloudSyncService::class);
                            $kakaoAlrimCloudSyncService->syncMessageTemplate();

                            if ($result) {
                                $this->json([
                                    'success' => true,
                                    'message' => '카카오톡 채널이 등록되었습니다.',
                                    'data' => [
                                        'alertMessage' => [
                                            'message' => '카카오톡 채널이 등록되었습니다.<br>메시지 설정 화면에서 등록된 채널 정보를 확인할 수 있습니다.'
                                        ],
                                        'requestData' => $requestData
                                    ]
                                ]);
                            } else {
                                \Logger::channel('kakao')->warning('카카오톡 채널 등록에 실패했습니다.', [$requestData['sender']]);
                                throw new \Exception("카카오톡 채널 등록에 실패했습니다.");
                            }
                            break;
                        case 'kakaoAlrim':
                            $kakaoAlrim = new KakaoAlrim();
                            $profile = $kakaoAlrim->getKakaoKey($requestData);

                            if ($profile['result'] == 'success') {
                                $requestData['approvalFl'] = 'y';
                                $requestData['approvalDt'] = date('Y-m-d');
                                $requestData['approvalId'] = \Session::get('manager.managerId');
                                $requestData['kakaoKey'] = $profile['data'];

                                /** @var Policy $policy */
                                $policy = \App::load('\\Component\\Policy\\Policy');
                                $result = $policy->saveKakaoAlrimConfig($requestData);

                                if($result) {
                                    $kakaoAlrim->saveKakaoAuto($requestData);
                                    $this->json([
                                        'success' => true,
                                        'message' => '카카오톡 채널이 등록되었습니다.',
                                        'data' => [
                                            'alertMessage' => [
                                                'message' => '카카오톡 채널이 등록되었습니다.<br>메시지 설정 화면에서 등록된 채널 정보를 확인할 수 있습니다.'
                                            ],
                                            'requestData' => $requestData
                                        ]
                                    ]);
                                } else {
                                    \Logger::channel('kakao')->warning('카카오톡 채널 등록에 실패하였습니다.', [$requestData['sender']]);
                                    throw new \Exception("카카오톡 채널 등록에 실패했습니다.");
                                }
                            }
                            break;
                    }
                } catch (\Throwable $e) {
                    \Logger::channel('kakao')->warning($e->getMessage(), [__METHOD__, $e->getTraceAsString()]);
                    $this->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                        'data' => [
                            'alertMessage' => [
                                'message' => '카카오톡 채널 등록에 실패했습니다.',
                                'subMessage' => '카카오 채널의 관리자 설정 또는 비즈니스 인증 상태를 확인 후 다시 시도해 주세요.'
                            ]
                        ]
                    ]);
                }
                break;
            default:
                throw new AlertCloseException(__("잘못된 접근입니다."));
        }
    }
}
