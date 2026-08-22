<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Share\Ncds;

class LayerScmController extends \Controller\Admin\Share\LayerScmController
{
    public function index()
    {
        parent::index();

        $data = $this->getData('data');

        if (empty($data) || !is_array($data)) {
            $data = [];
        }

        $this->ncdsJson($data);
    }
}
