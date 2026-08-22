<script type="text/javascript">
    let goodsCodeList = [];
    let goodsList = [];
    let fixGiftRoundsNumArray = [];
    let fieldID = 'conditionTable';
    let fixGiftRoundListId = 'fixGiftRoundList';
    let minPrice = <?=$minPrice?>;
    let sessionScmNo = <?=$sessionScmNo?>;
    let pathAdminGdShare = '<?=PATH_ADMIN_GD_SHARE;?>';
    let data = <?= json_encode($data) ?>;
    let mode = '<?= $mode ?>';

    $(document).ready(function () {
        // 정기결제(배송) 상품 수정시, 화면 세팅
        if (data.length !== 0) {
            $('#prevData').val(JSON.stringify(data));
            $('input[name="mode"]').val('modify');
            modifyFormSetting();
            setPrevData();
        } else {
            $('input[name="mode"]').val('register');
        }

        // 상품 등록,수정 처리
        $("#frmRegularGoods").validate({
            submitHandler: function (form) {
                fixGiftRoundsNumArray.sort((a, b) => a - b);
                $("#fixGiftRoundsNumArray").val(fixGiftRoundsNumArray);

                if (!validationRegularGoods()) {
                    return false;
                }
                if ($('input[name="regularGoods[discountUseFl]"]:checked').val() === 'y' && !validationRegularGoodsPrice()) {
                    return false;
                }
                if (!validationRegularGoodsDelivery()) {
                    return false;
                }
                if ($('input[name="regularGoods[giftPresentUseFl]"]:checked').val() === 'y' && !validationRegularGoodsGift()) {
                    return false;
                }
                form.target = 'ifrmProcess';
                form.submit();
            }
        });
        // 펼침,닫힘 관련 함수
        initDepthToggle(sessionScmNo);

        // 공급사 변경시 초기화 함수
        $('input[name="scmFl"]').on('change', function () {
            // 상품 및 사은품 초기화
            resetSelectedGoodsAndGift()
        });
    });

    /**
     * 정기결제(배송) 상품 수정시, 화면 세팅
     */
    function modifyFormSetting() {
        // 정기결제(배송) 상품 설정 세팅
        goodsList = data['goodsData'];
        settingAddGoods();

        // 정기결제(배송) 상품 할인 설정
        if (data['discountUseFl'] === 'y') {
            toggleVisibility('discount_type_value', true);
            changeDiscountTypeSpan(data['discountType'], false);
        }

        // 정기결제(배송) 상품 배송 설정
        if (data['deliveryCycleType'] === 'month') {
            deliveryCycleShow('month');
            updateSelectAll('deliveryCycleMonthAll', 'deliveryCycleMonth');
        } else if (data['deliveryCycleType'] === 'week') {
            deliveryCycleShow('week');
            updateSelectAll('deliveryCycleWeekAll', 'deliveryCycleWeek');
            updateSelectAll('deliveryCycleWeekDayAll', 'deliveryCycleWeekDay');
        }
        if (data['deliveryRoundsDisplayType'] === 'abled') {
            toggleVisibility('max_delivery_rounds', true);
        }

        // 정기결제(배송) 사은품 설정
        if (data['giftPresentUseFl'] === 'y') {
            giftPresentShow('used');
            giftPresentRoundsShow(data['roundsType']);
            if (data['roundsType'] === 'directInput') {
                giftPresentRoundsShow('fix');
                Object.values(data['fixGiftRoundsNum']).forEach(value => {
                    addFixGiftRound(value, {key: 'Enter'});
                });
            }
            presentConditionTable(data['conditionType']);
            fillConditionTable();
        }
    }

    /**
     * 이전 데이터 세팅 함수
     */
    function setPrevData() {
        // 등록 및 수정 폼
        const form = $("#frmRegularGoods");
        // 폼에서 가져올 태그 종류
        const inputs = form.find("input, select, textarea");

        const formData = {};

        inputs.each(function () {
            const input = $(this);
            // 배열 기호 [] 제거
            const name = input.attr("name")?.replace(/\[\]$/, "");

            if (!name) return;

            // 값 앞뒤 공백 제거
            const value = $.trim(input.val());

            // radio 버튼의 경우 체크된 것만 포함
            if (input.attr("type") === "radio") {

                // 체크되지 않은 값은 무시
                if (!input.prop("checked")) return;

                // radio는 단일 값
                formData[name] = value;
            } else if (input.attr("type") === "checkbox") {
                // 체크박스는 체크된 값만 추가, 체크되지 않은 건 무시
                if (!input.prop("checked")) return;

                if (!formData[name]) {
                    formData[name] = [];
                }
                formData[name].push(value);
            } else {
                // 일반 input, select, textarea 처리

                if (!formData[name]) {
                    formData[name] = [];
                }
                formData[name].push(value);
            }
        });

        // JSON 변환 후 hidden input에 저장
        $("#prevData").val(JSON.stringify(formData));
    }

    /**
     * 사은품 템플릿 세팅 함수
     * @param data  : 템플릿에 세팅할 값
     * @returns {*}
     */
    function createGiftPresentItemTemplate(data) {
        const compiledTemplate = _.template($('#templateGiftPresentItem').html());
        return compiledTemplate(data);
    }

    /**
     * 사은품 정보 채우기
     */
    function fillConditionTable() {
        // giftPresentInfo 배열을 순회하여 조건에 맞는 처리
        for (let i = 1; i < data['giftPresentInfo'].length; i++) {
            presentConditionTableAdd(data['conditionType']);
        }

        // 선물 정보 배열을 순회
        data['giftPresentInfo'].forEach((val, index) => {
            const giftKey = index + 1;

            // 각 선물 테이블의 thead와 tfoot를 보이게 설정
            $('#div_multi_' + giftKey + '_tbl thead').show();
            $('#div_multi_' + giftKey + '_tbl tfoot').show();

            // giftSno 값을 설정
            $('input[name="gift[giftSno][' + giftKey + ']"]').val(val['sno']);

            // 조건 타입에 따른 시작과 종료 값을 설정
            if (data['conditionType'] !== 'unconditional') {
                $('input[name="gift[conditionStart][' + giftKey + ']"]').val(parseInt(val['conditionStart']));
                $('input[name="gift[conditionEnd][' + giftKey + ']"]').val(parseInt(val['conditionEnd']));
            }

            // multiGiftNo가 있는 경우 추가 선물 처리
            if (val['multiGiftNo'] && val['multiGiftNo'].length > 0) {
                val['multiGiftNo'].forEach((mVal, mKey) => {
                    let addHtml = createGiftPresentItemTemplate({
                        index: mKey,
                        giftKey: giftKey,
                        giftNo: mVal['giftNo'],
                        targetNo: mVal['giftNo'],
                        name: mVal['giftNm'],
                        imageHtml: mVal['giftImage'].replace(/\\/g, ''),
                        giftNm: mVal['giftNm']
                    });

                    $('#div_multi_' + giftKey).append(addHtml);
                });

                // multiGiftNo 셀렉트 박스 초기화
                mode_selectbox_reset(`multiGiftNo_${giftKey}`, 'multi');

                // 사은품 갯수가 선택 수량보다 작거나 같을 경우, 사은품 수량 선택
                if (val['selectCnt'] <= val['multiGiftNo'].length) {
                    $(`#multi${giftKey}`).val(val['selectCnt']);
                }

                // giveCnt 값 설정
                $(`input[name="gift[giveCnt][${giftKey}]"]`).val(val['giveCnt']);
            }
        });
    }

    /**
     * 정기결제(배송) 상품 설정 유효성 검사
     * @returns {boolean} 유효성 검사 통과 여부
     */
    function validationRegularGoods() {
        if (mode !== 'modify' && $('input[name="itemGoodsNo[]"]').length < 1) {
            alert("선택된 상품 정보가 없습니다. 정기결제(배송) 상품으로 등록할 일반 상품을 선택해주세요.");
            return false;
        }
        return true;
    }

    /**
     * 정기결제(배송) 상품 할인 설정 유효성 검사
     * @returns {boolean} 유효성 검사 통과 여부
     */
    function validationRegularGoodsPrice() {
        let discountVal = $('input[name="regularGoods[discountValue]"]').val().trim();
        let discountType = $('select[name="regularGoods[discountType]"]').val();
        if (!discountVal) {
            alert("정기결제(배송) 상품 할인 금액을 입력해주세요.");
            return false;
        }
        if (!$.isNumeric(discountVal)) {
            alert("정기결제(배송) 상품 할인 금액에 대해 숫자를 입력해주세요.");
            return false;
        }
        if (parseFloat(discountVal) < 0.00) {
            alert("정기결제(배송) 상품 할인 금액에 대해 0이나 양수인 값을 입력해주세요.");
            return false;
        }

        if (discountType === 'percent' && parseFloat(discountVal) > 100.00) {
            alert("정기결제(배송) 상품 할인율에 대해 100.00이하의 입력해주세요.");
            return false;
        }
        if (discountType === 'fix' && parseFloat(discountVal) > 9999999999) {
            alert("정기결제(배송) 상품 할인가에 대해 9,999,999,999이하의 입력해주세요.");
            return false;
        }
        let regularProductsPriceBelowLimitFl = false;
        $('input[name="itemGoodsPrice[]"]').each(function () {
            // 현재 input 요소의 value 값
            let originPrice = $(this).val();

            if (discountType === 'percent' && parseFloat(originPrice.replace(/,/g, '').replace('원', '')) * ((100.00 - parseFloat(discountVal)) / 100) < minPrice) {
                regularProductsPriceBelowLimitFl = true;
                return false;
            } else if (discountType === 'fix' && parseFloat(originPrice.replace(',', '').replace('원', '')) - parseFloat(discountVal) < minPrice) {
                regularProductsPriceBelowLimitFl = true;
                return false;
            }
        });
        if (regularProductsPriceBelowLimitFl) {
            alert("정기결제 할인을 적용한 상품의 금액이 " + minPrice + "원 이상일 경우에만 정기결제(배송) 상품으로 등록할 수 있습니다.");
            return false;
        }

        return true;
    }

    /**
     * 정기결제(배송) 배송 설정 유효성 검사
     * @returns {boolean} 유효성 검사 통과 여부
     */
    function validationRegularGoodsDelivery() {
        if ($('input[name="regularGoods[deliveryCycleType]"]:checked').val() === 'month' && $('input[name="regularGoodsDeliveryCycle[deliveryCycleMonth][]"]:checked').length < 1) {
            alert("배송 주기를 선택해 주세요.");
            return false;
        } else if ($('input[name="regularGoods[deliveryCycleType]"]:checked').val() === 'week' && $('input[name="regularGoodsDeliveryCycle[deliveryCycleWeek][]"]:checked').length < 1) {
            alert("배송 주기를 선택해 주세요.");
            return false;
        } else if ($('input[name="regularGoods[deliveryCycleType]"]:checked').val() === 'week' && $('input[name="regularGoodsDeliveryCycle[deliveryCycleWeekDay][]"]:checked').length < 1) {
            alert("배송 요일을 선택해 주세요.");
            return false;
        }
        if ($('input[name="regularGoods[deliveryRoundsDisplayType]"]:checked').val() === 'abled') {
            let maxDeliveryRounds = $('input[name="regularGoods[maxDeliveryRounds]"]').val().trim();
            if (maxDeliveryRounds.length <= 0) {
                alert("최대 종료 회차를 입력해주세요.");
                return false;
            } else if (!$.isNumeric(maxDeliveryRounds)) {
                alert("최대 종료 회차에 대해 숫자를 입력해주세요.");
                return false;
            } else if (parseInt(maxDeliveryRounds) < 2 || parseInt(maxDeliveryRounds) > 50) {
                alert("최대 종료 회차는 2회 이상 50회 이하로 입력 가능합니다.");
                return false;
            }
        }

        return true;
    }

    /**
     * 정기결제(배송) 사은품 설정 유효성 검사
     * @returns {boolean} 유효성 검사 통과 여부
     */
    function validationRegularGoodsGift() {
        const isEmpty = value => value.trim().length === 0;

        const conditionTitle = $('input[name="regularGiftPresent[conditionTitle]"]').val().trim();
        if (isEmpty(conditionTitle)) {
            alert("사은품 지급 조건명을 입력해주세요.");
            return false;
        } else if (conditionTitle.length > 250) {
            alert("사은품 지급 조건명은 최대 250자까지 가능합니다.");
            return false;
        }

        if ($('input[name="regularGiftPresent[periodUseFl]"]:checked').val() === 'y') {
            const startDate = $('input[name="regularGiftPresent[startDate]"]').val().trim();
            const endDate = $('input[name="regularGiftPresent[endDate]"]').val().trim();

            if (isEmpty(startDate) || isEmpty(endDate)) {
                alert("사은품 지급 기간을 입력해주세요.");
                return false;
            }

            const isoEndDateStr = endDate.replace(' ', 'T');
            const parsedEndDate = new Date(isoEndDateStr);
            const now = new Date();

            if (parsedEndDate < now) {
                alert("사은품 지급 기간 종료일이 현재 시간보다 과거입니다.");
                return false;
            }
        }

        const roundsType = $('input[name="regularGiftPresent[roundsType]"]:checked').val();
        if (roundsType === 'multiplier') {
            const multiplierNum = $('input[name="regularGiftPresent[multiplierNum]"]').val().trim();
            if (isEmpty(multiplierNum)) {
                alert("사은품 지급 회차를 입력해주세요.");
                return false;
            } else if (!$.isNumeric(multiplierNum)) {
                alert("사은품 지급 회차에 대해 숫자를 입력해주세요.");
                return false;
            } else if (parseInt(multiplierNum) < 1 || parseInt(multiplierNum) > 50) {
                alert("사은품 지급 회차는 1 이상 50 이하로 입력 가능합니다.");
                return false;
            }
        } else if (roundsType === 'directInput') {
            const directInput = $('input[name="regularGiftPresent[fixGiftRoundsNumArray]"]').val().trim();
            if (isEmpty(directInput)) {
                alert("사은품 지급 회차를 입력해주세요.");
                return false;
            }
        }

        const conditionRangeList = [];
        const fieldCnt = $('#' + fieldID).find('tr.giftAddTr:last').attr('id').replace(fieldID + 'No_', '');

        for (let i = 1; i <= fieldCnt; i++) {
            // 해당 id에 맞는 데이터 없으면 건너뜀
            if (!(document.querySelector('#conditionTableNo_' + i))) {
                continue;
            }

            const conditionStart = $('input[name="gift[conditionStart][' + i + ']"]').val().trim();
            const conditionEnd = $('input[name="gift[conditionEnd][' + i + ']"]').val().trim();

            if (isEmpty(conditionStart) || isEmpty(conditionEnd)) {
                alert("구매 상품 수량을 입력해주세요.");
                return false;
            }

            if ($('input[name="regularGiftPresent[conditionType]"]:checked').val() !== 'unconditional') {
                const conditionStartValue = parseInt(conditionStart);
                const conditionEndValue = parseInt(conditionEnd);

                if (isNaN(conditionEndValue)) {
                    continue;
                }

                if (conditionStartValue === 0 || conditionEndValue === 0) {
                    alert("구매 상품 수량은 0을 입력하실 수 없습니다.");
                    return false;
                }

                if (conditionStartValue > 999999999 || conditionEndValue > 999999999) {
                    alert("구매 상품 수량은 999999999 이상을 입력하실 수 없습니다.");
                    return false;
                }

                if (conditionStartValue === conditionEndValue) {
                    alert("사은품 증정 조건의 구매 상품 수량 범위가 중복되었습니다.");
                    return false;
                }

                if (conditionStartValue > conditionEndValue) {
                    alert("사은품 증정 조건의 구매 상품 수량 범위가 반대로 입력되었습니다.");
                    return false;
                }

                // 새 범위가 기존과 겹치는지 확인
                for (const [savedStart, savedEnd] of conditionRangeList) {
                    const isOverlapping = !(conditionEndValue < savedStart || conditionStartValue > savedEnd);
                    if (isOverlapping) {
                        alert("사은품 증정 조건의 구매 상품 수량 범위가 중복되었습니다.");
                        return false;
                    }
                }

                conditionRangeList.push([conditionStartValue, conditionEndValue]);
            }

            const giveCnt = $('input[name="gift[giveCnt][' + i + ']"]').val().trim();
            if (isEmpty(giveCnt)) {
                alert("사은품 지급 수량을 입력해주세요.");
                return false;
            } else if (!$.isNumeric(giveCnt)) {
                alert("사은품 지급 수량에 대해 숫자를 입력해주세요.");
                return false;
            } else if (parseInt(giveCnt) < 1) {
                alert("사은품 지급 수량은 0을 입력하실 수 없습니다.");
                return false;
            } else if (parseInt(giveCnt) > 10000) {
                alert("사은품 지급 수량은 최대 10,000개까지 입력하실 수 있습니다.");
                return false;
            }

            // Validate if a gift is selected
            const multiHtml = $('#div_multi_' + i).html().trim();
            if (multiHtml === '') {
                alert("사은품을 선택해 주세요.");
                return false;
            }
        }

        return true;
    }

    /**
     * 상품 선택
     * @param typeStr
     * @param mode
     */
    function layerRegister(typeStr, mode) {
        var addParam = {
            "mode": mode,
            "layerFormID": "addGoodsForm",
            "parentFormID": "regular_goods",
            "dataFormID": "regular_goods",
            "layerTitle": "정기결제(배송) 상품 선택",
            "dataInputNm": "itemGoodsNo",
            "callFunc": "setAddGoods",
            "scmNo": $('input[name="scmNo"]').val(),
            "scmFl": $('input:radio[name=scmFl]:checked').val()
        };
        layer_add_info(typeStr, addParam);
    }

    /**
     * 공급사 선택
     * @param typeStr
     * @param mode
     */
    function scmLayerRegister(typeStr, mode) {
        var addParam = {
            "mode": mode,
            "layerTitle": "공급사 설정"
        };

        if (typeStr == 'scm') {
            $('input:radio[name=scmFl]:input[value=y]').prop("checked", true);
        }

        layer_add_info(typeStr, addParam);
    }

    /**
     * 상품 필드 및 사은품 초기화
     */
    function resetSelectedGoodsAndGift() {
        // 상품 초기화
        resetSelectedGoods();

        // 사은품 초기화
        const giftPresentUseFl = $('input[name="regularGoods[giftPresentUseFl]"]:checked').val();
        if (giftPresentUseFl === 'y') {
            const conditionType = $('input[name="regularGiftPresent[conditionType]"]:checked').val();
            presentConditionTable(conditionType);
        }
    }

    /**
     * 상품 필드 초기화
     */
    function resetSelectedGoods() {
        goodsList = [];
        goodsCodeList = [];
        const colspan = mode !== 'modify' ? '8' : '7';
        $("#tbl_add_goods_set tbody").html(`
        <tr id="tbl_add_goods_tr_none">
            <td colspan="${colspan}" class="no-data">선택된 상품이 없습니다.</td>
        </tr>
    `);
    }

    /**
     * 선택 상품 삭제
     */
    function deleteOption() {
        let chkCnt = $('input[name="itemGoodsNo[]"]:checked').length;
        if (chkCnt === 0) {
            alert('선택된 상품이 없습니다.');
            return;
        }
        // 선택된 상품이 있는 경우 삭제
        if (chkCnt > 0) {
            $('input[name="itemGoodsNo[]"]:checked').each(function () {
                field_remove('tbl_add_goods_' + $(this).val());
                removeFromGoodsList($(this).val());
            });

            $('input[name="itemGoodsNo[]"]').each(function (idx) {
                $("#addGoodsNumber" + $(this).val()).html(idx + 1);
            });

        }
    }

    /**
     * 상품 리스트 객체에서 제거하는 함수
     * @param goodsNo
     */
    function removeFromGoodsList(goodsNo) {
        const index = goodsCodeList.indexOf(goodsNo);
        if (index !== -1) {
            // goodsCodeList에서 제거
            goodsCodeList.splice(index, 1);

            // goodsList에서 제거
            goodsList = goodsList.filter(item => item.goodsNo !== goodsNo);
        }
    }

    /**
     * 선택된 상품 추가
     * @param frmData
     */
    function setAddGoods(frmData) {
        $.each(frmData.info, function (key, val) {
            if (!goodsCodeList.includes(val['goodsNo'])) {
                goodsCodeList.push(val['goodsNo']);
                goodsList.push(val);
            }
        });

        if (goodsCodeList.length > 500) {
            const excessCount = goodsCodeList.length - 500;

            // 앞에서 excessCount만큼 제거
            goodsCodeList.splice(0, excessCount);

            // 같은 개수만큼 제거
            goodsList.splice(0, excessCount);
        }

        settingAddGoods();
    }

    /**
     * 상품 세팅
     */
    function settingAddGoods() {
        let addHtml = "";

        $.each(goodsList, function (key, val) {
            let stockText = "";
            let totalStock = "";

            if (val.soldOutFl === 'y') {
                stockText = "품절";
            } else {
                stockText = "정상";
            }

            // 상품 재고
            if (val.stockFl === 'n') {
                totalStock = '∞';
            } else {
                totalStock = val.totalStock;
                if (val.totalStock === 0) {
                    stockText = "품절";
                }
            }

            let sortFix;
            let tableCss;
            if (val.sortFix === true) {
                sortFix = "checked = 'checked'";
                tableCss = "style='background:#d3d3d3' class='add_goods_fix'";
            } else {
                sortFix = '';
                tableCss = "class='add_goods_free'";
            }

            addHtml += `
            <tr id="tbl_add_goods_${val.goodsNo}" ${tableCss}>`;
            
            // mode가 'modify'인 경우 체크박스를 숨기되 checked 상태로 설정
            if (mode === 'modify') {
                addHtml += `
                <td class="center" style="display:none;">
                    <input type="hidden" name="itemGoodsNm[]" value="${val.goodsNm}" />
                    <input type="hidden" name="itemGoodsPrice[]" value="${val.goodsPrice}" />
                    <input type="hidden" name="itemScmNm[]" value="${val.scmNm}" />
                    <input type="hidden" name="itemTotalStock[]" value="${val.totalStock}" />
                    <input type="hidden" name="itemSoldOutFl[]" value="${val.soldOutFl}" />
                    <input type="hidden" name="itemStockFl[]" value="${val.stockFl}" />
                    <input type="checkbox" name="itemGoodsNo[]" id="regular_goods_${val.goodsNo}" value="${val.goodsNo}" checked="checked" style="display:none;" />
                </td>`;
            } else {
                addHtml += `
                <td class="center">
                    <input type="hidden" name="itemGoodsNm[]" value="${val.goodsNm}" />
                    <input type="hidden" name="itemGoodsPrice[]" value="${val.goodsPrice}" />
                    <input type="hidden" name="itemScmNm[]" value="${val.scmNm}" />
                    <input type="hidden" name="itemTotalStock[]" value="${val.totalStock}" />
                    <input type="hidden" name="itemSoldOutFl[]" value="${val.soldOutFl}" />
                    <input type="hidden" name="itemStockFl[]" value="${val.stockFl}" />
                    <input type="checkbox" name="itemGoodsNo[]" id="regular_goods_${val.goodsNo}" value="${val.goodsNo}" />
                </td>`;
            }
            
            addHtml += `
                <td class="center number" id="addGoodsNumber${val.goodsNo}">${key + 1}</td>
                <td class="center">${val.image}</td>
                <td>
                    <a href="../goods/goods_register.php?goodsNo=${val.goodsNo}" target="_blank">${val.goodsNm}</a>
                    <input type="hidden" name="targetGoodsNoList[]" value="${val.goodsNo}" />
                    <input type="checkbox" name="sortFix[]" id="layer_sort_fix_${val.goodsNo}" value="${val.goodsNo}" ${sortFix} style="display:none">
                </td>
                <td class="center">${val.goodsPrice}</td>
                <td class="center">${val.scmNm}</td>
                <td class="center">${totalStock}</td>
                <td class="center">${stockText}</td>
            </tr>`;
        });
        $("#tbl_add_goods_set tbody").html(addHtml);
    }

    /**
     * 라디오 버튼에 따라 요소의 표시 여부를 토글하는 함수
     * @param elementId 토글하고자 하는 요소의 Id
     * @param shouldShow 원하는 토글 상태
     */
    function toggleVisibility(elementId, shouldShow) {
        const element = $(`#${elementId}`);

        if (shouldShow) {
            // 'hidden' 클래스 제거
            element.removeClass('hidden');
        } else {
            // 'hidden' 클래스 추가
            element.addClass('hidden');
        }
    }

    /**
     * 전체 체크박스 선택/해제 기능
     * @param checkBoxIdUseAll '전체' 값을 가지고 있는 체크박스 Id
     * @param checkBoxClass '전체'값을 가진 체크박스에 영향을 받는 체크박스들의 class
     */
    function toggle_checkboxes(checkBoxIdUseAll, checkBoxClass) {
        const isChecked = $(`#${checkBoxIdUseAll}`).prop("checked");
        const checkboxes = $(`.${checkBoxClass}`);

        checkboxes.each(function () {
            this.checked = isChecked;
        });
    }

    /**
     * 특정 객체 삭제
     * @param thisID 해당 ID
     */
    function removeObj(thisID) {
        $('#' + thisID).remove();
    }

    /**
     * 개별 체크박스 선택/해제 시 전체 체크박스 상태 변경
     * @param checkBoxIdUseAll '전체' 값을 가지고 있는 체크박스 Id
     * @param checkBoxClass '전체'값을 가진 체크박스에 영향을 받는 체크박스들의 class
     */
    function updateSelectAll(checkBoxIdUseAll, checkBoxClass) {
        const checkboxes = $(`.${checkBoxClass}`);
        const selectAll = $(`#${checkBoxIdUseAll}`);

        // 모든 체크박스가 선택되면 '전체 선택' 체크박스를 체크
        selectAll.prop("checked", Array.from(checkboxes).every(checkbox => checkbox.checked));

        // 일부라도 체크가 해제되면 '전체 선택' 체크박스 체크 해제
        selectAll.prop("indeterminate", !selectAll.prop("checked") && Array.from(checkboxes).some(checkbox => checkbox.checked));
    }

    /**
     * 할인 금액의 유형별 span내 글자 변경
     * @param discountType 할인 방법 유형
     * @param resetFl : discountValue 초기화 여부
     */
    function changeDiscountTypeSpan(discountType, resetFl) {
        if (discountType === 'percent') {
            $('#discountTypeSpan').html('구매금액의');
        } else {
            $('#discountTypeSpan').html('구매수량별');
        }

        if (resetFl) {
            $('[name="regularGoods[discountValue]"]').val('');
        }
    }

    // 입력 값 검증 (정수만 또는 0 이상의 소수 허용)
    function validateDiscountValue(input) {
        // 할인 타입이 'fix'인 경우: 정수만 입력 가능
        if ($('[name="regularGoods[discountType]"]').val() === 'fix') {
            input.value = input.value.replace(/[^0-9]/g, '');
        } else {
            // 할인 타입이 'percent'인 경우: 0 이상의 소수 입력 가능
            input.value = input.value.replace(/[^0-9.]/g, '');

            // 소수점이 하나만 입력되도록 처리
            if ((input.value.match(/\./g) || []).length > 1) {
                input.value = input.value.substring(0, input.value.lastIndexOf('.'));
            }
        }
    }

    /**
     * 선택한 배송 주기에 맞추어 추가적인 체크박스를 보여줌
     * @param deliveryCycleType 선택한 배송 주기 값('month','week','all')
     */
    function deliveryCycleShow(deliveryCycleType) {
        if (deliveryCycleType === 'month') {
            toggleVisibility('delivery_cycle_month', true);
            toggleVisibility('delivery_cycle_week', false);
        } else if (deliveryCycleType === 'week') {
            toggleVisibility('delivery_cycle_month', false);
            toggleVisibility('delivery_cycle_week', true);
        } else {
            toggleVisibility('delivery_cycle_month', false);
            toggleVisibility('delivery_cycle_week', false);
        }
    }

    /**
     * 선택한 사은품 지급 사용여부에 따라 추가적인 표를 보여줌
     * @param giftPresentUseStatus 선택한 사은품 지급 사용여부(used, notUsed)
     */
    function giftPresentShow(giftPresentUseStatus) {
        if (giftPresentUseStatus === 'used') {
            toggleVisibility('gift_present_title', true);
            toggleVisibility('gift_present_period', true);
            toggleVisibility('gift_present_rounds', true);
            toggleVisibility('gift_present_condition', true);
            if ($('#' + fieldID).length === 0) {
                presentConditionTable($('input[name="regularGiftPresent[conditionType]"]:checked').val());
            } else {
                toggleVisibility(fieldID, true);
            }
        } else {
            toggleVisibility('gift_present_title', false);
            toggleVisibility('gift_present_period', false);
            toggleVisibility('gift_present_rounds', false);
            toggleVisibility('gift_present_condition', false);
            toggleVisibility(fieldID, false);
        }
    }

    /**
     * 선택한 사은품 지급 회차 조건에 따라 추가적인 입력칸 노출
     * @param giftPresentRoundstatus 선택한 사은품 지급 사용여부(once, multiplier, fix)
     */
    function giftPresentRoundsShow(giftPresentRoundstatus) {
        if (giftPresentRoundstatus === 'multiplier') {
            toggleVisibility('gift_multiplier_num', true);
            toggleVisibility('gift_fix_num', false);
            toggleVisibility('fixGiftRoundListDiv', false);
        } else if (giftPresentRoundstatus === 'fix') {
            toggleVisibility('gift_multiplier_num', false);
            toggleVisibility('gift_fix_num', true);

            // 직접 입력한 지급회차가 존재할 경우
            if (fixGiftRoundsNumArray.length > 0) {
                toggleVisibility('fixGiftRoundListDiv', true);
            }
        } else {
            toggleVisibility('gift_multiplier_num', false);
            toggleVisibility('gift_fix_num', false);
            toggleVisibility('fixGiftRoundListDiv', false);
        }
    }

    /**
     * 직접 입력을 통해 추가한 지급 회차
     * @param inputVal 숫자가 입력된 input 객체
     * @param event 발생한 이벤트 정보 객체
     */
    function addFixGiftRound(inputVal, event) {
        if (event.key === 'Enter') {
            let addHtml = '';

            let fieldID = 'fixGiftRound';

            let fixGiftRoundCnt = $(`.${fieldID}`).length;
            if (fixGiftRoundCnt >= <?= $maxFixGiftRound ?>) {
                alert('사은품 지급 회차는 <?= $maxFixGiftRound ?>개까지만 추가하실 수 있습니다.');
                return false;
            }
            if (inputVal.length <= 0) {
                return false;
            } else if (!$.isNumeric(inputVal)) {
                return false;
            } else if (parseFloat(inputVal) < 1) {
                return false;
            } else if (parseFloat(inputVal) > 50) {
                return false;
            } else if (fixGiftRoundsNumArray.includes(inputVal)) {
                return false;
            }

            fixGiftRoundsNumArray.push(inputVal);

            if ($('#fixGiftRoundListDiv').hasClass('hidden')) {
                toggleVisibility('fixGiftRoundListDiv', true);
            }

            let fieldNoID = $(`#${fixGiftRoundListId} div:last-child`).attr("id");
            let fieldNoChk = fieldNoID ? fieldNoID.replace(fieldID, '') : 0;
            let fieldNo = parseInt(fieldNoChk) + 1;

            addHtml += `
                <div id="${fieldID}${fieldNo}" class="${fieldID}" style="border: lightgrey 1px solid; width: fit-content; padding: 4px 7px; border-radius: 15px;" onclick="removeFixGiftRound('${fieldID}${fieldNo}', '${inputVal}')">
                    <span>${inputVal}</span>
                    <span style="background: none; border: none; margin-left: 5px; color: lightgrey;">X</span>
                </div>`;
            $("#" + fixGiftRoundListId).append(addHtml);

            if (event.target && event.target.value !== undefined) {
                event.target.value = '';
            }
        }
    }

    /**
     * 직접 입력을 통해 추가한 지급 회차 전체 초기화
     */
    function resetFixGiftRoundList() {
        fixGiftRoundsNumArray = [];
        let addHtml = '';
        $("#" + fixGiftRoundListId).html(addHtml);
        toggleVisibility('fixGiftRoundListDiv', false);
    }

    /**
     * 직접 입력을 통해 추가한 지급 회차 삭제
     * @param roundId
     * @param roundValue
     */
    function removeFixGiftRound(roundId, roundValue) {
        fixGiftRoundsNumArray = fixGiftRoundsNumArray.filter(item => item !== roundValue);
        removeObj(roundId);

        if (fixGiftRoundsNumArray.length === 0) {
            toggleVisibility('fixGiftRoundListDiv', false);
        }
    }

    /**
     * 사은품 지급 기간 초기화
     */
    function resetGiftPresentPeriod() {
        document.querySelector('input[name="regularGiftPresent[startDate]"]').value = '';
        document.querySelector('input[name="regularGiftPresent[endDate]"]').value = '';
    }

    /**
     * 사은품 증정 선택 기본 테이블
     * @param presentConditionType 사은품 증정 조건 값
     */
    function presentConditionTable(presentConditionType) {
        let addHtml = '';

        addHtml += `
            <table id="${fieldID}" class="table table-rows">
                <colgroup>
                    <col class="width-2xl" />
                    <col />
                    <col class="width-sm" />
                    <col class="width-xs" />
                </colgroup>
                <thead>
                    <tr class="giftAddTr">
                        <th>
                            ${presentConditionType === 'quantityLimited' ?
                                `구매 상품 수량 조건 <input type="button" class="btn btn-sm btn-white mgl10" value="추가" onclick="presentConditionTableAdd('${presentConditionType}');" />`
                                : ``
                            }
                            ${presentConditionType === 'unconditional' ?
                                `비고`
                                : ``
                            }
                        </th>
                        <th>
                            <img src="` + pathAdminGdShare + `img/bl_required.png" style="padding-right: 5px">사은품
                        </th>
                        <th>선택 수량</th>
                        <th>지급 수량</th>
                    </tr>
                </thead>
            </table>
        `;

        $('#giftPresentConditionDiv').html(addHtml);

        presentConditionTableAdd(presentConditionType);

        // 수량별 지급(quantityLimited) 추가상품 수량포함 체크박스 노출
        if (presentConditionType === 'quantityLimited') {
            toggleVisibility('addGoodsDiv', true);
        } else {
            toggleVisibility('addGoodsDiv', false);
        }
    }

    /**
     * 사은품 증정 선택 값 테이블
     * @param thisValue 사은품 증정 조건 값
     */
    function presentConditionTableAdd(thisValue) {
        let addHtml = '';
        let fieldNoChk = '';
        let fieldNoCnt = $('#' + fieldID + " tr.giftAddTr").length;
        if (fieldNoCnt > 1) fieldNoChk = $('#' + fieldID).find('tr.giftAddTr:last').attr('id').replace(fieldID + 'No_', '');
        if (fieldNoChk === '') {
            fieldNoChk = 0;
        }

        let fieldNo = parseInt(fieldNoChk) + 1;
        let fieldAddID = fieldID + 'No_' + fieldNo;

        addHtml += `
            <tr id="${fieldAddID}" class="giftAddTr">
                <td class="center">
                    <div class="form-inline">
                        <input type="hidden" name="gift[giftSno][${fieldNo}]" value="" />
                        ${thisValue === 'unconditional' ? `
                            <input type="hidden" name="gift[conditionStart][${fieldNo}]" value="0" />
                            <input type="hidden" name="gift[conditionEnd][${fieldNo}]" value="0" />
                            금액 수량 조건 없음
                        ` : ''}
                        ${fieldNo > 1 ? `
                            <input type="button" class="btn btn-sm btn-white btn-icon-minus" value="삭제" onclick="field_remove('${fieldAddID}');" />
                        ` : ''}
                        ${thisValue === 'quantityLimited' ? `
                            <input type="text" name="gift[conditionStart][${fieldNo}]" value="" class="form-control width-2xs" oninput="this.value = this.value.replace(/[^0-9]/g, '')" /> 개 ~
                            <input type="text" name="gift[conditionEnd][${fieldNo}]" value="" class="form-control width-2xs" oninput="this.value = this.value.replace(/[^0-9]/g, '')" /> 개
                        ` : ''}
                        ${thisValue !== 'unconditional' ? `
                            <input type="button" class="btn btn-sm btn-gray" value="복사" onclick="presentConditionTableAdd('${thisValue}');presentConditionTableCopy('${fieldNo}');" />
                        ` : ''}
                    </div>
                </td>
                <td class="left" style="padding-left: 5px; padding-top: 15px;">
                    <div>
                        <input type="button" class="btn btn-sm btn-gray" value="사은품 선택" onclick="layerGiftSelect('div_multi_${fieldNo}');" />
                    </div>
                    <table id="div_multi_${fieldNo}_tbl" class="mgt10 mgb0 table table-rows" style="width:80%">
                        <thead style="display:none">
                            <tr>
                                <th class="width7p">번호</th>
                                <th class="width10p">이미지</th>
                                <th>사은품명</th>
                                <th class="width8p">삭제</th>
                            </tr>
                        </thead>
                        <tbody id="div_multi_${fieldNo}"></tbody>
                        <tfoot style="display:none">
                            <td colspan="4">
                                <input type="button" class="btn btn-sm btn-gray" value="전체삭제" onclick="$('#div_multi_${fieldNo}').html('');">
                            </td>
                        </tfoot>
                    </table>
                </td>
                <td class="center" style="padding-left: 5px;">
                    <div>
                        ${`<?=str_replace(chr(10), '', gd_select_box('multiCodeNo', 'gift[selectCnt][CodeNo]', array('전체지급'), null, null, null));?>`.replace(/CodeNo/g, fieldNo)}
                    </div>
                </td>
                <td class="center" style="padding-left: 5px;">
                    <div>
                        <input type="text" name="gift[giveCnt][${fieldNo}]" value="1" class="form-control" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                    </div>
                </td>
            </tr>`;

        $('#' + fieldID).append(addHtml);

        $('input[name*="conditionStart"]').number_only();
        $('input[name*="conditionEnd"]').number_only();
    }

    /**
     * 사은품 선택
     * @param modeStr 사은품 모드
     */
    function layerGiftSelect(modeStr) {
        let loadChk = $('#addPresentForm').length;

        $("#" + modeStr + "_tbl thead").show();
        $("#" + modeStr + "_tbl tfoot").show();

        let scmNo = $('input[name="scmNo"]').val();
        let scmNoNm = $('button[name="scmNoNm"]').html();
        let scmFl = $('input:radio[name=scmFl]:checked').val();

        $.ajax({
            url: 'layer_gift_select.php',
            type: 'get',
            data: {condition: modeStr, scmNo: scmNo, scmNoNm: scmNoNm, scmFl: scmFl},
            async: false,
            success: function (data) {
                if (loadChk === 0) {
                    data = '<div id="addPresentForm">' + data + '</div>';
                }
                let layerForm = data;

                BootstrapDialog.show({
                    title: '사은품 선택',
                    message: $(layerForm),
                    closable: true
                });
            }
        });
    }

    /**
     * 사은품 복사
     * @param fieldNo 현재 번호
     */
    function presentConditionTableCopy(fieldNo) {
        let commonIdPre1 = eval('/\\[' + fieldNo + '\\]/g');
        let commonIdPre2 = eval('/GiftNo_' + fieldNo + '/g');
        let multiIdPre = eval('/multi_' + fieldNo + '_/g');

        let multiHtml = $('#div_multi_' + fieldNo).html();
        let multiValue = $('#multi' + fieldNo).val();
        let countValue = $('input[name="gift[giveCnt][' + fieldNo + ']"]').val();

        let fieldNewNo = $('#' + fieldID).find('tr.giftAddTr:last').attr('id').replace(fieldID + 'No_', '');

        let commonIdNew1 = '[' + fieldNewNo + ']';
        let commonIdNew2 = 'GiftNo_' + fieldNewNo;
        let multiIdNew = 'multi_' + fieldNewNo + '_';

        let changMultiHtml = multiHtml.replace(commonIdPre1, commonIdNew1).replace(commonIdPre2, commonIdNew2).replace(multiIdPre, multiIdNew);

        $('#div_multi_' + fieldNewNo).html(changMultiHtml);

        if (changMultiHtml != '') {
            mode_selectbox_reset('multiGiftNo_' + fieldNewNo, 'multi');
            $('#multi' + fieldNewNo + ' option[value=' + multiValue + ']').prop('selected', true);
        }

        $('input[name="gift[giveCnt][' + fieldNewNo + ']"]').val(countValue);
    }

    /**
     * 멀티 선택형 상품의 선택 조건 select Box
     * @param modeID 멀티 선택형 사은품 ID
     * @param modeNm select Box ID 이름 키값
     */
    function mode_selectbox_reset(modeID, modeNm) {
        let thisID = modeID.split('_');
        let modeCnt = $('input[name*="gift[' + thisID[0] + '][' + thisID[1] + ']"]').length;
        let modeTmpID = modeNm + thisID[1];
        let modeVal = $('#' + modeTmpID).val();
        let addOpt = '';
        let selectedStr = '';
        let textStr = '';

        if (modeCnt > 0) {
            for (let i = 0; i < modeCnt; i++) {
                if (i === 0) {
                    textStr = '전체지급';
                } else {
                    textStr = i + '개 선택';
                }
                if (i === modeVal) {
                    selectedStr = ' selected=\'selected\'';
                } else {
                    selectedStr = '';
                }
                addOpt += '<option value="' + i + '" ' + selectedStr + '>' + textStr + '</option>';
            }
        }
        $('#' + modeTmpID).html(addOpt);
    }
