<div class="ncua-search-result js-crm-groups-result">
    <div class="ncua-search-result__content">
        <div class="ncua-table ncua-table--horizontal">
            <table>
                <colgroup>
                    <col width="56px" />
                    <col />
                    <col width="170px"/>
                </colgroup>
                <thead>
                    <tr>
                        <th><div class="ncua-align-center">선택</div></th>
                        <th><div>CRM 그룹</div></th>
                        <th><div class="ncua-align-center">그룹 대상 수</div></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($crmGroups)): ?>
                    <?php foreach ($crmGroups as $crmGroup): ?>
                        <tr>
                            <td>
                                <div class="ncua-align-center">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input name="crmGroup" type="radio" value="<?= $crmGroup['no'] ?>" data-crm-group-name="<?= $crmGroup['title'] ?>" data-member-count="<?= $crmGroup['memberCount'] ?>" />
                                </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="crm-group-option">
                                    <p><?= $crmGroup['title'] ?></p>
                                    <ul>
                                        <li id="crm-group-desc-<?= $crmGroup['no'] ?>" class="ncua-notice-info">그룹 추출 조건 없음</li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <div class="ncua-align-center"><?= $crmGroup['memberCount'] ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="no-data" colspan="3">
                            <div>선택할 수 있는 CRM 그룹이 없습니다.</div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="ncua-table-bottom"></div>
    </div>
</div>
<div class="ncua-pagination"><?= $page->getPage('loadCrmGroupContentsByPage(\'PAGELINK\')'); ?></div>

<script type="text/javascript">
    const crmGroupsData = JSON.parse('<?= json_encode($crmGroups, JSON_UNESCAPED_UNICODE) ?>');

    $(document).ready(function () {
        crmGroupsData.forEach(function(group) {
            const target = document.getElementById('crm-group-desc-' + group.no);
            if (target) {
                const description = CrmGroupCondition.getConditionDescription(group.extractCondition);
                if (description.length > 0) {
                    target.innerHTML = description.join("<br />");
                }
            }
        });

        const firstRadio = document.querySelector('input[name="crmGroup"]');
        if (firstRadio) {
            firstRadio.checked = true;
        }

        document.querySelector('button[name="addCrmGroupButton"]').disabled = !crmGroupsData?.length;
    });

    function loadCrmGroupContentsByPage(page) {
        const pageNumber = parseInt(page.split('=')[1], 10);
        loadCrmGroupContents(pageNumber);
    }
</script>
