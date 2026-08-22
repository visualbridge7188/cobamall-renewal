<link rel="stylesheet" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-kakao-template-comment.css')?>">
<div class="modal-dialog__content layer_kakao_template_comment ">
    <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs">
        <textarea name="comment-content" class="ncua-input__textarea ncua-border-content-layout" readonly><?= $comment ?></textarea>
    </div>
</div>
<script>
    (function() {
        // requestAnimationFrame을 이중 중첩하여 레이아웃이 완전히 계산된 후 실행
        requestAnimationFrame(function() {
            requestAnimationFrame(function() {
                const textarea = document.querySelector('.layer_kakao_template_comment textarea[name="comment-content"]');
                const modalContent = document.querySelector('.layer_kakao_template_comment');
                
                if (textarea && modalContent) {
                    // modal-dialog__content의 최대 높이 계산
                    const maxModalHeight = 680;
                    const computedStyle = getComputedStyle(modalContent);
                    const modalPadding = parseFloat(computedStyle.paddingTop) + parseFloat(computedStyle.paddingBottom);
                    const maxTextareaHeight = maxModalHeight - modalPadding - 20;
                    
                    // textarea 높이를 내용에 맞게 자동 조정
                    textarea.style.height = 'auto';
                    const contentHeight = textarea.scrollHeight;
                    
                    // 내용 높이가 최대 높이보다 작으면 내용에 맞춤, 크면 최대 높이로 제한
                    if (contentHeight <= maxTextareaHeight) {
                        textarea.style.height = contentHeight + 'px';
                        textarea.style.overflowY = 'hidden';
                    } else {
                        textarea.style.height = maxTextareaHeight + 'px';
                        textarea.style.overflowY = 'auto';
                    }
                }
            });
        });
    })();
</script>
