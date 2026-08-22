<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Component\Member\Group\Util;
use Component\Member\Member;
use Component\Member\Util\MemberUtil;
use Component\Page\Page;
use Framework\Http\Request;

class PopupSelectMembersController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageSend', 'mobileSend');

        /** @var Request $request */
        $request = \App::getInstance('request');
        $this->setDefaultRequestParams($request);

        $postData = $request->post()->toArray();

        /* @var \Bundle\Component\Member\Member $memberComponent */
        $memberComponent = \App::load(Member::class);

        $groups = Util::getGroupName();
        $checked = MemberUtil::checkedByMemberListSearch($postData);
        $selected = MemberUtil::selectedByMemberListSearch($postData);

        $this->setData('combineSearch', $memberComponent->getCombineSearchSelectBox());
        $this->setData('searchKind', $memberComponent->getSearchKindASelectBox());
        $this->setData('groups', $groups);
        $this->setData('search', $postData);
        $this->setData('checked', $checked);
        $this->setData('selected', $selected);

        // 필요
        $this->setData('sendMode', $getParams['sendMode']);

        $this->getView()->setDefine('layout', 'layout_blank.php');
        $this->getView()->setPageName('crm/popup_select_members.php');
    }

    private function setDefaultRequestParams($request): void
    {
        $post = $request->post();

        if (!$post->has('mallSno')) {
            $post->set('mallSno', '');
        }
        if (!$post->get('sendMode', '') != 'mail') {
            $post->set('mallSno', DEFAULT_MALL_NUMBER);
        }

        if (!$post->has('maillingFl')) {
            $post->set('maillingFl', 'y');
        }
        if (!$post->has('smsFl')) {
            $post->set('smsFl', 'y');
        }

        $post->set('sendMode', $request->get()->get('sendMode'));
    }
}
