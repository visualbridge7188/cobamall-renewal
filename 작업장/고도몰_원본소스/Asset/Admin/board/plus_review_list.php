<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/plus-review-list.css')?>" rel="stylesheet"/>

<article class="ncua-content plus-review-list">
    <header class="ncua-page-header page-header js-affix">
        <h3>
            <?= end($naviMenu->location); ?>
        </h3>
    </header>

    <section class="ncua-card">
        <header class="ncua-card__header">
            <h4 class="ncua-card__title">플러스리뷰 게시글 검색</h4>
        </header>
        <section class="ncua-card__body">
            <?php include $plusReviewListSearch; ?>
            <?php include $plusReviewListResult; ?>
        </section>
    </section>
</article>

<script type="text/javascript">
    <?php
    // 날짜 차이 계산하여 현재 선택된 period 확인
    $startDate = isset($req['rangDate'][0]) && $req['rangDate'][0] ? strtotime($req['rangDate'][0]) : null;
    $endDate = isset($req['rangDate'][1]) && $req['rangDate'][1] ? strtotime($req['rangDate'][1]) : null;
    
    $currentPeriod = null;
    if ($startDate && $endDate) {
        $dateDiff = floor(($endDate - $startDate) / 86400); // 일 단위 차이
        
        // 각 period별로 isCurrent 설정
        $periods = [0, 6, 14, 29, 89, 364];
        if (in_array($dateDiff, $periods)) {
            $currentPeriod = $dateDiff;
        }
    }

    ?>
    const datePicker = new ncua.DatePicker(document.querySelector('#datepicker-container'), {
        size: 'xs', 
        buttons: [
            {
                text: '오늘',
                period: 0,
                unit: 'days',
                isCurrent: <?= ((int)$currentPeriod === 0) ? 'true' : 'false' ?>,
            },
            {
                text: '7일',
                period: 6,
                unit: 'days',
                isCurrent: <?= ((int)$currentPeriod === 6) ? 'true' : 'false' ?>,
            },
            {
                text: '15일',
                period: 14,
                unit: 'days',
                isCurrent: <?= ((int)$currentPeriod === 14) ? 'true' : 'false' ?>,
            },
            {
                text: '1개월',
                period: 29,
                unit: 'days',
                isCurrent: <?= ((int)$currentPeriod === 29) ? 'true' : 'false' ?>,
            },
            {
                text: '3개월',
                period: 89,
                unit: 'days',
                isCurrent: <?= ((int)$currentPeriod === 89) ? 'true' : 'false' ?>,
            },
            {
                text: '1년',
                period: 364,
                unit: 'days',
                isCurrent: <?= ((int)$currentPeriod === 364) ? 'true' : 'false' ?>,
            },
        ],
        datePickerOptions: [
            {
                element: 'start-date',
                attrName: 'rangDate[]',
                options: {
                    mode: 'single',
                    static: true,
                    dateFormat: 'Y-m-d',
                    clickOpens: true,
                    allowInvalidPreload: true,
                    locale: 'ko',
                },
            },
            {
                element: 'end-date',
                attrName: 'rangDate[]',
                options: {
                    mode: 'single',
                    static: true,
                    dateFormat: 'Y-m-d',
                    clickOpens: true,
                    allowInvalidPreload: true,
                    locale: 'ko',
                },
            },
        ],
    });

    datePicker.setDate(["<?= $req['rangDate'][0]; ?>", "<?= $req['rangDate'][1]; ?>"]);


    var mileageFlConfig = '<?=$mileageFlConfig;?>';

    function modify(sno) {
        location.href = 'plus_review_register.php?sno=' + sno+"&mode=modify&<?=Request::getQueryString()?>";
    }

    function view(sno) {
        location.href = 'plus_review_view.php?sno=' + sno+"&<?=Request::getQueryString()?>";
    }

    function milageAdd(sno) {
        var title = "마일리지 지급";
        $.get('./plus_review_milage.php',{ sno: sno }, function(data){
            if (data.result && data.result === 'fail') {
                NCDSAlert({ message: data.message, iconType: 'error' });
                return false;
            }

            data = '<div id="viewInfoForm">'+data+'</div>';

            var layerForm = data;

            BootstrapDialog.show({
                title:title,
                size: get_layer_size('wide-sm'),
                message: $(layerForm),
                cssClass: 'ncds-modal',
                closable: true
            });
        });
    }

    function applySet(sno,goodsNo) {
        $.post('./plus_review_ps.php', {'mode': 'applySet', 'sno': sno, 'goodsNo': goodsNo}, function (data) {
            if (data.result == 'ok') {
                {
                    NCDSAlert({ message:data.msg, iconType: 'success' });
                    $(`.js-apply-button-${sno} div`).text('승인완료');
                }
            }
        });
    }

    $(document).ready(function () {
        var listType = '<?=$listType;?>';
        $('.no-data').attr('colspan', $('#frmList table thead th').length);

        $('select[name=\'pageNum\']').change(function () {
            $('#frmSearch input[name="pageNum"]').val($(this).val());
            $('#frmSearch').submit();
        });

        $('select[name=\'sort\']').change(function () {
            $('#frmSearch input[name="sort"]').val($(this).val());
            $('#frmSearch').submit();
        });

        $('button.js-btn-delete').click(function () {
            var obj = $('input[name*="sno["]:checkbox:checked');
            var chkCnt = obj.length;
            if (chkCnt == 0) {
                NCDSAlert({ message: '선택된 게시글이 없습니다.', iconType: 'error' });
                return;
            }

            NCDSConfirm({ message: '선택한 게시글을 삭제하시겠습니까?\n\r영구 삭제되어 복원 불가능합니다.', callback: function (result) {
                if (result) {
                    var mode = (listType == 'board') ? 'delete' : 'deleteMemo';
                    $('#frmList input[name=\'mode\']').val(mode);
                    $('#frmList').submit();
                }
            }});
        });

        $('button.js-btn-report').click(function() {
            var obj = $('input[name*="sno["]:checkbox:checked');
            var chkCnt = obj.length;
            if (chkCnt == 0) {
                NCDSAlert({ message: '선택된 게시글이 없습니다.', iconType: 'error' });
                return;
            }

            NCDSConfirm({ message:'선택한 게시물을 신고해제 하시겠습니까?\n\r신고해제 시, 기존 신고내역은 확인 불가합니다.', callback: function (result) {
                if (result) {
                    $('#frmList input[name=\'mode\']').val('report');
                    $('#frmList').submit();
                }
            }});
        });

        $('button.js-btn-apply').click(function () {
            var obj = $('input[name*="sno["]:checkbox:checked');
            var chkCnt = obj.length;
            var chkCnt2 = 0;
            var val = $(this).data('value');
            if (chkCnt == 0) {
                NCDSAlert({ message: '선택된 게시글이 없습니다.', iconType: 'error' });
                return;
            }
            if(val == 'y'){
                $.each(obj, function (index, item) {
                    if( $('.js-apply-button-' + item.value).find('button, input[type="button"]').length == 0 ){
                        chkCnt2++;
                    };
                });
                if (chkCnt2 != 0) {
                    NCDSAlert({ message: '승인완료된 게시글이 존재합니다.', iconType: 'error' });
                    return;
                }
            }else{
                $.each(obj, function (index, item) {
                    if( $('.js-apply-button-' + item.value).find('button, input[type="button"]').length == 1 ){
                        chkCnt2++;
                    };
                });
                if (chkCnt2 != 0) {
                    NCDSAlert({ message: '미승인된 게시글이 존재합니다.', iconType: 'error' });
                    return;
                }
            }
            if(val == 'y') {
                NCDSConfirm({ message:'선택한 ' + chkCnt + '개의 게시글을 승인처리 하시겠습니까?', callback: function (result) {
                    if (result) {
                        $('#frmList input[name=\'mode\']').val('applySet');
                        $('#frmList').submit();
                    }
                }});
            }
            else {  //미승인
                NCDSConfirm({ message: '선택한 ' + chkCnt + '개의 게시글을 미승인처리 하시겠습니까?', callback: function (result) {
                    if (result) {
                        $('#frmList input[name=\'mode\']').val('notApplySet');
                        $('#frmList').submit();
                    }
                }});
            }

        });

        $('button.js-btn-milage').click(function () {
            var obj = $('input[name*="sno["]:checkbox:checked');
            var chkCnt = obj.length;
            var chkCnt2 = 0;
            if (mileageFlConfig !== 'y') {
                NCDSAlert({ message: "마일리지 지급 기능을 사용하시려면, 플러스리뷰 게시판 설정과 마일리지 기본 설정 메뉴에서 마일리지 사용유무 설정을 '사용함'으로 설정해주세요.", iconType: 'error' });
                return;
            }
            if (chkCnt == 0) {
                NCDSAlert({ message: '선택된 게시글이 없습니다.', iconType: 'error' });
                return;
            }
            if (chkCnt > 100) {
                NCDSAlert({ message: '마일리지 일괄 지급은 최대 100개까지 가능합니다.', iconType: 'error' });
                return;
            }
            var chkArry = [];
            $.each(obj, function (index, item) {
                if( $('.js-apply-milage-' + item.value).find('button, input[type="button"]').length == 0 ){
                    chkCnt2++;
                } else {
                    chkArry.push(item.value);
                }
            });
            if (chkCnt == chkCnt2) {
                NCDSAlert({ message: '마일리지 지급 가능한 게시글이 없습니다.', iconType: 'error' });
                return;
            }

            if (chkCnt2 === 0) {
                milageAdd(chkArry);
            } else {
                NCDSAlert({ message: '마일리지 지급완료/지급예정/지급불가 게시글은 제외됩니다.', iconType: 'error', callback: function() {
                    milageAdd(chkArry);
                } });
            }
        });

        // 초기화: 기존 plus-preview 요소 제거 및 컨테이너 생성
        document.querySelectorAll('.js-preview .plus-preview').forEach(el => el.remove());
        
        // 게시물 내용 상세보기 레이어 컨테이너 생성
        const ncuaContent = document.querySelector('.plus-review-list');
        let previewContainer = ncuaContent.querySelector('.plus-preview-container');
        if (!previewContainer) {
            previewContainer = document.createElement('div');
            previewContainer.className = 'plus-preview-container';
            ncuaContent.appendChild(previewContainer);
        }

        // 상수 정의
        const PREVIEW_WIDTH = 500;
        const PREVIEW_HEIGHT_DEFAULT = 550;
        const PREVIEW_TOP_OFFSET = 350;
        const PREVIEW_LEFT_OFFSET = 260;
        const BOUNDARY_PADDING = 10;
        const HIDE_DELAY = 100;

        // 타임아웃 관리 객체
        const hideTimeouts = {};
        
        // 각 preview 요소의 초기 마우스 위치 저장 (레이어 위치 고정용)
        const initialMousePositions = {};

        // jQuery의 offset()과 동일하게 문서 기준 절대 위치 반환
        const getOffset = (element) => {
            const rect = element.getBoundingClientRect();
            return {
                top: rect.top + window.scrollY,
                left: rect.left + window.scrollX
            };
        };

        // 마우스 위치를 ncua-content 기준으로 계산
        const getMouseY = (e) => {
            const contentOffset = getOffset(ncuaContent);
            return e.pageY - contentOffset.top;
        };

        // 타임아웃 취소
        const cancelHideTimeout = (previewId) => {
            if (hideTimeouts[previewId]) {
                clearTimeout(hideTimeouts[previewId]);
                delete hideTimeouts[previewId];
            }
        };

        // 레이어 위치 계산 및 설정
        const updatePreviewPosition = (previewTarget, previewLayer, mouseY) => {
            const contentOffset = getOffset(ncuaContent);
            const targetOffset = getOffset(previewTarget);
            
            // jQuery의 outerHeight(), outerWidth()는 offsetHeight, offsetWidth와 동일 (border 포함, margin 제외)
            const previewHeight = previewLayer.offsetHeight || PREVIEW_HEIGHT_DEFAULT;
            const contentHeight = ncuaContent.offsetHeight;
            const contentWidth = ncuaContent.offsetWidth;
            const targetWidth = previewTarget.offsetWidth;
            
            // Left 위치 계산 (jQuery offset() 기준)
            let relativeLeft = targetOffset.left - contentOffset.left + targetWidth - PREVIEW_LEFT_OFFSET;
            
            // Top 위치 계산: 마우스 위치가 레이어 중간에 오도록
            let relativeTop = mouseY - previewHeight / 2;
            
            // 상단 경계 체크
            if (relativeTop < BOUNDARY_PADDING) {
                relativeTop = BOUNDARY_PADDING;
            }
            
            // 하단 경계 체크
            if (relativeTop + previewHeight > contentHeight - BOUNDARY_PADDING) {
                relativeTop = mouseY - previewHeight;
                if (relativeTop < BOUNDARY_PADDING) {
                    relativeTop = BOUNDARY_PADDING;
                }
                if (relativeTop + previewHeight > contentHeight - BOUNDARY_PADDING) {
                    relativeTop = Math.max(BOUNDARY_PADDING, contentHeight - previewHeight - BOUNDARY_PADDING);
                }
            }
            
            // 좌우 경계 체크
            if (relativeLeft + PREVIEW_WIDTH > contentWidth - BOUNDARY_PADDING) {
                relativeLeft = contentWidth - PREVIEW_WIDTH - BOUNDARY_PADDING;
            }
            if (relativeLeft < BOUNDARY_PADDING) {
                relativeLeft = BOUNDARY_PADDING;
            }
            
            // 위치 설정
            previewLayer.style.top = (relativeTop - PREVIEW_TOP_OFFSET + 300) + 'px';
            previewLayer.style.left = (relativeLeft + 250) + 'px';
        };

        // 레이어 생성 및 이벤트 바인딩
        const createPreviewLayer = (previewId) => {
            let previewLayer = previewContainer.querySelector('#' + previewId);
            
            if (!previewLayer) {
                previewLayer = document.createElement('div');
                previewLayer.className = 'plus-preview loading';
                previewLayer.id = previewId;
                previewContainer.appendChild(previewLayer);
                
                // 레이어 마우스 이벤트 바인딩
                previewLayer.addEventListener('mouseenter', (e) => {
                    cancelHideTimeout(e.currentTarget.id);
                });
                
                previewLayer.addEventListener('mouseleave', (e) => {
                    const layerPreviewId = e.currentTarget.id;
                    const layerPreviewTarget = document.querySelector(`.js-preview[data-preview-id="${layerPreviewId}"]`);
                    hidePreviewLayer(layerPreviewId, layerPreviewTarget);
                });
            }
            
            return previewLayer;
        };

        // 레이어 숨김
        const hidePreviewLayer = (previewId, previewTarget) => {
            const previewLayer = previewContainer.querySelector('#' + previewId);
            
            if (previewLayer) {
                previewLayer.style.display = 'none';
            }
            if (previewTarget) {
                previewTarget.classList.remove('bg-color');
            }
            cancelHideTimeout(previewId);
        };

        // 데이터 로드
        const loadPreviewData = async (sno, previewLayer, previewTarget, previewId) => {
            if (previewLayer.innerHTML !== '' && !previewLayer.classList.contains('loading')) {
                return;
            }
            
            try {
                const params = new URLSearchParams({ sno: sno });
                const response = await fetch(`./plus_preview.php?${params}`);
                const data = await response.text();
                
                previewLayer.innerHTML = '';
                const viewInfoForm = document.createElement('div');
                viewInfoForm.id = 'viewInfoForm';
                viewInfoForm.innerHTML = data;
                previewLayer.appendChild(viewInfoForm);
                previewLayer.classList.remove('loading');
                
                // 데이터 로드 후 위치 재계산 (초기 마우스 위치 사용 - 고정된 위치)
                const initialMouseY = initialMousePositions[previewId] || 0;
                updatePreviewPosition(previewTarget, previewLayer, initialMouseY);
            } catch (error) {
                console.error('Preview data load error:', error);
            }
        };

        // 게시물 내용 미리보기 이벤트 핸들러 등록
        const previewElements = document.querySelectorAll('.js-preview');
        
        previewElements.forEach(previewElement => {
            previewElement.addEventListener('mouseenter', (e) => {
                const previewTarget = e.currentTarget;
                const previewId = previewTarget.dataset.previewId;
                const sno = previewTarget.dataset.sno;
                
                cancelHideTimeout(previewId);
                
                const previewLayer = createPreviewLayer(previewId);
                const mouseY = getMouseY(e);
                
                // 초기 마우스 위치 저장 (이 위치를 기준으로 레이어 고정)
                initialMousePositions[previewId] = mouseY;
                
                updatePreviewPosition(previewTarget, previewLayer, mouseY);
                previewLayer.style.display = 'block';
                previewTarget.classList.add('bg-color');
                
                loadPreviewData(sno, previewLayer, previewTarget, previewId);
            });
            
            // mousemove 이벤트 제거 - 레이어 위치를 고정하기 위해
            // (data-preview-id 영역 안에서는 레이어 위치가 변경되지 않음)
            
            previewElement.addEventListener('mouseleave', (e) => {
                const previewTarget = e.currentTarget;
                const previewId = previewTarget.dataset.previewId;
                const previewLayer = previewContainer.querySelector('#' + previewId);
                
                // 초기 마우스 위치 정보 삭제
                delete initialMousePositions[previewId];
                
                if (previewLayer && previewLayer.style.display !== 'none') {
                    hideTimeouts[previewId] = setTimeout(() => {
                        hidePreviewLayer(previewId, previewTarget);
                    }, HIDE_DELAY);
                } else {
                    hidePreviewLayer(previewId, previewTarget);
                }
            });
        });

        //검색어 변경 될 때 placeHolder 교체 및 검색 종류 변환 및 검색 종류 변환
        const searchKeyword = $('#frmSearch input[name="searchWord"]');
        const searchKind = $('#frmSearch #searchKind');
        const arrSearchKey = ['writerNick', 'writerNm', 'writerId'];
        const strSearchKey = $('select[name="searchField"]').val();

        // setKeywordPlaceholder 래퍼 함수 - CSS로 처리하기 위해 부모 요소에 클래스 추가
        function setKeywordPlaceholderWithParent(searchKeyword, searchKind, strSearchKey, arrSearchKey) {
            setKeywordPlaceholder(searchKeyword, searchKind, strSearchKey, arrSearchKey);
            // strSearchKey가 writerNm, writerNick, writerId 중 하나인지 확인
            const shouldShow = arrSearchKey.includes(strSearchKey);
            
            // 부모 요소에 클래스 추가/제거 (CSS가 보이기/숨기기 처리)
            let $parentContainer = searchKind.closest('.ncua-flex-gap');
            if ($parentContainer.length === 0) {
                $parentContainer = $('#frmSearch .ncua-flex-gap').first();
            }
            if (shouldShow) {
                $parentContainer.addClass('has-writer-search');
            } else {
                $parentContainer.removeClass('has-writer-search');
            }
        }

        setKeywordPlaceholderWithParent(searchKeyword, searchKind, strSearchKey, arrSearchKey);

        searchKind.change(function (e) {
            setKeywordPlaceholderWithParent(searchKeyword, searchKind, $('select[name="searchField"]').val(), arrSearchKey);
        });

        $('select[name="searchField"]').change(function (e) {
            setKeywordPlaceholderWithParent(searchKeyword, searchKind, $(this).val(), arrSearchKey);
        });

        // 초기화 버튼 클릭 시 폼 초기화
        document.querySelector('button[type="reset"]')?.addEventListener('click', (e) => {
            e.preventDefault();
            
            // 등록일로 변경 (기본값)
            const searchDateFlSelect = document.querySelector('select[name="searchDateFl"]');
            if (searchDateFlSelect) {
                searchDateFlSelect.value = 'regDt';
                searchDateFlSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
            
            // DatePicker 초기화 (7일 전부터 오늘까지)
            const today = new Date();
            const sevenDaysAgo = new Date(today);
            sevenDaysAgo.setDate(today.getDate() - 6); // 7일 전
            
            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
            
            const startDate = formatDate(sevenDaysAgo);
            const endDate = formatDate(today);
            
            if (typeof datePicker !== 'undefined' && datePicker?.setDate) {
                datePicker.setDate([startDate, endDate]);
                
                // "7일" 버튼 클릭 (DatePicker 컨테이너 내부의 버튼 찾기)
                setTimeout(() => {
                    const datePickerContainer = document.querySelector('#datepicker-container');
                    const buttons = datePickerContainer?.querySelectorAll('button') || [];
                    buttons.forEach(button => {
                        if (button.textContent.trim() === '7일') {
                            button.click();
                        }
                    });
                }, 100);
            }
            
            // 검색어 필드를 상품명으로 변경
            const searchFieldSelect = document.querySelector('select[name="searchField"]');
            if (searchFieldSelect) {
                searchFieldSelect.value = 'goodsNm';
                searchFieldSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
            
            // 검색어 입력 필드 비우기
            const searchWordInput = document.querySelector('input[name="searchWord"]');
            if (searchWordInput) {
                searchWordInput.value = '';
            }
            
            // 라디오 버튼 초기화 헬퍼 함수
            const resetRadio = (name, value = '') => {
                const radio = document.querySelector(`input[name="${name}"][value="${value}"]`);
                if (radio) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change', { bubbles: true }));
                }
            };
            
            // 속성: 전체
            resetRadio('reviewType', '');
            
            // 댓글여부: 전체
            resetRadio('isMemo', '');
            
            // 승인여부: 전체
            resetRadio('applyFl', '');
            
            // 마일리지 지급: 전체
            resetRadio('mileage', '');
            
            // 검색 종류 placeholder 재설정
            if (typeof setKeywordPlaceholderWithParent === 'function') {
                setKeywordPlaceholderWithParent(searchKeyword, searchKind, 'goodsNm', arrSearchKey);
            }

            $('#frmSearch').submit();
        });
    });

</script>
