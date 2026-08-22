<section class="mobile-preview mobile-preview--alim" data-component="kakao-notification-message-preview">
    <span class="ncua-profile ncua-profile--float ncua-profile--kakao">채널명</span>

    <section class="ncua-prev-alim">
        <h2 class="ncua-prev-alim__title">알림톡 도착</h2>
        <section class="ncua-prev-alim__content">
            <!-- header -->
            <?php if ($template['templateEmphasizeType'] === "TEXT") { ?>
                <header class="ncua-prev-alim__em">
                    <h3 class="ncua-prev-alim__em-title"><?= $template['templateSubtitle'] ?></h3>
                    <span class="ncua-prev-alim__em-title-sub"><?= $template['templateTitle'] ?></span>
                </header>
            <?php } ?>
            <!-- // header -->
            <!-- figure -->
            <?php if($template['templateEmphasizeType'] === "IMAGE" && !empty($template['templateImageUrl'])) { ?>
                <figure class="ncua-prev-alim__media">
                    <img src="<?= $template['templateImageUrl'] ?>" alt="<?= $template['templateImageName'] ?? "" ?>">
                </figure>
            <?php } ?>
            <!-- // figure -->
            <!-- list -->
            <?php if ($template['templateEmphasizeType'] === "ITEM_LIST") { ?>
                <section class="ncua-prev-alim__list">
                    <?php if (!empty($template['templateHeader'])) { ?>
                        <h3 class="ncua-prev-alim__list-title"><?= $template['templateHeader'] ?></h3>
                    <?php } ?>
                    <div class="ncua-prev-alim__highlight">
                        <?php if (!empty($template['templateItemHighlight']['title']) || !empty($template['templateItemHighlight']['description'])) { ?>
                            <div>
                                <p class="ncua-prev-alim__highlight-title"><?= $template['templateItemHighlight']['title'] ?></p>
                                <?= $template['templateItemHighlight']['description'] ?>
                            </div>
                        <?php } ?>
                        <?php if(!empty($template['templateItemHighlight']['imageUrl'])) { ?>
                            <img src="<?= $template['templateItemHighlight']['imageUrl'] ?>" alt="">
                        <?php } ?>
                    </div>
                    <div class="ncua-prev-alim-table">
                        <table>
                            <tbody>
                            <?php foreach($template['templateItems'] as $templateItem) { ?>
                                <tr>
                                    <th><?= $templateItem['title'] ?></th>
                                    <td><?= $templateItem['description'] ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                            <?php if (!empty($template['templateItemSummary']['title']) || !empty($template['templateItemSummary']['description'])) { ?>
                                <tfoot>
                                    <tr>
                                        <th><?= $template['templateItemSummary']['title'] ?? "" ?></th>
                                        <td><?= $template['templateItemSummary']['description'] ?? "" ?></td>
                                    </tr>
                                </tfoot>
                            <?php } ?>
                        </table>
                    </div>
                </section>
            <?php } ?>
            <!-- // list -->
            <!-- text -->
            <div class="alimtalk-contents-preview" style="white-space: pre-wrap;" ><?= $template['baseContents'] ?></div>
            <!-- // text -->
            <?php if (!empty($template['templateExtra'])) { ?>
                <p class="ncua-prev-alim__info"><?= $template['templateExtra'] ?></p>
            <?php } ?>
            <?php if ($hasAddChannelButton): ?>
                <p class="ncua-prev-alim__info">채널 추가하고 이 채널의 마케팅 메시지 등을 카카오톡으로 받기</p>
            <?php endif; ?>
        </section>
        <!-- buttons -->
        <footer class="ncua-prev-alim__footer">
        <?php foreach($template['templateButtons'] as $templateButton) { ?>
            <?php if ($templateButton['type'] === "ADD_CHANNEL") { ?>
                <button class="ncua-prev-btn ncua-prev-alim__btn--add-channel" type="button"><?= $templateButton['name'] ?></button>
            <?php } else { ?>
                <button class="ncua-prev-btn" type="button"><?= $templateButton['name'] ?></button>
            <?php } ?>
        <?php } ?>
        </footer>
        <!-- // buttons -->
    </section>
</section>
<script>
    const alimtalkPreview = document.querySelector('.alimtalk-contents-preview');
    if (alimtalkPreview) {
        alimtalkPreview.textContent = resolveReplaceCodeForPreview(alimtalkPreview.textContent);
    }
</script>
