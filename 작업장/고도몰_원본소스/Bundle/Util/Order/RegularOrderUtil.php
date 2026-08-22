<?php

namespace Bundle\Util\Order;

use DateTime;
use Framework\Utility\DateTimeUtils;
use Origin\Enum\RegularDelivery\RegularGoods\RegularGoodsAttribute;

class RegularOrderUtil
{
    const SATURDAY_NUMBER = 6;
    const SUNDAY_NUMBER = 7;
    const ORDER_CREATION_SUBTRACT_DAYS = 2;

    /**
     * 배송예정일 및 주문 생성일 계산(배송일 예정일 -2일)
     * 첫 배송일은 월/주 관계 없이 기준일 바로 다음 주의 일/요일이 반환되어야 함.
     * 첫 배송일이 아닌 경우는 월/주 를 기준일에서 더하고 그 월/주의 일/요일이 반환되어야 함.
     * -2 계산 시 주말인 경우는 계산 일에서 스킵함.
     * @param string $deliveryDueDate 배송예정일
     * @param string $type 계산 타입(month, week)
     * @param int $period (1~6 개월/주)
     * @param int $roundDay (일자/요일)
     * @param bool $isFirst (정기결제 첫 배송일 계산 인지)
     * @param string|null $today (없으면 자동으로 오늘 날짜 할당)
     * @throws \Exception
     */
    public static function calculateOrderDate(string $deliveryDueDate, string $type, int $period, int $roundDay, bool $isFirst = false, $today = null): array
    {
        $baseDate = new DateTime($deliveryDueDate);

        // 월 계산
        if ($type === 'month') {
            $shippingDate = self::calculateMonthlyShippingDate($baseDate, $roundDay, $isFirst, $period);
        } else {
            // 주 계산
            $shippingDate = self::getShippingRequestDateByWeekday($baseDate, $isFirst ? 1 : $period, $roundDay);
        }

        // 배송예정일 주말이면 건너뛰기
        $shippingRequestDate = self::adjustForWeekends($shippingDate);
        // 주문 생성일 (-2일) 계산
        $orderCreationDate = self::getOrderCreationDate($shippingRequestDate);

        // 첫 요청일 계산에서 계산했는데 했는데 주문생성일이 배송예정일 보다 작은 경우
        if ($isFirst) {
            while (strtotime($orderCreationDate) <= strtotime($deliveryDueDate)) {
                $shippingRequestDate = date('Y-m-d', strtotime("$shippingDate +1 $type"));
                $orderCreationDate = self::getOrderCreationDate($shippingRequestDate);
            }
        }

        $today = $today ?? date('Y-m-d');
        if (strtotime($orderCreationDate) <= strtotime($today)) {
            return self::calculateOrderDate($today, $type, $period, $roundDay, true, $today);
        }

        return [$shippingRequestDate, $orderCreationDate];
    }

    /**
     * 월/일 계산 시 배송예정일 계산
     * @param DateTime $baseDate
     * @param int $roundDay
     * @param bool $isFirst
     * @param int $period
     * @return string
     * @throws \Exception
     */
    private static function calculateMonthlyShippingDate(DateTime $baseDate, int $roundDay, bool $isFirst, int $period): string
    {
        // 첫배송이 아닌 경우, 전달받은 월/주만큼 이동해야 한다.
        // 다만, 배송예정일의 경우 오늘이 1 or 2일 이고 -1일이나 -2일 한 날짜가 주말이라면, priod를 -1 해줘야 한다.
        // 예) 1회차 2025-08-31(일) 이 배송예정일이면, 2025-09-01 로 배송예정일이 들어가야 하는데, 2회차의 경우는 2025-09-30 이 되어야 하기 때문이다.(정책상)
        if (!$isFirst) {
            $minusOne = clone $baseDate;
            $minusOne->modify('-1 day');
            $minusTwo = clone $baseDate;
            $minusTwo->modify('-2 day');
            $weekendDays = [self::SATURDAY_NUMBER, self::SUNDAY_NUMBER];
            if (($baseDate->format('d') === '01' || $baseDate->format('d') === '02')
                && (in_array($minusOne->format('N'), $weekendDays) || in_array($minusTwo->format('N'), $weekendDays))) {
                $period -= 1;
            }
            $baseDate = new DateTime(DateTimeUtils::addMonthsToDate($baseDate->format('Y-m-d'), $period));
        }

        // 배송예정일 날짜 정보 (처음이면 들어온 날짜, 처음이 아니면 다음달로 이동 된 정보)
        $year = $baseDate->format('Y');
        $month = $baseDate->format('m');
        $day = (int)$baseDate->format('d');

        // 해당 달의 마지막 날로 이동
        $lastDayOfMonth = (new DateTime("$year-$month-01"))->format('t');
        // 마지막 날과 요청 날짜중 작은 날로 이동
        $roundDay = min($roundDay, $lastDayOfMonth);
        $shippingRequestDate = new DateTime("$year-$month-" . sprintf('%02d', $roundDay));

        // 첫 계산인 경우
        if ($isFirst) {
            // (roundDay 가 요청 날짜보다 작거나 roundDay- 2도 작으면) -> 다음 달 이동
            if ((int)$shippingRequestDate->format('d') - 2 <= $day) {
                $baseDate->modify('first day of next month');
                $year = $baseDate->format('Y');
                $month = $baseDate->format('m');

                $lastDayOfMonth = (new DateTime("$year-$month-01"))->format('t');
                $roundDay = min($roundDay, $lastDayOfMonth);

                $shippingRequestDate = new DateTime("$year-$month-" . sprintf('%02d', $roundDay));
            }
        }

        return $shippingRequestDate->format('Y-m-d');
    }

