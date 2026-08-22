<?php

/* 
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved 
 * 
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium 
 * is strictly prohibited. 
 */

namespace Bundle\Controller\Admin\Share\Ncds;


class LayerMemberGroupController extends \Controller\Admin\Share\LayerMemberGroupController
{
    /**
     * Description
     */
    public function index()
    {
        parent::index();
        
        $data = $this->getData('data');
        $this->ncdsJson($data);
    }
}
