<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Component\Page\Page;
use Component\Member\MemberGroup;
use Framework\StaticProxy\Proxy\UserFilePath;
use Framework\Utility\StringUtils;

class LayerSelectMemberGroupsResultController extends \Controller\Admin\Controller
{
    public function index()
    {
        $request = \App::getInstance('request');
        $getParams = $request->get()->all();

        /* @var \Bundle\Component\Member\MemberGroup $memberGroupComponent */
        $memberGroupComponent = \App::load(MemberGroup::class);
        $groupData = $memberGroupComponent->getGroupListSearch();
        $groups = $groupData['data'] ?? [];

        // getGroupListSearch 내부 페이징 처리 포함
        $page = \App::load('\\Component\\Page\\Page');
        $searchCount = $page->recode['total'];
        $totalCount = $memberGroupComponent->getAllGroupCount()[0]['cnt'];

        $this->setData('search', StringUtils::strIsSet($groupData['search']));
        $this->setData('groups', $groups);
        $this->setData('groupSearchCount', $searchCount);
        $this->setData('groupTotalCount', $totalCount);
        $this->setData('page', $page);
        $this->setData('requestData', $getParams);

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
