<section class="ncua-search-result js-bizm-kakao-alim-talk-history-result">
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
                        <?= gd_select_box('sort', 'sort', $sort, null, $searchFormData['sort'], null, null, 'ncua-select__tag') ?>
                    </span>
                </span>
                <span class="ncua-select ncua-select--xs">
                    <span class="ncua-select__content">
                        <?= gd_select_box('pageSize', 'pageSize', gd_array_change_key_value([10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200, 300, 500]), '개씩 보기', $searchFormData['pageSize'], null, null, 'ncua-select__tag') ?>
                    </span>
                </span>
            </div>
        </div>

        <div class="ncua-table ncua-table--horizontal">
            <table class="js-result-table">
                <colgroup>
                    <col width="120px" />
                    <col />
                    <col width="257px" />
                    <col width="180px" />
                </colgroup>
                <thead>
                <tr>
                    <th>
                        <div>발송 유형</div>
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
                <?php if (!empty($bizmKakaoAlimTalkHistoryList)): ?>
                    <?php foreach ($bizmKakaoAlimTalkHistoryList as $bizmKakaoAlimTalkHistory): ?>
                        <tr>
                            <td>
                                <div class="ncua-flex-column">
                                    <?= $bizmKakaoAlimTalkHistory->getTriggerType()->value ?>
                                    <?php if ($bizmKakaoAlimTalkHistory->isRepeated()): ?>
                                        <span class="ncua-count"><?= $bizmKakaoAlimTalkHistory->getRepeatRound() ?: 1?>회차</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div><button class="ncua-left-align mobile-history-list-message-content-btn js-show-message-contents" data-send-key="<?= $bizmKakaoAlimTalkHistory->getSendKey() ?>" type="button"><?= $bizmKakaoAlimTalkHistory->getContent() ?></button></div>
                            </td>
                            <td>
                                <div><?= $bizmKakaoAlimTalkHistory->getReservedAt() ?></div>
                            </td>
                            <td>
                                <div class="ncua-flex-column">
                                    <span><?= $bizmKakaoAlimTalkHistory->getSendStatus()->value ?></span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="no-data">
                            <div>
                                <p>발송 내역이 없습니다.
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
        const container = document.querySelector('.js-bizm-kakao-alim-talk-history-result');

        container.querySelector('#sort').addEventListener('change', function () {
            loadContentsByPage('page=1', this.value, null);
        });

        container.querySelector('#pageSize').addEventListener('change', function () {
            loadContentsByPage('page=1', null, this.value);
        });

        container.addEventListener('click', (e) => {
            const messageBtn = e.target.closest('.js-show-message-contents');
            if (messageBtn) {
                showMessageContents(messageBtn.dataset.sendKey);
                return;
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
        formData.append('sender', 'kakaoAlrim');

        if (sort) formData.append('sort', sort);
        if (pageSize) formData.append('pageSize', pageSize);

        loadContents(formData);
    }

    function showMessageContents(sendKey) {
        $.post('/crm/mobile_message_history/layer_kakao_alim_talk_contents.php', {sendGroupKey: sendKey}, function (data) {
            ncds_layer_popup({ message:data, title:'발송 내용' });
        }).fail(function () {
            NCDSAlert({ message: '발송 내용을 불러오는 중 오류가 발생했습니다.', iconType: 'error' });
        });
    }

    function showHistoryDetail(sendKey) {
        $.post('/crm/mobile_message_history/layer_message_history_detail.php', {sendGroupKey: sendKey, type: 'ALIMTALK'}, function (data) {
            ncds_layer_popup({ message: data, title: '발송 내역 상세', size: 'wide' });
        }).fail(function () {
            NCDSAlert({ message: '발송 내역을 불러오는 중 오류가 발생했습니다.', iconType: 'error' });
        });
    }
</script>
