<form id="frmAutoPg" name="frmAutoPg" action="settle_auto_pg_ps.php" method="post" enctype="multipart/form-data">
    <div class="page-header js-affix">
        <h3><?php echo end($naviMenu->location); ?>
            <small>계약된 전자결제(PG)의 설정을 하실 수 있습니다.</small>
        </h3>
    </div>
    <!-- // 정기결제 PG 추가되면 주석 해제하기 -->
    <!--<ul class="nav nav-tabs nav-justified mgb30" role="tablist">-->
    <!--    --><?php
    //
    //    foreach ($autoPgList as $val) {
    //        ?>
    <!--        <li role="presentation" class="--><?php //= $val ?><!--">-->
    <!--            <a href="settle_auto_pg_config.php?pgMode=--><?php //= $val ?><!--" role="tab">--><?php //= $val ?><!--</a>-->
    <!--        </li>-->
    <!--    --><?php //} ?>
    <!--</ul>-->
    <?php include($layoutPgContent); ?>
</form>

<script type="text/javascript">
    $(document).ready(function () {
        $('#frmAutoPg').validate({
            submitHandler: function (form) {
                form.target = 'ifrmProcess';
                form.submit();
            },
            rules: {
            },
            messages: {
            }
        });
    });

    /**
     * 결제수단 자동 설정 - PG 중앙화로 요청
     */
    function settleKindUpdate(){
        var pgName = $('input[name=\'pgName\']').val();
        var pgId = '<?php echo $config['pgId'];?>';
        var params = {
            mode: 'pgAutoUpdate',
            pgType: pgName,
            pgId: pgId
        };

        $.post('./settle_auto_pg_ps.php', params, function (data) {
            var resultVal = true;
            var resultMsg = '';
            if (data === '') {
                resultVal = false;
                resultMsg = '결과 없음';
            }
            if (resultVal === true) {
                var resultData = $.parseJSON(data);
                if (resultData.result === 'ok') {
                    alert(pgName + ' 정보 갱신 완료 되었습니다. 잠시후 새로고침 됩니다.');
                    setTimeout(function() {
                        parent.location.reload();
                    }, 2000);
                } else {
                    resultVal = false;
                    resultMsg = resultData.error_msg;
                }
            }

            if (resultVal === false) {
                alert(pgName + ' 정보 갱신에 실패하였습니다. \n서비스 신청이 완료된 상태라면 고객센터로 문의하여 주세요. \n(' + resultMsg + ') ');
            }
        });
    }
</script>
