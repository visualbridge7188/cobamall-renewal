<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Goods;

use Component\Present\Admin\PresentAdmin;
use Component\Present\Config\PresentConfig;
use Component\Present\Exception\PresentCardDisplayException;
use Component\Present\Exception\PresentCardMessageLengthException;
use Component\Present\Exception\PresentCardValidationException;
use Component\Present\Exception\PresentGoodsLimitOverException;
use Framework\Http\Response;
use Request;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator;

/**
 * 선물하기 설정 저장
 */
class PresentConfigPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $postValue = Request::post()->toArray();
        $presentConfig = \App::getInstance(PresentConfig::class);

        try {
            $mode = $postValue['mode'];

            switch ($mode) {
                case 'showPresentBanner':
                    // 배너 다시 보지 않기 설정
                    $session = \App::getInstance('session');
                    $session->set('showPresentBanner', false);
                    break;
                case 'presentConfig':
                    // 선물하기 설정 저장
                    // post 유효성 검사
                    Validator::arrayVal()
                        ->key('useFl', Validator::notEmpty()->in(['y', 'n']))
                        ->key('expirationPeriod', Validator::digit()->in($presentConfig->getExpirationPeriod()))
                        ->key('applyType', Validator::in($presentConfig->getApplyType()))
                        ->key('card', Validator::notEmpty())
                        ->assert($postValue);

                    // 선물하기 데이터 저장
                    $presentAdmin = \App::getInstance(PresentAdmin::class);
                    $saveResult = $presentAdmin->save($postValue, Request::files()->toArray());

                    if ($saveResult === false) {
                        \Logger::channel('presentGoods')->warning('PresentConfig save error.', [
                            'postValue' => $postValue
                        ]);
                        $this->json(
                            data: [
                                'result' => 'fail',
                                'message' => '저장에 실패하였습니다. 다시 시도해주세요.'
                            ],
                            status: Response::HTTP_INTERNAL_SERVER_ERROR
                        );
                    }
                    break;
                default:
                    throw new ValidationException('Not Define mode');
            }
        } catch (PresentGoodsLimitOverException
                | PresentCardDisplayException
                | PresentCardMessageLengthException
                | PresentCardValidationException $presentException) {
            $map = [
                PresentGoodsLimitOverException::class   => 'fail_goods_limit',
                PresentCardDisplayException::class      => 'fail_card_display',
                PresentCardValidationException::class   => 'fail_card_validate',
                PresentCardMessageLengthException::class   => 'fail_card_message_length',
            ];

            $this->json(
                data: [
                    'result' => $map[$presentException::class] ?? 'fail',
                    'message' => $presentException->getMessage(),
                ],
                status: Response::HTTP_BAD_REQUEST
            );
        } catch (ValidationException $e) {
            \Logger::channel('presentGoods')->warning('PresentConfig postData validate error.', [
                'error' => $e->getMessage()
            ]);
            $this->json(
                data: [
                    'result' => 'fail',
                    'message' => __('잘못된 요청입니다.')],
                status: Response::HTTP_BAD_REQUEST
            );
        } catch (\Throwable $throwable) {
            \Logger::channel('presentGoods')->warning('Present save Throwable error.', [
                'error' => $throwable->getMessage(),
                'trace' => $throwable->getTrace(),
            ]);
            $this->json(
                data: [
                    'result' => 'fail',
                    'message' => '저장에 실패하였습니다. 다시 시도해주세요.'
                ],
                status: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        $this->json(data: ['result' => 'success']);
    }
}
