<?php if (!empty($kakaoAlrimBizmConfig['plusId'])): ?>
    <div class="ncua-flex ncua-flex-gap ncua-align-items-center ncua-flex-column kakao-channel-setting__content-card" id="registeredBizmKakaoTalkChannelCard">
        <div class="ncua-border-content-layout">
            <div class="ncua-border-content">
                <div class="ncua-border-content-title">채널</div>
                <div class="ncua-border-content-form">
                    <span class="kakao-channel-setting__sub-title"><?= $kakaoAlrimBizmConfig['plusId'] ?></span>
                </div>
            </div>
            <div class="ncua-divider ncua-divider--text ncua-divider--single-line">
                <div class="ncua-divider__line"></div>
            </div>

            <div class="ncua-border-content">
                <div class="ncua-border-content-title">발신프로필키</div>
                <div class="ncua-border-content-form">
                    <span class="kakao-channel-setting__sub-title"><?= $kakaoAlrimBizmConfig['kakaoKey'] ?></span>
                </div>
            </div>
            <div class="ncua-divider ncua-divider--text ncua-divider--single-line">
                <div class="ncua-divider__line"></div>
            </div>
            <div class="ncua-border-content">
                <div class="ncua-border-content-title">발신프로필 상태</div>
                <div class="ncua-border-content-form">
                    <span class="kakao-channel-setting__sub-title"><?= $kakaoAlrimBizmConfig['status'] ?></span>
                </div>
            </div>
        </div>
        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--destructive delete-kakao-channel-btn" data-click-target="deleteBizmKakaoTalkChannel"><span class="ncua-btn__label">카카오톡 채널 삭제</span></button>
    </div>
<?php else: ?>
    <div class="ncua-flex ncua-gap-8 ncua-align-items-center">
        <span>카카오 알림톡 발송을 위해 카카오톡 채널을 등록하세요.</span>
        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" data-click-target="registerBizmKakaoTalkChannel">
            <span class="ncua-btn__label">카카오톡 채널 등록</span>
        </button>
    </div>
<?php endif; ?>

<script type="text/javascript">
    const isConnectedPg = '<?= $isConnectedPg ?>';
    $(document).ready(function() {
        // 카카오톡 채널 등록
        $('button[data-click-target="registerBizmKakaoTalkChannel"]').on('click', function (e) {
            e.preventDefault();

            if (!document.getElementById('registeredCallNumberCard')) {
                NCDSAlert({ message: '발신번호 설정 후 카카오톡 채널을 등록해 주세요.', iconType: 'error' });
                return;
            }

            if (isConnectedPg === 'n') {
                NCDSAlert({ message: 'PG 연결 완료 후 카카오톡 채널을 등록해 주세요.', iconType: 'error' });
                return;
            }

            $.post('./message_config/layer_register_kakao_talk_channel.php', {sender: 'kakaoAlrim'}, function (data) {
                ncds_layer_popup({message: data, title: '카카오톡 채널 등록', size: 'wide-sm'});
            }).fail(function () {
                NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', 'iconType': 'error' });
            });
        });

        // 카카오톡 채널 삭제
        $('button[data-click-target="deleteBizmKakaoTalkChannel"]').on('click', function (e) {
            NCDSConfirm({
                message: '카카오톡 채널을 삭제하시겠습니까?',
                subMessage: `카카오톡 채널을 삭제하면 카카오 알림톡 사용할 수 없으며 알림톡으로 설정된 자동 알림이 발송되지 않습니다.<br/>
            관련 설정과 등록한 알림톡 템플릿도 모두 삭제되며, 복구되지 않습니다.`,
                btnText: {
                    confirmLabel: '확인',
                    cancelLabel: '취소'
                },
                callback: function(result) {
                    if (!result) return;

                    $.post('./message_config/layer_bizm_kakao_talk_channel_ps.php', {mode: 'delete'}, function (response) {
                        if (response.error || !response.success) {
                            NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', 'iconType': 'error' });
                            return;
                        }

                        loadBizmKakaoTalkChannelLayer();

                        NCDSToast({
                            message: '카카오톡 채널이 삭제되었습니다.',
                            color: 'success',
                        });

                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    }, 'json');
                }
            });
        });
    });
</script>
