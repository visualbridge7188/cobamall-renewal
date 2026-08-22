<?php
/**
 * 개발소스관리 NCDS 레이아웃
 *
 * layout_development_popup.php의 NCDS 전환 버전.
 * NCDS CDN CSS/JS를 로드하고, LNB는 development/_lnb.php를 사용한다.
 *
 * @copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 */

include UserFilePath::adminSkin('head.php');
?>
<!-- 개발소스관리 전용 공통 스타일(디자인 QA §1) — 이 레이아웃에서만 로드하여 다른 관리자 페이지(GNB/LNB)에 영향을 주지 않도록 스코핑 -->
<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/development/dev-source-common.css')?>" rel="stylesheet"/>
<style type="text/css">
    .staging-top-banner {
        background: var(--orange-50, #fefaf5);
        color: var(--orange-600, #b93815);
        padding: 8px 16px;
        text-align: center;
        font-family: var(--font-family-ncua-commerce-sans);
        font-size: 14px;
        font-weight: 700;
        line-height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-bottom: 1px solid var(--orange-300, #f7b27a);
    }
    .staging-top-banner svg {
        flex-shrink: 0;
    }
    body.has-staging-banner {
        --staging-banner-height: 30px;
    }
</style>
<body class="<?php echo $adminBodyClass; ?> layout-basic ncds<?= Globals::get('gLicense.isDevShop') ? ' has-staging-banner' : '' ?>">
    <div id="container-wrap" class="container-fluid">
        <div id="container" class="row">
            <?php if (Globals::get('gLicense.isDevShop')): ?>
            <div class="staging-top-banner">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 16h2v2h-2v-2zm0-6h2v4h-2v-4z"/></svg>
                <?= __('현재 스테이징 서버 접속 중입니다.') ?>
            </div>
            <?php endif; ?>
            <div id="header" class="col-xs-12">
                <?php if (isset($layoutHeader)) { include($layoutHeader); } ?>
                <div id="panel_popupPanel"></div>
                <div id="panel_popupCos_modal"></div>
                <div id="panel_popupCos_layer"></div>
            </div>


            <div id="content-wrap">
                <div id="menu">
                    <?php include($layoutMenu); ?>
                </div>
                <div id="content" class="row">
                    <div class="col-xs-12 ncua-content-wrap js-ncds-content-wrap">
                        <?php include($layoutContent); ?>
                        <?php include($layoutHelp); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="gnbTopAnchor">
        <a href="#top"><img src="<?=PATH_ADMIN_GD_SHARE?>img/btn_gnb_top.png"></a>
    </div>

    <iframe name="ifrmProcess" src="/blank.php" width="100%" height="200" class="<?=App::isDevelopment() === true ? 'display-block' : 'display-none'?>"></iframe>
<script type="text/javascript">
    $(function () {
        adminPanelApiAjax('<?php echo $manualData['menuCode'];?>', '<?php echo $manualData['menuKey'];?>', '<?php echo $manualData['menuFile'];?>');
        <?= gd_isset($menuAccessAuth); ?>

        $(document).on("click", "a[href=#top]", function (e) {
            $('html body').animate({scrollTop: 0}, 'fast');
        });

        $(window).scroll(function () {
            if ($(window).height() < $(document).height()) {
                if ($(window).scrollTop() >= 1) {
                    $("#gnbTopAnchor").slideDown(150);
                } else {
                    $("#gnbTopAnchor").slideUp(100);
                }
            }
        });
    });
</script>
</body>
</html>
