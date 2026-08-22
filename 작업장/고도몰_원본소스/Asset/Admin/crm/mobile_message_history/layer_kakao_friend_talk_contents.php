<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-preview.css') ?>" rel="stylesheet"/>
<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'script/slider/slick/slick.css') ?>" rel="stylesheet"/>
<script src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'script/slider/slick/slick.min.js') ?>"></script>

<div class="mobile-preview-wrap">
    <section class="mobile-preview mobile-preview--kakao">
        <span class="ncua-profile ncua-profile--float ncua-profile--kakao">(광고) 채널명</span>
        <?php if ($isCarousel): ?>
            <div class="mobile-preview__slider js-mobile-preview">
                <?php foreach ($carousels as $carousel): ?>
                    <section class="ncua-prev" data-location="수신거부 | 홈 > 채널차단">
                        <?php if (!empty($carousel->getAttachment()->getImage())): ?>
                            <figure class="ncua-prev__media">
                                <img src="<?= $carousel->getAttachment()->getImage()->getImageUrl() ?>" alt="">
                            </figure>
                        <?php endif; ?>

                        <?php if (!empty($carousel->getHeader())): ?>
                            <p class="ncua-prev__content-title"><?= $carousel->getHeader() ?></p>
                        <?php endif; ?>

                        <?php if (!empty($carousel->getMessage())): ?>
                        <p class="ncua-prev__text"><?= nl2br(gd_htmlspecialchars_stripslashes($carousel->getMessage())) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($carousel->getAttachment()->getButtons())): ?>
                            <div class="ncua-prev__btns<?= count($carousel->getAttachment()->getButtons()) > 1 ? ' ncua-prev__btns--horizontal' : '' ?>">
                                <?php foreach (array_slice($carousel->getAttachment()->getButtons(), 0, 2) as $button): ?>
                                    <button class="ncua-prev__btn" type="button"><?= $button->getName() ?></button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($carousel->getAttachment()->getCoupon())): ?>
                            <button type="button" class="ncua-prev__coupon">
                                <span class="ncua-prev__coupon-text">
                                    <?= $carousel->getAttachment()->getCoupon()->getTitle() ?>
                                    <span class="ncua-prev__coupon-desc"><?= $carousel->getAttachment()->getCoupon()->getDescription() ?></span>
                                </span>
                                <span class="ncua-prev__coupon-icon"></span>
                            </button>
                        <?php endif; ?>
                    </section>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <section class="ncua-prev<?= $isText ? ' ncua-prev--sm' : '' ?><?= in_array($type, $wideTypes) ? ' ncua-prev--lg' : '' ?>" data-location="수신거부 | 홈 > 채널차단">
                <?php if ($isWideItemList && !empty($header)): ?>
                    <h2 class="ncua-prev__title"><?= $header ?></h2>
                <?php endif; ?>

                <?php if (in_array($type, $availableImageTypes) && !empty($imageUrl)): ?>
                    <figure class="ncua-prev__media">
                        <img src="<?= $imageUrl ?>" alt="">
                    </figure>
                <?php endif; ?>

                <p class="ncua-prev__text"><?= nl2br(gd_htmlspecialchars_stripslashes($content)) ?></p>

                <?php if ($isWideItemList && !empty($items)): ?>
                    <figure class="ncua-prev__media">
                        <img src="<?= reset($items)->getImageUrl() ?>" alt="">
                        <p class="ncua-prev__media-text"><?= reset($items)->getTitle() ?></p>
                    </figure>
                    <div>
                        <?php foreach (array_slice($items, 1, 4) as $item): ?>
                            <div class="ncua-prev__prd">
                                <picture class="ncua-prev__prd-media">
                                    <img src="<?= $item->getImageUrl() ?>" alt="">
                                </picture>
                                <div class="ncua-prev__prd-text">
                                    <p><?= $item->getTitle() ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($buttons)): ?>
                    <?php if (in_array($type, $doubleButtonTypes)): ?>
                        <div class="ncua-prev__btns<?= count($buttons) > 1 ? ' ncua-prev__btns--horizontal' : '' ?>">
                            <?php foreach (array_slice($buttons, 0, 2) as $button): ?>
                                <button class="ncua-prev__btn" type="button"><?= $button->getName() ?></button>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="ncua-prev__btns">
                            <?php foreach (array_slice($buttons, 0, 5) as $button): ?>
                                <button class="ncua-prev__btn" type="button"><?= $button->getName() ?></button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (!empty($coupon)): ?>
                    <button type="button" class="ncua-prev__coupon">
                    <span class="ncua-prev__coupon-text">
                        <?= $coupon->getTitle() ?>
                        <span class="ncua-prev__coupon-desc"><?= $coupon->getDescription() ?></span>
                    </span>
                        <span class="ncua-prev__coupon-icon"></span>
                    </button>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </section>
    <?php if ($isCarousel): ?>
        <section class="mobile-preview-table">
            <div class="ncua-table ncua-table--vertical">
                <table>
                    <tbody>
                        <?php foreach ($carousels as $index => $carousel): ?>
                            <?php if (!empty($carousel->getAttachment()->getImage())): ?>
                                <tr>
                                    <th><div>캐러셀 <?= $index + 1 ?> 이미지 링크</div></th>
                                    <td><div class="ncua-left-align"><?= $carousel->getAttachment()->getImage()->getImageLink() ?></div></td>
                                </tr>
                            <?php endif; ?>

                            <?php if (!empty($carousel->getAttachment()->getButtons())): ?>
                                <?php foreach (array_slice($carousel->getAttachment()->getButtons(), 0, 2) as $button): ?>
                                    <?php if (!empty($button->getUrl()->getMobileUrl())): ?>
                                        <tr>
                                            <th><div><?= $button->getName() ?> 모바일 링크</div></th>
                                            <td><div class="ncua-left-align"><?= $button->getUrl()->getMobileUrl() ?></div></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($button->getUrl()->getAosUrl())): ?>
                                        <tr>
                                            <th><div><?= $button->getName() ?> 안드로이드 링크</div></th>
                                            <td><div class="ncua-left-align"><?= $button->getUrl()->getAosUrl() ?></div></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($button->getUrl()->getIosUrl())): ?>
                                        <tr>
                                            <th><div><?= $button->getName() ?> IOS 링크</div></th>
                                            <td><div class="ncua-left-align"><?= $button->getUrl()->getIosUrl() ?></div></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if (!empty($carousel->getAttachment()->getCoupon())): ?>
                                <tr>
                                    <th><div><?= $carousel->getAttachment()->getCoupon()->getTitle() ?> 쿠폰명</div></th>
                                    <td><div class="ncua-left-align"><?= $carousel->getAttachment()->getCoupon()->getDescription() ?></div></td>
                                </tr>
                                <tr>
                                    <th><div><?= $carousel->getAttachment()->getCoupon()->getTitle() ?> 링크</div></th>
                                    <td><div class="ncua-left-align"><?= $carousel->getAttachment()->getCoupon()->getUrl() ?></div></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php else: ?>
        <?php if (!empty($imageLink) || !empty($items) || !empty($buttons) || !empty($coupon)):?>
            <section class="mobile-preview-table">
                <div class="ncua-table ncua-table--vertical">
                    <table>
                        <tbody>
                            <?php if (!empty($imageLink)):?>
                                <tr>
                                    <th><div>이미지 링크</div></th>
                                    <td><div class="ncua-left-align"><?= $imageLink ?></div></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (!empty($items)):?>
                                <?php foreach (array_slice($items, 0, 4) as $item): ?>
                                    <tr>
                                        <th><div><?= $item->getTitle() ?> 링크</div></th>
                                        <td><div class="ncua-left-align"><?= $item->getUrl() ?></div></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php if (!empty($buttons)):?>
                                <?php foreach (array_slice($buttons, 0, 5) as $button): ?>
                                    <?php if (!empty($button->getUrl()->getMobileUrl())): ?>
                                        <tr>
                                            <th><div><?= $button->getName() ?> 모바일 링크</div></th>
                                            <td><div class="ncua-left-align"><?= $button->getUrl()->getMobileUrl() ?></div></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($button->getUrl()->getAosUrl())): ?>
                                        <tr>
                                            <th><div><?= $button->getName() ?> 안드로이드 링크</div></th>
                                            <td><div class="ncua-left-align"><?= $button->getUrl()->getAosUrl() ?></div></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($button->getUrl()->getIosUrl())): ?>
                                        <tr>
                                            <th><div><?= $button->getName() ?> IOS 링크</div></th>
                                            <td><div class="ncua-left-align"><?= $button->getUrl()->getIosUrl() ?></div></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php if (!empty($coupon)):?>
                                <tr>
                                    <th><div><?= $coupon->getTitle() ?> 링크</div></th>
                                    <td><div class="ncua-left-align"><?= $coupon->getUrl() ?></div></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>
</div>
<script type="text/javascript">
    $(document).on('shown.bs.modal', '.modal', function() {
        const $slider = $('.js-mobile-preview');

        if (!$slider) return;

        // 이미 초기화되어 있으면 setPosition만 호출
        if ($slider.hasClass('slick-initialized')) {
            $slider.slick('setPosition');
        } else {
            // 아직 초기화되지 않았으면 Slick 초기화
            $slider.slick({
                // 슬라이더 옵션 설정
                dots: true,
                arrows: false,
                infinite: false,
                slidesToShow: 1,
                slidesToScroll: 1,
                variableWidth: true,
                // 필요한 옵션 추가
            });
        }
    });
</script>
