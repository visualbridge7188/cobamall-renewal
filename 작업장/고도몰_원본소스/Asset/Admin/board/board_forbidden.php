<article class="ncua-content">
    <form id="frmForbidden" action="/board/board_ps.php" method="post" target="ifrmProcess">
        <input type="hidden" name="mode" value="forbidden" />

        <header class="page-header ncua-page-header js-affix">
            <h3 class="ncua-help-manual"><?php echo end($naviMenu->location);?></h3>
            <span class="ncua-page-header__actions">
                <button type="submit" class="ncua-btn ncua-btn--md ncua-btn--primary">저장</button>
            </span>
        </header>

        <section class="ncua-card">
            <header class="ncua-card__header">
                <h4 class="ncua-card__title">게시판 금칙어</h4>
            </header>
            <section class="ncua-card__body">
                <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs">
                    <textarea name="word" class="ncua-input__textarea" placeholder="예) 대출, 바카라, 로또 등과 같이 단어별 구분은 ','(콤마)로 등록하세요."><?php echo gd_isset($forbidden)?></textarea>
                </div>
            </section>
        </section>
    </form>
</article>

<script type="module">
    const code = '251125001';
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
        });
    }
</script>
