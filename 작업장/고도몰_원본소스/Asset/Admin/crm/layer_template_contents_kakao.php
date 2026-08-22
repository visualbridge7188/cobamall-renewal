<!-- 카카오 알림톡 -->
<?php
// templateEmphasizeType: NONE(메시지형), TEXT(강조표기형), IMAGE(이미지형), ITEM_LIST(아이템리스트형)
$templateType = ($kakaoCloud?->getTemplateEmphasizeType() ?? $kakaoBizm?->getTemplateEmphasizeType()) ?: 'NONE';
$templateContent = $kakaoCloud?->getTemplateContent() ?? $kakaoBizm?->getTemplateContent();
$templateExtra = $kakaoCloud?->getTemplateExtra() ?? $kakaoBizm?->getTemplateExtra();
$templateMessageType = $kakaoCloud?->getTemplateMessageType() ?? $kakaoBizm?->getTemplateMessageType();
$templateButtons = $kakaoCloud?->getTemplateButtons() ?? [];
if (empty($templateButtons) && $kakaoBizm?->getTemplateButton()) {
    $templateButtons = json_decode($kakaoBizm->getTemplateButton(), true) ?: [];
}
?>
<section class="mobile-preview mobile-preview--alim layer-preview-contents--kakao" >
    <span class="ncua-profile ncua-profile--float ncua-profile--kakao">채널명</span>
    <section class="ncua-prev-alim">
        <h2 class="ncua-prev-alim__title">알림톡 도착</h2>
        <section class="ncua-prev-alim__content">
            <?php if ($request->getProvider() === 'cloud' && $templateContent): ?>
                <!-- 강조표기형 헤더 -->
                <?php if ($templateType === 'TEXT'): ?>
                <header class="ncua-prev-alim__em">
                    <span class="ncua-prev-alim__em-title"><?= htmlspecialchars($kakaoCloud?->getTemplateSubtitle() ?? '') ?></span>
                    <h3 class="ncua-prev-alim__em-title-sub"><?= htmlspecialchars($kakaoCloud?->getTemplateTitle() ?? '') ?></h3>
                </header>
                <?php endif; ?>

                <!-- 상단 이미지 (이미지형, 아이템리스트형) -->
                <?php if (in_array($templateType, ['IMAGE', 'ITEM_LIST']) && $kakaoCloud?->getTemplateImageUrl()): ?>
                <figure class="ncua-prev-alim__media">
                    <img src="<?= htmlspecialchars($kakaoCloud?->getTemplateImageUrl()) ?>" alt="">
                </figure>
                <?php endif; ?>

                <!-- 아이템 리스트형 -->
                <?php if ($templateType === 'ITEM_LIST'): ?>
                <section class="ncua-prev-alim__list">
                    <?php if ($kakaoCloud?->getTemplateHeader()): ?>
                    <h3 class="ncua-prev-alim__list-title"><?= htmlspecialchars($kakaoCloud?->getTemplateHeader()) ?></h3>
                    <?php endif; ?>

                    <?php if ($kakaoCloud?->getTemplateItemHighlight()): ?>
                    <?php $highlight = $kakaoCloud?->getTemplateItemHighlight(); ?>
                    <div class="ncua-prev-alim__highlight">
                        <div>
                            <p class="ncua-prev-alim__highlight-title"><?= htmlspecialchars($highlight['title'] ?? '') ?></p>
                            <p><?= htmlspecialchars($highlight['description'] ?? '') ?></p>
                        </div>
                        <?php if (!empty($highlight['imageUrl'])): ?>
                        <img src="<?= htmlspecialchars($highlight['imageUrl']) ?>" alt="">
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($kakaoCloud?->getTemplateItems()): ?>
                    <div class="ncua-prev-alim-table">
                        <table>
                            <tbody>
                                <?php foreach ($kakaoCloud?->getTemplateItems() as $item): ?>
                                <tr>
                                    <th><?= htmlspecialchars($item['title'] ?? '') ?></th>
                                    <td><?= htmlspecialchars($item['description'] ?? '') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <?php $summary = $kakaoCloud?->getTemplateItemSummary(); ?>
                            <?php if (!empty($summary['title']) || !empty($summary['description'])): ?>
                            <tfoot>
                                <tr>
                                    <th><?= htmlspecialchars($summary['title'] ?? '') ?></th>
                                    <td><?= htmlspecialchars($summary['description'] ?? '') ?></td>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                    <?php endif; ?>
                </section>
                <?php endif; ?>

                <!-- 본문 -->
                <div class="ncua-prev-alim__text"><?= nl2br(htmlspecialchars($templateContent ?? '')) ?></div>

                <!-- 부가 정보 -->
                <?php if ($templateExtra): ?>
                <p class="ncua-prev-alim__info"><?= nl2br(htmlspecialchars($templateExtra)) ?></p>
                <?php endif; ?>

                <!-- 채널 추가 문구 -->
                <?php if (in_array($templateMessageType, ['AD', 'MI', 'ADD_CHANNEL', 'MIX'])): ?>
                <p class="ncua-prev-alim__info">채널 추가하고 이 채널의 마케팅 메시지 등을 카카오톡으로 받기</p>
                <?php endif; ?>

            <?php elseif ($request->getProvider() === 'bizm' && $templateContent): ?>
                <!-- 비즈엠 -->
                <div class="ncua-prev-alim__text"><?= nl2br(htmlspecialchars($templateContent ?? '')) ?></div>

            <?php else: ?>
                <!-- 데이터 없는 경우 -->
                <div class="ncua-prev-alim__text">템플릿 데이터를 불러올 수 없습니다.</div>
            <?php endif; ?>
        </section>

        <!-- 버튼 -->
        <?php if ($templateButtons): ?>
        <footer class="ncua-prev-alim__footer">
            <?php foreach ($templateButtons as $button): ?>
            <?php
                $buttonType = $button['type'] ?? $button['linkType'] ?? '';
                $isChannelAddButton = in_array($buttonType, ['AC', 'ADD_CHANNEL']);
                $buttonClass = 'ncua-prev__btn' . ($isChannelAddButton ? ' ncua-prev-alim__btn--add-channel' : '');
            ?>
            <button class="<?= $buttonClass ?>" type="button"><?= htmlspecialchars($button['name'] ?? $button['buttonName'] ?? '') ?></button>
            <?php endforeach; ?>
        </footer>
        <?php endif; ?>
    </section>
</section>
