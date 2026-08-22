<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\PopupSelectMembers;

use Bundle\Component\Page\Page;
use Component\Member\Group\Util;
use Component\Member\Member;
use Framework\Http\Request;

class LayerSelectMembersResultController extends \Controller\Admin\Controller
{
    const SORT_LIST = [
        'entryDt desc' => '회원가입일 내림차순',
        'entryDt asc' => '회원가입일 오름차순',
        'lastLoginDt desc' => '최종로그인 내림차순',
        'lastLoginDt asc' => '최종로그인 오름차순',
        'loginCnt desc' => '방문횟수 내림차순',
        'loginCnt asc' => '방문횟수 오름차순',
        'memNm desc' => '이름 내림차순',
        'memNm asc' => '이름 오름차순',
        'memId desc' => '아이디 내림차순',
        'memId asc' => '아이디 오름차순',
        'mileage desc' => '마일리지 내림차순',
        'mileage asc' => '마일리지 오름차순',
        'saleAmt desc' => '주문금액 내림차순',
        'saleAmt asc' => '주문금액 오름차순',
    ];

    const DEFAULT_PAGE = 1;
    const DEFAULT_PER_PAGE = 10;

    public function index()
    {
        /** @var Request $request */
        $request = \App::getInstance('request');
        $postData = $request->post()->toArray();
        $postData['sendMode'] = $request->request()->get('sendMode');
        if (in_array($postData['key'], ['company', 'busiNo'])) $postData['searchKind'] = 'fullLikeSearch';

        /* @var \Bundle\Component\Member\Member $memberComponent */
        $memberComponent = \App::load(Member::class);

        $members = $memberComponent->listsWithCoupon($postData, $postData['page'] ?? self::DEFAULT_PAGE, $postData['pageSize'] ?? self::DEFAULT_PER_PAGE);
        $searchCount = $memberComponent->foundRowsByListsWithCoupon($postData);
        $totalCount = $memberComponent->foundRowsByListsWithCoupon(['sendMode' => $postData['sendMode'], 'mallSno' => $postData['mallSno']]); // sendMode만 포함하여 전체 회원수 조회

        $page = new Page($postData['page'] ?? self::DEFAULT_PAGE, $searchCount, $totalCount, $postData['pageSize'] ?? self::DEFAULT_PER_PAGE);

        $sysIcons = [
            'payco' => ['icon' => PATH_ADMIN_GD_SHARE . 'ncds/image/payco_logo.png', 'name' => '페이코'],
            'facebook' => ['icon' => PATH_ADMIN_GD_SHARE . 'ncds/image/facebook_logo.png', 'name' => '페이스북'],
            'naver' => ['icon' => PATH_ADMIN_GD_SHARE . 'ncds/image/naver_logo.png', 'name' => '네이버'],
            'kakao' => ['icon' => PATH_ADMIN_GD_SHARE . 'ncds/image/kakao_logo.png', 'name' => '카카오'],
            'wonder' => ['icon' => PATH_ADMIN_GD_SHARE . 'ncds/image/wemakeprice_logo.png', 'name' => '위메프'],
            'apple' => ['icon' => PATH_ADMIN_GD_SHARE . 'ncds/image/apple_logo.png', 'name' => '애플'],
            'google' => ['icon' => PATH_ADMIN_GD_SHARE . 'ncds/image/google_logo.png', 'name' => '구글'],
        ];

        $this->setData('searchFormData', $postData);
        $this->setData('snsIcons', $sysIcons);
        $this->setData('sortType', self::SORT_LIST);
        $this->setData('members', $members);
        $this->setData('groups', Util::getGroupName());
        $this->setData('page', $page);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
