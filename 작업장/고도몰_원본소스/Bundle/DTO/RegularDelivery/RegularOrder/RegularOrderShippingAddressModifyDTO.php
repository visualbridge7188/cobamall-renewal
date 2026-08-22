<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Framework\Utility\StringUtils;
use Origin\DTO\AbstractDTO;
use Session;

/**
 * @property-read int $sno
 * @property-read string $defaultFl
 * @property-read int $memNo
 * @property-read string $shippingTitle
 * @property-read string $shippingName
 * @property-read string|null $shippingPhone
 * @property-read string|null $shippingCellPhone
 * @property-read string|null $shippingZonecode
 * @property-read string $shippingAddress
 * @property-read string $shippingAddressSub
 * @property-read string|null $shippingMessage
 * @property-read string|null $modDt
 */
class RegularOrderShippingAddressModifyDTO extends AbstractDTO
{
    public $sno;
    public $defaultFl;
    public $memNo;
    public $shippingTitle;
    public $shippingName;
    public $shippingZonecode;
    public $shippingAddress;
    public $shippingAddressSub;
    public $shippingMessage;
    public $shippingPhone;
    public $shippingCellPhone;
    public $modDt;
    /**
     * @param array $shippingAddressData
     */
    public function __construct(array $shippingAddressData)
    {
        $data['sno'] = $shippingAddressData['sno'];
        $data['defaultFl'] = $shippingAddressData['defaultFl'];
        $data['memNo'] = Session::get('member.memNo');
        $data['shippingTitle'] = $shippingAddressData['shippingTitle'];
        $data['shippingName'] = $shippingAddressData['shippingName'];
        $data['shippingZonecode'] = $shippingAddressData['shippingZonecode'];
        $data['shippingAddress'] = $shippingAddressData['shippingAddress'];
        $data['shippingAddressSub'] = $shippingAddressData['shippingAddressSub'];
        $data['shippingMessage'] = $shippingAddressData['shippingMessage'];
        $data['shippingPhone'] = StringUtils::numberToPhone(str_replace('-', '', $shippingAddressData['shippingPhone']), true);
        $data['shippingCellPhone'] = StringUtils::numberToPhone(str_replace('-', '', $shippingAddressData['shippingCellPhone']), true);
        $data['modDt'] = date('Y-m-d H:i:s');

        $this->setProperties($data);
    }
}
