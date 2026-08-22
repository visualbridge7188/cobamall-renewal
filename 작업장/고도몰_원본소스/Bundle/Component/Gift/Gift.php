<?php
/**
 * 사은품 class
 *
 * @author    artherot
 * @version   1.0
 * @since     1.0
 * @copyright ⓒ 2016, NHN godo: Corp.
 */
namespace Bundle\Component\Gift;

use Component\Database\DBTableField;
use Framework\Utility\ArrayUtils;
use Session;

class Gift
{
    protected $db;

    /**
     * 생성자
     */
    public function __construct()
    {
        if (!is_object($this->db)) {
            $this->db = \App::load('DB');
        }
    }

    /**
     * 사은품 정보 출력
     * 완성된 쿼리문은 $db->strField , $db->strJoin , $db->strWhere , $db->strGroup , $db->strOrder , $db->strLimit 멤버 변수를
     * 이용할수 있습니다.
     *
     * @param  string $giftNo    사은품 번호 (기본 null)
     * @param  string $giftField 출력할 필드명 (기본 null)
     * @param  array  $arrBind   bind 처리 배열 (기본 null)
     * @param  string $dataArray return 값을 배열처리 (기본값 false)
     *
     * @return array  사은품 정보
     */
    public function getGiftInfo($giftNo = null, $giftField = null, $arrBind = null, $dataArray = false)
    {
        if ($giftNo) {
            if ($this->db->strWhere) {
                $this->db->strWhere = " g.giftNo = ? AND " . $this->db->strWhere;
            } else {
                $this->db->strWhere = " g.giftNo = ?";
            }
            $this->db->bind_param_push($arrBind, 'i', $giftNo);
        }
        if ($giftField) {
            if ($this->db->strField) {
                $this->db->strField = $giftField . ', ' . $this->db->strField;
            } else {
                $this->db->strField = $giftField;
            }
        }
        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_GIFT . ' g ' . gd_implode(' ', $query);
        $getData = $this->db->query_fetch($strSQL, $arrBind);

        if (gd_count($getData) == 1 && $dataArray === false) {
            return gd_htmlspecialchars_stripslashes($getData[0]);
        }

        return gd_htmlspecialchars_stripslashes($getData);
    }

    /**
     * 사은품 증정 정보 출력
     *
     * @param  string $presentSno 사은품 증정 sno
     *
     * @return array  해당 사은품 증정 정보
     */
    public function getGiftPresent($presentSno)
    {
        $arrField = DBTableField::setTableField('tableGiftPresent');
        $strSQL = "SELECT sno, " . gd_implode(', ', $arrField) . " FROM " . DB_GIFT_PRESENT . " WHERE sno = ?";
        $arrBind = [
            'i',
            $presentSno,
        ];
        $getData = $this->db->query_fetch($strSQL, $arrBind);
        if (gd_count($getData) > 0) {
            return gd_htmlspecialchars_stripslashes($getData[0]);
        } else {
            return false;
        }
    }

    /**
     * 사은품 증정 정보의 설정 사은품 출력
     *
     * @param  string $presentSno 사은품 증정 sno
     *
     * @return array  해당 사은품 정보
     */
    public function getGiftPresentInfo($presentSno)
    {
        $arrField = DBTableField::setTableField('tableGiftPresentInfo');
        $strSQL = "SELECT sno, " . gd_implode(', ', $arrField) . " FROM " . DB_GIFT_PRESENT_INFO . " WHERE presentSno = ?";
        $arrBind = [
            'i',
            $presentSno,
        ];
        $getData = $this->db->query_fetch($strSQL, $arrBind);
        if (gd_count($getData) > 0) {
            return gd_htmlspecialchars_stripslashes($getData);
        } else {
            return false;
        }
    }

