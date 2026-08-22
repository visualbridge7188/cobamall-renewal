<section class="mobile-preview mobile-preview--sms">
    <div class="ncua-prev sms-contents-preview" style="white-space: pre-wrap;"><?= $mainContents ?></div>
</section>
<script>
    const smsPreview = document.querySelector('.sms-contents-preview');
    $(document).ready(() => {
        smsPreview.textContent = resolveReplaceCodeForPreview(smsPreview.textContent);
    })
</script>
