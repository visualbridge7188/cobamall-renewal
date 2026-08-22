<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-history-download.css') ?>" rel="stylesheet"/>
<div class="modal-history-detail">
    <section>
        <form id="frmDetailSearch" method="post" class="js-form-enter-submit">
            <input type="hidden" name="sendGroupKey" value="<?= $sendGroupKey ?>"/>
            <input type="hidden" name="type" value="<?= $type ?>"/>
            <div class="ncua-table ncua-table--vertical">
                <table>
                    <colgroup>
                        <col width="144px" />
                    </colgroup>
                    <tbody>
                        <tr>
                            <th>
                                <div>발송 결과</div>
                            </th>
                            <td>
                                <div class="ncua-flex-gap">
                                    <?php foreach ($messageResultTypes as $messageResultType): ?>
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="resultType" value="<?= $messageResultType->name ?>" <?php if ($messageResultType->name == 'ALL') echo 'checked'; ?> />
                                            </span>
                                            <span class="ncua-radio-field__text"><?= $messageResultType->value ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <div>수신 번호</div>
                            </th>
                            <td>
                                <div>
                                    <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs">
                                                <input type="text" name="keyword" value="" placeholder="수신 번호를 정확히 입력하세요." />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="ncua-btn-group">
                <button class="ncua-btn ncua-btn--xs has-underline ncua-btn--text" type="reset">초기화</button><button class="ncua-btn ncua-btn--xs ncua-btn--secondary" type="submit">검색</button>
            </p>
        </form>
    </section>
    <div id="layerMessageHistoryDetailResult"></div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        initializeEvents();
        loadLayerContents(new FormData(document.getElementById('frmDetailSearch')), 'VIEW');


    });

    function initializeEvents() {
        const form = document.getElementById('frmDetailSearch');
        form.addEventListener('submit', async (e) => {
            e.preventDefault(); // 기본 submit 막기
            loadLayerContents(new FormData(e.target), 'SEARCH');
        });

        form.querySelector('button[type="reset"]')?.addEventListener('click', (e) => {
            setTimeout(() => {
                form.querySelector('button[type="submit"]')?.click();
            }, 100);
        });
    }

    function loadLayerContents(formData, workingType) {
        formData.set('workingType', workingType || 'VIEW');
        $.ajax({
            url: './mobile_message_history/layer_message_history_detail_result.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                $('#layerMessageHistoryDetailResult').html(data);
            },
            error: function () {
                NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', iconType: 'error' });
            },
        });
    }
</script>
