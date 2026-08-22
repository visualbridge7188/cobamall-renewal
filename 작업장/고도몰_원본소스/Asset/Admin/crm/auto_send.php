<link rel="stylesheet" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/crm-common.css')?>">
<link rel="stylesheet" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/auto-send.css')?>">
<script src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/switch.js')?>"></script>
<script src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/card-toggle.js')?>"></script>
<script src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/unsaved-changes-guard.js')?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/spinner-modal.js') ?>"></script>

<div class="ncua-page-header page-header js-affix affix-top">
    <h3 class="ncua-help-manual"><?php echo end($naviMenu->location); ?></h3>
    <div class="ncua-page-header__actions">
        <button class="auto-send-save-button ncua-btn ncua-btn--md ncua-btn--primary"><span class="ncua-btn__label">저장</span></button>
    </div>
</div>

<form id="frmAutoSend" name="frmAutoSend">
    <!-- 초기값 저장용 hidden inputs -->
    <?php foreach ($autoSends as $code => $autoSend): ?>
        <input type="hidden" class="initial-value" data-code="<?= $code ?>" value="<?= $autoSend['shouldAutoSend'] ?>" />
    <?php endforeach; ?>

    <article class="ncua-content auto_send">
        <?php include $messagePoint; ?>
        <?php include $autoRecommend; ?>
        <?php include $autoListSearch; ?>
    </article>
</form>

