<?php

namespace Bundle\Component\Mypage;

use Framework\Security\Encryptor;
use Framework\Utility\StringUtils;
use Origin\DTO\Payment\PgCardDTO;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderStatus;
use Origin\Enum\RegularDelivery\RegularGoods\DeliveryCycle;
use Origin\Repository\Payment\PgCardInfoRepositoryInterface;
use Repository\Code\CodeRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderAddGoodsRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGiftRepository;
use Repository\RegularDelivery\RegularGoods\RegularGoodsDeliveryCycleRepository;
use Util\Order\RegularOrderUtil;
use Component\Page\Page;
use Component\Policy\Policy;
use Component\Code\Code;

class RegularDelivery
{
    /**
     * @var Encryptor
     */
    private $encryptor;

    /**
     * @var CodeRepository
     */
    private $codeRepository;

    /**
     * @var PgCardInfoRepositoryInterface
     */
    private $pgCardInfoRepository;

    /**
     * @var RegularOrderRepository
     */
    private $regularOrderRepository;

    /**
     * @var RegularOrderGoodsRepository
     */
    private $regularOrderGoodsRepository;

    /**
     * @var RegularOrderAddGoodsRepository
     */
    private $regularOrderAddGoodsRepository;

    /**
     * @var RegularOrderGiftRepository
     */
    private $regularOrderGiftRepository;

    /**
     * @var RegularGoodsDeliveryCycleRepository
     */
    private $regularGoodsDeliveryCycleRepository;

    /**
     * @var Policy
     */
    private $policy;

    /**
     * @param Encryptor $encryptor
     * @param CodeRepository $codeRepository
     * @param PgCardInfoRepositoryInterface $pgCardInfoRepository
     * @param RegularOrderRepository $regularOrderRepository
     * @param RegularOrderGoodsRepository $regularOrderGoodsRepository
     * @param RegularOrderAddGoodsRepository $regularOrderAddGoodsRepository
     * @param RegularOrderGiftRepository $regularOrderGiftRepository
     * @param RegularGoodsDeliveryCycleRepository $regularGoodsDeliveryCycleRepository
     * @param Policy $policy
     */
    public function __construct(
        Encryptor $encryptor,
        CodeRepository $codeRepository,
        PgCardInfoRepositoryInterface $pgCardInfoRepository,
        RegularOrderRepository $regularOrderRepository,
        RegularOrderGoodsRepository $regularOrderGoodsRepository,
        RegularOrderAddGoodsRepository $regularOrderAddGoodsRepository,
        RegularOrderGiftRepository $regularOrderGiftRepository,
        RegularGoodsDeliveryCycleRepository $regularGoodsDeliveryCycleRepository,
        Policy $policy
    ) {
        $this->encryptor = $encryptor;
        $this->codeRepository = $codeRepository;
        $this->pgCardInfoRepository = $pgCardInfoRepository;
        $this->regularOrderRepository = $regularOrderRepository;
        $this->regularOrderGoodsRepository = $regularOrderGoodsRepository;
        $this->regularOrderAddGoodsRepository = $regularOrderAddGoodsRepository;
        $this->regularOrderGiftRepository = $regularOrderGiftRepository;
        $this->regularGoodsDeliveryCycleRepository = $regularGoodsDeliveryCycleRepository;
        $this->policy = $policy;
    }

    /**
     * 신청서 소유권 확인
     *
     * @param int $applyNo 신청번호
     * @param int $memNo 회원번호
     * @return void
     * @throws \Exception 소유권이 없을 경우 예외 발생
     */
    public function verifyOwnership(int $applyNo, int $memNo): void
    {
        if (!$this->regularOrderRepository->hasRegularOrderOwnership($applyNo, $memNo)) {
            throw new \Exception('신청하신 신청번호가 아닙니다.');
        }
    }

