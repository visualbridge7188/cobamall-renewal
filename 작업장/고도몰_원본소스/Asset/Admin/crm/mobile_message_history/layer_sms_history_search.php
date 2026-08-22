<section class="ncua-card__body">
    <form id="frmSearch" method="post" class="js-form-enter-submit">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <colgroup>
                    <col width="240px" />
                    <col />
                    <col width="240px" />
                    <col />
                </colgroup>
                <tbody>
                <tr>
                    <th>
                        <div>발송일</div>
                    </th>
                    <td colspan="3">
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
                        <div class="ncua-flex-gap js-trigger-type">
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input type="checkbox" value="all" checked="checked"/>
                                </span>
                                <span class="ncua-checkbox-field__text">전체</span>
                            </label>
                            <?php foreach ($triggerTypes as $triggerType): ?>
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="triggerTypes[]" value="<?= $triggerType->name ?>" checked="checked" />
                                    </span>
                                    <span class="ncua-checkbox-field__text"><?= $triggerType->value ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <th>
                        <div>발송 구분</div>
                    </th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="smsType" value="" checked="checked"/>
                                </span>
                                <span class="ncua-radio-field__text">전체</span>
                            </label>
                            <?php foreach($smsTypes as $smsType): ?>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="smsType" value="<?= $smsType->name ?>" />
                                    </span>
                                    <span class="ncua-radio-field__text"><?= $smsType->name ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>
                        <div>발송 상태</div>
                    </th>
                    <td colspan="3">
                        <div class="ncua-flex-gap js-send-status">
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input type="checkbox" value="all" checked="checked"/>
                                </span>
                                <span class="ncua-checkbox-field__text">전체</span>
                            </label>
                            <?php foreach ($sendStatuses as $sendStatus): ?>
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="sendStatuses[]" value="<?= $sendStatus->name ?>" checked="checked" />
                                    </span>
                                    <span class="ncua-checkbox-field__text"><?= $sendStatus->value ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>
                        <div>검색어</div>
                    </th>
                    <td colspan="3">
                        <div class="ncua-gap-4">
                            <span class="ncua-select ncua-select--xs">
                                <span class="ncua-select__content">
                                    <?= gd_select_box(null, 'searchType', $searchType, null, array_key_first($searchType), null, null, 'ncua-select__tag'); ?>
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
                <li>'CRM > 메시지 발송'에서 등록한 예약발송 대기건은 <a class="ncua-link" href="./scheduled_send.php">예약 발송 관리</a>에서 확인해 주세요.</li>
                <li>'SMS 개별/전체 발송'에서 등록한 예약발송 대기건은 아래 리스트에서 확인할 수 있습니다.</li>
            </ul>
            <span class="mobile-history-list-search-btns">
                <button type="reset" name="reset" class="ncua-btn ncua-btn--xs has-underline ncua-btn--text">초기화</button>
                <button type="submit" class="ncua-btn ncua-btn--xs ncua-btn--secondary">검색</button>
            </span>
        </div>
    </form>
    <div id="layerSmsHistoryResult"></div>
</section>
<script>
    const DATE_RANGE = {
        minDate: moment().subtract(364, 'days').format(),
        maxDate: null,
    };

    const datePicker = createCRMDatePicker({
        containerId: 'datepicker-container',
        startAttrName: 'dateFrom',
        endAttrName: 'dateTo',
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

            if (!formData.has('triggerTypes[]') || !formData.has('sendStatuses[]')) {
                NCDSAlert({message: '검색 조건을 선택해 주세요.', iconType: 'error'});
                return;
            }

            if (!formData.get('dateFrom') || !formData.get('dateTo')) {
                NCDSAlert({message: '발송일을 선택해 주세요.', iconType: 'error'});
                return;
            }

            loadContents(formData, true);
        });

        form.querySelector('button[type="reset"]')?.addEventListener('click', () => {
            setTimeout(() => {
                Array.from(form.querySelectorAll('#datepicker-container .ncua-button-group__item')).find(el => el.textContent.includes('7'))?.click();

                const dateFromInput = form.querySelector('input[name="dateFrom"]');
                const dateToInput = form.querySelector('input[name="dateTo"]');

                if (!dateFromInput?.value || !dateToInput?.value) {
                    const today = new Date();
                    const sevenDaysAgo = new Date(today.getTime() - 6 * 24 * 60 * 60 * 1000);

                    if (dateFromInput) dateFromInput.value = sevenDaysAgo.toISOString().slice(0, 10);
                    if (dateToInput) dateToInput.value = today.toISOString().slice(0, 10);
                }

                form.querySelector('button[type="submit"]')?.click();
            }, 100);
        });

        ['.js-trigger-type', '.js-send-status'].forEach(selector => new CheckboxGroup(selector));
    }

    function loadContents(formData, isSearch = false) {
        const loadingModal = window.spinnerModal({ message: '생성 중...' });

        if (isSearch) {
            loadingModal.open();
        }

        $.ajax({
            url: './mobile_message_history/layer_sms_history_result.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                $('#layerSmsHistoryResult').html(data);
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
