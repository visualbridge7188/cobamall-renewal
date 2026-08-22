<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\Present;

use Component\Database\DBTableField;
use Framework\Database\DBManager;

/**
 * 선물하기 테이블 데이터 처리 클래스
 */
class PresentDAO
{
    public function __construct(
        private readonly DBManager $db
    ) {
    }

    /**
     * 선물하기 수령자 정보 저장
     * 
     * @param array $receiverInfoData 수령자 정보 데이터
     * @return void
     * @throws DatabaseException
     */
    public function insertPresentReceiverInfo(array $receiverInfoData): void
    {
        $arrBind = $this->db->get_binding(DBTableField::tablePresentReceiverInfo(), $receiverInfoData, 'insert');
        $this->db->set_insert_db(DB_PRESENT_RECEIVER_INFO, $arrBind['param'], $arrBind['bind'], 'y', false);
    }

    /**
     * 선물하기 주문 카드 정보 저장
     * 
     * @param array $orderCardData 주문 카드 데이터
     * @return void
     * @throws DatabaseException
     */
    public function insertPresentOrderCard(array $orderCardData): void
    {
        $arrBind = $this->db->get_binding(DBTableField::tablePresentOrderCard(), $orderCardData, 'insert');
        $this->db->set_insert_db(DB_PRESENT_ORDER_CARD, $arrBind['param'], $arrBind['bind'], 'y', false);
    }

    /**
     * 선물하기 수령자 정보 업데이트
     * 
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품번호
     * @param array $presentReceiverUpdateData 수령자 정보 데이터
     * @return void
     * @throws DatabaseException 선물 수령자 정보 업데이트 실패 시 예외 발생
     */
    public function updatePresentReceiverInfo(string $orderNo, int $orderGoodsNo, array $presentReceiverUpdateData): void
    {
        // modDt는 set_update_db에서 자동으로 modDt=now() 추가되므로 제외
        $updateDataWithoutModDt = $presentReceiverUpdateData;
        unset($updateDataWithoutModDt['modDt']);
        
        // 전달된 배열의 키만 사용하여 해당 필드만 업데이트
        $arrBind = $this->db->get_binding(
            DBTableField::tablePresentReceiverInfo(), 
            $updateDataWithoutModDt, 
            'update',
            array_keys($updateDataWithoutModDt) // 전달된 필드만 포함
        );
        
        $this->db->bind_param_push($arrBind['bind'], 's', $orderNo);
        $this->db->bind_param_push($arrBind['bind'], 'i', $orderGoodsNo);
        $this->db->set_update_db(DB_PRESENT_RECEIVER_INFO, $arrBind['param'], 'orderNo = ? AND orderGoodsNo = ?', $arrBind['bind']);
    }

    /**
     * 주문번호로 confirmToken을 빈값으로 업데이트
     * 
     * @param string $orderNo 주문번호
     * @return void
     * @throws DatabaseException
     */
    public function clearConfirmTokenByOrderNo(string $orderNo): void
    {
        $updateData = ['confirmToken' => ''];
        $arrBind = $this->db->get_binding(
            DBTableField::tablePresentReceiverInfo(), 
            $updateData, 
            'update',
            ['confirmToken']
        );
        
        $this->db->bind_param_push($arrBind['bind'], 's', $orderNo);
        $this->db->set_update_db(DB_PRESENT_RECEIVER_INFO, $arrBind['param'], 'orderNo = ?', $arrBind['bind']);
    }
}
