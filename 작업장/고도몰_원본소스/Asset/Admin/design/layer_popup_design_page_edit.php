<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */
?>
<style>
    /* 팝업에서 부모에 명시적 높이가 없어 height:100% 미동작 → viewport 기준 높이 지정 */
    #design-code { height: calc(100vh - 69px) !important; }
</style>
<div id="menu">
    <!-- close/open -->
    <div class="js-adminmenu-toggle"></div>
    <div class="js-listgroup-toggle"></div>
    <!-- //close/open -->

    <div class="menu-header design">
        <h2>HTML 편집</h2>
    </div>
    <div class="panel">
        <div class="panel-heading menu-icon-minus">디자인 설정</div>
        <ul class="list-group">
            <li class="list-group-item active">
                <a href="layer_popup_design_page_edit.php?designPageId=default&skinType=<?= $skinType ?>&skinCode=<?= $skinCode ?>">
                    디자인 스킨 레이아웃 설정
                </a>
            </li>
        </ul>
        <!-- 파일트리 -->
        <div class="tree_panel file_tree" data-popup-mode="withMenu"></div>
    </div>
</div>
<div id="content" class="row">
    <div class="col-xs-12 ncua-content-wrap js-ncds-content-wrap">
        <?php include $designPageEditSection; ?>
    </div>
</div>

<script>
$(window).on('load', function() {
    // 기존 designTree.js - location_move 함수 오버라이드 (이동 페이지 경로 수정)
    if (typeof myDesignTree !== 'undefined') {
        myDesignTree.location_move = function() {
            var NODE = this.treeObj.selected;
            var path = 'layer_popup_design_page_edit.php?' + $(NODE).attr('linkType') + 'Id=' + $(NODE).attr('linkId') + '&skinType=<?= $skinType ?>&skinCode=<?= $skinCode ?>';
            document.location.href = path;
        };
    }

    var popupBase = 'layer_popup_design_page_edit.php?skinType=<?= $skinType ?>&skinCode=<?= $skinCode ?>';

    // 레이아웃 맵 내 편집하기 링크 오버라이드 (design_page_edit.php → layer_popup_design_page_edit.php)
    $('#design_page_map a[href*="design_page_edit.php"]').each(function() {
        var designPageId = new URL(this.href).searchParams.get('designPageId');
        if (designPageId) {
            this.href = popupBase + '&designPageId=' + designPageId;
        }
    });

    // designPageMove 함수 오버라이드 (select 드롭다운 편집하기 — 원본 래핑)
    var _originDesignPageMove = window.designPageMove;
    window.designPageMove = function(sobj, skinType) {
        var selected_path = sobj.attr('path');
        if (selected_path && selected_path !== 'noprint') {
            document.location.href = popupBase + '&designPageId=' + selected_path;
        } else {
            _originDesignPageMove.apply(this, arguments);
        }
    };

    // 디자인 페이지 삭제 후 리다이렉트 오버라이드 (design_skin_list.php → 팝업 default 페이지)
    window._designDeleteRedirectUrl = popupBase + '&designPageId=default';
});
</script>
