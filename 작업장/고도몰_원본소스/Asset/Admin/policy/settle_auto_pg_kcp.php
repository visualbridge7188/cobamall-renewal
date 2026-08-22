<input type="hidden" name="mode" value="kcp_auto" />
<input type="hidden" name="pgName" value="kcpbilling" />
<div class="table-title">
    <span class="gd-help-manual">NHN KCP 정기결제 설정</span>
</div>
<table class="table table-cols">
    <colgroup>
        <col class="width-md"/>
        <col/>
    </colgroup>
    <tr>
        <th>결제수단 설정</th>
        <td>
            <label class="checkbox-inline"><input type="checkbox" name="settleKind" value="y" checked="checked" disabled/>신용카드</label>
        </td>
    </tr>
    <tr>
        <th>NHN KCP 정기결제 사이트 코드</th>
        <td>
            <?php if (empty($config['siteCode'])) {?>
            <div class="notice-info notice-danger">정기결제 PG 신청 전 또는 승인대기 상태입니다.</div>
            <div class="notice-info">
                신청 전인 경우 먼저 서비스를 신청하세요.
                <a href="<?=$pgLinkUrl?>" target="_blank" id="tutorial-step-guide-done" class="btn btn-gray btn-sm">정기결제 PG 신청</a>
            </div>
            <?php } else { ?>
                <span class="text-blue bold"><?php echo $config['siteCode']; ?> (자동 설정 완료)</span>
                <input type="hidden" name="siteCode" value="<?php echo $config['siteCode']; ?>">
                <input type="button" onclick="settleKindUpdate();" value="NHN KCP 정보 갱신" class="btn btn-gray btn-sm" />
            <?php } ?>
        </td>
    </tr>
    <tr>
        <th>NHN KCP 정기결제 사이트 키</th>
        <td>
        <?php if (empty($config['siteKey'])) {?>
            <div class="notice-info notice-danger">정기결제 PG 신청 전 또는 승인대기 상태입니다.</div>
        </td>
        <?php } else { ?>
            <span class="text-blue bold"><?php echo $config['siteKey']; ?> (자동 설정 완료)</span>
            <input type="hidden" name="siteKey" value="<?php echo $config['siteKey']; ?>">
        <?php } ?>
    </tr>
    <tr>
        <th>NHN KCP 정기결제 그룹 ID</th>
        <td>
        <?php if (empty($config['kcpgroupId'])) {?>
            <div class="notice-info notice-danger">정기결제 PG 신청 전 또는 승인대기 상태입니다.</div>
        <?php } else { ?>
            <span class="text-blue bold"><?php echo $config['kcpgroupId']; ?> (자동 설정 완료)</span>
            <input type="hidden" name="groupId" value="<?php echo $config['kcpgroupId']; ?>">
        <?php } ?>
        </td>
    </tr>
</table>

<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/aggregator/TutorialHandler.js"></script>
<script type="text/javascript">
    window.GodoTutorial.tutorialHandlerInstance = new GodoTutorial.TutorialHandler({
        category: 'PAYMENT_TUTORIAL',
        code: '<?= $tutorialCode ?>',
        mode: '<?= $tutorialMode ?>',
        linkModalUrl: '<?= $tutorialLinkModalUrl ?>',
        linkModalSize: <?= json_encode($tutorialLinkModalSize ?? []) ?>,
        stepGuideSteps: <?= json_encode($tutorialStepGuideSteps ?? []) ?>,
        stepGuideButtonLabel: {
            prev: '이전',
            next: '다음',
            done: '신청 하기',
            skip: '나중에 하기'
        },
        stepGuideLinks: <?= json_encode($tutorialStepGuideLinks ?? []) ?>,
    });
</script>

