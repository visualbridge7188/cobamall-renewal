/**
 * 배포하기 — 통합 JS (단일 페이지 phase 전환)
 * Story 2A.2: showPhase, showIntro, startProcess, showPrevStepModal, onStepperClick, startRedeploy
 * 기존 deploy_consent.js, deploy_verify.js, deploy_skin.js, deploy_execute.js 로직 통합
 */
(function () {
    'use strict';

    /* ================================================================
     * Phase 전환 코어 (AC1, AC2, AC3)
     * ================================================================ */

    var phaseStepMap = {
        phase1:'1', phase2a:'2', phase2b:'3',
        phase3a:'4', phase3b:'5',
        phase4a:'R', phase4b:'R'
    };

    var phaseActionsIds = [
        'pageActionsPhase1', 'pageActionsPhase2a', 'pageActionsPhase2b',
        'pageActionsPhase3a', 'pageActionsPhase3b', 'pageActionsPhase4a', 'pageActionsPhase4b'
    ];

    var phaseActionsMap = {
        phase1: 'pageActionsPhase1', phase2a: 'pageActionsPhase2a',
        phase2b: 'pageActionsPhase2b', phase3a: 'pageActionsPhase3a',
        phase3b: 'pageActionsPhase3b', phase4a: 'pageActionsPhase4a',
        phase4b: 'pageActionsPhase4b'
    };

    var stepToFirstPhase = {
        '1':'phase1', '2':'phase2a', '3':'phase2b',
        '4':'phase3a', '5':'phase3b', 'R':'phase4a'
    };

    var currentPhase = null;
    var visitedSteps = new Set();
    var prevStepTarget = null;
    var introPageEnabled = false;
    var notificationTimers = {};

    /**
     * showPhase(phaseId) — AC2
     */
    function showPhase(phaseId) {
        if (!phaseStepMap[phaseId]) return;

        currentPhase = phaseId;
        var stepNum = phaseStepMap[phaseId];

        // phase-content .active 토글
        var phases = document.querySelectorAll('.ds-deploy__phase-content');
        phases.forEach(function (el) { el.classList.remove('active'); });
        var target = document.getElementById(phaseId);
        if (target) target.classList.add('active');

        // visitedSteps 기록
        visitedSteps.add(stepNum);

        // 사이드바 제목 갱신
        var titleEl = document.getElementById('sidebarTitle');
        if (titleEl) {
            if (stepNum === 'R' && phaseId === 'phase4b') {
                titleEl.textContent = '롤백 완료';
            } else if (stepNum === 'R') {
                titleEl.textContent = '롤백 진행';
            } else {
                titleEl.textContent = '배포 진행 현황';
            }
        }

        // PageTitle 단계별 버튼 전환
        var introEl = document.getElementById('pageActionsIntro');
        if (introEl) introEl.classList.add('ds-deploy--hidden');
        phaseActionsIds.forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.classList.add('ds-deploy--hidden');
        });
        var actId = phaseActionsMap[phaseId];
        if (actId) {
            var actEl = document.getElementById(actId);
            if (actEl) actEl.classList.remove('ds-deploy--hidden');
        }

        // 롤백 스텝 표시/숨김
        var stepperR = document.getElementById('stepper-R');
        if (stepperR) {
            stepperR.style.display = (stepNum === 'R') ? '' : 'none';
        }

        // 롤백 진행 시 1~5단계 완료 처리 — visitedSteps 여부에 무관하게 완료(✓) 표시 (AC1, AC3)
        if (stepNum === 'R') {
            ['1', '2', '3', '4', '5'].forEach(function(s) { visitedSteps.add(s); });
        }

        // Stepper 상태 갱신
        updateStepper(stepNum);

        // phase3a 진입 시 자동 배포 실행
        if (phaseId === 'phase3a') {
            startDeployExecution();
        }

        // phase2a 진입 시 자동 검증 실행
        if (phaseId === 'phase2a') {
            initVerifyPhase();
        }

        // phase4a 진입 시 자동 롤백 실행
        if (phaseId === 'phase4a') {
            startRollback();
        }
    }

    function updateStepper(activeStepNum) {
        var stepNums = ['1', '2', '3', '4', '5', 'R'];
        stepNums.forEach(function (num) {
            var item = document.getElementById('stepper-' + num);
            if (!item) return;

            item.classList.remove(
                'ds-deploy__stepper-item--active',
                'ds-deploy__stepper-item--completed',
                'ds-deploy__stepper-item--clickable',
                'ds-deploy__stepper-item--rollback-active'
            );
            item.style.cursor = '';

            if (num === activeStepNum) {
                if (num === 'R') {
                    item.classList.add('ds-deploy__stepper-item--rollback-active');
                } else {
                    item.classList.add('ds-deploy__stepper-item--active');
                }
            } else if (visitedSteps.has(num) && isStepBefore(num, activeStepNum)) {
                item.classList.add('ds-deploy__stepper-item--completed');
                item.classList.add('ds-deploy__stepper-item--clickable');
                item.style.cursor = 'pointer';
            }
        });
    }

    function isStepBefore(stepA, stepB) {
        var order = ['1', '2', '3', '4', '5', 'R'];
        return order.indexOf(stepA) < order.indexOf(stepB);
    }

    /**
     * showIntro() — AC3
     * deploy.php 초기 상태로 이동 (파라미터 제거)
     */
    function showIntro() {
        window.location = '/development/deploy.php';
    }

    /**
     * startProcess() — AC3
     */
    function startProcess() {
        var intro = document.getElementById('step-intro');
        var process = document.getElementById('deploy-process');

        if (intro) intro.style.display = 'none';
        if (process) process.classList.remove('ds-deploy--hidden');

        window.scrollTo(0, 0);
        showPhase('phase1');
    }

    /**
     * showPrevStepModal(targetPhase) — AC4
     */
    function showPrevStepModal(targetPhase) {
        prevStepTarget = targetPhase;
        var modal = document.getElementById('prev-step-modal');
        if (modal) modal.style.display = 'flex';
    }

    function closePrevStepModal() {
        var modal = document.getElementById('prev-step-modal');
        if (modal) modal.style.display = 'none';
        prevStepTarget = null;
    }

    function confirmPrevStep() {
        var target = prevStepTarget;
        closePrevStepModal();
        if (target) {
            showPhase(target);
        }
    }

    /**
     * onStepperClick(stepNum) — AC9
     */
    function onStepperClick(stepNum) {
        var item = document.getElementById('stepper-' + stepNum);
        if (!item) return;

        // 롤백 진행/완료 중 1~5단계 클릭 차단 — 롤백 플로우 이탈 방지 (M1 fix)
        if ((currentPhase === 'phase4a' || currentPhase === 'phase4b') && stepNum !== 'R') {
            return;
        }

        var isCompleted = item.classList.contains('ds-deploy__stepper-item--completed');
        var isActive = item.classList.contains('ds-deploy__stepper-item--active') ||
                       item.classList.contains('ds-deploy__stepper-item--rollback-active');

        if (!isCompleted && !isActive) return;

        var firstPhase = stepToFirstPhase[stepNum];
        if (firstPhase) showPhase(firstPhase);
    }

    /**
     * startRedeploy() — AC8
     */
    function startRedeploy() {
        verifyInitialized = false;
        showPhase('phase2a');

        // phase2a 내 .case-content 모두 비활성화 → 'p2a-progress'만 활성화
        var caseContents = document.querySelectorAll('#phase2a .case-content');
        caseContents.forEach(function (el) { el.classList.remove('active'); });
        var progress = document.getElementById('p2a-progress');
        if (progress) progress.classList.add('active');

        // case-bar 버튼 동기화
        var caseBtns = document.querySelectorAll('#phase2a .case-btn');
        caseBtns.forEach(function (btn) { btn.classList.remove('active'); });
        var progressBtn = document.querySelector('#phase2a .case-btn[data-case="p2a-progress"]');
        if (progressBtn) progressBtn.classList.add('active');
    }

    /* ================================================================
     * Phase 1: 사용 동의 + 이메일 인증 (from deploy_consent.js)
     * ================================================================ */

    var TIMER_SECONDS = 600;
    var timerInterval = null;
    var remainingSeconds = 0;

    function initConsentPhase() {
        var btnSendOtp = document.getElementById('btnSendOtp');
        var btnResendOtp = document.getElementById('btnResendOtp');
        var btnVerifyOtp = document.getElementById('btnVerifyOtp');
        var btnNextStep = document.getElementById('btnNextStep');
        var otpCodeInput = document.getElementById('otpCode');
        var otpTimer = document.getElementById('otpTimer');
        var otpMessage = document.getElementById('otpMessage');
        var authButtons = document.getElementById('authButtons');
        var authButtonsAfter = document.getElementById('authButtonsAfter');
        var otpField = document.getElementById('otpField');

        function startTimer() {
            remainingSeconds = TIMER_SECONDS;
            clearInterval(timerInterval);
            updateTimerDisplay();
            timerInterval = setInterval(function () {
                remainingSeconds--;
                updateTimerDisplay();
                if (remainingSeconds <= 0) {
                    clearInterval(timerInterval);
                    showOtpMessage('인증번호가 만료되었습니다. 재발송해 주세요.', 'error');
                }
            }, 1000);
        }

        function updateTimerDisplay() {
            var mm = String(Math.floor(remainingSeconds / 60)).padStart(2, '0');
            var ss = String(remainingSeconds % 60).padStart(2, '0');
            if (otpTimer) otpTimer.textContent = mm + ':' + ss;
        }

        function showOtpMessage(text, type) {
            if (!otpMessage) return;
            otpMessage.textContent = text;
            otpMessage.className = 'ds-deploy__auth-message';
            if (type === 'success') otpMessage.className += ' ds-deploy__auth-message--success';
            else if (type === 'error') otpMessage.className += ' ds-deploy__auth-message--error';
            var inputWrap = otpCodeInput ? otpCodeInput.closest('.ncua-input') : null;
            if (inputWrap) {
                if (type === 'error') inputWrap.classList.add('ds-deploy__auth-input--error');
                else inputWrap.classList.remove('ds-deploy__auth-input--error');
            }
        }

        function switchToAfterSendState() {
            if (authButtons) authButtons.classList.add('ds-deploy--hidden');
            if (otpField) otpField.classList.remove('ds-deploy--hidden');
        }

        var emailVerified = false;

        function checkConsentReady() {
            var checkboxes = document.querySelectorAll('.js-consent-check input[type="checkbox"]');
            var allChecked = true;
            checkboxes.forEach(function (cb) { if (!cb.checked) allChecked = false; });
            if (allChecked && emailVerified) enableConsentNext();
            else disableConsentNext();
        }

        function enableConsentNext() {
            if (btnNextStep) {
                btnNextStep.disabled = false;
                btnNextStep.classList.remove('is-disable');
            }
        }

        function disableConsentNext() {
            if (btnNextStep) {
                btnNextStep.disabled = true;
                btnNextStep.classList.add('is-disable');
            }
        }

        // 동의 체크박스 변경 시 활성화 조건 재검사
        document.querySelectorAll('.js-consent-check input[type="checkbox"]').forEach(function (cb) {
            cb.addEventListener('change', checkConsentReady);
        });

        function disableBtn(btn) {
            if (btn) { btn.disabled = true; btn.classList.add('is-disable'); }
        }

        if (btnSendOtp) {
            btnSendOtp.addEventListener('click', function () {
                disableBtn(btnSendOtp);
                ajaxPost('./deploy_consent_ps.php', buildParams({ mode: 'sendOtp', siteKey: siteKey }), function () {
                    switchToAfterSendState();
                    startTimer();
                    showOtpMessage('인증번호가 발송되었습니다.', 'success');
                }, function (error) {
                    showOtpMessage(error, 'error');
                    btnSendOtp.disabled = false;
                    btnSendOtp.classList.remove('is-disable');
                });
            });
        }

        if (btnResendOtp) {
            btnResendOtp.addEventListener('click', function () {
                disableBtn(btnResendOtp);
                ajaxPost('./deploy_consent_ps.php', buildParams({ mode: 'sendOtp', siteKey: siteKey }), function () {
                    startTimer();
                    if (otpCodeInput) otpCodeInput.value = '';
                    showOtpMessage('인증번호가 재발송되었습니다.', 'success');
                    btnResendOtp.disabled = false;
                    btnResendOtp.classList.remove('is-disable');
                }, function (error) {
                    showOtpMessage(error, 'error');
                    disableBtn(btnResendOtp);
                });
            });
        }

        if (btnVerifyOtp) {
            btnVerifyOtp.addEventListener('click', function () {
                var code = otpCodeInput ? otpCodeInput.value.trim() : '';
                if (!code || code.length !== 6) {
                    showOtpMessage('인증번호 6자리를 입력해 주세요.', 'error');
                    return;
                }
                disableBtn(btnVerifyOtp);
                ajaxPost('./deploy_consent_ps.php', buildParams({ mode: 'verifyOtp', code: code, siteKey: siteKey }), function () {
                    clearInterval(timerInterval);
                    showOtpMessage('인증번호가 일치합니다.', 'success');
                    emailVerified = true;
                    checkConsentReady();
                    disableBtn(btnResendOtp);
                }, function (error) {
                    showOtpMessage(error, 'error');
                    btnVerifyOtp.disabled = false;
                    btnVerifyOtp.classList.remove('is-disable');
                });
            });
        }

        if (btnNextStep) {
            btnNextStep.addEventListener('click', function () {
                if (btnNextStep.disabled) return;
                showPhase('phase2a');
            });
        }

    }

    /* ================================================================
     * Phase 2a: 사전 검증 (from deploy_verify.js)
     * ================================================================ */

    var verifyState = { sourceVerify: null, schemaCompare: null, schemaQueries: [], dmlQueries: null };
    var verifyInitialized = false;

    function initVerifyPhase() {
        if (verifyInitialized) return;
        verifyInitialized = true;

        startSourceVerify();
        startSchemaCompare();
    }

    function startSourceVerify() {
        var el = getVerifySourceEls();
        showEl(el.loading); hideEl(el.pass); hideEl(el.fail);

        ajaxPost('./deploy_verify_ps.php', buildParams({ siteKey: siteKey, mode: 'startSourceVerify' }),
            function (data) {
                verifyState.sourceVerify = data.status;
                if (data.status === 'pass') { hideEl(el.loading); showEl(el.pass); }
                else {
                    hideEl(el.loading); showEl(el.fail);
                    setText(el.fileCount, data.fileCount || 0);
                    setText(el.classCount, data.classCount || 0);
                    setText(el.functionCount, data.functionCount || 0);
                }
                checkVerifyDone();
            },
            function () {
                verifyState.sourceVerify = 'fail';
                hideEl(el.loading); showEl(el.fail);
                checkVerifyDone();
            }
        );
    }

    function startSchemaCompare() {
        var el = getVerifySchemaEls();
        showEl(el.loading); hideEl(el.pass); hideEl(el.fail); hideEl(el.error);

        ajaxPost('./deploy_verify_ps.php', buildParams({ siteKey: siteKey, mode: 'startSchemaCompare' }),
            function (data) {
                verifyState.schemaCompare = data.status;
                hideEl(el.loading); hideEl(el.pass); hideEl(el.fail); hideEl(el.error);
                if (data.status === 'pass' || data.status === 'skipped') {
                    showEl(el.pass);
                } else if (data.status === 'error') {
                    showEl(el.error);
                } else {
                    verifyState.schemaQueries = data.queries || [];
                    verifyState.dmlQueries = data.dmlQueries || null;
                    showEl(el.fail);
                    setText(el.tableCount, (data.addedTableCount || 0) + (data.changedColumnCount || 0));
                    setText(el.deletedCount, data.deletedTableCount || 0);
                    setText(el.dmlCount, data.dmlPendingCount || 0);
                }
                checkVerifyDone();
            },
            function () {
                verifyState.schemaCompare = 'error';
                hideEl(el.loading); hideEl(el.pass); hideEl(el.fail); showEl(el.error);
                checkVerifyDone();
            }
        );
    }

    function checkVerifyDone() {
        var sourceOk = verifyState.sourceVerify === 'pass';
        var schemaOk = verifyState.schemaCompare === 'pass' || verifyState.schemaCompare === 'skipped';
        var btnNext = document.querySelector('#pageActionsPhase2a .js-btn-next');
        if (!btnNext) return;
        if (sourceOk && schemaOk) { btnNext.removeAttribute('disabled'); btnNext.classList.remove('is-disable'); }
        else { btnNext.setAttribute('disabled', 'disabled'); btnNext.classList.add('is-disable'); }
    }

    function getVerifySourceEls() {
        return {
            loading: document.querySelector('#phase2a .js-source-loading'),
            pass: document.querySelector('#phase2a .js-source-pass'),
            fail: document.querySelector('#phase2a .js-source-fail'),
            fileCount: document.querySelector('#phase2a .js-source-file-count'),
            classCount: document.querySelector('#phase2a .js-source-class-count'),
            functionCount: document.querySelector('#phase2a .js-source-function-count')
        };
    }

    function getVerifySchemaEls() {
        return {
            loading: document.querySelector('#phase2a .js-schema-loading'),
            pass: document.querySelector('#phase2a .js-schema-pass'),
            fail: document.querySelector('#phase2a .js-schema-fail'),
            error: document.querySelector('#phase2a .js-schema-error'),
            tableCount: document.querySelector('#phase2a .js-schema-table-count'),
            deletedCount: document.querySelector('#phase2a .js-schema-deleted-count'),
            dmlCount: document.querySelector('#phase2a .js-schema-dml-count')
        };
    }

    /* ================================================================
     * Phase 2b: 스킨 선택 (from deploy_skin.js)
     * ================================================================ */

    function initSkinPhase() {
        var skipCheckbox = document.getElementById('skinSkipCheck');
        var skinListsWrap = document.getElementById('skinListsWrap');

        document.querySelectorAll('.js-skin-check').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                updateSkinNext();
            });
        });

        if (skipCheckbox) {
            skipCheckbox.addEventListener('change', function () {
                if (skinListsWrap) {
                    skinListsWrap.style.opacity = skipCheckbox.checked ? '0.5' : '';
                    skinListsWrap.style.pointerEvents = skipCheckbox.checked ? 'none' : '';
                }
                document.querySelectorAll('.js-skin-check').forEach(function (cb) {
                    if (skipCheckbox.checked) {
                        cb.setAttribute('disabled', 'disabled');
                    } else {
                        cb.removeAttribute('disabled');
                    }
                });
                updateSkinNext();
            });
        }

        var btnSkinNext = document.querySelector('#pageActionsPhase2b .js-btn-next');
        if (btnSkinNext) {
            btnSkinNext.addEventListener('click', function () {
                var isSkipped = skipCheckbox && skipCheckbox.checked ? '1' : '0';
                var selectedSkins = JSON.stringify(collectSelectedSkins());

                ajaxPost('./deploy_skin_ps.php',
                    buildParams({ siteKey: siteKey, mode: 'saveSkinSelection', isSkipped: isSkipped, selectedSkins: selectedSkins }),
                    function () { showPhase('phase3a'); },
                    function () { showPhase('phase3a'); }
                );
            });
        }

        updateSkinNext();
    }

    function updateSkinNext() {
        var skipCheckbox = document.getElementById('skinSkipCheck');
        if (skipCheckbox && skipCheckbox.checked) { enableSkinNext(); return; }
        var checkedSkins = document.querySelectorAll('.js-skin-check:checked');
        if (checkedSkins.length > 0) { enableSkinNext(); return; }
        disableSkinNext();
    }

    function enableSkinNext() {
        var btn = document.querySelector('#pageActionsPhase2b .js-btn-next');
        if (btn) { btn.removeAttribute('disabled'); btn.classList.remove('is-disable'); }
    }

    function disableSkinNext() {
        var btn = document.querySelector('#pageActionsPhase2b .js-btn-next');
        if (btn) { btn.setAttribute('disabled', 'disabled'); btn.classList.add('is-disable'); }
    }

    function collectSelectedSkins() {
        var skins = [];
        document.querySelectorAll('.js-skin-check').forEach(function (checkbox) {
            if (checkbox.checked) {
                var row = checkbox.closest('.ds-deploy__skin-row');
                if (row) {
                    var skinCode = row.getAttribute('data-skin-code') || '';
                    var skinName = row.getAttribute('data-skin-name') || '';
                    var devices = [];
                    try { devices = JSON.parse(row.getAttribute('data-skin-devices') || '[]'); } catch (e) { devices = []; }
                    devices.forEach(function (device) {
                        skins.push({ skinDevice: device, skinCode: skinCode, skinName: skinName });
                    });
                }
            }
        });
        return skins;
    }

    /* ================================================================
     * Phase 3a: 배포 실행 (from deploy_execute.js)
     * ================================================================ */

    var DEPLOY_LABELS = {
        1: { pending: '인트로 페이지 전환', running: '인트로 페이지 전환', done: '인트로 페이지 전환 완료' },
        2: { pending: '소스 파일 복사 완료', running: '소스 파일 복사 중...', done: '소스 파일 복사 완료' },
        3: { pending: '검증 완료 확인', running: '검증 확인 중...', done: '검증 완료 확인' }
    };
    var STEP_PROGRESS = { 1: 33, 2: 66, 3: 100 };
    var POLL_INTERVAL = 5000;
    var POLL_MAX_COUNT = 60;
    var POLL_ERROR_MAX = 5;
    var EXEC_PS_URL = './deploy_execute_ps.php';

    var deployStep = 0;
    var pollTimer = null;
    var pollCount = 0;
    var pollErrorCount = 0;

    function setDeployWarnBannerText(text) {
        var el = document.getElementById('deployWarnBannerText');
        if (el) el.textContent = text;
    }

    function startDeployExecution() {
        stopPolling();
        hideDeployError();

        setDeployIcon(1, 'pending'); setDeployLabel(1, 'pending');
        setDeployIcon(2, 'pending'); setDeployLabel(2, 'pending');
        setDeployIcon(3, 'pending'); setDeployLabel(3, 'pending');

        var radios = document.querySelectorAll('input[name="introPage"]');
        radios.forEach(function (r) { r.checked = false; });

        var introSection = document.getElementById('introPageSection');
        if (introSection) introSection.classList.add('ds-deploy--hidden');

        setDeployWarnBannerText('배포 실행 단계입니다. 아래 체크리스트를 순서대로 진행해 주세요.');
        updateDeployProgress(0);
        deployStep = 0;
        enableDeployNext();
    }

    function advanceDeployStep() {
        if (deployStep === 0) {
            // 1단계: 인트로 섹션 표시, 라디오 미선택 시 비활성
            setDeployWarnBannerText('배포를 실행 중입니다. 완료 후 운영 사이트에서 결과를 확인해 주세요.');
            var introSection = document.getElementById('introPageSection');
            if (introSection) introSection.classList.remove('ds-deploy--hidden');
            deployStep = 1;
            var selected = document.querySelector('input[name="introPage"]:checked');
            if (!selected) disableDeployNext();
        } else if (deployStep === 1) {
            // 1단계 확정: 인트로 페이지 전환 (라디오 선택 기반)
            var sel = document.querySelector('input[name="introPage"]:checked');
            if (sel && sel.value === 'yes') executeIntroEnable();
            else if (sel && sel.value === 'no') completeIntroSkip();
            else showIntroSkipModal(function () { completeIntroSkip(); });
        } else if (deployStep === 2) {
            // 2단계: 소스 파일 복사
            executeSourceCopy();
        } else if (deployStep === 3) {
            // 3단계: 검증 완료 확인
            executeHealthCheck();
        } else if (deployStep === 4) {
            var phase3bEl = document.getElementById('phase3b');
            if (phase3bEl) {
                var introWrap = phase3bEl.querySelector('.ds-deploy__intro-release');
                if (introWrap) {
                    if (introPageEnabled) {
                        introWrap.classList.remove('ds-deploy--hidden');
                    } else {
                        introWrap.classList.add('ds-deploy--hidden');
                    }
                }
            }
            showPhase('phase3b');
        }
    }

    function executeIntroEnable() {
        disableDeployNext(); setDeployIcon(1, 'running');
        var introSkin = document.getElementById('introSkinSelect');
        var skinValue = introSkin ? introSkin.value : '';
        postExecJson({ mode: 'saveIntroPage', useIntroPage: 'yes', introSkin: skinValue }, function (err, res) {
            if (err || !res || !res.success) {
                setDeployIcon(1, 'fail');
                showDeployError(res && res.error ? res.error : '인트로 페이지 설정에 실패했습니다.');
                enableDeployNext(); return;
            }
            introPageEnabled = true;
            completeIntroStep();
        });
    }

    function showIntroSkipConfirm() {
        showIntroSkipModal(function () { completeIntroSkip(); });
    }

    function completeIntroSkip() {
        disableDeployNext();
        postExecJson({ mode: 'saveIntroPage', useIntroPage: 'no' }, function (err, res) {
            if (err || !res || !res.success) {
                showDeployError('인트로 설정 저장에 실패했습니다.');
                setDeployIcon(1, 'fail'); enableDeployNext(); return;
            }
            completeIntroStep();
        });
    }

    function completeIntroStep() {
        setDeployIcon(1, 'done'); setDeployLabel(1, 'done');
        var introSection = document.getElementById('introPageSection');
        if (introSection) introSection.classList.add('ds-deploy--hidden');
        updateDeployProgress(STEP_PROGRESS[1]);
        deployStep = 2; enableDeployNext();
    }

    function executeSourceCopy() {
        disableDeployNext(); hideDeployError();
        setDeployIcon(2, 'running'); setDeployLabel(2, 'running');
        postExecJson({ mode: 'startDeploy' }, function (err, res) {
            if (err || !res || !res.success) {
                setDeployIcon(2, 'fail');
                showDeployError(res && res.error ? res.error : '배포 요청에 실패했습니다.');
                enableDeployNext(); return;
            }
            startDeployPolling();
        });
    }

    function startDeployPolling() {
        stopPolling(); pollCount = 0; pollErrorCount = 0;
        pollTimer = setInterval(function () {
            pollCount++;
            if (pollCount > POLL_MAX_COUNT) {
                stopPolling(); setDeployIcon(2, 'fail');
                showDeployError('배포 상태 확인 시간이 초과되었습니다.');
                enableDeployNext(); return;
            }
            postExecJson({ mode: 'getDeployStatus' }, function (err, res) {
                if (err || !res || !res.success) {
                    pollErrorCount++;
                    if (pollErrorCount >= POLL_ERROR_MAX) {
                        stopPolling(); setDeployIcon(2, 'fail');
                        showDeployError('배포 상태 조회에 연속 실패했습니다.');
                        enableDeployNext();
                    }
                    return;
                }
                pollErrorCount = 0;
                var status = res.data && res.data.deployStatus;
                if (status === 'COMPLETED') {
                    stopPolling(); setDeployIcon(2, 'done'); setDeployLabel(2, 'done');
                    updateDeployProgress(STEP_PROGRESS[2]); deployStep = 3; enableDeployNext();
                } else if (status === 'FAILED') {
                    stopPolling(); setDeployIcon(2, 'fail');
                    showDeployError('배포 중 오류가 발생했습니다.');
                    disableDeployNext();
                }
            });
        }, POLL_INTERVAL);
    }

    function executeHealthCheck() {
        disableDeployNext(); hideDeployError();
        setDeployIcon(3, 'running'); setDeployLabel(3, 'running');
        postExecJson({ mode: 'healthCheck' }, function (err, res) {
            if (err || !res || !res.success) {
                setDeployIcon(3, 'fail');
                showDeployError(res && res.error ? res.error : '헬스체크에 실패했습니다.');
                disableDeployNext(); return;
            }
            setDeployIcon(3, 'done'); setDeployLabel(3, 'done');
            updateDeployProgress(STEP_PROGRESS[3]); deployStep = 4; enableDeployNext();
            var warnBanner = document.getElementById('deployWarnBanner');
            if (warnBanner) warnBanner.classList.add('ds-deploy--hidden');
        });
    }

    function postExecJson(data, callback) {
        data.siteKey = siteKey;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', EXEC_PS_URL, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            try { callback(null, JSON.parse(xhr.responseText)); }
            catch (e) { callback(e, null); }
        };
        xhr.onerror = function () { callback(new Error('네트워크 오류'), null); };
        var params = [];
        for (var key in data) {
            if (data.hasOwnProperty(key)) params.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
        }
        xhr.send(params.join('&'));
    }

    function setDeployIcon(step, state) {
        var icon = document.getElementById('dci-icon-' + step);
        if (!icon) return;
        icon.className = 'ds-deploy__check-icon';
        icon.innerHTML = '';
        if (state === 'pending') icon.classList.add('ds-deploy__check-icon--pending');
        else if (state === 'running') {
            icon.classList.add('ds-deploy__check-icon--running');
            icon.innerHTML = '<div class="ncua-spinner ncua-spinner--xs"><div class="ncua-spinner__content"></div></div>';
        } else if (state === 'done') {
            icon.classList.add('ds-deploy__check-icon--done');
            icon.setAttribute('data-ncua-icon', 'CheckCircleFill');
            if (!window.ncua || !window.ncua.Icon) icon.innerHTML = '&#10003;';
        } else if (state === 'fail') {
            icon.classList.add('ds-deploy__check-icon--fail');
            icon.setAttribute('data-ncua-icon', 'XCircleFill');
            if (!window.ncua || !window.ncua.Icon) icon.innerHTML = '&#10007;';
        }
    }

    function setDeployLabel(step, state) {
        var el = document.getElementById('dci-label-' + step);
        if (!el) return;
        el.textContent = DEPLOY_LABELS[step][state] || DEPLOY_LABELS[step].pending;
        el.className = 'ds-deploy__check-label' + (state === 'pending' ? ' ds-deploy__check-label--pending' : '');
    }

    function updateDeployProgress(pct) {
        var text = document.getElementById('deployProgressText');
        var bar = document.getElementById('deployProgressBar');
        if (text) text.textContent = pct + '%';
        if (bar) { bar.style.width = pct + '%'; bar.setAttribute('aria-valuenow', pct); }
    }

    function showDeployError(message) {
        var callout = document.getElementById('deployErrorCallout');
        var text = callout ? callout.querySelector('.ds-deploy__error-callout-text') : null;
        if (text) text.textContent = message;
        if (callout) callout.classList.remove('ds-deploy--hidden');
        var btnRetry = document.getElementById('btnDeployRetry');
        var btnSupport = document.getElementById('btnDeploySupport');
        if (btnRetry) btnRetry.classList.remove('ds-deploy--hidden');
        if (btnSupport) btnSupport.classList.remove('ds-deploy--hidden');
    }

    function hideDeployError() {
        var callout = document.getElementById('deployErrorCallout');
        if (callout) callout.classList.add('ds-deploy--hidden');
        var btnRetry = document.getElementById('btnDeployRetry');
        var btnSupport = document.getElementById('btnDeploySupport');
        if (btnRetry) btnRetry.classList.add('ds-deploy--hidden');
        if (btnSupport) btnSupport.classList.add('ds-deploy--hidden');
    }

    function enableDeployNext() {
        var btn = document.getElementById('btnDeployNext');
        if (btn) { btn.disabled = false; btn.classList.remove('is-disable'); }
    }

    function disableDeployNext() {
        var btn = document.getElementById('btnDeployNext');
        if (btn) { btn.disabled = true; btn.classList.add('is-disable'); }
    }

    function stopPolling() {
        if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
    }

    /* ================================================================
     * Phase 4a: 롤백 실행 (Story 2A.4)
     * ================================================================ */

    var ROLLBACK_LABELS = {
        1: { pending: '롤백 소스 확인', running: '롤백 소스 확인', done: '롤백 소스 확인' },
        2: { pending: '이전 소스 복원 중...', running: '이전 소스 복원 중...', done: '이전 소스 복원 완료' },
        3: { pending: '롤백 후 검증', running: '롤백 후 검증 중...', done: '롤백 후 검증' }
    };
    var ROLLBACK_PROGRESS = { 1: 33, 2: 66, 3: 100 };
    var ROLLBACK_POLL_INTERVAL = 5000;
    var ROLLBACK_POLL_MAX_COUNT = 60;
    var ROLLBACK_POLL_ERROR_MAX = 3;

    var isRollingBack = false;
    var rollbackPollTimer = null;
    var rollbackPollCount = 0;
    var rollbackPollErrorCount = 0;
    var rollbackVersionName = '';

    function startRollback() {
        if (isRollingBack) return;
        isRollingBack = true;

        hideRollbackError();
        setRollbackIcon(1, 'pending'); setRollbackLabel(1, 'pending');
        setRollbackIcon(2, 'pending'); setRollbackLabel(2, 'pending');
        setRollbackIcon(3, 'pending'); setRollbackLabel(3, 'pending');
        updateRollbackProgress(0);
        disableRollbackNext();

        // Step 1: versionName 취득
        setRollbackIcon(1, 'running');
        postExecJson({ mode: 'getDeployStatus' }, function (err, res) {
            if (err || !res || !res.success) {
                rollbackFail(1, '배포 이력을 조회할 수 없습니다.');
                return;
            }
            var history = res.data && res.data.history;
            rollbackVersionName = (history && history.currentTagLabel) ? history.currentTagLabel : '';
            if (!rollbackVersionName) {
                rollbackFail(1, '배포 이력을 조회할 수 없습니다.');
                return;
            }
            setRollbackIcon(1, 'done'); setRollbackLabel(1, 'done');
            updateRollbackProgress(ROLLBACK_PROGRESS[1]);
            executeRollbackRequest();
        });
    }

    function executeRollbackRequest() {
        setRollbackIcon(2, 'running'); setRollbackLabel(2, 'running');
        postExecJson({ mode: 'startRollback', versionName: rollbackVersionName }, function (err, res) {
            if (err || !res || !res.success) {
                rollbackFail(2, (res && res.error) ? res.error : '롤백 요청에 실패했습니다.');
                return;
            }
            startRollbackPolling();
        });
    }

    function startRollbackPolling() {
        stopRollbackPolling();

        rollbackPollCount = 0;
        rollbackPollErrorCount = 0;
        rollbackPollTimer = setInterval(function () {
            rollbackPollCount++;
            if (rollbackPollCount > ROLLBACK_POLL_MAX_COUNT) {
                stopRollbackPolling();
                rollbackFail(2, '롤백 상태 확인 시간이 초과되었습니다.');
                return;
            }
            postExecJson({ mode: 'getDeployStatus' }, function (err, res) {
                if (err || !res || !res.success) {
                    rollbackPollErrorCount++;
                    if (rollbackPollErrorCount >= ROLLBACK_POLL_ERROR_MAX) {
                        stopRollbackPolling();
                        rollbackFail(2, '상태 조회에 실패했습니다.');
                    }
                    return;
                }
                rollbackPollErrorCount = 0;
                var status = res.data && res.data.deployStatus;
                if (status === 'COMPLETED') {
                    stopRollbackPolling();
                    setRollbackIcon(2, 'done'); setRollbackLabel(2, 'done');
                    updateRollbackProgress(ROLLBACK_PROGRESS[2]);
                    executeRollbackHealthCheck();
                } else if (status !== 'REQUESTED') {
                    stopRollbackPolling();
                    rollbackFail(2, '롤백 중 오류가 발생했습니다.');
                }
            });
        }, ROLLBACK_POLL_INTERVAL);
    }

    function executeRollbackHealthCheck() {
        setRollbackIcon(3, 'running'); setRollbackLabel(3, 'running');
        postExecJson({ mode: 'healthCheck' }, function (err, res) {
            if (err || !res || !res.success) {
                setRollbackIcon(3, 'fail');
                showRollbackError(res && res.error ? res.error : '헬스체크에 실패했습니다.');
                isRollingBack = false;
                return;
            }
            setRollbackIcon(3, 'done'); setRollbackLabel(3, 'done');
            updateRollbackProgress(ROLLBACK_PROGRESS[3]);
            enableRollbackNext();
            isRollingBack = false;
        });
    }

    function rollbackFail(failStep, message) {
        for (var i = 1; i <= 3; i++) {
            if (i < failStep) { setRollbackIcon(i, 'done'); setRollbackLabel(i, 'done'); }
            else if (i === failStep) { setRollbackIcon(i, 'fail'); }
            else { setRollbackIcon(i, 'pending'); setRollbackLabel(i, 'pending'); }
        }
        showRollbackError(message);
        isRollingBack = false;
    }

    function retryRollback() {
        startRollback();
    }

    function rollbackNext() {
        showPhase('phase4b');
    }

    function setRollbackIcon(step, state) {
        var icon = document.getElementById('rci-icon-' + step);
        if (!icon) return;
        icon.className = 'ds-deploy__check-icon';
        icon.innerHTML = '';
        if (state === 'pending') icon.classList.add('ds-deploy__check-icon--pending');
        else if (state === 'running') {
            icon.classList.add('ds-deploy__check-icon--running');
            icon.innerHTML = '<div class="ncua-spinner ncua-spinner--xs"><div class="ncua-spinner__content"></div></div>';
        } else if (state === 'done') {
            icon.classList.add('ds-deploy__check-icon--done');
            icon.setAttribute('data-ncua-icon', 'CheckCircleFill');
            if (!window.ncua || !window.ncua.Icon) icon.innerHTML = '&#10003;';
        } else if (state === 'fail') {
            icon.classList.add('ds-deploy__check-icon--fail');
            icon.setAttribute('data-ncua-icon', 'XClose');
            if (!window.ncua || !window.ncua.Icon) icon.innerHTML = '&#10007;';
        }
    }

    function setRollbackLabel(step, state) {
        var el = document.getElementById('rci-label-' + step);
        if (!el) return;
        el.textContent = ROLLBACK_LABELS[step][state] || ROLLBACK_LABELS[step].pending;
        el.className = 'ds-deploy__check-label' + (state === 'pending' ? ' ds-deploy__check-label--pending' : '');
    }

    function updateRollbackProgress(pct) {
        var text = document.getElementById('rollbackProgressText');
        var bar = document.getElementById('rollbackProgressBar');
        if (text) text.textContent = pct + '%';
        if (bar) { bar.style.width = pct + '%'; bar.setAttribute('aria-valuenow', pct); }
    }

    function showRollbackError(message) {
        var callout = document.getElementById('rollbackErrorCallout');
        var text = document.getElementById('rollbackErrorText');
        if (text) text.textContent = message;
        if (callout) callout.classList.remove('ds-deploy--hidden');
    }

    function hideRollbackError() {
        var callout = document.getElementById('rollbackErrorCallout');
        if (callout) callout.classList.add('ds-deploy--hidden');
    }

    function enableRollbackNext() {
        var btn = document.getElementById('btnRollbackNext');
        if (btn) { btn.disabled = false; btn.classList.remove('is-disable'); }
    }

    function disableRollbackNext() {
        var btn = document.getElementById('btnRollbackNext');
        if (btn) { btn.disabled = true; btn.classList.add('is-disable'); }
    }

    function stopRollbackPolling() {
        if (rollbackPollTimer) { clearInterval(rollbackPollTimer); rollbackPollTimer = null; }
    }

    /* ================================================================
     * 공통 AJAX 유틸
     * ================================================================ */

    function ajaxPost(url, data, onSuccess, onError) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.success) onSuccess(response.data);
                else onError(response.message || response.error || '오류가 발생했습니다.');
            } catch (e) { onError('서버 응답을 처리할 수 없습니다.'); }
        };
        xhr.send(data);
    }

    function buildParams(params) {
        var parts = [];
        for (var key in params) {
            if (params.hasOwnProperty(key)) parts.push(encodeURIComponent(key) + '=' + encodeURIComponent(params[key]));
        }
        return parts.join('&');
    }

    function showEl(el) { if (el) el.classList.remove('ds-deploy--hidden'); }
    function hideEl(el) { if (el) el.classList.add('ds-deploy--hidden'); }
    function setText(el, val) { if (el) el.textContent = val; }

    /* ================================================================
     * 이벤트 바인딩 + 초기화
     * ================================================================ */

    // 시작하기 버튼
    var btnStart = document.getElementById('btnStartDeploy');
    if (btnStart && !btnStart.disabled) {
        btnStart.addEventListener('click', function () { startProcess(); });
    }

    // phase2a [다음 단계] (PageTitle 버튼)
    var btnVerifyNext = document.querySelector('#pageActionsPhase2a .js-btn-next');
    if (btnVerifyNext) {
        btnVerifyNext.addEventListener('click', function () {
            if (btnVerifyNext.disabled) return;
            showPhase('phase2b');
        });
    }

    // phase2a 재검증 버튼
    var btnSourceReverify = document.querySelector('#phase2a .js-btn-source-reverify');
    if (btnSourceReverify) {
        btnSourceReverify.addEventListener('click', function () {
            verifyState.sourceVerify = null; verifyInitialized = false;
            startSourceVerify();
        });
    }
    document.querySelectorAll('#phase2a .js-btn-schema-reverify').forEach(function (btn) {
        btn.addEventListener('click', function () {
            verifyState.schemaCompare = null;
            startSchemaCompare();
        });
    });
    var btnSchemaReport = document.querySelector('#phase2a .js-btn-schema-report');
    if (btnSchemaReport) {
        btnSchemaReport.addEventListener('click', function () {
            if (!verifyState.schemaQueries || !verifyState.schemaQueries.applyToTarget) return;
            var q = verifyState.schemaQueries;
            var sections = [];
            if (q.applyToTarget && q.applyToTarget.length) {
                sections.push('-- [DDL Apply to Target]\n' + q.applyToTarget.join('\n'));
            }
            var dq = verifyState.dmlQueries;
            if (dq && dq.applyToTarget && dq.applyToTarget.length) {
                sections.push('-- [DML Apply to Target]\n' + dq.applyToTarget.join('\n\n'));
            }
            if (!sections.length) return;
            var content = sections.join('\n\n');
            var blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'schema_compare_report.txt';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });
    }

    // phase3a [다음 단계] + [재시도]
    var btnDeployNext = document.getElementById('btnDeployNext');
    if (btnDeployNext) btnDeployNext.addEventListener('click', advanceDeployStep);

    // phase4a [처음으로] — 헤더 버튼은 delegated listener 범위 밖이므로 직접 바인딩
    var btnRollbackNextEl = document.getElementById('btnRollbackNext');
    if (btnRollbackNextEl) btnRollbackNextEl.addEventListener('click', showIntro);

    var btnRetry = document.querySelector('.js-btn-retry');
    if (btnRetry) {
        btnRetry.addEventListener('click', function () {
            hideDeployError();
            if (deployStep === 1) {
                var sel = document.querySelector('input[name="introPage"]:checked');
                if (sel && sel.value === 'yes') executeIntroEnable();
                else completeIntroSkip();
            } else if (deployStep === 2) { executeSourceCopy(); }
            else if (deployStep === 3) { executeHealthCheck(); }
        });
    }

    // 인트로 라디오 변경 (deployStep===1: 인트로 섹션 표시 후 대기 상태)
    document.querySelectorAll('input[name="introPage"]').forEach(function (r) {
        r.addEventListener('change', function () {
            if (deployStep === 1) {
                var skinSelect = document.getElementById('introSkinSelect');
                if (this.value === 'yes') {
                    if (skinSelect) skinSelect.classList.remove('ds-deploy--hidden');
                } else {
                    if (skinSelect) skinSelect.classList.add('ds-deploy--hidden');
                }
                enableDeployNext();
            }
        });
    });

    // [이전] 버튼 바인딩 (data-prev-target 속성 기반)
    document.querySelectorAll('.js-btn-prev[data-prev-target]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var target = btn.getAttribute('data-prev-target');
            if (target) showPrevStepModal(target);
        });
    });

    // stepper 클릭 바인딩 (data-step 속성 기반)
    document.querySelectorAll('.ds-deploy__stepper-item[data-step]').forEach(function (item) {
        item.addEventListener('click', function () {
            var step = item.getAttribute('data-step');
            if (step) onStepperClick(step);
        });
    });

    // 모달 [취소] / [확인] 바인딩
    var btnPrevCancel = document.getElementById('btnPrevStepCancel');
    if (btnPrevCancel) btnPrevCancel.addEventListener('click', closePrevStepModal);

    var btnPrevConfirm = document.getElementById('btnPrevStepConfirm');
    if (btnPrevConfirm) btnPrevConfirm.addEventListener('click', confirmPrevStep);

    // 스킨 phase 초기화
    initConsentPhase();
    initSkinPhase();

    // URL ?rollback=true 처리 (AC7)
    if (typeof isRollback !== 'undefined' && isRollback === true) {
        startProcess();
        showPhase('phase4a');
    }

    /* ================================================================
     * 전역 노출 (AC10: 9.5)
     * ================================================================ */

    /* ── phase3b: 사후 검증 ── */

    (function () {
        var chk = document.getElementById('introReleaseCheck');
        var warn = document.getElementById('intro-keep-warning');
        var hidden = document.getElementById('autoReleaseIntro');
        if (!chk || !warn) return;
        chk.addEventListener('change', function () {
            if (chk.checked) {
                warn.classList.add('ds-deploy--hidden');
                if (hidden) hidden.value = '1';
            } else {
                warn.classList.remove('ds-deploy--hidden');
                if (hidden) hidden.value = '0';
            }
        });
    }());

    function showToast(type, message, subText) {
        var areaId = (type === 'success') ? 'notificationSuccess' : 'notificationWarning';
        var textId = (type === 'success') ? 'notificationSuccessText' : 'notificationWarningText';
        var subTextId = (type === 'success') ? 'notificationSuccessSubText' : 'notificationWarningSubText';
        var area = document.getElementById(areaId);
        var textEl = document.getElementById(textId);
        if (!area || !textEl) return;
        textEl.textContent = message;
        var subTextEl = document.getElementById(subTextId);
        if (subTextEl) {
            if (subText) {
                subTextEl.textContent = subText;
                subTextEl.style.display = '';
            } else {
                subTextEl.textContent = '';
                subTextEl.style.display = 'none';
            }
        }
        area.classList.add('show');
        if (notificationTimers[areaId]) clearTimeout(notificationTimers[areaId]);
        var duration = (type === 'success') ? 3000 : 5000;
        notificationTimers[areaId] = setTimeout(function () {
            area.classList.remove('show');
        }, duration);
    }

    function finalizeDeploy() {
        var hidden = document.getElementById('autoReleaseIntro');
        var autoRelease = hidden ? hidden.value : '0';
        if (autoRelease !== '1') {
            window.location.href = window.location.pathname;
            return;
        }
        postExecJson({ mode: 'releaseIntroPage' }, function (err, response) {
            if (err || !response || !response.success) {
                showToast('warning', '인트로 해제에 실패했습니다. 수동으로 해제해 주세요.');
                setTimeout(function () { window.location.href = window.location.pathname; }, 5000);
                return;
            }
            window.location.href = window.location.pathname;
        });
    }

    function showRollbackConfirm() {
        var modal = document.getElementById('rollbackModal');
        if (modal) modal.style.display = 'flex';
    }

    function closeRollbackModal() {
        var modal = document.getElementById('rollbackModal');
        if (modal) modal.style.display = 'none';
    }

    function showIntroSkipModal(onConfirm) {
        var modal = document.getElementById('introSkipModal');
        if (!modal) { if (onConfirm) onConfirm(); return; }
        modal.style.display = 'flex';
        var btnConfirm = document.getElementById('btnIntroSkipConfirm');
        var btnCancel = document.getElementById('btnIntroSkipCancel');
        function cleanup() {
            modal.style.display = 'none';
            if (btnConfirm) btnConfirm.removeEventListener('click', doConfirm);
            if (btnCancel) btnCancel.removeEventListener('click', doCancel);
        }
        function doConfirm() { cleanup(); if (onConfirm) onConfirm(); }
        function doCancel() { cleanup(); }
        if (btnConfirm) btnConfirm.addEventListener('click', doConfirm);
        if (btnCancel) btnCancel.addEventListener('click', doCancel);
    }

    function confirmRollback() {
        closeRollbackModal();
        showPhase('phase4a');
    }

    /* ── phase3b: 운영 사이트 바로가기 ── */
    function openProdAdmin() {
        if (typeof prodAdminUrl !== 'undefined' && prodAdminUrl) {
            window.open(prodAdminUrl, '_blank');
        }
    }

    function openProdShop() {
        if (typeof prodShopUrl !== 'undefined' && prodShopUrl) {
            window.open(prodShopUrl, '_blank');
        }
    }

    function goDeployHistory() {
        window.location.href = 'deploy_history.php';
    }

    /* ── phase3b/3c: data-action 이벤트 바인딩 ── */
    var deployActionMap = {
        'openProdAdmin': openProdAdmin,
        'openProdShop': openProdShop,
        'finalizeDeploy': finalizeDeploy,
        'startRedeploy': startRedeploy,
        'showRollbackConfirm': showRollbackConfirm,
        'goDeployHistory': goDeployHistory,
        'retryFinalize': finalizeDeploy,
        'showIntroFromComplete': showIntro,
        'goDeployHistoryFromComplete': goDeployHistory,
        'closeRollbackModal': closeRollbackModal,
        'confirmRollback': confirmRollback,
        'retryRollback': retryRollback,
        'rollbackNext': rollbackNext,
        'showIntroFromRollback': showIntro
    };

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-action]');
        if (!btn) return;
        if (!btn.closest('#phase3b, #phase4a, #phase4b, #rollbackModal')) return;
        var action = btn.getAttribute('data-action');
        if (deployActionMap[action]) {
            e.preventDefault();
            deployActionMap[action]();
        }
    });

    window.showPhase = showPhase;
    window.showPrevStepModal = showPrevStepModal;
    window.closePrevStepModal = closePrevStepModal;
    window.confirmPrevStep = confirmPrevStep;
    window.showIntro = showIntro;
    window.startProcess = startProcess;
    window.startRedeploy = startRedeploy;
    window.onStepperClick = onStepperClick;
    window.startDeployExecution = startDeployExecution;
    window.advanceDeployStep = advanceDeployStep;
    window.finalizeDeploy = finalizeDeploy;
    window.showRollbackConfirm = showRollbackConfirm;
    window.closeRollbackModal = closeRollbackModal;
    window.confirmRollback = confirmRollback;
    window.startRollback = startRollback;
    window.retryRollback = retryRollback;
    window.rollbackNext = rollbackNext;
    window.showDeployCase = function (caseId, btn) {
        var hidden = 'ds-deploy--hidden';
        var els = [
            document.querySelector('.js-source-loading'), document.querySelector('.js-source-pass'),
            document.querySelector('.js-source-fail'), document.querySelector('.js-schema-loading'),
            document.querySelector('.js-schema-pass'), document.querySelector('.js-schema-fail')
        ];
        els.forEach(function (el) { if (el) el.classList.add(hidden); });
        var btnN = document.querySelector('#pageActionsPhase2a .js-btn-next');
        if (caseId === 'progress') {
            showEl(els[0]); showEl(els[3]);
            if (btnN) { btnN.disabled = true; btnN.classList.add('is-disable'); }
        } else if (caseId === 'pass') {
            showEl(els[1]); showEl(els[4]);
            if (btnN) { btnN.disabled = false; btnN.classList.remove('is-disable'); }
        } else if (caseId === 'fail') {
            showEl(els[2]); showEl(els[5]);
            if (btnN) { btnN.disabled = true; btnN.classList.add('is-disable'); }
        }
        var caseBtns = document.querySelectorAll('.ds-deploy__case-btn');
        caseBtns.forEach(function (b) { b.classList.remove('ds-deploy__case-btn--active'); });
        if (btn) btn.classList.add('ds-deploy__case-btn--active');
    };

})();
