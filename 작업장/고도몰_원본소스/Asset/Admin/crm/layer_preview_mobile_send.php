<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-mobile-send-preview.css')?>" rel="stylesheet"/>
<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'script/slider/slick/slick.css')?>" rel="stylesheet"/>
<script src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'script/slider/slick/slick.min.js')?>"></script>
<script>
     function resolveReplaceCodeForPreview(content, isMain = true) {
         return MobileMessageReplaceCode.resolvePreviewContent(content, {
             shortLinkKeys: isMain ? window.shortLinkKeys : window.shortLinkAlternativeKeys,
         });
     }
</script>

<div class="modal-dialog__content">
    <article class="ncua-content mobile-send-preview">
        <ul>
            <li class="ncua-notice-info">대체 메시지를 작성하지 않은 경우, 기존 메시지(친구톡, 알림톡, 마이앱)의 내용이 그대로 표시됩니다.</li>
            <li class="ncua-notice-info">수정 또는 발송 전, 실제 수신자에게 보여질 화면을 미리 확인할 수 있습니다.</li>
        </ul>
        <div class="message-preview-container">
            <div class="message-preview">
                <p class="message-preview-title">메시지 미리보기</p>
                <div class="message-preview-content">
                    <?php include $previewContents ?>
                </div>
            </div>
            <?php if($useAlternative == "y" && $sendMethod !== 'SMS') { ?>
                <div class="message-preview alt-message-preview">
                    <p class="message-preview-title">대체 메시지 미리보기</p>
                    <div class="message-preview-content">
                        <?php include $alternativePreviewContents ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php if ($showAdWarningText === 'y'): ?>
            <p class="caution-ad-message">(광고)성 메시지에는 080 수신거부 문구가 필수입니다.</p>
        <?php endif; ?>
        <div class="message-auth-code-container">
            <p class="ncua-modal-content-title">
                메시지 인증번호
            </p>
            <div class="ncua-table ncua-table--vertical">
                <table>
                    <tbody>
                        <tr>
                            <th class="ncua-required"><div>인증번호</div></th>
                            <td>
                                <div>
                                    <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs">
                                                <input name="sendPassword" type="text" value="" placeholder="발송 시에 발급된 인증번호를 입력해 주세요." />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <ul>
                <li class="ncua-caution-text">무단으로 메시지 포인트가 사용되지 않도록 메시지 인증번호로 인증 후 발송할 수 있습니다.</li>
                <li class="ncua-notice-info">메시지 인증번호는 <a class="ncua-btn ncua-btn--xs ncua-btn--text has-underline" href="https://www.nhn-commerce.com/mygodo/myGodo_shopMain.php" target="_blank">[마이페이지 > 쇼핑몰 관리]</a> 에서 확인할 수 있습니다.</li>
            </ul>
        </div>
    </article>
</div>
<div class="modal-dialog__footer">
    <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray" onclick="layer_close()">취소</button>
    <button type="submit" class="ncua-btn ncua-btn--sm ncua-btn--primary" onclick="sendMessage()">발송</button>
</div>
<script type="text/javascript">
     const previewPayload = <?= $payloadJson ?>;

     $(document).on('shown.bs.modal', '.modal', function() {
            const $slider = $('.js-layer-mobile-preview');

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

     async function sendMessage() {
         // 인증번호 입력 검증
         const sendPassword = document.querySelector('input[name="sendPassword"]')?.value?.trim() || '';
         if (!sendPassword) {
             NCDSValidator.highlight(document.querySelector('input[name="sendPassword"]'));
             NCDSAlert({
                 message: '메시지 인증번호를 입력해 주세요.',
                 iconType: 'error'
             });
             return;
         }

         const smsPasswordValidate = await validateSendPassword(sendPassword);
         if (!smsPasswordValidate.success) {
             NCDSAlert({ message: smsPasswordValidate.message, subMessage: smsPasswordValidate.subMessage, iconType: 'error' });
             layer_close();
             return;
         }

         // 레시피 신규 모드(기존 CRM 그룹 미선택)는 발송 시 그룹이 새로 생성되므로 20개 초과 차단
         if (previewPayload.recipeType && !previewPayload.notificationTargetRepeatNo) {
             try {
                 if (await CrmGroupStatus.getCount() >= CrmGroupStatus.MAX_COUNT) {
                     NCDSAlert({ message: 'CRM 그룹은 최대 ' + CrmGroupStatus.MAX_COUNT + '개까지 등록할 수 있습니다.', iconType: 'error' });
                     return;
                 }
             } catch (e) {
                 NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', iconType: 'error' });
                 return;
             }
         }

         const isOnlyAgreed = previewPayload.isOnlyAgreeSMSMember ?? true;

         const targetCounts = getExpectTargetCountSeparated()
         if (isOnlyAgreed || targetCounts.rejectedCount === 0) {
             await postSendMessage();
         } else {
             $.post('./layer_reject_recipient_member_info.php', targetCounts)
                 .done(function (data) {
                     ncds_layer_popup({ message: data, title: '수신거부회원 포함 안내', size: 'wide-sm' });
                 });
         }
     }

     async function postSendMessage(option = null) {
         if (option === 'fixOnlyAgreed') {
             previewPayload.isOnlyAgreeSMSMember = true;
             const isOnlyAgreedCheckbox = document.querySelector('input[name="isOnlyAgreed"]');
             isOnlyAgreedCheckbox.checked = true;
         }

         $.ajax({
             url: './mobile_send_ps.php',
             type: 'POST',
             data: { mode: 'send', payload: JSON.stringify(previewPayload) },
             dataType: 'json',
             success: () => {
                 const recipeType = document.querySelector('input[name="recipeType"]')?.value || '';
                 if (recipeType) {
                     window.parent.postMessage({
                         type: 'CLOSE_CRM_RECIPE_DRAWER',
                         reloadList: true,
                         toast: { message: '메시지 발송이 정상적으로 완료되었습니다.', color: 'success', autoClose: 2000 },
                     }, '*');
                 } else {
                     NCDSAlert({
                         message: '메시지 발송이 정상적으로 완료되었습니다.',
                         iconType: 'success',
                         callback: () => {
                             window.location.href = window.location.pathname;
                         },
                     });
                 }
             },
             error: (xhr) => {
                 if (xhr.status === 400) {
                     const data = xhr.responseJSON;
                     NCDSAlert({ message: data?.message || '처리 중에 오류가 발생하여 실패했습니다', iconType: 'error' });
                 } else {
                     NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', 'iconType': 'error' });
                 }
             },
             complete: () => {
                 layer_close();
             }
         });
     }
</script>