</script>

<form id="frmRegularGoods" name="frmRegularGoods" action="./regular_goods_ps.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="mode" id="mode" />
    <input type="hidden" name="sno" value="<?= $data['sno']; ?>" />
    <input type="hidden" name="prevData" id="prevData" />
    <div class="page-header js-affix">
        <h3><?php echo end($naviMenu->location); ?></h3>
        <div class="btn-group">
            <input type="button" value="목록" class="btn btn-white btn-icon-list"
                   onclick="goList('./regular_goods_list.php');" />
            <input type="submit" value="저장" id="tutorial-step-guide-done" class="btn btn-red">
        </div>
    </div>
    <div class="table-title gd-help-manual">
        정기결제(배송) 상품 설정
    </div>
    <div class="js-excludeGroup">
        <table class="table table-cols no-title-line" style="width: 100%;">
            <colgroup>
                <col class="width-lg" />
                <col />
            </colgroup>
            <tr>
                <th>공급사 구분</th>
                <td id="tutorial-step-guide-01">
                    <input type="hidden" id="scmNoData" />
                    <label class="radio-inline"><input type="radio" name="scmFl" value="n" <?= isset($data['goodsData']) ? 'disabled' : '' ?>
                            <?= empty($data['scmNo']) || $data['scmNo'] == DEFAULT_CODE_SCMNO ? 'checked' : '' ?> onclick=" $('#scmLayer').html(''); resetSelectedGoodsAndGift();" ; />본사</label>
                    <label class="radio-inline"><input type="radio" name="scmFl" value="y" <?= isset($data['goodsData']) ? 'disabled' : '' ?>
                            <?= !empty($data['scmNo']) && $data['scmNo'] != DEFAULT_CODE_SCMNO ? 'checked' : '' ?> onclick="resetSelectedGoodsAndGift(); scmLayerRegister('scm','radio')"/>공급사</label>
                    <label><button type="button" class="btn btn-sm btn-gray scmBtn"
                            <?= isset($data['goodsData']) ? 'disabled' : '' ?> onclick="resetSelectedGoodsAndGift(); scmLayerRegister('scm','radio');">공급사 선택</button></label>
                    <div id="scmLayer" class="selected-btn-group <?= !empty($data['scmNo']) && $data['scmNo'] != DEFAULT_CODE_SCMNO ? 'active' : '' ?>">
                        <?php if (!empty($data['scmNo']) && $data['scmNo'] != DEFAULT_CODE_SCMNO): ?>
                            <h5>선택된 공급사 : </h5>
                            <input type="hidden" name="scmNo" value="<?= $data['scmNo'] ?>" />
                            <span class="btn"> <?= $data['scmNm'] ?></span>
                            <button type="button" class="btn btn-danger <?= isset($data['goodsData']) ? 'hidden' : '' ?>"
                                    data-toggle="delete" data-target="#info_scm_<?= $data['scmNo'] ?>">삭제</button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <th>정기결제(배송) 상품 선택</th>
                <td>
                    <table id="tbl_add_goods_set" class="table table-rows">
                        <thead>
                        <tr id="goodsRegisteredTrArea">
                            <?php if ($mode !== 'modify'): ?>
                            <th class="width2p"><input type="checkbox" id="allCheck" value="y" class="js-checkall"
                                                       data-target-name="itemGoodsNo[]" /></th>
                            <?php endif; ?>
                            <th class="width2p center">번호</th>
                            <th class="width5p center">이미지</th>
                            <th>상품명</th>
                            <th class="width10p center">판매가</th>
                            <th class="width10p center">공급사</th>
                            <th class="width5p center">재고</th>
                            <th class="width5p center">품절여부</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr id="tbl_add_goods_tr_none">
                            <td colspan="<?= $mode !== 'modify' ? '8' : '7' ?>" class="no-data">선택된 상품이 없습니다.</td>
                        </tr>
                        </tbody>
                    </table>

                    <?php if (empty($data['goodsData'])): ?>
                        <div class="table-action">
                            <div class="pull-left">
                                <button class="btn btn-white checkDelete" type="button" onclick="deleteOption()">선택 삭제</button>
                            </div>
                            <div class="pull-right">
                                <button class="btn btn-white checkRegister" type="button" onclick="layerRegister('goods','select')">상품 선택</button>
                            </div>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
    <br>
    <div class="table-title gd-help-manual">
        정기결제(배송) 상품 할인 설정
        <span class="depth-toggle"><button type="button" class="btn btn-sm btn-link bold depth-toggle-button"
                                           depth-name="regularGoodsDiscount"><span>닫힘</span></button></span>
    </div>
    <input type="hidden" id="depth-toggle-hidden-regularGoodsDiscount"
           value="<?= $toggle['regularGoodsDiscount_' . $sessionScmNo] ?>">
    <div id="depth-toggle-line-regularGoodsDiscount" class="depth-toggle-line display-none"></div>
    <div id="depth-toggle-layer-regularGoodsDiscount">
        <table class="table table-cols">
            <colgroup>
                <col class="width-lg" />
            </colgroup>
            <tr>
                <th>상품할인 사용여부</th>
                <td style="padding: 0">
                    <div id="tutorial-step-guide-02" style="width: 190px; padding: 12px 15px;">
                        <label class="radio-inline">
                            <input type="radio" name="regularGoods[discountUseFl]" value="y"
                                   onclick="toggleVisibility('discount_type_value',true)" <?= $data['discountUseFl'] === 'y' ? 'checked="checked"' : ''; ?> />사용함
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="regularGoods[discountUseFl]" value="n"
                                   onclick="toggleVisibility('discount_type_value',false)" <?= empty($data['discountUseFl']) || $data['discountUseFl'] === 'n' ? 'checked="checked"' : ''; ?> />사용안함
                        </label>
                    </div>
                </td>
            </tr>
            <tr class="hidden" id="discount_type_value">
                <th>금액설정</th>
                <td>
                    <div class="form-inline">
                        <label>
                            <span id="discountTypeSpan">구매금액의</span>
                            <input name="regularGoods[discountValue]" value="<?= $data["discountType"] === 'percent' ? $data["discountRate"] : $data["discountPrice"]; ?>"
                                   class="form-control width-sm" oninput="validateDiscountValue(this)" placeholder="0.00"/>
                            <?= gd_select_box('discountType', 'regularGoods[discountType]', $discountType, null, $data['discountType'], null, 'style="width: 50px;" onchange="changeDiscountTypeSpan(this.value, true)"'); ?>
                        </label>

                        <p class="notice-info">절사기준 <a href='/policy/base_currency_unit.php' target="_blank"
                                                       class="btn-link">[설정 > 기본정책 > 금액/단위 기준설정]</a>에서 설정한 기준에 따름 :
                            0.1원 단위로 버림</p>
                        <p class="notice-info">할인금액 기준은 일반 상품의 판매가+옵션가+텍스트옵션가입니다.</p>
                        <p class="notice-info">정기결제 할인을 적용한 상품의 금액이 <?= $minPrice ?>원 이상일 경우에만 정기결제(배송)이 가능합니다.</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="table-title gd-help-manual">
        정기결제(배송) 배송 설정
        <span class="depth-toggle"><button type="button" class="btn btn-sm btn-link bold depth-toggle-button"
                                           depth-name="regularGoodsDelivery"><span>닫힘</span></button></span>
    </div>
    <input type="hidden" id="depth-toggle-hidden-regularGoodsDelivery"
           value="<?= $toggle['regularGoodsDelivery_' . $sessionScmNo] ?>">
    <div id="depth-toggle-line-regularGoodsDelivery" class="depth-toggle-line display-none"></div>
    <div id="depth-toggle-layer-regularGoodsDelivery">
        <table class="table table-cols">
            <colgroup>
                <col class="width-lg" />
            </colgroup>
            <tr>
                <th>배송 방법 노출 여부</th>
                <td style="padding: 0">
                    <div id="tutorial-step-guide-03" style="width: 300px; padding: 12px 15px;">
                        <label class="radio-inline">
                            <input type="radio" name="regularGoods[deliveryType]"
                                   value="all" <?=  empty($data['deliveryType']) || $data['deliveryType'] === 'all' ? 'checked="checked"' : ''; ?> />전체(일반배송, 정기배송)
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="regularGoods[deliveryType]"
                                   value="regular" <?= $data['deliveryType'] === 'regular' ? 'checked="checked"' : ''; ?> />정기배송
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <th>배송 주기</th>
                <td style="padding: 0">
                    <div id="tutorial-step-guide-04" style="width: 560px; padding: 12px 15px;">
                        <div>
                            <label class="radio-inline">
                                <input type="radio" name="regularGoods[deliveryCycleType]" value="all"
                                       onclick="deliveryCycleShow('all')"<?= empty($data['deliveryCycleType']) || $data['deliveryCycleType'] === 'all' ? 'checked="checked"' : ''; ?> />전체
                            </label>
                            <label class="radio-inline width-3xs">
                                <input type="radio" name="regularGoods[deliveryCycleType]" value="month"
                                       onclick="deliveryCycleShow('month')" <?= $data['deliveryCycleType'] === 'month' ? 'checked="checked"' : ''; ?> />월 단위
                            </label>
                            <label class="radio-inline width-3xs">
                                <input type="radio" name="regularGoods[deliveryCycleType]" value="week"
                                       onclick="deliveryCycleShow('week')" <?= $data['deliveryCycleType'] === 'week' ? 'checked="checked"' : ''; ?> />주 단위
                            </label>
                        </div>
                        <div class="hidden" id="delivery_cycle_month">
                            <label class="radio-inline">
                                <input type="checkbox" id="deliveryCycleMonthAll" onclick="toggle_checkboxes('deliveryCycleMonthAll', 'deliveryCycleMonth')" />전체
                            </label>
                            <?php
                            for ($i = 1; $i <= 6; $i++): ?>
                                <label class="radio-inline width-3xs">
                                    <input type="checkbox" name="regularGoodsDeliveryCycle[deliveryCycleMonth][]" class="deliveryCycleMonth"
                                           value="<?= $i ?>" onclick="updateSelectAll('deliveryCycleMonthAll', 'deliveryCycleMonth')" <?= in_array($i, $data['regularGoodsDeliveryCycle']['monthCycle'] ?? []) ? 'checked="checked"' : ''; ?> /><?= $i ?>개월
                                </label>
                            <?php endfor; ?>
                        </div>
                        <div class="hidden" id="delivery_cycle_week">
                            <div>
                                <label class="radio-inline">
                                    <input type="checkbox" id="deliveryCycleWeekAll"
                                           onclick="toggle_checkboxes('deliveryCycleWeekAll', 'deliveryCycleWeek')" />전체
                                </label>
                                <?php
                                for ($i = 1; $i <= 6; $i++): ?>
                                    <label class="radio-inline width-3xs">
                                        <input type="checkbox" name="regularGoodsDeliveryCycle[deliveryCycleWeek][]" class="deliveryCycleWeek"
                                               value="<?= $i ?>"
                                               onclick="updateSelectAll('deliveryCycleWeekAll', 'deliveryCycleWeek')" <?= in_array($i, $data['regularGoodsDeliveryCycle']['weekCycle'] ?? []) ? 'checked="checked"' : ''; ?> /><?= $i ?>주
                                    </label>
                                <?php endfor; ?>
                            </div>

                            <div>
                                <label class="radio-inline">
                                    <input type="checkbox" id="deliveryCycleWeekDayAll"
                                           onclick="toggle_checkboxes('deliveryCycleWeekDayAll', 'deliveryCycleWeekDay')" />전체
                                </label>
                                <?php
                                $days = ['월요일', '화요일', '수요일', '목요일', '금요일'];
                                foreach ($days as $index => $day): ?>
                                    <label class="radio-inline width-3xs">
                                        <input type="checkbox" name="regularGoodsDeliveryCycle[deliveryCycleWeekDay][]" class="deliveryCycleWeekDay"
                                               value="<?= $index + 1 ?>"
                                               onclick="updateSelectAll('deliveryCycleWeekDayAll', 'deliveryCycleWeekDay')" <?= in_array($index+1, $data['regularGoodsDeliveryCycle']['weekDayCycle'] ?? []) ? 'checked="checked"' : ''; ?> /><?= $day ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
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
                                <input type="radio" name="regularGoods[deliveryRoundsDisplayType]" value="all"
                                       onclick="toggleVisibility('max_delivery_rounds', false)" <?= empty($data['deliveryRoundsDisplayType']) || $data['deliveryRoundsDisplayType'] === 'all' ? 'checked="checked"' : ''; ?> />전체
                            </label>
                            <label class="radio-inline width-3xs">
                                <input type="radio" name="regularGoods[deliveryRoundsDisplayType]" value="disabled"
                                       onclick="toggleVisibility('max_delivery_rounds', false)" <?= $data['deliveryRoundsDisplayType'] === 'disabled' ? 'checked="checked"' : ''; ?> />미설정
                            </label>
                            <label class="radio-inline width-3xs">
                                <input type="radio" name="regularGoods[deliveryRoundsDisplayType]" value="abled"
                                       onclick="toggleVisibility('max_delivery_rounds', true)" <?= $data['deliveryRoundsDisplayType'] === 'abled' ? 'checked="checked"' : ''; ?> />설정
                            </label>
                        </div>
                        <div class="hidden" id="max_delivery_rounds" style="display: inline-block;">
                            최대 회차 :
                            <label class="radio-inline">
                                <input name="regularGoods[maxDeliveryRounds]"
                                       value="<?= $data['maxDeliveryRounds']; ?>" class="form-control width-2xs"
                                       type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                            </label>
                            회
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="table-title gd-help-manual">
        정기결제(배송) 사은품 설정
        <span class="depth-toggle"><button type="button" class="btn btn-sm btn-link bold depth-toggle-button"
                                           depth-name="regularGiftPresent"><span>닫힘</span></button></span>
    </div>
    <input type="hidden" id="depth-toggle-hidden-regularGiftPresent"
           value="<?= $toggle['regularGiftPresent_' . $sessionScmNo] ?>">
    <div id="depth-toggle-line-regularGiftPresent" class="depth-toggle-line display-none"></div>
    <div id="depth-toggle-layer-regularGiftPresent">
        <table class="table table-cols">
            <colgroup>
                <col class="width-lg" />
            </colgroup>
            <tr>
                <th>사은품 지급 사용여부</th>
                <td style="padding: 0">
                    <div id="tutorial-step-guide-05" style="width: 190px; padding: 12px 15px;">
                        <label class="radio-inline">
                            <input type="radio" name="regularGoods[giftPresentUseFl]" value="y"
                                   onclick="giftPresentShow('used')" <?= $data['giftPresentUseFl'] === 'y' ? 'checked="checked"' : ''; ?> />사용함
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="regularGoods[giftPresentUseFl]" value="n"
                                   onclick="giftPresentShow('notUsed')" <?= empty($data['giftPresentUseFl']) || $data['giftPresentUseFl'] === 'n' ? 'checked="checked"' : ''; ?> />사용안함
                        </label>
                    </div>
                </td>
            </tr>
            <tr class="hidden" id="gift_present_title">
                <th>지급 조건명</th>
                <td>
                    <label class="radio-inline">
                        <input type="text" name="regularGiftPresent[conditionTitle]" value="<?= $data['conditionTitle']; ?>" class="form-control" maxlength="250" />
                    </label>
                </td>
            </tr>
            <tr class="hidden" id="gift_present_period">
                <th>지급 기간</th>
                <td>
                    <div class="form-inline">
                        <span class="radio-inline">
                            <input type="radio" name="regularGiftPresent[periodUseFl]" onclick="resetGiftPresentPeriod()"
                                   value="n" <?= empty($data['periodUseFl']) || $data['periodUseFl'] === 'n' ? 'checked="checked"' : ''; ?> />제한 없음
                        </span>
                        <span class="radio-inline">
                            <input type="radio" name="regularGiftPresent[periodUseFl]"
                                   value="y" <?= $data['periodUseFl'] === 'y' ? 'checked="checked"' : ''; ?> />
                            <div class="input-group js-datetimepicker startDatetime" onclick="$('input[name=\'regularGiftPresent[periodUseFl]\']').eq(1).prop('checked',true);">
                                <input type="text" class="form-control width-md" name="regularGiftPresent[startDate]"
                                       value="<?= $data['startDate']; ?>" />
                                <span class="input-group-addon">
                                    <span class="btn-icon-calendar">
                                    </span>
                                </span>
                            </div>
                            ~
                            <div class="input-group js-datetimepicker endDatetime" onclick="$('input[name=\'regularGiftPresent[periodUseFl]\']').eq(1).prop('checked',true);">
                                <input type="text" class="form-control width-md" name="regularGiftPresent[endDate]"
                                       value="<?= $data['endDate']; ?>" />
                                <span class="input-group-addon">
                                    <span class="btn-icon-calendar">
                                    </span>
                                </span>
                            </div>
                        </span>
                    </div>
                </td>
            </tr>
            <tr class="hidden" id="gift_present_rounds">
                <th>지급 회차 조건</th>
                <td>
                    <div class="form-inline">
                        <label class="radio-inline">
                            <input type="radio" name="regularGiftPresent[roundsType]" value="once"
                                   onclick="giftPresentRoundsShow('once')" <?= empty($data['roundsType']) || $data['roundsType'] === 'once' ? 'checked="checked"' : ''; ?> />최초 1회 지급
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="regularGiftPresent[roundsType]" value="multiplier"
                                   onclick="giftPresentRoundsShow('multiplier')" <?= $data['roundsType'] === 'multiplier' ? 'checked="checked"' : ''; ?> />배수 설정
                            <input id="gift_multiplier_num" class="form-control hidden width-md"
                                   name="regularGiftPresent[multiplierNum]" placeholder="숫자만 입력"
                                   value="<?= $data['multiplierNum']; ?>" type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="regularGiftPresent[roundsType]" value="directInput"
                                   onclick="giftPresentRoundsShow('fix')" <?= $data['roundsType'] === 'directInput' ? 'checked="checked"' : ''; ?> />직접 입력
                            <input id="gift_fix_num" class="form-control hidden width-2xl"
                                   onkeydown=" addFixGiftRound(this.value, event);" placeholder="회차는 최대 <?= $maxFixGiftRound ?>개까지 설정 가능합니다. (Enter로 구분)"
                                   type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                        </label>
                    </div>
                    <div id="fixGiftRoundListDiv" class="hidden" style="padding-top: 10px; display: flex; gap: 10px;">
                        <input type="hidden" id="fixGiftRoundsNumArray" name="regularGiftPresent[fixGiftRoundsNumArray]"
                               value="<?= $data['fixGiftRoundsNumArray']; ?>" />
                        <div onclick="resetFixGiftRoundList();" style="background-color: grey; min-width: fit-content; min-height:fit-content; max-height: fit-content; padding: 4px 7px; border-radius: 15px; color:white;">
                            <span>전체삭제</span>
                            <span style="background: none; border: none; margin-left: 5px;">X</span>
                        </div>
                        <div id="fixGiftRoundList" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
                        <div style="clear: both;"></div>
                    </div>
                </td>
            </tr>
            <tr class="hidden" id="gift_present_condition">
                <th>증정 조건</th>
                <td>
                    <label class="radio-inline">
                        <input type="radio" name="regularGiftPresent[conditionType]" value="unconditional"
                               onclick="presentConditionTable(this.value);" <?= empty($data['conditionType']) || $data['conditionType'] === 'unconditional' ? 'checked="checked"' : ''; ?> />무조건 지급
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="regularGiftPresent[conditionType]" value="quantityLimited"
                               onclick="presentConditionTable(this.value);" <?= $data['conditionType'] === 'quantityLimited' ? 'checked="checked"' : ''; ?> />수량별 지급
                    </label>
                    <div id="addGoodsDiv" class="mgt10 hidden">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="regularGiftPresent[addGoodsFl]" value="y" <?= $data['addGoodsFl'] === 'y' ? 'checked="checked"' : ''; ?>>추가상품 수량 포함
                        </label>
                        <div class="notice-info">추가상품 수량 포함 체크 시 정기결제(배송) 상품 설정에서 선택한 상품에 연결된 추가상품 개수도 포함되어 주문 시 사은품을 증정하게 됩니다.</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div id="giftPresentConditionDiv"></div>
    <div class="table-title gd-help-manual">
        관리자 메모
        <span class="depth-toggle"><button type="button" class="btn btn-sm btn-link bold depth-toggle-button"
                                           depth-name="adminMemo"><span>닫힘</span></button></span>
    </div>
    <input type="hidden" id="depth-toggle-hidden-adminMemo" value="<?= $toggle['adminMemo_' . $sessionScmNo] ?>">
    <div id="depth-toggle-line-adminMemo" class="depth-toggle-line display-none"></div>
    <div id="depth-toggle-layer-adminMemo">
        <table class="table table-cols">
            <colgroup>
                <col class="width-lg" />
            </colgroup>
            <tr>
                <th>관리자 메모</th>
                <td>
                    <textarea name="regularGoods[adminMemo]" rows="3" class="form-control"><?= $data['adminMemo']; ?></textarea>
                </td>
            </tr>
        </table>
    </div>
