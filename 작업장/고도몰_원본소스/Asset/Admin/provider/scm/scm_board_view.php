<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/scm-board-view.css')?>" rel="stylesheet"/>

<article class="ncua-content scm-board-view">
    <header class="page-header js-affix ncua-page-header">
        <h3><button type="button" class="ncua-btn ncua-btn--md only-icon ncua-btn--secondary-gray ncua-history-back js-btn-back">뒤로가기</button><?= end($naviMenu->location); ?></h3>
        <div class="ncua-page-header__actions">
            <a href="scm_board_list.php?<?= $queryString ?>" class="ncua-btn ncua-btn--md ncua-btn--secondary-gray">목록</a>
        </div>
    </header>
    <?php include $articleView ?>
</article>
<script>
    function articleDelete(sno) {
        NCDSConfirm({message: '정말 삭제하시겠습니까?', callback: function (result) {
            if(result) {
                ifrmProcess.location.href="scm_board_ps.php?mode=delete&sno="+sno;
            }
        }});

    }
    document.querySelector('.js-btn-back').addEventListener('click', function() {
        history.back();
    });
</script>
