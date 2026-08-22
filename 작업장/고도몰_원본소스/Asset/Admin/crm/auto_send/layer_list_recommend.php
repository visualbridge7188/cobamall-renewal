<section class="ncua-card">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">자동 알림 추천</h4>
        <button type="button" class="ncua-card__toggle <?= $shouldExpandRecommend ? 'opened' : '' ?>" id="recommend-toggle-btn"></button>
    </header>
    <section id="auto-list-recommend" class="ncua-card__body auto-recommend-layout" <?= !$shouldExpandRecommend ? 'style="display:none"' : '' ?>>
        <?php foreach ($recommendAutoSends as $code => $autoSend): ?>
            <div class="auto-recommend-item">
                <div class="auto-recommend-item-title"><?= $autoSend['title']; ?></div>
                <p class="auto-recommend-item-description">
                    <?= $autoSend['description']; ?>
                </p>
                <div class="auto-recommend-item-actions">
                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" onclick="preview_auto_send('<?= $code ?>');">
                        <span class="ncua-btn__label">미리보기</span>
                    </button>
                    <div class="ncua-switch ncua-switch--xs">
                        <label class="ncua-switch__option ncua-switch__option--left <?= $autoSend['shouldAutoSend'] === 'y' ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                            <input class="auto-send-radio ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" data-code="<?= $code ?>" name="sendStatus[<?= $code ?>]" <?= $autoSend['shouldAutoSend'] === 'y' ? 'checked' : '' ?> />
                            <span class="ncua-switch__label">발송함</span>
                        </label>
                        <label class="ncua-switch__option ncua-switch__option--right <?= $autoSend['shouldAutoSend'] === 'n' ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                            <input class="auto-send-radio ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" data-code="<?= $code ?>" name="sendStatus[<?= $code ?>]" <?= $autoSend['shouldAutoSend'] === 'n' ? 'checked' : '' ?> />
                            <span class="ncua-switch__label">발송안함</span>
                        </label>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
</section>
<script>
    document.getElementById('recommend-toggle-btn')?.addEventListener('click', function () {
        // card-toggle.js가 opened 클래스를 토글하기 전에 실행되므로, 현재 opened → 접힘 예정
        var willCollapse = this.classList.contains('opened');
        $.post('./auto_send_ps.php', {
            mode: 'saveRecommendCollapseState',
            collapsed: willCollapse ? 'true' : 'false'
        });
    });
</script>
