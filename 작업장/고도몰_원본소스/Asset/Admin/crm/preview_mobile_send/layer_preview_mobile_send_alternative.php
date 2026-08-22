<section class="mobile-preview mobile-preview--sms">
    <div class="ncua-prev alternative-contents-preview" style="white-space: pre-wrap;"><?= $alternativeContents ?></div>
</section>
<script>
    const alternativeSmsPreview = document.querySelector('.alternative-contents-preview');
    $(document).ready(() => {
        alternativeSmsPreview.textContent = resolveReplaceCodeForPreview(alternativeSmsPreview.textContent);
    })
</script>
