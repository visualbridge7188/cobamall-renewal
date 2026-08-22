<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\Goods;

use Bundle\Component\Database\DBTableField;
use Framework\Database\DBManager;

class SimpleGoodsDAO
{
    public function __construct(
        private readonly DBManager $db
    ) {
    }

    public function insertSimpleGoods(int $goodsNo): void
    {
        $arrData = [
            'goodsNo' => $goodsNo,
            'simpleGoodsFl' => 'y'
        ];
        $arrBind = $this->db->get_binding(DBTableField::tableSimpleGoods(), $arrData, 'insert');
        $this->db->set_insert_db(DB_SIMPLE_GOODS, $arrBind['param'], $arrBind['bind'], 'y');
    }

    /**
     * 옵션 이미지 임시 세션 문자열 생성 (32자리, DB_GOODS_OPTION_ICON_TEMP 기준 중복 회피)
     *
     * 일반상품등록 옵션 팝업의 세션 생성(GoodsAdmin::getSessionString)과 동일한 규칙으로 생성.
     */
    public function generateOptionIconTempSession(): string
    {
        do {
            $session = '';
            for ($i = 0; $i < 32; $i++) {
                switch (random_int(0, 2)) {
                    case 0:
                        $session .= chr(random_int(48, 57));  // 0-9
                        break;
                    case 1:
                        $session .= chr(random_int(65, 90));  // A-Z
                        break;
                    default:
                        $session .= chr(random_int(97, 122)); // a-z
                        break;
                }
            }

            $arrBind = [];
            $this->db->bind_param_push($arrBind['bind'], 's', $session);
            $strSQL = 'SELECT COUNT(*) cnt FROM ' . DB_GOODS_OPTION_ICON_TEMP . ' WHERE session = ?';
            $row = $this->db->query_fetch($strSQL, $arrBind['bind'], false);
        } while (($row['cnt'] ?? 0) != 0);

        return $session;
    }

    /**
     * 옵션 이미지 임시테이블(DB_GOODS_OPTION_ICON_TEMP) 다건 INSERT
     *
     * @param array $rows [['session','optionNo','optionValue','goodsImage','isUpdated'], ...]
     */
    public function insertOptionIconTempRows(array $rows): void
    {
        foreach ($rows as $row) {
            $arrBind = $this->db->get_binding(DBTableField::tableGoodsOptionIconTemp(), $row, 'insert');
            $this->db->set_insert_db(DB_GOODS_OPTION_ICON_TEMP, $arrBind['param'], $arrBind['bind'], 'y');
        }
    }
}
