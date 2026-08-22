<form id="frmRegularGoodsUpdate" method="get">
    <input type="hidden" name="mode" value="<?= $mode ?>">
    <input type="hidden" name="dataForm" value="<?= htmlspecialchars(json_encode($dataForm)) ?>">
    <div>
        <table class="table-cols no-title-line" style="width: 100%;">
            <colgroup>
                <col class="width-sm"/>
                <col/>
            </colgroup>
            <?php if ($mode === 'updateApplyStatus') : ?>
                <tr>
                    <th>
                        신청 상태
                    </th>
                    <td>
                        <label class="radio-inline"><input type="radio" name="applyStatus" value="abled" checked>신청 가능</label>
                        <label class="radio-inline"><input type="radio" name="applyStatus" value="disabled">신청 중지</label>
                    </td>
                </tr>
            <?php endif; ?>
        </table>
        <br>
        <div class="notice-info mgb5">신청 중지할 경우 기존 신청한 정기결제(배송) 건에는 영향이 없으며, 신규 신청만 불가하게 됩니다.</div>
        <br>
        <div class="text-center">
            <input type="button" value="취소" class="btn btn-sm btn-white js-layer-close"/>
            <input type="button" value="선택상품 일괄수정" class="btn btn-sm btn-black regular-goods-update-form-btn"/>
        </div>
    </div>
</form>
<script>
    $('.regular-goods-update-form-btn').click(function () {
        // 신청 상태 없데이트
        $('#frmRegularGoodsUpdate').attr('method', 'post');
        $('#frmRegularGoodsUpdate').attr('action', './regular_goods_ps.php');
        $('#frmRegularGoodsUpdate').attr('target', 'ifrmProcess');
        $('#frmRegularGoodsUpdate').submit();
    });
</script>
