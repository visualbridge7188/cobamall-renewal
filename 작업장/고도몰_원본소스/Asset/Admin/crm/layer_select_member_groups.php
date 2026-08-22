<link rel="stylesheet" href="<?= PATH_ADMIN_GD_SHARE ?>ncds/css/crm/layer-select-member-group.css">

<div class="modal-dialog__content">
    <article class="ncua-content select-member-grade">
        <form id="formGroupSearch" method="get">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <colgroup>
                    <col width="144px" />
                    <col />
                </colgroup>
                <tbody>
                    <tr>
                        <th class="ncua-required"><div>회원등급명</div></th>
                        <td>
                            <div class="ncua-gap-8">
                                <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input name="keyword" type="text" value="" placeholder="검색하려는 등급명을 입력하세요" />
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary" onclick="loadMemberGroups()">검색</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        </form>
        <div id="layerSelectMemberGroupsResult"></div>
    </article>
</div>
<div class="modal-dialog__footer">
    <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray" onclick="layer_close()">취소</button>
    <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--primary" onclick="addMemberGroups()">확인</button>
</div>

<script type="text/javascript">
    const formGroupSearch = document.querySelector('#formGroupSearch');

    async function loadMemberGroups(formData = null) {
        try {
            if (!formData) {
                formData = new FormData(formGroupSearch);
            }
            const params = new URLSearchParams(formData).toString();
            const response = await fetch(`./layer_select_member_groups_result.php?${params}`, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP ${response.status}: ${errorText}`);
            }

            $('#layerSelectMemberGroupsResult').html(await response.text());
        } catch (e) {
            logger.error(e);
        }
    }

    function addMemberGroups() {
        const checkedGroups = document.querySelectorAll('#layerSelectMemberGroupsResult input[name="chk[]"]:checked');
        if (!checkedGroups.length) {
            NCDSAlert({message: '등급을 선택해 주세요.', iconType: 'error' });
            return;
        }

        const selectedGroups = Array.from(checkedGroups).map(checkbox => `${checkbox.value}|${checkbox.dataset.groupName}`);
        setSelectedMemberGroups(selectedGroups);
        layer_close();
    }

    loadMemberGroups();
</script>
