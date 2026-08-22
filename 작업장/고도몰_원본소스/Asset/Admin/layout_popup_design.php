<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */
/**
 * 디자인 팝업용 레이아웃 (헤더/푸터 없이 메뉴 + 콘텐츠)
 */
?>
<?php include UserFilePath::adminSkin('head.php'); ?>
<style>
    body.layout-design:not(.sitemap):not(.cache) #container-wrap #content.row { margin-top: 0; }
    body.layout-design .ncua-content-wrap .page-header { height: 64px; }
</style>
<body class="<?= $adminBodyClass ?> layout-design">

<div id="container-wrap" class="container-fluid">
    <div id="container" class="row">
        <div id="panel_popupPanel"></div>
        <div id="panel_popupCos_modal"></div>
        <div id="panel_popupCos_layer"></div>

        <div id="content-wrap">
            <?php include($layoutContent); ?>
        </div>
    </div>
</div>

<iframe name="ifrmProcess" src="/blank.php" width="100%" height="200"
        class="<?= App::isDevelopment() === true ? 'display-block' : 'display-none' ?>"></iframe>
<script type="text/javascript">
    $(function () {
        adminPanelApiAjax('<?= $manualData['menuCode'] ?>', '<?= $manualData['menuKey'] ?>', '<?= $manualData['menuFile'] ?>');
        <?= gd_isset($menuAccessAuth) ?>
        <?= $functionAuth ?>
    });
</script>
</body>
</html>
