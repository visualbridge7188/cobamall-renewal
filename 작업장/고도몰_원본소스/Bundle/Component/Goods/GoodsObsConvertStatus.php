<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ? 2016, NHN commerce: Corp.
 * @link http://www.commerce.co.kr
 */

namespace Bundle\Component\Goods;

use Component\Database\DBTableField;

class GoodsObsConvertStatus
{
    protected $db;

    protected $arrBind = [];

    protected $arrWhere = [];

    const DEFAULT_STORAGE = ['local', 'obs'];

    public function __construct()
    {
        if (!is_object($this->db)) {
            $this->db = \App::load('DB');
        }
    }

    public function getConvertFlByData($arrImage = [], $arrOptionIcon = [], $imageStorage = "") {
        $imageDisabledData = [
            'manage' => '',
            'manageClass' => '',
            'manageOption' => '',
            'manageOptionClass' => '',
            'alertYn' => 'N'
        ];

        if (gd_in_array($imageStorage, self::DEFAULT_STORAGE)) {
            foreach ($arrImage as $imageKey => $imageValue) {
                if ($imageValue['obsConvertFl'] == 'n') {
                    // $goodsNo 상풍이미지 obs 전환 중 -> 상품이미지 수정 불가
                    $imageDisabledData['manage'] = 'disabled="disabled"';
                    $imageDisabledData['manageClass'] = 'obs-display-none';
                }
            }

            foreach ($arrOptionIcon as $optionIconKey => $optionIconValue) {
                if ($optionIconValue['obsConvertFl'] == 'n') {
                    // $goodsNo 상품옵션이미지 obs 전환 중 -> 상품옵션이미지 수정 불가
                    $imageDisabledData['manageOption'] = 'disabled="disabled"';
                    $imageDisabledData['manageOptionClass'] = 'obs-display-none';
                }
            }

            if (!empty($imageDisabledData['manage']) || !empty($imageDisabledData['manageOption'])) {
                // 페이지 접근 시 알림창 여부
                $imageDisabledData['manage'] = 'disabled="disabled"';
                $imageDisabledData['manageOption'] = 'disabled="disabled"';
                $imageDisabledData['manageClass'] = 'obs-display-none';
                $imageDisabledData['manageOptionClass'] = 'obs-display-none';
                $imageDisabledData['alertYn'] = 'Y';
            }
        }

        return gd_htmlspecialchars_stripslashes($imageDisabledData);
    }

    public function getGoodsObsConvertStatusCount($goodsNo) {
        $arrBind = [];
        $strSql = '
            SELECT gi.goodsNo, COUNT(*) AS obsConvertStatusCnt
            FROM (
                  SELECT goodsNo, obsConvertFl AS imageConvertFl, "y" AS optionConvertFl
                  FROM es_goodsImage
                  WHERE goodsNo = ?
            
                  UNION ALL
            
                  SELECT goodsNo, "y" AS imageConvertFl, obsConvertFl AS optionConvertFl
                  FROM es_goodsOptionIcon
                  WHERE goodsNo = ?
            ) AS gi
            LEFT JOIN es_goods as g on g.goodsNo = gi.goodsNo
            WHERE (gi.imageConvertFl = "n" OR gi.optionConvertFl = "n")
            AND g.imageStorage IN ("obs", "local")
            GROUP BY gi.goodsNo
        ';

        $this->db->bind_param_push($arrBind, 'i', $goodsNo);
        $this->db->bind_param_push($arrBind, 'i', $goodsNo);

        return $this->db->query_fetch($strSql, $arrBind, false);
    }

