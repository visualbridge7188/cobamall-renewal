<form id="frmSearchRegularGoods" name="frmSearchRegularGoods" method="get" class="js-form-enter-submit">
    <input type="hidden" name="applyNo" value="<?= $search['applyNo']; ?>"/>
    <div class="search-detail-box">
        <input type="hidden" name="detailSearch" value="<?= $search['detailSearch']; ?>"/>
        <table class="table table-cols">
            <colgroup>
                <col class="width-md"/>
                <col/>
                <col/>
                <col/>
            </colgroup>
            <tr>
                <th>공급사 구분</th>
                <td colspan="3">
                    <label class="radio-inline">
                        <input type="radio" name="scmFl" value="all" onclick="$('#scmLayer').html('')"
                            <?= empty($search['scmFl']) || $search['scmFl'] === 'all' ? 'checked="checked"' : ''; ?>/>전체
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="scmFl" value="n" onclick="$('#scmLayer').html('')"
                            <?= $search['scmFl'] === 'n' ? 'checked="checked"' : ''; ?>/>본사
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="scmFl" value="y" onclick="layerRegister('scm','checkbox')"
                            <?= $search['scmFl'] === 'y' ? 'checked="checked"' : ''; ?>/>공급사
                    </label>
                    <label>
                        <button type="button" class="btn btn-sm btn-gray" onclick="layerRegister('scm','checkbox')">공급사 선택
                        </button>
                    </label>

                    <div id="scmLayer"
                         class="selected-btn-group <?= $search['scmFl'] == 'y' && !empty($search['scmNo']) ? 'active' : '' ?>">
                        <h5>선택된 공급사 : </h5>
                        <?php if ($search['scmFl'] == 'y') : ?>
                            <?php foreach ($search['scmNo'] as $k => $scmNo) : ?>
                                <span id="info_scm_<?= $scmNo ?>" class="btn-group btn-group-xs">
                                <input type="hidden" name="scmNo[]" value="<?= $scmNo ?>"/>
                                <input type="hidden" name="scmNoNm[]" value="<?= $search['scmNoNm'][$k] ?>"/>
                                <span class="btn"><?= $search['scmNoNm'][$k] ?></span>
                                <button type="button" class="btn btn-icon-delete" data-toggle="delete"
                                        data-target="#info_scm_<?= $scmNo ?>">삭제</button>
                                </span>
                            <?php endforeach ?>
                        <?php endif ?>
                    </div>
                </td>
            </tr>
            <tr>
                <th>검색어</th>
                <td colspan="3">
                    <div class="form-inline">
                        <?= gd_select_box('key', 'key', $combineSearch, null, $search['key']); ?>
                        <input type="text" name="keyword" class="form-control" value="<?= $search["keyword"]; ?>"/>
                    </div>
                </td>
            </tr>
            <tr>
                <th>기간검색</th>
                <td colspan="3">
                    <div class="form-inline">
                        <select name="searchDateFl" class="form-control">
                            <option value="regDt" <?= empty($search['searchDateFl']) || $search['searchDateFl'] === 'regDt' ? 'selected' : '' ?>>
                                등록일
                            </option>
                            <option value="modDt" <?= $search['searchDateFl'] === 'modDt' ? 'selected' : '' ?>>수정일
                            </option>
                        </select>

                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="searchDateStart"
                                   value="<?= $search["searchDateStart"] ?>"/>
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>
                        ~
                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="searchDateEnd"
                                   value="<?= $search["searchDateEnd"]; ?>"/>
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>
                        <?= gd_search_date($search['searchPeriod']) ?>
                    </div>
                </td>
            </tr>
            </tbody>
            <tbody class="js-search-detail" style="display: none;">
            <tr>
                <th>배송주기</th>
                <td>
                    <div>
                        <label class="radio-inline">
                            <input type="radio" name="deliveryCycleType" value="all"
                                   onclick="deliveryCycleShow('all')" <?= empty($search['deliveryCycleType']) || $search['deliveryCycleType'] === 'all' ? 'checked="checked"' : ''; ?> />전체
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="deliveryCycleType" value="month"
                                   onclick="deliveryCycleShow('month')" <?= $search['deliveryCycleType'] === 'month' ? 'checked="checked"' : ''; ?>/>월 단위
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="deliveryCycleType" value="week"
                                   onclick="deliveryCycleShow('week')" <?= $search['deliveryCycleType'] === 'week' ? 'checked="checked"' : ''; ?>/>주 단위
                        </label>
                    </div>
                    <div class="hidden" id="delivery_cycle_month">
                        <label class="radio-inline">
                            <input type="checkbox" id="deliveryCycleMonthAll"
                                   onclick="toggleCheckboxes('deliveryCycleMonthAll', 'deliveryCycleMonth')"/>전체
                        </label>
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <label class="radio-inline">
                                <input type="checkbox" name="deliveryCycleMonth[]" class="deliveryCycleMonth"
                                       value="<?= $i ?>" <?= is_array($search['deliveryCycleMonth']) && in_array($i, $search['deliveryCycleMonth']) ? 'checked' : '' ?>
                                       onclick="updateSelectAll('deliveryCycleMonthAll', 'deliveryCycleMonth')"/><?= $i ?>개월
                            </label>
                        <?php endfor; ?>
                    </div>
                    <div class="hidden" id="delivery_cycle_week">
                        <div>
                            <label class="radio-inline">
                                <input type="checkbox" id="deliveryCycleWeekAll"
                                       onclick="toggleCheckboxes('deliveryCycleWeekAll', 'deliveryCycleWeek')"/>전체
                            </label>
                            <?php
                            for ($i = 1; $i <= 6; $i++): ?>
                                <label class="radio-inline">
                                    <input type="checkbox" name="deliveryCycleWeek[]" class="deliveryCycleWeek"
                                           value="<?= $i ?>" <?= is_array($search['deliveryCycleWeek']) &&  in_array($i, $search['deliveryCycleWeek']) ? 'checked' : '' ?>
                                           onclick="updateSelectAll('deliveryCycleWeekAll', 'deliveryCycleWeek')"/><?= $i ?>주
                                </label>
                            <?php endfor; ?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>종료회차</th>
                <td>
                    <div>
                        <div style="display: inline-block;">
                            <label class="radio-inline">
                                <input type="radio" name="deliveryRoundsDisplayType" value="all"
                                       onclick="toggleVisibility('max_delivery_rounds', false); resetMaxDeliveryRounds();"
                                    <?= empty($search['deliveryRoundsDisplayType']) || $search['deliveryRoundsDisplayType'] === 'all' ? 'checked="checked"' : ''; ?>/>전체
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="deliveryRoundsDisplayType" value="disabled"
                                       onclick="toggleVisibility('max_delivery_rounds', false); resetMaxDeliveryRounds();"
                                    <?= $search['deliveryRoundsDisplayType'] === 'disabled' ? 'checked="checked"' : ''; ?>/>미설정
                            </label>
                            <label class="radio-inline width-3xs">
                                <input type="radio" name="deliveryRoundsDisplayType" value="abled"
                                       onclick="toggleVisibility('max_delivery_rounds', true)"
                                    <?= $search['deliveryRoundsDisplayType'] === 'abled' ? 'checked="checked"' : ''; ?>/>설정
                            </label>
                        </div>
                        <div class="hidden" id="max_delivery_rounds" style="display: inline-block;">
                            최대회차 :
                            <label class="radio-inline">
                                <input name="maxDeliveryRounds" class="form-control width-2xs"
                                       value="<?= $search['maxDeliveryRounds']; ?>" type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </label>
                            회 이하
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-sm btn-link js-search-toggle bold">상세검색 <span>펼침</span></button>
    </div>

    <div class="table-btn">
        <input type="submit" value="검색" class="btn btn-lg btn-black">
    </div>


    <div class="table-header">
        <div class="pull-right form-inline">
            <?= gd_select_box('sort', 'sort', $sortList, null, $search['sort'], null); ?>
            <?= gd_select_box('pageSizeNum', 'pageSizeNum', gd_array_change_key_value([10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200, 300, 500]), '개 보기', Request::get()->get('pageSizeNum'), null); ?>
        </div>
    </div>
