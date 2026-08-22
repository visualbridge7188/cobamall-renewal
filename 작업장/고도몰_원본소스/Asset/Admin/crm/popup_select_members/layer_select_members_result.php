<div class="ncua-search-result js-select-members-result">
    <div class="ncua-search-result__summary">
        <p class="ncua-search-result__summary-count">
            검색 <strong class="search-count"><?= $page->getTotal() ?></strong>개
        </p>
    </div>
    <div class="ncua-search-result__content">
        <!-- 검색 결과 액션 -->
        <div class="ncua-search-result__actions">
            <div class="ncua-button-group ncua-button-group--xs ncua-sequence-btns has-border">
                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray add-all-search-members-btn">
                    검색회원 전체 추가
                </button>
            </div>
            <div class="ncua-search-result__actions-select">
                <span class="ncua-select ncua-select--xs ncua-input-width-150">
                    <span class="ncua-select__content">
                        <?= gd_select_box('sort', 'sort', $sortType, null, $searchFormData['sort'], null, null, 'ncua-select__tag'); ?>
                    </span>
                </span>
                <span class="ncua-select ncua-select--xs">
                    <span class="ncua-select__content">
                        <?= gd_select_box('pageSize', 'pageSize', gd_array_change_key_value([10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200, 300, 500]), '개씩 보기', $searchFormData['pageSize'], null, null, 'ncua-select__tag'); ?>
                    </span>
                </span>
            </div>
        </div>
        <div class="ncua-table ncua-table--horizontal">
            <table class="js-result-table">
                <colgroup>
                    <col width="56px" />
                    <col width="60px" />
                    <col width="170px" />
                    <col />
                    <col />
                    <col width="170px"/>
                    <col width="170px" />
                </colgroup>
                <thead>
                <tr>
                    <th>
                        <div>
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input type="checkbox" name="chkAll" value="all" />
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
                        <td colspan="7">
                            <div class="no-data">검색 내역이 없습니다.</div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="ncua-table-bottom"></div>
    </div>
    <div class="ncua-pagination"><?= $page->getPage('loadContentsByPage(\'PAGELINK\')') ?></div>
</div>
<script type="text/javascript">
    const searchFormData = JSON.parse('<?= json_encode($searchFormData, JSON_UNESCAPED_UNICODE) ?>');

    $(document).ready(function () {
        initializeSelectMembersEvents();
    });

    function initializeSelectMembersEvents() {
        const container = document.querySelector('.js-select-members-result');

        container.querySelector('#sort').addEventListener('change', function () {
            loadContentsByPage('page=1', this.value, null);
        });

        container.querySelector('#pageSize').addEventListener('change', function () {
            loadContentsByPage('page=1', null, this.value);
        });

        container.querySelector('.add-all-search-members-btn').addEventListener('click', function () {
            container.querySelector('input[type=checkbox][name=chkAll]').click();
            document.querySelector('#addMember').click();
        });

        new CheckboxGroup('.js-result-table');
    }

    function loadContentsByPage(page, sort = null, pageSize = null) {
        const formData = new FormData();
        Object.entries(searchFormData).forEach(([key, value]) => {
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

        loadContents(formData);
    }
</script>
