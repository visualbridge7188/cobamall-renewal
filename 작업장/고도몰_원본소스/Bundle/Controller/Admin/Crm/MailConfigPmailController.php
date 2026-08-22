<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */
namespace Bundle\Controller\Admin\Crm;

use Framework\Utility\SkinUtils;

class MailConfigPmailController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'mail', 'configPmail');

        /** @var  \Bundle\Component\Mail\Pmail $pMail */
        $pMail = \App::load('\\Component\\Mail\\Pmail');

        $pMailConfig = $pMail->getMailConfigPmailWithLicense();
        $mailDomainSelectBox = SkinUtils::makeSelectBoxByMailDomain('mail_site', 'mail_site', null, $pMailConfig['email'][1], '직접입력');

        $conf = $pMail->getMailConfigPmail();

        $this->setData('mailDomainSelectBox', $mailDomainSelectBox);
        $this->setData('pMailConfig', $pMailConfig);
        $this->addScript(['member.js']);
    }
}
