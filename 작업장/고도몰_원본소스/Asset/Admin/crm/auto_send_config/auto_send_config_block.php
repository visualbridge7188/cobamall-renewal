<?php if (!empty($blockMode)): ?>
<script>
    <?php if ($blockMode === 'claim'): ?>
    NCDSAlert({
        message: '해당 알림은 현재 사용할 수 없습니다.',
        subMessage: '고객 직접 교환/반품/환불 신청 기능이 비활성화 상태입니다.',
        iconType: 'error',
        callback: function () {
            window.location.href = 'auto_send.php';
        }
    });
    <?php elseif ($blockMode === 'smsConnect'): ?>
    NCDSAlert({
        message: '일시적인 오류가 발생하였습니다. \n다시 시도해 주세요.',
        iconType: 'error',
        callback: function () {
            window.location.href = 'auto_send.php';
        }
    });
    <?php endif; ?>
</script>
<?php return true; endif; ?>
<?php return false; ?>