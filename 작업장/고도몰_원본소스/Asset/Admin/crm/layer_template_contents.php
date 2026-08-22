<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/crm-common.css') ?>"
      rel="stylesheet"/>
<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-preview.css') ?>"
      rel="stylesheet"/>
<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/component/card.css') ?>"
      rel="stylesheet"/>
<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'script/slider/slick/slick.css') ?>"
      rel="stylesheet"/>

<?php
$smsData = $response->getSms();
$kakaoCloud = $response->getCloud();
$kakaoBizm = $response->getBizm();
$myappData = $response->getMyapp();
?>


<div class="mobile-preview-wrap mobile-preview-wrap-basic">
    <div class="layer-preview-content-wrap">
        <?php
        $templates = ['sms' => $layerTemplateContentsSms,
            'kakao' => $layerTemplateContentsKakao,
            'myapp' => $layerTemplateContentsMyapp,
        ];
        include $templates[$request->getSendMethod()];
        ?>
    </div
</div>