    /**
     * 검색 날짜 가져오기
     *
     * @param mixed $searchDates
     * @return array
     */
    public function getSearchDateCondition($searchDates = []): array
    {
        // 시작일, 종료일 계산
        if (empty($searchDates)) {
            $searchDates = [
                date('Y-m-d 00:00:00', strtotime("-7 days")),
                date('Y-m-d 23:59:59')
            ];
        }

        if (is_array($searchDates)) {
            $formattedDates = [];
            foreach ($searchDates as $date) {
                $cleanDate = StringUtils::xssClean($date);
                $formattedDates[] = $cleanDate;
            }
        } else {
            if ($searchDates == 1) {
                $formattedDates = [
                    date('Y-m-d 00:00:00'),
                    date('Y-m-d 23:59:59')
                ];
            } else {
                $formattedDates = [
                    date('Y-m-d 00:00:00', strtotime("-{$searchDates} days")),
                    date('Y-m-d 23:59:59')
                ];
            }
        }

        return $formattedDates;
    }

    /**
     * 정기배송 신청 목록 조회
     *
     * @param array $searchDate (0: 시작일자, 1: 종료일자)
     * @return array
     */
    public function getRegularDeliveryApplyList(array $searchDate, int $pageNum): array
    {
        $memNo = \Session::get('member.memNo');
        $regularDeliveryApplyList = $this->regularOrderRepository->getRegularOrderListBetweenDate($memNo, $searchDate[0], $searchDate[1], $pageNum);
        $totalApplyListCount = $this->regularOrderRepository->countTotalRegularOrderListBetweenDate($memNo, $searchDate[0], $searchDate[1]);

        $formattedApplyList = $this->getFormattedApplyList($regularDeliveryApplyList);

        // 페이징 처리
        $page = new Page($pageNum);
        $page->setUrl(\Request::getQueryString());
        $page->setTotal($totalApplyListCount);
        $page->setCurrentPage($pageNum);
        $page->setList(10);
        $page->setPage();

        return [
            'applyList' => $formattedApplyList,
            'page' => $page
        ];
    }

    /**
     * 이미지 URL을 가져오는 메서드
     *
     * @param array $data
     * @param int $size
     * @return string
     */
    private function getGoodsImageUrl(array $data, int $size = 50): string
    {
        $imageName = $data['goodsImageStorage'] === 'obs' ? $data['imageUrl'] : $data['imageName'];

        return gd_html_goods_image(
            $data['goodsNo'],
            $imageName,
            $data['imagePath'],
            $data['imageStorage'],
            $size,
            $data['goodsNm'],
            '_blank'
        );
    }

    /**
     * 추가 상품 이미지 URL
     *
     * @param array $data
     * @param int $size
     * @return string
     */
    private function getAddGoodsImageUrl(array $data, int $size = 50): string
    {
        return gd_html_add_goods_image(
            $data['addGoodsNo'],
            $data['imageNm'],
            $data['imagePath'],
            $data['imageStorage'],
            $size,
            $data['goodsNm'],
            '_blank'
        );
    }

    /**
     * applyList 배열의 각 요소에 정기배송 데이터 값을 정리하여 반환
     *
     * @param array $applyList
     * @return array
     */
    private function getFormattedApplyList(array $applyList): array
    {
        return array_map(function($data) {
            // 다음 배송예정일 가져오기
            $nextDeliveryDate = $this->getNextDeliveryDate($data['deliveryDueDate'], $data['applyStatus']);
            
            // 신청서 상태 확인
            $data['isActive'] = in_array($data['applyStatus'], RegularOrderStatus::getActiveStatus()) ? 'y' : 'n';
            $data['isPause'] = in_array($data['applyStatus'], RegularOrderStatus::getPauseStatus()) ? 'y' : 'n';
            $data['isInactive'] = in_array($data['applyStatus'], RegularOrderStatus::getInactiveStatus()) ? 'y' : 'n';

            // 이미지 URL 가져오기
            $imageUrl = $this->getGoodsImageUrl($data);

            // 정기 배송 관련 데이터 매핑 후 가져오기
            return $this->getRegularDeliveryData($data, $imageUrl, $nextDeliveryDate);
        }, $applyList);
    }