    public function updateObsConvertFl($goodsNo, $arrGoodsImageData = []) {
        if (!empty($arrGoodsImageData)) {
            // 일부만 수정하는 경우 일부만 convertFl = n 으로 업데이트 할 수 있도록
            $imageData = $arrGoodsImageData['image'];
            $optionImageData = $arrGoodsImageData['imageIcon'];

            // 상품이미지
            $insertData = $imageData['insert'] ?? [];
            $updateData = $imageData['update'] ?? [];

            $insertUpdateData = gd_array_merge($insertData, $updateData);
            foreach ($insertUpdateData as $key => $val) {
                if (filter_var($val['imageName'], FILTER_VALIDATE_URL) !== false) {
                    continue;
                }

                $arrBind = [];
                $strSQL = 'UPDATE ' . DB_GOODS_IMAGE . ' SET obsConvertFl = ? WHERE goodsNo = ? AND imageNo = ? AND imageKind = ? AND imageName = ?';
                $this->db->bind_param_push($arrBind, 's', 'n');
                $this->db->bind_param_push($arrBind, 'i', $goodsNo);
                $this->db->bind_param_push($arrBind, 's', $val['imageNo']);
                $this->db->bind_param_push($arrBind, 's', $val['imageKind']);
                $this->db->bind_param_push($arrBind, 's', $val['imageName']);
                $this->db->bind_query($strSQL, $arrBind);
            }

            // 상품옵션이미지
            $insertData = $optionImageData['insert'] ?? [];
            $updateData = $optionImageData['update'] ?? [];

            $insertUpdateData = gd_array_merge($insertData, $updateData);
            foreach ($insertUpdateData as $key => $val) {
                $arrBind = [];
                $strSQL = 'UPDATE ' . DB_GOODS_OPTION_ICON . ' SET obsConvertFl = ? WHERE goodsNo = ? AND optionValue = ? AND goodsImage NOT LIKE "http%" ';
                $this->db->bind_param_push($arrBind, 's', 'n');
                $this->db->bind_param_push($arrBind, 'i', $goodsNo);
                $this->db->bind_param_push($arrBind, 's', $val['optionValue']);
                $this->db->bind_query($strSQL, $arrBind);
            }
        } else {
            // 상품이미지
            $arrBind = [];
            $strSQL = 'UPDATE ' . DB_GOODS_IMAGE . ' SET obsConvertFl = ? WHERE goodsNo = ? AND imageName NOT LIKE "http%" ';
            $this->db->bind_param_push($arrBind, 's', 'n');
            $this->db->bind_param_push($arrBind, 'i', $goodsNo);
            $this->db->bind_query($strSQL, $arrBind);

            // 상품옵션이미지
            $arrBind = [];
            $strSQL = 'UPDATE ' . DB_GOODS_OPTION_ICON . ' SET obsConvertFl = ? WHERE goodsNo = ? AND goodsImage NOT LIKE "http%" ';
            $this->db->bind_param_push($arrBind, 's', 'n');
            $this->db->bind_param_push($arrBind, 'i', $goodsNo);
            $this->db->bind_query($strSQL, $arrBind);
        }
    }

    /**
     * URL 이미지는 OBS 변환 대상이 아니므로 obsConvertFl 을 'y' 로 강제 처리하고
     * obs → url 직접입력 전환 시 잔존하는 OBS 경로/URL 컬럼을 함께 비운다.
     * (legacy 'n' 잔존값 정리 + 신규 작업 후 URL 행 잠금 방지 + stale OBS 컬럼 정리)
     */
    public function setObsConvertFlagForUrlImages($goodsNo)
    {
        // 상품이미지 : URL 직접입력 행은 OBS 폴더/URL 컬럼이 있을 이유가 없으므로 함께 비움
        $arrBind = [];
        $strSQL = 'UPDATE ' . DB_GOODS_IMAGE
            . ' SET obsConvertFl = ?, imageFolder = "", thumbImageFolder = "", imageUrl = "", thumbImageUrl = ""'
            . ' WHERE goodsNo = ? AND imageName LIKE "http%" ';
        $this->db->bind_param_push($arrBind, 's', 'y');
        $this->db->bind_param_push($arrBind, 'i', $goodsNo);
        $this->db->bind_query($strSQL, $arrBind);

        // 상품옵션이미지 : URL 직접입력 행은 goodsImageUrl / goodsThumbImageUrl 도 함께 비움
        $arrBind = [];
        $strSQL = 'UPDATE ' . DB_GOODS_OPTION_ICON
            . ' SET obsConvertFl = ?, goodsImageUrl = "", goodsThumbImageUrl = ""'
            . ' WHERE goodsNo = ? AND goodsImage LIKE "http%" ';
        $this->db->bind_param_push($arrBind, 's', 'y');
        $this->db->bind_param_push($arrBind, 'i', $goodsNo);
        $this->db->bind_query($strSQL, $arrBind);
    }

    /**
     * 상품번호를 기준으로 상품 이미지의 변환 대기 표시(obsConvertFl='n')를 풀어줌('y').
     */
    public function unlockObsConvertFl(int $goodsNo): void
    {
        // 상품이미지
        $arrBind = [];
        $strSQL = 'UPDATE ' . DB_GOODS_IMAGE . ' SET obsConvertFl = ? WHERE goodsNo = ? AND imageName NOT LIKE "http%" ';
        $this->db->bind_param_push($arrBind, 's', 'y');
        $this->db->bind_param_push($arrBind, 'i', $goodsNo);
        $this->db->bind_query($strSQL, $arrBind);

        // 상품옵션이미지
        $arrBind = [];
        $strSQL = 'UPDATE ' . DB_GOODS_OPTION_ICON . ' SET obsConvertFl = ? WHERE goodsNo = ? AND goodsImage NOT LIKE "http%" ';
        $this->db->bind_param_push($arrBind, 's', 'y');
        $this->db->bind_param_push($arrBind, 'i', $goodsNo);
        $this->db->bind_query($strSQL, $arrBind);
    }
}
