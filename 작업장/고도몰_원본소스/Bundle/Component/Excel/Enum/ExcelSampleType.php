<?php

namespace Bundle\Component\Excel\Enum;

enum ExcelSampleType: string
{
    case MEMBER = '회원샘플파일';
    case GOODS = '상품샘플파일';
    case DEPOSIT = '예치금샘플파일';
    case MILEAGE = '마일리지샘플파일';
    case SMS = 'SMS샘플파일';
    case COUPON = '쿠폰 수동 엑셀발급 샘플파일';
    case PAPER_COUPON = '페이퍼쿠폰 인증번호 엑셀등록 샘플파일';
    case INVOICE = 'invoice_excel_sample';
    case EXTERNAL_ORDER = 'external_order_excel_sample';
    case DELIVERY_AREA = 'area_delivery_excel_sample';

    public function getFilename(): string
    {
        return $this->value;
    }
}