    /**
     * 다음 배송예정일 가져오기
     *
     * @param string $deliveryDueDate
     * @param string $applyStatus
     * @return string
     */
    private function getNextDeliveryDate(string $deliveryDueDate, string $applyStatus): string
    {
        // 일시정지 상태이거나 해지 상태일 경우 다음 배송예정일이 0000-00-00으로 출력
        $stopOrInactiveStatuses = [
            RegularOrderStatus::USER_STOP,
            RegularOrderStatus::ADMIN_STOP,
            RegularOrderStatus::SYSTEM_STOP,
            RegularOrderStatus::USER_INACTIVE,
            RegularOrderStatus::ADMIN_INACTIVE,
            RegularOrderStatus::SYSTEM_INACTIVE
        ];

        if (in_array($applyStatus, $stopOrInactiveStatuses)) {
            return '0000-00-00';
        }

        return $deliveryDueDate;
    }

    /**
     * 정기 배송 관련 기본 정보 가져오기
     *
     * @param array $data
     * @return array
     */
    private function getBasicInfo(array $data): array
    {
        return [
            'regDt' => $data['regDt'] ?? '',
            'applyNo' => $data['applyNo'] ?? 0,
            'deliveryCycleType' => $data['deliveryCycleType'] === 'month' ? '월 단위' : '주 단위',
            'deliveryCycle' => $data['deliveryCycle'] . ($data['deliveryCycleType'] === 'month' ? '개월' : '주'),
            'deliveryCycleDay' => $data['deliveryCycleType'] === 'month' ? $data['deliveryCycleDay'] . '일' : DeliveryCycle::DELIVERY_CYCLE_WEEK_DAY[$data['deliveryCycleDay']] . '요일',
            'applyStatus' => RegularOrderStatus::getStatusLabel($data['applyStatus']),
            'isActive' => $data['isActive'],
            'isInactive' => $data['isInactive'],
            'isPause' => $data['isPause']
        ];
    }

    /**
     * 정기 배송 관련 상품 정보 가져오기
     *
     * @param array $data
     * @param string $imageUrl
     * @return array
     */
    private function getGoodsInfo(array $data, string $imageUrl): array
    {
        // 옵션 정보가 있는 경우 옵션 정보 가져오기
        if ($data['regularGoodsOptionInfo']) {
            $parsedOptions = RegularOrderUtil::parseOptionData(
                $data['regularGoodsOptionInfo'],
                'optionName',
                'optionValue',
                $data['regularGoodsOptionPrice'] ?? 0,
                'optionPrice'
            )['summary'];
        }

        // 텍스트 옵션 정보가 있는 경우 텍스트 옵션 정보 가져오기
        if ($data['regularGoodsOptionTextInfo']) {
            $parsedOptionTexts = RegularOrderUtil::parseOptionData(
                $data['regularGoodsOptionTextInfo'],
                'optionTextName',
                'optionTextValue',
                $data['regularGoodsOptionTextPrice'] ?? 0,
                'optionTextPrice'
            )['summary'];
        }

        // 추가상품 정보 가져오기
        $addGoodsInfo = $this->regularOrderAddGoodsRepository->findRegularOrderAddGoodsByApplyNo($data['applyNo']);
        $addGoodsList = [];
        if (!empty($addGoodsInfo)) {
            foreach ($addGoodsInfo as $addGoods) {
                $addGoodsList[] = [
                    'addGoodsOptionName' => $addGoods['addGoodsOptionName'],
                    'addGoodsNm' => $addGoods['goodsNm'] ?? '',
                    'addGoodsCnt' => $addGoods['regularAddGoodsCnt'],
                    'addGoodsPrice' => $addGoods['regularAddGoodsPrice'] ?? 0,
                    'addGoodsTotalPrice' => (int)$addGoods['regularAddGoodsPrice'] * $addGoods['regularAddGoodsCnt'],
                    'addGoodsImage' => $this->getAddGoodsImageUrl($addGoods)
                ];
            }
        }

        // 상품 개수에 따른 rowSpan 값
        $goodsRowCnt = $data['regularGoodsCnt'] ? 1 : 0;
        // 추가 상품 개수에 따른 rowSpan 값
        $addGoodsRowCnt = count($addGoodsList);

        return [
            'goodsRowCnt' => $goodsRowCnt,  
            'addGoodsRowCnt' => $addGoodsRowCnt,
            'applyGoodsCnt' => $goodsRowCnt + $addGoodsRowCnt, // 상품 개수 + 추가 상품 개수
            'goods' => [
                [
                    'goodsNo' => $data['goodsNo'],
                    'goodsNm' => $data['goodsNm'],
                    'goodsCnt' => $data['regularGoodsCnt'],
                    'goodsPrice' => (int)$data['regularGoodsPrice'],
                    'goodsPriceWithOption' => ((int)$data['regularGoodsPrice'] + (int)$data['regularGoodsOptionPrice'] + (int)$data['regularGoodsOptionTextPrice']) * $data['regularGoodsCnt'],
                    'goodsTotalPrice' => $this->getGoodsTotalPrice($data, $addGoodsList),
                    'goodsImage' => $imageUrl,
                    'optionInfo' => $data['regularGoodsOptionInfo'] ? $parsedOptions : null,
                    'optionPrice' => (int)$data['regularGoodsOptionPrice'],
                    'optionTextInfo' => $data['regularGoodsOptionTextInfo'] ? $parsedOptionTexts : null,
                    'optionTextPrice' => (int)$data['regularGoodsOptionTextPrice']
                ]
            ],
            'addGoods' => $addGoodsList // 추가 상품 정보 배열
        ];
    }

