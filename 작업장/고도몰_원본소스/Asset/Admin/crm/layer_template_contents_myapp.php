<!-- 마이앱 (앱푸시) -->
<section class="mobile-preview mobile-preview--myapp layer-preview-contents--myapp">
    <span class="ncua-profile ncua-profile--float ncua-profile--myapp"></span>
    <figure class="ncua-prev-myapp">
        <figcaption class="ncua-prev-myapp__caption">
            <span><?= htmlspecialchars($myappData?->getPushSubject() ?? '') ?></span><br />
            <span><?= nl2br(htmlspecialchars($myappData?->getPushContent() ?? '')) ?></span><br />
            <?php if ($myappData?->getPushWithdraw()): ?>
            <span><?= htmlspecialchars($myappData?->getPushWithdraw()) ?></span>
            <?php endif; ?>
        </figcaption>
        <?php if ($myappData?->getPushImage()): ?>
        <span class="ncua-prev-myapp__media">
            <img data-preview-image="templateWithdrawalImageInput" src="<?= htmlspecialchars($myappData->getPushImage()) ?>" alt="">
        </span>
        <?php endif; ?>
    </figure>
</section>

