<div class="ncua-border-content-layout js-luna-kakao-channel-card">
    <div class="ncua-flex ncua-gap-4 ncua-flex-column">
        <div class="ncua-flex ncua-align-items-center ncua-required" data-tooltip-seq="011"><div>블룸에이아이 알림톡 아이디</div></div>
        <div class="ncua-gap-4">
            <div class="ncua-input ncua-input--xs ncua-input-width-320">
                <div class="ncua-input__content">
                    <div class="ncua-input__field ncua-input__field--xs">
                        <input type="text" name="lunaClientId" value="<?= $kakaoAlrimLunaConfig['lunaCliendId'] ?>"/>
                    </div>
                </div>
            </div>
            <?php if (!empty($kakaoAlrimLunaConfig['lunaCliendId'])): ?>
                <button type="button" data-click-target="logoutKakaoLuna" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">블룸에이아이 로그아웃</button>
            <?php else: ?>
                <button type="button" data-click-target="loginKakaoLuna" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">블룸에이아이 로그인</button>
                <iframe id="ifrmLnkCheck" name="ifrmLnkCheck" style="display:none;"></iframe>
                <form id="frmLunaIdSend" name="frmLunaIdSend" method="post" target="ifrmLnkCheck" action="<?= $lunaKakaoRequestUrl ?>">
                    <input type="hidden" id="lunaIdParam" name="p" value="">
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // 블룸에이아이 로그인
        const cartSection = document.querySelector('.js-luna-kakao-channel-card');
        cartSection.querySelector('button[data-click-target="loginKakaoLuna"]')?.addEventListener('click', function (e) {
            e.preventDefault();
            const lunaClientIdInput = cartSection.querySelector('input[name="lunaClientId"]');
            const lunaClientId = lunaClientIdInput.value;

            if (lunaClientId.trim() === '') {
                NCDSAlert({ message: '아이디를 입력해 주세요.', iconType: 'warning' });
                lunaClientIdInput.closest('.ncua-input').classList.add('destructive');
                lunaClientIdInput.focus();
                return;
            }

            $.post('./message_config/layer_luna_kakao_talk_channel_ps.php', {mode: 'generateSendData', lunaCliendId: lunaClientId, callbackType: 'message'}, function (response) {
                if (response.error) {
                    NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', iconType: 'error' });
                    return;
                }

                if (!response.success) {
                    NCDSAlert({ message: '블룸에이아이 로그인에 실패하였습니다.<br>다시 시도해 주세요.', iconType: 'error' });
                    return;
                }

                cartSection.querySelector('#lunaIdParam').value = response.data;
                cartSection.querySelector('#frmLunaIdSend').submit();
            });
        });

        // 블룸에이아이 로그아웃
        cartSection.querySelector('button[data-click-target="logoutKakaoLuna"]')?.addEventListener('click', function (e) {
            e.preventDefault();
            NCDSConfirm({
                message: '로그아웃 하시겠습니까?',
                subMessage: '로그아웃하면 블룸에이아이 알림톡 사용이 중지되며 <br/>알림톡으로 발송되던 자동알림과 반복발송이 더 이상 발송되지 않습니다.',
                btnText: {
                    confirmLabel: '확인',
                    cancelLabel: '취소'
                },
                callback: (result) => {
                    if (!result) return;

                    $.post('./message_config/layer_luna_kakao_talk_channel_ps.php', {mode: 'logout'}, function (response) {
                        if (response.error) {
                            NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', iconType: 'error' });
                            return;
                        }

                        if (!response.success) {
                            NCDSAlert({ message: '블룸에이아이 로그아웃에 실패하였습니다.<br>다시 시도해 주세요.', iconType: 'error' });
                            return;
                        }

                        window.location.reload();
                    });
                }
            });
        })
    });
</script>
