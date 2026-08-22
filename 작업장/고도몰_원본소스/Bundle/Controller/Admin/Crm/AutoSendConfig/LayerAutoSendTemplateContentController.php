<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\AutoSendConfig;

use Bundle\Component\Page\Page;
use Exception;
use Origin\Enum\Crm\Message\MyappPushTemplateCategory;
use Origin\Enum\Crm\Message\SmsTemplateCategory;
use Origin\Repository\Member\Myapp\MyappPushRepository;
use Origin\Repository\Member\Sms\SmsContentsRepository;
use Request;

class LayerAutoSendTemplateContentController extends \Controller\Admin\Controller
{
    private const PER_PAGE = 10;

    public function index()
    {
        $request = Request::post()->toArray();
        $channel = $request['channel'];
        $categoryValue = $request['category'] ?? '';
        $keyword = $request['keyword'] ?? '';
        $page = max(1, (int)($request['page'] ?? 1));
        $offset = ($page - 1) * self::PER_PAGE;

        switch ($channel) {
            case 'MYAPP_PUSH':
                $category = MyappPushTemplateCategory::tryFrom($categoryValue);
                $repository = new MyappPushRepository();
                $totalCount = $repository->searchCount($category, $keyword);
                $templates = array_map(function ($template) {
                    return [
                      'sno' => $template['pushSno'],
                      'subject' => $template['templateName'] ?? '',
                      'contents' => $template['pushContent'],
                      'title' => $template['pushSubject'] ?? '',
                      'myappImage' => $template['pushImage'],
                      'url' => $template['pushUrl'] ?? '',
                    ];
                }, $repository->searchPaginated($category, $keyword, self::PER_PAGE, $offset));
                $categories = MyappPushTemplateCategory::getFindOptions();
                break;
            case 'SMS':
                $category = SmsTemplateCategory::tryFrom($categoryValue);
                $repository = new SmsContentsRepository();
                $totalCount = $repository->searchCount($category, $keyword);
                $templates = $repository->searchPaginated($category, $keyword, self::PER_PAGE, $offset);
                $categories = SmsTemplateCategory::getFindOptions();
                break;
            default:
                throw new Exception();
        }

        $page = new Page($page, $totalCount, $totalCount, self::PER_PAGE);

        $templates = array_map(function ($template) {
            $template['subject'] = nl2br(gd_htmlspecialchars_stripslashes($template['subject']));
            $template['contents'] = gd_htmlspecialchars_stripslashes($template['contents']);
            return $template;
        }, $templates);

        $this->setData('channel', $channel);
        $this->setData('templates', $templates);
        $this->setData('categories', $categories);
        $this->setData('page', $page);

        $this->getView()->setDefine('layout', 'layout_layer.php');
        $this->getView()->setPageName('crm/auto_send_config/layer_auto_send_template_content.php');
    }
}