    /**
     * 사은품 증정 모든 정보 출력
     *
     * @param  string $presentSno 사은품 증정 sno
     *
     * @return array  해당 사은품 증정 모든 정보
     */
    public function getGiftPresentData($presentSno)
    {
        $getData = [];
        $getData = $this->getGiftPresent($presentSno);
        $getData['gift'] = $this->getGiftPresentInfo($presentSno);

        // 구성 상품
        if ($getData['presentFl'] == 'g') {
            $getData['presentKindCd'] = $this->viewGoodsData($getData['presentKindCd']);
        }
        if ($getData['presentFl'] == 'c') {
            $getData['presentKindCd'] = $this->viewCategoryData($getData['presentKindCd']);
        }
        if ($getData['presentFl'] == 'b') {
            $getData['presentKindCd'] = $this->viewCategoryData($getData['presentKindCd'], 'brand');
        }
        if ($getData['presentFl'] == 'e') {
            $getData['presentKindCd'] = $this->viewEventData($getData['presentKindCd']);
        }

        // 예외 조건
        $getData['exceptGoodsNo'] = $this->viewGoodsData($getData['exceptGoodsNo']);
        $getData['exceptCateCd'] = $this->viewCategoryData($getData['exceptCateCd']);
        $getData['exceptBrandCd'] = $this->viewCategoryData($getData['exceptBrandCd'], 'brand');
        $getData['exceptEventCd'] = $this->viewEventData($getData['exceptEventCd']);

        // 사은품
        if (is_array($getData['gift'])) {
            $this->viewGiftData($getData['gift']);
        }

        return $getData;
    }

    /**
     * 사은품 증정 상품 상세 정보
     *
     * @param  string $getData 데이타
     *
     * @return array  상품 상세 정보
     */
    public function viewGoodsData($getData)
    {
        if (empty($getData)) {
            return false;
        }

        $goods = \App::load('\\Component\\Goods\\GoodsAdmin');

        return $goods->getGoodsDataDisplay($getData);
    }

    /**
     * 사은품 증정 상품 상세 정보
     *
     * @param  string $getData 데이타
     *
     * @return array  상품 상세 정보
     */
    public function viewCategoryData($getData, $cateMode = 'category')
    {
        if (empty($getData)) {
            return false;
        }
        if ($cateMode == 'category') {
            $cate = \App::load('\\Component\\Category\\CategoryAdmin');
        } else {
            $cate = \App::load('\\Component\\Category\\BrandAdmin');
        }
        $tmp['code'] = explode(INT_DIVISION, $getData);
        foreach ($tmp['code'] as $val) {
            $tmp['name'][] = gd_htmlspecialchars_decode($cate->getCategoryPosition($val));
        }

        return $tmp;
    }

    /**
     * 사은품 증정 이벤트 상세 정보
     *
     * @param  string $getData 데이타
     *
     * TODO:php82 잘 못 된거 아닌지..?
     * return array  이벤트 상세 정보
     */
    public function viewEventData($getData)
    {
        if (empty($getData)) {
            return false;
        }
    }

