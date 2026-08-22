<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/mobile-send.css') ?>" rel="stylesheet"/>
<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/crm-common.css') ?>" rel="stylesheet"/>
<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/crm-recipe-send.css') ?>" rel="stylesheet"/>
<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'script/slider/slick/slick.css') ?>" rel="stylesheet"/>
<script src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'script/slider/slick/slick.min.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/charcount.js') ?>"></script>
<script type="text/javascript" src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'script/jquery/jquery.multi_select_box.js') ?>"></script>
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
    <form id="frmCrmRecipe">
        <input type="hidden" name="recipeType" value="<?= gd_htmlspecialchars($recipeType ?? '') ?>"/>
        <input type="hidden" name="recipeName" value="<?= gd_htmlspecialchars($recipeName ?? '레시피') ?>"/>

        <header class="ncua-page-header page-header js-affix">
            <h3 class="ncua-help-manual"><?= gd_htmlspecialchars($recipeName ?? '레시피') ?></h3>
            <div class="ncua-page-header__actions">
                <button id="closeCrmRecipe" type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray">닫기</button>
                <button id="previewCrmRecipe" type="button" class="ncua-btn ncua-btn--sm ncua-btn--primary">발송</button>
            </div>
        </header>

        <div class="ncua-split-layout">
            <div class="section-container">
                <div id="layerRecipientSetting" class="ncua-card ncua-card--no-border">
                    <section class="js-recipe-recipient-section js-recipient-setting-section">
                        <header class="ncua-card__header">
                            <div>
                                <h4 class="ncua-card__title">수신 대상</h4>
                                <?php if (!empty($recipeDefault['guideMessages']['targetConditionGuide'])): ?>
                                    <p class="ncua-notice-info js-recipient-guide"><?= gd_htmlspecialchars($recipeDefault['guideMessages']['targetConditionGuide']) ?></p>
                                <?php endif; ?>
                            </div>
                            <button type="button" id="openRecipientDetail" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">상세 설정</button>
                        </header>
                        <section class="ncua-card__body">
                            <input type="hidden" name="selectedCrmGroup" value="" />
                            <div class="ncua-border-group-box recipient-condition-group">
                                <?php if (!empty($conditionGuideView['hasConditionGuide'])): ?>
                                <div class="condition-guide-box js-condition-guide-box">
                                    <div class="condition-guide-row js-condition-selection-guide">
                                        <?= gd_htmlspecialchars($conditionGuideView['guideParts'][0] ?? '') ?>
                                        <span class="ncua-select ncua-select--xs">
                                            <span class="ncua-select__content">
                                                <select name="conditionValue" class="ncua-select__tag js-condition-value-select">
                                                    <?php foreach ($conditionGuideView['selectableValues'] as $v): ?>
                                                        <option value="<?= gd_htmlspecialchars((string) $v) ?>" <?= $v === $conditionGuideView['currentValue'] ? 'selected' : '' ?>><?= gd_htmlspecialchars((string) $v) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </span>
                                        </span>
                                        <?= gd_htmlspecialchars($conditionGuideView['guideParts'][1] ?? '') ?>
                                    </div>
                                    <p class="ncua-notice-info js-condition-selected-guide"></p>
                                    <?php if (!empty($conditionGuideView['productSelectableCount'])): ?>
                                    <div class="ncua-border-group-box condition-product-select-box js-condition-product-select">
                                        <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray js-open-product-select">상품 선택</button>
                                        <input type="hidden" name="selectedProductNos" class="js-selected-product-nos" value="" data-max="<?= (int) $conditionGuideView['productSelectableCount'] ?>" />
                                        <div class="condition-product-select__list js-selected-product-list"></div>
                                        <p class="ncua-notice-info">특정 상품은 최대 <?= (int) $conditionGuideView['productSelectableCount'] ?>개까지 선택 가능합니다.</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                                <div class="crm-group-selected-row js-crm-group-selected-row display-none">
                                    <span id="crmGroupTagContainer" class="crm-group-tag-container"></span>

                                    <span class="crm-group-extraction js-crm-group-extraction display-none">
                                        <span class="crm-group-extraction-text">고객 추출 중 입니다.</span>
                                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--text-gray has-underline refresh-crm-group">
                                            <img src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/refresh-cw-02.svg" alt="새로고침" />
                                            새로고침
                                        </button>
                                    </span>

                                    <span class="recipient-count-text js-recipient-count-text display-none">발송 인원 총 <strong class="recipient-count-total">0</strong> 명 <span class="point-text">(수신거부대상자 <strong class="recipient-count-reject">0</strong> 명 포함)</span></span>
                                </div>
                            </div>

                            <div class="recipient-info">
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="isOnlyAgreed" checked />
                                    </span>
                                    <span class="ncua-checkbox-field__text">SMS 수신동의한 회원에게 발송</span>
                                </label>
                                <span class="ncua-flex" data-tooltip-code="260604001" data-tooltip-seq="001"></span>
                            </div>
                        </section>
                    </section>
                </div>
                <div id="layerSendMethodSetting" class="ncua-card ncua-card--no-border"></div>
                <div id="layerSendTimeSetting" class="ncua-card ncua-card--no-border"></div>
            </div>

            <aside class="ncua-panel">
                <div id="layerPreviewMessage"></div>
            </aside>
        </div>
    </form>
