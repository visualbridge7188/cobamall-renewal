<?php if (gd_is_provider() === false) { ?>
    <div class="main-section">
        <div class="table-title">
            <span class="">스토어</span>
        </div>
        <div class="slide-items-wrap">
            <div class="single-item">
                <div class="slide-item">
                    <span class="slide-item-text">
                        <a href="/service/service_info.php?menu=power_main">개발 없이 필요한 기능을 도입하세요.</a>
                    </span>
                </div>
            </div>
        </div>

        <script>
            // 스토어
            $('.single-item').slick();
        </script>
    </div>
    <div class="main-section">
        <div class="table-title">
            운영필수서비스 현황
        </div>
        <div class="plusshop-stat-outer">
            <table class="table table-bordered">
                <colgroup>
                    <col class="width20p">
                    <col class="width20p">
                </colgroup>
                <tr>
                    <td class="<?php if ($svcStates['pg'] === true) {
                        echo 'active';
                    } ?>"><a href="<?php echo URI_ADMIN; ?>policy/settle_pg_config.php">전자 결제(PG)</a></td>
                    <td class="<?php if ($svcStates['escrow'] === true) {
                        echo 'active';
                    } ?>"><a href="<?php echo URI_ADMIN; ?>policy/settle_pg_config.php">구매안전 서비스</a></td>
                </tr>
                <tr>
                    <td class="<?php if ($svcStates['mpg'] === true) {
                        echo 'active';
                    } ?>"><a href="<?php echo URI_ADMIN; ?>policy/settle_pg_mobile_config.php">휴대폰 결제</a></td>
                    <td class="<?php if ($svcStates['payco'] === true) {
                        echo 'active';
                    } ?>"><a href="<?php echo URI_ADMIN; ?>policy/settle_pg_payco.php">페이코</a></td>
                </tr>
                <tr>
                    <td class="<?php if ($svcStates['naverPay'] === true) {
                        echo 'active';
                    } ?>"><a href="<?php echo URI_ADMIN; ?>policy/naver_pay_config.php">네이버페이</a></td>
                    <td class="<?php if ($svcStates['domain'] === true) {
                        echo 'active';
                    } ?>"><a href="javascript:gotoGodomall('domain');">정식 도메인</a></td>
                </tr>
                <tr>
                    <td class="<?php if ($svcStates['ssl'] === true) {
                        echo 'active';
                    } ?>"><a href="<?php echo URI_ADMIN; ?>policy/ssl_pc_setting.php">보안서버(SSL)</a></td>
                    <td class="<?php if ($svcStates['ipin'] === true) {
                        echo 'active';
                    } ?>"><a href="<?php echo URI_ADMIN; ?>policy/member_auth_ipin.php">아이핀 본인인증</a></td>
                </tr>
                <tr>
                    <td class="<?php if ($svcStates['auth_cellphone'] === true) {
                        echo 'active';
                    } ?>"><a href="<?php echo URI_ADMIN; ?>policy/member_auth_cellphone.php">휴대폰 본인확인</a></td>
                    <td class="<?php if ($svcStates['sms'] === true) {
                        echo 'active';
                    } ?>"><a href="<?php echo URI_ADMIN; ?>crm/message_config.php">SMS</a></td>
                </tr>
                <tr>
                    <!--<td class="<?php if ($svcStates['acecounter'] === true) {
                        echo 'active';
                    } ?>"><a href="<?php echo URI_ADMIN; ?>">에이스카운터</a></td>-->
                    <td class="<?php if ($svcStates['bankda'] === true) {
                        echo 'active';
                    } ?>"><a href="<?php echo URI_ADMIN; ?>order/bankda_service.php">무통장자동입금</a></td>
                    <td class="<?php if ($svcStates['godobill'] === true) {
                        echo 'active';
                    } ?>"><a href="<?php echo URI_ADMIN; ?>order/tax_invoice_config.php">고도빌</a></td>
                </tr>
                <tr>
                    <td class="<?= $svcStates['pmail'] === true ? 'active' : '' ?>"><a href="<?= URI_ADMIN; ?>crm/mail_config_pmail.php">파워메일</a></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
<?php } ?>
