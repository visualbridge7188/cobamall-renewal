<section class="js-recipient-setting-section">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title tooltip-align" data-tooltip-icon-type="fill" data-tooltip-seq="001">
            수신 대상 설정
        </h4>
    </header>
    <section class="ncua-card__body">
        <input type="hidden" name="selectedMembers" value="" />
        <input type="hidden" name="selectedMemberGroups" value="" />
        <input type="hidden" name="selectedCrmGroup" value="" />
        <input type="hidden" name="uploadedExcelKey" value="">
        <!-- 수신 대상 라디오 버튼 -->
        <div class="ncua-border-group-box">
            <?php foreach ($recipientTypes as $recipientType): ?>
                <?php if ($recipientType->name === 'EXCEL_UPLOAD' && $isConnectedPg !== 'y') continue; ?>
                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                        <input name="recipientType" type="radio" value="<?= $recipientType->name ?>" <?= $recipientType->name === 'ALL' ? 'checked="checked"' : '' ?> />
                    </span>
                    <span>
                        <span class="ncua-radio-field__text"><?= $recipientType->value ?></span>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>

        <!-- 수신 대상 카운트 영역 -->
        <div class="mobile-send__recipient-count">
            <!-- 회원 선택 버튼 -->
            <div class="recipient-count-box recipient-component" data-target="ALL,MEMBER,CRM_GROUP">
                <span class="member-select recipient-component" data-target="MEMBER">
                    <button type="button" data-click-target="selectMembers" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">회원 선택</button>
                    <button type="button" data-click-target="selectMemberGroups" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">회원 등급 선택</button>
                </span>

                <!-- CRM 그룹 영역 -->
                <span class="crm-group-select recipient-component" data-target="CRM_GROUP">
                    <button type="button" data-click-target="selectCrmGroup" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray select-crm-group">CRM 그룹 선택</button>
                    <span id="crmGroupTagContainer" class="crm-group-tag-container"></span>
                </span>

                <!-- CRM 그룹 추출 상태 -->
                <span class="crm-group-extraction js-crm-group-extraction display-none">
                    <span class="crm-group-extraction-text">고객 추출 중 입니다.</span>
                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--text-gray has-underline refresh-crm-group">
                        <img src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/refresh-cw-02.svg" alt="새로고침" />
                        새로고침
                    </button>
                </span>

                <span class="recipient-count-text tooltip-align recipient-component js-recipient-count-text" data-target="ALL,MEMBER,CRM_GROUP" data-tooltip-seq="002">발송 인원 총 <strong class="recipient-count-total">0</strong> 명 <span class="point-text">(수신거부대상자 <strong class="recipient-count-reject">0</strong> 명 포함)</span></span>
            </div>

            <!-- 회원 선택 태그 -->
            <div class="member-tag recipient-component"></div>

            <!-- 파일 업로드 -->
            <div class="excel-upload-area recipient-component" data-target="EXCEL_UPLOAD">
                <div id="excelFileInput" class="excel-file-input"></div>
                <div id="excelFileTagContainer" class="ncua-file-tag"></div>
                <form id="formMemberSampleExcelDownload" action="./mobile_send/layer_recipient_setting_ps.php" method="post">
                    <input type="hidden" name="mode" value="downloadExcelSample"/>
                </form>
                <a href="#" class="ncua-btn ncua-btn--xs ncua-btn--text has-underline excel-download-btn" onclick="downloadExcelSample()">엑셀 샘플 다운로드</a>
            </div>

            <!-- 직접 입력 -->
            <div class="direct-input-area recipient-component" data-target="DIRECT">
                <div class="direct-member-search">
                    <div class="ncua-input ncua-input--xs">
                        <div class="ncua-input__content">
                            <div class="ncua-input__field ncua-input__field--xs">
                                <input id="directPhoneInput" name="" type="text" value="" placeholder="예) 010-1234-5678" />
                            </div>
                        </div>
                    </div>
                    <button type="button" data-click-target="addDirectPhoneBtn" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">번호 추가</button>
                </div>
                <div class="direct-member-count-wrap">
                    <p class="direct-member-count">발송인원 총 <span id="directPhoneNoCount">0</span>명</p>
                    <ul id="directPhoneNoContainer" class="direct-member-list">
                        <li class="no-data">아직 추가된 발송 대상이 없습니다.<br/>전화번호를 입력 후 [번호 추가] 버튼을 눌러주세요.</li>
                    </ul>
                    <div class="direct-member-button-wrap">
                        <button type="button" data-click-target="deleteSelectedPhoneNoButton" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">선택 삭제</button>
                        <button type="button" data-click-target="deleteAllPhoneNosButton" class="ncua-btn ncua-btn--xs ncua-btn--secondary">전체 삭제</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="recipient-info">
            <!-- SMS 수신동의 체크박스 -->
            <div class="recipient-component" data-target="ALL,MEMBER,CRM_GROUP">
                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                        <input type="checkbox" name="isOnlyAgreed" checked />
                    </span>
                    <span class="ncua-checkbox-field__text">SMS 수신동의한 회원에게 발송</span>
                </label>
            </div>

            <ul class="recipient-info-list recipient-component" data-target="ALL,MEMBER,CRM_GROUP,EXCEL_UPLOAD,DIRECT">
                <li class="ncua-notice-info">정보통신망법에 따라 수신거부한 회원에게는 광고성정보를 발송할 수 없으며, 위반시 과태료가 부과됩니다.</li>
                <li class="ncua-notice-info">해외몰 회원에게는 SMS 발송이 제한되며, 국내몰 회원만 검색이 가능합니다.</li>
                <li class="ncua-caution-text recipient-component" data-target="EXCEL_UPLOAD,DIRECT">대상을 직접 입력 또는 엑셀 업로드하는 경우 "휴면회원" 및 "수신거부회원"의 정보를 포함하지 않도록 주의하시기 바랍니다.</li>
            </ul>

            <!-- CRM 그룹 안내 -->
            <div class="crm-group-notice recipient-component" data-target="CRM_GROUP">
                <p class="crm-group-notice-title">CRM 그룹 안내</p>
                <ul>
                    <li class="ncua-notice-info">CRM 그룹 선택하여 발송 시, 휴면회원, 탈퇴회원, 해외몰 회원은 대상에서 제외되므로 실제 발송 인원과 차이가 있을 수 있습니다.</li>
                    <li class="ncua-notice-info">CRM 그룹은 <a class="ncua-btn ncua-btn--xs ncua-btn--text has-underline" href="../crm/crm_group.php" target="_blank">[CRM > CRM 관리 > CRM 그룹]</a>에서 등록할 수 있습니다.</li>
                </ul>
                <button type="button" data-click-target="createCrmGroup" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray select-crm-group">CRM 그룹 생성</button>
            </div>
        </div>
    </section>