    /**
     * 주/요일 계산 시 배송예정일 계산
     * @param DateTime $baseTime
     * @param int $period
     * @param int $weekday
     * @return string
     */
    private static function getShippingRequestDateByWeekday(DateTime $baseTime, int $period, int $weekday): string
    {
        $originalBaseTime = clone $baseTime;

        $currentWeekday = $baseTime->format('N');
        $dayDiff = $weekday - $currentWeekday;
        $weekDayTime = clone $baseTime->modify("$dayDiff days"); // 지정된 요일로 이동

        // 주문생성일(-2일 계산된 일자) 가 기준일보다 작아지는지 확인
        $baseTime->modify("-2 days"); // 지정된 주기로 이동
        if ($baseTime <= $originalBaseTime) {
            $originalBaseTime->modify("+$period week");
            $currentWeekday = $originalBaseTime->format('N');
            $dayDiff = $weekday - $currentWeekday;
            $originalBaseTime->modify("$dayDiff days"); // 지정된 요일로 이동
            return $originalBaseTime->format('Y-m-d');
        }


        return $weekDayTime->format('Y-m-d');
    }

    /**
     * 주문 생성일 계산 (-2일)
     * 주말은 -2일 계산에서 스킵함
     * @param string $shippingRequestDate
     * @return string
     * @throws \Exception
     */
    private static function getOrderCreationDate(string $shippingRequestDate): string
    {
        $date = new DateTime($shippingRequestDate);
        $daysSubtracted = 0;

        while ($daysSubtracted < self::ORDER_CREATION_SUBTRACT_DAYS) {
            $date->modify('-1 day');
            // 주말이 아닌 경우만
            if (!in_array($date->format('N'), [self::SATURDAY_NUMBER, self::SUNDAY_NUMBER])) {
                $daysSubtracted++;
            }
        }

        return $date->format('Y-m-d');
    }


    /**
     * 배송예정일이 주말이면 다음주 월요일로 변경
     * @param string $shippingRequestDate
     * @return string
     * @throws \Exception
     */
    private static function adjustForWeekends(string $shippingRequestDate): string
    {
        $date = new DateTime($shippingRequestDate);
        $dayOfWeek = $date->format('N');

        if ($dayOfWeek == self::SATURDAY_NUMBER) { // 토요일이면
            $date->modify('+2 day');
        } elseif ($dayOfWeek == self::SUNDAY_NUMBER) { // 일요일이면
            $date->modify('+1 day');
        }

        return $date->format('Y-m-d');
    }

