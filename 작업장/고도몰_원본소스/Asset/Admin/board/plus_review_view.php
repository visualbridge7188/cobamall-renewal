<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/plus-review-view.css')?>" rel="stylesheet"/>

<article class="ncua-content">
    <div class="plus_review_view">
        <header class="page-header js-affix ncua-page-header">
            <h3>
                <button type="button" class="ncua-btn ncua-btn--md only-icon ncua-btn--secondary-gray ncua-history-back js-btn-back">뒤로가기</button>
                <?= end($naviMenu->location); ?>
            </h3>
            <?php if($req['popupMode'] != 'yes') { // CRM 팝업모드가 아닐 경우 ?>
            <div class="ncua-page-header__actions">
                <button type="button" class="ncua-btn ncua-btn--md ncua-btn--secondary-gray js-btn-list">목록</button>
            </div>
            <?php } ?>
        </header>

        <!-- 플러스리뷰 게시글 보기 -->
        <?php include $plusReviewView ?>
        
    </div>
</article>
<script>
    // URL 파라미터 처리 유틸리티
    const getUrlQueryString = (paramKey = '') => {
        try {
            const url = new URL(window.location.href);
            const searchParams = new URLSearchParams(url.search);
            
            if (paramKey) {
                return searchParams.get(paramKey) || '';
            }
            
            const excludeParams = ['sno', 'mode'];
            const filteredParams = Array.from(searchParams.entries())
                .filter(([key]) => !excludeParams.includes(key))
                .map(([key, value]) => `${key}=${value}`);
                
            return filteredParams.join('&');
        } catch (error) {
            console.error('URL 파싱 중 오류 발생:', error);
            return '';
        }
    };

    // DOM 조작 유틸리티
    const toggleElements = (showElements, hideElements) => {
        // 배열이 아닌 경우 배열로 변환
        const elementsToShow = Array.isArray(showElements) ? showElements : [showElements];
        const elementsToHide = Array.isArray(hideElements) ? hideElements : [hideElements];
        
        elementsToShow.forEach(element => element.style.display = '');
        elementsToHide.forEach(element => element.style.display = 'none');
    };

    // 신고 해제 API 호출
    const reportArticle = async (sno, listType) => {
        try {
            const response = await fetch('./article_ps.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    mode: 'report',
                    sno,
                    bdId: 'plusReview',
                    listType
                })
            });

            if (!response.ok) {
                throw new Error('네트워크 응답이 올바르지 않습니다');
            }

            const data = await response.text();
            document.body.insertAdjacentHTML('beforeend', data);
            location.href = `../board/plus_review_list.php?isShow=n&listType=${listType}`;
        } catch (error) {
            NCDSAlert({message: error.message, iconType: 'error'});
        }
    };

    // 이벤트 핸들러
    document.addEventListener('DOMContentLoaded', () => {
        // 뒤로가기 버튼
        document.querySelector('.js-btn-back')?.addEventListener('click', () => {
            history.back();
        });

        // 목록 버튼
        document.querySelector('.js-btn-list')?.addEventListener('click', () => {
            location.href = "plus_review_list.php?" + getUrlQueryString();
        });

        // 수정 버튼
        document.querySelector('.js-btn-modify')?.addEventListener('click', () => {
            location.href = "plus_review_register.php?mode=modify&sno=<?=$req['sno']?>&popupMode=<?=$req['popupMode']?>&queryString=<?=urlencode($queryString)?>";
        });

        // 삭제 버튼
        document.querySelector('.js-btn-remove')?.addEventListener('click', () => {
            document.forms.frmDelete.submit();
        });

        // 댓글 수정 버튼들
        document.querySelectorAll('.js-btn-memo-modify').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.target.closest('tr');
                const textarea = row.querySelector('.js-textarea-modify-memo');
                const memoText = row.querySelector('.js-text-memo');
                const submitButton = row.querySelector('.js-btn-memo-submit');
                const modifyButton = e.target;

                // 수정 모드 활성화
                memoText.style.display = 'none';
                modifyButton.style.display = 'none';
                textarea.style.display = 'block';
                submitButton.style.display = 'block';
            });
        });

        // 댓글 등록 버튼들
        document.querySelectorAll('.js-btn-memo-submit').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.target.closest('tr');
                const form = row.querySelector('form');
                const textarea = row.querySelector('.js-textarea-modify-memo');
                const memoText = row.querySelector('.js-text-memo');
                const modifyButton = row.querySelector('.js-btn-memo-modify');
                
                form.submit();
                
                // 수정 모드 해제
                memoText.style.display = 'block';
                modifyButton.style.display = 'block';
                textarea.style.display = 'none';
                e.target.style.display = 'none';
            });
        });

        // 댓글 삭제 버튼들
        document.querySelectorAll('.js-btn-memo-delete').forEach(button => {
            button.addEventListener('click', (e) => {
                const row = e.target.closest('tr');
                const form = row.querySelector('form');
                const modeInput = row.querySelector('input[name=mode]');

                NCDSConfirm({message: "정말로 삭제하시겠습니까?",
                    callback: (result) => {
                        if (result) {
                            modeInput.value = 'deleteMemo';
                            form.submit();
                        }
                    }
                });
            });
        });

        // 신고 해제 버튼
        document.querySelector('.js-btn-report')?.addEventListener('click', () => {
            const sno = '<?= $req['sno'] ?>';
            const listType = '<?= $listType ?>';
            
            NCDSConfirm({message: "선택한 게시물을 신고해제 하시겠습니까?<br/>이 경우 기존 신고내역은 확인 불가합니다",
                callback: (result) => {
                    if (result) {
                        reportArticle(sno, listType);
                    }
                }
            });
        });
    });
</script>
