<link rel="stylesheet" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-my-app-send-guide.css')?>">
<div class="modal-dialog__content layer-my-app-send-guide">
    <ul class="send-guide-notice">
        <li class="ncua-caution-text">정보통신망법 <a href="https://www.law.go.kr/LSW//lsInfoP.do?lsId=000030&ancYnChk=0#0000" target="_blank" class="ncua-btn ncua-btn--xs ncua-btn--text has-underline">제50조</a>에 따라 광고성 메시지 전송 시에는 명시사항을 포함하여 발송해야 합니다.</li>
        <li class="ncua-caution-text">위반 시 정보통신망법 <a href="https://www.law.go.kr/LSW//lsInfoP.do?lsId=000030&ancYnChk=0#0000" target="_blank" class="ncua-btn ncua-btn--xs ncua-btn--text has-underline">제76조</a>에 따라 3천만원 이하의 과태료가 부과될 수 있습니다.</li>
        <li class="ncua-caution-text">필요한 조치를 취하지 않아 발생하는 불이익에 대하여 당사는 책임을 지지 않습니다.</li>
    </ul>
    <div class="ncua-table ncua-table--vertical">
        <table>
            <tbody>
                <tr>
                    <th><div>준수사항</div></th>
                    <td>
                        <div>    
                            <ul>
                                <li>1. 전송자의 명칭 및 연락처 표시</li>
                                <li>2. 푸시 제목 또는 내용 시작 부분에 (광고) 표시</li>
                                <li>3. 수신거부 방법 표시</li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><div>예시</div></th>
                    <td>
                        <div>
                            <img src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/img-message-send-guide-sample.png" alt="광고성 메시지 전송 시 유의사항 예시">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="modal-dialog__footer">
    <button onclick="layer_close()" type="button" class="ncua-btn ncua-btn--sm ncua-btn--primary">확인</button>
</div>