    /**
     * 사은품 증정 사은품 상세 정보
     *
     * @param  string  $getData    데이타
     * @param  integer $imageSize  출력할 이미지의 사이즈
     * @param  boolean $userMode   사용자 모드 여부
     * @param  boolean $stockCheck 재고 체크여부
     *
     * @return array   사은품 상세 정보
     */
    public function viewGiftData(&$getData, $imageSize = null, $userMode = false, $stockCheck = false)
    {
        $arrInclude = [
            'scmNo',
            'giftNo',
            'giftNm',
            'stockFl',
            'stockCnt',
            'imageStorage',
            'imagePath',
            'imageNm',
        ];
        $arrField = DBTableField::setTableField('tableGift', $arrInclude, null, 'g');

        if (empty($getData) || is_array($getData) === false) {
            return false;
        }

        foreach ($getData as $key => $val) {
            if (!empty($val['multiGiftNo'])) {
                $arrGiftNo = explode(INT_DIVISION, $val['multiGiftNo']);
                $arrBind = [];
                foreach ($arrGiftNo as $bVal) {
                    $this->db->bind_param_push($arrBind['bind'], 'i', $bVal);
                    $arrBind['param'][] = '?';
                }
                $this->db->strField = gd_implode(', ', $arrField);
                if ($userMode === false) {
                    $this->db->strWhere = 'g.giftNo IN (' . gd_implode(',', $arrBind['param']) . ')';
                } else {
                    $this->db->strWhere = 'g.giftNo IN (' . gd_implode(',', $arrBind['param']) . ') AND ( if (g.stockFl = \'y\' , g.stockCnt , 1 ) ) > 0';
                }
                $getData[$key]['multiGiftNo'] = $this->getGiftInfo(null, null, $arrBind['bind'], true);

                // 사은품 증정내 제공상품 총 갯수
                $getData[$key]['total'] += gd_count($getData[$key]['multiGiftNo']);

                // 사은품 정보가 없으면 unset
                if ($userMode === true && empty($getData[$key]['multiGiftNo']) === true) {
                    unset($getData[$key]['multiGiftNo']);
                    continue;
                }

                // 상품 갯수형의 경우 재고 체크 (증정하려는 상품 수량보다 현재 남아 있는 사은품 상품의 재고가 적은 경우 제외)
                if ($stockCheck === true && $getData[$key]['giveCnt'] > 0) {
                    foreach ($getData[$key]['multiGiftNo'] as $iKey => & $iVal) {
                        if ($iVal['stockFl'] == 'y' && $getData[$key]['giveCnt'] > $iVal['stockCnt']) {
                            unset($getData[$key]['multiGiftNo'][$iKey]);
                            continue;
                        }
                    }
                    unset($iKey, $iVal);
                }

                // 이미지 사이즈를 설정한 경우 (사용자모드에서 사용)
                if ($imageSize !== null) {
                    foreach ($getData[$key]['multiGiftNo'] as $iKey => & $iVal) {
                        $iVal['imageUrl'] = gd_html_preview_image($iVal['imageNm'], $iVal['imagePath'], $iVal['imageStorage'], $imageSize, 'gift', $iVal['giftNm'], null, false, true);
                        unset($iVal['imageNm'], $iVal['imagePath'], $iVal['imageStorage']);
                    }
                }
            }
        }
    }

    /**
     * 해당 상품 정보가 포함된 사은품 정책 리스트 (이벤트는 제외)
     *
     * @param  integer $goodsNo 상품 번호
     * @param  string  $cateCd  카테고리 코드
     * @param  string  $brandCd 브랜드 코드
     *
     * @return array   사은품 정책 리스트
     */
    public function getGiftPresentInGoods($goodsNo, $cateCd = null, $brandCd = null)
    {
        // 현 페이지 결과
        $this->db->strField = 'gp.* ';
        $this->db->strWhere = 'if (gp.presentPeriodFl = \'y\', (gp.periodStartYmd <= \'' . date('Y-m-d H:i:s') . '\' AND gp.periodEndYmd >= \'' . date('Y-m-d H:i:s') . '\'), gp.presentPeriodFl = \'n\' )';
        $this->db->strOrder = 'gp.sno DESC';

        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_GIFT_PRESENT . ' gp ' . gd_implode(' ', $query);
        $data = $this->db->query_fetch($strSQL);

        $getData = [];
        foreach ($data as $key => $val) {
            $presentResult = false;

            // 포함 여부 체크
            $presentKindCd = explode(INT_DIVISION, $val['presentKindCd']);
            $arrCateCd = [];
            $arrBrandCd = [];
            if (!empty($cateCd)) {
                for ($i = 0; $i < (strlen($cateCd) / DEFAULT_LENGTH_CATE); $i++) {
                    $arrCateCd[] = substr($cateCd, 0, (($i + 1) * DEFAULT_LENGTH_CATE));
                }
            }
            if (!empty($brandCd)) {
                for ($i = 1; $i < (strlen($brandCd) / DEFAULT_LENGTH_CATE); $i++) {
                    $arrBrandCd[] = substr($brandCd, 0, (($i + 1) * DEFAULT_LENGTH_CATE));
                }
            }
            if ($val['presentFl'] == 'a') {
                $presentResult = true;
            } elseif ($val['presentFl'] == 'g') {
                if (gd_in_array($goodsNo, $presentKindCd)) {
                    $presentResult = true;
                }
            } elseif ($val['presentFl'] == 'c') {
                foreach ($arrCateCd as $cVal) {
                    if (gd_in_array($cVal, $presentKindCd)) {
                        $presentResult = true;
                    }
                }
            } elseif ($val['presentFl'] == 'b') {
                foreach ($arrBrandCd as $cVal) {
                    if (gd_in_array($cVal, $presentKindCd)) {
                        $presentResult = true;
                    }
                }
            }

            // 예외 여부 체크
            $exceptGoodsNo = explode(INT_DIVISION, $val['exceptGoodsNo']);
            $exceptCateCd = explode(INT_DIVISION, $val['exceptCateCd']);
            $exceptBrandCd = explode(INT_DIVISION, $val['exceptBrandCd']);

            if ($presentResult === true) {
                if (gd_in_array($goodsNo, $exceptGoodsNo)) {
                    $presentResult = false;
                }
                foreach ($arrCateCd as $cVal) {
                    if (gd_in_array($cVal, $exceptCateCd)) {
                        $presentResult = false;
                    }
                }
                foreach ($arrBrandCd as $cVal) {
                    if (gd_in_array($cVal, $exceptBrandCd)) {
                        $presentResult = false;
                    }
                }
            }

            if ($presentResult === true) {
                $getData[$key]['sno'] = $val['sno'];
                $getData[$key]['presentTitle'] = $val['presentTitle'];
                if ($val['presentPeriodFl'] == 'y') {
                    $getData[$key]['period'] = $val['periodStartYmd'] . ' ~ ' . $val['periodEndYmd'];
                } else {
                    $getData[$key]['period'] = __('제한없음');
                }
            }
        }

        return gd_htmlspecialchars_stripslashes($getData);
    }

