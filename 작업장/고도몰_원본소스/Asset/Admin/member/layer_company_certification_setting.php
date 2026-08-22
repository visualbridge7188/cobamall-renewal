<table class="table table-cols">
    <colgroup>
        <col width="30%">
        <col/>
    </colgroup>
    <tbody>
    <tr>
        <th class="require">업로드파일 최대크기</th>
        <td>
            <input type="text" name="companyCertificationFileSize" value="<?=$fileSize?>" class="width-sm js-number-only" style="padding-left: 10px"/> MByte(s)
        </td>
    </tr>
    </tbody>
</table>
<div class="text-center">
    <button type="button" class="btn btn-lg btn-black js-layer-submit">적용</button>
</div>
<script>
$(function() {
    $('input.js-number-only').number_only("d");

    $('.js-layer-submit').on('click', function() {
        let val = $("input[name='companyCertificationFileSize']").val();
        let inputSize = Number(val);
        if (!val || isNaN(inputSize) || inputSize < 1 || inputSize > 5) {
            dialog_alert('업로드 용량은 최소 1MB 부터 5MB 까지 설정할 수 있습니다.');
            return false;
        }

        // 부모 폼의 certificationFileSize 값 입력
        var key = '<?= $key ?>';
        var matched = key.match(/^comAddiCert(\d+)$/);
        if (matched) {
            // 추가 첨부 항목인 경우: comAddiCert[index][certificationFileSize] 변경
            var index = matched[1];
            $('input[name="comAddiCert[' + index + '][certificationFileSize]"]', window.parent.document).val(inputSize);
        } else {
            // 사업자등록증인 경우
            $('#certificationFileSize', window.parent.document).val(inputSize);
        }

        $.ajax({
            url: '../member/company_certification_ps.php',
            type: 'POST',
            data: {
                mode: 'setting',
                fileSize: inputSize,
                key : '<?= $key ?>'
            },
            success: function(res) {
                if(res.result) {
                    dialog_alert('설정이 저장되었습니다.');
                    setTimeout(function() {
                        layer_close();
                    }, 2000);
                } else {
                    dialog_alert(res.message || '저장에 실패했습니다.');
                }
            },
            error: function() {
                dialog_alert('서버 오류가 발생했습니다.');
            }
        });
    });
});
</script>
