<?php
/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link      http://www.godo.co.kr
 */

namespace Bundle\Controller\Admin\Policy;


use Framework\Enum\ExternalUrl;
use Origin\Service\Member\ManagerLoginService;

class LayerConnectDomainController extends \Controller\Admin\Controller
{
    public function index()
    {
        /** @var ManagerLoginService $managerLoginService */
        $managerLoginService = \App::getInstance(ManagerLoginService::class);
        $isOauthSuperManagerLoggedIn = $managerLoginService->isOauthSuperManagerLoggedIn();
        $this->setData('isOauthSuperManagerLoggedIn', $isOauthSuperManagerLoggedIn);

        $domainChangeUrlParam = http_build_query([
            'pageKey' => \Globals::get('gLicense.godosno'),
            'mode' => 'domain',
        ]);
        $this->setData('domainChangeUrl', ExternalUrl::NHN_COMMERCE_MYGODO->getUrl("/myGodo_Domain_change.php?$domainChangeUrlParam"));
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
