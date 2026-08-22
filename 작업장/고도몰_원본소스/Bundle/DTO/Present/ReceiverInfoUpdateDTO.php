<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\Present;

use Carbon\Carbon;
use Origin\DTO\AbstractDTO;
use Component\Present\Confirm\PresentConfirm;

/**
 * 선물 수령자 정보 업데이트 DTO
 * es_presentReceiverInfo와 es_orderInfo 두 테이블에 공통으로 사용
 */
class ReceiverInfoUpdateDTO extends AbstractDTO
{
    protected readonly string $receiverName;
    protected readonly string $phone;
    protected readonly string $cellPhone;
    protected readonly string $zipcode;
    protected readonly string $address;
    protected readonly string $addressSub;
    protected readonly string $orderMemo;
    protected readonly string $acceptFl;
    protected readonly Carbon $modDt;

    /**
     * @param array $receiverData 수령자 정보 ['receiverName', 'phone', 'cellPhone', 'zonecode', 'zipcode', 'address', 'addressSub', 'orderMemo', 'acceptFl']
     */
    public function __construct(array $receiverData)
    {
        $this->receiverName = $receiverData['receiverName'] ?? '';
        $this->phone = $receiverData['phone'] ?? '';
        $this->cellPhone = $receiverData['cellPhone'] ?? '';
        
        // zipcode 처리: zipcode가 있으면 사용, 없으면 zonecode를 zipcode로 사용(es_orderInfo 테이블에서 둘다 사용중으로 추가)
        if (isset($receiverData['zipcode']) && $receiverData['zipcode'] !== '' && $receiverData['zipcode'] !== '-') {
            $this->zipcode = $receiverData['zipcode'];
        } elseif (isset($receiverData['zonecode']) && $receiverData['zonecode'] !== '' && $receiverData['zonecode'] !== '-') {
            $this->zipcode = $receiverData['zonecode'];
        } else {
            $this->zipcode = '';
        }
        
        $this->address = $receiverData['address'] ?? '';
        $this->addressSub = $receiverData['addressSub'] ?? '';
        $this->orderMemo = $receiverData['orderMemo'] ?? '';
        $this->acceptFl = $receiverData['acceptFl'] ?? PresentConfirm::PRESENT_ACCEPT_READY;
        $this->modDt = Carbon::now();
    }

    /**
     * es_presentReceiverInfo 테이블 업데이트용 배열 반환
     * 
     * @param array $excludeFields 제외할 필드 목록 (예: ['modDt', 'acceptFl'])
     * @return array
     */
    public function toPresentReceiverInfoArray(array $excludeFields = [], bool $withNoFilter = false): array
    {
        $data = [
            'receiverName' => $this->receiverName,
            'phone' => $this->phone,
            'cellPhone' => $this->cellPhone,
            'zipcode' => $this->zipcode,
            'address' => $this->address,
            'addressSub' => $this->addressSub,
            'orderMemo' => $this->orderMemo,
            'acceptFl' => $this->acceptFl,
            'modDt' => $this->modDt,
        ];
        
        // 제외할 필드 제거
        foreach ($excludeFields as $field) {
            unset($data[$field]);
        }
        

        if ($withNoFilter) {
            return $data;
        } else {
            // 빈 값과 null 제거 (업데이트에 필요한 데이터만 반환)
            return array_filter($data, function($value) {
                return $value !== '' && $value !== null;
            });
        }
    }

    /**
     * es_orderInfo 테이블 업데이트용 배열 반환
     * 
     * @param array $excludeFields 제외할 필드 목록 (예: ['modDt'])
     * @return array
     */
    public function toOrderInfoArray(array $excludeFields = [], bool $withNoFilter = false): array
    {
        $data = [
            'receiverName' => $this->receiverName,
            'receiverPhone' => $this->phone,
            'receiverCellPhone' => $this->cellPhone,
            'receiverZipcode' => $this->zipcode,
            'receiverAddress' => $this->address,
            'receiverAddressSub' => $this->addressSub,
            'orderMemo' => $this->orderMemo,
            'modDt' => $this->modDt,
        ];
        
        // 제외할 필드 제거
        foreach ($excludeFields as $field) {
            unset($data[$field]);
        }

        if ($withNoFilter) {
            return $data;
        } else {
            // 빈 값과 null 제거 (업데이트에 필요한 데이터만 반환)
            return array_filter($data, function($value) {
                return $value !== '' && $value !== null;
            });
        }
    }
}

