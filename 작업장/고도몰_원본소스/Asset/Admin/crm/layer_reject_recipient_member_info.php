<div class="modal-dialog__content">
    <article class="ncua-content reject-recipient-member-info">
        <p>
            발송대상 전체 <?= $totalCount ?>명 수신거부회원 (<?= $rejectedCount ?>명) 입니다.<br />
            수신거부회원에게 메시지 발송하시겠습니까?<br />
            수신거부한 회원에게 광고성 정보를 발송하는 경우 과태료가 부과될 수 있습니다.
        </p>
    </article>
</div>
<div class="modal-dialog__footer">
    <button type="button" id="cancelSendWithReject" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray">발송 취소</button>
    <button type="button" id="sendWithReject" class="ncua-btn ncua-btn--sm ncua-btn--secondary">수신거부회원에게 발송</button>
    <button type="button" id="sendOnlyAgreed" class="ncua-btn ncua-btn--sm ncua-btn--primary">제외하고 발송</button>
</div>
<script>
    $(document).ready(function () {
        document.querySelector('#cancelSendWithReject').addEventListener('click', function () {
            layer_close();
        });

        document.querySelector('#sendWithReject').addEventListener('click', function () {
            postSendMessage();
            layer_close();
        });

        document.querySelector('#sendOnlyAgreed').addEventListener('click', function () {
            postSendMessage('fixOnlyAgreed');
            layer_close();
        });
    })
</script>