</form>
<script type="text/javascript">
    let search = <?= json_encode($search); ?>;

    $(document).ready(function () {
        // 상품 검색 조건 세팅
        if (search['detailSearch'] !== null && search['detailSearch'] === 'y') {
            searchFormSetting();
        }
    });

    /**
     * 상품 검색 조건 세팅
     */
    function searchFormSetting() {
        // 배송주기 세팅
        if (search['deliveryCycleType'] === 'month') {
            deliveryCycleShow('month');
            updateSelectAll('deliveryCycleMonthAll', 'deliveryCycleMonth');
        } else if (search['deliveryCycleType'] === 'week') {
            deliveryCycleShow('week');
            updateSelectAll('deliveryCycleWeekAll', 'deliveryCycleWeek');
        }

        // 종료회차 세팅
        if (search['deliveryRoundsDisplayType'] === 'abled') {
            toggleVisibility('max_delivery_rounds', true);
        } else {
            toggleVisibility('max_delivery_rounds', false);
        }

    }

    /**
     * 공급사 선택
     * @param typeStr : layer 타입
     * @param mode : 선택 mode(checkbox, radio, ...)
     */
    function layerRegister(typeStr, mode) {
        let addParam = {
            "mode": mode,
            "layerTitle": "공급사 선택"
        };

        if (typeStr == 'scm') {
            $('input:radio[name=scmFl]:input[value=y]').prop("checked", true);
        }

        layer_add_info(typeStr, addParam);
    }

    /**
     * 라디오 버튼에 따라 요소의 표시 여부를 토글하는 함수
     *
     * @param string elementId 토글하고자 하는 요소의 Id
     * @param bool shouldShow 원하는 토글 상태
     */
    function toggleVisibility(elementId, shouldShow) {
        let element = $('#' + elementId);

        if (shouldShow) {
            element.removeClass('hidden');
        } else {
            element.addClass('hidden');
        }
    }

    /**
     * 선택한 배송 주기에 맞추어 추가적인 체크박스를 보여줌 및 필요없는 값 초기화
     *
     * @param string deliveryCycleType 선택한 배송 주기 값('month','week','all')
     */
    function deliveryCycleShow(deliveryCycleType) {
        if (deliveryCycleType === 'month') {
            toggleVisibility('delivery_cycle_month', true);
            toggleVisibility('delivery_cycle_week', false);
            $('#deliveryCycleWeekAll').prop('checked', false);
            $('input[name="deliveryCycleWeek[]"]').prop('checked', false);
        } else if (deliveryCycleType === 'week') {
            toggleVisibility('delivery_cycle_month', false);
            toggleVisibility('delivery_cycle_week', true);
            $('#deliveryCycleMonthAll').prop('checked', false);
            $('input[name="deliveryCycleMonth[]"]').prop('checked', false);
        } else {
            toggleVisibility('delivery_cycle_month', false);
            toggleVisibility('delivery_cycle_week', false);
            $('#deliveryCycleMonthAll').prop('checked', false);
            $('input[name="deliveryCycleMonth[]"]').prop('checked', false);
            $('#deliveryCycleWeekAll').prop('checked', false);
            $('input[name="deliveryCycleWeek[]"]').prop('checked', false);
        }
    }

    /**
     * 종료회차 전체 혹은 미설정 선택 시 maxDeliveryRounds값 초기화
     *
     */
    function resetMaxDeliveryRounds() {
        $('input[name="maxDeliveryRounds"]').val('');
    }

</script>
