<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/article-view.css')?>" rel="stylesheet"/>

<article class="ncua-content article-view <?php if($req['popupMode'] =='yes') { ?>popup-mode<?php } ?>">
    <header class="page-header <?php if($req['popupMode'] != 'yes') {?>js-affix<?php } ?>  ncua-page-header">
        <h3 class="<?php if (!gd_is_provider()) { ?>ncua-help-manual<?php } ?>">
            <?php if($req['popupMode'] !='yes') { ?>
                <button type="button" class="ncua-btn ncua-btn--md only-icon ncua-btn--secondary-gray ncua-history-back js-btn-back">뒤로가기</button>
            <?php } ?>
            <?= end($naviMenu->location); ?>

        </h3>
        <div class="ncua-page-header__actions">
        <?php if($req['popupMode'] !='yes') { // CRM 팝업모드가 아닐 경우 ?>
            <a href="javascript:btnList('<?= $req['bdId'] ?>')" class="ncua-btn ncua-btn--md ncua-btn--secondary-gray">목록</a>
        <?php }  ?>
        </div>
    </header>
    <?php include "_article_detail.php" ?>
    <?php include $articleReply ?>
    <div class="button-wrap">
        <?php if($bdView['data']['auth']['modify'] == 'y' && $isShow == 'y'){?>
        <button onclick="btnModifyWrite('<?= $req['bdId'] ?>','<?= $req['sno'] ?>')" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray">수정</button>
        <?php }?>
        <?php if($bdView['data']['auth']['reply'] == 'y' && $isShow == 'y'){?>
        <button onclick="btnReplyWrite('<?= $req['bdId'] ?>','<?= $req['sno'] ?>')" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray">답변</button>
        <?php }?>
        <?php if($bdView['data']['auth']['delete'] == 'y' && $listType == 'board'){?>
        <button onclick="ncuaBtnDelete('<?= $req['bdId'] ?>','<?= $req['sno'] ?>', '<?=$req['popupMode'] ?>', '<?=$bdView['cfg']['bdReplyDelFl']?>', '<?=$isShow?>')" class="ncua-btn ncua-btn--sm ncua-btn--destructive">삭제</button>
        <?php }?>
        <?php if($bdView['data']['auth']['delete'] == 'y' && $listType == 'memo'){?>
            <button onclick="ncuaBtnMemoDelete('<?= $req['bdId'] ?>','<?= $bdView['data']['bdSno'] ?>','<?= $req['sno'] ?>', '<?=$req['popupMode'] ?>')" class="ncua-btn ncua-btn--sm ncua-btn--destructive">삭제</button>
        <?php }?>
        <?php if($isShow == 'n'){
            $goodsNo = ($req['bdId'] == 'goodsreview') ? $bdView['data']['goodsNo'] : '';
            ?>
            <button onclick="ncuaBtnReport('<?= $req['bdId'] ?>','<?= $req['sno'] ?>', '<?=$req['popupMode'] ?>', '<?=$listType?>', '<?=$goodsNo?>')" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray">신고해제</button>
        <?php }?>
    </div>
</article>
<script type="text/javascript">
    document.querySelector('.js-btn-back').addEventListener('click', function() {
        history.back();
    });
</script>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', () => {
        // 댓글 삭제 버튼
        const memoContainer = document.querySelector('.ncua-memo-list');

        if (!memoContainer) {
            return;
        }

        memoContainer.addEventListener('click', (e) => {
            const deleteBtn = e.target.closest('.js-btn-delete');
            if (!deleteBtn) return;

            const row = deleteBtn.closest('tr');
            const form = row.querySelector('form');
            const modeInput = row.querySelector('input[name=mode]');

            NCDSConfirm({message: "정말로 삭제하시겠습니까?",
                callback: (result) => {
                    if (result) {
                        modeInput.value = 'delete';
                        form.submit();
                    }
                }
            });
        });
    });

    /**
     * 게시판 액션 공통 처리 함수
     * @param {Object} options - 액션 옵션
     * @param {string} options.confirmMessage - 확인 메시지
     * @param {string} options.ajaxUrl - Ajax 요청 URL
     * @param {Object} options.ajaxData - Ajax 요청 데이터
     * @param {string} options.redirectUrl - 리다이렉트 URL
     * @param {string} options.popupMode - 팝업 모드 여부
     */
    const executeBoardAction = ({confirmMessage, ajaxUrl, ajaxData, redirectUrl, popupMode}) => {
        NCDSConfirm({
            message: confirmMessage,
            callback: (result) => {
                if (result) {
                    $.ajax({
                        method: "POST",
                        url: ajaxUrl,
                        data: ajaxData,
                        dataType: 'text'
                    }).success(function (data) {
                        if (popupMode == 'yes') {
                            dialog_alert(data, '알림');
                        } else {
                            $('body').append(data);
                            location.href = redirectUrl;
                        }
                    }).error(function (e) {
                        alert(e.responseText);
                    });
                }
            }
        });
    };

    const ncuaBtnDelete = (bdId, sno, popupMode, bdReplyDelFl, isShow) => {
        let confirmMsg = bdReplyDelFl == 'reply'
            ? '해당 글의 답변글도 함께 삭제됩니다.<br />'
            : '';

        executeBoardAction({
            confirmMessage: confirmMsg + '정말 삭제하시겠습니까?',
            ajaxUrl: '../board/article_ps.php',
            ajaxData: {mode: 'delete', sno: sno, bdId: bdId, popupMode: popupMode, bdViewDel: 'y'},
            redirectUrl: `../board/article_list.php?bdId=${bdId}&isShow=${isShow}`,
            popupMode: popupMode
        });
    };

    const ncuaBtnMemoDelete = (bdId, bdSno, sno, popupMode) => {
        executeBoardAction({
            confirmMessage: '정말 삭제하시겠습니까?',
            ajaxUrl: '../board/memo_ps.php',
            ajaxData: {mode: 'delete', sno: sno, bdId: bdId, bdSno: bdSno, popupMode: popupMode},
            redirectUrl: `../board/article_list.php?isShow=n&listType=memo&bdId=${bdId}`,
            popupMode: popupMode
        });
    };

    const ncuaBtnReport = (bdId, sno, popupMode, listType, goodsNo) => {
        if (_.isUndefined(listType)) {
            listType = 'board';
        }

        executeBoardAction({
            confirmMessage: '선택한 게시물을 신고해제 하시겠습니까?<br />이 경우 기존 신고내역은 확인 불가합니다',
            ajaxUrl: '../board/article_ps.php',
            ajaxData: {mode: 'report', sno: sno, bdId: bdId, popupMode: popupMode, listType: listType, goodsNo: goodsNo},
            redirectUrl: `../board/article_list.php?isShow=n&listType=${listType}&bdId=${bdId}`,
            popupMode: popupMode
        });
    };
</script>
