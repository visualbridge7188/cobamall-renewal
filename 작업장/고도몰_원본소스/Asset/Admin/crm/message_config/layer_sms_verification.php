<?php if (empty($smsPassword)): ?>
    <div class="ncua-flex ncua-gap-8 ncua-align-items-center">
        <span>메시지 발송을 위해 인증번호를 설정하세요.</span>
        <button type="button" id="manageSmsVerification" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">
            <span class="ncua-btn__label">인증번호 설정</span>
        </button>
    </div>
<?php elseif (!$isVerified): ?>
    <div class="ncua-flex ncua-gap-8 ncua-align-items-center" id="mismatchedSmsPasswordCard">
        <span id="mismatchedSmsPasswordCard"><a class="ncua-link" href="https://www.nhn-commerce.com/mygodo/myGodo_shopMain.php" target="_blank">[NHN 커머스 > 마이페이지 > 쇼핑몰 관리]</a> 에 설정한 인증번호와 일치하지 않습니다.</span>
        <button type="button" id="manageSmsVerification" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">
            <span class="ncua-btn__label">인증번호 설정</span>
        </button>
    </div>
<?php else: ?>
    <div class="ncua-flex ncua-gap-4 ncua-align-items-center" id="registeredSmsPasswordCard">
        <div class="ncua-input ncua-input--xs ncua-input-width-320 is-disabled">
            <div class="ncua-input__content">
                <div class="ncua-input__field ncua-input__field--xs">
                    <input type="password" value="<?= $smsPassword ?>" disabled>
                </div>
            </div>
        </div>
        <button type="button" id="manageSmsVerification" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">
            <span class="ncua-btn__label">인증번호 변경</span>
        </button>
    </div>
<?php endif; ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#manageSmsVerification').click(function (e) {
            $.post('./message_config/layer_manage_sms_verification.php', {mode: 'change'}, function (data) {
                ncds_layer_popup({
                    message: data,
                    title: '메시지 인증번호 등록/변경',
                    size: 'wide-sm',
                    callback: function () {
                        loadSmsVerificationLayer();
                    }
                });
            });
        });
    });
</script>
