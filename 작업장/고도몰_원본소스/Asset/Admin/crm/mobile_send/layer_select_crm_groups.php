<link rel="stylesheet" href="<?= PATH_ADMIN_GD_SHARE ?>ncds/css/crm/layer-select-crm-groups.css">
<div class="modal-dialog__content">
    <article class="ncua-content select-crm-groups">
        <?php if ($showCreateGroup === 'y'): ?>
            <div class="create-crm-group">
                <div>
                    <p class="create-crm-group-title">CRM 그룹 생성</p>
                    <span class="create-crm-group-description">원하는 조건을 선택해 내게 꼭 맞는 고객 그룹을 만들어 보세요.</span>
                </div>
                <button id="createCrmGroupPopup" type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">CRM 그룹 생성</button>
            </div>
            <p class="ncua-modal-content-title">CRM 그룹</p>
        <?php endif; ?>
        <div id="crmGroupListContainer"></div>
    </article>
</div>
<div class="modal-dialog__footer">
    <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray">취소</button>
    <button type="submit" name="addCrmGroupButton" class="ncua-btn ncua-btn--sm ncua-btn--primary">확인</button>
</div>
<script type="text/javascript">
    function loadCrmGroupContents(page = 1) {
        $.ajax({
            url: '/crm/mobile_send/layer_select_crm_groups_result.php',
            type: 'GET',
            data: {page: page},
            success: function (data) {
                $('#crmGroupListContainer').html(data);
            }
        });
    }

    $(document).ready(function () {
        loadCrmGroupContents();
        /**
         * CRM 그룹 생성 버튼
         */
        document.querySelector('#createCrmGroupPopup')?.addEventListener('click', async function () {
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

        /**
         * 확인 버튼 클릭 시 선택된 CRM 그룹 전달
         */
        $('button[name="addCrmGroupButton"]').click(function(e) {
            if (this.disabled) return;
            e.preventDefault();

            const selectedRadio = document.querySelector('input[name="crmGroup"]:checked');
            if (!selectedRadio) { return; }

            const crmNo = selectedRadio.value;
            const groupName = selectedRadio.dataset.crmGroupName;

            if (typeof window.setSelectedCrmGroup === 'function') {
                window.setSelectedCrmGroup(crmNo, groupName);
            }

            layer_close();
        });

        /**
         * 취소 버튼 클릭 시 모달 닫기
         */
        $('.modal-dialog__footer button.ncua-btn--secondary-gray').click(function(e) {
            layer_close();
        });
    });
</script>
