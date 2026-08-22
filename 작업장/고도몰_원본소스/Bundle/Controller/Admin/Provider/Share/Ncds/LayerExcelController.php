<?php

/* 
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved 
 * 
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited. 
 */

namespace Bundle\Controller\Admin\Provider\Share\Ncds;

class LayerExcelController extends \Controller\Admin\Share\LayerExcelController
{
    public function index()
    {
        parent::index();

        // passwordFl 기본값 설정 (기본적으로 '사용'으로 설정)
        $checked['passwordFl']['y'] = 'checked="checked"';
        $checked['passwordFl']['n'] = '';
        
        $this->setData('checked', $checked);
        $this->getView()->setPageName('share/ncds/layer_excel.php');
    }
}
