<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/share/layer_purchase.css')?>" rel="stylesheet" />
<div class="modal-dialog__content layer_purchase">
    <article class="ncua-content">
        <form id="layer_search_purchase" class="js-form-enter-submit">
            <div class="ncua-table ncua-table--vertical">
                <table>
                    <tbody>
                        <tr>
                            <th><div>검색어</div></th>
                            <td class="ncds-table__cell ncds-table__cell--search-keyword">
                                <div class="ncua-gap-4">
                                    <span class="ncua-select ncua-select--xs">
                                        <span class="ncua-select__content">
                                            <?php echo gd_select_box('key', 'key', $search['combineSearch'], null, $search['key'], null, null, 'ncua-select__tag'); ?>
                                        </span>
                                    </span>
                                    <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs">
                                                <input type="text" name="keyword" value="<?=$search['keyword']; ?>" placeholder="검색어 전체를 정확히 입력하세요." />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>사용상태</div></th>
                            <td>
                                <div class="ncua-radio-group ncua-flex-gap">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="useFl" value="" <?=gd_isset($checked['useFl']['all']);?> />
                                        </span>
                                        <span><span class="ncua-radio-field__text">전체</span></span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="useFl" value="y" <?=gd_isset($checked['useFl']['y']);?> />
                                        </span>
                                        <span><span class="ncua-radio-field__text">사용함</span></span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="useFl" value="n" <?=gd_isset($checked['useFl']['n']);?> />
                                        </span>
                                        <span><span class="ncua-radio-field__text">사용안함</span></span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>거래상태</div></th>
                            <td>
                                <div class="ncua-radio-group ncua-flex-gap">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="businessFl" value="" <?=gd_isset($checked['businessFl']['all']);?> />
                                        </span>
                                        <span><span class="ncua-radio-field__text">전체</span></span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="businessFl" value="y" <?=gd_isset($checked['businessFl']['y']);?> />
                                        </span>
                                        <span><span class="ncua-radio-field__text">거래중</span></span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="businessFl" value="n" <?=gd_isset($checked['businessFl']['n']);?> />
                                        </span>
                                        <span><span class="ncua-radio-field__text">거래중지</span></span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="businessFl" value="x" <?=gd_isset($checked['businessFl']['x']);?> />
                                        </span>
                                        <span><span class="ncua-radio-field__text">거래해지</span></span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="ncua-btn-group ncua-align-right">
                <button type="reset" class="ncua-btn ncua-btn--md has-underline ncua-btn--text">초기화</button>
                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary" onclick="layer_list_search();">검색</button>
            </p>
        </form>

        <div class="ncua-search-result">
            <div class="ncua-search-result__summary">
                <p class="ncua-search-result__summary-count">검색 <strong><?= number_format(gd_isset($page->recode['total'], 0)); ?></strong>개</p>
            </div>

            <div class="ncua-search-result__content">
                <div class="ncua-table ncua-table--horizontal">
                    <table>
                        <colgroup>
                            <col width="56px">
                            <col width="70px">
                            <col width="100px">
                            <col width="120px">
                            <col width="*">
                            <col width="100px">
                            <col width="100px">
                            <col width="120px">
                            <col width="100px">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>
                                    <?php if ($mode == 'radio') { ?>
                                        <div>선택</div>
                                    <?php } else { ?>
                                        <div>
                                            <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                    <input type="checkbox" id="allCheck" value="y" onclick="check_toggle(this.id,'layer_purchase_');"/>
                                                </span>
                                            </label>
                                        </div>
                                    <?php } ?>
                                </th>
                                <th><div>번호</div></th>
                                <th><div>매입처 코드</div></th>
                                <th><div>매입처 자체코드</div></th>
                                <th><div>매입처명</div></th>
                                <th><div>사용상태</div></th>
                                <th><div>거래상태</div></th>
                                <th><div>상품유형</div></th>
                                <th><div>등록일</div></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (gd_isset($data) && is_array($data)) {
                                $businessFl = array('y' => '거래중', 'n' => '거래중지', 'x' => '거래해지');
                                $useFl = array('y' => '사용', 'n' => '사용안함');
                                $i = 0;
                                foreach ($data as $key => $val) {
                                    ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <?php if ($mode == 'radio' || $mode == 'radio-search') { ?>
                                                    <label class="ncua-radio-field ncua-radio-field--xs">
                                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                            <input type="radio" id="layer_purchase_<?=$val['purchaseNo']; ?>" name="layer_purchase" value="<?=$val['purchaseNo']; ?>"/>
                                                        </span>
                                                    </label>
                                                <?php } else { ?>
                                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                            <input type="checkbox" id="layer_purchase_<?=$val['purchaseNo']; ?>" name="layer_purchase_<?=$i; ?>" value="<?=$val['purchaseNo']; ?>"/>
                                                        </span>
                                                    </label>
                                                <?php } ?>
                                            </div>
                                        </td>
                                        <td><div><?=number_format($page->idx--); ?></div></td>
                                        <td><div><?=$val['purchaseNo']?></div></td>
                                        <td><div><?=$val['purchaseCd']?></div></td>
                                        <td>
                                            <div>
                                                <label for="layer_purchase_<?=$val['purchaseNo']; ?>" class="hand"><?=$val['purchaseNm']?></label>
                                                <input type="hidden" id="purchaseNo_<?=$val['purchaseNo']; ?>" value="<?=gd_htmlspecialchars($val['purchaseNm']); ?>"/>
                                            </div>
                                        </td>
                                        <td><div><?=$useFl[$val['useFl']]?></div></td>
                                        <td><div><?=$businessFl[$val['businessFl']]?></div></td>
                                        <td><div><?=$val['category']?></div></td>
                                        <td><div><?=gd_date_format('Y-m-d', $val['regDt']); ?></div></td>
                                    </tr>
                                    <?php
                                    $i++;
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="9" class="ncua-empty-state">
                                        <div class="ncua-empty-state__content">
                                            <p>검색을 이용해 주세요.</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="ncua-pagination">
                <?=$page->getPage('layer_list_search(\'PAGELINK\')'); ?>
            </div>
        </div>

        <div class="ncua-btn-group ncua-align-right">
            <button type="button" class="ncua-btn ncua-btn--md ncua-btn--secondary-gray" onclick="closeLayer();">취소</button>
            <button type="button" class="ncua-btn ncua-btn--md ncua-btn--primary" onclick="select_code();">확인</button>
        </div>
    </article>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('input').keydown(function(e) {
            if (e.keyCode == 13) {
                layer_list_search();
                return false;
            }
        });

        // 초기화 버튼 클릭 시 폼 초기화
        $('button[type="reset"]').on('click', function(e) {
            e.preventDefault();
            
            // 검색어 초기화
            $('#layer_search_purchase input[name="keyword"]').val('');
            
            // 검색 필드 선택박스 초기화
            $('#layer_search_purchase select[name="key"]').prop('selectedIndex', 0);
            
            // 라디오 버튼 초기화 (전체로)
            $('#layer_search_purchase input[name="useFl"][value=""]').prop('checked', true);
            $('#layer_search_purchase input[name="businessFl"][value=""]').prop('checked', true);
            
            // 초기화 후 검색 실행
            layer_list_search();
        });
    });

    function layer_list_search(pagelink) {
        if (typeof pagelink == 'undefined') {
            pagelink = '';
        }

        var frm = $("#layer_search_purchase").serializeArray();
        var parameters = {
            'layerFormID': '<?=$layerFormID?>',
            'parentFormID': '<?=$parentFormID?>',
            'dataFormID': '<?=$dataFormID?>',
            'dataInputNm': '<?=$dataInputNm?>',
            'mode': '<?=$mode?>',
            'callFunc': '<?=$callFunc?>',
            'childRow': '<?=$childRow?>',
            'search': '<?=$search?>',
            'pagelink': pagelink
        };

        $.each(frm, function(i, field){
            if(field.name) parameters[field.name] = field.value;
        });

        $.get('../share/layer_purchase.php', parameters, function (data) {
            $('#<?=$layerFormID?>').html(data);
        });
    }

    function closeLayer() {
        // 모달 닫기 (NCDS 모달인 경우)
        if (typeof window.ncua !== 'undefined' && window.ncua.Modal) {
            const modal = document.querySelector('.ncua-modal');
            if (modal) {
                const modalInstance = modal._modalInstance;
                if (modalInstance && typeof modalInstance.close === 'function') {
                    modalInstance.close();
                }
            }
        }
        // 기존 Bootstrap Dialog 닫기
        $('div.bootstrap-dialog-close-button').click();
    }

    function select_code() {
        if ($('input[id*=\'layer_purchase_\']:checked').length == 0) {
            NCDSAlert({
                message: '매입처를 선택해 주세요!',
                iconType: 'warning'
            });
            return false;
        }

        var applyGoodsCnt = 0;
        var chkGoodsCnt = 0;
        var resultJson = {
            mode: "<?=$mode?>",
            parentFormID: "<?=$parentFormID?>",
            dataFormID: "<?=$dataFormID?>",
            dataInputNm: "<?=$dataInputNm?>",
            childRow: "<?=$childRow?>",
            search: '<?=$search?>',
            info: []
        };

        $('input[id*=\'layer_purchase\']:checked').each(function () {
            var purchaseNo = $(this).val();
            var purchaseNm = $('#purchaseNo_' + purchaseNo).val();

            if ($('#<?=$dataFormID?>_' + purchaseNo).length == 0) {
                resultJson.info.push({"purchaseNo": purchaseNo, "purchaseNm": purchaseNm});
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
                NCDSAlert({
                    message: '선택한 ' + chkGoodsCnt + '개의 매입처 중 ' + applyGoodsCnt + '개의 매입처가 추가 되었습니다.',
                    iconType: 'info'
                });
            }

            // 선택된 버튼 div 토글
            if (chkGoodsCnt > 0) {
                $('#' + resultJson.parentFormID).addClass('active');
            } else {
                $('#' + resultJson.parentFormID).removeClass('active');
            }

            // 모달 닫기
            closeLayer();
        } else {
            NCDSAlert({
                message: '동일한 매입처가 이미 존재합니다.',
                iconType: 'warning'
            });
        }
    }

    function displayTemplate(data) {
        if (data.dataInputNm == '') {
            data.dataInputNm = 'purchaseNo';
        }

        if (data.info.length > 0 && !$('#' + data.parentFormID).children().is('h5') && data.mode != 'simple') {
            $('#' + data.parentFormID).prepend('<h5>선택된 매입처</h5>');
        }

        var parentFormCount = $('#' + data.parentFormID+' tr').length;

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
                    purchaseNm: val.purchaseNm,
                    purchaseNo: val.purchaseNo,
                    dataFormID: data.dataFormID,
                    dataInputNm: data.dataInputNm,
                    key: key + childRow + parentFormCount
                });

                $("#" + data.parentFormID).append(addHtml);
                if (data.mode == 'radio-search' && data.info.length > 0 && !$('#' + data.parentFormID).children().is('h5')) {
                    $('#' + data.parentFormID).prepend('<h5>선택된 매입처:</h5>');
                }
            });
        } else if (data.mode == 'checkbox' || data.mode == 'simple' || data.mode == 'search') {
            $.each(data.info, function (key, val) {
                var addHtml = "";

                if (data.mode == 'simple') {
                    var complied = _.template($('#brandSimpleTemplate').html());
                    addHtml += complied({
                        purchaseNm: val.purchaseNm,
                        purchaseNo: val.purchaseNo,
                        dataFormID: data.dataFormID,
                        dataInputNm: data.dataInputNm,
                        key: key + Number(data.childRow) + parentFormCount
                    });
                } else {
                    var complied = _.template($('#brandSearchTemplate').html());
                    addHtml += complied({
                        purchaseNm: val.purchaseNm,
                        purchaseNo: val.purchaseNo,
                        dataFormID: data.dataFormID,
                        dataInputNm: data.dataInputNm
                    });
                }

                $("#" + data.parentFormID+" h5").after(addHtml);
            });
            $('#purchaseNoneFlText').html('선택한 매입처 미지정 상품');
        }
    }