    /**
     * 정기결제옵션가 계산
     *
     * @param float $optionPrice : 일반상품 옵션 가격
     * @param string $discountUseFl : 정기결제(배송) 할인 여부
     * @param $discountType : 정기결제(배송) 할인 타입
     * @param $discountRate : 정기결제(배송) 비율
     * @return float : 정기결제옵션가
     * @throws \Exception
     */
    public static function calculateRegularOptionPrice(
        float  $optionPrice,
        string $discountUseFl,
               $discountType,
               $discountRate
    ): float
    {
        // 할인 정보 검증
        self::validateRegularDiscountInputData($discountUseFl, $discountType, $discountRate);

        // 할인 사용 여부 확인
        if ($discountUseFl === 'n') {
            return $optionPrice; // 할인 적용 안함
        }

        // 할인 타입에 따른 가격 계산
        switch ($discountType) {
            case 'percent':
                $goodsTrunc = gd_policy('basic.trunc')['goods'];

                // 할인율 계산
                $optionPrice *= (1 - $discountRate / 100);

                // 유틸 절삭 정책 사용
                return gd_number_figure($optionPrice, $goodsTrunc['unitPrecision'], $goodsTrunc['unitRound']);

            default :
                return $optionPrice; // 고정 금액 미할인(상품에서 이미 할인이 들어감)
        }
    }

    /**
     * 정기결제 할인 값에 대한 유효성 검사
     *
     * @param string $discountUseFl : 정기결제(배송) 할인 여부
     * @param $discountType : 정기결제(배송) 할인 타입
     * @param $discountRate : 정기결제(배송) 비율
     * @return void
     * @throws \Exception
     */
    public static function validateRegularDiscountInputData(
        string $discountUseFl,
               $discountType,
               $discountRate
    )
    {
        // 할인 정보가 필요한 경우에만 유효성 검사
        if ($discountUseFl === 'y') {
            if (empty($discountType)) {
                throw new \Exception('할인 정보가 부족합니다. 할인 타입과 할인율 또는 고정 금액을 입력해야 합니다.');
            }

            // 할인 타입 검증
            if (!in_array($discountType, ['percent', 'fix'])) {
                throw new \Exception('잘못된 할인 타입입니다. ("percent" 또는 "fix"만 가능합니다.)');
            }

            // 'percent'일 경우 discountRate가 필요하고, 'fix'일 경우 discountPrice가 필요
            if ($discountType === 'percent' && $discountRate === null) {
                throw new \Exception('할인 타입이 "percent"일 때는 할인율이 필요합니다.');
            }
        }
    }
    /**
     * 정기 상품 배송 주기에 따른 설정 데이터 가져오기
     * @param string $deliveryCycleType : 배송 주기 타입 (all/month/week)
     * @param $deliveryCycle : 배송 주기 (월,주 주기)
     * @param $deliveryCycleWeekDay : 배송 주기 (요일 주기)
     * @return array 배송 주기 데이터
     * @throws \Exception
     */
    public static function getDeliveryCycleData(string $deliveryCycleType, $deliveryCycle = null, $deliveryCycleWeekDay = null): array
    {
        $deliveryCycleData = [
            'deliveryCycleMonth' => null,
            'deliveryCycleMonthDay' => null,
            'deliveryCycleWeek' => null,
            'deliveryCycleWeekDay' => null
        ];

        switch ($deliveryCycleType) {
            case 'all':
                $deliveryCycleData['deliveryCycleMonth'] = range(1, 6); // 1~6개월
                $deliveryCycleData['deliveryCycleMonthDay'] = range(1, 31); // 1~31일
                $deliveryCycleData['deliveryCycleWeek'] = range(1, 6);  // 1~6주
                $deliveryCycleData['deliveryCycleWeekDay'] = range(1, 5);  // 월~금
                break;

            case 'month':
                $deliveryCycleData['deliveryCycleMonth'] = $deliveryCycle;
                $deliveryCycleData['deliveryCycleMonthDay'] = range(1, 31); // 1~31일
                break;

            case 'week':
                $deliveryCycleData['deliveryCycleWeek'] = $deliveryCycle;
                $deliveryCycleData['deliveryCycleWeekDay'] = $deliveryCycleWeekDay;
                break;

            default:
                throw new \Exception('잘못된 배송 주기 타입입니다.');
        }

        return $deliveryCycleData;
    }