    /**
     * 주문시 사은품 리스트
     *
     * @param  integer $getData 상품 정보
     * @param integer $groupSno 그룹번호
     * @param boolean $isWrite 수기주문 여부
     * @param bool $isStrip 데이터 반환 시 gd_htmlspecialchars_stripslashes 실행 여부 (주문시 사은품 유효성 체크에 사용)
     *
     * @return array   사은품 리스트
     */
    public function getGiftPresentOrder($getData, $groupSno=0, $isWrite=false, $isStrip = true)
    {
        // 현 페이지 결과
        $arrInclude = [
            'scmNo',
            'presentTitle',
            'presentFl',
            'presentKindCd',
            'exceptGoodsNo',
            'exceptCateCd',
            'exceptBrandCd',
            'exceptEventCd',
            'conditionFl',
            'presentPermission',
            'presentPermissionGroup',
            'addGoodsFl',
        ];
        $arrField = DBTableField::setTableField('tableGiftPresent', $arrInclude, null, 'gp');

        $this->db->strField = 'sno, ' . gd_implode(', ', $arrField);
        $this->db->strWhere = 'if (gp.presentPeriodFl = \'y\', (gp.periodStartYmd <= \'' . date('Y-m-d H:i:s') . '\' AND gp.periodEndYmd >= \'' . date('Y-m-d H:i:s') . '\'), gp.presentPeriodFl = \'n\' )';
        $this->db->strOrder = 'gp.sno DESC';

        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_GIFT_PRESENT . ' gp ' . gd_implode(' ', $query);
        $arrGiftPresent = $this->db->query_fetch($strSQL);


        //현재 그룹 정보
        if($isWrite === true){
            $myGroup = $groupSno;
        }
        else {
            $myGroup = Session::get('member.groupSno');
        }

        foreach ($getData as $goodsNo => $goodsInfo) {
            \Logger::channel('order')->info('사은품 지급을 위한 상품 데이터 탐색', [$goodsNo, $goodsInfo['price'], $goodsInfo['cnt']]);
            foreach ($arrGiftPresent as $key => $giftPresent) {
                if ($giftPresent['scmNo'] != $goodsInfo['scmNo']) { //공급사가 다른경우 패쓰
                    continue;
                }

                if ($giftPresent['presentPermission'] != 'all') {
                    if($isWrite === true){
                        if($groupSno < 1){
                            continue;
                        }
                    }
                    else {
                        if (gd_is_login() === false) {
                            continue;
                        }
                    }

                    if ($giftPresent['presentPermission'] == 'group' && $giftPresent['presentPermissionGroup'] && !gd_in_array($myGroup, explode(INT_DIVISION, $giftPresent['presentPermissionGroup']))) {
                        continue;
                    }
                }

                $presentResult = false;

                // 포함 여부 체크
                $presentKindCd = explode(INT_DIVISION, $giftPresent['presentKindCd']);
                $arrCateCd = [];
                $arrBrandCd = [];
                if (!empty($goodsInfo['cateCd'])) {
                    for ($i = 0; $i < (strlen($goodsInfo['cateCd']) / DEFAULT_LENGTH_CATE); $i++) {
                        $arrCateCd[] = substr($goodsInfo['cateCd'], 0, (($i + 1) * DEFAULT_LENGTH_CATE));
                    }
                }
                if (!empty($goodsInfo['brandCd'])) {
                    for ($i = 0; $i < (strlen($goodsInfo['brandCd']) / DEFAULT_LENGTH_CATE); $i++) {
                        $arrBrandCd[] = substr($goodsInfo['brandCd'], 0, (($i + 1) * DEFAULT_LENGTH_CATE));
                    }
                }

                // 지급상품선택 조건
                switch ($giftPresent['presentFl']) {
                    // 전체상품
                    case 'a':
                        $presentResult = true;
                        break;

                    // 특정상품
                    case 'g':
                        if (gd_in_array($goodsNo, $presentKindCd)) {
                            $presentResult = true;
                        }
                        break;

                    // 특정카테고리
                    case 'c':
                        foreach ($arrCateCd as $cVal) {
                            if (gd_in_array($cVal, $presentKindCd)) {
                                $presentResult = true;
                            }
                        }
                        break;

                    // 특정브랜드
                    case 'b':
                        foreach ($arrBrandCd as $cVal) {
                            if (gd_in_array($cVal, $presentKindCd)) {
                                $presentResult = true;
                            }
                        }
                        break;
                }

                // 예외 여부 체크
                $exceptGoodsNo = explode(INT_DIVISION, $giftPresent['exceptGoodsNo']);
                $exceptCateCd = explode(INT_DIVISION, $giftPresent['exceptCateCd']);
                $exceptBrandCd = explode(INT_DIVISION, $giftPresent['exceptBrandCd']);

                // 지급이 가능 한 경우
                if ($presentResult === true) {
                    if (gd_in_array($goodsNo, $exceptGoodsNo)) {
                        $presentResult = false;
                    }
                    foreach ($arrCateCd as $cVal) {
                        if (gd_in_array($cVal, $exceptCateCd)) {
                            $presentResult = false;
                        }
                    }
                    foreach ($arrBrandCd as $cVal) {
                        if (gd_in_array($cVal, $exceptBrandCd)) {
                            $presentResult = false;
                        }
                    }
                }

                // 사은품 증정 데이타
                if ($presentResult === true) {
                    \Logger::channel('order')->info(__METHOD__ . '지급할 사은품', [$giftPresent]);
                    $setData[$giftPresent['sno']]['goodsNo'] = $goodsNo;
                    $setData[$giftPresent['sno']]['scmNo'] = $giftPresent['scmNo'];
                    $setData[$giftPresent['sno']]['price'] = gd_isset($setData[$giftPresent['sno']]['price'], 0) + $goodsInfo['price'];
                    $setData[$giftPresent['sno']]['cnt'] = gd_isset($setData[$giftPresent['sno']]['cnt'], 0) + $goodsInfo['cnt'];
                    // 추가상품 수량 포함 체크 + (구매상품 수량만큼 지급 || 수량별 지급) 시 cnt 에 추가상품 수량 더하기
                    if($giftPresent['addGoodsFl'] == 'y' && ($giftPresent['conditionFl'] == 'l' || $giftPresent['conditionFl'] == 'c')) {
                        $setData[$giftPresent['sno']]['cnt'] += $goodsInfo['addGoodsCnt'];
                    }
                    $setData[$giftPresent['sno']]['title'] = $giftPresent['presentTitle'];
                    $setData[$giftPresent['sno']]['condition'] = $giftPresent['conditionFl'];
                    $setData[$giftPresent['sno']]['presentSno'] = $giftPresent['sno'];
                    \Logger::channel('order')->info(__METHOD__ . '사은품 지급 결과', [$setData]);
                }
            }
        }
        unset($arrGiftPresent);

        // 사은품 증정 데이타가 없으면 return
        if (isset($setData) === false) {
            return;
        }

        // 사은품 불러오기
        $arrInclude = [
            'multiGiftNo',
            'selectCnt',
            'giveCnt',
            'infoNo',
        ];
        $arrField = DBTableField::setTableField('tableGiftPresentInfo', $arrInclude, null, 'gpi');
        $arrBind = [];
        foreach ($setData as $sno => & $setDataValue) {
            $strWhere = [];
            $strWhere[] = 'gpi.presentSno = ?';
            $this->db->bind_param_push($arrBind, 'i', $sno);

            // 증정조건 체크
            switch ($setDataValue['condition']) {
                // 금액별 지급인 경우
                case 'p':
                    $strWhere[] = ' ? BETWEEN gpi.conditionStart AND gpi.conditionEnd';
                    $this->db->bind_param_push($arrBind, 'i', $setDataValue['price']);
                    break;

                // 수량별 지급인 경우
                case 'c':
                    $strWhere[] = ' ? BETWEEN gpi.conditionStart AND gpi.conditionEnd AND giveCnt > 0';
                    $this->db->bind_param_push($arrBind, 'i', $setDataValue['cnt']);
                    break;
            }

            $this->db->strField = gd_implode(', ', $arrField);
            $this->db->strWhere = gd_implode(' AND ', $strWhere);

            $query = $this->db->query_complete();
            $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_GIFT_PRESENT_INFO . ' gpi ' . gd_implode(' ', $query);
            $giftData = $this->db->query_fetch($strSQL, $arrBind);
            unset($arrBind, $strWhere);

            // 사은품이 존재하지 않는 경우 unset
            if (empty($giftData) === true) {
                unset($setData[$sno]);
            } else {
                // 사은품 정보
                $this->viewGiftData($giftData, 80, true, true);

                // 사은품이 없으면 사은품 증정 정보를 unset
                if (empty($giftData) === true) {
                    unset($setData[$sno]);
                } else {
                    // 사은품 총 갯수 설정
                    foreach ($giftData as $cKey => $cVal) {
                        // 구매상품 수량만큼 지급하는 경우
                        if ($setDataValue['condition'] == 'l') {
                            $giftData[$cKey]['giveCnt'] = $cVal['giveCnt'] * $setDataValue['cnt'];
                        }
                        $setDataValue['total'] += $cVal['total'];
                    }

                    // 사은품 데이터
                    $setDataValue['gift'] = $giftData;
                }
            }
        }
        return $isStrip ? gd_htmlspecialchars_stripslashes($setData) : $setData;
    }