    /**
     * 정기배송 신청서의 상품 정보 정리
     *
     * @param array $data
     * @param int $size
     * @return array
     */
    private function getRegularGoodsInfo(array $data, int $size): array
    {
        // 옵션 정보가 있는 경우 옵션 정보 가져오기
        if ($data['regularGoodsOptionInfo']) {
            $parsedOptions = $this->parseOptionData(
                $data['regularGoodsOptionInfo'],
                'optionName',
                'optionValue',
                $data['regularGoodsOptionPrice'] ?? 0,
                'optionPrice'
            );
        }

        // 텍스트 옵션 정보가 있는 경우 텍스트 옵션 정보 가져오기
        if ($data['regularGoodsOptionTextInfo']) {
            $parsedOptionTexts = $this->parseOptionData(
                $data['regularGoodsOptionTextInfo'],
                'optionTextName',
                'optionTextValue',
                $data['regularGoodsOptionTextPrice'] ?? 0,
                'optionTextPrice'
            );
        }

        return [
            'regularGoodsNo' => $data['regularGoodsNo'],
            'goodsNo' => $data['goodsNo'],
            'goodsNm' => $data['regularGoodsNm'],
            'goodsCnt' => $data['regularGoodsCnt'],
            'goodsPrice' => (int) $data['regularGoodsPrice'],
            'goodsImage' => $this->getGoodsImageUrl($data, $size),
            'optionSno' => (int) $data['optionSno'],
            'optionInfo' => $data['regularGoodsOptionInfo'] ? $parsedOptions : null,
            'originOptionPrice' => (int) $data['originGoodsOptionPrice'],
            'regularOptionPrice' => (int) $data['regularGoodsOptionPrice'],
            'optionTextInfo' => $data['regularGoodsOptionTextInfo'] ? $parsedOptionTexts : null,
            'originOptionTextPrice' => (int) $data['originGoodsOptionTextPrice'],
            'regularOptionTextPrice' => (int) $data['regularGoodsOptionTextPrice'],
        ];
    }

    /**
     * 정기배송 신청서의 추가 상품 정보 정리
     *
     * @param array $data
     * @param int $size
     * @return array
     */
    private function getAddGoodsInfo(array $data, int $size): array
    {
        return array_map(static function ($item) use ($size) {
            return [
                'goodsNo' => $item['addGoodsNo'],
                'goodsNm' => $item['goodsNm'],
                'goodsPrice' => $item['regularAddGoodsPrice'],
                'goodsCnt' => $item['regularAddGoodsCnt'],
                'goodsOptionNm' => $item['optionNm'],
                'goodsImage' => gd_html_add_goods_image(
                    $item['addGoodsNo'],
                    $item['imageNm'],
                    $item['imagePath'],
                    $item['imageStorage'],
                    $size,
                    $item['goodsNm'],
                    '_blank'
                )
            ];
        }, $data);
    }

