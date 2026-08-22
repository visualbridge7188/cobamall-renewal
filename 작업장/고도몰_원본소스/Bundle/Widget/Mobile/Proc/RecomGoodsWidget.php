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
namespace Bundle\Widget\Mobile\Proc;

use Component\RegularDelivery\RegularGoods\RegularGoods;
use App;

/**
 * Class MypageQnaController
 *
 * @package Bundle\Controller\Front\Outline
 * @author
 */

class RecomGoodsWidget extends \Widget\Mobile\Widget
{

    public function index()
    {

        $recom = App::load('\\Component\\Goods\\RecommendGoods');
        $regularGoods = \App::getInstance(RegularGoods::class);

        $getData = $recom->getGoodsDataUser('mobile');
        $data = $getData['data'];
        if (!empty($data['goodsNo'])) {
            $regularGoodsData = $regularGoods->getRegularPriceInfo($getData['config']['displayField'], $data['goodsNo']);

            if ($regularGoodsData['useRegularPriceFl']) {
                $data['deliveryType'] = $regularGoodsData['deliveryType'];
                $data['regularPrice'] = $regularGoodsData['regularPrice'];
                $data['applyStatus'] = $regularGoodsData['applyStatus'];
            }
            $data['useRegularPriceFl'] = $regularGoodsData['useRegularPriceFl'];
        } else {
            $data['useRegularPriceFl'] = false;
        }

        //품절상품 설정
        $soldoutDisplay = gd_policy('soldout.mobile');

        $this->setData('config', $getData['config']);
        $this->setData('data', $data);
        $this->setData('isMobile', \Request::isMobileDevice());
        $this->setData('soldoutDisplay', gd_isset($soldoutDisplay));

    }
}
