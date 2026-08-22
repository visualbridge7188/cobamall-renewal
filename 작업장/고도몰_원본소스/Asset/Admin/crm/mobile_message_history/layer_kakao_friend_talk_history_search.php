<section class="ncua-card__body">
    <form id="frmSearch" method="post" action="" class="js-form-enter-submit">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <colgroup>
                    <col width="240px" />
                    <col />
                </colgroup>
                <tbody>
                    <tr>
                        <th>
                            <div>발송일</div>
                        </th>
                        <td>
                            <div>
                                <div id="datepicker-container"></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div>발송 유형</div>
                        </th>
                        <td>
                            <div class="ncua-flex-gap js-schedule-type">
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" value="all" checked="checked"/>
                                    </span>
                                    <span class="ncua-checkbox-field__text">전체</span>
                                </label>
                                <?php foreach($scheduleTypes as $scheduleType): ?>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="scheduleTypes[]" value="<?= $scheduleType->name ?>" checked="checked"/>
                                        </span>
                                        <span class="ncua-checkbox-field__text"><?= $scheduleType->value ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div>발송 상태</div>
                        </th>
                        <td>
                            <div class="ncua-flex-gap js-send-status">
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" value="all" checked="checked"/>
                                    </span>
                                    <span class="ncua-checkbox-field__text">전체</span>
                                </label>
                                <?php foreach($sendResultStatuses as $sendResultStatus): ?>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="sendResultStatuses[]" value="<?= $sendResultStatus->name ?>" checked="checked"/>
                                        </span>
                                        <span class="ncua-checkbox-field__text"><?= $sendResultStatus->value ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div>검색어</div>
                        </th>
                        <td>
                            <div class="ncua-gap-4">
                                <span class="ncua-select ncua-select--xs">
                                    <span class="ncua-select__content">
                                        <?= gd_select_box(null, 'searchType', $searchType, null, array_key_first($searchType), null, 'disabled=disabled', 'ncua-select__tag'); ?>
                                    </span>
                                </span>
                                <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="keyword" value="" placeholder="검색어를 입력해 주세요" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="ncua-btn-group">
            <ul class="mobile-history-list-info">
                <li>발송 대기 중인 예약발송건은 <a class="ncua-link" href="./scheduled_send.php">예약 발송 관리</a>에서 확인할 수 있습니다.</li>
            </ul>
            <span class="mobile-history-list-search-btns">
                <button type="reset" name="reset" class="ncua-btn ncua-btn--xs has-underline ncua-btn--text">초기화</button>
                <button type="submit" class="ncua-btn ncua-btn--xs ncua-btn--secondary">검색</button>
            </span>
        </div>
    </form>
    <div id="layerKakaoFriendTalkHistoryResult"></div>
</section>

<script>
    const DATE_RANGE = {
        minDate: moment().subtract(364, 'days').format(),
        maxDate: null,
    };
    const datePicker = createCRMDatePicker({
        containerId: 'datepicker-container',
        startAttrName: 'startDate',
        endAttrName: 'endDate',
        startDateOptions: DATE_RANGE,
        endDateOptions: DATE_RANGE
    });
    attachDateValidation(datePicker, {
        maxRecentYear: 1
    });

    $(document).ready(function () {
        initializeEvents();
        loadContents(new FormData(document.getElementById('frmSearch')), true);
    });

    function initializeEvents() {
        const form = document.getElementById('frmSearch');
        form.addEventListener('submit', async (e) => {
            e.preventDefault(); // 기본 submit 막기

            const formData = new FormData(e.target);

            if (!formData.has('scheduleTypes[]') || !formData.has('sendResultStatuses[]')) {
                NCDSAlert({message: '검색 조건을 선택해 주세요.', iconType: 'error'});
                return;
            }

            if (!formData.get('startDate') || !formData.get('endDate')) {
                NCDSAlert({message: '발송일을 선택해 주세요.', iconType: 'error'});
                return;
            }

            loadContents(formData, true);
        });

        form.querySelector('button[type="reset"]')?.addEventListener('click', (e) => {
            setTimeout(() => {
                Array.from(form.querySelectorAll('#datepicker-container .ncua-button-group__item')).find(el => el.textContent.includes('7'))?.click();

                const dateFromInput = form.querySelector('input[name="startDate"]');
                const dateToInput = form.querySelector('input[name="endDate"]');

                if (!dateFromInput?.value || !dateToInput?.value) {
                    const today = new Date();
                    const sevenDaysAgo = new Date(today.getTime() - 6 * 24 * 60 * 60 * 1000);

                    if (dateFromInput) dateFromInput.value = sevenDaysAgo.toISOString().slice(0, 10);
                    if (dateToInput) dateToInput.value = today.toISOString().slice(0, 10);
                }

                form.querySelector('button[type="submit"]')?.click();
            }, 100);
        });

        ['.js-schedule-type', '.js-send-status'].forEach(selector => new CheckboxGroup(selector));
    }

    function loadContents(formData, isSearch = false) {
        const loadingModal = window.spinnerModal({ message: '생성 중...' });

        if (isSearch) {
            loadingModal.open();
        }

        $.ajax({
            url: './mobile_message_history/layer_kakao_friend_talk_history_result.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                $('#layerKakaoFriendTalkHistoryResult').html(data);
            },
            error: function () {
                NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', iconType: 'error' });
            },
            complete: function () {
                if (isSearch) {
                    loadingModal.close();
                }
            }
        });
    }
</script>