    /**
     * 주문 사은품 데이터 검증 및 정규화 (변조 방지)
     *
     * 주문서에서 전달된 사은품 hidden 필드(giveCnt/selectCnt/giftNo)는 개발자도구 등으로
     * 변조가 가능하므로, 서버에서 재조회한 유효 사은품 정보($giftInfo)를 기준으로 검증·정규화
     *  - giveCnt(지급수량) : 지급조건의 값으로 강제 정규화 (변조 무력화, 주문은 정상 생성)
     *  - selectCnt(선택수량) : 실제 선택한 사은품 개수가 지급조건과 다르면 차단 (전체=0 은 제외)
     *  - 지급조건에 포함되지 않은 사은품(giftNo)은 제거
     *
     * @param array $submittedGift 주문서에서 전달된 사은품 데이터 [presentSno][index] => [...]
     * @param array $giftInfo getGiftPresentOrder() 로 조회한 유효 사은품 정보
     * @return array  정규화된 사은품 데이터
     * @throws \Exception
     */
    public function validateAndNormalizeOrderGift(array $submittedGift, array $giftInfo): array
    {
        if (empty($submittedGift) || empty($giftInfo)) {
            return $submittedGift;
        }

        foreach ($submittedGift as $presentSno => $giftData) {
            // 유효하지 않은 사은품 정책은 기존 저장 로직에서 스킵되므로 건드리지 않음
            if (!array_key_exists($presentSno, $giftInfo) || empty($giftInfo[$presentSno]['gift'])) {
                continue;
            }

            // 권위 데이터 집계 : giftNo => 지급조건(row), 지급조건별 giveCnt/selectCnt
            $authGiveCnt = []; // giftNo => giveCnt
            $giftNoToRow = []; // giftNo => rowKey
            $rowSelectCnt = []; // rowKey => selectCnt
            foreach ($giftInfo[$presentSno]['gift'] as $rowKey => $row) {
                $rowSelectCnt[$rowKey] = (int) ($row['selectCnt'] ?? 0);
                if (empty($row['multiGiftNo']) || !is_array($row['multiGiftNo'])) {
                    continue;
                }
                foreach ($row['multiGiftNo'] as $item) {
                    if (!isset($item['giftNo'])) {
                        continue;
                    }
                    $authGiveCnt[$item['giftNo']] = (int) ($row['giveCnt'] ?? 0);
                    $giftNoToRow[$item['giftNo']] = $rowKey;
                }
            }

            // 제출된 사은품 정규화 + 지급조건별 선택 개수/제출 선택수량 집계
            $selectedCntByRow = [];
            $submittedSelectCntByRow = [];
            foreach ($giftData as $index => $gift) {
                // 선택되지 않은 항목(giftNo 없음)은 통과 (기존 로직이 저장하지 않음)
                if (!isset($gift['giftNo']) || $gift['giftNo'] === '') {
                    continue;
                }
                // 지급조건에 포함되지 않은 사은품(변조)은 제거
                if (!isset($authGiveCnt[$gift['giftNo']])) {
                    \Logger::channel('order')->info(__METHOD__ . ' 지급조건에 없는 사은품 제거', [$presentSno, $gift['giftNo']]);
                    unset($submittedGift[$presentSno][$index]);
                    continue;
                }
                // giveCnt(지급수량) 정규화 - POST 변조 무력화
                $submittedGift[$presentSno][$index]['giveCnt'] = $authGiveCnt[$gift['giftNo']];

                $rowKey = $giftNoToRow[$gift['giftNo']];
                $selectedCntByRow[$rowKey] = ($selectedCntByRow[$rowKey] ?? 0) + 1;
                // 제출된 선택수량(변조 탐지용) - 같은 지급조건 항목은 동일 값
                if (isset($gift['selectCnt'])) {
                    $submittedSelectCntByRow[$rowKey] = (int) $gift['selectCnt'];
                }
            }

            // 선택수량(selectCnt) 검증
            foreach ($rowSelectCnt as $rowKey => $selectCnt) {
                $selectedCnt = (int) ($selectedCntByRow[$rowKey] ?? 0);
                $submittedSelectCnt = $submittedSelectCntByRow[$rowKey] ?? null;
                $message = GiftSelectCntValidator::resolveViolationMessage($selectCnt, $selectedCnt, $submittedSelectCnt);
                if ($message !== null) {
                    \Logger::channel('order')->warning(__METHOD__ . ' 사은품 선택수량 검증 실패', [
                        'presentSno' => $presentSno,
                        'rowKey' => $rowKey,
                        'authSelectCnt' => $selectCnt,
                        'selectedCnt' => $selectedCnt,
                        'submittedSelectCnt' => $submittedSelectCnt,
                    ]);
                    throw new \Exception($message);
                }
            }
        }

        return $submittedGift;
    }
}