    /**
     * 정기배송 신청서의 사은품 정보 정리
     *
     * @param array $data
     * @param int $size
     * @return array
     */
    private function getGiftInfo(array $data, int $size): array
    {
        return array_map(static function ($item) use ($size) {
            return [
                'giftNo' => $item['giftNo'],
                'giftNm' => $item['giftNm'],
                'giveCnt' => $item['giveCnt'],
                'giftImage' => gd_html_gift_image(
                    $item['imageNm'],
                    $item['imagePath'],
                    $item['imageStorage'],
                    $size,
                    $item['giftNm']
                ),
                'conditionTitle' => $item['conditionTitle'],
                'regularGiftPresentInfoSno' => $item['regularGiftPresentInfoSno'],
            ];
        }, $data);
    }

    /**
     * 정기 배송 관련 배송 정보 가져오기
     *
     * @param array $data
     * @param string $nextDeliveryDate
     * @return array
     */
    private function getDeliveryInfo(array $data, string $nextDeliveryDate): array
    {
        // 회차 건너뛰기 시, 배송예정일 회차 건너뛰기 시점으로 다시 재생성
        $regularOrderDates = RegularOrderUtil::calculateOrderDate($nextDeliveryDate, $data['deliveryCycleType'], $data['deliveryCycle'], $data['deliveryCycleDay']);
        // 회차 건너뛰기 시, 다음 배송예정일
        $skipDeliveryDate = $regularOrderDates[0];

        return [
            'deliveryInfo' => [
                'currentDeliveryRound' => $data['deliveryRound'],
                'maxDeliveryRound' => $data['maxDeliveryRound'],
                'totalCount' => $data['deliveryCycle'],
                'nextDeliveryDate' => $nextDeliveryDate,
                'deliveryRoundSkipFl' => $data['deliveryRoundSkipFl'],
                'skipDeliveryDate' => $skipDeliveryDate
            ]
        ];
    }

    /**
     * 정기배송 신청서의 배송지 정보 정리
     *
     * @param array $data
     * @return array
     */
    private function getShippingInfo(array $data): array
    {
        return [
            'shippingSno' => $data['shippingSno'],
            'shippingTitle' => $data['shippingTitle'],
            'shippingName' => $data['shippingName'],
            'shippingAddress' => $data['shippingAddress'],
            'shippingAddressSub' => $data['shippingAddressSub'],
            'shippingPhone' => $data['shippingPhone'],
            'shippingCellPhone' => $data['shippingCellPhone'],
            'shippingMessage' => $data['shippingMessage']
        ];
    }

    /**
     * 정기배송 신청서의 배송주기 정보 정리
     *
     * @param array $data
     * @return array
     */
    private function getSelectedCycleData(array $data): array
    {
        return [
            'deliveryCycleType' => $data['deliveryCycleType'],
            'deliveryCycle' => $data['deliveryCycle'],
            'deliveryCycleDay' => $data['deliveryCycleDay'],
            'maxDeliveryRound' => $data['maxDeliveryRound'],
            'deliveryDueDate' => $this->getNextDeliveryDate($data['deliveryDueDate'], $data['applyStatus']),
            'deliveryRound' => $data['deliveryRound']
        ];
    }

    /**
     * 정기 배송 관련 데이터 가져오기
     *
     * @param array $data
     * @param string $imageUrl
     * @param string $nextDeliveryDate
     * @return array
     */
    private function getRegularDeliveryData(array $data, string $imageUrl, string $nextDeliveryDate): array
    {
        // 카드정보 세팅
        $memNo = \Session::get('member.memNo');
        $regularDeliveryData['paymentData'] = $this->getDeliveryCardInfo($data['cardNo'], $memNo);

        return array_merge(
            $this->getBasicInfo($data),
            $this->getGoodsInfo($data, $imageUrl),
            $this->getDeliveryInfo($data, $nextDeliveryDate),
            $regularDeliveryData
        );
    }

