
<link rel="stylesheet" href="<?= PATH_ADMIN_GD_SHARE ?>ncds/css/board/board-list.css">
<article class="ncua-content board-list">
    <header class="page-header ncua-page-header js-affix">
        <h3 class="ncua-help-manual"><?= end($naviMenu->location); ?>
        </h3>
        <span class="ncua-page-header__actions">
            <button type="button" class="ncua-btn ncua-btn--md ncua-btn--primary js-register">게시판 등록</button>
        </span>
    </header>

    <section class="ncua-card">
        <header class="ncua-card__header">
            <h4 class="ncua-card__title">게시판 검색</h4>
        </header>
        
        <section class="ncua-card__body">
            <!-- 검색 -->
            <?php include $boardListSearch; ?>
            <!-- // 검색 -->
            <!-- 검색 결과 -->
            <?php include $boardListResult; ?>
            <!-- // 검색 결과 -->
        </section>
    </section>
</article>

<script type="text/javascript">
    $(document).ready(function () {
        $('#frmList').validate({
            dialog: false,
            invalidHandler: function(event, validator) {
                if (validator.errorList.length > 0) {
                    NCDSAlert({
                        message: validator.errorList[0].message,
                        iconType: 'error'
                    });
                }
            },
            submitHandler: function (form) {
                form.target = 'ifrmProcess';
                NCDSConfirm({message: '선택한 게시판을 삭제하시겠습니까?\n\r영구 삭제되어 복원 불가능합니다.', callback: function (result) {
                    if (result) {
                        form.submit();
                    }
                }});

            },
            rules: {
                'sno[]': {
                    required: true
                }
            },
            messages: {
                'sno[]': {
                    required: '선택한 게시판이 없습니다.'
                },

            },
        });

        // 등록
        $('.js-register').click(function () {
            location.href = 'board_register.php';
        });
        
        // 관리자 접속 후 사용자화면 클릭했을경우 fl추가
        $('.user-board').click(function (){
            $.post('./article_ps.php', {
                'mode': 'userBoardChk',
                'fl': $(this).data('fl')
            }, function (data) {
                console.log(data);
            });
        });

        //검색어 변경 될 때 placeHolder 교체 및 검색 종류 변환 및 검색 종류 변환
        var searchKeyword = $('#frmSearchBase input[name="keyword"]');
        var searchKind = $('#frmSearchBase #searchKind');
        setKeywordPlaceholder(searchKeyword, searchKind);
        searchKind.change(function (e) {
            setKeywordPlaceholder(searchKeyword, searchKind);
        });

        // 초기화 버튼 기능
        $('#btnReset').click(function() {
            // 검색어 초기화
            $('#frmSearchBase input[name="keyword"]').val('');
            
            // 검색 필드 선택박스 초기화 (첫 번째 옵션으로)
            $('#frmSearchBase select[name="key"]').prop('selectedIndex', 0);
            
            // 검색 종류 선택박스 초기화 (첫 번째 옵션으로)
            $('#frmSearchBase select[name="searchKind"]').prop('selectedIndex', 0);
            
            // 체크박스 모두 해제
            $('#frmSearchBase input[type="checkbox"]').prop('checked', false);
            
            // 검색어 플레이스홀더 재설정
            setKeywordPlaceholder(searchKeyword, searchKind);
            
            // 초기화 후 검색 실행
            $('#frmSearchBase').submit();
        });

        // 선택 삭제 버튼 활성화/비활성화 처리
        const updateDeleteButtonState = () => {
            const deleteButton = document.getElementById('btnDeleteSelected');
            if (!deleteButton) return;
            
            const checkedBoxes = document.querySelectorAll('#frmList input[name="sno[]"]:checked:not(:disabled)');
            deleteButton.disabled = !checkedBoxes.length;
        };

        // 체크박스 변경 시 버튼 상태 업데이트
        const formList = document.getElementById('frmList');
        if (formList) {
            formList.addEventListener('change', (e) => {
                if (e.target.matches('input[name="sno[]"]') || e.target.matches('.js-checkall')) {
                    updateDeleteButtonState();
                }
            });
        }

        // 초기 상태 설정
        updateDeleteButtonState();
    });
</script>

<script type="text/javascript">
    const code = '251023002';
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
        });
    }
</script>
