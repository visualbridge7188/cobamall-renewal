<div class="regular-table-dashboard">
    <table class="table table-rows">
        <thead>
        <tr>
            <th><input type="checkbox" id="giftCheckAll" onclick="toggle_checkboxes('giftCheckAll','giftCheck')"></th>
            <th>번호</th>
            <th>사은품명</th>
            <th>등록일</th>
            <th>재고</th>
        </tr>
        </thead>
        <tbody class="multiGiftData">
        <?php foreach ($multiGiftData as $index => $gift) : ?>
            <tr>
                <td><input type="checkbox" class="giftCheck" name="gift" value="<?= $gift['giftNo'] ?>"
                           onclick="updateSelectAll('giftCheckAll','giftCheck'); setSelectGiftNumArray(this.value)">
                </td>
                <td><?= $index + 1 ?></td>
                <td class="regular_gift_nm">
                    <div><?= $gift['giftImage'] ?><span><?= $gift['giftNm'] ?></span></div>
                </td>
                <td><?= gd_date_format('Y-m-d', $gift['regDt']) ?></td>
                <td><?= $gift['stockFl'] === 'n' ? '무제한' : $gift['stockCnt'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="text-center" id="paginationBox"><?= $page->getPage('#'); ?></div>
    <div class="text-center">
        <button type="button" class="btn btn-lg btn-white js-layer-close">취소</button>
        <button type="submit" class="btn btn-lg btn-black js-regular-delivery-change" onclick="changeGiftOption()">저장
        </button>
    </div>
</div>
<style>
    .regular-table-dashboard {
        border-top: 1px solid #dadada;
    }

    .regular-table-dashboard th {
        background: #A9A9A9;
        border: 1px solid #dadada;
        text-align: center;
        color: white;
        font-weight: bold;
    }

    .regular-table-dashboard td {
        padding-left: 20px;
        padding-right: 20px;
        border: 1px solid #dadada;
        line-height: 1.8;
        text-align: center;
        vertical-align: top;
    }

    .regular_gift_nm div {
        text-align: left;
    }

    .regular_gift_nm img {
        margin-right: 5px;
    }
</style>
<script>
    let selectGiftNumArray = [];

    $(document).ready(function () {
        if (parseInt("<?= $selectCount ?>", 10) === 0) {
            $('.giftCheck').prop('checked', true).prop('disabled', true);
            $('#giftCheckAll').prop('checked', true).prop('disabled', true);
        }
    });

    /**
     * 페이지네이션 링크 클릭 시 AJAX 호출
     */
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        let page = $(this).data('page');
        loadPageData(page);
    });

    /**
     * 페이지 데이터 로드 함수
     * @param page : 로드할 페이지 번호
     */
    function loadPageData(page) {
        let applyNo = Number(<?= $applyNo ?>);

        if (Number.isInteger(applyNo)) {
            $.ajax({
                type: 'GET',
                url: '/order/layer_regular_order.php',
                data: {
                    mode: 'regular-gift-update-list',
                    applyNo: applyNo,
                    page: page
                },
                success: function (response) {
                    updatePageContent(response);
                },
                error: function () {
                    alert('페이지를 불러오는 데 실패했습니다.');
                }
            });
        } else {
            alert('비정상적인 신청서 번호입니다.');
        }
    }

    /**
     * 페이지 콘텐츠 업데이트 함수
     * @param response
     */
    function updatePageContent(response) {
        let dom = $('<div>').html(response);
        let newHtml = dom.find('tbody.multiGiftData').html();
        $('tbody.multiGiftData').html(newHtml);

        newHtml = dom.find('#paginationBox').html();
        $('#paginationBox').html(newHtml);

        if (parseInt("<?= $selectCount ?>", 10) === 0) {
            $('.giftCheck').prop('checked', true).prop('disabled', true);
            $('#giftCheckAll').prop('checked', true).prop('disabled', true);
        } else {
            $('.giftCheck').each(function () {
                if (selectGiftNumArray.includes($(this).val())) {
                    $(this).prop('checked', true);
                }
            });

            updateSelectAll('giftCheckAll', 'giftCheck');
        }
    }

    /**
     * 사은품 정보 변경
     */
    function changeGiftOption() {
        const ERROR_REGULAR_GIFT_DELETE = '<?= $ERROR_REGULAR_GIFT_DELETE ?>';
        const selectCount = parseInt("<?= $selectCount ?>", 10);

        // 검증 로직
        let selectGiftLen = selectGiftNumArray.length;
        if (selectCount !== 0 && selectGiftLen === 0) {
            dialog_alert('사은품을 선택해주세요.');
            return;
        } else if (selectCount > 0 && selectGiftLen !== selectCount) {
            dialog_alert('사은품은 ' + selectCount + '개 선택하셔야 합니다.');
            return;
        }

        // AJAX 전송
        $.ajax({
            method: 'POST',
            url: '../order/layer_regular_order_ps.php',
            data: {
                mode: 'update_regular_gift',
                selectCount: selectCount,
                applyNo: parseInt("<?= $applyNo ?>", 10),
                regularGiftPresentInfoSno: parseInt("<?= $regularGiftPresentInfoSno ?>", 10),
                giftNoList: selectGiftNumArray
            },
            success: function (res) {
                if (res.result === 'success') {
                    dialog_alert(res.message, '안내');
                    setTimeout(function () {
                        location.reload();
                    }, 3000)
                } else if (res.code === Number(ERROR_REGULAR_GIFT_DELETE)) {
                    dialog_alert(res.message);
                    selectGiftNumArray = [];
                    loadPageData(1);
                } else {
                    dialog_alert(res.message);
                    setTimeout(function () {
                        location.reload();
                    }, 3000)
                }
            },
            error: function (err) {
                dialog_alert(err.responseText);
                location.reload();
            }
        });
    };

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
            setSelectGiftNumArray(this.value);
        });
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
     * 선택된 사은품 번호 배열(selectGiftNumArray)을 업데이트하는 함수
     *
     * @param giftNum 사은품 번호
     */
    function setSelectGiftNumArray(giftNum) {
        if (!selectGiftNumArray.includes(giftNum)) {
            selectGiftNumArray.push(giftNum);
        } else {
            selectGiftNumArray = selectGiftNumArray.filter(item => item !== giftNum);
        }
    }
</script>
