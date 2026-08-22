<?php
/**
 * 정기결제 신청 리스트 검색 폼 레이아웃
 *
 */
?>

<!-- 검색을 위한 form -->
<form id="frmSearchOrder" method="get" class="js-form-enter-submit">
    <input type="hidden" name="detailSearch" value="<?= $search['detailSearch']; ?>"/>
    <input type="hidden" name="view" value="regularOrder"/>
    <input type="hidden" name="applyPath" value="<?=gd_php_self()?>">
    <input type="hidden" name="searchFl" value="y">

    <div class="table-title gd-help-manual">
        정기결제(배송) 신청 검색
        <span class="search"><button type="button" class="btn btn-sm btn-black" onclick="set_search_config(this.form, '<?=$isOrderSearchMultiGrid?>')">검색설정저장</button></span>
    </div>

    <div class="search-detail-box">
        <table class="table table-cols">
            <colgroup>
                <col class="width-md"/>
                <col/>
                <col/>
                <col/>
            </colgroup>
            <tbody>
            <?php if(gd_use_provider() === true) {  ?>
            <?php if (!isset($isProvider) && $isProvider != true) { ?>
            <tr>
                <th>공급사 구분</th>
                <td colspan="3">
                    <label class="radio-inline">
                        <input type="radio" name="scmFl" value="all" <?php if ($search['scmFl'] === 'all') : ?> checked <?php endif;?>/>전체
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="scmFl" value="0" <?php if ($search['scmFl'] === '0') : ?> checked <?php endif;?>/>본사
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="scmFl" value="1" class="js-layer-register" <?php if ($search['scmFl'] === '1') : ?> checked <?php endif;?> data-type="scm" data-mode="checkbox"/> 공급사
                    </label>
                    <input type="button" value="공급사 선택" class="btn btn-sm btn-gray js-layer-register" data-type="scm" data-mode="search"/>

                    <div id="scmLayer" class="selected-btn-group <?=$search['scmFl'] == '1' && !empty($search['scmNo']) ? 'active' : ''?>">
                        <h5>선택된 공급사 : </h5>
                        <?php if ($search['scmFl'] == '1' && empty($search['scmNo']) === false) { ?>
                            <?php foreach ($search['scmNo'] as $k => $v) { ?>
                                <div id="info_scm_<?= $v ?>" class="btn-group btn-group-xs">
                                    <input type="hidden" name="scmNo[]" value="<?= $v ?>"/>
                                    <input type="hidden" name="scmNoNm[]" value="<?= $search['scmNoNm'][$k] ?>"/>
                                    <span class="btn"><?= $search['scmNoNm'][$k] ?></span>
                                    <button type="button" class="btn btn-icon-delete" data-toggle="delete" data-target="#info_scm_<?= $v ?>">삭제</button>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </td>
            </tr>
            <?php } ?>
            <?php } ?>
            <tr>
                <th>검색어</th>
                <td colspan="3">
                    <div class="form-inline">
                        <?= gd_select_box('key', 'key', $search['combineSearch'], null, $search['key'], null, null, 'form-control '); ?>
                        <input type="text" name="keyword" value="<?= $search['keyword']; ?>" class="form-control width-xl"/>
                    </div>
                </td>
            </tr>
            <tr>
                <th>기간검색</th>
                <td colspan="3">
                    <div class="form-inline">
                        <div class="input-group js-datepicker">
                            <input type="text" name="treatDate[]" value="<?= $search['treatDate'][0]; ?>" class="form-control width-xs">
                                <span class="input-group-addon">
                                    <span class="btn-icon-calendar">
                                    </span>
                                </span>
                        </div>
                        ~
                        <div class="input-group js-datepicker">
                            <input type="text" name="treatDate[]" value="<?= $search['treatDate'][1]; ?>" class="form-control width-xs">
                                <span class="input-group-addon">
                                    <span class="btn-icon-calendar">
                                    </span>
                                </span>
                        </div>
                        <?= gd_search_date('', 'treatDate[]', false) ?>
                    </div>
                </td>
            </tr>
            </tbody>
            <tbody class="js-search-detail">
            <tr>
                <th>이용상태</th>
                <td>
                    <div class="dl-horizontal dl-checkbox">
                        <label class="checkbox-inline" style="margin: 0 10px 0 0;">
                            <input type="checkbox" name="applyStatus[]" value="all" class="js-not-checkall" data-target-name="applyStatus[]" <?php if (in_array('all', $search['applyStatus'] ?? [])) : ?> checked <?php endif;?>/> 전체
                        </label>
                        <!-- 이용중 상태 값 모음 -->
                        <?php foreach ($activeStatusLabel as $key => $val) : ?>
                            <?php if ($key === 'active') : ?>
                                <?php foreach ($val as $activeKey => $activeStatus) : ?>
                                    <label class="checkbox-inline" style="margin: 0 10px 0 0;">
                                        <input type="checkbox" name="applyStatus[]" value="<?=$activeKey?>" class="js-apply-status" data-group="applyStatus" <?php if (in_array($activeKey, $search['applyStatus'] ?? [])) : ?> checked <?php endif;?>/> <?= $activeStatus ?>
                                    </label>
                                <?php endforeach; ?>
                            <?php elseif ($key === 'pause') : ?>
                                <label class="checkbox-inline" style="margin: 0 10px 0 0;">
                                    <input type="checkbox" name="applyStatus[]" value="pause" <?php if (in_array('pause', $search['applyStatus'] ?? [])) : ?> checked <?php endif;?> /> 일시정지
                                </label>
                                <?php if (is_array($val)) : ?>
                                    (
                                    <?php foreach ($val as $pauseKey => $pauseStatus) : ?>
                                        <label class="checkbox-inline" style="margin: 0 10px 0 0;">
                                            <input type="checkbox" name="applyStatus[]" value="<?= $pauseKey ?>" class="pause-status" <?php if (in_array($pauseKey, $search['applyStatus'] ?? [])) : ?> checked <?php endif;?> /> <?= $pauseStatus ?>
                                        </label>
                                    <?php endforeach; ?>
                                    )
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <br/>
                        <!-- 해지 상태값 모음 -->
                        <?php foreach ($inactiveStatusLabel as $key => $val) : ?>
                            <label class="checkbox-inline" style="margin: 0 10px 0 0;">
                                <input type="checkbox" name="applyStatus[]" value="inactive" <?php if (in_array('inactive', $search['applyStatus'])) : ?> checked <?php endif;?> /> 해지
                            </label>
                            (
                            <?php foreach ($val as $inactiveKey => $inactiveStatus) : ?>
                                <label class="checkbox-inline" style="margin: 0 10px 0 0;">
                                    <input type="checkbox" name="applyStatus[]" value="<?= $inactiveKey ?>" class="inactive-status" <?php if (in_array($inactiveKey, $search['applyStatus'] ?? [])) : ?> checked <?php endif;?> /> <?= $inactiveStatus ?>
                                </label>
                            <?php endforeach; ?>
                            )
                        <?php endforeach; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <th>배송주기</th>
                <td>
                    <div class="dl-horizontal dl-checkbox">
                        <!-- 배송주기 전체/월/주 -->
                        <label class="checkbox-inline" style="margin: 0 10px 0 0;">
                            <input type="radio" name="deliveryCycleType" value="all" class="js-not-checkall" data-target-name="deliveryCycleType" <?php if ($search['deliveryCycleType'] === 'all') : ?> checked <?php endif;?>/> 전체
                        </label>
                        <?php
                        foreach ($deliveryCycleType as $key => $val) : ?>
                            <label class="checkbox-inline" style="margin: 0 10px 0 0;">
                                <input type="radio" name="deliveryCycleType" value="<?= $key ?>" <?php if ($search['deliveryCycleType'] === $key) : ?> checked <?php endif;?> /> <?= $val ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <!-- 월 주기 모음 -->
                    <div class="dl-horizontal dl-checkbox delivery-cycle-month" <?php if ($search['deliveryCycleType'] !== 'month') : ?> style="display: none" <?php endif; ?>>
                        <label class="checkbox-inline" style="margin: 0 10px 0 0;">
                            <input type="checkbox" name="deliveryCycleMonth[]" value="all" class="js-not-checkall" data-target-name="deliveryCycleMonth[]" <?php if (in_array('all', $search['deliveryCycleMonth'])) : ?> checked <?php endif;?>> 전체
                        </label>
                        <?php foreach ($deliveryCycleMonth as $monthKey => $monthValue) : ?>
                            <label class="checkbox-inline" style="margin: 0 10px 0 0;">
                                <input type="checkbox" name="deliveryCycleMonth[]" value="<?= $monthKey ?>" <?php if (in_array($monthKey, $search['deliveryCycleMonth'] ?? [])) : ?> checked <?php endif;?>/>
                                <?= $monthValue ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <!-- 주 요일 모음-->
                    <div class="dl-horizontal dl-checkbox delivery-cycle-week" <?php if ($search['deliveryCycleType'] !== 'week') : ?> style="display: none"  <?php endif; ?>>
                        <label class="checkbox-inline" style="margin: 0 10px 0 0;">
                            <input type="checkbox" name="deliveryCycleWeek[]" value="all" class="js-not-checkall" data-target-name="deliveryCycleWeek" <?php if (in_array('all', $search['deliveryCycleWeek'])) : ?> checked <?php endif;?>/> 전체
                        </label>
                        <?php foreach ($deliveryCycleWeek as $weekKey => $weekValue ) : ?>
                            <label class="checkbox-inline" style="margin: 0 10px 0 0;">
                                <input type="checkbox" name="deliveryCycleWeek[]" value="<?= $weekKey ?>" <?php if (in_array($weekKey, $search['deliveryCycleWeek'] ?? [])) : ?> checked <?php endif;?> /> <?= $weekValue ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </td>
            </tr>
            <!-- 배송 요일 -->
            <tr <?php if ($search['deliveryCycleType'] !== 'week') : ?> style="display: none" <?php endif; ?> class="delivery-cycle-week">
                <th>배송요일</th>
                <td colspan="3">
                    <div class="dl-horizontal dl-checkbox">
                        <label class="checkbox-inline" style="margin: 0 10px 0 0;">
                            <input type="checkbox" name="deliveryCycleDayWeek[]" value="all" class="js-not-checkall" data-target-name="deliveryCycleDayWeek" <?php if (in_array('all', $search['deliveryCycleDayWeek'])) : ?> checked <?php endif;?> /> 전체
                        </label>
                        <?php
                        foreach ($deliveryCycleWeekDay as $key => $val) : ?>
                            <span>
                                <label class="checkbox-inline" style="margin: 0 10px 0 0;">
                                    <input type="checkbox" name="deliveryCycleDayWeek[]" value="<?= $key ?>" <?php if (in_array($key, $search['deliveryCycleDayWeek'])) : ?> checked <?php endif;?>/> <?= $val ?>요일
                                </label>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-sm btn-link js-search-toggle bold">상세검색 <span>닫힘</span></button>
    </div>
    <div class="table-btn">
        <input type="submit" value="검색" class="btn btn-lg btn-black">
    </div>

    <div class="table-header <?=$tableHeaderClass?>">
        <div class="pull-left">
            검색 <strong class="text-danger"><?= number_format(gd_isset($page->recode['total'], 0)); ?></strong>개 /
            전체 <strong class="text-danger"><?= number_format(gd_isset($page->recode['amount'], 0)); ?></strong>개
        </div>
        <div class="pull-right">
            <div class="form-inline">
                <?= gd_select_box('sort', 'sort', $search['sortList'], null, $search['sort'], null, null, 'form-control'); ?>
                <?= gd_select_box('pageNum', 'pageNum', gd_array_change_key_value($page->page['pageNumList']), '개 보기', $page->page['list']); ?>
                <button type="button" class="js-layer-register btn btn-sm btn-black" style="height: 27px !important;" data-type="order_grid_config" data-order-grid-mode="<?=$orderAdminGridMode?>">조회항목설정</button>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function () {

        // 배송 주기에 따른 토글 과 hide 시 해당 값은 submit 되지 않도록 처리
        $("input[name='deliveryCycleType']").on("change", function () {
            const selected = $("input[name='deliveryCycleType']:checked").val();
            if (selected === "month") {
                $(".delivery-cycle-month").show().find("input").prop("disabled", false);
                $(".delivery-cycle-week").hide().find("input").prop("disabled", true);
            } else if (selected === "week") {
                $(".delivery-cycle-week").show().find("input").prop("disabled", false);
                $(".delivery-cycle-month").hide().find("input").prop("disabled", true);
            } else {
                $(".delivery-cycle-week, .delivery-cycle-month").hide().find("input").prop("disabled", true);
            }
        });

        $("input[name='applyStatus[]']").on("change", function () {
            // "이용중", "일시정지", "해지" 체크박스가 모두 체크되었는지 확인
            const activeChecked = $("input[name='applyStatus[]'][value='active']").is(":checked");
            const pauseChecked = $("input[name='applyStatus[]'][value='pause']").is(":checked");
            const inactiveChecked = $("input[name='applyStatus[]'][value='inactive']").is(":checked");
            const userStopChecked = $("input[name='applyStatus[]'][value='userStop']").is(":checked");
            const systemStopChecked = $("input[name='applyStatus[]'][value='systemStop']").is(":checked");
            const adminStopChecked = $("input[name='applyStatus[]'][value='adminStop']").is(":checked");
            const userInactiveChecked = $("input[name='applyStatus[]'][value='userInactive']").is(":checked");
            const systemInactiveChecked = $("input[name='applyStatus[]'][value='systemInactive']").is(":checked");
            const adminInactiveChecked = $("input[name='applyStatus[]'][value='adminInactive']").is(":checked");
            const roundFinishChecked = $("input[name='applyStatus[]'][value='roundFinish']").is(":checked");

            if (activeChecked && pauseChecked && inactiveChecked) {
                setApplyStatusAllChecked("applyStatus");
            } else {
                unsetApplyStatusAllChecked("applyStatus");
            }

            // "이용중", "일시정지", "해지" 모두 해제되어 있으면 전체 체크박스 선택
            if (!activeChecked && !pauseChecked && !inactiveChecked
                && !userStopChecked && !systemStopChecked && !adminStopChecked
                && !userInactiveChecked && !systemInactiveChecked && !adminInactiveChecked && !roundFinishChecked
            ) {
                setApplyStatusAllChecked("applyStatus");
            }
        });

        // "일시정지" 버튼 클릭 시
        $("input[name='applyStatus[]'][value='pause']").on("change", function () {
            const isChecked = $(this).is(":checked");
            $(".pause-status").prop("checked", isChecked);
        });

        // "해지" 버튼 클릭 시
        $("input[name='applyStatus[]'][value='inactive']").on("change", function () {
            const isChecked = $(this).is(":checked");
            $(".inactive-status").prop("checked", isChecked);
        });

        // 일시정지 하위(고객 일시정지, 시스템 일시정지, 관리자 일시정지)가 모두 체크되면 일시정지 체크박스 체크
        $('input.pause-status').on('change', function () {
            const allChecked = $('input.pause-status').length === $('input.pause-status').filter(':checked').length;
            $('input[type="checkbox"][value="pause"]').prop('checked', allChecked);
        });

        // 해지 하위(고객 해지, 시스템 해지, 관리자 해지, 회차 종료) 가 모두 체크되면 해지 체크박스 체크
        $('input.inactive-status').on('change', function () {
            const allChecked = $('input.inactive-status').length === $('input.inactive-status').filter(':checked').length;
            $('input[type="checkbox"][value="inactive"]').prop('checked', allChecked);
        });

        // 배송주기 월 하위 주기 전체 선택시, 월 전체 체크박스 체크
        $('input[name="deliveryCycleMonth[]"]').not('[value="all"]').on('change', function () {
            // 하위 체크박스 상태에 따라 "전체" 체크박스 상태 변경
            const allChecked = $('input[name="deliveryCycleMonth[]"]').not('[value="all"]').length === $('input[name="deliveryCycleMonth[]"]').not('[value="all"]').filter(':checked').length;
            if (allChecked) {
                setApplyStatusAllChecked("deliveryCycleMonth");
            } else {
                unsetApplyStatusAllChecked("deliveryCycleMonth");
            }
        });

        // 배송주기 주 하위 요일 전체 선택시, 주 전체 체크박스 체크
        $('input[name="deliveryCycleWeek[]"]').not('[value="all"]').on('change', function () {
            const allChecked = $('input[name="deliveryCycleWeek[]"]').not('[value="all"]').length === $('input[name="deliveryCycleWeek[]"]').not('[value="all"]').filter(':checked').length;
            $('input[name="deliveryCycleWeek[]"][value="all"]').prop('checked', allChecked);
            if (allChecked) {
                setApplyStatusAllChecked("deliveryCycleWeek");
            } else {
                unsetApplyStatusAllChecked("deliveryCycleWeek");
            }
        });

        // 배송 요일
        $('input[name="deliveryCycleDayWeek[]"]').not('[value="all"]').on('change', function () {
            const allChecked = $('input[name="deliveryCycleDayWeek[]"]').not('[value="all"]').length === $('input[name="deliveryCycleDayWeek[]"]').not('[value="all"]').filter(':checked').length;
            $('input[name="deliveryCycleDayWeek[]"][value="all"]').prop('checked', allChecked);
            if (allChecked) {
                setApplyStatusAllChecked("deliveryCycleDayWeek");
            } else {
                unsetApplyStatusAllChecked("deliveryCycleDayWeek");
            }
        });

        // 전체 체크박스 체크 및 이외 체크박스 해제
        function setApplyStatusAllChecked(inputName) {
            $(`input[name="${inputName}[]"][value="all"]`).prop('checked', true);
            $(`input[name="${inputName}[]"]`).not('[value="all"]').prop('checked', false);
        }

        // 전체 체크박스 해제
        function unsetApplyStatusAllChecked(inputName) {
            $(`input[name="${inputName}[]"][value="all"]`).prop('checked', false);
        }

    });
</script>
