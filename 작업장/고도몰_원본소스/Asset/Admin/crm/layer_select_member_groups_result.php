<div class="ncua-search-result">
    <div class="ncua-search-result__summary">
        <p class="ncua-search-result__summary-count">
            검색 <strong class="search-count"><?= $groupSearchCount ?></strong>개 /
            전체 <strong class="total-count"><?= $groupTotalCount ?></strong>개
        </p>
    </div>
    <div class="ncua-search-result__content">
        <div class="ncua-table ncua-table--horizontal">
            <table class="js-select-member-groups-result-table">
                <colgroup>
                    <col width="56px" />
                    <col width="70px" />
                    <col />
                    <col width="150px" />
                </colgroup>
                <thead>
                    <tr>
                        <th>
                            <div>
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" value="all" />
                                    </span>
                                </label>
                            </div>
                        </th>
                        <th><div>번호</div></th>
                        <th><div>회원등급명</div></th>
                        <th><div>등록일</div></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if (isset($groups)) {
                    foreach ($groups as $group) { ?>
                        <tr>
                            <td>
                                <div>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="chk[]" data-group-name="<?= htmlspecialchars($group['groupNm']) ?>" value="<?= $group['sno']; ?>" />
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td><div><?= $page->getTotal() - ($page->idx--) + 1 ?></div></td>
                            <td><div><?= htmlspecialchars($group['groupNm']) ?></div></td>
                            <td><div><?= gd_date_format('Y-m-d', $group['regDt']); ?></div></td>
                        </tr>
                    <?php }
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="ncua-table-bottom"></div>
    </div>
</div>
<div class="ncua-pagination"><?= $page->getPage('loadMemberGroupsByPage(\'PAGELINK\')'); ?></div>
<script type="text/javascript">
    const requestData = JSON.parse('<?= json_encode($requestData, JSON_UNESCAPED_UNICODE); ?>');

    $(document).ready(function () {
        initializeSelectMemberGroupsEvents();
    });

    function initializeSelectMemberGroupsEvents() {
        new CheckboxGroup('.js-select-member-groups-result-table');
    }

    async function loadMemberGroupsByPage(pageLink = null) {
        try {
            let page = 1;
            if (pageLink) {
                const pageLinkParams = new URLSearchParams(pageLink);
                page = pageLinkParams.get('page');
            }

            const formData = new FormData();
            Object.entries(requestData).forEach(([key, value]) => {
                if (Array.isArray(value)) {
                    value.forEach(v => formData.append(`${key}[]`, v));
                } else {
                    formData.append(key, value);
                }
            });
            formData.append('pagelink', `page=${page}`);

            await loadMemberGroups(formData);
        } catch (e) {
            logger.error(e);
        }
    }
</script>
