<form method="post" name="frmSuperAdminMemo" id="frmSuperAdminMemo" action="../order/regular_order_ps.php" target="ifrmProcess">
    <input type="hidden" name="mode" value="admin_memo_save">
    <input type="hidden" name="applyNo" id="applyNo" value="<?= $requestGetParams['applyNo']; ?>">
    <input type="hidden" name="sort" value="<?= $requestGetParams['sort']; ?>"/>
    <input type="hidden" name="pageNum" value="<?= $requestGetParams['pageNum']; ?>"/>
    <input type="hidden" name="adminMemoSno" value="">

    <div class="page-header">
        <h3>관리자메모</h3>
        <?php if(!$isProvider){ ?>
        <div class="btn-group">
            <button type="submit" class="btn btn-red">저장</button>
        </div>
        <?php }?>
    </div>
    <div class="super-admin-memo-title mgb20">
        <div class="pdl30">
            <p>신청번호 : <?= $requestGetParams['applyNo']; ?></p>
            <p>신청일시 : <?= $requestGetParams['regDt']; ?></p>
        </div>
    </div>

    <?php if(!$isProvider){ ?>
        <div class="search-detail-box">
            <table class="table table-cols">
                <colgroup>
                    <col class="width-sm">
                    <col>
                    <col class="width-sm">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>메모 유형</th>
                    <td>
                        <label class="radio-inline">
                            <input type="radio" name="memoType" value="order" checked="checked" />신청번호별
                        </label>
                    </td>
                    <th>메모 구분</th>
                    <td>
                        <?= gd_select_box('orderMemoCd', 'orderMemoCd', $memoCd, null, null, '=메모 구분='); ?>
                    </td>
                </tr>
                <tr>
                    <th>메모 내용</th>
                    <td colspan="3">
                        <textarea name="adminMemo" class="form-control" rows="6"><?=$data['content']?></textarea>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="text-center mgt10">
            <button type="button" class="btn btn-black js-memo-reset">초기화</button>
        </div>
    <?php }?>

    <table cellpadding="0" cellpadding="0" width="100%" id="orderGoodsMemoList" class="table table-rows table-fixed mgt10">
        <thead>
        <tr id="orderGoodsList">
            <th class="width10p">작성일</th>
            <th class="width10p">작성자</th>
            <th class="width10p">메모 구분</th>
            <th class="width10p">신청번호</th>
            <th class="width30p">메모 내용</th>
            <?php if (!$isProvider) { ?>
                <th class="width5p">관리</th>
            <?php }?>
        </tr>
        </thead>
        <?php
        if ($memoData) {
            foreach ($memoData as $mKey => $mVal) {
                ?>
                <tbody id="orderGoodsMemoData<?= $mKey; ?>">
                <tr>
                    <td class="text-center">
                        <span><?php if ($mVal['modDt']) {
                                echo $mVal['modDt'];
                            } else {
                                echo $mVal['regDt'];
                            } ?></span></td>
                    <td class="text-center">
                        <span class="managerId"><?= $mVal['managerId']; ?></span><br/>
                        <?php if($mVal['managerNm']){?><span class="managerNm">(<?= $mVal['managerNm']; ?>)</span><?php }?>
                    </td>
                    <td class="text-center">
                        <span class="itemNm"><?= $mVal['itemNm']; ?></span>
                    </td>
                    <td class="text-center">
                        <span><?php echo $mVal['applyNo'];?></span>
                    </td>
                    <td>
                        <span class="content-memo"><?=str_replace('\"','"', str_replace(['\r\n', '\n', chr(10)], '<br>', $mVal['content']));?></span>
                    </td>
                    <?php if (!$isProvider) { ?>
                    <td class="text-center">
                        <div class="pdb5">
                            <span class="mod-button" style="padding-bottom: 5px;">
                                <button type="button" class="btn btn-sm btn-gray js-admin-memo-modify" data-memocd="<?=$mVal['memoCd'];?>" data-manager-sno="<?=$mVal['managerSno'];?>" data-m-sno="<?=$managerSno;?>" data-content="<?=gd_htmlspecialchars($mVal['content']);?>" data-no="<?=$mVal['sno'];?>">수정</button>
                            </span>
                        </div>
                        <div>
                            <span class="del-button">
                                <button type="button" class="btn btn-sm btn-gray js-admin-memo-delete" data-manager-sno="<?=$mVal['managerSno'];?>" data-m-sno="<?=$managerSno;?>" data-no="<?=$mVal['sno'];?>">삭제</button>
                            </span>
                        </div>
                    </td>
                    <?php } ?>
                </tr>
                </tbody>
                <?php
            }
        }else{
            ?>
            <tr>
                <td colspan="6" class="no-data">
                    등록된 메모가 없습니다.
                </td>
            </tr>
        <?php }?>
    </table>
    <div class="center"><?= $page->getPage(); ?></div>

</form>

<script type="text/javascript">
    <!--
    $(document).ready(function(){

        // 폼 체크 후 전송
        $('#frmSuperAdminMemo').validate({
            dialog: false,
            rules: {
                adminMemo: 'required',
            },
            messages: {
                adminMemo: '관리자 메모를 입력해주세요.',
            },
            submitHandler: function(form) {
                form.submit();
            }
        });


        // 메모 수정
        $('.js-admin-memo-modify').click(function () {
            if (($(this).data('manager-sno') == $(this).data('m-sno')) || ($(this).data('manager-sno') == 0)) {
                let contentStr = $(this).data('content').toString().replace(/\\r\\n/gi, "\n").replace(/\\"/gi,'"');

                // 수정 모드로 변경
                // 저장 버튼의 data-submit-mode도 같이 변경
                $('input[name="mode"]').attr('value', 'admin_memo_modify');
                $('input[name="adminMemoSno"]').attr('value',$(this).data('no'));
                $("#orderMemoCd").val($(this).data('memocd')).prop("selected", true);
                $("textarea[name='adminMemo']").val(contentStr);

            } else {
                alert('메모를 등록한 관리자만 수정가능합니다.');
                return false;
            }
        });

        // 메모 삭제
        $('.js-admin-memo-delete').click(function () {
            if (($(this).data('manager-sno') == $(this).data('m-sno')) || ($(this).data('manager-sno') == 0)) {
                let sno = $(this).data('no');
                dialog_confirm('선택한 관리자메모를 삭제하시겠습니까? 삭제하시면 복구 하실 수 없습니다.', function (result) {
                    if (result) {
                        $('input[name="mode"]').attr('value', 'admin_memo_delete');
                        $('input[name="adminMemoSno"]').attr('value', sno);
                        $("#frmSuperAdminMemo").attr('target', 'ifrmProcess');
                        $("#frmSuperAdminMemo").submit();
                    }
                });
            } else {
                alert('메모를 등록한 관리자만 삭제가능합니다.');
                return false;
            }
        });

        // 초기화
        $('.js-memo-reset').click(function () {
            $("input[name='memoType'][value='order']").prop("checked",true);
            $("#orderMemoCd").val($(this).data('memocd')).prop("selected", false);
            $("textarea[name='adminMemo']").val('');
            $('input[name="no"]').attr('value','');
        });
    });
    //-->
</script>
