/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

/**
 * 정기결제 카드 등록
 */
function card_register(pgName) {
    if (pgName === undefined || pgName === null || pgName === '') {
        alert('카드 등록 기능이 준비 중입니다. 자세한 내용은 쇼핑몰에 문의해 주세요.');
        return;
    }

    if (!document.getElementById("cardRegistLayer")) {
        let div = document.createElement('div');
        div.id = 'cardRegistLayer';
        div.className = 'layer_wrap cardRegistLayer dn';
        document.body.appendChild(div);
    }

    let iframe = document.createElement('iframe');
    iframe.id = 'cardRegistLayer';
    iframe.src = '../payment/' + pgName + '/pg_card_register.php';
    iframe.style.width = '100%';
    iframe.style.height = '100%';
    iframe.style.border = 'none';

    // iframe 안의 pgSettleStart 함수 호출 (PC: jsf__pay 호출 / Mobile: kcp_AJAX 호출)
    iframe.onload = function() {
        try {
            let iframeWindow = iframe.contentWindow;
            if (typeof iframeWindow.jsf__pay === 'function') {
                // PC: 카드등록 레이어 유지(딤 노출). 정리는 PgCardRegister 콜백(m_Completepayment)에서 처리
                iframeWindow.jsf__pay();
            } else if (typeof iframeWindow.kcp_AJAX === 'function') {
                // Mobile: pay_form을 최상위 창에서 이동하도록 설정 후 KCP AJAX 실행
                if (iframeWindow.document.pay_form) {
                    iframeWindow.document.pay_form.target = '_top';
                }
                iframeWindow.kcp_AJAX();
                $('#cardRegistLayer').addClass('dn');
            }
        } catch (e) {
            $('#cardRegistLayer').remove();
        }
    };

    $('#cardRegistLayer').removeClass('dn');
    $('#cardRegistLayer').find('> div').position({
        my: "center center",
        at: "center center",
        of: window
    });
    
    // 부모 문서의 body에 iframe 추가.
    document.getElementById("cardRegistLayer").appendChild(iframe);
    
    // iframe 추가 후 미노출 필요한 클래스들을 숨김 처리
    hideOtherElements();
}

/**
 * iframe 표시 시 미노출 처리 함수
 */
function hideOtherElements() {
    // 숨기고 싶은 클래스들의 배열
    const classesToHide = [
        '.header_search',
        '.scroll_wrap'
    ];
    
    classesToHide.forEach(className => {
        const elements = document.querySelectorAll(className);
        elements.forEach(element => {
            element.style.display = 'none';
        });
    });
}
