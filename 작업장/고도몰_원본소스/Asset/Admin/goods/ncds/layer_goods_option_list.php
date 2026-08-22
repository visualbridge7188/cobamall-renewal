<link rel="stylesheet" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/share/layer-goods-option-list.css')?>">
<div class="modal-dialog__content">
<article class="ncua-content">
    <div class="ncua-table ncua-table--vertical">
        <table>
            <tbody>
                <tr>
                    <th><div>검색어</div></th>
                    <td>
                        <div class="ncua-gap-4">
                            <span class="ncua-select ncua-select--xs" style="width:140px;flex:0 0 140px;">
                                <span class="ncua-select__content">
                                    <?php echo gd_select_box('key','key',array('all'=>'=통합검색=','optionManageNm'=>'옵션 관리명','optionName'=>'옵션명'),null,$search['key'], null, null, 'ncua-select__tag');?>
                                </span>
                            </span>
                            <div class="ncua-input ncua-input--xs" style="flex:1;">
                                <div class="ncua-input__content-wrap"><div class="ncua-input__content"><div class="ncua-input__field ncua-input__field--xs">
                                    <input type="text" name="keyword" value="<?=$search['keyword'];?>" placeholder="검색어를 입력하세요" />
                                </div></div></div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><div>옵션 노출 방식</div></th>
                    <td>
                        <div class="ncua-radio-group ncua-flex ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="optionDisplayFl" value="" <?php if (empty($search['optionDisplayFl'])) { echo 'checked'; } ?> />
                                </span>
                                <span><span class="ncua-radio-field__text">전체</span></span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="optionDisplayFl" value="s" <?=gd_isset($checked['optionDisplayFl']['s']);?> />
                                </span>
                                <span><span class="ncua-radio-field__text">일체형</span></span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="optionDisplayFl" value="d" <?=gd_isset($checked['optionDisplayFl']['d']);?> />
                                </span>
                                <span><span class="ncua-radio-field__text">분리형</span></span>
                            </label>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <p class="ncua-btn-group ncua-align-right" style="gap:16px;">
        <button type="button" class="ncua-btn ncua-btn--xs has-underline ncua-btn--text" onclick="layer_list_reset();"><span class="ncua-btn__label">초기화</span></button>
        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary" onclick="layer_list_search();"><span class="ncua-btn__label">검색</span></button>
    </p>

    <div class="ncua-search-result">
        <div class="ncua-search-result__summary">
            <p class="ncua-search-result__summary-count">검색 <strong><?= number_format(gd_isset($page->recode['total'], 0)); ?></strong>개 / 전체 <strong class="text-danger"><?= number_format(gd_isset($page->recode['amount'], 0)); ?></strong>개</p>
        </div>
    </div>

    <div class="ncua-freq-list__table-wrap">
        <form id="frm_layer_goods_option">
            <input type="hidden" name="mode" value="apply_goods_option">
            <div class="ncua-table ncua-table--horizontal ncua-table--rounded">
                <table>
                    <colgroup>
                        <col width="56px"/>
                        <col width="80px"/>
                        <col width="200px"/>
                        <col width="100px"/>
                        <col width="120px"/>
                        <col width="*"/>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>
                            <div style="justify-content:center;">
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" id="optLoadFreqCheckAll"/>
                                    </span>
                                </label>
                            </div>
                        </th>
                        <th><div style="justify-content:center;">번호</div></th>
                        <th><div style="justify-content:center;">옵션 관리명</div></th>
                        <th><div style="justify-content:center;">옵션표시</div></th>
                        <th><div style="justify-content:center;">옵션갯수</div></th>
                        <th><div style="justify-content:center;">옵션명</div></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $hasData = is_array($data) && count($data) > 0;
                    if ($hasData) {
                        $arrOptionDisplay = array('s' => '일체형', 'd' => '분리형', 'm' => '멀티형');
                        foreach ($data as $key => $val) {
                            $arrOptionName = explode(STR_DIVISION, $val['optionName']);
                    ?>
                    <tr>
                        <td>
                            <div style="justify-content:center;">
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" value="<?=$val['sno'];?>" name="sno[]" data-option-display="<?=$val['optionDisplayFl']?>"/>
                                    </span>
                                </label>
                            </div>
                        </td>
                        <td><div style="justify-content:center;"><?=number_format($page->recode['start'] + $key + 1);?></div></td>
                        <td><div><?=gd_htmlspecialchars($val['optionManageNm']);?></div></td>
                        <td><div style="justify-content:center;"><?=$arrOptionDisplay[$val['optionDisplayFl']];?></div></td>
                        <td><div style="justify-content:center;"><?=gd_count($arrOptionName);?></div></td>
                        <td><div style="justify-content:center;"><?=gd_htmlspecialchars(str_replace(STR_DIVISION, ', ', $val['optionName']));?></div></td>
                    </tr>
                    <?php
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="6"><div style="justify-content:center;"><?= empty($search['keyword']) && empty($search['optionDisplayFl']) ? '등록된 자주쓰는 옵션이 없습니다.' : '검색 결과가 없습니다.' ?></div></td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php if ($hasData) { ?>
                <div class="ncua-freq-list__footer-bar"></div>
                <?php } ?>
            </div>
        </form>
    </div>

    <?php if ($hasData) { ?>
    <div class="ncua-search-result-pagination">
        <div class="ncua-pagination">
            <?php echo $page->getPage('layer_list_search(\'PAGELINK\')'); ?>
        </div>
    </div>
    <?php } ?>

    <div class="ncua-notice-info" style="margin-top:8px;">
        "일체형/분리형" 옵션을 함께 선택한 경우 등록이 불가능합니다.<br/>
        선택된 옵션의 "옵션갯수"의 총 합계가 5개를 초과한 경우 등록이 불가능합니다.
    </div>
</article>
</div>

<div class="board-template-write-footer modal-dialog__footer">
    <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray js-cancel">
        <span class="ncua-btn__label">취소</span>
    </button>
    <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--primary js-close">
        <span class="ncua-btn__label">선택 완료</span>
    </button>
</div>

<script type="text/javascript">
    <!--
    $(document).ready(function(){
        var callFunc = '<?= gd_isset($callFunc, '') ?>';

        $('#optLoadFreqCheckAll').click(function(){
            $('input[name*="sno[]"]').prop('checked', $(this).prop('checked'));
        });

        $('input[name*="sno[]"]').click(function(){
            var total = $('input[name*="sno[]"]').length;
            var checked = $('input[name*="sno[]"]:checked').length;
            $('#optLoadFreqCheckAll').prop('checked', total === checked);
        });

        $('.js-cancel').click(function(){
            $('div.bootstrap-dialog-close-button').click();
        });

        $('.js-close').click(function(e){

            if ($('input[name*="sno[]"]:checked').length == 0) {
                NCDSAlert({ message: '선택된 옵션이 없습니다.', iconType: 'error' });
                return false;
            }

            optionValueFill = false;

            var parameters = $("#frm_layer_goods_option").serialize();
            $.post('./goods_ps.php', parameters, function (data) {
                var getData = $.parseJSON(data);

                var msg = [];
                if(getData.displayFl =='') {
                    msg.push("옵션표시 유형이 다른 경우 등록이 불가능합니다.");
                }

                if(getData.optionName.length > 5) {
                    msg.push("선택된 옵션의 옵션개수가 5개를 초과하여 등록이 불가능합니다.");
                }

                if(msg.length > 0) {
                    NCDSAlert({ message: msg.join('<br>'), iconType: 'error' });
                    return false;
                }

                if (callFunc && typeof window[callFunc] === 'function') {
                    // 'deferClose' 반환 콜백은 적용 성공 시점에 스스로 모달을 닫는다 (실패·취소 시 모달 유지)
                    if (window[callFunc](getData) !== 'deferClose') {
                        $('div.bootstrap-dialog-close-button').click();
                    }
                    return;
                }

                var optionCnt = getData.optionName.length;

                if(typeof(option_reset_layer) == "undefined"){
                    option_reset();
                    $('#optionY_optionCnt').val(optionCnt);
                    $("input[name='optionY[optionDisplayFl]'][value='"+getData.displayFl+"']").prop('checked',true);
                    option_setting(optionCnt);

                    for(var i = 0; i < optionCnt; i++) {
                        $('#option_optionName_'+i).val(getData.optionName[i]);
                        if (typeof getData.optionValue != 'undefined' && getData.optionValue != null) {
                            if (typeof getData.optionValue[i] != 'undefined') {
                                var valueCnt = getData.optionValue[i].length;
                                $('#option_optionCnt_' + i).val(valueCnt);
                                option_value_conf(i, valueCnt, true);

                                for (var j = 0; j < valueCnt; j++) {
                                    $('#option_optionValue_' + i + '_' + j).val(getData.optionValue[i][j]);
                                }
                            }
                        }
                    }

                    option_grid();
                }else{
                    option_reset_layer();
                    $('#optionY_optionCnt').val(optionCnt);
                    $("input[name='optionY[optionDisplayFl]'][value='"+getData.displayFl+"']").prop('checked',true);
                    option_setting_layer(optionCnt);

                    for(var i = 0; i < optionCnt; i++) {
                        $('#option_optionName_layer_'+i).val(getData.optionName[i]);
                        if (typeof getData.optionValue != 'undefined' && getData.optionValue != null) {
                            if (typeof getData.optionValue[i] != 'undefined') {
                                var valueCnt = getData.optionValue[i].length;
                                $('#option_optionCnt_' + i).val(valueCnt);
                                option_value_conf_layer(i, valueCnt, true);

                                for (var j = 0; j < valueCnt; j++) {
                                    $('#option_optionValue_' + i + '_' + j).val(getData.optionValue[i][j]);
                                }
                            }
                        }
                    }

                    option_grid_layer('y');
                }

                $('div.bootstrap-dialog-close-button').click();
            });
        });
    });

    function layer_list_search(pagelink)
    {
        var keyStr          = $('#key').val();
        var keyword         = $('input[name=\'keyword\']').val();
        var optionDisplayFl = $('input[name=\'optionDisplayFl\']:checked').val();

        if (typeof optionDisplayFl == 'undefined') {
            optionDisplayFl     = '';
        }

        var parameters  = {
            'key'               : keyStr,
            'keyword'           : keyword,
            'optionDisplayFl'   : optionDisplayFl,
            'scmNo' : '<?=$scmNo?>',
            'callFunc' : '<?= gd_isset($callFunc, '') ?>',
            'pagelink'          : pagelink
        };

        $.get('ncds/layer_goods_option_list.php', parameters, function(data){
            $('#layerOptionListForm').html(data);
        });
    }

    function layer_list_reset()
    {
        var parameters = {
            'scmNo' : '<?=$scmNo?>',
            'callFunc' : '<?= gd_isset($callFunc, '') ?>'
        };

        $.get('ncds/layer_goods_option_list.php', parameters, function(data){
            $('#layerOptionListForm').html(data);
        });
    }

    //-->
</script>
