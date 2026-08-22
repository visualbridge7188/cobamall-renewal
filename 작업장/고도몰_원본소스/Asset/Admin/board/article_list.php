<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/article-list.css')?>" rel="stylesheet"/>
<script type="text/javascript" src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/utils/checkbox-all.js')?>"></script>

<article class="ncua-content article-list">
    <header class="ncua-page-header page-header js-affix">
        <h3 class="<?php if (!gd_is_provider()) { ?>ncua-help-manual<?php } ?>">
            <?= end($naviMenu->location); ?>
        </h3>
        <?php ?>
        <?php if ($board->canWrite() == 'y') { ?>
            <div class="ncua-page-header__actions">
                <button type="button" id="btnWrite" class="ncua-btn ncua-btn--md ncua-btn--primary" onclick="btnWrite('<?= $req['bdId'] ?>');" >게시글 등록</button>
            </div>
        <?php } ?>
    </header>
    <section class="ncua-card">
        <header class="ncua-card__header">
            <h4 class="ncua-card__title">게시글 관리</h4>
        </header>
        <section class="ncua-card__body">
            <?php include $articleListSearch ?>
            <?php include $articleListResult ?>
        </section>
    </section>
</article>

<script type="text/javascript">
    const setSearchForm = () => {
        const form = document.getElementById('frmSearch');
        if (!form) return;

        const sortValue = document.querySelector('select[name=\'sort\']').value;
        const pageNumValue = document.querySelector('select[name=\'pageNum\']').value;

        const sortInput = document.getElementById('sort');
        const pageNumInput = document.getElementById('pageNum');

        if (sortInput) {
            sortInput.value = sortValue;
        }

        if (pageNumInput) {
            pageNumInput.value = pageNumValue;
        }
    };

    $('select[name=\'sort\']').on('change', setSearchForm);
    $('select[name=\'pageNum\']').on('change', setSearchForm);
</script>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const categoryElement = document.getElementById('category');
        if (categoryElement) {
            categoryElement.classList.add('ncua-select__tag');
            categoryElement.classList.remove('form-control');
        }
    });
</script>
<script type="text/javascript" defer>
    const isShow = '<?= $isShow ?>';
    
    // 날짜 선택기 인스턴스를 전역 변수로 저장 (초기화 버튼에서 접근하기 위해)
    let searchDateDatePicker = null; // 등록일/수정일 검색용
    let eventPeriodDatePicker = null; // 이벤트 기간 검색용
    
    const datepickerContainer = document.querySelector('#datepicker-container')
    if (datepickerContainer) {
        searchDateDatePicker = new ncua.DatePicker(document.querySelector('#datepicker-container'), {
            size: 'xs', 
            buttons: [
                {
                    text: '오늘',
                    period: 0,
                    unit: 'days',
                    isCurrent: true,
                },
                {
                    text: '7일',
                    period: 6,
                    unit: 'days',
                    isCurrent: false,
                },
                {
                    text: '15일',
                    period: 14,
                    unit: 'days',
                    isCurrent: false,
                },
                {
                    text: '1개월',
                    period: 29,
                    unit: 'days',
                    isCurrent: false,
                },
                {
                    text: '3개월',
                    period: 89,
                    unit: 'days',
                    isCurrent: false,
                },
                {
                    text: '1년',
                    period: 364,
                    unit: 'days',
                    isCurrent: false,
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
                        allowInput: true,
                        locale: 'ko',
                        disableMobile: true
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
                        allowInput: true,
                        locale: 'ko',
                        disableMobile: true
                    },
                },
            ],
            });
        searchDateDatePicker.setDate(["<?= $req['rangDate'][0]; ?>", "<?= $req['rangDate'][1]; ?>"]);
    }

    const bdEventFl = '<?= $bdList['cfg']['bdEventFl'] ?>';
    
    if (bdEventFl === 'y') {
        eventPeriodDatePicker = new ncua.DatePicker(document.querySelector('#datepicker-container02'), {
        size: 'xs', 
        buttons: [
            {
                text: '오늘',
                period: 0,
                unit: 'days',
                isCurrent: true,
            },
            {
                text: '7일',
                period: 6,
                unit: 'days',
                isCurrent: false,
            },
            {
                text: '15일',
                period: 14,
                unit: 'days',
                isCurrent: false,
            },
            {
                text: '1개월',
                period: 29,
                unit: 'days',
                isCurrent: false,
            },
            {
                text: '3개월',
                period: 89,
                unit: 'days',
                isCurrent: false,
            },
            {
                text: '1년',
                period: 364,
                unit: 'days',
                isCurrent: false,
            },
        ],
        datePickerOptions: [
            {
            element: 'start-date',
            attrName: 'rangEventDate[]',
            options: {
                mode: 'single',
                static: true,
                dateFormat: 'Y-m-d',
                clickOpens: true,
                allowInvalidPreload: true,
                allowInput: true,
                locale: 'ko',
            },
            },
            {
            element: 'end-date',
            attrName: 'rangEventDate[]',  // input name의 값을 넣어 주세요
            options: {
                mode: 'single',
                static: true,
                dateFormat: 'Y-m-d',
                clickOpens: true,
                allowInvalidPreload: true,
                allowInput: true,
                locale: 'ko',
            },
            },
        ],
        });
        eventPeriodDatePicker.setDate(["<?= $req['rangEventDate'][0]; ?>", "<?= $req['rangEventDate'][1]; ?>"]);
    }