</form>

<script type="text/html" id="templateGiftPresentItem">
    <tr id="multi_<%= giftKey %>_<%= giftNo %>">
        <td class="center">
            <%= index + 1 %>
            <input type="hidden" name="gift[multiGiftNo][<%= giftKey %>][]" value="<%= giftNo %>" />
        </td>
        <td class="outline"><%= imageHtml %></td>
        <td class="outline"><%= giftNm %></td>
        <td>
            <input type="button" class="btn btn-sm btn-gray"
                   onclick="field_remove('multi_<%= giftKey %>_<%= giftNo %>');
                         mode_selectbox_reset('multiGiftNo_<%= giftKey %>', 'multi');"
                   value="삭제" />
        </td>
    </tr>
</script>
<style>
    #delivery_cycle_month,
    #delivery_cycle_week {
        padding-top: 5px;
    }
</style>

<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/aggregator/TutorialHandler.js"></script>
<script type="text/javascript">
    window.GodoTutorial.tutorialHandlerInstance = new GodoTutorial.TutorialHandler({
        category: 'PAYMENT_TUTORIAL',
        code: '<?= $tutorialCode ?>',
        mode: '<?= $tutorialMode ?>',
        linkModalUrl: '<?= $tutorialLinkModalUrl ?>',
        linkModalSize: <?= json_encode($tutorialLinkModalSize ?? []) ?>,
        stepGuideSteps: <?= json_encode($tutorialStepGuideSteps ?? []) ?>,
        stepGuideLinks: <?= json_encode($tutorialStepGuideLinks ?? []) ?>,
    });
</script>
