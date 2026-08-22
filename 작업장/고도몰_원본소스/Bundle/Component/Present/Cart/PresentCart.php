<?php

namespace Bundle\Component\Present\Cart;

use DTO\Present\Cart\PresentCartInsertDTO;
use Repository\Cart\CartRepository;
use Repository\Present\Cart\PresentCartRepository;
use Framework\Log\Logger;
use Component\Present\Config\PresentConfig;
use Component\Policy\Policy;
use Framework\Utility\StringUtils;
use Carbon\Carbon;
use Repository\Goods\AddGoodsRepository;
use Repository\Goods\GoodsOptionRepository;
use Component\Cart\Cart;
use Component\Present\Exception\PresentStockException;

class PresentCart
{
    public function __construct(
        private readonly Cart $cart,
        private readonly PresentConfig $presentConfig,
        private readonly AddGoodsRepository $addGoodsRepository,
        private readonly CartRepository $cartRepository,
        private readonly GoodsOptionRepository $goodsOptionRepository,
        private readonly PresentCartRepository $presentCartRepository,
        private readonly Policy $policy,
        private readonly Logger $logger
    ) {
    }

    /**
     * 선물하기 버튼으로 넘어온 장바구니 항목들을 directCart = 'present' 로 변경 후, 선물하기 정보 페이지 URL 생성
     *
     * @param int $cartSno 장바구니 항목
     * @return string 선물하기 정보 페이지 URL
     */
    public function updateAndBuildUrl(int $cartSno): string
    {
        // directCart = 'present' 로 변경
        $this->cartRepository->updateDirectCart([$cartSno], 'present');

        // 선물하기 정보 페이지 URL 생성
        return $this->generatePresentInfoUrl($cartSno);
    }

    /**
     * 선물하기 정보 페이지 URL 생성
     *
     * @param int $cartSno 장바구니 항목
     * @return string 선물하기 정보 페이지 URL
     */
    protected function generatePresentInfoUrl(int $cartSno): string
    {
        $cartIdx = $this->encodeCartIdx($cartSno);
        return '../present/present_write.php?cartIdx=' . $cartIdx;
    }

    /**
     * cartIdx 인코딩
     *
     * @param int $cartSno 장바구니 항목
     * @return string 인코딩된 cartIdx
     */
    protected function encodeCartIdx(int $cartSno): string
    {
        return urlencode($cartSno);
    }