    /**
     * 정기배송 해지 사유 코드 조회
     *
     * @return array
     */
    public function getCancelReasonCodes(): array
    {
        // 정기배송 해지 사유 그룹코드로 해지 사유 조회
        return $this->codeRepository->findRegularDeliveryCancelReasonNames(Code::GROUP_CODE_REGULAR_DELIVERY_CANCEL_REASON);
    }

    /**
     * 옵션 데이터를 파싱하는 함수
     * 옵션 예시:
     * [["옵션명1", "옵션값1"]]
     * [["옵션명2", "옵션값2"]]
     *
     * @param string $optionData 옵션 데이터
     * @param string $nameKey 옵션명 키값
     * @param string $valueKey 옵션값 키값
     * @param int $optionPrice 옵션가격
     * @param string $priceKey 옵션가격 키값
     * @return array
     */
    private function parseOptionData(string $optionData, string $nameKey = 'optionName', string $valueKey = 'optionValue', int $optionPrice = 0, string $priceKey = 'optionPrice')
    {
        if (empty($optionData)) {
            return [];
        }

        $parsedData = [];
        $groups = explode(",\n", $optionData);

        foreach ($groups as $group) {
            if (empty(trim($group))) {
                continue;
            }

            $options = json_decode($group, true);
            if (!is_array($options)) {
                continue;
            }

            foreach ($options as $option) {
                if (empty($option[0]) || empty($option[1])) {
                    continue;
                }
                $parsedData[] = [
                    $nameKey => $option[0],
                    $valueKey => $option[1],
                    $priceKey => $optionPrice
                ];
            }
        }

        return $parsedData;
    }

    /**
     * 상품정보 변경 관련 데이터 조회
     *
     * @param int $applyNo
     * @return array
     */
    public function getRegularDeliveryGoodsChangeInfo(int $applyNo): array
    {
        // 상품 정보 조회
        $goodsInfo = $this->regularOrderGoodsRepository->findRegularOrderGoodsByApplyNo($applyNo);

        // 추가상품 목록 조회
        $addGoodsList = $this->regularOrderAddGoodsRepository->findRegularOrderAddGoodsByApplyNo($applyNo);

        // 사은품 목록 조회
        $giftList = $this->regularOrderGiftRepository->findGiftInfoByApplyNo($applyNo);

        return [
            'goodsData' => $this->getRegularGoodsInfo($goodsInfo, 65),
            'addGoodsData' => $this->getAddGoodsInfo($addGoodsList, 65),
            'giftData' => $this->getGiftInfo($giftList, 65)
        ];
    }

    /**
     * 배송정보 변경 관련 데이터 가져오기
     *
     * @param int $applyNo
     * @return array
     */
    public function getRegularDeliveryChangeInfo(int $applyNo): array
    {
        $memNo = \Session::get('member.memNo');

        // 정기배송 신청서 배송정보
        $regularDeliveryChangeInfo = $this->regularOrderGoodsRepository->findRegularDeliveryChangeInfoByApplyNo($applyNo);
        $addGoodsCount = $this->regularOrderAddGoodsRepository->countRegularOrderAddGoodsByApplyNo($applyNo);

        return [
            'goodsNm' => $regularDeliveryChangeInfo['goodsNm'],
            'addGoodsCount' => $addGoodsCount,
            'shippingInfo' => $this->getShippingInfo($regularDeliveryChangeInfo),
            'cycleData' => $this->getDeliveryCycleData($regularDeliveryChangeInfo),
            'selectedCycleData' => $this->getSelectedCycleData($regularDeliveryChangeInfo),
            'paymentData' => $this->getDeliveryCardInfo($regularDeliveryChangeInfo['cardNo'], $memNo)
        ];
    }

