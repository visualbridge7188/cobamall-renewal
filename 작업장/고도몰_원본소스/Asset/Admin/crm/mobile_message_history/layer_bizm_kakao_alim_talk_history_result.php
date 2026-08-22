<section class="ncua-search-result js-bizm-kakao-alim-talk-history-result">
    <div class="ncua-search-result__summary">
        <p class="ncua-search-result__summary-count">
            검색 <strong><?= $page->getTotal() ?></strong> 개
        </p>
        <div class="ncua-search-result__summary-button">
            <button type="button" id="excelDownload" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" data-type="<?= $excelMenu->name ?>" data-click-target="excelDownload">
                <img src="/admin/gd_share/ncds/image/ico_excel_download.svg" alt="엑셀 다운로드" />
                엑셀 다운로드
            </button>
        </div>
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
                    <col width="48px" />
                    <col width="120px" />
                    <col width="120px" />
                    <col />
                    <col width="257px" />
                    <col width="257px" />
                    <col width="180px" />
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
                    <th>
                        <div>차감 포인트</div>
                    </th>
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
                        <div>발송 건수 (발송성공/발송실패)</div>
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
                                <div>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                <input type="checkbox" name="sendKeys[]" value="<?= $bizmKakaoAlimTalkHistory->getSendKey() ?>" />
                                            </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div><?= empty($bizmKakaoAlimTalkHistory->getUsedPoint()) ? '-' : $bizmKakaoAlimTalkHistory->getUsedPoint() ?></div>
                            </td>
                            <td>
                                <div class="ncua-flex-column">
                                    <?= $bizmKakaoAlimTalkHistory->getTriggerType()->value ?>
                                </div>
                            </td>
                            <td>
                                <div><button class="ncua-left-align mobile-history-list-message-content-btn js-show-message-contents" data-send-key="<?= $bizmKakaoAlimTalkHistory->getSendKey() ?>" type="button"><?= $bizmKakaoAlimTalkHistory->getContent() ?></button></div>
                            </td>
                            <td>
                                <div><?= $bizmKakaoAlimTalkHistory->getReservedAt() ?></div>
                            </td>
                            <td>
                                <div><?= "{$bizmKakaoAlimTalkHistory->getTotalCount()} ({$bizmKakaoAlimTalkHistory->getSuccessCount()}/{$bizmKakaoAlimTalkHistory->getFailCount()})" ?></div>
                            </td>
                            <td>
                                <div class="ncua-flex-column">
                                    <span><?= $bizmKakaoAlimTalkHistory->getSendStatus()->value ?></span>
                                    <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray js-show-history-detail" data-send-key="<?= $bizmKakaoAlimTalkHistory->getSendKey() ?>">상세</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="no-data">
                            <div>
                                <p>발송 내역이 없습니다.
                                <?php if (!$isEnabled): ?>
                                    <br />이 발송수단은 현재 '사용 안함'으로 설정되어 있습니다.<br />
                                    발송을 위해 <a class="ncua-link" href="./message_config.php">메시지 설정</a>에서 사용을 활성화해 주세요.
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
    const excelSearchQuery = JSON.parse('<?= json_encode($excelSearchQuery, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS) ?>');

    $(document).ready(function () {
        initializeEvents();
    });

    function initializeEvents() {
        const container = document.querySelector('.js-bizm-kakao-alim-talk-history-result');

        container.querySelector('button[data-click-target=excelDownload]').addEventListener('click', (e) => {
            const title = `카카오 알림톡 발송내역 다운로드<br/><span style="color: var(--gray-400); font-size: 13px; font-weight: 400;">발송이 완료된 내역만 다운로드됩니다.</span>`;
            const type = e.target.dataset.type;
            const checkedSendKeys = Array.from(container.querySelectorAll('input[name="sendKeys[]"]:checked')).map(input => input.value);

            $.post('./mobile_message_history/layer_message_history_excel_request.php', {
                type: type,
                searchQuery: excelSearchQuery,
                selectedSendKeys: checkedSendKeys
            }, function (data) {
                ncds_layer_popup({title: title, message: data, size: 'wide'});
            }).fail(function (xhr) {
                NCDSAlert({
                    message: xhr.status === 403 ? xhr.responseJSON.message : '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.',
                    iconType: 'error'
                });
            });
        });

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

            const detailBtn = e.target.closest('.js-show-history-detail');
            if (detailBtn) {
                showHistoryDetail(detailBtn.dataset.sendKey);
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
        $.post('./mobile_message_history/layer_kakao_alim_talk_contents.php', {sendGroupKey: sendKey}, function (data) {
            ncds_layer_popup({ message:data, title:'발송 내용' });
        }).fail(function () {
            NCDSAlert({ message: '발송 내용을 불러오는 중 오류가 발생했습니다.', iconType: 'error' });
        });
    }

    function showHistoryDetail(sendKey) {
        $.post('./mobile_message_history/layer_message_history_detail.php', {sendGroupKey: sendKey, type: 'ALIMTALK'}, function (data) {
            ncds_layer_popup({ message: data, title: '발송 내역 상세', size: 'wide' });
        }).fail(function () {
            NCDSAlert({ message: '발송 내역을 불러오는 중 오류가 발생했습니다.', iconType: 'error' });
        });
    }
</script>
