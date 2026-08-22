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
namespace Bundle\Controller\Admin\Provider\Board;

use Request;

class ScmBoardRegisterController extends \Controller\Admin\Scm\ScmBoardRegisterController
{
    public function index()
    {
        parent::index();

        // NCDS 템플릿 설정
        $data = $this->getData('data');
        $data['mode'] = Request::get()->get('mode');
        $this->setData('data', $data);
        $this->setData('adminBodySubClass', 'ncds');
        $this->getView()->setDefine('articleRegister', 'provider/scm/scm_board_register/article_register.php');
        $this->getView()->setPageName('provider/scm/scm_board_register.php');
    }
}