    /**
     * 배송 주기 데이터 가져오기
     *
     * @param array $regularDeliveryChangeInfo
     * @return array
     */
    private function getDeliveryCycleData(array $regularDeliveryChangeInfo): array
    {
        $regularGoodsPolicy = json_decode($regularDeliveryChangeInfo['regularGoodsPolicy'], true);
        $deliveryCycleType = $regularGoodsPolicy['deliveryCycleType'];
        $maxDeliveryRounds = $regularGoodsPolicy['maxDeliveryRounds'] ?: 50;

        // 정기상품 배송 주기 정보 가져오기
        $deliveryCycleData = $regularGoodsPolicy['cycleData'];
        if ($deliveryCycleType === 'month') {
            $deliveryCycle = array_values(array_filter($deliveryCycleData['monthCycle']));
            $deliveryCycleWeekDay = null;
        } elseif ($deliveryCycleType === 'week') {
            $deliveryCycle = array_values(array_filter($deliveryCycleData['weekCycle']));
            $deliveryCycleWeekDay = array_values(array_filter($deliveryCycleData['weekDayCycle']));
        }

        // 정기상품 배송주기 정리
        $regularDeliveryCycleData = RegularOrderUtil::getDeliveryCycleData(
            $deliveryCycleType,
            $deliveryCycle ?? [],
            $deliveryCycleWeekDay ?? []
        );

        // 종료회차라면, 현재 회차부터, 아니라면 다음 회차부터 선택 가능
        if ($regularDeliveryChangeInfo['maxDeliveryRound'] === $regularDeliveryChangeInfo['deliveryRound']) {
            $deliveryRoundOptions = range($regularDeliveryChangeInfo['deliveryRound'], $maxDeliveryRounds);
        } else {
            $deliveryRoundOptions = range($regularDeliveryChangeInfo['deliveryRound'] + 1, $maxDeliveryRounds);
        }

        return array_merge([
            'applyStatus' => $regularDeliveryChangeInfo['regularGoodsApplyStatus'],
            'deliveryRoundsDisplayType' => $regularGoodsPolicy['deliveryRoundsDisplayType'],
            'deliveryCycleType' => $deliveryCycleType,
            'deliveryRoundOptions' => $deliveryRoundOptions
        ], $regularDeliveryCycleData);
    }

    /**
     * 카드 정보를 조회하고 처리하는 메소드
     *
     * @param string $cardNo 카드번호
     * @param int $memNo 회원번호
     * @return array 처리된 카드 정보
     */
    private function getDeliveryCardInfo(string $cardNo, int $memNo): array
    {
        // 카드 정보 조회
        $cardInfo = $this->pgCardInfoRepository->findUsedCardByCardNoAndMemNo($cardNo, $memNo);

        // 암호화된 카드번호 복호화(카드 뒷번호 4자리)
        $decryptCardNo = $this->encryptor->decrypt($cardInfo['cardNo']);
        $cardInfo['decryptCardNo'] = $decryptCardNo;

        // DTO 변환 및 복호화된 카드번호 추가
        return array_merge(
            (new PgCardDTO($cardInfo))->toArray(),
            ['decryptCardNo' => $decryptCardNo]
        );
    }

    /**
     * 상품 총 가격 계산
     *
     * @param array $goodsList
     * @param array $addGoodsList
     * @return int
     */
    private function getGoodsTotalPrice(array $goodsList, array $addGoodsList): int
    {
        // 정기상품 가격 계산 (상품가격 + 옵션가격 + 텍스트옵션가격) * 수량
        $regularGoodsPrice = ((int)$goodsList['regularGoodsPrice'] 
            + (int)$goodsList['regularGoodsOptionPrice'] 
            + (int)$goodsList['regularGoodsOptionTextPrice']) 
            * $goodsList['regularGoodsCnt'];

        // 추가상품 가격 합계
        $addGoodsTotalPrice = array_sum(array_column($addGoodsList, 'addGoodsTotalPrice'));

        return $regularGoodsPrice + $addGoodsTotalPrice;
    }

    /**
     * 정기 결제(배송) 기능 사용 여부 체크
     *
     * @return bool
     */
    public function isUseRegularDelivery(): bool
    {
        return $this->policy->getValue("order.basic")['useRegularDelivery'] === 'y';
    }
}