    /**
     * 선물하기 정보를 저장
     *
     * @param array $postData POST로 넘어온 선물하기 데이터
     * @throws \Exception 처리 실패시
     */
    public function savePresentCart(array $postData): void
    {
        try {
            // 장바구니 정보 전처리
            $processedData = $this->convertCartData($postData);

            // 전처리된 데이터를 Insert DTO 배열로 변환
            $insertDTOs = $this->convertDataToInsertDTOs($processedData);

            // DTO 배열을 DB 저장용 배열로 변환
            $insertRows = array_map(fn (PresentCartInsertDTO $insertDTO) => $insertDTO->toDbArray(), $insertDTOs);

            // es_presentCart 테이블에 저장
            $this->presentCartRepository->insertPresentCartRows($insertRows);
        } catch (\Exception $e) {
            $this->logger->channel('presentOrder')->warning(__METHOD__ . ', 선물하기 저장 실패: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * cartIdx 디코딩, 수령자 데이터 정제 등 데이터 전처리
     *
     * @param array $rawData POST로 넘어온 원본 데이터
     * @return array 처리된 선물하기 데이터
     */
    protected function convertCartData(array $rawData): array
    {
        // cartIdx가 있으면 디코딩하여 cartSno로 변환
        if (isset($rawData['cartIdx']) && !isset($rawData['cartSno'])) {
            $rawData['cartSno'] = $this->decodeCartIdx($rawData['cartIdx']);
            unset($rawData['cartIdx']);
        }

        // 수령자 수에 맞게 데이터 정제
        if (isset($rawData['receivers'])) {
            $rawData['receivers'] = $this->prepareReceiverData($rawData['receivers']);
        }

        return $rawData;
    }

    /**
     * 인코딩된 cartIdx를 디코딩하여 실제 cartSno 값 추출
     *
     * @param string $encodedCartIdx 인코딩된 cartIdx (예: "1234")
     * @return int cartSno 값
     * @throws \Exception 디코딩 실패시
     */
    public function decodeCartIdx(string $encodedCartIdx): int
    {
        // URL 디코딩
        $decodedString = urldecode($encodedCartIdx);

        if (is_numeric($decodedString)) {
            return (int) $decodedString;
        }

        throw new \Exception('선물하기 상품 정보가 올바르지 않습니다.');
    }

    /**
     * 전처리된 데이터를 Insert DTO 배열로 변환
     *
     * @param array $processedData 전처리된 데이터
     * @return PresentCartInsertDTO[] Insert DTO 배열
     */
    private function convertDataToInsertDTOs(array $processedData): array
    {
        $receivers = $processedData['receivers'] ?? [];

        if (empty($receivers)) {
            throw new \InvalidArgumentException('최소 1명 이상의 수령자 정보가 필요합니다.');
        }

        $insertDTOs = [];
        $cartSno = (int) ($processedData['cartSno'] ?? 0);
        $cardNo = (int) ($processedData['cardNo'] ?? 0);
        $cardMessage = $processedData['cardMessage'] ?? '';

        foreach ($receivers as $receiver) {
            $insertDTOs[] = PresentCartInsertDTO::createForInsert(
                cartSno: $cartSno,
                receiverName: $receiver['name'],
                cellPhone: $receiver['phone'],
                cardSno: $cardNo,
                cardMessage: $cardMessage
            );
        }

        return $insertDTOs;
    }

    /**
     * 수령자 데이터를 정제
     *
     * @param array $originalReceivers 원본 수령자 배열
     * @return array 정제된 수령자 배열
     */
    private function prepareReceiverData(array $originalReceivers): array
    {
        $receivers = [];

        foreach ($originalReceivers as $receiver) {
            if (isset($receiver['name'], $receiver['phone'])) {
                $name = $receiver['name'];
                $phone = $receiver['phone'];

                $receivers[] = [
                    'name' => $name,
                    'phone' => $phone
                ];
            }
        }

        return $receivers;
    }

    /**
     * 장바구니 내 선물하기 상품 여부 체크
     *
     * @param int $cartSno 장바구니 항목
     * @return bool 선물하기 상품 여부
     */
    public function isPresentCartGoods(int $cartSno): bool
    {
        // 선물하기 기능 사용 가능 여부 체크
        if (!$this->presentConfig->isUsePresent()) {
            return false;
        }

        // 장바구니 내 선물하기 상품 여부 체크
        return $this->cartRepository->existsPresentCartByCartSno($cartSno);
    }

    /**
     * 선물하기 수령자 정보 조회
     *
     * @param int $cartSno 장바구니 항목
     * @return array 선물하기 수령자 정보
     */
    public function getPresentCartReceiverInfo(int $cartSno): array
    {
        $presentCartReceiverInfo = $this->presentCartRepository->findPresentCartReceiverInfoByCartSno($cartSno);

        if (empty($presentCartReceiverInfo)) {
            return [
                'receiverName' => '',
                'cellPhone' => ''
            ];
        }

        // TODO: N명의 수령자 정보로 변경될 경우, 전체 수령자 데이터에 대해 가져오도록 수정 필요
        // 단일 수령자 정보만 반환
        $receiver = $presentCartReceiverInfo[0];
        return [
            'receiverName' => $receiver['receiverName'] ?? '',
            'cellPhone' => $receiver['cellPhone'] ?? ''
        ];
    }

    /**
     * 장바구니 데이터 배열에서 첫 번째 sno 값 추출
     *
     * @param array $cartData 장바구니 데이터
     * @return int|null 추출된 cartSno, 없으면 null
     */
    public function extractCartSno(array $cartData): ?int
    {
        $cartSno = null;
        array_walk_recursive($cartData, function ($value, $key) use (&$cartSno) {
            if ($key === 'sno' && $cartSno === null) {
                $cartSno = (int)$value;
            }
        });

        return $cartSno;
    }

    /**
     * 장바구니 데이터가 선물하기 주문으로 진행될 것인지 확인
     * (주문 생성 시점에서 호출됨)
     *
     * @param array $cartData 장바구니 데이터
     * @return bool 선물하기 주문 여부 (장바구니에 선물하기 상품이 있는지 확인)
     */
    public function isPresentCartOrder(array $cartData): bool
    {
        if (empty($cartData)) {
            return false;
        }

        $cartSno = $this->extractCartSno($cartData);

        if ($cartSno === null) {
            return false;
        }

        return $this->isPresentCartGoods($cartSno);
    }

    /**
     * 선물 배송기한 입력 만료일
     *
     * @return string
     */
    public function getExpirationDatetime(): string
    {
        $presentConfigs = $this->policy->getValue('goods.present');
        $expirationPeriod = StringUtils::strIsSet($presentConfigs['expirationPeriod'], "7");

        return Carbon::now()
            ->addDays((int)$expirationPeriod)
            ->format('Y-m-d');
    }

    /**
     * 장바구니 상품 재고 체크
     *
     * @param string $cartIdx 장바구니 번호
     * @throws PresentStockException 재고 부족 시
     */
    public function checkCartStock(string $cartIdx): void
    {
        // cartIdx 디코딩
        $cartSno = $this->decodeCartIdx($cartIdx);

        // 장바구니 정보 가져오기
        $cartInfo = $this->cart->getCartGoodsData([$cartSno], null, null, true);
        if (empty($cartInfo)) {
            throw new \Exception('선물하기 상품 정보를 찾을 수 없습니다.');
        }

        // 각 상품의 재고 체크
        foreach ($cartInfo as $scmGroup) {
            foreach ($scmGroup as $deliveryGroup) {
                foreach ($deliveryGroup as $goods) {
                    $goodsNo = $goods['goodsNo'];
                    $goodsCnt = $goods['goodsCnt'];
                    $optionSno = $goods['optionSno'] ?? null;

                    // 추가상품인 경우
                    if ($goods['goodsType'] === 'addGoods') {
                        $addGoodsNo = $goodsNo;
                        $addGoodsData = $this->addGoodsRepository->findStockInfoByAddGoodsNo($addGoodsNo);
                        if ($addGoodsData) {
                            // 품절 체크 (재고 사용 여부와 관계없이)
                            if ($addGoodsData['soldOutFl'] === 'y') {
                                throw new PresentStockException();
                            }
                            
                            // 재고 사용(stockUseFl = '1')인 경우 재고 수량 체크
                            if ($addGoodsData['stockUseFl'] == '1') {
                                if ($addGoodsData['stockCnt'] == 0 || $addGoodsData['stockCnt'] - $goodsCnt < 0) {
                                    throw new PresentStockException();
                                }
                            }
                        }
                    } else {
                        // 일반 상품 재고 체크
                        $soldOutFl = $goods['soldOutFl'] ?? 'n';
                        $stockFl = $goods['stockFl'] ?? 'n';
                        $totalStock = $goods['totalStock'] ?? 0;
    
                        if ($soldOutFl === 'y' || ($soldOutFl === 'n' && $stockFl === 'y' && ($totalStock <= 0 || $totalStock < $goodsCnt))) {
                            throw new PresentStockException();
                        }

                        $optionFl = $goods['optionFl'] ?? 'n';
                        // 일반 상품인 경우 - 옵션을 사용함이고 optionSno가 있는 경우에만 옵션 재고 체크
                        if ($optionFl === 'y' && !empty($optionSno)) {
                            // optionSno를 배열로 변환
                            $optionSnoArray = is_array($optionSno) ? $optionSno : [$optionSno];

                            // 옵션 재고 정보 조회
                            $optionDataBySno = $this->goodsOptionRepository->findStockInfoByGoodsNoAndOptionSnoArray($goodsNo, $optionSnoArray);

                            foreach ($optionSnoArray as $sno) {
                                if (empty($optionDataBySno[$sno])) {
                                    throw new \InvalidArgumentException('옵션 정보를 찾을 수 없습니다.');
                                }

                                $optionData = $optionDataBySno[$sno];
                                if ($optionData['soldOutFl'] === 'y' || $optionData['optionSellFl'] === 'n') {
                                    throw new PresentStockException();
                                }
                                if ($optionData['stockFl'] === 'y' && ($optionData['stockCnt'] == 0 || $optionData['stockCnt'] - $goodsCnt < 0)) {
                                    throw new PresentStockException();
                                }
                            }
                        }
                    }
                }
            }
        }
    }


}
