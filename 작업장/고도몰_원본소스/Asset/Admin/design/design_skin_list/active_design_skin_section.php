<section class="ncua-card active-design-skin-section max-width-center">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">현재 설정된 디자인 스킨</h4>
    </header>
    <section class="ncua-card__body">

        <div class="active_skin ncua-flex ncua-flex-gap">
            <!-- PC -->
            <div class="js-active-skin-card active_skin--card" data-skin-code="<?= $skinConf['front']['liveInfo']['skin_code'] ?>" data-skin-type="front" data-skin-sno="<?= $skinConf['front']['liveInfo']['skin_sno'] ?>" data-default-design-page-id="<?= $skinConf['front']['liveInfo']['default_design_page_id'] ?? 'default' ?>" data-skin-name="<?= $skinConf['front']['liveInfo']['skin_name'] ?>">
            <?php if (isset($skinConf['front']['liveInfo']['skin_code']) && !empty($skinConf['front']['liveInfo']['skin_code'])) { ?> 
                <div class="active_skin--thumb">
                    <img src="<?= $skinConf['front']['liveInfo']['skin_cover']; ?>" alt="<?= $skinConf['front']['liveInfo']['skin_name'] ?> 스킨"/>
                    <div class="active_skin--thumb--overlay ncua-flex ncua-align-center ncua-align-items-center">
                        <a href="<?= $skinConf['front']['previewUrl'] ?>" target="_blank" type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray js-skin-preview">
                            <span class="ncua-btn__label">미리보기</span>
                        </a>
                    </div>
                </div>

                <div class="active_skin--info">
                    <div class="active_skin--info--title">
                        <?= $skinConf['front']['liveInfo']['skin_name']; ?>
                        <span class="ncua-badge ncua-badge--pill-outline ncua-badge--success ncua-badge--xs">
                            <span class="ncua-dot ncua-dot--success ncua-dot--sm"></span>
                            <span class="ncua-badge__label">사용 스킨</span>
                        </span>
                    </div>
                    <div class="active_skin--info--desc">
                        <span class="active_skin--info-type">PC</span>
                        <span class="active_skin--info-code"><?= $skinConf['front']['liveInfo']['skin_code'] ?></span>
                        <span class="active_skin--info-time"><?= $skinConf['front']['liveInfo']['skin_last_edit_date'] ?></span>
                    </div>
                    <div class="active_skin--info--btns ncua-flex ncua-gap-4 ncua-align-right">
                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-skin-edit">
                            <span class="ncua-btn__label">HTML로 편집하기</span>
                        </button>
                        <?php if (isset($cacheUrl)) { ?>
                            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-skin-publish">
                                <span class="ncua-btn__label">갱신</span>
                            </button>
                        <?php } ?>
                        <div class="ncua-dropdown ncua-dropdown--pc"></div>
                    </div>
                </div>
                <?php } else { ?>
                    <p class="inactive_skin-card-description">현재 설정된 PC 스킨이 없습니다.</p>
                <?php } ?>
            </div>

            <!-- Mobile -->
            <div class="js-active-skin-card active_skin--card" data-skin-code="<?= $skinConf['mobile']['liveInfo']['skin_code']; ?>" data-skin-type="mobile" data-skin-sno="<?= $skinConf['mobile']['liveInfo']['skin_sno'] ?>" data-default-design-page-id="<?= $skinConf['mobile']['liveInfo']['default_design_page_id'] ?? 'default' ?>" data-skin-name="<?= $skinConf['mobile']['liveInfo']['skin_name'] ?>">
            <?php if (isset($skinConf['mobile']['liveInfo']['skin_code']) && !empty($skinConf['mobile']['liveInfo']['skin_code'])) { ?> 
                <div class="active_skin--thumb">
                    <img src="<?= $skinConf['mobile']['liveInfo']['skin_cover'] ?>" alt="<?= $skinConf['mobile']['liveInfo']['skin_name'] ?> 스킨"/>
                    <div class="active_skin--thumb--overlay ncua-flex ncua-align-center ncua-align-items-center">
                        <a href="<?= $skinConf['mobile']['previewUrl'] ?>" target="_blank" type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray js-skin-preview">
                            <span class="ncua-btn__label">미리보기</span>
                        </a>
                    </div>
                </div>

                <div class="active_skin--info">
                    <div class="active_skin--info--title">
                        <?= $skinConf['mobile']['liveInfo']['skin_name'] ?>
                        <span class="ncua-badge ncua-badge--pill-outline ncua-badge--success ncua-badge--xs">
                            <span class="ncua-dot ncua-dot--success ncua-dot--sm"></span>
                            <span class="ncua-badge__label">사용 스킨</span>
                        </span>
                    </div>
                    <div class="active_skin--info--desc">
                        <span class="active_skin--info-type">모바일</span>
                        <span class="active_skin--info-code"><?= $skinConf['mobile']['liveInfo']['skin_code'] ?></span>
                        <span class="active_skin--info-time"><?= $skinConf['mobile']['liveInfo']['skin_last_edit_date'] ?></span>
                    </div>
                    <div class="active_skin--info--btns ncua-flex ncua-gap-4 ncua-align-right">
                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-skin-edit">
                            <span class="ncua-btn__label">HTML로 편집하기</span>
                        </button>
                        <?php if (isset($cacheUrl)) { ?>
                            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-skin-publish">
                                <span class="ncua-btn__label">갱신</span>
                            </button>
                        <?php } ?>
                        <div class="ncua-dropdown ncua-dropdown--mobile"></div>
                    </div>
                </div>

                <?php } else { ?>
                    <p class="inactive_skin-card-description">현재 설정된 모바일 스킨이 없습니다.</p>
                <?php } ?>
            </div>
        </div>
    </section>
</section>