</script>

<script type="text/html" id="brandInputTemplate">
    <span id="<%=dataFormID%>_<%=purchaseNo%>" class="pull-left">
        <input type="hidden" name="<%=dataInputNm%>" value="<%=purchaseNo%>"/>
        <%
        if($('input[name=' + dataInputNm + 'Nm]').length > 0) {
            $('input[name=' + dataInputNm + 'Nm]').val(purchaseNm);
            if($('#'+dataInputNm+'Del').length > 0) {
                $('#'+dataInputNm+'Del').show();
            }
        } else {
        %>
        <input type="hidden" name="<%=dataInputNm%>Nm" value="<%=purchaseNm%>"/>
        <span class="outline"><b>선택된 매입처</b> <%=purchaseNm%></span>
        <span class="button gray small"><input type="button" class="btn btn-sm btn-gray" data-toggle="delete" data-target="#<%=dataFormID%>_<%=purchaseNo%>" value="삭제"/></span>
        </span>
        <% } %>
</script>

<script type="text/html" id="brandSimpleTemplate">
    <tr id="<%=dataFormID%>_<%=purchaseNo%>">
        <td class="center"><span class="number"><%=(key + 1)%></span><input type="hidden" name="<%=dataInputNm%>[]" value="<%=purchaseNo%>"/>
            <input type="hidden" name="<%=dataInputNm%>Nm[]" value="<%=purchaseNm%>"/></td>
        <td><%=purchaseNm%></td>
        <td class="center">
            <input type="button" class="btn btn-icon-delete" data-toggle="delete" data-target="#<%=dataFormID%>_<%=purchaseNo%>" value="삭제"/>
        </td>
    </tr>
</script>

<script type="text/html" id="brandSearchTemplate">
    <div id="<%=dataFormID%>_<%=purchaseNo%>" class="btn-group btn-group-xs">
        <input type="hidden" name="<%=dataInputNm%>[]" value="<%=purchaseNo%>">
        <input type="hidden" name="<%=dataInputNm%>Nm[]" value="<%=purchaseNm%>">
        <span class="btn"><%=purchaseNm%></span>
        <button type="button" class="btn btn-icon-delete" data-toggle="delete" data-target="#<%=dataFormID%>_<%=purchaseNo%>" data-none="#purchaseNoneFlText">삭제</button>
    </div>
</script>

<script type="text/html" id="brandSearchInputTemplate">
    <div id="<%=dataFormID%>_<%=purchaseNo%>" class="btn-group btn-group-xs">
        <input type="hidden" name="<%=dataInputNm%>" value="<%=purchaseNo%>">
        <input type="hidden" name="<%=dataInputNm%>Nm" value="<%=purchaseNm%>">
        <span class="btn"><%=purchaseNm%></span>
        <button type="button" class="btn btn-icon-delete" data-toggle="delete" data-target="#<%=dataFormID%>_<%=purchaseNo%>">삭제</button>
    </div>
</script>