</article>

<div id="crmRecipeData"
     data-sms-point-each="<?= gd_htmlspecialchars((string) $smsPointEach) ?>"
     data-lms-point-each="<?= gd_htmlspecialchars((string) $lmsPointEach) ?>"
     data-kakao-alrimtalk-point-each="<?= gd_htmlspecialchars((string) $kakaoAlrimTalkPointEach) ?>"
     data-kakao-friendtalk-point-each="<?= gd_htmlspecialchars((string) $kakaoFriendTalkPointEach) ?>"
     data-message-config="<?= gd_htmlspecialchars(json_encode($messageConfig, JSON_UNESCAPED_UNICODE)) ?>"
     data-recipe-default="<?= gd_htmlspecialchars(json_encode($recipeDefault ?? [], JSON_UNESCAPED_UNICODE)) ?>"
     <?php if (!empty($crmGroup)): ?>data-crm-group="<?= gd_htmlspecialchars(json_encode($crmGroup, JSON_UNESCAPED_UNICODE)) ?>"<?php endif; ?>
     hidden></div>

<script type="text/javascript">
    const crmRecipeDataEl = document.getElementById('crmRecipeData');
    const smsPointEach = Number(crmRecipeDataEl.dataset.smsPointEach);
    const lmsPointEach = Number(crmRecipeDataEl.dataset.lmsPointEach);
    const kakaoAlrimtalkPointEach = Number(crmRecipeDataEl.dataset.kakaoAlrimtalkPointEach);
    const kakaoFriendtalkPointEach = Number(crmRecipeDataEl.dataset.kakaoFriendtalkPointEach);
    const messagePoint = Number(<?= (float) ($smsPoint ?? 0) ?>);
    const messageConfig = JSON.parse(crmRecipeDataEl.dataset.messageConfig);
    const recipeDefault = JSON.parse(crmRecipeDataEl.dataset.recipeDefault);
    const crmGroup = JSON.parse(crmRecipeDataEl.dataset.crmGroup || '{}');

    $(document).ready(function () {
        // 툴팁 init — layer init 보다 먼저 끝내 seq 충돌 회피
        const tooltipCodeEl = document.querySelector('[data-tooltip-code]');
        const cosGuideReady = (window.GodoCosGuide && tooltipCodeEl)
            ? window.GodoCosGuide.init({
                apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
                guideCode: Number(tooltipCodeEl.getAttribute('data-tooltip-code')),
            })
            : Promise.resolve();

        cosGuideReady.finally(() => {
            loadPreviewMessageLayer().then(() => {
                loadSendMethodSettingLayer();
            });
            loadSendTimeSettingLayer();
        });
        initializeEvents();
        bindConditionGuideSelectChange();
        initRecipientCrmGroup();
        initProductSelect();
    });

    function bindConditionGuideSelectChange() {
        const select = document.querySelector('select.js-condition-value-select');
        if (!select) return;
        select.addEventListener('change', updateConditionPreview);
        updateConditionPreview(); // 초기 미리보기도 JS가 렌더 (서버 initialPreview 제거)
    }

    function computeRelativeDateStr(offset, unit) {
        const date = new Date();
        if (unit === 'MONTH') {
            const targetDay = date.getDate();
            date.setDate(1);
            date.setMonth(date.getMonth() + offset);
            const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
            date.setDate(Math.min(targetDay, lastDay));
        } else {
            date.setDate(date.getDate() + offset);
        }
        // toISOString(UTC)은 KST에서 하루 어긋날 수 있어 로컬 일자로 포맷
        const pad = n => String(n).padStart(2, '0');
        return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
    }

    function updateConditionPreview() {
        const cg = recipeDefault.conditionGuide || {};
        const previewEl = document.querySelector('.js-condition-selected-guide');
        const select = document.querySelector('select.js-condition-value-select');
        if (!previewEl || !select || !cg.selectedValueGuide) return;

        const value = parseInt(select.value, 10) || 0;
        const unit = cg.valueUnit || 'DAY';
        const recipeType = MobileMessageReplaceCode.normalizeRecipeType(document.querySelector('input[name="recipeType"]')?.value);
        const isPeriod = (cg.dateDisplayType || 'DAY') === 'PERIOD';

        let startDay, endDay;
        if (recipeType === 'repurchase-reminder') {
            startDay = computeRelativeDateStr(-(value + 1), unit);
            endDay = computeRelativeDateStr(-value, unit);
        } else if (['dormant-coupon-wakeup', 'dormant-mileage-wakeup'].includes(recipeType)) {
            startDay = endDay = computeRelativeDateStr(value, unit);
        } else {
            startDay = computeRelativeDateStr(-value, unit);
            endDay = isPeriod ? computeRelativeDateStr(0, unit) : startDay;
        }

        previewEl.textContent = cg.selectedValueGuide
            .replaceAll('{no}', String(value))
            .replaceAll('{selected_day}', startDay)
            .replaceAll('{start_day}', startDay)
            .replaceAll('{end_day}', endDay);
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
            if (!enable) clearComponent?.(campaignMessageType);
            updateTabComponent?.(document, activeSendTabType);
            if (activeSendTabType === 'MAIN') updateCampaignTypeComponent?.(campaignMessageType);
        } else if (sendMethod === 'SMS') {
            document.querySelector('.js-replace-code')?.classList.toggle('display-none', !enable);
            if (!enable) clearSmsComponent?.();
        }
    }

    function setMyappSendConditionAvailability(isRecipientAllType, isOnlyAgreed) {
        // 레시피는 항상 CRM 그룹 대상 → 발송조건 행 미노출, 회원 대상 고정
        isRecipientAllType = false;
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

    function readCrmGroupCounts() {
        const total = parseInt((document.querySelector('.recipient-count-total')?.textContent ?? '0').replace(/[^0-9]/g, ''), 10) || 0;
        const reject = parseInt((document.querySelector('.recipient-count-reject')?.textContent ?? '0').replace(/[^0-9]/g, ''), 10) || 0;
        return { total, reject };
    }

    function getExpectTargetCount() {
        if (!document.querySelector('input[name="selectedCrmGroup"]')?.value) return 0;
        const { total, reject } = readCrmGroupCounts();
        const isOnlyAgreed = document.querySelector('input[name="isOnlyAgreed"]')?.checked;
        return isOnlyAgreed ? total - reject : total;
    }

    function getExpectTargetCountSeparated() {
        if (!document.querySelector('input[name="selectedCrmGroup"]')?.value) {
            return { agreedCount: 0, rejectedCount: 0, totalCount: 0 };
        }
        const { total, reject } = readCrmGroupCounts();
        return { agreedCount: total - reject, rejectedCount: reject, totalCount: total };
    }

    function loadSendMethodSettingLayer() {
        $.post('./mobile_send/layer_send_method_setting.php', { recipeContext: JSON.stringify(recipeDefault) }, function (data) {
            $('#layerSendMethodSetting').html(data);
            filterSendMethodForRecipe();
        });
    }

    function loadSendTimeSettingLayer() {
        $.post('./mobile_send/layer_recipe_send_time_setting.php', { recipeContext: JSON.stringify(recipeDefault) }, function (data) {
            $('#layerSendTimeSetting').html(data);
        });
    }

    function filterSendMethodForRecipe() {
        const input = document.querySelector('input[name="sendMethod"][value="ALIMTALK"]');
        if (!input) return;
        input.disabled = true;
        input.closest('.ncua-radio-field')?.classList.add('display-none');
    }

    function loadPreviewMessageLayer() {
        return $.post('./mobile_send/layer_preview_message.php', function (data) {
            $('#layerPreviewMessage').html(data);
        });
    }

    function sendClickLog(action) {
        return $.post('./crm_recipe_click_log_ps.php', {
            action: action,
            recipeType: document.querySelector('input[name="recipeType"]')?.value ?? '',
            currentPage: location.href.split('?')[0],
        });
    }

    function initializeEvents() {
        document.querySelector('#closeCrmRecipe').addEventListener('click', function () {
            NCDSConfirm({
                message: '레시피 등록을 중단하시겠습니까?',
                subMessage: '저장하지 않은 내용이 있습니다. 현재 설정한 내용이 모두 사라집니다.',
                callback: function (result) {
                    if (!result) {
                        return;
                    }
                    // 로그 요청 완료 후 드로어를 닫아야 요청이 취소되지 않음
                    sendClickLog('close').always(function () {
                        if (window.parent && window.parent !== window) {
                            window.parent.postMessage({ type: 'CLOSE_CRM_RECIPE_DRAWER' }, '*');
                        }
                    });
                },
            });
        });

        document.querySelector('#previewCrmRecipe').addEventListener('click', handleRecipeSendClick);
    }

    const recipeRecipientSection = document.querySelector('.js-recipe-recipient-section');

    function initProductSelect() {
        const wrap = recipeRecipientSection?.querySelector('.js-condition-product-select');
        if (!wrap) return; // 상품선택 미사용 레시피면 미렌더

        const hidden = wrap.querySelector('.js-selected-product-nos');
        const listEl = wrap.querySelector('.js-selected-product-list');
        const max = parseInt(hidden?.dataset.max ?? '0', 10) || 0;
        let selectedProducts = []; // [{ no, name }]

        function escapeHtml(str) {
            return String(str).replace(/[&<>"']/g, function (c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }

        function renderProducts() {
            hidden.value = selectedProducts.map(function (p) { return p.no; }).join(',');
            if (selectedProducts.length === 0) {
                listEl.innerHTML = '';
                return;
            }
            const chips = selectedProducts.map(function (p) {
                return '<span class="ncua-tag ncua-tag--md condition-product-select__chip">'
                    + '<span class="ncua-tag__text">' + escapeHtml(p.name) + '</span>'
                    + '<button type="button" class="ncua-tag__close js-remove-product" data-no="' + escapeHtml(p.no) + '" aria-label="삭제"></button>'
                    + '</span>';
            }).join('');
            listEl.innerHTML = chips
                + '<button type="button" class="ncua-btn ncua-btn--xs ncua-btn--text-gray js-remove-all-product">전체 삭제</button>';
        }

        wrap.querySelector('.js-open-product-select')?.addEventListener('click', function () {
            layer_add_info('goods', { layerTitle: '상품 선택', mode: 'simple', callFunc: 'setAddGoods' });
        });

        listEl.addEventListener('click', function (e) {
            const removeBtn = e.target.closest('.js-remove-product');
            if (removeBtn) {
                selectedProducts = selectedProducts.filter(function (p) { return p.no !== removeBtn.dataset.no; });
                renderProducts();
                return;
            }
            if (e.target.closest('.js-remove-all-product')) {
                selectedProducts = [];
                renderProducts();
            }
        });

        // NCDS 상품선택 모달(layer_goods) 콜백 — 모달은 매번 빈 상태로 열리므로 기존 선택에 병합한다(no 기준 중복 제거).
        window.setAddGoods = function (resultJson) {
            const info = (resultJson && resultJson.info) ? resultJson.info : [];
            const incoming = info.map(function (v) {
                return { no: String(v.goodsNo), name: (v.goodsNm || v.goodsName || '') };
            });

            const merged = selectedProducts.slice();
            incoming.forEach(function (p) {
                if (!merged.some(function (e) { return e.no === p.no; })) {
                    merged.push(p);
                }
            });

            if (max > 0 && merged.length > max) {
                NCDSAlert({ message: max + '개까지 선택 가능합니다.', iconType: 'error' });
                selectedProducts = merged.slice(0, max);
            } else {
                selectedProducts = merged;
            }
            renderProducts();
        };
    }

    function initRecipientCrmGroup() {
        // self 로 MODAL_OPEN 을 보내 이 iframe 이 팝업의 opener 가 되게 한다
        document.getElementById('openRecipientDetail')?.addEventListener('click', async function () {
            try {
                if (await CrmGroupStatus.getCount() >= CrmGroupStatus.MAX_COUNT) {
                    NCDSAlert({ message: 'CRM 그룹은 최대 ' + CrmGroupStatus.MAX_COUNT + '개까지 등록할 수 있습니다.', iconType: 'error' });
                    return;
                }
                window.postMessage({
                    type: 'MODAL_OPEN',
                    payload: {
                        target: 'CRM_GROUP_CREATE_POPUP',
                        query: {
                            recipeType: document.querySelector('input[name="recipeType"]')?.value ?? '',
                        },
                        width: 1000,
                        height: 570,
                    },
                }, '*');
            } catch (e) {
                NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', iconType: 'error' });
            }
        });

        recipeRecipientSection.querySelector('button.refresh-crm-group')?.addEventListener('click', async function () {
            try {
                const crmGroupNo = recipeRecipientSection.querySelector('input[name="selectedCrmGroup"]').value;
                const response = await CrmGroupStatus.getInfo(crmGroupNo);
                setSelectedMemberCount(response.data.count, response.data.smsOptOutCount);
                updateCrmGroupData(response.data.status);
                NCDSToast(CrmGroupStatus.toast(response.data.status));
            } catch (e) {
                NCDSToast(CrmGroupStatus.errorToast());
            }
        });

        if (crmGroup?.no) {
            renderSelectedCrmGroup(crmGroup);
        }
    }

    function renderSelectedCrmGroup({ no, title, count, smsOptOutCount, status }) {
        const tagContainer = recipeRecipientSection.querySelector('#crmGroupTagContainer');
        const selectedCrmGroupInput = recipeRecipientSection.querySelector('input[name="selectedCrmGroup"]');

        markCrmGroupSelected();

        tagContainer.innerHTML = '';
        tagContainer.appendChild(generateCrmGroupTag(title));

        selectedCrmGroupInput.value = no;
        selectedCrmGroupInput.dataset.crmGroupName = title;

        setSelectedMemberCount(count, smsOptOutCount);
        updateCrmGroupData(status);
    }

    window.selectCreatedCrmGroup = function (crmNo, groupName) {
        setSelectedCrmGroup(crmNo, groupName);
    };

    window.getSelectedCrmGroup = function () {
        const input = recipeRecipientSection.querySelector('input[name="selectedCrmGroup"]');
        return { crmNo: input.value, crmGroupName: input.dataset.crmGroupName };
    };

    window.setSelectedCrmGroup = async function (crmNo, groupName) {
        try {
            const { data } = await CrmGroupStatus.getInfo(crmNo);
            // status 응답 title 로 보강, 없으면 전달받은 그룹명
            renderSelectedCrmGroup({
                no: crmNo,
                title: data.title || groupName,
                count: data.count,
                smsOptOutCount: data.smsOptOutCount,
                status: data.status,
            });
        } catch (e) {
            NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', iconType: 'error' });
        }
    };

    function updateCrmGroupData(status) {
        CrmGroupStatus.toggleView(recipeRecipientSection, status);
    }

    function generateCrmGroupTag(groupName) {
        const tag = document.createElement('span');
        tag.className = 'ncua-tag ncua-tag--md';
        tag.innerHTML = `
            <span class="ncua-tag__text"></span>
            <button type="button" class="ncua-tag__close"></button>
        `;
        tag.querySelector('.ncua-tag__text').textContent = groupName;

        tag.querySelector('.ncua-tag__close').addEventListener('click', function () {
            NCDSConfirm({
                message: '이미 생성된 CRM 그룹입니다.',
                subMessage: '그룹 선택 해제하면 레시피에서 다시 선택하기 어렵습니다.<br>선택 해제하시겠습니까?<br>해당 그룹은 "CRM 그룹 관리"에서 조회할 수 있습니다.',
                callback: function (result) {
                    if (!result) return;

                    resetCrmGroupSelection();
                    tag.remove();
                },
            });
        });

        return tag;
    }

    function setSelectedMemberCount(totalCount, rejectCount) {
        recipeRecipientSection.querySelector('.recipient-count-total').innerText = totalCount;
        recipeRecipientSection.querySelector('.recipient-count-reject').innerText = rejectCount;
    }

    // 그룹 선택 상태
    function markCrmGroupSelected() {
        recipeRecipientSection.querySelector('.js-condition-guide-box')?.classList.add('display-none');
        recipeRecipientSection.querySelector('.js-crm-group-selected-row')?.classList.remove('display-none');
    }

    // 그룹 미선택(초기화) 상태
    function resetCrmGroupSelection() {
        const input = recipeRecipientSection.querySelector('input[name="selectedCrmGroup"]');
        input.value = '';
        input.dataset.crmGroupName = '';

        setSelectedMemberCount(0, 0);

        recipeRecipientSection.querySelector('.js-condition-guide-box')?.classList.remove('display-none');
        recipeRecipientSection.querySelector('.js-crm-group-selected-row')?.classList.add('display-none');
        recipeRecipientSection.querySelector('.js-crm-group-extraction')?.classList.add('display-none');
        recipeRecipientSection.querySelector('.js-recipient-count-text')?.classList.add('display-none');
    }

    function generateTargetInfoPayload() {
        const input = document.querySelector('input[name="selectedCrmGroup"]');
        return {
            targetType: 'CRM',
            targetCondition: {
                crm: {
                    no: input?.value ? input.value : null,
                    name: input?.dataset.crmGroupName || ''
                }
            }
        };
    }

    function validateHasMessagePoint() {
        return messagePoint > 0;
    }

    function validateMessagePoint() {
        const sendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value ?? 'SMS';
        const expectCount = getExpectTargetCount();
        let requiredPoint;
        switch (sendMethod) {
            case 'SMS':
                const messageLength = window.messageInput?.calculateLength(window.messageInput.getValue()) ?? 0;
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

    function generateSendSchedulePayload() {
        const sendCycle = document.querySelector('#sendCycle').value;
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
            repeatConfig.repeatDays = [...repeatDatesContainer.querySelectorAll('.ncua-tag')].map(el => parseInt(el.dataset.day));
        }
        return { repeatType: sendCycle, repeatConfig: repeatConfig };
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
            const nextMonth = new Date(baseDate.getFullYear(), baseDate.getMonth() + 1, 1, parseInt(hour), parseInt(minutes), 0);
            for (const day of repeatDays) {
                const candidate = new Date(nextMonth);
                candidate.setDate(day);
                if (candidate.getMonth() === nextMonth.getMonth()) {
                    return candidate;
                }
            }
        }
        return baseDate;
    }

    function getSendData() {
        const sendMethod = document.querySelector('input[name="sendMethod"]:checked').value;
        const selectedGroupNo = document.querySelector('input[name="selectedCrmGroup"]')?.value || '';
        const recipeName = document.querySelector('input[name="recipeName"]').value;

        const sendData = {
            recipeType: document.querySelector('input[name="recipeType"]').value,
            campaignTitle: recipeName, // 필수, 기본값=레시피명
            targetInfo: generateTargetInfoPayload(),
            notificationServiceType: sendMethod,
            ...generateSendMethodRequest(),
            sendType: 'REPEAT',
            sendSchedule: generateSendSchedulePayload(),
            enableAlternative: enableAlternative(sendMethod),
            isOnlyAgreeSMSMember: document.querySelector('input[name="isOnlyAgreed"]').checked,
            accessRouteType: 'MESSAGE',
            targetExpectCount: getExpectTargetCount(),
        };

        // 친구톡은 캠페인명을 제목으로 사용
        const friendtalkCampaignName = sendData.kakaoFriendtalkRequest?.campaignName?.trim();
        if (friendtalkCampaignName) {
            sendData.campaignTitle = friendtalkCampaignName;
        }

        // 재사용: 그룹번호 참조 / 신규: 조건+그룹명
        if (selectedGroupNo) {
            sendData.notificationTargetRepeatNo = parseInt(selectedGroupNo, 10);
        } else {
            sendData.repeatRuleTitle = recipeName; // 신규 모드 필수, 기본값=레시피명
            const conditionSelect = document.querySelector('select.js-condition-value-select');
            const productNos = (document.querySelector('.js-selected-product-nos')?.value ?? '')
                .split(',').map(no => no.trim()).filter(Boolean);
            sendData.recipeCondition = {
                value: parseInt(conditionSelect?.value ?? '0', 10) || 0,
                products: productNos.map(no => ({ no: parseInt(no, 10) })), // name 은 선택값(N)
            };
        }

        return sendData;
    }

    function getPreviewData() {
        const sendData = getSendData();
        return {
            sendMethod: sendData.notificationServiceType,
            viewType: 'layer',
            payload: JSON.stringify(sendData),
        };
    }

    function openPreviewLayerPopup(openButton) {
        $.ajax({
            url: './layer_preview_mobile_send.php',
            type: 'POST',
            data: getPreviewData(),
            success: function (data) {
                ncds_layer_popup({ message: data, title: '발송전 미리보기', size: 'wide' });
            },
            complete: function () {
                openButton.prop('disabled', false);
            }
        });
    }

    async function handleRecipeSendClick() {
        const $button = $(this);
        if ($button.prop('disabled')) return;
        $button.prop('disabled', true);

        document.querySelectorAll('.destructive').forEach(el => {
            el.classList.remove('destructive');
            el.querySelector('.ncua-input__destructive-icon-wrap')?.remove();
        });

        const fail = (message, subMessage) => {
            NCDSAlert({ message, subMessage, iconType: 'error' });
            $button.prop('disabled', false);
        };

        if (!messageConfig.hasCallNumber) {
            return fail('발신번호가 없어 발송할 수 없습니다.', '설정에서 발신번호 등록 후 발송 시도를 해주세요.');
        }
        if (!validateHasMessagePoint()) {
            return fail('메시지 포인트를 충전해 주세요');
        }
        if (!validateMessagePoint()) {
            return fail('포인트가 부족하여 발송할 수 없습니다.', '발송 대상 인원보다 포인트가 적어 발송이 불가합니다. <br /> 충전 후 다시 시도해주세요.');
        }
        const result = validateSendMethodContent();
        if (!result.success) {
            return fail(result.message, result.subMessage);
        }
        const repeatResult = validateRepeatTime();
        if (!repeatResult.result) {
            document.querySelectorAll('input[name="repeatDate[]"]').forEach(item => NCDSValidator.highlight(item));
            NCDSValidator.highlight(document.querySelector('select[name="repeatHour"]'));
            NCDSValidator.highlight(document.querySelector('select[name="repeatMinutes"]'));
            return fail(repeatResult.message, repeatResult.subMessage);
        }

        // 대체 메시지 미작성 시 본문 치환코드 변환해 자동 세팅
        const sendMethod = document.querySelector('input[name="sendMethod"]:checked').value;
        const mainContents = window.messageInput?.getValue() ?? '';
        if (enableAlternative(sendMethod) && (window.alternativeMessageInput?.getValue() ?? '') === '') {
            const allKeys = [
                MobileMessageReplaceCode.CRM_RECIPE,
                MobileMessageReplaceCode.MEMBER, MobileMessageReplaceCode.GOODS, MobileMessageReplaceCode.ORDER,
                MobileMessageReplaceCode.PROMOTION, MobileMessageReplaceCode.BOARD, MobileMessageReplaceCode.REGULAR,
                MobileMessageReplaceCode.PRESENT,
            ].flatMap(category => Object.keys(category));
            const alternativeContents = mainContents.replace(/#\{([a-zA-Z_0-9]+)}/g, (match, key) =>
                allKeys.includes(MobileMessageReplaceCode.normalizeReplaceKey(key)) ? `{${key}}` : match);
            window.alternativeMessageInput.insertText(alternativeContents);
            window.alternativePreview?.setContent(alternativeContents);
        }

        sendClickLog('send');

        // 인원 0명이어도 컨펌 없이 바로 발송전 미리보기
        openPreviewLayerPopup($button);
    }
</script>
