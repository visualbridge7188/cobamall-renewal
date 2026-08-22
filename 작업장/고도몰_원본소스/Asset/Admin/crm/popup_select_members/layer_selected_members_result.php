<article class="ncua-add-members js-selected-members-result">
    <h2 class="ncua-select-members__title">선택 회원 리스트</h2>

    <form id="formSelectedList" method="get">
        <div class="ncua-search-result">
            <div class="ncua-search-result__content">
                <!-- action -->
                <div class="ncua-search-result__actions">
                    <div class="ncua-search-result__summary">
                        <p class="ncua-search-result__summary-count">
                            선택 <strong class="selected-count">0</strong>개
                        </p>
                    </div>
                    <div class="ncua-search-result__actions-select">
                        <span class="ncua-select ncua-select--xs ncua-input-width-150">
                            <span class="ncua-select__content">
                                <?= gd_select_box('sortSelected', 'sortSelected', $sortType, null, $selectedFormData['sort'], null, null, 'ncua-select__tag'); ?>
                            </span>
                        </span>
                        <span class="ncua-select ncua-select--xs ncua-input-width-120">
                            <span class="ncua-select__content">
                                <?= gd_select_box('pageSizeSelected', 'pageSizeSelected', gd_array_change_key_value([10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200, 300, 500]), '개씩 보기', $selectedFormData['pageSize'], null, null, 'ncua-select__tag'); ?>
                            </span>
                        </span>
                    </div>
                </div>
                <div class="ncua-table ncua-table--horizontal">
                    <table class="js-selected-result-table">
                        <colgroup>
                            <col width="56px" />
                            <col width="60px" />
                            <col width="170px" />
                            <col />
                            <col />
                            <col width="170px"/>
                            <col width="170px" />
                        <thead>
                        <tr>
                            <th>
                                <div>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" value="all" />
                                        </span>
                                    </label>
                                </div>
                            </th>
                            <th><div>번호</div></th>
                            <th><div>아이디/닉네임</div></th>
                            <th><div>이름</div></th>
                            <th><div>등급</div></th>
                            <th><div>이메일</div></th>
                            <th><div>휴대폰번호</div></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($members)): ?>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                    <input type="checkbox" name="chk[]" value="<?= $member['memNo']; ?>"
                                                           data-app-fl="<?= $member['appFl'] === 'y' ? 'y' : 'n' ?>"
                                                           data-sms-fl="<?= $member['smsFl'] === 'y' ? 'y' : 'n' ?>"
                                                           data-mailling-fl="<?= $member['maillingFl'] === 'y' ? 'y' : 'n' ?>"/>
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td><div><?= $page->idx-- ?></div></td>
                                    <td>
                                        <div>
                                            <?= htmlspecialchars($member['memId']) ?>
                                            <?php if (!empty($member['snsTypeFl']) && isset($snsIcons[$member['snsTypeFl']])): ?>
                                                <div class="member-icon">
                                                    <img src="<?= $snsIcons[$member['snsTypeFl']]['icon'] ?>" alt="<?= $snsIcons[$member['snsTypeFl']]['name'] ?>" />
                                                </div>
                                            <?php endif; ?>
                                            <p class="color-primary"><?= htmlspecialchars($member['nickNm']) ?></p>
                                        </div>
                                    </td>
                                    <td><div><?= htmlspecialchars($member['memNm']) ?></div></td>
                                    <td><div><?= htmlspecialchars($groups[$member['groupSno']] ?? '') ?></div></td>
                                    <td>
                                        <div>
                                            <a href="mailto:<?= htmlspecialchars($member['email']) ?>"
                                               class="ncua-btn ncua-btn--xs ncua-btn--text-gray has-underline">
                                                <?= htmlspecialchars($member['email']) ?>
                                            </a>
                                            <p class="color-primary">
                                                <?= $member['maillingFl'] === 'y' ? '수신' : '수신거부' ?>
                                            </p>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <?= htmlspecialchars($member['cellPhone']) ?>
                                            <p class="color-primary">
                                                <?= $member['smsFl'] === 'y' ? '수신' : '수신거부' ?>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="no-data">
                                    <div>
                                        <p>왼쪽 회원 리스트에서<br />수신 대상 설정 회원을 선택해주세요.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="ncua-table-bottom"></div>
            </div>
            <div class="ncua-pagination"><?= $page->getPage('loadSelectedContentsByPage(\'PAGELINK\')') ?></div>
        </div>
    </form>
</article>
<script type="text/javascript">
    const selectedFormData = JSON.parse('<?= json_encode($selectedFormData, JSON_UNESCAPED_UNICODE) ?>');

    $(document).ready(function () {
        initializeEvents();
    });

    function initializeEvents() {
        const container = document.querySelector('.js-selected-members-result');

        container.querySelector('#sortSelected').addEventListener('change', function () {
            loadSelectedContentsByPage('page=1', this.value, null);
        });

        container.querySelector('#pageSizeSelected').addEventListener('change', function () {
            loadSelectedContentsByPage('page=1', null, this.value);
        });

        new CheckboxGroup('.js-selected-result-table');
    }

    function loadSelectedContentsByPage(page, sort = null, pageSize = null) {
        const formData = new FormData();
        Object.entries(selectedFormData).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                value.forEach(v => formData.append(`${key}[]`, v));
            } else {
                formData.append(key, value);
            }
        });

        const [key, value] = page.split('=');
        formData.append(key, value);

        if (sort) formData.append('sort', sort);
        if (pageSize) formData.append('pageSize', pageSize);

        loadSelectedContents(formData);
    }
</script>
