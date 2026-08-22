<section class="mobile-preview mobile-preview--myapp">
    <span class="ncua-profile ncua-profile--float ncua-profile--myapp"></span>
    <figure class="ncua-prev-myapp">
        <figcaption class="ncua-prev-myapp__caption">
            <?php if (!empty($myappPayload['title'])): ?>
                <span><?= $myappPayload['notificationType'] === 'AD' ? '(광고) ' : '' ?><?= $myappPayload['title'] ?></span><br />
            <?php endif; ?>
            <?php if (!empty($myappPayload['content'])): ?>
                <span class="myapp-contents-preview" style="white-space: pre-wrap;"><?= $myappPayload['content'] ?></span><br />
            <?php endif; ?>
            <?php if ($myappPayload['notificationType'] === 'AD'): ?>
                <span class="myapp-withdrawal-method"><?= !empty($myappPayload['unsubscribeGuide']) ? $myappPayload['unsubscribeGuide'] : '수신 거부: 설정> 알림 OFF' ?></span>
            <?php endif; ?>
        </figcaption>
        <?php if (!empty($myappPayload['imageUrl'])) { ?>
            <span class="ncua-prev-myapp__media">
                <img src="<?= $myappPayload['imageUrl'] ?>" alt="마이앱 푸시 이미지 미리보기" />
            </span>
        <?php } ?>
    </figure>
</section>
<script>
    const myappPreview = document.querySelector('.myapp-contents-preview');
    if (myappPreview) {
        myappPreview.textContent = resolveReplaceCodeForPreview(myappPreview.textContent);
    }
</script>
