<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/crm-common.css') ?>" rel="stylesheet"/>
<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/message-template-list.css') ?>" rel="stylesheet"/>
<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-preview.css') ?>" rel="stylesheet"/>
<script type="text/javascript" src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/utils/checkbox-all.js') ?>"></script>
<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/mobile-history-list.css') ?>" rel="stylesheet"/>
<script type="text/javascript" src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-datepicker-factory/ncds-datepicker-factory.js') ?>"></script>
<script type="text/javascript" src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-checkbox-group/ncds-checkbox-group.js') ?>"></script>

<article class="ncua-content message-template-list">
    <header class="ncua-page-header page-header js-affix affix-top" style="width: 1017px;">
        <h3 class="ncua-help-manual"><?php echo end($naviMenu->location); ?></h3>
        <div class="ncua-page-header__actions">
            <button type="submit" class="ncua-btn ncua-btn--md ncua-btn--primary" onclick="update_template({ mode: 'register'})">템플릿 등록</button>
        </div>
    </header>

    <div class="swiper ncua-horizontal-tab ncua-horizontal-tab--md ncua-horizontal-tab--underline-fill">
        <div class="swiper-wrapper ncua-gap-8">
            <div class="swiper-slide ncua-horizontal-tab__item">
                <a href="javascript:" onclick="redirectWithParams('sendMethod','sms',false)"
                   class="ncua-tab-button <?= ($sendMethod === 'sms') ? ' is-active' : '' ?>" data-html="true"
                   data-content="SMS" data-placement="top">SMS</a>
            </div>
            <div class="swiper-slide ncua-horizontal-tab__item">
                <a href="javascript:" onclick="redirectWithParams('sendMethod','kakao',false)"
                   class="ncua-tab-button <?= ($sendMethod === 'kakao') ? ' is-active' : '' ?>" data-html="true"
                   data-content="카카오 알림톡" data-placement="top">카카오 알림톡</a>
            </div>
            <div class="swiper-slide ncua-horizontal-tab__item">
                <a href="javascript:" onclick="redirectWithParams('sendMethod','myapp',false)"
                   class="ncua-tab-button <?= ($sendMethod === 'myapp') ? ' is-active' : '' ?>" data-html="true"
                   data-content="마이앱(앱푸시)" data-placement="top">마이앱(앱푸시)</a>
            </div>
        </div>
    </div>

    <section class="ncua-card">
        <section class="ncua-card__body">
            <!-- 검색 폼 -->
            <?php include $messageTemplateSearch; ?>

            <!-- 검색 결과 -->
            <?php include $messageTemplateResult; ?>
        </section>
    </section>
</article>

