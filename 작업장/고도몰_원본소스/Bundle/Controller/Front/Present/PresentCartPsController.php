<?php

namespace Bundle\Controller\Front\Present;

use Component\Present\Config\PresentConfig;
use Component\Present\Cart\PresentCart;
use Component\Present\Exception\PresentStockException;
use Exception;
use Request;
use Framework\Http\Response;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator;

class PresentCartPsController extends \Controller\Front\Controller
{
    public function index()
    {
        try {
            // 선물하기 기능 사용 가능 여부 체크
            $presentConfig = \App::getInstance(PresentConfig::class);
            if (!$presentConfig->isUsePresent()) {
                $this->json([
                    'success' => false,
                    'message' => '선물하기 기능이 비활성화되어 있습니다.'
                ]);
            }

            $postValue = Request::post()->toArray();

            // cartIdx 검증
            $cartValidator = Validator::key('cartIdx', Validator::notEmpty()->stringType())
                ->setTemplate('선물하기 상품 정보가 없습니다.');
            $cartValidator->assert($postValue);

            // 선물하기 관련 정보 데이터 유효성 검증
            $validator = Validator::arrayVal()
                ->key('cardNo', Validator::stringType(), false)
                ->key('cardMessage', Validator::stringType()->length(null, 200), false)
                ->key('receivers', Validator::arrayType()->notEmpty(), true);
            $validator->assert($postValue);

            $presentCartService = \App::getInstance(PresentCart::class);
            
            // 재고 체크
            $presentCartService->checkCartStock($postValue['cartIdx']);       
            // 선물하기 정보 es_presentCart 테이블에 저장
            $presentCartService->savePresentCart($postValue);
            
            $this->json([
                'success' => true,
                'cartIdx' => $postValue['cartIdx']
            ]);
            
        } catch (PresentStockException | \InvalidArgumentException $e) {
            \Logger::channel('presentOrder')->warning(__METHOD__ . ', ' . $e->getFile() . '[' . $e->getLine() . '], ' . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (ValidationException $e) {
            \Logger::channel('presentOrder')->warning(__METHOD__ . ', ' . $e->getFile() . '[' . $e->getLine() . '], ' . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => '선물하기에 실패했습니다. 입력 정보를 확인해주세요.',
            ], Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            \Logger::channel('presentOrder')->warning(__METHOD__ . ', ' . $e->getFile() . '[' . $e->getLine() . '], ' . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => '선물하기에 실패했습니다. 다시 시도해주세요.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