<script>
    const form = document.getElementById('frmAutoSend');
    const useJoinPolicy = <?= $useJoinPolicy ? 'true' : 'false' ?>;
    const MEMBER_JOIN_APPROVE_CODE = 'APPROVAL';
    const FORCE_SEND_CODE = 'PRESENT';

    document.addEventListener('DOMContentLoaded', () => {
        onClickAutoSendSearch();
        onClickAutoSendSearchReset();
        initAutoSendRadioSync();
        initAutoSendListSort();

        /**
         * 저장하지 않은 변경사항 이탈 방지
         */
        window.unsavedGuard = new UnsavedChangesGuard('#frmAutoSend', (proceed) => {
            NCDSConfirm({
                message: '페이지를 이동하시겠습니까?',
                subMessage: '저장하지 않은 내용이 있습니다.<br/>페이지를 이동하면 설정한 내용이 모두 사라집니다.',
                btnText: {
                    confirmLabel: '이동',
                    cancelLabel: '취소'
                },
                callback: (result) => {
                    if (result) proceed();
                }
            });
        });
    });

    function initAutoSendListSort() {
        const container = document.getElementById('ncuaSearchResult');
        if (container) {
            bindSortEvent(container);
            sortAutoSendList('category'); // 기본 정렬: 카테고리순
        }
    }

    // 저장 버튼 클릭
    document.querySelector('.auto-send-save-button').addEventListener('click', function (e) {
        e.preventDefault();
        const changedCodes = getChangedAutoSendCodes();
        const loadingModal = window.spinnerModal({ message: '저장 중...' });

        if (Object.keys(changedCodes).length === 0) {
            NCDSToast({message: '변경된 내용이 없습니다.', color: 'warning'});
            return;
        }

        NCDSConfirm({
            message: '발송 여부를 변경하시겠습니까?',
            subMessage: '변경한 알림은 모든 수신 대상과 발송 수단이<br/>일괄적으로 \'발송함\' 또는 \'발송안함\'으로 변경됩니다.',
            btnText: {
                confirmLabel: '확인',
                cancelLabel: '취소'
            },
            callback: (result) => {
                if (!result) return;

                loadingModal.open();  
                $.ajax({
                    url: './auto_send_ps.php',
                    type: 'POST',
                    data: {
                        mode: 'updateEnabledFlags',
                        codes: changedCodes
                    },
                    success: function (data) {
                        loadingModal.close();

                        if (data.result) {
                            updateInitialValues(changedCodes);
                            window.unsavedGuard?.reset();
                            NCDSToast({message: '저장되었습니다.', color: 'success'});
                        } else {
                            NCDSToast({message: '일시적인 오류가 발생하였습니다. </br>다시 시도해 주세요.', color: 'error'});
                        }
                    },
                    error: function () {
                        NCDSToast({message: '일시적인 오류가 발생하였습니다. </br>다시 시도해 주세요.', color: 'error'});
                    }
                });
            }
        });
    });

    /**
     * Form에서 변경된 코드만 추출
     * initial value vs sendStatus[CODE] 비교
     */
    function getChangedAutoSendCodes() {
        const formData = new FormData(form);
        const changed = {};

        // 초기값 hidden input들을 순회
        form.querySelectorAll('.initial-value').forEach(input => {
            const code = input.dataset.code;
            const initialValue = input.value;
            const currentValue = formData.get(`sendStatus[${code}]`);

            if (currentValue && currentValue !== initialValue) {
                changed[code] = currentValue;
            }
        });

        return changed;
    }

    /**
     * 저장 성공 후 초기값 업데이트
     */
    function updateInitialValues(changedCodes) {
        Object.entries(changedCodes).forEach(([code, value]) => {
            const initialInput = form.querySelector(`.initial-value[data-code="${code}"]`);
            if (initialInput) {
                initialInput.value = value;
            }
        });
    }

    function initAutoSendRadioSync() {
        bindAutoSendRadioEvents(form);
    }

    function bindAutoSendRadioEvents(container) {
        container.querySelectorAll('.auto-send-radio').forEach(radio => {
            const code = radio.dataset.code;
            const value = radio.value;

            // 가입 승인 코드의 발송함 라디오이고 조건 미충족 시 클릭 차단
            if (code === MEMBER_JOIN_APPROVE_CODE && value === 'y' && !useJoinPolicy) {
                radio.addEventListener('click', handleBlockedMemberApproveRadioClick);
                syncAutoSendEnabled(code, 'n');
            } else if (code === FORCE_SEND_CODE && value === 'n') {
                radio.addEventListener('click', handleBlockedForceSendRadioClick);
                syncAutoSendEnabled(code, 'y');
            } else {
                radio.addEventListener('change', () => syncAutoSendEnabled(code, value));
            }
        });
    }

    function handleBlockedMemberApproveRadioClick(e) {
        e.preventDefault();
        NCDSAlert({
            message: '가입승인 절차 또는 연령 제한 설정이 되어 있는 경우 사용할 수 있는 알림입니다.',
            subMessage: '[회원 > 회원 관리 > 회원 가입 정책 관리]에서 가입 설정을 변경 후 다시 시도해 주세요.',
            iconType: 'error'
        });
    }

    function handleBlockedForceSendRadioClick(e) {
        e.preventDefault();
        NCDSAlert({
            message: '선물메시지 알림은 \'발송안함\'으로 변경할 수 없습니다.',
            subMessage: '해당 알림은 선물 수령자 안내를 위한 필수 알림으로, 항상 발송됩니다.',
            iconType: 'error'
        });
    }

    function syncAutoSendEnabled(code, value) {
        document.querySelectorAll(
            `.auto-send-radio[data-code="${code}"]`
        ).forEach(radio => {
            const isChecked = radio.value === value;
            radio.checked = isChecked;
            radio.toggleAttribute('checked', isChecked);

            const label = radio.closest('.ncua-switch__option');
            label?.classList.toggle('ncua-switch__option--active', isChecked);
            label?.classList.toggle('ncua-switch__option--inactive', !isChecked);

            // 테이블 행의 data-send-status 업데이트 (정렬용)
            const row = radio.closest('tr');
            if (row && isChecked) {
                row.dataset.sendStatus = value;
            }
        });
    }

    function onClickAutoSendSearchReset () {
        document.getElementById('list-search-reset').addEventListener('click', () => {
            // 발송 대상 초기화 (첫 번째 값: all)
            const recipientAll = document.querySelector('input[name="recipient"][value="all"]');
            if (recipientAll) {
                recipientAll.checked = true;
            }

            // 카테고리 초기화 (첫 번째 값: all)
            const categoryAll = document.querySelector('input[name="category"][value="all"]');
            if (categoryAll) {
                categoryAll.checked = true;
            }

            // 발송 상태 초기화 (첫 번째 값: all)
            const shouldAutoSendAll = document.querySelector('input[name="shouldAutoSend"][value="all"]');
            if (shouldAutoSendAll) {
                shouldAutoSendAll.checked = true;
            }

            // 초기화 후 검색 실행
            document.getElementById('list-search').click();
        });
    }


    function loadAutoSendList(formData) {
        const loadingModal = window.spinnerModal();
        loadingModal.open();
        $.ajax({
            url: './auto_send/layer_list.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                loadingModal.close();

                const container = document.getElementById('ncuaSearchResult');
                container.innerHTML = data;
                bindAutoSendRadioEvents(container);
                bindSortEvent(container);
                sortAutoSendList('category'); // 기본 정렬: 카테고리순
                syncInitialValuesWithCurrentState();
                window.unsavedGuard?.reset();
            }
        });
    }

    // 카테고리 정렬 순서
    const CATEGORY_ORDER = {
        '주문/배송': 1,
        '정기결제(배송)': 2,
        '선물하기': 3,
        '회원': 4,
        '쿠폰/프로모션': 5,
        '게시판': 6
    };

    function bindSortEvent(container) {
        const sortSelect = container.querySelector('#autoSendListSort');
        if (sortSelect) {
            sortSelect.addEventListener('change', (e) => {
                sortAutoSendList(e.target.value);
            });
        }
    }

    function sortAutoSendList(sortType) {
        const tbody = document.querySelector('#ncuaSearchResult tbody');
        if (!tbody) return;

        const rows = Array.from(tbody.querySelectorAll('tr'));

        rows.sort((a, b) => {
            if (sortType === 'category') {
                // 카테고리순 정렬
                const categoryA = a.dataset.category || '';
                const categoryB = b.dataset.category || '';
                const orderA = CATEGORY_ORDER[categoryA] || 999;
                const orderB = CATEGORY_ORDER[categoryB] || 999;
                return orderA - orderB;
            } else if (sortType === 'sendStatus') {
                // 발송 설정순 정렬 (발송함 'y'가 상단)
                const statusA = a.dataset.sendStatus || 'n';
                const statusB = b.dataset.sendStatus || 'n';
                if (statusA === statusB) return 0;
                return statusA === 'y' ? -1 : 1;
            }
            return 0;
        });

        // DOM에 정렬된 순서로 다시 추가
        rows.forEach(row => tbody.appendChild(row));
    }

    /**
     * 검색 후 현재 라디오 버튼 상태로 초기값 동기화
     */
    function syncInitialValuesWithCurrentState() {
        form.querySelectorAll('.initial-value').forEach(input => {
            const code = input.dataset.code;
            const currentRadio = form.querySelector(`input[name="sendStatus[${code}]"]:checked`);
            if (currentRadio) {
                input.value = currentRadio.value;
            }
        });
    }

    function onClickAutoSendSearch() {
        document.getElementById('list-search').addEventListener('click', () => {
            const formData = new FormData();
            formData.append('recipient', $('input[name="recipient"]:checked').val());
            formData.append('category', $('input[name="category"]:checked').val());
            formData.append('shouldAutoSend', $('input[name="shouldAutoSend"]:checked').val());
            loadAutoSendList(formData);
        });
    }

    /**
     * 자동 알림 리스트 > item > 설정 버튼
     */
    const auto_send_config = (code) => {
        const changedCodes = getChangedAutoSendCodes();
        const loadingModal = window.spinnerModal();

        if (Object.keys(changedCodes).length > 0) {
            NCDSConfirm({
                message: '페이지를 이동하시겠습니까?',
                subMessage: '저장하지 않은 내용이 있습니다.<br/>페이지를 이동하면 설정한 내용이 모두 사라집니다.',
                btnText: {
                    confirmLabel: '이동',
                    cancelLabel: '취소'
                },
                callback: (result) => {
                    if (result) {
                        window.unsavedGuard?.reset();
                        loadingModal.open();
                        const params = new URLSearchParams();
                        params.set('code', code);
                        location.href = `auto_send_config.php?${params}`;
                    }
                }
            });
        } else {
            loadingModal.open();
            const params = new URLSearchParams();
            params.set('code', code);
            location.href = `auto_send_config.php?${params}`;
        }
    }

    /**
     * 자동 알림 리스트 > item > 미리보기 버튼
     */
    const preview_auto_send = (code) => {
        const loadingModal = window.spinnerModal();
        loadingModal.open();

        $.get(`./layer_preview_auto_send.php?code=${encodeURIComponent(code)}`, function (data) {
            loadingModal.close();
            ncds_layer_popup({ message: data, title: '자동 알림 미리보기' , size: 'wide-lg'});
        });
    }

    /**
     * 메시지 포인트 > 포인트 충전 버튼
     */
     const chargeMessagePoints = () => {
        show_popup('./popup_charge_message_points.php');
    }


</script>

