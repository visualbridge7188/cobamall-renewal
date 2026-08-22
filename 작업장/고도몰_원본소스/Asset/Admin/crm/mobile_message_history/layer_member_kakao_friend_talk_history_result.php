<section class="ncua-search-result js-kakao-friend-talk-history-result">
    <div class="ncua-search-result__summary">
        <p class="ncua-search-result__summary-count">
            검색 <strong><?= $page->getTotal() ?></strong> 개
        </p>
    </div>

    <div class="ncua-search-result__content">
        <div class="ncua-search-result__actions">
            <div class="ncua-search-result__actions-select">
                <span class="ncua-select ncua-select--xs">
                    <span class="ncua-select__content">
                        <?= gd_select_box('sort', 'sortType', $sort, null, $searchFormData['sort'], null, null, 'ncua-select__tag'); ?>
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
                    <col width="120px" />
                    <col width="367px" />
                    <col />
                    <col width="180px" />
                    <col width="180px" />
                </colgroup>
                <thead>
                    <tr>
                        <th>
                            <div>발송 유형</div>
                        </th>
                        <th>
                            <div>캠페인 명</div>
                        </th>
                        <th>
                            <div>발송 내용</div>
                        </th>
                        <th>
                            <div>발송 일시</div>
                        </th>
                        <th>
                            <div>발송 상태</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($kakaoFriendTalkHistoryList)): ?>
                    <?php foreach ($kakaoFriendTalkHistoryList as $kakaoFriendTalkHistory): ?>
                        <tr>
                            <td>
                                <div><?= $kakaoFriendTalkHistory->getTriggerType()->value ?></div>
                                <?php if ($kakaoFriendTalkHistory->isRepeated()): ?>
                                    <span class="ncua-count"><?= $kakaoFriendTalkHistory->getRepeatRound() ?: 1 ?>회차</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><?= $kakaoFriendTalkHistory->getCampaignName() ?></div>
                            </td>
                            <td>
                                <div class="ncua-align-items-start"><button class="ncua-left-align mobile-history-list-message-content-btn js-show-message-contents" data-send-history-no="<?= $kakaoFriendTalkHistory->getSendHistoryNo() ?>" type="button"><?= $kakaoFriendTalkHistory->getContent() ?></button></div>
                            </td>
                            <td>
                                <div><?= $kakaoFriendTalkHistory->getReservedAt() ?></div>
                            </td>
                            <td>
                                <div class="ncua-flex-column">
                                    <span><?= $kakaoFriendTalkHistory->getSendStatus()->value ?></span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="no-data">
                            <div><p>발송 내역이 없습니다.
                                <?php if (!$isEnabled): ?>
                                    <br />이 발송수단은 현재 '사용 안함'으로 설정되어 있습니다.<br />
                                    발송을 위해 <a class="ncua-link" href="/crm/message_config.php" target="_blank">메시지 설정</a>에서 사용을 활성화해 주세요.
                                <?php endif; ?>
                                </p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="ncua-table-bottom"></div>
    </div>
    <div class="ncua-pagination"><?= $page->getPage('loadContentsByPage(\'PAGELINK\')') ?></div>
</section>
<script type="text/javascript">
    const searchFormData = JSON.parse('<?= json_encode($searchFormData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS) ?>');

    $(document).ready(function () {
        initializeEvents();
    });

    function initializeEvents() {
        const container = document.querySelector('.js-kakao-friend-talk-history-result');

        container.querySelector('#sort').addEventListener('change', function () {
            loadContentsByPage('page=1', this.value, null);
        });

        container.querySelector('#pageSize').addEventListener('change', function () {
            loadContentsByPage('page=1', null, this.value);
        });

        container.addEventListener('click', (e) => {
            const messageBtn = e.target.closest('.js-show-message-contents');
            if (messageBtn) {
                showMessageContents(messageBtn.dataset.sendHistoryNo);
            }
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

    function showMessageContents(sendHistoryNo) {
        $.post('/crm/mobile_message_history/layer_kakao_friend_talk_contents.php', {sendHistoryNo: sendHistoryNo}, function (data) {
            ncds_layer_popup({ message:data, title:'발송 내용' });
        }).fail(function () {
            NCDSAlert({ message: '발송 내용을 불러오는 중 오류가 발생했습니다.', iconType: 'error' });
        });
    }
</script>
