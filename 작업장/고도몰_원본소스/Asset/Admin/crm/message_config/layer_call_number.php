<?php if ($hasCaller && $smsPreRegister && !empty($smsAutoData['smsCallNum'])): ?>
    <div class="ncua-flex ncua-gap-4 ncua-align-items-center">
        <!-- 발신번호 등록 상태 -->
        <div class="ncua-input ncua-input--xs ncua-input-width-320 is-disabled" id="registeredCallNumberCard">
            <div class="ncua-input__content">
                <div class="ncua-input__field ncua-input__field--xs">
                    <input type="text" value="<?= gd_number_to_phone($smsAutoData['smsCallNum']) ?>" disabled />
                </div>
            </div>
        </div>
        <button type="button" id="manageCallNumber" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">
            <span class="ncua-btn__label">발신번호 설정</span>
        </button>
    </div>
<?php else: ?>
    <div class="ncua-flex ncua-gap-8 ncua-align-items-center">
        <!-- 발신번호 미등록 상태 -->
        <span>메시지 발송을 위해 발신번호를 설정하세요.</span>
        <button type="button" id="manageCallNumber" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">
            <span class="ncua-btn__label">발신번호 설정</span>
        </button>
    </div>
<?php endif; ?>

<script type="text/javascript">
    $(document).ready(function () {
        // 발신번호 설정
        $('#manageCallNumber').click(function () {
            $.post('./message_config_ps.php', {'mode': 'checkCaller'}, function (response) {
                if (response.error || !response.success) {
                    NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', 'iconType': 'error' });
                    return;
                }

                switch (response.data.type) {
                    case 'no_caller':
                        NCDSConfirm({
                            message: '발신번호 관리책임자를 먼저 등록해 주세요.',
                            subMessage: `발신번호 위조를 통한 스팸·금융사기 방지를 위해 관리책임자를 필수로 등록해야 합니다.<br/>
                            관리책임자는 <a href="https://www.nhn-commerce.com/mygodo/sms/dashboard.gd" class="send-manager-link" target="_blank">[NHN 커머스 > 마이페이지 > SMS 발신번호 관리]</a>에서 등록할 수 있습니다.`,
                            btnText: {
                                confirmLabel: '관리책임자 등록',
                                cancelLabel: '취소'
                            },
                            callback: (result) => {
                                if (!result) {
                                    return;
                                }
                                window.open('https://www.nhn-commerce.com/mygodo/sms/dashboard.gd', '_blank');
                            }
                        });
                        break;
                    case 'connected_caller':
                        show_popup_message_config({
                            url: './popup_manage_call_number.php',
                            width: 760,
                            height: 700,
                            onClose: function () {
                                loadCallNumberLayer();
                            }
                        });
                        break;
                    case 'single_caller':
                    case 'multiple_callers':
                        $.post('./message_config/layer_caller_connector.php', null, function (data) {
                            ncds_layer_popup({message: data, title: '관리책임자 연동'});
                        });
                        break;
                    case 'unverified_account':
                        NCDSAlert({
                            message: '',
                            subMessage: '발신번호 등록 전 본인확인(최초 1회)이 필요합니다.<br>확인을 누르면 통합회원 본인인증 페이지로 이동합니다.',
                            iconType: 'warning',
                            callback: () => {
                                show_popup('https://accounts.nhn-commerce.com/mypage/certify?redirect=mypage/acc');
                            }
                        });
                        break;
                }
            }, 'json');
        });
    });
</script>
