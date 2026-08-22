<link rel="stylesheet" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/scm-board-list.css'); ?>">
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/supply-combo-box.js')?>"></script>
<article class="ncua-content scm-board-list">
    <header class="ncua-page-header page-header js-affix">
        <h3 class="ncua-help-manual"><?= end($naviMenu->location); ?></h3>
        <div class="ncua-page-header__actions">
            <button type="button" id="btnWrite" class="ncua-btn ncua-btn--md ncua-btn--primary js-register">게시글 등록</button>
        </div>
    </header>
    <section class="ncua-card">
        <header class="ncua-card__header">
            <h4 class="ncua-card__title">공급사 게시판 검색</h4>
        </header>
        <section class="ncua-card__body">
            <?php include $articleSearchForm ?>
            <?php include $articleResultForm ?>
        </section>
    </section>
    
</article>

<script type="text/javascript">
    $(document).ready(function () {
        // 등록
        $('.js-register').bind('click', function () {
            location.href = './scm_board_register.php';
        });

        //삭제
        $('.js-btn-delete').bind('click', function () {
            if ($('input[name="sno[]"]:checked').length == 0) {
                NCDSAlert({message: '선택한 게시글이 없습니다.', iconType: 'error'});
            }
            else {
                NCDSConfirm({message: '삭제하시겠습니까?<br />이 게시글에 달린 답변글도 삭제됩니다.', callback: function(data){
                    if(data){
                        $('#frmList').submit();
                    }
                }});


            }
        })

        //검색어 변경 될 때 placeHolder 교체 및 검색 종류 변환 및 검색 종류 변환
        var searchKeyword = $('#frmSearch input[name="keyword"]');
        var searchKind = $('#frmSearch #searchKind');
        var arrSearchKey = ['', 'writer'];
        var strSearchKey = $('#frmSearch #searchField').val();

        setKeywordPlaceholder(searchKeyword, searchKind, strSearchKey, arrSearchKey);

        searchKind.change(function (e) {
            setKeywordPlaceholder(searchKeyword, searchKind, $('#frmSearch #searchField').val(), arrSearchKey);
        });

        $('#frmSearch #searchField').change(function (e){
            setKeywordPlaceholder(searchKeyword, searchKind, $(this).val(), arrSearchKey);
        });
    });

    /**
     * 카테고리 연결하기 Ajax layer
     */
    function layer_register(typeStr, mode, isDisabled) {

        var addParam = {
            "mode": mode,
        };

        if (typeStr == 'scm') {
            $('input:radio[name=scmFl]:input[value=y]').prop("checked", true);
        }

        if (!_.isUndefined(isDisabled) && isDisabled == true) {
            addParam.disabled = 'disabled';
        }

        layer_add_info(typeStr,addParam);
    }
</script>
<script type="text/javascript">
    const datePicker = new ncua.DatePicker(document.querySelector('#datepicker-container'), {
        size: 'xs',
        buttons: [
            {
                text: '오늘',
                period: 0,
                unit: 'days',
                isCurrent: false,
            },
            {
                text: '7일',
                period: 6,
                unit: 'days',
                isCurrent: true,
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
                attrName: 'searchDate[]',
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
                attrName: 'searchDate[]',
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
    datePicker.setDate(["<?= $search['searchDate'][0]; ?>", "<?= $search['searchDate'][1]; ?>"]);

    try {
        /**
         * 공급사 선택 ComboBox 초기화
         */
        const initSupplyComboBoxHandler = () => {
            if (typeof window.initSupplyComboBox === 'undefined') {
                setTimeout(initSupplyComboBoxHandler, 100);
                return;
            }

            // 초기 데이터 준비
            const initData = [];
            <?php if (($search['scmFl'] == 'y' || $search['scmFl'] == '1') && !empty($search['scmNo'])) { ?>
                <?php foreach ($search['scmNo'] as $k => $v) { ?>
                    initData.push({
                        id: '<?= $v ?>',
                        label: '<?= addslashes($search['scmNoNm'][$k]) ?>'
                    });
                <?php } ?>
            <?php } ?>

            const applySupplyComboBox = window.initSupplyComboBox({
                scmLayerSelector: 'scmLayer',
                dataInputNm: 'scmNo',
                comboBoxLayerId: 'ncua-combo-box-layer',
                radioGroupClass: 'supply-radio-group',
                initData: initData,
                apiUrl: '../share/ncds/layer_scm.php',

                tagIdPrefix: 'info_scm',
                tagHiddenName: 'scmNoNm'
            });
            
            // 전역 변수로 저장 (리셋 버튼에서 사용)
            window.applySupplyComboBox = applySupplyComboBox;
        }

        initSupplyComboBoxHandler();
    } catch (error) {
        console.error('공급사 ComboBox 초기화 오류:', error);
    }

    const btnReset = document.querySelector('.js-btn-reset');
    const searchForm = document.getElementById('frmSearch');
    
    const resetSelect = (selectElement, value) => {
        if (selectElement) {
            selectElement.value = value;
            selectElement.dispatchEvent(new Event('change', { bubbles: true }));
        }
    };
    
    if (btnReset && searchForm) {
        btnReset.addEventListener('click', function(e) {
            e.preventDefault();
            
            // 페이지 번호를 1로 초기화
            const pageNumInput = document.getElementById('pageNum');
            if (pageNumInput) {
                pageNumInput.value = '1';
            }
            
            // 공급사 구분 라디오 버튼 초기화 (전체로)
            const scmFlAllRadio = document.querySelector('input[name="scmFl"][value="all"]');
            if (scmFlAllRadio) {
                scmFlAllRadio.checked = true;
                scmFlAllRadio.dispatchEvent(new Event('change', { bubbles: true }));
            }
            
            // 공급사 선택 레이어 초기화
            const scmLayer = document.getElementById('scmLayer');
            if (scmLayer) {
                const scmTags = scmLayer.querySelectorAll('.ncua-tag');
                scmTags.forEach(tag => tag.remove());
            }
            
            // 공급사 입력 필드 제거
            const scmNoInputs = searchForm.querySelectorAll('input[name="scmNo[]"], input[name="scmNoNm[]"]');
            scmNoInputs.forEach(input => input.remove());
            
            // ComboBox 초기화
            if (window.applySupplyComboBox?.comboBox) {
                window.applySupplyComboBox.comboBox.setDisabled(true);
                window.applySupplyComboBox.comboBox.clear();
            }
            
            // scmLayer 숨김 처리
            if (scmLayer) {
                scmLayer.style.display = 'none';
            }
            
            // 분류 초기화
            resetSelect(document.getElementById('category'), '');
            
            // 검색 필드 초기화 (통합검색으로)
            resetSelect(document.getElementById('searchField'), '');
            
            // 검색 종류 초기화
            resetSelect(document.getElementById('searchKind'), '');
            
            // 검색어 초기화
            const keywordInput = document.querySelector('input[name="keyword"]');
            if (keywordInput) {
                keywordInput.value = '';
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
            
            // 폼 제출하여 검색 실행
            searchForm.submit();
        });
    }
</script>
