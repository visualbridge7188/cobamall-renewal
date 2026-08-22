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
 * @link http://www.godo.co.kr
 */

namespace Bundle\Controller\Admin\Share;

use Globals;
use Request;

class LayerRegularGoodsUpdateFormController extends \Controller\Admin\Controller
{

    public function index()
    {
        $postValue = Request::get()->toArray();

        $this->setData('mode', $postValue['mode']);
        $this->setData('dataForm', $postValue['dataForm']);

        // --- 관리자 디자인 템플릿
        $this->getView()->setDefine('layout', 'layout_layer.php');

        $this->getView()->setPageName('share/layer_regular_goods_update_form.php');
    }
}
