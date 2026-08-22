<?php

namespace Bundle\Controller\Admin\Goods;

use Framework\Security\Token;
use Framework\Utility\ImageUtils;
use Globals;
use Session;

/**
 * 간편상품등록 컨트롤러
 */
class SimpleGoodsRegisterController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('goods', 'goods', 'simpleGoodsRegister');

        // 이미지 정책/저장소 설정
        $conf['image'] = gd_policy('goods.image');

        foreach ($conf['image'] as $k => $v) {
            foreach ($v as $key => $value) {
                if (stripos($key, 'size') === 0) {
                    if ($conf['image']['imageType'] === 'fixed') {
                        $conf['image'][$k]['fixed' . $key] = [$value, $conf['image'][$k]['h' . $key]];
                        unset($conf['image'][$k]['h' . $key]);
                    }
                }
            }
            if (stripos($k, 'imageType') === 0) {
                $imageType = $conf['image']['imageType'];
                unset($conf['image']['imageType']);
            }
            unset($conf['image'][$k]['sequenceNumber']);
        }

        $tmpStorageConf = gd_policy('basic.storage');
        $defaultImageStorage = '';

        // 기본 정보
        if (gd_is_provider() === true) {
            $scmAdmin = \App::load(\Component\Scm\ScmAdmin::class);
            $scmNo = Session::get('manager.scmNo');
            $scmData = $scmAdmin->getScm($scmNo);
        }

        if (empty($tmpStorageConf['storageDefault'])) {
            $tmpStorageConf['storageDefault'] = ['imageStorage0' => ['goods']];
        }
        foreach ($tmpStorageConf['storageDefault'] as $index => $item) {
            if (in_array('goods', $item)) {
                if (gd_isset($scmData['imageStorage'])) {
                    $defaultImageStorage = $scmData['imageStorage'];
                } else {
                    $defaultImageStorage = $tmpStorageConf['httpUrl'][$index];
                }
            }
        }
        unset($tmpStorageConf);

        ImageUtils::sortImageConf($conf['image']);

        // 카테고리 데이터
        $cate = \App::load('\\Component\\Category\\CategoryAdmin');
        $rootCateList = $cate->getRootCateListJson();

        $gGlobal = Globals::get('gGlobal');
        $mallData = [];
        foreach ($gGlobal['useMallList'] as $mall) {
            $mallData[] = [
                'sno'        => $mall['sno'],
                'mallName'   => $mall['mallName'],
                'domainFl'   => $mall['domainFl'],
                'standardFl' => $mall['standardFl'],
            ];
        }

        // 품절/배송 상태 코드
        $request = \App::getInstance('request');
        $mallSno = $request->get()->get('mallSno', 1);
        $codeComponent = \App::load('\\Component\\Code\\Code', $mallSno);

        $stockReason = $codeComponent->getGroupItems('05002');
        $stockReasonNew['y'] = $stockReason['05002001']; //정상은 코드 변경
        $stockReasonNew['n'] = $stockReason['05002002']; //품절은 코드 변경
        unset($stockReason['05002001']);
        unset($stockReason['05002002']);
        $stockReason = gd_array_merge($stockReasonNew, $stockReason);

        $deliveryReason = $codeComponent->getGroupItems('05003');
        $deliveryReasonNew['normal'] = $deliveryReason['05003001']; //정상은 코드 변경
        unset($deliveryReason['05003001']);
        $deliveryReason = gd_array_merge($deliveryReasonNew, $deliveryReason);

        $isProvider = gd_is_provider();

        $mobileConfig = gd_policy('mobile.config');
        $mobileShopFl = gd_isset($mobileConfig['mobileShopFl'], 'n');

        $this->setData('scmNo', gd_isset($scmNo, ''));
        $this->setData('isProvider', $isProvider);
        $this->setData('mobileShopFl', $mobileShopFl);
        $this->setData('cate', $cate);
        $this->setData('rootCateList', $rootCateList);
        $this->setData('mallData', json_encode($mallData, JSON_UNESCAPED_UNICODE));
        $this->setData('conf', $conf);
        $this->setData('imageType', gd_isset($imageType));
        $this->setData('defaultImageStorage', $defaultImageStorage);
        $this->setData('stockReason', $stockReason);
        $this->setData('deliveryReason', $deliveryReason);

        $this->setData('simpleGoodsToken', Token::generate('simpleGoodsToken'));

        // 공급사와 동일한 페이지 사용
        $this->getView()->setPageName('goods/simple_goods_register.php');
        $this->setData('adminBodySubClass', 'ncds');

    }
}
