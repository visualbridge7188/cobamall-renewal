<?php
$ftType = $friendtalk['type'] ?? 'TEXT';
$isCarousel = ($ftType === 'CAROUSEL_FEED');
$isWideType = in_array($ftType, ['WIDE_IMAGE', 'WIDE_ITEM_LIST']);
$isWideItemList = ($ftType === 'WIDE_ITEM_LIST');
$hasImageType = in_array($ftType, ['IMAGE', 'WIDE_IMAGE']);
// wide/carousel: 최대 2개 horizontal 버튼, 그 외: 최대 5개 vertical 버튼
$isDoubleButtonType = in_array($ftType, ['WIDE_IMAGE', 'WIDE_ITEM_LIST', 'CAROUSEL_FEED']);
$prevClass = $isWideType ? 'ncua-prev ncua-prev--lg' : 'ncua-prev';
$btnClass = $isDoubleButtonType ? 'ncua-prev__btns ncua-prev__btns--horizontal' : 'ncua-prev__btns';
$btnMax = $isDoubleButtonType ? 2 : 5;
?>
<section class="mobile-preview mobile-preview--kakao">
    <span class="ncua-profile ncua-profile--float ncua-profile--kakao">(광고) 채널명</span>
    <?php if ($isCarousel): ?>
        <?php
        $slides = [];
        foreach ($friendtalk['carousels'] ?? [] as $carousel) {
            $attachment = $carousel['attachment'] ?? [];
            $slides[] = [
                'header' => $carousel['header'] ?? null,
                'content' => $carousel['message'] ?? '',
                'imageUrl' => $attachment['image']['imageUrl'] ?? null,
                'buttons' => $attachment['buttons'] ?? [],
                'coupon' => $attachment['coupon'] ?? null,
            ];
        }
        ?>
        <div class="mobile-preview__slider js-layer-mobile-preview">
            <?php foreach ($slides as $slide): ?>
                <section class="ncua-prev ncua-prev--lg">
                    <?php if (!empty($slide['header'])): ?>
                        <h2 class="ncua-prev__title"><?= $slide['header'] ?></h2>
                    <?php endif; ?>

                    <?php if (!empty($slide['imageUrl'])): ?>
                        <figure class="ncua-prev__media">
                            <img src="<?= $slide['imageUrl'] ?>" alt="">
                        </figure>
                    <?php endif; ?>

                    <?php if (!empty($slide['content'])): ?>
                        <p class="ncua-prev__text" style="white-space: pre-wrap;"><?= $slide['content'] ?></p>
                    <?php endif; ?>

                    <?php if (!empty($slide['buttons'])): ?>
                        <div class="ncua-prev__btns ncua-prev__btns--horizontal">
                            <?php foreach (array_slice($slide['buttons'], 0, 2) as $button): ?>
                                <button class="ncua-prev__btn" type="button"><?= $button['name'] ?? '' ?></button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($slide['coupon'])): ?>
                        <button type="button" class="ncua-prev__coupon">
                            <span class="ncua-prev__coupon-text">
                                <?= $slide['coupon']['title'] ?? '' ?>
                                <?php if (!empty($slide['coupon']['description'])): ?>
                                    <time><?= $slide['coupon']['description'] ?></time>
                                <?php endif; ?>
                            </span>
                            <span class="ncua-prev__coupon-icon"></span>
                        </button>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <section class="<?= $prevClass ?>">
            <?php if ($isWideItemList && !empty($friendtalk['header'])): ?>
                <h2 class="ncua-prev__title"><?= $friendtalk['header'] ?></h2>
            <?php endif; ?>

            <?php if ($isWideItemList): ?>
                <?php if ($isWideItemList && !empty($friendtalk['items'])): ?>
                    <figure class="ncua-prev__media">
                        <img src="<?= $friendtalk['items'][0]['imageUrl'] ?>" alt="">
                        <p class="ncua-prev__media-text"><?= $friendtalk['items'][0]['title'] ?></p>
                    </figure>
                <?php endif; ?>
            <?php else: ?>
                <?php if ($hasImageType && !empty($friendtalk['imageUrl'])): ?>
                    <figure class="ncua-prev__media">
                        <img src="<?= $friendtalk['imageUrl'] ?>" alt="">
                    </figure>
                <?php endif; ?>
            <?php endif; ?>


            <?php if ($isWideItemList && !empty($friendtalk['items'])): ?>
                <div>
                    <?php foreach (array_slice($friendtalk['items'], 1, 4) as $item): ?>
                        <div class="ncua-prev__prd">
                            <?php if (!empty($item['imageUrl'])): ?>
                                <picture class="ncua-prev__prd-media">
                                    <img src="<?= $item['imageUrl'] ?>" alt="">
                                </picture>
                            <?php endif; ?>
                            <div class="ncua-prev__prd-text">
                                <p><?= $item['title'] ?? '' ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($friendtalk['content'])): ?>
                <p class="ncua-prev__text friendtalk-contents-preview" style="white-space: pre-wrap;"><?= $friendtalk['content'] ?></p>
            <?php endif; ?>

            <?php if (!empty($friendtalk['buttons'])): ?>
                <div class="<?= $btnClass ?>">
                    <?php foreach (array_slice($friendtalk['buttons'], 0, $btnMax) as $button): ?>
                        <button class="ncua-prev__btn" type="button"><?= $button['name'] ?? '' ?></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($friendtalk['coupon'])): ?>
                <button type="button" class="ncua-prev__coupon">
                    <span class="ncua-prev__coupon-text">
                        <span class="ncua-prev__coupon-title"><?= $friendtalk['coupon']['title'] ?? '' ?></span>
                        <?php if (!empty($friendtalk['coupon']['description'])): ?>
                            <time class="ncua-prev__coupon-desc"><?= $friendtalk['coupon']['description'] ?></time>
                        <?php endif; ?>
                    </span>
                    <span class="ncua-prev__coupon-icon"></span>
                </button>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</section>
<script>
    document.querySelectorAll('.friendtalk-contents-preview, .ncua-prev__text, .ncua-prev__title').forEach(el => {
        el.textContent = resolveReplaceCodeForPreview(el.textContent);
    });
</script>
