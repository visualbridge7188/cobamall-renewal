<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-preview.css') ?>" rel="stylesheet"/>

<div class="mobile-preview-wrap">
    <?php if (!empty($previewMessage)): ?>
        <p class="mobile-preview-info"><?= $previewMessage ?></p>
    <?php endif; ?>
    <section class="mobile-preview mobile-preview--alim">
        <span class="ncua-profile ncua-profile--float ncua-profile--kakao">채널명</span>

        <section class="ncua-prev-alim<?= !$isTemplateExists ? ' ncua-prev-alim--del-template' : '' ?>">
            <h2 class="ncua-prev-alim__title">알림톡 도착</h2>
            <section class="ncua-prev-alim__content">
            <!-- 강조형 텍스트 -->
            <?php if (!empty($templateTitle)): ?>
                <header class="ncua-prev-alim__em">
                    <?php if (!empty($templateSubTitle)): ?>
                    <h3 class="ncua-prev-alim__em-title"><?= $templateSubTitle ?></h3>
                    <?php endif; ?>
                    <span class="ncua-prev-alim__em-title-sub"><?= $templateTitle ?></span>
                </header>
            <?php endif; ?>

            <!-- 이미지 -->
            <?php if (!$isTemplateExists): ?>
                <figure class="ncua-prev-alim__media">
                    <img src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'img/crm/alim-talk-not-found-img.png') ?>" alt="">
                </figure>
            <?php elseif (!empty($templateImageUrl)): ?>
                <figure class="ncua-prev-alim__media">
                    <img src="<?= $templateImageUrl ?>" alt="">
                </figure>
            <?php endif; ?>

            <!-- 아이템 리스트 -->
            <?php if (!empty($templateItems)): ?>
                <section class="ncua-prev-alim__list">
                    <?php if (!empty($templateHeader)): ?>
                        <h3 class="ncua-prev-alim__list-title"><?= $templateHeader ?></h3>
                    <?php endif; ?>
                    <?php if (!empty($templateItemHighlight)): ?>
                        <div class="ncua-prev-alim__highlight">
                            <div>
                                <?php if (!empty($templateItemHighlight->getTitle())): ?>
                                    <p class="ncua-prev-alim__highlight-title"><?= $templateItemHighlight->getTitle() ?></p>
                                <?php endif; ?>
                                <?= $templateItemHighlight->getDescription() ?>
                            </div>
                            <img src="<?= $templateItemHighlight->getImageUrl() ?>" alt="">
                        </div>
                    <?php endif; ?>
                    <div class="ncua-prev-alim-table">
                        <table>
                            <tbody>
                                <?php foreach ($templateItems as $templateItem): ?>
                                    <tr>
                                        <?php if (!empty($templateItem->getTitle())): ?>
                                            <th><?= $templateItem->getTitle() ?></th>
                                        <?php endif; ?>
                                        <?php if (!empty($templateItem->getDescription())): ?>
                                            <td><?= $templateItem->getDescription() ?></td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <?php if (!empty($templateItemSummary)): ?>
                                <tfoot>
                                    <tr>
                                        <?php if (!empty($templateItemSummary->getTitle())): ?>
                                            <th><?= $templateItemSummary->getTitle() ?></th>
                                        <?php endif; ?>
                                        <?php if (!empty($templateItemSummary->getDescription())): ?>
                                            <td><?= $templateItemSummary->getDescription() ?></td>
                                        <?php endif; ?>
                                    </tr>
                                </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                </section>
            <?php endif; ?>

            <!-- 내용 -->
                <?= nl2br(gd_htmlspecialchars_stripslashes($templateContent)) ?>
                <?php if (!empty($templateExtra)): ?>
                    <p class="ncua-prev-alim__info"><?= nl2br(gd_htmlspecialchars_stripslashes($templateExtra)) ?></p>
                <?php endif; ?>
                <?php if ($hasAddChannelButton): ?>
                    <p class="ncua-prev-alim__info">채널 추가하고 이 채널의 마케팅 메시지 등을 카카오톡으로 받기</p>
                <?php endif; ?>
            </section>

            <!-- 버튼 -->
            <?php if (!empty($templateButtons)): ?>
                <footer class="ncua-prev-alim__footer">
                    <?php foreach ($templateButtons as $templateButton): ?>
                        <button class="ncua-prev__btn<?= $templateButton->getType()->name === 'ADD_CHANNEL' ? ' ncua-prev-alim__btn--add-channel' : '' ?>" type="button"><?= $templateButton->getName() ?></button>
                    <?php endforeach; ?>
                </footer>
            <?php endif; ?>
        </section>
    </section>
    <?php if (!empty($templateButtons)): ?>
        <section class="mobile-preview-table">
            <div class="ncua-table ncua-table--vertical">
                <table>
                    <tbody>
                        <?php foreach ($templateButtons as $templateButton): ?>
                            <tr>
                                <th><div><?= $templateButton->getName() ?> 타입</div></th>
                                <td><div class="ncua-left-align"><?= $templateButton->getType()->value ?></div></td>
                            </tr>
                            <tr>
                                <th><div><?= $templateButton->getName() ?> 링크</div></th>
                                <td><div class="ncua-left-align"><?= $templateButton->getMobileUrl() ?></div></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endif; ?>
</div>