<script type="text/javascript">
    const start = new Date('1970-01-01');
    const end = new Date(); // 오늘 날짜
    const diffTime = Math.abs(end - start);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    const datePicker = createCRMDatePicker({
        containerId: 'datepicker-container',
        startAttrName: 'dateFrom',
        endAttrName: 'dateTo',
        buttons: [
           {text: '오늘', period: 0, unit: 'days', isCurrent: false},
           {text: '7일', period: 6, unit: 'days', isCurrent: true},
           {text: '15일', period: 14, unit: 'days', isCurrent: false},
           {text: '1개월', period: 29, unit: 'days', isCurrent: false},
           {text: '3개월', period: 89, unit: 'days', isCurrent: false},
           {text: '전체', period: diffDays - 1, unit: 'days', isCurrent: false},
       ]
    });

    <?php if ($search->getDateFrom() && $search->getDateTo()): ?>
        datePicker.setDate(["<?= $search->getDateFrom() ?>", "<?= $search->getDateTo() ?>"]);
    <?php endif; ?>

    <?php if ($search->getDateFrom() === '1970-01-01'): ?>
        document.querySelectorAll('#datepicker-container input').forEach(input => {
            input.readOnly = true;
            input.style.backgroundColor = '#f5f5f5';
            input.style.cursor = 'not-allowed';
            input.parentElement.style.pointerEvents = 'none';
        });
    <?php endif; ?>

    attachDateValidation(datePicker, {
        containerId: 'datepicker-container',
        maxMonthRange: 9999,
        messages: {
            startAfterEnd: '시작일자는 종료일자보다 이후일 수 없습니다.',
            endBeforeStart: '종료일자는 시작일자보다 이전일 수 없습니다.',
        }
    });

    document.getElementById('datepicker-container').addEventListener('click', function(e) {
        const button = e.target.closest('button');
        if (!button) return;

        const dateInputs = this.querySelectorAll('input');
        const isAllButton = button.textContent.trim() === '전체';

        dateInputs.forEach(input => {
            input.readOnly = isAllButton;
            input.style.backgroundColor = isAllButton ? '#f5f5f5' : '';
            input.style.cursor = isAllButton ? 'not-allowed' : '';
            input.parentElement.style.pointerEvents = isAllButton ? 'none' : '';
        });
    });

    // 검색 폼 유효성 검사
    const searchValidators = [
        (form) => {
            const hasCheckbox = (name) => !!form.querySelector(`input[name="${name}"]`);
            const hasChecked = (name) => !!form.querySelector(`input[name="${name}"]:checked`);
            if (['category[]', 'status[]'].some(name => hasCheckbox(name) && !hasChecked(name))) {
                return '검색 조건을 선택해 주세요.';
            }
        },
    ];

    document.getElementById('frmSearch').addEventListener('submit', (e) => {
        const message = searchValidators.reduce((msg, validate) => msg || validate(e.currentTarget), '');
        if (!message) return;

        e.preventDefault();
        NCDSAlert({ message, iconType: 'error' });
    });

    // 템플릿 코드별 autoFl 매핑
    const templateAutoMap = {
        <?php foreach ($results as $item): ?>
        '<?= $item->getTemplateCode() ?>': '<?= $item->getAutoFl() ?>',
        <?php endforeach; ?>
    };

    const templateBasicMap = {
        <?php foreach ($results as $item): ?>
        '<?= $item->getTemplateCode() ?>': '<?= $item->getBasicFl() ?>',
        <?php endforeach; ?>
    };

    const searchData = JSON.parse('<?= json_encode($search->toArray(), JSON_UNESCAPED_UNICODE) ?>');

    // 템플릿 등록 페이지로 이동
    function update_template(data = null) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = './message_template_register.php';

        if (data) {
            Object.entries(data).forEach(([key, value]) => {
                form.appendChild(createHiddenInput(key, value));
            });
        }
        form.appendChild(createHiddenInput('search', JSON.stringify(searchData)));
        document.body.appendChild(form);
        form.submit();
    }

    // 카카오 알림톡 템플릿 검수 답변 레이어
    function check_kakao_template_comment(provider, templateCode) {
        $.post('./layer_kakao_template_comment.php', {provider: provider, templateCode: templateCode}, function (data) {
            ncds_layer_popup({
                message: data,
                title: '카카오 알림톡 템플릿 검수 답변<div class="modal-title-description">템플릿 검수에 대한 문의사항은 <a href="https://cs.kakao.com/requests?category=481&locale=ko&node=46235&service=159" target="_blank" class="ncua-link">카카오톡 고객센터</a>로 문의하시기 바랍니다.</div>'
            });
        });
    }

    // 발송 내용 레이어
    function check_template_contents(templateCode, sendMethod, provider) {
        $.post('./layer_template_contents.php', {
            templateCode: templateCode,
            sendMethod: sendMethod,
            provider: provider
        }, function (data) {
            layer_popup(data, '템플릿 미리보기');
        });
    }

    // hidden input 생성
    function createHiddenInput(name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        return input;
    }


    function redirectWithParams(key, value, isAll = true) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';

        if (isAll) {
            Object.entries(searchData).forEach(([k, v]) => {
                if (Array.isArray(v)) {
                    v.forEach(item => form.appendChild(createHiddenInput(`${k}[]`, item)));
                } else {
                    form.appendChild(createHiddenInput(k, v));
                }
            });
        }

        form.appendChild(createHiddenInput('page', 1));
        form.appendChild(createHiddenInput(key, value));
        document.body.appendChild(form);
        form.submit();
    }

    // 검색 초기화
    function resetSearch() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';
        form.appendChild(createHiddenInput('sendMethod', '<?= $sendMethod ?>'));
        document.body.appendChild(form);
        form.submit();
    }

    ['.js-trigger-type', '.js-trigger-status'].forEach(selector => new CheckboxGroup(selector));

    // 페이지 크기 변경 (현재 첫 번째 항목 기준으로 페이지 재계산)
    function changePageSize(newPageSize) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';

        Object.entries(searchData).forEach(([k, v]) => {
            if (k === 'page' || k === 'pageSize') return;
            if (Array.isArray(v)) {
                v.forEach(item => form.appendChild(createHiddenInput(`${k}[]`, item)));
            } else {
                form.appendChild(createHiddenInput(k, v));
            }
        });

        form.appendChild(createHiddenInput('page', 1));
        form.appendChild(createHiddenInput('pageSize', newPageSize));
        document.body.appendChild(form);
        form.submit();
    }

    // 페이지 이동
    function loadByPage(page) {
        const [key, value] = page.split('=');
        redirectWithParams(key, value, true);
    }

    $(document).ready(function () {
        history.replaceState(null, '', window.location.pathname);

        const container = document.querySelector('#frmList') || document.body;

        // 버튼 클릭 이벤트 (복제, 수정)
        container.addEventListener('click', (e) => {
            const target = e.target;
            if (!target) return;

            // 복제 버튼
            if (target.classList.contains('js-btn-copy')) {
                const templateCode = target.value;
                const sendMethod = '<?= $sendMethod ?>';

                if (sendMethod === 'sms' || sendMethod === 'myapp') {
                    // SMS, 마이앱은 바로 복제 처리
                    $.post('./message_template_ps.php', {
                        mode: 'copy',
                        sendMethod: sendMethod,
                        templateCode: templateCode
                    }, function(response) {
                        if (response.success) {
                            NCDSToast({message: '템플릿이 복제되었습니다.', color: 'success'});

                            setTimeout(() => {
                                redirectWithParams('sendMethod', '<?= $sendMethod ?>', true);
                            }, 3000);
                        } else {
                            NCDSAlert({
                                message: response.message || '복제에 실패했습니다.',
                                iconType: 'error'
                            });
                        }
                    }, 'json');
                } else {
                    // 카카오는 등록 페이지로 이동
                    const data = {
                        mode: 'copy',
                        sendMethod: sendMethod,
                        templateCode: templateCode,
                        provider: '<?= $search->getProvider() ?>'
                    }
                    update_template(data);
                }
            }

            // 수정 버튼 (기본 템플릿 수정 불가 안내)
            if (target.classList.contains('js-btn-modify')) {
                const templateCode = target.value;
                const hasBasic = templateBasicMap[templateCode] === 'y';

                if (hasBasic) {
                    NCDSAlert({
                        message: '고도몰에서 제공하는 기본 템플릿은 수정할 수 없습니다.<br>복제 후 내용을 수정해 신규 템플릿으로 등록해 주세요.',
                        iconType: 'error'
                    });
                    return false;
                }

                const data = {
                    mode: 'modify',
                    sendMethod: '<?= $sendMethod ?>',
                    templateCode: templateCode,
                    provider: '<?= $search->getProvider() ?>'
                }
                update_template(data)
            }
        });

        // 선택삭제 유효성 검사
        $('#frmList').validate({
            ignore: ':hidden', dialog: false, submitHandler: function (form) {
                const mode = form.mode.value;
                if (mode === 'delete') {
                    // 선택된 템플릿 코드 가져오기
                    const selectedCodes = Array.from(form.querySelectorAll('input[name="templateCodeList[]"]:checked'))
                        .map(input => input.value);

                    // 자동알림 사용 중인 템플릿 체크
                    const hasAutoAlrim = selectedCodes.some(code => {
                        return templateAutoMap[code] === 'y';
                    });

                    if (hasAutoAlrim) {
                        NCDSAlert({
                            message: '자동알림에 사용 중인 템플릿은 삭제할 수 없습니다.',
                            iconType: 'error'
                        });
                        return false;
                    }

                    const data = {
                        mode: mode,
                        sendMethod: '<?= $sendMethod ?>',
                        templateCodeList: selectedCodes,
                        provider: '<?= $search->getProvider() ?>'
                    }

                    NCDSConfirm({
                        message: '선택한 템플릿을 삭제하시겠습니까?',
                        subMessage: '삭제한 템플릿은 복구할 수 없습니다',
                        callback: function (result) {
                            if (!result) return false;

                            $.post('./message_template_ps.php', data, function (response) {
                                if (response.success) {
                                    NCDSToast({message: '템플릿이 삭제되었습니다.', color: 'success'});

                                    setTimeout(() => {
                                        redirectWithParams('page', 1, true);
                                    }, 3000);
                                } else {
                                    NCDSAlert({
                                        message: response.message || '삭제에 실패했습니다.',
                                        iconType: 'error'
                                    });
                                }
                            }, 'json');
                        },
                    });
                    return false; // 폼 기본 제출 방지
                }
            },
            invalidHandler: function (event, validator) {
                if (!validator.errorList.length) return;
                NCDSAlert({
                    message: validator.errorList[0].message,
                    iconType: 'error'
                });
            },
            rules: {
                'templateCode[]': {
                    required: true
                }
            },
            messages: {
                'templateCode[]': {
                    required: '선택하신 글이 없습니다.'
                },

            },
        });

        // 선택삭제 핸들러 함수 정의
        const handleDeleteClick = () => {
            const frmList = document.querySelector('#frmList');
            const modeInput = frmList?.querySelector('input[name="mode"]');

            if (!frmList || !modeInput) return;

            modeInput.value = 'delete';
            $('#frmList').submit();
        };

        // 선택삭제 이벤트 바인딩
        const btnDelete = document.querySelector('.js-btn-delete');
        if (btnDelete) {
            btnDelete.addEventListener('click', handleDeleteClick);
        }

        const code = '251219001';
        // COS 가이드 초기화
        if (window.GodoCosGuide) {
            window.GodoCosGuide.init({
                apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
                guideCode: code,
            });
        }
    });

    // 페이지 이동 후 토스트 메시지 표시
    const pendingToast = sessionStorage.getItem('pendingToast');
    if (pendingToast) {
        sessionStorage.removeItem('pendingToast');
        NCDSToast(JSON.parse(pendingToast));
    }
</script>

