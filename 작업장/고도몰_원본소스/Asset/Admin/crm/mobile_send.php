<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/mobile-send.css') ?>" rel="stylesheet"/>
<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/crm-common.css') ?>" rel="stylesheet"/>
<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'script/slider/slick/slick.css') ?>" rel="stylesheet"/>
<script src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'script/slider/slick/slick.min.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/charcount.js')?> "></script>
<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/crm/crm_group_description.js"></script>
<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/crm/mobile_message_replace_code.js"></script>
<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/crm/message_send_common.js"></script>
<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/crm/crm_group_status.js"></script>
<script type="text/javascript" src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-checkbox-group/ncds-checkbox-group.js') ?>"></script>
<script type="text/javascript" src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-datepicker-factory/ncds-datepicker-factory.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/scroll-sticky.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/ncds-jquery-validator.js') ?>"></script>
<script type="text/javascript" src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/godo-ui-module/chip-selector/chip-selector.js') ?>"></script>
<script type="text/javascript" src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/godo-ui-module/message-input/message-input.js') ?>"></script>
<script type="text/javascript" src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/godo-ui-module/crm-preview/crm-preview-loader.js') ?>"></script>
<script type="text/javascript" src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/godo-ui-module/godo-ui-module.js') ?>"></script>
<script src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/aggregator-message-router.js') ?>"></script>

<article class="ncua-content content-wrap-initial mobile-send">
    <form id="frmMobileSend" action="mobile_send_ps.php" method="post">
        <header class="ncua-page-header page-header js-affix">
            <h3 class="ncua-help-manual"><?php echo end($naviMenu->location); ?></h3>
            <div class="ncua-page-header__actions">
                <button id="previewMobileSend" type="button" class="ncua-btn ncua-btn--md ncua-btn--primary">발송</button>
            </div>
        </header>
        <div class="ncua-split-layout">
            <div class="section-container">
                <!-- 메시지 포인트 영역 -->
                <div id="layerMessagePoint" class="ncua-card ncua-card--no-border"></div>

                <!-- 수신대상 설정 영역 -->
                <div id="layerRecipientSetting" class="ncua-card ncua-card--no-border"></div>

                <!-- 발송수단 설정 영역 -->
                <div id="layerSendMethodSetting" class="ncua-card ncua-card--no-border"></div>

                <!-- 발송시간 설정 영역 -->
                <div id="layerSendTimeSetting" class="ncua-card ncua-card--no-border"></div>
            </div>

            <aside class="ncua-panel"> <!-- $previewMessageSide -->
                <div id="layerPreviewMessage"></div>
            </aside>
        </div>
    </form>
