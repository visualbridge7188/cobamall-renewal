<div class="modal-dialog__content">
    <article class="ncua-content">
        <div class="ncua-table ncua-table--vertical" id="layer_brand_table">
            <table class="ncua-layer-brand-table">
                <tr>
                    <th><div>브랜드명</div></th>
                    <td>
                        <div>
                            <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input name="cateNm" type="text" value="<?php echo $search['cateNm']; ?>" placeholder="검색어를 입력하세요." />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><div>브랜드 선택</div></th>
                    <td>
                        <div class="ncua-select-group ncua-gap-4 ncua-flex-wrap">
                            <?php echo $brand->getMultiCategoryBox(null, gd_isset($search['brand']), 'class="ncua-select__tag"'); ?>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <p class="ncua-btn-group ncua-align-right">
            <button type="reset" class="ncua-btn ncua-btn--xs has-underline ncua-btn--text" id="btn_reset_search">초기화</button>
            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary" onclick="layer_list_search('PAGELINK');">검색</button>
        </p>

        <div class="ncua-search-result">
            <div class="ncua-search-result__summary">
                <p class="ncua-search-result__summary-count">검색 <strong><?= number_format(gd_isset($page->recode['total'], 0)); ?></strong>개 / 전체 <strong class="text-danger"><?= number_format(gd_isset($page->recode['amount'], 0)); ?></strong>개</p>
            </div>

            <div class="ncua-search-result__content">
                <div class="ncua-table ncua-table--horizontal ncua-table--rounded">
                    <table>
                        <colgroup>
                            <col width="56px">
                            <col width="70px">
                            <?php if ($gGlobal['isUse'] === true) { ?><col width="10%"><?php } ?>
                            <col width="*">
                            <col width="20%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>
                                    <div>
                                        <?php if ($mode == 'radio') { ?>선택 <?php } else { ?>
                                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                <input type="checkbox" id="allCheck" value="y" onclick="check_toggle(this.id,'layer_brand_');">
                                            </span>
                                        </label>
                                        <?php } ?>
                                    </div>
                                </th>
                                <th><div>번호</div></th>
                                <?php if ($gGlobal['isUse'] === true) { ?><th><div>노출상점</div></th><?php } ?>
                                <th><div>브랜드명</div></th>
                                <th><div>등록일</div></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (gd_isset($data) && is_array($data)) {
                            $i = 0;
                            foreach ($data as $key => $val) {
                                ?>
                                <tr>
                                    <td>
                                        <div>
                                        <?php if ($mode == 'radio' || $mode =='radio-search') { ?>
                                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                    <input type="radio" id="layer_brand_<?php echo $val['cateCd']; ?>" name="layer_brand" value="<?php echo $val['cateCd']; ?>" />
                                                </span>
                                            </label>
                                        <?php } else { ?>
                                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                    <input type="checkbox" id="layer_brand_<?php echo $val['cateCd']; ?>" name="layer_brand_<?php echo $i; ?>" value="<?php echo $val['cateCd']; ?>" />
                                                </span>
                                            </label>
                                        <?php } ?>
                                        </div>
                                    </td>
                                    <td><div><?php echo number_format($page->idx--); ?></div></td>
                                    <?php if ($gGlobal['isUse'] === true) { ?><td><div>
                                        <?php foreach(explode(",",$val['mallDisplay']) as $mallKey => $mallValue) {
                                            if ($useMallList[$mallValue]['domainFl']) { ?>
                                                <span class="js-popover flag flag-16 flag-<?= $useMallList[$mallValue]['domainFl'] ?>" data-content="<?= $useMallList[$mallValue]['mallName'] ?>"></span>
                                            <?php }
                                        }?>
                                    </div></td><?php } ?>
                                    <td>
                                        <div>
                                            <label for="layer_brand_<?php echo $val['cateCd']; ?>" class="hand"><?php echo $brand->getCategoryPosition($val['cateCd'],0,' &gt; ',false,false); ?></label>
                                            <input type="hidden" id="cateNm_<?php echo $val['cateCd']; ?>" value="<?php echo gd_htmlspecialchars($val['cateNm']); ?>"/>
                                        </div>
                                    </td>
                                    <td><div><?php echo gd_date_format('Y-m-d', $val['regDt']); ?></div></td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="<?php echo ($gGlobal['isUse'] === true) ? 5 : 4; ?>" class="no-data"><div>검색된 정보가 없습니다.</div></td>
                            </tr>
                            <?php
                        }
                        ?>

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="ncua-search-result-pagination">
                <div class="ncua-pagination ncua-pagination--pc">
                    <?php echo $page->getPage('layer_list_search(\'PAGELINK\')'); ?>
                </div>
            </div>
        </div>
    </article>
</div>

<div class="board-template-write-footer <?php if($req['mode'] != 'popup') {?>modal-dialog__footer<?php }?>">
    <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray" onclick="layer_close();">취소</button>
    <button type="submit" class="ncua-btn ncua-btn--sm ncua-btn--primary" onclick="select_code();">선택 완료</button>
</div>

<script type="text/javascript">
    <!--


    $(document).ready(function () {

        $('input').keydown(function(e) {
            if (e.keyCode == 13) {
                layer_list_search();
                return false
            }
        });

        // 초기화 버튼 클릭 이벤트
        $('#btn_reset_search').on('click', function(e) {
            e.preventDefault();
            reset_search_form();
            layer_list_search();
        });
    });

    // 검색 폼 초기화 함수
    const reset_search_form = () => {
        const layerBrandTable = document.querySelector('#layer_brand_table');
        if (!layerBrandTable) return;
   
        // 검색어 입력 초기화
        const keywordInput = layerBrandTable.querySelector('input[name="cateNm"]');
        if (keywordInput) {
            keywordInput.value = '';
        }
        
        // 브랜드 선택 초기화
        for (let i = <?= DEFAULT_DEPTH_BRAND;?>; i > 0; i--) {
            const brandSelect = document.querySelector(`#brand${i}`);
            if (brandSelect) {
                brandSelect.value = '';
                brandSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    };

    function layer_list_search(pagelink) {
        var cateNm = $('input[name=\'cateNm\']').val();
        var brand = '';
        for (var i = <?php echo DEFAULT_DEPTH_BRAND;?>; i > 0; i--) {
            if ($('#brand' + i).val()) {
                brand = $('#brand' + i).val();
                break;
            }
        }

        if (typeof pagelink == 'undefined') {
            pagelink = '';
        }
        var parameters = {
            'layerFormID': '<?php echo $layerFormID?>',
            'parentFormID': '<?php echo $parentFormID?>',
            'dataFormID': '<?php echo $dataFormID?>',
            'dataInputNm': '<?php echo $dataInputNm?>',
            'mode': '<?php echo $mode?>',
            'callFunc': '<?php echo $callFunc?>',
            'childRow': '<?php echo $childRow?>',
            'search': '<?php echo $search?>',
            'brand[]': brand,
            'cateNm': cateNm,
            'pagelink': pagelink
        };
        $.get('/share/ncds/layer_brand.php', parameters, function (data) {
            $('#<?php echo $layerFormID?>').html(data);
        });
    }

    function select_code() {
        if ($('input[id*=\'layer_brand_\']:checked').length == 0) {
            NCDSAlert({message: '브랜드를 선택해 주세요.', iconType: 'error'});
            return false;
        }

        var applyGoodsCnt = 0;
        var chkGoodsCnt = 0;
        var resultJson = {
            mode: "<?php echo $mode?>",
            parentFormID: "<?php echo $parentFormID?>",
            dataFormID: "<?php echo $dataFormID?>",
            dataInputNm: "<?php echo $dataInputNm?>",
            childRow: "<?php echo $childRow?>",
            search: '<?php echo $search?>',
            info: []
        };

        $('input[id*=\'layer_brand\']:checked').each(function () {
            var cateCd = $(this).val();
            var cateNm = $('#cateNm_' + cateCd).val();

            if ($('#<?php echo $dataFormID?>_' + cateCd).length == 0) {
                resultJson.info.push({"cateCd": cateCd, "cateNm": cateNm});
                applyGoodsCnt++;
            }
            chkGoodsCnt++;
        });

        if (applyGoodsCnt > 0) {
            <?php if($callFunc) { ?>
            <?=$callFunc?>(resultJson);
            <?php } else { ?>
            displayTemplate(resultJson);
            <?php } ?>

            if (applyGoodsCnt != chkGoodsCnt) {
                NCDSAlert({message: '선택한 ' + chkGoodsCnt + '개의 브랜드 중 ' + applyGoodsCnt + '개의 브랜드가 추가 되었습니다.', iconType: 'success'});
            }

            // 선택된 버튼 div 토글
            if (chkGoodsCnt > 0) {
                $('#' + resultJson.parentFormID).addClass('active');
            } else {
                $('#' + resultJson.parentFormID).removeClass('active');
            }

            $('div.bootstrap-dialog-close-button').click();
        } else {
            NCDSAlert({message: '동일한 브랜드가 이미 존재합니다.', iconType: 'error'});
        }
    }

    function displayTemplate(data) {
        if (data.dataInputNm == '') {
            data.dataInputNm = 'brandCd';
        }

        if (data.info.length > 0 && !$('#' + data.parentFormID).children().is('h5') && data.mode != 'simple') {
            $('#' + data.parentFormID).prepend('<h5>선택된 브랜드</h5>');
        }

        var parentFormCount = $('#' + data.parentFormID+' tr').length;
        
        const hasNoData = $("#" + data.parentFormID + " .tr-no-data").length > 0;
        if (data.info.length > 0 && hasNoData) {
            $("#" + data.parentFormID + " .tr-no-data").remove();
            parentFormCount -= 1;
        }

        if (data.mode == 'radio' || data.mode == 'radio-search') {
            $("#" + data.parentFormID).html('');
            $.each(data.info, function (key, val) {
                var childRow = data.childRow ? Number(data.childRow) : 0;
                var addHtml = "";
                if(data.mode == 'radio-search'){
                    var complied = _.template($('#brandSearchInputTemplate').html());
                } else{
                    var complied = _.template($('#brandInputTemplate').html());
                }
                addHtml += complied({
                    cateNm: val.cateNm,
                    cateCd: val.cateCd,
                    dataFormID: data.dataFormID,
                    dataInputNm: data.dataInputNm,
                    key: key + childRow + parentFormCount
                });

                $("#" + data.parentFormID).append(addHtml);
                if (data.mode == 'radio-search' && data.info.length > 0 && !$('#' + data.parentFormID).children().is('h5')) {
                    $('#' + data.parentFormID).prepend('<h5>선택된 브랜드:</h5>');
                    $('#brandNoneFlText').html('선택한 브랜드 미지정 상품');
                }
            });
        } else if (data.mode == 'checkbox' || data.mode == 'simple' || data.mode == 'search') {
            $.each(data.info, function (key, val) {
                var addHtml = "";

                if (data.mode == 'simple') {
                    var complied = _.template($('#brandSimpleTemplate').html());
                    addHtml += complied({
                        cateNm: val.cateNm,
                        cateCd: val.cateCd,
                        dataFormID: data.dataFormID,
                        dataInputNm: data.dataInputNm,
                        key: key + Number(data.childRow) + parentFormCount
                    });
                } else {
                    var complied = _.template($('#brandSearchTemplate').html());
                    addHtml += complied({
                        cateNm: val.cateNm,
                        cateCd: val.cateCd,
                        dataFormID: data.dataFormID,
                        dataInputNm: data.dataInputNm,
                        key: key + Number(data.childRow)+parentFormCount
                    });
                }

                $("#" + data.parentFormID).append(addHtml);
            });
        }

        //브랜드 미지정 상품이 있을시 체크해제
        /*if($("input[name='brandNoneFl']").length > 0){
            $("input[name='brandNoneFl']").prop("checked", false);
        }*/

    }

    // 폼 초기화
    const resetTableFormElements = (triggerSelector, containerSelector) => {
        const triggerEl = document.querySelector(triggerSelector);
        const tableEl = document.querySelector(containerSelector);

        if (!triggerEl || !tableEl) return;

        triggerEl.addEventListener('click', () => {
            const elements = Array.from(tableEl.querySelectorAll('input, select, textarea'));
            for (const el of elements) {
                if (!el) continue;
                if (el.type === 'checkbox' || el.type === 'radio') {
                    if (el.checked === el.defaultChecked) continue;
                    el.checked = el.defaultChecked;
                    continue;
                }
                if (el.tagName === 'SELECT') {
                    let changed = false;
                    for (const option of el.options) {
                        if (option.selected !== option.defaultSelected) {
                            option.selected = option.defaultSelected;
                            changed = true;
                        }
                    }
                    if (!changed) continue;
                    continue;
                }
                if (el.value === el.defaultValue) continue;
                el.value = el.defaultValue;
            }
        });
    };
    resetTableFormElements('#resetBrandForm', '.ncua-layer-brand-table');

    try {
        const brandMultiSelectManager = createNcuaMultiSelectManager({ 
            targetGroups: document.querySelectorAll('.ncua-select-group'),
            sizeClass: 'ncua-select--xs' 
        });
        brandMultiSelectManager.init();
    } catch {
        // ignore
    }
    //-->
</script>

<script type="text/html" id="brandInputTemplate">
    <tr id="<%=dataFormID%>_<%=cateCd%>">
        <input type="hidden" name="<%=dataInputNm%>" value="<%=cateCd%>"/>
        <%
        if($('input[name=' + dataInputNm + 'Nm]').length > 0) {
            $('input[name=' + dataInputNm + 'Nm]').val(cateNm);
            if($('#'+dataInputNm+'Del').length > 0) {
                $('#'+dataInputNm+'Del').show();
            }
        } else {
        %>
        <input type="hidden" name="<%=dataInputNm%>Nm" value="<%=cateNm%>"/>
        <td><div><span class="number"><%=(key + 1)%></span></div></td>
        <td><div class="ncua-left-align"><%=cateNm%></div></td>
        <td><div><button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" data-toggle="delete" data-target="#<%=dataFormID%>_<%=cateCd%>"><span class="ncua-btn__label">삭제</span></button></div></td>
        <% } %>
    </tr>
</script>

<script type="text/html" id="brandSimpleTemplate">
    <tr id="<%=dataFormID%>_<%=cateCd%>">
        <td><div><%=(key + 1)%><input type="hidden" name="<%=dataInputNm%>[]" value="<%=cateCd%>"/>
            <input type="hidden" name="<%=dataInputNm%>Nm[]" value="<%=cateNm%>"/></div></td>
        <td><div class="ncua-left-align"><%=cateNm%></div></td>
        <td><div><button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" data-toggle="delete" data-target="#<%=dataFormID%>_<%=cateCd%>"><span class="ncua-btn__label">삭제</span></button></div></td>
    </tr>
</script>

<script type="text/html" id="brandSearchTemplate">
    <tr id="<%=dataFormID%>_<%=cateCd%>">
        <td>
            <div>
                <input type="hidden" name="<%=dataInputNm%>[]" value="<%=cateCd%>">
                <input type="hidden" name="<%=dataInputNm%>Nm[]" value="<%=cateNm%>">
                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                        <input type="checkbox" data-target="#<%=dataFormID%>_<%=cateCd%>">
                    </span>
                </label>
            </div>
        </td>
        <td><div><span class="number"><%=(key + 1)%></span></div></td>
        <td><div class="ncua-left-align"><%=cateNm%></div></td>
    </tr>
</script>

<script type="text/html" id="brandSearchInputTemplate">
    <tr id="<%=dataFormID%>_<%=cateCd%>">
        <div>
            <input type="hidden" name="<%=dataInputNm%>" value="<%=cateCd%>">
            <input type="hidden" name="<%=dataInputNm%>Nm" value="<%=cateNm%>">
            <td><div><span class="number"><%=(key + 1)%></span></div></td>
            <td><div class="ncua-left-align"><%=cateNm%></div></td>
            <td><div><button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" data-toggle="delete" data-target="#<%=dataFormID%>_<%=cateCd%>" data-none="#brandNoneFlText"><span class="ncua-btn__label">삭제</span></button></div></td>
        </div>
    </tr>
</script>