    /**
     * 옵션 데이터를 파싱하는 함수
     * 옵션 예시:
     * 옵션명1: 옵션값1 / 옵션명2: 옵션값2
     * 텍스트옵션명1: 텍스트옵션값1 / 텍스트옵션명2: 텍스트옵션값2
     *
     * @param string $optionData 옵션 데이터
     * @param string $nameKey 옵션명 키값
     * @param string $valueKey 옵션값 키값
     * @param int $optionPrice 옵션가격
     * @param string $priceKey 옵션가격 키값
     * @return array
     */
    public static function parseOptionData(
        string $optionData, 
        string $nameKey = 'optionName', 
        string $valueKey = 'optionValue', 
        int $optionPrice = 0, 
        string $priceKey = 'optionPrice'
    ): array
    {
        if (empty($optionData)) {
            return ['list' => [], 'summary' => ''];
        }

        $parsedData = [];
        $nameList = [];
        $valueList = [];

        $options = json_decode($optionData, true);

        if (!is_array($options)) {
            return ['list' => [], 'summary' => ''];
        }

        foreach ($options as $option) {
            if (!is_array($option) || empty($option[0]) || empty($option[1])) {
                continue;
            }
            $parsed = [
                $nameKey => $option[0],
                $valueKey => $option[1],
                $priceKey => $option[2] ?? $optionPrice
            ];
            $parsedData[] = $parsed;
            $nameList[] = $parsed[$nameKey];
            $valueList[] = $parsed[$valueKey];
        }

        $summary = [];
        if ($nameList && $valueList) {
            $summary = implode('/', $nameList) . ' : ' . implode('/', $valueList);
        }

        return [
            'list' => $parsedData,
            'summary' => $summary
        ];
    }


    /**
     * 정기배송 상품 할인 정보 계산
     *
     * @param float $regularPrice
     * @param string $discountUseFl
     * @param string $discountType
     * @param float|null $discountRate
     * @param int|null $discountPrice
     * @return array
     * @throws \Exception
     */
    public static function calculateRegularGoodsDiscount(
        float  $regularPrice,
        string $discountUseFl,
        string $discountType = null,
        float  $discountRate = null,
        int    $discountPrice = null
    ): array
    {
        // 할인 사용 여부가 'y'가 아닐 경우
        if ($discountUseFl !== 'y') {
            return [$discountRate, $discountPrice];
        }

        // 정기상품이 정률 할인일 경우
        if ($discountType === RegularGoodsAttribute::DISCOUNT_PERCENT) {
            if ($discountRate === null || $discountRate <= 0 || $discountRate >= 100) {
                throw new \Exception('유효하지 않은 할인율입니다.');
            }
            $originalPrice = $regularPrice / (1 - $discountRate / 100);
            $discountPrice = (int)($originalPrice - $regularPrice);

        // 정기상품이 정액 할인일 경우
        } elseif ($discountType === RegularGoodsAttribute::DISCOUNT_FIX) {
            if ($discountPrice === null || $discountPrice <= 0) {
                throw new \Exception('유효하지 않은 할인 금액입니다.');
            }
            $originalPrice = $regularPrice + $discountPrice;
            $discountRate = round(($discountPrice / $originalPrice) * 100, 2);
        } else {
            throw new \Exception('잘못된 할인 타입입니다.');
        }

        return [$discountRate, $discountPrice];
    }


    /**
     * 신청서의 정보 기반으로 신청 시점의 원본 상품 할인 가격 계산
     *
     * @param array $regularOrder
     * @return float
     */
    public static function calculateRegularOrderOriginGoodsDiscountPrice(array $regularOrder): float
    {
        $discountPrice = 0;
        $regularGoodsPolicy = json_decode($regularOrder['regularGoodsPolicy'], true); // 신청 시점 정책

        if ($regularGoodsPolicy['discountUseFl'] === 'y') {
            // 정률 할인
            if ($regularGoodsPolicy['discountType'] === 'percent') {
                $discountPrice = self::calculateTotalRegularOrderOriginGoodsPrice($regularOrder) * ($regularGoodsPolicy['discountRate'] / 100);
            } elseif ($regularGoodsPolicy['discountType'] === 'fix') {
                // 정액 할인
                $discountPrice = $regularGoodsPolicy['discountPrice'] * $regularOrder['regularGoodsCnt'];
            }
        }

        return $discountPrice;
    }

    /**
     * 신청서의 총 원본 상품금액 계산
     * 정기상품이 아닌 원본 상품의 가격으로 계산한다.
     *
     * @param array $regularOrder
     * @return float
     */
    public static function calculateTotalRegularOrderOriginGoodsPrice(array $regularOrder): float
    {
        return ($regularOrder['originGoodsPrice'] + $regularOrder['originGoodsOptionPrice'] + ($regularOrder['originGoodsOptionTextPrice'] ?? 0)) * $regularOrder['regularGoodsCnt'];
    }
}
