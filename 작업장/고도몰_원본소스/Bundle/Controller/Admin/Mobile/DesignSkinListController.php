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

namespace Bundle\Controller\Admin\Mobile;

use Framework\Debug\Exception\AlertOnlyException;

/**
 * 디자인 스킨 선택
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class DesignSkinListController extends \Controller\Admin\Design\DesignSkinListController
{
    /**
     * index
     *
     * @throws AlertOnlyException
     */
    public function index()
    {
        parent::index();

        $request = \App::getInstance('request');

        //--- 관리자 디자인 템플릿
        $this->callMenu('mobile', 'designConf', 'skinList');

        $this->getView()->setDefine('layoutMenu', 'menu_design_mobile.php');
        $this->getView()->setDefine('layoutContent', 'design/' . $request->getFileUri());
    }
}