</script>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const resetBtn = document.querySelector('.js-btn-reset');
        const searchForm = document.getElementById('frmSearch');
        
        const resetSelect = (selectElement, value) => {
            if (selectElement) {
                selectElement.value = value;
                selectElement.dispatchEvent(new Event('change', { bubbles: true }));
            }
        };
        
        if (resetBtn && searchForm) {
            resetBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // 페이지 번호 초기화
                const pageNumInput = document.getElementById('pageNum');
                if (pageNumInput) {
                    pageNumInput.value = '';
                }
                
                // 게시판 선택 초기화 (첫 번째 옵션으로)
                const bdIdSelect = document.getElementById('bdId');
                if (bdIdSelect && bdIdSelect.options.length > 0) {
                    bdIdSelect.selectedIndex = 0;
                }
                
                // 말머리 초기화
                resetSelect(document.getElementById('category'), '');
                
                // 날짜 기준 라디오 버튼 초기화 (등록일 기준으로)
                const regDtRadio = document.querySelector('input[name="searchDateFl"][value="regDt"]');
                if (regDtRadio) {
                    regDtRadio.checked = true;
                }
                
                // 날짜 선택기 초기화
                if (searchDateDatePicker && typeof searchDateDatePicker.setDate === 'function') {
                    searchDateDatePicker.setDate(['', '']);
                }
                
                // 이벤트 기간 날짜 선택기 초기화
                if (eventPeriodDatePicker && typeof eventPeriodDatePicker.setDate === 'function') {
                    eventPeriodDatePicker.setDate(['', '']);
                }
                
                // 답변상태 초기화 (=전체=)
                resetSelect(document.getElementById('replyStatus'), '');
                
                // 평점 초기화 (=전체=)
                resetSelect(document.querySelector('select[name="goodsPt"]'), '');
                
                // 검색 필드 초기화 (제목으로)
                resetSelect(document.getElementById('searchField'), 'subject');
                
                // 검색 종류 초기화
                resetSelect(document.getElementById('searchKind'), '');
                
                // 검색어 초기화
                const searchWordInput = document.querySelector('input[name="searchWord"]');
                if (searchWordInput) {
                    searchWordInput.value = '';
                }
                
                // 폼 제출하여 검색 실행
                searchForm.submit();
            });
        }
    });
</script>
<script type="text/javascript">
    // 본사 - 게시글 관리
    const articleListCode = '251111001';
    // 공급사 - 상품문의 코드
    const goodsQuestionCode = '251111002';
    // 공급사 - 상품후기 코드
    const goodsReviewCode = '251111003';

    let code = '';
    <?php if (gd_is_provider()) { ?> 
        <?php if (end(array: $naviMenu->location) === '상품후기 관리' || end($naviMenu->lno) === 'godo00434') { ?>
            code = goodsReviewCode;
        <?php } else { ?>
            code = goodsQuestionCode;
        <?php } ?>
    <?php } else { ?>
        code = articleListCode;
    <?php } ?>

    if (code !== '' && window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
        });
    }
</script>
