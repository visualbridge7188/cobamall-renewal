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

namespace Bundle\Controller\Admin\Mobile;
use Framework\Enum\ExternalUrl;
use Component\Design\SkinDesign;
use Component\Design\DesignPopup;
use Component\Page\Page;
use Globals;

/**
 * 팝업 리스트
 * @author Bag YJ <kookoo135@godo.co.kr>
 */
class PopupListController extends \Bundle\Controller\Admin\Design\PopupListController
{
    protected $menuType;
    /**
     * index
     *
     */
    public function index()
    {
        $this->menuType = 'mobile';
        parent::index();
        $this->getView()->setDefine('layoutContent', 'design/' . \Request::getFileUri());
        $guideUrl = ExternalUrl::BEGINNER_DESIGN_GUIDE_URL->getUrl('/design/banner_setting/mobile#step-3');

        $this->setData('skinType', $this->menuType);
        $this->setData('guideUrl', $guideUrl);
    }
}
