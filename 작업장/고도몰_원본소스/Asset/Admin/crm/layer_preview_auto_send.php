<?php
$allRecipients = $allRecipients ?? array_keys($templates);
$firstRecipient = null;
foreach ($allRecipients as $key) {
    if (isset($templates[$key])) {
        $firstRecipient = $key;
        break;
    }
}
?>
<link rel="stylesheet" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-preview-auto-send.css')?>">
<div class="modal-dialog__content layer-preview-auto-send">
    <?php if (!empty($templates)): ?>
    <!-- 탭 영역 -->
    <div class="ncua-horizontal-tab ncua-horizontal-tab--sm ncua-horizontal-tab--button-white">
        <div class="swiper swiper-initialized swiper-horizontal">
            <div class="swiper-wrapper">
                <?php foreach ($allRecipients as $recipientKey):
                    $hasTemplate = isset($templates[$recipientKey]);
                ?>
                    <div class="swiper-slide ncua-horizontal-tab__item">
                        <button type="button" class="ncua-tab-button tab-<?= $recipientKey ?><?= $recipientKey === $firstRecipient ? ' is-active' : '' ?>" data-recipient="<?= $recipientKey ?>"<?= !$hasTemplate ? ' disabled' : '' ?>>
                            <?= $recipientLabels[$recipientKey] ?? $recipientKey ?>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- 컨텐츠 영역 -->
    <?php foreach ($templates as $recipientKey => $recipientChannels): ?>
    <div class="preview-content-wrap preview-<?= $recipientKey ?>" data-recipient-content="<?= $recipientKey ?>" style="<?= $recipientKey !== $firstRecipient ? 'display:none;' : '' ?>">
        <div class="preview-content-item-wrap">
            <?php foreach ($recipientChannels as $channelKey => $channelData):
                $templateContents = $channelData['templateContents'] ?? '';
                $channelLabel = $channelLabels[$channelKey] ?? $channelKey;
            ?>
            <article class="preview-content-item" data-channel="<?= $channelKey ?>">
                <h4 class="preview-content-item-title"><?= $channelLabel ?></h4>

                <?php if ($channelKey === 'SMS'): ?>
                <!-- SMS 미리보기 -->
                <section data-component="sms-message-preview" class="mobile-preview mobile-preview--sms">
                    <div class="ncua-prev"><?= nl2br(htmlspecialchars($templateContents)) ?></div>
                </section>
                
                <?php elseif ($channelKey === 'KAKAO_ALRIM_TALK'):
                    $kakaoTemplate = ($kakaoTemplateMap ?? [])[$channelData['templateCode'] ?? ''] ?? null;
                    $kakaoBaseContents = $kakaoTemplate['baseContents'] ?? $templateContents;
                ?>
                <!-- 카카오 알림톡 미리보기 -->
                <section class="mobile-preview mobile-preview--alim">
                    <span class="ncua-profile ncua-profile--float ncua-profile--kakao">채널명</span>
                    <section class="ncua-prev-alim">
                        <h2 class="ncua-prev-alim__title">알림톡 도착</h2>

                        <?php if (!empty($kakaoTemplate['templateImageUrl'])): ?>
                        <figure class="ncua-prev-alim__media">
                            <img src="<?= htmlspecialchars($kakaoTemplate['templateImageUrl']) ?>" alt="">
                        </figure>
                        <?php endif; ?>

                        <section class="ncua-prev-alim__content">
                            <?php if (!empty($kakaoTemplate['templateTitle'])): ?>
                            <header class="ncua-prev-alim__em">
                                <h3 class="ncua-prev-alim__em-title"><?= htmlspecialchars($kakaoTemplate['templateTitle']) ?></h3>
                                <?php if (!empty($kakaoTemplate['templateSubtitle'])): ?>
                                <span class="ncua-prev-alim__em-title-sub"><?= htmlspecialchars($kakaoTemplate['templateSubtitle']) ?></span>
                                <?php endif; ?>
                            </header>
                            <?php endif; ?>

                            <?php
                            $kakaoItems = array_filter($kakaoTemplate['templateItems'] ?? [], fn($item) => !empty($item['title']));
                            if (!empty($kakaoItems)):
                            ?>
                            <section class="ncua-prev-alim__list">
                                <?php if (!empty($kakaoTemplate['templateHeader'])): ?>
                                <h3 class="ncua-prev-alim__list-title"><?= htmlspecialchars($kakaoTemplate['templateHeader']) ?></h3>
                                <?php endif; ?>

                                <?php if (!empty($kakaoTemplate['templateItemHighlight']['title'])): ?>
                                <div class="ncua-prev-alim__highlight">
                                    <div>
                                        <p class="ncua-prev-alim__highlight-title"><?= htmlspecialchars($kakaoTemplate['templateItemHighlight']['title']) ?></p>
                                        <?= htmlspecialchars($kakaoTemplate['templateItemHighlight']['description'] ?? '') ?>
                                    </div>
                                    <?php if (!empty($kakaoTemplate['templateItemHighlight']['imageUrl'])): ?>
                                    <img src="<?= htmlspecialchars($kakaoTemplate['templateItemHighlight']['imageUrl']) ?>" alt="">
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                                <div class="ncua-prev-alim-table">
                                    <table>
                                        <tbody>
                                            <?php foreach ($kakaoItems as $item): ?>
                                            <tr>
                                                <th><?= htmlspecialchars($item['title']) ?></th>
                                                <td><?= htmlspecialchars($item['description'] ?? '') ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <?php if (!empty($kakaoTemplate['templateItemSummary']['title'])): ?>
                                        <tfoot>
                                            <tr>
                                                <th><?= htmlspecialchars($kakaoTemplate['templateItemSummary']['title']) ?></th>
                                                <td><?= htmlspecialchars($kakaoTemplate['templateItemSummary']['description'] ?? '') ?></td>
                                            </tr>
                                        </tfoot>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </section>
                            <?php endif; ?>

                            <div class="ncua-prev-alim__text"><?= nl2br(htmlspecialchars($kakaoBaseContents)) ?></div>

                            <?php if (!empty($kakaoTemplate['templateExtra'])): ?>
                            <p class="ncua-prev-alim__info"><?= nl2br(htmlspecialchars($kakaoTemplate['templateExtra'])) ?></p>
                            <?php endif; ?>
                        </section>

                        <?php
                        $kakaoButtons = $kakaoTemplate['templateButtons'] ?? [];
                        if (!empty($kakaoButtons)):
                            usort($kakaoButtons, function ($a, $b) {
                                if ($a['type'] === 'ADD_CHANNEL') return -1;
                                if ($b['type'] === 'ADD_CHANNEL') return 1;
                                return ($a['order'] ?? 0) - ($b['order'] ?? 0);
                            });
                        ?>
                        <footer class="ncua-prev-alim__footer">
                            <?php if (array_filter($kakaoButtons, fn($btn) => $btn['type'] === 'ADD_CHANNEL')): ?>
                            <button class="ncua-prev-btn ncua-prev-alim__btn--add-channel" type="button">채널 추가</button>
                            <?php endif; ?>
                            <?php foreach ($kakaoButtons as $btn): ?>
                                <?php if ($btn['type'] !== 'ADD_CHANNEL'): ?>
                                <button class="ncua-prev-btn" type="button"><?= htmlspecialchars($btn['name'] ?? '버튼') ?></button>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </footer>
                        <?php endif; ?>
                    </section>
                </section>

                <?php elseif ($channelKey === 'MYAPP_PUSH'):
                    $myappTitle = $channelData['title'] ?? '';
                    $myappImage = $channelData['image'] ?? '';
                ?>
                <!-- 마이앱(앱푸시) 미리보기 -->
                <section class="mobile-preview mobile-preview--myapp">
                    <span class="ncua-profile ncua-profile--float ncua-profile--myapp"></span>
                    <figure class="ncua-prev-myapp">
                        <?php if (!empty($myappTitle)): ?>
                        <h3 class="ncua-prev-myapp__title"><?= htmlspecialchars($myappTitle) ?></h3>
                        <?php endif; ?>
                        <figcaption class="ncua-prev-myapp__caption"><?= nl2br(htmlspecialchars($templateContents)) ?></figcaption>
                        <?php if (!empty($myappImage)): ?>
                        <span class="ncua-prev-myapp__media">
                            <img src="<?= htmlspecialchars($myappImage) ?>" alt="" data-preview-image="myapp">
                        </span>
                        <?php endif; ?>
                    </figure>
                </section>
                <?php endif; ?>

            </article>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <?php else: ?>
    <!-- 데이터 없음 -->
    <div class="ncua-empty-state">
        <p>미리보기 데이터가 없습니다.</p>
    </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
(function() {
    const container = document.querySelector('.layer-preview-auto-send');
    if (!container) return;

    const tabButtons = container.querySelectorAll('.ncua-tab-button');
    const previewContents = container.querySelectorAll('.preview-content-wrap');

    // 탭 클릭 이벤트
    const handleTabClick = (event) => {
        const clickedTab = event.target.closest('.ncua-tab-button');
        if (!clickedTab || clickedTab.disabled) return;

        const recipientKey = clickedTab.dataset.recipient;
        if (!recipientKey) return;

        // 모든 탭 비활성화
        tabButtons.forEach(btn => btn.classList.remove('is-active'));
        clickedTab.classList.add('is-active');

        // 모든 컨텐츠 숨기기
        previewContents.forEach(content => content.style.display = 'none');

        // 선택된 컨텐츠 표시
        const targetContent = container.querySelector(`[data-recipient-content="${recipientKey}"]`);
        if (targetContent) {
            targetContent.style.display = 'block';
        }
    };

    // 이벤트 위임
    const tabContainer = container.querySelector('.ncua-horizontal-tab');
    if (tabContainer) {
        tabContainer.addEventListener('click', handleTabClick);
    }
})();
</script>