</section>

<script type="text/javascript">
    const recipientSettingSection = document.querySelector('.js-recipient-setting-section');

    // 엑셀 파일 관련
    const tagInstances = new Map();
    let currentFiles = [];
    const excelAllowedExtensions = ['xlsx', 'xls'];
    const MAX_EXCEL_FILE_SIZE = 10 * 1024 * 1024; // 10MB

    // 초기 CRM 그룹 선택 데이터
    const selectedCrmGroup = <?= json_encode($selectedCrmGroup ?? null) ?>;

    $(document).ready(function () {
        initializeRecipientSettingSectionEvents();
        initializeRecipientSettingSectionElement();
    });

    function initializeRecipientSettingSectionEvents() {
        recipientSettingSection.querySelectorAll('[name="recipientType"]').forEach(radio => {
            radio.addEventListener('change', function() {
                recipientSettingSection.querySelectorAll('.recipient-component').forEach(el => {
                    el.classList.add('display-none');
                    const targets = el.dataset.target?.split(',') ?? [];
                    if (targets.includes(radio.value)) {
                        el.classList.remove('display-none');
                    }
                });

                clearRecipients();
                setSendMethodAvailability(radio.value !== 'EXCEL_UPLOAD' && radio.value !== 'DIRECT');
                setMyappSendConditionAvailability(radio.value === 'ALL', recipientSettingSection.querySelector('input[name="isOnlyAgreed"]').checked);
                updateFriendtalkCampainUnitPoint();

                const isRestricted = ['EXCEL_UPLOAD', 'DIRECT'].includes(radio.value);
                if (typeof initReservedSendDatePicker === 'function') initReservedSendDatePicker(isRestricted);
                if (typeof initRepeatSendDatePicker === 'function') initRepeatSendDatePicker(isRestricted);

                switch (radio.value) {
                    case 'ALL':
                        renderAllSelectLayer();
                        recipientSettingSection.querySelector('.js-recipient-count-text').classList.remove('display-none');
                        break;
                    case 'MEMBER':
                    case 'MEMBER_GROUP':
                        recipientSettingSection.querySelector('.js-recipient-count-text').classList.remove('display-none');
                        break;
                    case 'CRM_GROUP':
                        recipientSettingSection.querySelector('button[data-click-target="selectCrmGroup"]').classList.remove('display-none');
                        break;
                }
            });

            radio.addEventListener('click', function(e) {
                if (['EXCEL_UPLOAD', 'DIRECT'].includes(this.value)) {
                    if (!document.querySelector('.js-send-method-setting-section') || !isEditing()) return;

                    e.preventDefault();
                    NCDSConfirm({
                        message: '수신 대상을 변경하시겠습니까?',
                        subMessage: '수신 대상을 변경하면 지금까지 작성한 내용이 모두 삭제됩니다. 계속 진행하시겠습니까?',
                        callback: function (result) {
                            if (result) {
                                e.target.checked = true;
                                e.target.dispatchEvent(new Event('change'));
                            }
                        }
                    });
                }
            });
        });

        recipientSettingSection.querySelector('button[data-click-target="selectMembers"]').addEventListener('click', () => {
            const params = { sendMode: 'sms' };
            const query = new URLSearchParams(params).toString();
            window.open(`./popup_select_members.php?${query}`, 'selectMembersPopup', 'width=1920, height=1000, scrollbars=no')
        });

        recipientSettingSection.querySelector('button[data-click-target="selectMemberGroups"]').addEventListener('click', () => {
            $.post('./layer_select_member_groups.php', null, function (data) {
                ncds_layer_popup({
                    message: data,
                    title: '회원 등급',
                    size: 'wide-sm'
                });
            });
        });

        recipientSettingSection.querySelector('button[data-click-target="selectCrmGroup"]').addEventListener('click', () => {
            $.post('./mobile_send/layer_select_crm_groups.php', null, function (data) {
                ncds_layer_popup({
                    message: data,
                    title: 'CRM 그룹선택',
                    size: 'wide-sm'
                });
            });
        });

        recipientSettingSection.querySelector('button[data-click-target="createCrmGroup"]').addEventListener('click', async () => {
            try {
                if (await CrmGroupStatus.getCount() >= CrmGroupStatus.MAX_COUNT) {
                    NCDSAlert({ message: 'CRM 그룹은 최대 ' + CrmGroupStatus.MAX_COUNT + '개까지 등록할 수 있습니다.', iconType: 'error' });
                    return;
                }
                parent.postMessage({ type: 'MODAL_OPEN', payload: { target: 'CRM_GROUP_CREATE_POPUP', width: 1000, height: 570 } }, '*');
            } catch (e) {
                NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', iconType: 'error' });
            }
        });

        recipientSettingSection.querySelector('button[data-click-target="addDirectPhoneBtn"]').addEventListener('click', function() {
            const phoneInput = recipientSettingSection.querySelector('#directPhoneInput');
            const phone = phoneInput.value.replace(/^\s+|\s+$/g, '');

            if (!phone) {
                NCDSAlert({ message: '전화번호를 입력해주세요.', iconType: 'error' });
                return;
            }
            if (!isValidPhoneNumber(phone)) {
                NCDSAlert({ message: '올바른 전화번호 형식이 아닙니다.<br>예) 010-1234-5678', iconType: 'error' });
                return;
            }

            if (addPhoneNoItem(phone)) {
                phoneInput.value = '';
            }

            updateFriendtalkCampainUnitPoint();
        });

        recipientSettingSection.querySelector('button[data-click-target="deleteSelectedPhoneNoButton"]').addEventListener('click', function() {
            const directPhoneNoContainer = recipientSettingSection.querySelector('#directPhoneNoContainer');
            const checkedItems = directPhoneNoContainer.querySelectorAll('input[name="directPhoneNos[]"]:checked');

            if (checkedItems.length === 0) {
                NCDSAlert({ message: '삭제할 번호를 선택해주세요.', iconType: 'error' });
                return;
            }

            checkedItems.forEach(checkbox => {
                checkbox.closest('li').remove();
            });

            if (directPhoneNoContainer.querySelectorAll('li:not(.no-data)').length === 0) {
                directPhoneNoContainer.innerHTML = `<li class="no-data">아직 추가된 발송 대상이 없습니다.<br/>전화번호를 입력 후 [번호 추가] 버튼을 눌러주세요.</li>`;
            }

            updateDirectPhoneNoCount();
            updateFriendtalkCampainUnitPoint();
        });

        recipientSettingSection.querySelector('button[data-click-target="deleteAllPhoneNosButton"]').addEventListener('click', function() {
            const directPhoneNoContainer = recipientSettingSection.querySelector('#directPhoneNoContainer');

            if (directPhoneNoContainer.querySelectorAll('li:not(.no-data)').length === 0) {
                NCDSAlert({ message: '삭제할 번호가 없습니다.', iconType: 'error' });
                return;
            }

            NCDSConfirm({
                message: '전체 삭제하시겠습니까?',
                subMessage: '추가된 모든 번호가 삭제됩니다.',
                callback: function(result) {
                    if (result) {
                        directPhoneNoContainer.innerHTML = `<li class="no-data">아직 추가된 발송 대상이 없습니다.<br/>전화번호를 입력 후 [번호 추가] 버튼을 눌러주세요.</li>`;
                        updateDirectPhoneNoCount();
                        updateFriendtalkCampainUnitPoint();
                    }
                }
            });
        });

        recipientSettingSection.querySelector('input[name="isOnlyAgreed"]').addEventListener('click', function(e) {
            const isRecipientAllType = recipientSettingSection.querySelector('input[name="recipientType"]:checked').value === 'ALL';
            setMyappSendConditionAvailability(isRecipientAllType, e.target.checked);
            updateFriendtalkCampainUnitPoint();
        });

        recipientSettingSection.querySelector('button.refresh-crm-group').addEventListener('click', async function(e) {
            try {
                const crmGroupNo = recipientSettingSection.querySelector('input[name="selectedCrmGroup"]').value;
                const response = await CrmGroupStatus.getInfo(crmGroupNo);
                setSelectedMemberCount(response.data.count, response.data.smsOptOutCount);
                updateCrmGroupData(response.data.status);
                NCDSToast(CrmGroupStatus.toast(response.data.status));
            } catch (e) {
                NCDSToast(CrmGroupStatus.errorToast());
            }
        });
    }

    function clearRecipients() {
        recipientSettingSection.querySelector('input[name="selectedMembers"]').value = "";
        recipientSettingSection.querySelector('input[name="selectedMemberGroups"]').value = "";
        recipientSettingSection.querySelector('#crmGroupTagContainer').innerText = "";
        setSelectedMemberCount(0, 0);
    }

    window.getSelectedMembers = function () {
        const value = recipientSettingSection.querySelector('input[name="selectedMembers"]').value;
        return value ? value.split(',') : [];
    }

    window.setSelectedMembers = async function (selectedMemberNos) {
        recipientSettingSection.querySelector('input[name="selectedMembers"]').value = selectedMemberNos.join(',');

        if (selectedMemberNos.length > 0) {
            const formData = new FormData();
            formData.append('mode', 'getCountMembers');
            formData.append('selectedMemberNos', JSON.stringify(selectedMemberNos));
            const memberCounts = await fetchRecipientCounts(formData);
            setSelectedMemberCount(memberCounts.totalCount, memberCounts.rejectCount);
        } else {
            setSelectedMemberCount(0, 0);
        }
    }

    window.getSelectedMemberGroups = function () {
        const value = recipientSettingSection.querySelector('input[name="selectedMemberGroups"]').value;
        return value ? value.split(',') : [];
    }

    window.setSelectedMemberGroups = async function (selectedMemberGroups) {
        const input = recipientSettingSection.querySelector('input[name="selectedMemberGroups"]');
        const existingValues = input.value ? input.value.split(',') : [];
        const mergedValues = [...new Set([...existingValues, ...selectedMemberGroups])];
        input.value = mergedValues.join(',');

        if (mergedValues.length > 0) {
            const memberGroupNos = mergedValues.map(v => v.split('|')[0]);

            const memberTagContainer = recipientSettingSection.querySelector('.member-tag');
            memberTagContainer.classList.remove('display-none');
            memberTagContainer.innerHTML = '';
            mergedValues.forEach(groupData => {
                const tag = generateGroupTag(groupData);
                memberTagContainer.appendChild(tag);
            });

            const formData = new FormData();
            formData.append('mode', 'getCountGroupMembers');
            formData.append('selectedMemberGroups', JSON.stringify(memberGroupNos));
            const memberCounts = await fetchRecipientCounts(formData);
            setSelectedMemberCount(memberCounts.totalCount, memberCounts.rejectCount);
        } else {
            setSelectedMemberCount(0, 0);
        }
    }

    function generateGroupTag(groupData) {
        const groupName = groupData.split('|')[1];

        const tag = document.createElement('span');
        tag.className = 'ncua-tag ncua-tag--sm';
        tag.dataset.value = groupData;
        tag.innerHTML = `
            <span class="ncua-tag__text">${groupName}</span>
            <button type="button" class="ncua-tag__close"></button>
        `;

        tag.querySelector('.ncua-tag__close').addEventListener('click', () => {
            removeSelectedMemberGroup(groupData);
            tag.remove();
        });

        return tag;
    }

    function removeSelectedMemberGroup(value) {
        const input = recipientSettingSection.querySelector('input[name="selectedMemberGroups"]');
        const values = input.value ? input.value.split(',') : [];
        input.value = values.filter(v => v !== value).join(',');

        if (!input.value) {
            recipientSettingSection.querySelector('.member-tag').classList.add('display-none');
        }

        const updatedValues = input.value ? input.value.split(',') : [];
        // 삭제 완료된 Set으로 다시 버튼 그리기
        setSelectedMemberGroups(updatedValues);

        // 버튼 활성화 체크
        validateMemberRecipientButton();
    }

    window.selectCreatedCrmGroup = function (crmNo, groupName) {
        layer_close();
        setSelectedCrmGroup(crmNo, groupName);
    }

    window.getSelectedCrmGroup = function () {
        const input = recipientSettingSection.querySelector('input[name="selectedCrmGroup"]');
        return {crmNo: input.value, crmGroupName: input.dataset.crmGroupName};
    }

    window.setSelectedCrmGroup = async function (crmNo, groupName) {
        const tagContainer = recipientSettingSection.querySelector('#crmGroupTagContainer');
        const selectedCrmGroupInput = recipientSettingSection.querySelector('input[name="selectedCrmGroup"]');

        // CRM 그룹 선택 버튼 숨김
        recipientSettingSection.querySelector('button[data-click-target="selectCrmGroup"]').classList.add('display-none');

        tagContainer.innerHTML = '';
        const tag = generateCrmGroupTag(groupName);
        tagContainer.appendChild(tag);

        selectedCrmGroupInput.value = crmNo;
        selectedCrmGroupInput.dataset.crmGroupName = groupName;

        try {
            const response = await CrmGroupStatus.getInfo(crmNo);
            setSelectedMemberCount(response.data.count, response.data.smsOptOutCount);
            updateCrmGroupData(response.data.status);
        } catch (e) {
            NCDSAlert({message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', iconType: 'error'});
        }
    }

    function updateCrmGroupData(status) {
        CrmGroupStatus.toggleView(recipientSettingSection, status);
    }

    window.getExcelUploadData = function () {
        let uploadKeyInput = recipientSettingSection.querySelector('input[name="uploadedExcelKey"]');
        return {uploadedExcelKey: uploadKeyInput.value, successTargetCount: uploadKeyInput.dataset.successTargetCount}
    }

    function generateCrmGroupTag(groupName) {
        const tag = document.createElement('span');
        tag.className = 'ncua-tag ncua-tag--md';
        tag.innerHTML = `
               <span class="ncua-tag__text">${groupName}</span>
               <button type="button" class="ncua-tag__close"></button>
           `;

        // 닫기 버튼 이벤트
        tag.querySelector('.ncua-tag__close').addEventListener('click', function() {
            const selectedCrmGroupInput = recipientSettingSection.querySelector('input[name="selectedCrmGroup"]');
            selectedCrmGroupInput.value = '';
            selectedCrmGroupInput.dataset.crmGroupName = '';

            setSelectedMemberCount(0, 0);

            // CRM 그룹 선택 버튼 다시 표시
            recipientSettingSection.querySelector('button[data-click-target="selectCrmGroup"]').classList.remove('display-none');

            recipientSettingSection.querySelector('.js-crm-group-extraction').classList.add('display-none');
            recipientSettingSection.querySelector('.js-recipient-count-text').classList.remove('display-none');

            tag.remove();
        });

        return tag;
    }

    function setSelectedMemberCount(totalCount, rejectCount) {
        recipientSettingSection.querySelector('.recipient-count-total').innerText = totalCount;
        recipientSettingSection.querySelector('.recipient-count-reject').innerText = rejectCount;
        validateMemberRecipientButton();
        updateFriendtalkCampainUnitPoint();
    }

    function updateFriendtalkCampainUnitPoint() {
        if (document.querySelector('input[name="sendMethod"]:checked')?.value === 'FRIENDTALK') {
            const campaignMessageType = document.querySelector('input[name="campaignMessageType"]:checked')?.value;
            updateCampaignUnitPoint(campaignMessageType);
        }
    }

    function isValidPhoneNumber(phone) {
        const cleaned = phone.replace(/-/g, '');
        return /^01[0-9]{8,9}$/.test(cleaned);
    }

    function formatPhoneNumber(phone) {
        const cleaned = phone.replace(/-/g, '');
        if (cleaned.length === 10) {
            return cleaned.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
        } else if (cleaned.length === 11) {
            return cleaned.replace(/(\d{3})(\d{4})(\d{4})/, '$1-$2-$3');
        }
        return phone;
    }

    function isDuplicate(phone) {
        const directPhoneNoContainer = recipientSettingSection.querySelector('#directPhoneNoContainer');
        const existingInputs = directPhoneNoContainer.querySelectorAll('input[name="directPhoneNos[]"]');
        return [...existingInputs].some(input => input.value === phone);
    }

    function addPhoneNoItem(phone) {
        const directPhoneNoContainer = recipientSettingSection.querySelector('#directPhoneNoContainer');
        const formattedPhone = formatPhoneNumber(phone);

        if (isDuplicate(formattedPhone)) {
            NCDSAlert({ message: '이미 추가된 번호입니다.', iconType: 'error' });
            return false;
        }

        const phoneNumberTag = generatePhoneNumberTag(formattedPhone);
        directPhoneNoContainer.appendChild(phoneNumberTag);

        directPhoneNoContainer.querySelector('li.no-data')?.remove();

        updateDirectPhoneNoCount();
        return true;
    }

    function generatePhoneNumberTag(phoneNumber) {
        const li = document.createElement('li');
        li.innerHTML = `
                   <label>
                       <input hidden name="directPhoneNos[]" type="checkbox" value="${phoneNumber}" />
                       <span>${phoneNumber}</span>
                   </label>
               `;

        return li;
    }

    function updateDirectPhoneNoCount() {
        const phoneNumberCount = recipientSettingSection.querySelector('#directPhoneNoContainer').querySelectorAll('li:not(.no-data)').length;
        recipientSettingSection.querySelector('#directPhoneNoCount').textContent = String(phoneNumberCount);
    }

    function fetchRecipientCounts(formData) {
        return new Promise((resolve) => {
            $.ajax({
                url: './mobile_send/layer_recipient_setting_ps.php',
                type: 'GET',
                data: Object.fromEntries(formData),
                dataType: 'json',
                success: (response) => {
                    resolve(response.success ? response.data : { totalCount: 0, rejectCount: 0 });
                },
                error: () => {
                    resolve({ totalCount: 0, rejectCount: 0 });
                }
            });
        });
    }

    function validateMemberRecipientButton() {
        const selectedMembers = recipientSettingSection.querySelector('input[name="selectedMembers"]').value;
        const selectedMemberGroups = recipientSettingSection.querySelector('input[name="selectedMemberGroups"]').value;

        const selectMembersBtn = recipientSettingSection.querySelector('button[data-click-target="selectMembers"]');
        const selectMemberGroupsBtn = recipientSettingSection.querySelector('button[data-click-target="selectMemberGroups"]');

        if (selectedMembers) {
            selectMembersBtn.disabled = false;
            selectMemberGroupsBtn.disabled = true;
        } else if (selectedMemberGroups) {
            selectMembersBtn.disabled = true;
            selectMemberGroupsBtn.disabled = false;
        } else {
            selectMembersBtn.disabled = false;
            selectMemberGroupsBtn.disabled = false;
        }
    }

    function initializeRecipientSettingSectionElement() {
        const excelFileTagContainer = document.querySelector('#excelFileTagContainer');

        // 엑셀 업로드 버튼 로드
        new ncua.FileInput({
            container: 'excelFileInput',
            fileInputName: 'excelFile[]',
            hintItems: ['엑셀 파일 저장은 반드시 "Excel 통합 문서(xlsx)" 혹은 "Excel 97-2003 통합문서(xls)"로 저장하셔야 합니다. 그 외 csv나 xml 파일등은 지원 되지 않습니다.'],
            multiple: false,
            onChange: async (newFiles) => {
                const file = newFiles[0];

                // 파일 유효성 검사
                if (!await validateExcelFiles(file, excelAllowedExtensions, MAX_EXCEL_FILE_SIZE)) {
                    return false;
                }

                const fileName = file.name;

                try {
                    const uploadResponse = await uploadExcelTargets(file);

                    recipientSettingSection.querySelectorAll('.ncua-file-tags__content').forEach(el => {
                        const tagText = el.querySelector('.ncua-tag__text')?.textContent;
                        if (tagText) {
                            tagInstances.delete(tagText);
                        }
                        el.remove();
                    });

                    let uploadKeyInput = recipientSettingSection.querySelector('input[name="uploadedExcelKey"]');
                    uploadKeyInput.value = uploadResponse.uploadedExcelKey;
                    uploadKeyInput.dataset.successTargetCount = uploadResponse.successTargetCount;

                    // 새 파일 태그 생성
                    createFileTag(fileName, file, {
                        container: excelFileTagContainer,
                        fileInputName: 'excelFile[]',
                        onRemove: () => {
                            const uploadKeyInput = recipientSettingSection.querySelector('input[name="uploadedExcelKey"]');
                            uploadKeyInput.value = "";
                            uploadKeyInput.dataset.successTargetCount = "";
                            updateFriendtalkCampainUnitPoint();
                        }
                    });

                    updateFriendtalkCampainUnitPoint();
                } catch (error) {
                    NCDSAlert({ message: error.message || '엑셀 파일 업로드에 실패했습니다.', iconType: 'error' });
                    return false;
                }
            }
        });

        // 초기 수신 대상 선택
        recipientSettingSection.querySelector('input[name="recipientType"]:checked')?.dispatchEvent(new Event('change'));

        // 전체 회원 기본 선택 시 카운트 자동 조회
        if (recipientSettingSection.querySelector('input[name="recipientType"]:checked')?.value === 'ALL') {
            renderAllSelectLayer();
        }

        // 초기 CRM 그룹 선택 처리
        const urlParams = new URLSearchParams(window.location.search);
        const [crmGroupNo, crmGroupName] = ['crmGroupNo', 'crmGroupName', 'crmGroupMemberCount'].map(key => urlParams.get(key));
        if (crmGroupNo) {
            document.querySelector('input[name="recipientType"][value="CRM_GROUP"]').click();
            setTimeout(() => {
                setSelectedCrmGroup(crmGroupNo, crmGroupName);
            }, 100);
        }
    }

    function uploadExcelTargets(file) {
        const formData = new FormData();
        formData.append('mode', 'uploadExcelTarget');
        formData.append('excel', file);

        return new Promise((resolve, reject) => {
            $.ajax({
                url: './mobile_send/layer_recipient_setting_ps.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: (response) => {
                    if (response.success) {
                        resolve({
                            uploadedExcelKey: response.data.uploadedExcelKey,
                            successTargetCount: response.data.successTargetCount ?? 0
                        });
                    } else {
                        reject(new Error(response.message || '엑셀 파일 처리에 실패했습니다.'));
                    }
                },
                error: () => {
                    reject(new Error('엑셀 파일 처리에 실패했습니다.'));
                }
            });
        });
    }

    const createFileTag = (fileName, file, options) => {
        const { index = null, container, fileInputName, onRemove } = options;
        const fileTag = new ncua.Tag({
            text: fileName,
            size: 'sm',
            close: true,
            onButtonClick: () => {
                const tagInstance = tagInstances.get(fileName);
                if (tagInstance && tagInstance.wrapper) {
                    tagInstance.wrapper.remove();
                }
                // Map에서 태그 인스턴스 삭제
                tagInstances.delete(fileName);
                // 파일 배열에서 제거
                currentFiles = currentFiles.filter(f => f.name !== fileName);
                // 삭제 콜백 실행
                if (typeof onRemove === 'function') {
                    onRemove(fileName);
                }
            }
        });

        // hidden input 생성
        const hiddenUpfilesInput = document.createElement('input');
        hiddenUpfilesInput.hidden = true;

        hiddenUpfilesInput.type = 'file';
        hiddenUpfilesInput.tabIndex = -1;
        hiddenUpfilesInput.setAttribute('aria-hidden', 'true');
        hiddenUpfilesInput.className = 'no-filestyle';
        hiddenUpfilesInput.accept = '';
        hiddenUpfilesInput.name = fileInputName;
        // FileList를 DataTransfer로 변환하여 파일 설정
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        hiddenUpfilesInput.files = dataTransfer.files;

        // div로 감싸기
        const wrapperDiv = document.createElement('div');
        wrapperDiv.className = 'ncua-file-tags__content';
        wrapperDiv.appendChild(fileTag.element);
        wrapperDiv.appendChild(hiddenUpfilesInput);

        // 태그 인스턴스에 wrapper 정보 저장
        fileTag.wrapper = wrapperDiv;
        tagInstances.set(fileName, fileTag);

        // 감싼 div를 container에 추가
        container.appendChild(wrapperDiv);
    }

    const validateExcelFiles = async (file, allowedExtensions = null, maxSize = null) => {
        // 파일이 없는 경우
        if (!file) {
            NCDSAlert({message: '파일을 선택해 주세요.', iconType: 'error'});
            return false;
        }

        // 파일 확장자 검증
        const fileName = file.name;
        const fileExtension = fileName.split('.').pop().toLowerCase();

        if (allowedExtensions && !allowedExtensions.includes(fileExtension)) {
            let extensionMessage = '';
            if (allowedExtensions === excelAllowedExtensions) {
                extensionMessage = '지원되지 않는 파일입니다.\nExcel 통합 문서(xlsx) 또는 Excel 97-2003 통합문서(xls) 파일만 업로드 가능합니다.';
            }
            NCDSAlert({message: extensionMessage, iconType: 'error'});
            return false;
        }

        // 파일 사이즈 검증
        if (maxSize && file.size > maxSize) {
            const maxSizeMB = (maxSize / (1024 * 1024)).toFixed(1);
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            NCDSAlert({
                message: `최대 ${maxSizeMB}MB까지 업로드 가능합니다. (현재 파일: ${fileSizeMB}MB)`,
                iconType: 'error'
            });
            return false;
        }

        return true;
    }

    async function renderAllSelectLayer() {
        const formData = new FormData();
        formData.append('mode', 'getCountAll');
        const memberCounts = await fetchRecipientCounts(formData);
        setSelectedMemberCount(memberCounts.totalCount, memberCounts.rejectCount);
    }

    function downloadExcelSample() {
        recipientSettingSection.querySelector('#formMemberSampleExcelDownload').submit();
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
