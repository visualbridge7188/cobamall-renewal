/**
 * NCDS Card Toggle
 * ncua-card__toggle 버튼 클릭 시 opened 클래스를 토글합니다.
 */
(function() {
    'use strict';

    // 문서 로드 완료 후 이벤트 리스너 등록
    document.addEventListener('DOMContentLoaded', function() {
        initCardToggle();
    });

    /**
     * 카드 토글 초기화
     */
    function initCardToggle() {
        // 이벤트 위임을 사용하여 동적으로 추가된 버튼도 처리
        document.addEventListener('click', function(event) {
            // 클릭된 요소가 ncua-card__toggle 클래스를 가진 버튼인지 확인
            if (event.target.classList.contains('ncua-card__toggle')) {
                handleToggle(event.target);
            }
        });
    }

    /**
     * 토글 버튼 처리
     * @param {HTMLElement} button - 토글 버튼 요소
     */
    function handleToggle(button) {
        // opened 클래스 토글
        button.classList.toggle('opened');

        // 카드 본문(body) 표시/숨김 처리 (선택적)
        const card = button.closest('.ncua-card');
        if (card) {
            const cardBody = card.querySelector('.ncua-card__body');
            if (cardBody) {
                if (button.classList.contains('opened')) {
                    cardBody.style.display = '';
                } else {
                    cardBody.style.display = 'none';
                }
            }
        }
    }

})();
