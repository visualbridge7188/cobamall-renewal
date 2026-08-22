<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */
namespace Bundle\Controller\Admin\Crm;

use Request;

class LayerMailLogDetailController extends \Controller\Admin\Controller
{
    public function index()
    {
        /** @var \Bundle\Controller\Admin\Controller $this */

        /** @var \Bundle\Component\Mail\MailLog $mailLog */
        $mailLog = \App::load('\\Component\\Mail\\MailLog');
        $contents = $mailLog->getMailLog(Request::get()->get('sno'));

        $this->getView()->setDefine('layout', 'layout_layer.php');
        $this->getView()->setDefine('layoutContent', \Request::getDirectoryUri() . '/' . \Request::getFileUri());

        $this->setData('contents', $contents);
    }
}
