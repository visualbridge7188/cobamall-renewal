<?php
/*
Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved 

Unauthorized copying or redistribution of this file in source and binary forms via any medium 
is strictly prohibited.
*/

namespace Bundle\Controller\Mobile\Present;

class PresentWriteController extends \Controller\Front\Present\PresentWriteController
{
    public function index()
    {
        parent::index();
        
        $this->setData('gPageName', '선물카드/수령대상설정');
    }
}
