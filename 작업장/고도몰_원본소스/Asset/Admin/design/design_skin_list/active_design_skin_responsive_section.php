<section class="ncua-card active-design-skin-section max-width-center">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">현재 설정된 디자인 스킨</h4>
    </header>

    <section class="ncua-card__body">
        <div class="ncua-flex ncua-flex-gap">
            <div class="js-active-skin-card active_skin--responsive--card" data-skin-code="<?= $skinConf['front']['liveInfo']['skin_code'] ?>" data-skin-type="responsive" data-skin-sno="<?= $skinConf['front']['liveInfo']['skin_sno'] ?>">
                <div class="active_skin--thumb">
                    <div class="active_skin--thumb--preview">
                        <img class="active_skin--thumb--preview-pc" src="<?= $skinConf['front']['liveInfo']['skin_cover_front'] ?>" alt="반응형 스킨 PC"/>
                        <img class="active_skin--thumb--preview-mobile" src="<?= $skinConf['front']['liveInfo']['skin_cover_mobile'] ?>" alt="반응형 스킨 모바일"/>
                    </div>

                    <div class="active_skin--thumb--overlay ncua-flex ncua-align-center ncua-align-items-center">
                        <a href="<?= $skinConf['front']['previewUrl'] ?>" target="_blank" type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray js-skin-preview">
                            <span class="ncua-btn__label">미리보기</span>
                        </a>
                    </div>
                </div>

                <div class="active_skin--info">
                    <div class="active_skin--info--title">
                        <?= $skinConf['front']['liveInfo']['skin_name'] ?>
                        <span class="ncua-badge ncua-badge--pill-outline ncua-badge--success ncua-badge--xs">
                            <span class="ncua-dot ncua-dot--success ncua-dot--sm"></span>
                            <span class="ncua-badge__label">사용 스킨</span>
                        </span>
                    </div>
                    <div class="active_skin--info--desc">
                        <span class="active_skin--info-code"><?= $skinConf['front']['liveInfo']['skin_code'] ?></span>
                        <span class="active_skin--info-time"><?= $skinConf['front']['liveInfo']['skin_last_edit_date'] ?></span>
                    </div>
                    <div class="active_skin--info--btns ncua-flex ncua-gap-4 ncua-align-right">
                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-skin-edit">
                            <span class="ncua-btn__label">디자인 에디터로 편집하기</span>
                        </button>
                        <div class="ncua-dropdown ncua-dropdown--responsive"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</section>
