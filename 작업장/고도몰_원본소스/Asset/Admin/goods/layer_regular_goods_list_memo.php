<div class="super-admin-memo-title mgb20" style="height:40px;">
    <div class="">
        <span style="font-weight:bold;margin-right:10px;">상품명</span>
        :
        <span style="margin-left:10px;"><?= gd_htmlspecialchars_slashes($regularGoodsAdminMemoData['goodsNm'], 'strip'); ?></span></p>
    </div>
</div>
<form method="post" name="layerRegularGoodsListMemoFrm" id="layerRegularGoodsListMemoFrm"
      action="../goods/regular_goods_ps.php" target="ifrmProcess">
    <input type="hidden" name="mode" value="update_regular_goods_admin_memo">
    <input type="hidden" name="preAdminMemo" value="<?= $regularGoodsAdminMemoData['adminMemo']; ?>">
    <input type="hidden" name="regularGoodsSno" value="<?= $regularGoodsSno; ?>">

    <div>
        <textarea name="adminMemo" rows="6" class="form-control"
                  style="margin-bottom:15px;"><?= str_replace(['\r\n', '\n'], chr(10), gd_htmlspecialchars_stripslashes($regularGoodsAdminMemoData['adminMemo'])); ?></textarea>
    </div>
    <div class="text-center">
        <input type="button" value="닫기" class="btn btn-white js-layer-close"/>
        <input type="submit" value="저장" class="btn btn-black js-check-save"/>
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function () {
        // 폼 체크 후 전송
        $('#layerRegularGoodsListMemoFrm').validate({
            dialog: false,
            rules: {
                adminMemo: 'required'
            },
            messages: {
                adminMemo: '관리자 메모를 입력해주세요.'
            },
            submitHandler: function (form) {
                form.target = 'ifrmProcess';
                form.submit();
            }
        });
    });
</script>
