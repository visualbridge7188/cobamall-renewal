<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Component\Page\Page;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\SkinUtils;

class MailLogListController extends \Controller\Admin\Controller
{
    public function index()
    {
        $request = \App::getInstance('request');
        try {
            if ($request->get()->has('regdt') === false) {
                $request->get()->set('regdt', DateTimeUtils::getBetweenDateString('-6days'));
            }
            /** @var \Bundle\Controller\Admin\Controller $this */
            $this->callMenu('crm', 'mail', 'log');

            $page = $request->get()->get('page', 1);
            $pageNum = $request->get()->get('pageNum', 10);

            /**  @var  \Bundle\Component\Mail\MailLog $mailLog */
            $mailLog = \App::load('\\Component\\Mail\\MailLog');
            $requestGetParams = $request->get()->all();
            $logData = $mailLog->getLogList($requestGetParams, $page, $pageNum);

            $page = new Page($page, $mailLog->foundRowsByLogList(), null, $pageNum);
            $page->setPage();
            $page->setUrl($request->getQueryString());

            $checked = SkinUtils::setChecked(['sendType'], $requestGetParams);

            $this->setData('requestGetParams', $requestGetParams);
            $this->setData('searchKindASelectBox', \Component\Member\Member::getSearchKindASelectBox());
            $this->setData('data', $logData);
            $this->setData('page', $page);
            $this->setData('checked', $checked);

            $this->addScript(['member.js']);
        } catch (\Exception $e) {
            throw new AlertBackException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