</article>
<!-- 레이아웃 및 팝업 노출 스크립트 -->
<script type="text/javascript">
    const messagePoint = Number(<?= $smsPoint ?>);
    const smsPointEach = Number(<?= $smsPointEach ?>);
    const lmsPointEach = Number(<?= $lmsPointEach ?>);
    const kakaoAlrimtalkPointEach = Number(<?= $kakaoAlrimTalkPointEach ?>);
    const kakaoFriendtalkPointEach = Number(<?= $kakaoFriendTalkPointEach ?>);
    const messageConfig = JSON.parse('<?= json_encode($messageConfig, JSON_UNESCAPED_UNICODE) ?>');

    $(document).ready(function () {
        commonLogic();
        initializeEvents();
        initializeElements();
    });

    function commonLogic() {
        /**
         * 윈도우 팝업 함수 ( 원하는 사이즈, 스크롤 등 위해 분리 )
         */
        window.show_popup_message_config = ({url, width, height, onClose}) => {
            win = popup({
                url: url,
                target: '',
                width: width,
                height: height,
                scrollbars: 'yes',
                resizable: 'yes'
            });
            win?.focus();

            if (onClose && win) {
                const timer = setInterval(() => {
                    if (win.closed) {
                        clearInterval(timer);
                        onClose();
                    }
                }, 500);
            }

            return win;
        };
    }

    function initializeEvents() {
        document.querySelector('#previewMobileSend').addEventListener('click', async function () {
            // 더블클릭 방지
            const $button = $(this);
            if ($button.prop('disabled')) return;
            $button.prop('disabled', true);

            document.querySelectorAll('.destructive').forEach(el => {
                el.classList.remove('destructive');
                el.querySelector('.ncua-input__destructive-icon-wrap')?.remove();
            });

            // 발신번호 검증
            if (!messageConfig.hasCallNumber) {
                NCDSAlert({message: '발신번호가 없어 발송할 수 없습니다.', subMessage: '설정에서 발신번호 등록 후 발송 시도를 해주세요.', iconType: 'error'});
                $button.prop('disabled', false);
                return;
            }

            // 포인트 0인 경우
            if (!validateHasMessagePoint()) {
                NCDSAlert({
                    message: '메시지 포인트를 충전해 주세요',
                    iconType: 'error'
                });
                $button.prop('disabled', false);
                return;
            }

            // 발송인원 X 수단별 포인트 검증
            if (!validateMessagePoint()) {
                NCDSAlert({
                    message: '포인트가 부족하여 발송할 수 없습니다.',
                    subMessage: '발송 대상 인원보다 포인트가 적어 발송이 불가합니다. <br /> 충전 후 다시 시도해주세요.',
                    iconType: 'error'
                });
                $button.prop('disabled', false);
                return;
            }

            // CRM그룹 새로고침 필요
            const crmGroupExtraction = document.querySelector('.js-crm-group-extraction');
            if (crmGroupExtraction && !crmGroupExtraction.classList.contains('display-none')) {
                NCDSAlert({message: '추출된 고객을 새로고침 해주세요.', subMessage: '생성한 CRM 그룹의 고객을 새로고침 하여 추출 고객 수를 확인 해주세요.', iconType: 'error'});
                $button.prop('disabled', false);
                return;
            }

            // 발송 수단 검증
            const result = validateSendMethodContent()
            if (!result.success) {
                NCDSAlert({
                    message: result.message,
                    subMessage: result.subMessage,
                    iconType: 'error'
                });
                $button.prop('disabled', false);
                return;
            }

            const sendSetting = document.querySelector('input[name="sendSetting"]:checked')?.value;
            switch (sendSetting) {
                case 'SCHEDULED':
                    if (!validateReservedTime()) {
                        NCDSValidator.highlight(document.querySelector('input[name="reserveDate"]'));
                        NCDSValidator.highlight(document.querySelector('select[name="reserveHour"]'));
                        NCDSValidator.highlight(document.querySelector('select[name="reserveMinutes"]'));
                        NCDSAlert({
                            message: '처리 중에 오류가 발생하여 실패되었습니다.',
                            subMessage: '예약 발송 시간은 현재시간 기준 10분 후부터 가능합니다.',
                            iconType: 'error'
                        });
                        $button.prop('disabled', false);
                        return;
                    }
                    break;
                case 'REPEAT':
                    const validateRepeatTimeResult = validateRepeatTime();
                    if (!validateRepeatTimeResult.result) {
                        document.querySelectorAll('input[name="repeatDate[]"]').forEach((item) => {
                            NCDSValidator.highlight(item);
                        })
                        NCDSValidator.highlight(document.querySelector('select[name="repeatHour"]'));
                        NCDSValidator.highlight(document.querySelector('select[name="repeatMinutes"]'));
                        NCDSAlert({
                            message: validateRepeatTimeResult.message,
                            subMessage: validateRepeatTimeResult.subMessage,
                            iconType: 'error'
                        });
                        $button.prop('disabled', false);
                        return;
                    }
                    break;
            }

            const sendMethod = document.querySelector('input[name="sendMethod"]:checked').value;
            const mainContents = sendMethod === 'ALIMTALK'
                ? (document.querySelector('textarea[name="messageContent"]')?.value ?? '')
                : (window.messageInput?.getValue() ?? '');
            if (enableAlternative(sendMethod)) {
                const alternativeContents = window.alternativeMessageInput.getValue() ?? '';
                if (alternativeContents === '') {
                    const allKeys = [
                        MobileMessageReplaceCode.MEMBER,
                        MobileMessageReplaceCode.GOODS,
                        MobileMessageReplaceCode.ORDER,
                        MobileMessageReplaceCode.PROMOTION,
                        MobileMessageReplaceCode.BOARD,
                        MobileMessageReplaceCode.REGULAR,
                        MobileMessageReplaceCode.PRESENT,
                    ].flatMap(category => Object.keys(category));

                    const alternativeContents = mainContents.replace(/#\{([a-zA-Z_0-9]+)}/g, (match, key) => {
                        return allKeys.includes(MobileMessageReplaceCode.normalizeReplaceKey(key)) ? `{${key}}` : match;
                    });
                    window.alternativeMessageInput.insertText(alternativeContents);
                    window.alternativePreview.setContent(alternativeContents);
                }
            }

            // 발송 인원이 없는경우
            if (getExpectTargetCount() === 0) {
                $button.prop('disabled', false);
                NCDSConfirm({
                    message: '예상 인원이 없습니다.',
                    subMessage: '발송 시 인원이 없으면 발송 실패됩니다. <br /> 그래도 등록하시겠습니까?'
                }).then(function (result) {
                    if (result) {
                        if (!isFriendTalkSendTimeAvailable()) {
                            NCDSConfirm({
                                message: '발송제한 시간으로 인해 내일 08:00에 예약되어 발송됩니다.',
                            }).then(function (result) {
                                if (result) {
                                    openPreviewLayerPopup($button);
                                }
                            });
                        } else {
                            openPreviewLayerPopup($button);
                        }
                    }
                });
            } else {
                if (!isFriendTalkSendTimeAvailable()) {
                    $button.prop('disabled', false);
                    NCDSConfirm({
                        message: '발송제한 시간으로 인해 내일 08:00에 예약되어 발송됩니다.',
                    }).then(function (result) {
                        if (result) {
                            openPreviewLayerPopup($button);
                        }
                    });
                } else {
                    openPreviewLayerPopup($button);
                }
            }
        });
    }

    function isFriendTalkSendTimeAvailable() {
        const sendMethod = document.querySelector('input[name="sendMethod"]:checked').value;
        const sendSetting = document.querySelector('input[name="sendSetting"]:checked')?.value;

        if (sendMethod === 'FRIENDTALK' && sendSetting === 'IMMEDIATE') {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const currentTime = hours * 60 + minutes;
            const startTime = 8 * 60;      // 08:00
            const endTime = 20 * 60 + 31;  // 20:30:59까지 포함

            if (currentTime < startTime || currentTime > endTime) {
                return false;
            }
        }
        return true;
    }

    function openPreviewLayerPopup(openButton) {
        const previewData = getPreviewData();
        $.ajax({
            url: './layer_preview_mobile_send.php',
            type: 'POST',
            data: previewData,
            success: function (data) {
                ncds_layer_popup({
                    message: data,
                    title: '발송전 미리보기',
                    size: 'wide',
                });
            },
            complete: function () {
                openButton.prop('disabled', false);
            }
        });
    }

    function getPreviewData() {
        const sendData = getSendData();
        const sendMethod = sendData.notificationServiceType;

        const previewPayload = {
            sendMethod: sendMethod,
            viewType: 'layer',
            payload: JSON.stringify(sendData),
        };

        // AlimTalk: 템플릿 시각 메타데이터 (버튼, 강조 타입 등)는 getSendData()에 미포함
        if (sendMethod === 'ALIMTALK') {
            const template = getSelectedKakaoAlimtalkTemplate();
            if (template) {
                previewPayload.previewMeta = JSON.stringify(template);
            }
        }

        return previewPayload;
    }

    function generateSendSchedulePayload() {
        const sendSetting = document.querySelector('input[name="sendSetting"]:checked')?.value ?? 'IMMEDIATE';
        // 발송 타입 (즉시, 예약, 반복) 일정 지정
        switch (sendSetting) {
            case 'SCHEDULED':
                const reserveDate = document.querySelector('input[name="reserveDate"]').value;
                const reserveHour = document.querySelector('select[name="reserveHour"]').value;
                const reserveMinutes = document.querySelector('select[name="reserveMinutes"]').value;
                const scheduledAt = `${reserveDate} ${reserveHour}:${reserveMinutes}:00`;
                return {scheduledAt: scheduledAt};
            case 'REPEAT':
                const sendCycle = document.querySelector('#sendCycle').value
                const [repeatStartDate, repeatEndDate] = [...document.querySelectorAll('input[name="repeatDate[]"]')].map(el => el.value);
                const repeatHour = document.querySelector('select[name="repeatHour"]').value;
                const repeatMinutes = document.querySelector('select[name="repeatMinutes"]').value;
                const sendTime = `${repeatHour}:${repeatMinutes}:00`;
                const hasNoEndDate = document.querySelector('input[name="hasNoEndDate"]').checked;
                const repeatConfig = {
                    startDate: repeatStartDate,
                    endDate: hasNoEndDate ? null : repeatEndDate,
                    sendTime: sendTime,
                };
                if (sendCycle === 'WEEKLY') {
                    repeatConfig.repeatDays = [...document.querySelectorAll('input[name="repeatDays[]"]:checked')].map(el => parseInt(el.value));
                } else if (sendCycle === 'MONTHLY') {
                    const repeatDatesContainer = document.querySelector('#repeatDatesContainer');
                    repeatConfig.repeatDays = [...repeatDatesContainer.querySelectorAll('.ncua-tag')]
                        .map(el => parseInt(el.dataset.day));
                }
                return {repeatType: sendCycle, repeatConfig: repeatConfig};
            default:
                return null;
        }
    }

    function generateTargetInfoPayload() {
        const recipientType = document.querySelector('input[name="recipientType"]:checked')?.value ?? 'MEMBER';
        switch (recipientType) {
            case 'ALL':
                return {targetType: recipientType, targetCondition: null}
            case 'MEMBER':
                const selectedMemberNos = getSelectedMembers();
                const selectedMemberGroups = getSelectedMemberGroups().map(item => {
                    const [no, name] = item.split('|');
                    return { no, name };
                });
                if (selectedMemberNos.length > 0) {
                    return {targetType: 'MEMBER', targetCondition: { memberNos: selectedMemberNos }}
                } else if (selectedMemberGroups.length > 0) {
                    return {targetType: 'MEMBER_GRADE', targetCondition: { memberGrades: selectedMemberGroups }}
                } else {
                    return {targetType: 'MEMBER', targetCondition: null}
                }
            case 'CRM_GROUP':
                const selectedCrmGroup = getSelectedCrmGroup();
                return {
                    targetType: 'CRM',
                    targetCondition: {
                        crm: {
                            no: selectedCrmGroup.crmNo ? selectedCrmGroup.crmNo : null,
                            name: selectedCrmGroup.crmGroupName ? selectedCrmGroup.crmGroupName : ''
                        }
                    }
                }
            case 'EXCEL_UPLOAD':
                const excelData = getExcelUploadData();
                return {
                    uploadedExcelKey: excelData.uploadedExcelKey ?? null,
                    targetType: 'EXCEL',
                    targetCondition: { excel: [] } // 서버에서 uploadedExcelKey 통해 할당
                }
            case 'DIRECT':
                const directPhoneNoContainer = document.getElementById('directPhoneNoContainer');
                const directPhoneInputs = directPhoneNoContainer.querySelectorAll('input[name="directPhoneNos[]"]');
                const directPhoneNos = [...directPhoneInputs].map(input => input.value);
                return {targetType: 'CUSTOM', targetCondition: { phoneNos: directPhoneNos }}
        }
    }


    function getSendData() {
        const sendMethod = document.querySelector('input[name="sendMethod"]:checked').value;
        const expectCount = getExpectTargetCount();
        const isOnlyAgreed = document.querySelector('input[name="isOnlyAgreed"]').checked;

        return {
            targetInfo: generateTargetInfoPayload(),
            notificationServiceType: sendMethod,
            ...generateSendMethodRequest(),
            sendType: document.querySelector('input[name="sendSetting"]:checked')?.value ?? 'IMMEDIATE',
            sendSchedule: generateSendSchedulePayload(),
            enableAlternative: enableAlternative(sendMethod),
            isOnlyAgreeSMSMember: isOnlyAgreed,
            accessRouteType: 'MESSAGE',
            targetExpectCount: expectCount,
        };
    }


    function getExpectTargetCountSeparated() {
        const recipientType = document.querySelector('input[name="recipientType"]:checked')?.value;
        const defaultResult = { agreedCount: 0, rejectedCount: 0, totalCount: 0 };

        if (recipientType == null || recipientType === '') {
            return defaultResult;
        }

        const totalCountText = document.querySelector('.recipient-count-total').textContent ?? '0';
        const rejectCountText = document.querySelector('.recipient-count-reject').textContent ?? '0';
        const totalCount = parseInt(totalCountText.replace(/[^0-9]/g, ''), 10) || 0;
        const rejectCount = parseInt(rejectCountText.replace(/[^0-9]/g, ''), 10) || 0;

        switch (recipientType) {
            case 'ALL':
            case 'MEMBER':
                return {
                    agreedCount: totalCount - rejectCount,
                    rejectedCount: rejectCount,
                    totalCount: totalCount
                };
            case 'CRM_GROUP':
                return {
                    agreedCount: totalCount - rejectCount,
                    rejectedCount: rejectCount,
                    totalCount: totalCount
                };
            case 'EXCEL_UPLOAD':
                const uploadKeyInput = document.querySelector('input[name="uploadedExcelKey"]');
                if (uploadKeyInput && uploadKeyInput.dataset.successTargetCount) {
                    const excelTotalCount = parseInt(uploadKeyInput.dataset.successTargetCount.replace(/[^0-9]/g, ''), 10) || 0;
                    return {
                        agreedCount: excelTotalCount,
                        rejectedCount: 0,
                        totalCount: excelTotalCount
                    };
                }
                break;
            case 'DIRECT':
                const directPhoneNoContainer = document.querySelector('#directPhoneNoContainer');
                if (directPhoneNoContainer) {
                    const directTotalCount = directPhoneNoContainer.querySelectorAll('li:not(.no-data)').length;
                    return {
                        agreedCount: directTotalCount,
                        rejectedCount: 0,
                        totalCount: directTotalCount
                    };
                }
                break;
        }

        return defaultResult;
    }

    function validateCallNumber() {
        return new Promise((resolve) => {
            $.ajax({
                url: './mobile_send_ps.php',
                type: 'POST',
                data: { mode: 'validateCallNumber' },
                dataType: 'json',
                success: (response) => {
                    if (response.success) return resolve({ success: true });
                    resolve({ success: false, message: response.message, subMessage: response.data?.subMessage });
                },
                error: () => {
                    resolve({ success: false, message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.' });
                }
            });
        });
    }

    function validateHasMessagePoint() {
        return messagePoint > 0;
    }

    function validateMessagePoint() {
        const sendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value ?? "SMS";
        const expectCount = getExpectTargetCount();

        let requiredPoint;

        switch (sendMethod) {
            case 'SMS':
                const messageLength = window.messageInput.calculateLength(window.messageInput.getValue());
                requiredPoint = messageLength > 90 ? lmsPointEach * expectCount : smsPointEach * expectCount;
                break;
            case 'ALIMTALK':
                requiredPoint = kakaoAlrimtalkPointEach * expectCount;
                break;
            case 'FRIENDTALK':
                requiredPoint = kakaoFriendtalkPointEach * expectCount;
                break;
            default:
                requiredPoint = 1;
        }

        return messagePoint >= requiredPoint;
    }


    function getExpectTargetCount() {
        const recipientType = document.querySelector('[name="recipientType"]:checked')?.value
        if (recipientType == null || recipientType === '') {
            return 0;
        }
        const totalCountText = document.querySelector('.recipient-count-total').textContent ?? '0';
        const rejectCountText = document.querySelector('.recipient-count-reject').textContent ?? '0';
        const totalCount = parseInt(totalCountText.replace(/[^0-9]/g, ''), 10) || 0;
        const rejectCount = parseInt(rejectCountText.replace(/[^0-9]/g, ''), 10) || 0;

        switch (recipientType) {
            case 'ALL':
            case 'MEMBER':
            case 'CRM_GROUP':
                const isOnlyAgreed = document.querySelector('input[name="isOnlyAgreed"]').checked;
                return isOnlyAgreed ? totalCount - rejectCount : totalCount;
            case 'EXCEL_UPLOAD':
                const excelData = getExcelUploadData();
                if (excelData.uploadedExcelKey && excelData.successTargetCount) {
                    return parseInt(excelData.successTargetCount.replace(/[^0-9]/g, ''), 10) || 0;
                } else {
                    return 0;
                }
            case 'DIRECT':
                const directPhoneNoContainer = document.querySelector('#directPhoneNoContainer');
                if (directPhoneNoContainer) {
                    return directPhoneNoContainer.querySelectorAll('li:not(.no-data)').length;
                } else {
                    return 0;
                }
            default:
                return 0;
        }
    }

    function isScheduledTimeValid(date, hour, minutes, minMinutes = 10) {
        const scheduledAt = new Date(`${date} ${hour}:${minutes}:00`);
        const now = new Date();
        const diffMinutes = (scheduledAt - now) / (1000 * 60);
        return diffMinutes >= minMinutes;
    }

    function validateReservedTime() {
        const reserveDate = document.querySelector('input[name="reserveDate"]').value;
        const reserveHour = document.querySelector('select[name="reserveHour"]').value;
        const reserveMinutes = document.querySelector('select[name="reserveMinutes"]').value;

        if (!reserveDate || !reserveHour || !reserveMinutes) {
            return false;
        }

        return isScheduledTimeValid(reserveDate, reserveHour, reserveMinutes);
    }

    function validateRepeatTime() {
        const [repeatStartDate, repeatEndDate] = [...document.querySelectorAll('input[name="repeatDate[]"]')].map(el => el.value);
        const repeatHour = document.querySelector('select[name="repeatHour"]').value;
        const repeatMinutes = document.querySelector('select[name="repeatMinutes"]').value;
        const hasNoEndDate = document.querySelector('input[name="hasNoEndDate"]').checked;

        if (!repeatStartDate || !repeatHour || !repeatMinutes || (!hasNoEndDate && !repeatEndDate)) {
            return { result: false, message: '처리 중에 오류가 발생하여 실패되었습니다.', subMessage: '반복 발송 주기 입력을 확인해주세요.' };
        }

        const now = new Date();
        const startDateTime = new Date(`${repeatStartDate} ${repeatHour}:${repeatMinutes}:00`);
        const diffMinutes = (startDateTime - now) / (1000 * 60);

        if (diffMinutes < 10) {
            return { result: false, message: '처리 중에 오류가 발생하여 실패되었습니다.', subMessage: '반복 발송은 현재 시간으로부터 10분 이후로만 설정이 가능합니다.' };
        }

        const firstSchedule = getFirstScheduleDate(repeatStartDate, repeatHour, repeatMinutes);
        const endDate = hasNoEndDate ? null : new Date(`${repeatEndDate} ${repeatHour}:${repeatMinutes}:00`);
        if (firstSchedule === null || (endDate && firstSchedule > endDate)) {
            return { result: false, message: '선택한 기간 내 발송 일정이 없습니다.<br>발송 설정을 변경해주세요.' };
        }

        return { result: true };
    }

    function getFirstScheduleDate(startDateStr, hour, minutes) {
        const sendCycle = document.querySelector('#sendCycle').value;
        const baseDate = new Date(`${startDateStr} ${hour}:${minutes}:00`);

        if (sendCycle === 'WEEKLY') {
            const repeatDays = [...document.querySelectorAll('input[name="repeatDays[]"]:checked')].map(el => parseInt(el.value));
            if (repeatDays.length === 0) return null;

            for (let i = 0; i < 7; i++) {
                const candidate = new Date(baseDate);
                candidate.setDate(candidate.getDate() + i);
                const dayOfWeek = candidate.getDay() === 0 ? 7 : candidate.getDay();
                if (repeatDays.includes(dayOfWeek)) {
                    return candidate;
                }
            }
        } else if (sendCycle === 'MONTHLY') {
            const repeatDatesContainer = document.querySelector('#repeatDatesContainer');
            const repeatDays = [...repeatDatesContainer.querySelectorAll('.ncua-tag')].map(el => parseInt(el.dataset.day)).sort((a, b) => a - b);
            if (repeatDays.length === 0) return null;

            const startDay = baseDate.getDate();
            for (const day of repeatDays) {
                if (day >= startDay) {
                    const candidate = new Date(baseDate);
                    candidate.setDate(day);
                    if (candidate.getMonth() === baseDate.getMonth()) {
                        return candidate;
                    }
                }
            }
            // 현재 월에 해당하는 일자가 없으면 다음 달 첫 번째 일자
            const nextMonth = new Date(baseDate.getFullYear(), baseDate.getMonth() + 1, 1, parseInt(hour), parseInt(minutes), 0);
            for (const day of repeatDays) {
                const candidate = new Date(nextMonth.getFullYear(), nextMonth.getMonth(), day, parseInt(hour), parseInt(minutes), 0);
                if (candidate.getMonth() === nextMonth.getMonth()) {
                    return candidate;
                }
            }
        }

        return baseDate;
    }

    function loadMessagePointLayer() {
        $.post('./mobile_send/layer_message_point.php', null, function (data) {
            $('#layerMessagePoint').html(data);
        });
    }

    function loadRecipientSettingLayer() {
        $.post('./mobile_send/layer_recipient_setting.php', function (data) {
            $('#layerRecipientSetting').html(data);
        });
    }

    function loadSendMethodSettingLayer() {
        $.post('./mobile_send/layer_send_method_setting.php', function (data) {
            $('#layerSendMethodSetting').html(data);
        });
    }

    function loadSendTimeSettingLayer() {
        $.post('./mobile_send/layer_send_time_setting.php', function (data) {
            $('#layerSendTimeSetting').html(data);
        });
    }

    function loadPreviewMessageLayer() {
        return $.post('./mobile_send/layer_preview_message.php', function (data) {
            $('#layerPreviewMessage').html(data);
        });
    }

    function setRecipientTypeAvailability(enable) {
        document.querySelectorAll('input[name="recipientType"]').forEach(input => {
            if (['EXCEL_UPLOAD', 'DIRECT'].includes(input.value)) {
                input.disabled = !enable;
            }
        });
    }

    function setSendMethodAvailability(enable) {
        document.querySelectorAll('input[name="sendMethod"]').forEach(input => {
            if (['ALIMTALK', 'MYAPP'].includes(input.value)) {
                input.disabled = !enable;
            }
        });

        const sendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value;

        if (sendMethod === 'FRIENDTALK') {
            const activeSendTabType = document.querySelector('button[data-send-tab-type].is-active')?.dataset.sendTabType;
            const campaignMessageType = document.querySelector('input[name="campaignMessageType"]:checked')?.value;
            if (!enable) clearComponent(campaignMessageType);
            updateTabComponent(document, activeSendTabType);
            if (activeSendTabType === 'MAIN') {
                updateCampaignTypeComponent(campaignMessageType);
            }
        } else if (sendMethod === 'SMS') {
            document.querySelector('.js-replace-code')?.classList.toggle('display-none', !enable);
            if (!enable) clearSmsComponent();
        }
    }

    function setMyappSendConditionAvailability(isRecipientAllType, isOnlyAgreed) {
        const myappPushSendConditionRow = document.querySelector('.js-myapp-push-send-condition-row');
        const appInstalledRadio = document.querySelector('input[name="myappSendCondition"][value="APP_INSTALLED"]');
        const appInstalledField = appInstalledRadio?.closest('.ncua-radio-field');

        myappPushSendConditionRow?.classList.toggle('display-none', !isRecipientAllType);
        appInstalledField?.classList.toggle('display-none', !isRecipientAllType || isOnlyAgreed);

        if (!isRecipientAllType || isOnlyAgreed) {
            const memberOnlyRadio = document.querySelector('input[name="myappSendCondition"][value="MEMBER_ONLY"]');
            if (memberOnlyRadio) memberOnlyRadio.checked = true;
        }
    }

    function initializeElements() {
        loadMessagePointLayer();
        loadRecipientSettingLayer();
        loadPreviewMessageLayer().then(() => {
            loadSendMethodSettingLayer();
        });
        loadSendTimeSettingLayer();
    }
</script>

<!-- 툴팁 스크립트 -->
<script defer type="text/javascript">
    const code = 251219003;
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
        }).then((data) => {
            window.cosData = data;
        });
    }
</script>
