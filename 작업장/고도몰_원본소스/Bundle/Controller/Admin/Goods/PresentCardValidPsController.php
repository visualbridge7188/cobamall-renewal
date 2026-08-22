<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Goods;

use Component\Present\Card\PresentCard;
use Component\Present\Exception\PresentCardValidationException;
use Framework\Http\Response;
use Request;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;

class PresentCardValidPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        try {
            $imageFiles = Request::files()->toArray()['presentCard'];

            // 브라우저 기반 검사
            Validator::arrayVal()
                ->key('error', Validator::in(["0", 0]))
                ->key('type', Validator::in(['image/jpeg','image/png']))
                ->key('size', Validator::max(PresentCard::MAX_FILE_SIZE))
                ->assert($imageFiles);

            $presentCard = \App::getInstance(PresentCard::class);
            $presentCard->checkImageRatio($imageFiles['tmp_name']);
            $presentCard->checkImageMime($imageFiles['tmp_name']);
        } catch (NestedValidationException | PresentCardValidationException $presentCardValidationException) {
            \Logger::channel('presentGoods')->warning('Present card validation error.', [
                'error' => $presentCardValidationException->getMessage()
            ]);
            $this->json(
                data: [
                    'result' => 'fail',
                    'message' => $presentCardValidationException->getMessage()
                ],
                status: Response::HTTP_BAD_REQUEST
            );
        } catch (\Throwable $throwable) {
            \Logger::channel('presentGoods')->warning('Present card throwable error.', [
                'error' => $throwable->getMessage()
            ]);
            $this->json(
                data: [
                    'result' => 'fail',
                    'message' => '저장에 실패하였습니다. 다시 시도해주세요.',
                ],
                status: Response::HTTP_BAD_REQUEST
            );
        }
        $this->json(data: ['result' => 'success']);
    }
}
