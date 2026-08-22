/**
 * 커스터마이징 현황 페이지 JavaScript
 * Story 2B.1: 카드 영역 인터랙션
 * Story 2B.2: 환경별 카드 버튼 동작
 *
 * 각 카드 데이터는 개별 ajax로 병렬 로딩
 */
(function () {
    'use strict';

    var articleEl = document.querySelector('.ds-customization');
    var isStaging = (articleEl && articleEl.getAttribute('data-is-staging') === '1');

    // =========================================================================
    // 카드 렌더링 헬퍼
    // =========================================================================

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function showCardError(cardId, message) {
        var card = document.getElementById(cardId);
        if (!card) return;
        var body = card.querySelector('.ds-customization__card-body');
        if (body) {
            body.innerHTML = '<span class="ds-customization__kpi-number" style="color: var(--gray-300);">-</span>' +
                '<span class="ds-customization__card-error" style="color:var(--gray-400);font-size:var(--font-size-xs,12px);">' +
                escapeHtml(message || '데이터를 불러올 수 없습니다') + '</span>';
        }
    }

    // =========================================================================
    // 카드1: 시스템 버전 (async ajax)
    // =========================================================================
    function loadSystemVersion() {
        fetch('/development/system_version_ps.php', { method: 'POST' })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                var card = document.getElementById('cardSysVersion');
                if (!card) return;
                var body = card.querySelector('.ds-customization__card-body');
                var bottom = card.querySelector('.ds-customization__card-bottom');

                if (!json.success || !json.data) {
                    showCardError('cardSysVersion');
                    return;
                }

                var d = json.data;
                var godoVersion = escapeHtml(d.godoVersion || '-');
                var phpVersion = escapeHtml(d.phpVersion || '').split('.')[0];

                var versionDisplay = 'PHP ' + phpVersion + ' <span class="ds-customization__version-sep">&middot;</span> ' + godoVersion;

                body.innerHTML = '<span class="ds-customization__kpi-number" style="color: var(--gray-700);">' + versionDisplay + '</span>';

                if (d.isLatest) {
                    bottom.innerHTML = '<span class="ds-customization__version-status ds-customization__version-status--up-to-date">고도몰의 최신 버전을 사용중입니다</span>';
                } else {
                    bottom.innerHTML = '<span class="ds-customization__version-status ds-customization__version-status--needs-update">최신 버전이 출시되었습니다. 안정적인 운영을 위해 업데이트를 진행해 주세요</span>';
                }
            })
            .catch(function () {
                showCardError('cardSysVersion');
            });
    }

    // =========================================================================
    // 카드2: 커스터마이징 집계 (async ajax)
    // =========================================================================
    function loadCustomizationSummary() {
        fetch('/development/customization_summary_ps.php', { method: 'POST' })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                var card = document.getElementById('cardCustomize');
                if (!card) return;
                var body = card.querySelector('.ds-customization__card-body');
                var tag = document.getElementById('tagNeedsUpdate');

                if (!json.success || !json.data) {
                    showCardError('cardCustomize');
                    return;
                }

                var d = json.data;
                var fileCount = parseInt(d.fileCount, 10) || 0;
                var functionCount = parseInt(d.functionCount, 10) || 0;
                var classCount = parseInt(d.classCount, 10) || 0;
                var totalCount = fileCount + functionCount + classCount;

                if (totalCount === 0) {
                    body.innerHTML = '<span class="ds-customization__kpi-number" style="color: var(--gray-300);">-</span>';
                } else {
                    if (tag) tag.style.display = '';
                    body.innerHTML =
                        '<span class="ds-customization__kpi-number" style="color: var(--orange-500);">' + totalCount + '</span>' +
                        '<div class="ds-customization__sub-items">' +
                            '<div class="ds-customization__sub-item"><span class="ds-customization__sub-label">파일</span><span class="ds-customization__sub-value">' + fileCount + '</span></div>' +
                            '<div class="ds-customization__sub-item"><span class="ds-customization__sub-label">함수</span><span class="ds-customization__sub-value">' + functionCount + '</span></div>' +
                            '<div class="ds-customization__sub-item"><span class="ds-customization__sub-label">클래스</span><span class="ds-customization__sub-value">' + classCount + '</span></div>' +
                        '</div>';
                }
            })
            .catch(function () {
                showCardError('cardCustomize');
            });
    }

    // =========================================================================
    // 카드3: 스테이징 서버 현황 (운영 환경) / 배포 현황 (스테이징 환경)
    // =========================================================================
    function loadStagingInfo() {
        fetch('/development/staging_info_ps.php', { method: 'POST' })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                var d = json.data || {};
                renderStagingCard(d.stagingInfo);
            })
            .catch(function () {
                showCardError('cardStaging');
            });
    }

    function loadDeployStatus() {
        fetch('/development/deploy_status_ps.php', { method: 'POST' })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                var d = json.data || {};
                renderDeployCard(d.deployStatus);
            })
            .catch(function () {
                showCardError('cardDeploy');
            });
    }

    function renderStagingCard(info) {
        var card = document.getElementById('cardStaging');
        if (!card) return;
        var body = card.querySelector('.ds-customization__card-body');
        var btnArea = document.getElementById('stagingBtnArea');

        if (!info) {
            showCardError('cardStaging');
            return;
        }

        var isUsing = info.isUsing;
        var isCreating = info.isCreating;
        var adminUrl = info.adminUrl || '';

        if (isCreating) {
            body.innerHTML =
                '<span class="ds-customization__kpi-number" id="stagingStatus" style="color: var(--gray-400);">생성 중</span>' +
                '<span class="ds-customization__staging-notice" style="color: var(--gray-400); font-size: var(--font-size-xs, 12px);">최대 1시간 소요</span>';
            btnArea.innerHTML = '<button class="ncua-btn ncua-btn--sm ncua-btn--primary" id="stagingBtn" disabled><span class="ncua-btn__label">스테이징 서버 신청</span></button>';
        } else if (isUsing) {
            var expiresAt = info.expiresAt || '';
            var daysLeft = (typeof info.daysLeft === 'number') ? info.daysLeft : null;
            var isImminent = info.isImminent === true;
            var expiryHtml = '';
            if (expiresAt) {
                var expiryDateStr = expiresAt.substring(0, 10);
                var imminentClass = isImminent ? ' pw-staging-expire--imminent' : '';
                var dDayText = (isImminent && daysLeft !== null) ? ' (D-' + daysLeft + ')' : '';
                expiryHtml = '<span class="pw-staging-expire' + imminentClass + '">' +
                    '유효기간: ' + escapeHtml(expiryDateStr) + dDayText +
                    '</span>';
            }

            body.innerHTML =
                '<span class="ds-customization__kpi-number" id="stagingStatus" style="color: var(--green-600);">사용 중</span>' +
                expiryHtml +
                '<div class="ncds-callout ncds-callout--info pw-staging-extend-notice">스테이징 서버 관리자 페이지 로그인 시 유효기간이 1개월 자동 연장됩니다.</div>';

            var safeUrl = (adminUrl.indexOf('http://') === 0 || adminUrl.indexOf('https://') === 0)
                ? escapeHtml(adminUrl) : '';
            btnArea.innerHTML =
                '<a href="' + safeUrl + '" target="_blank" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray" id="stagingBtn"><span class="ncua-btn__label">스테이징 서버 접속</span></a>';
        } else {
            var calloutHtml = info.showCallout
                ? '<div class="ds-customization__callout-info">커스터마이징을 안전하게 관리할 수 있는 스테이징 서버를 신청하세요.</div>'
                : '';
            body.innerHTML =
                '<span class="ds-customization__kpi-number" id="stagingStatus" style="color: var(--gray-300);">미사용</span>' +
                calloutHtml;
            btnArea.innerHTML =
                '<button class="ncua-btn ncua-btn--sm ncua-btn--primary" id="stagingBtn" data-validate-url="/development/staging_validate_ps.php" data-apply-url="/development/staging_ps.php"><span class="ncua-btn__label">스테이징 서버 신청</span></button>';

            bindStagingApplyButton();
        }
    }

    var STATUS_COLOR_MAP = {
        NOT_DEPLOYED: 'var(--gray-300)',
        REQUESTED: 'var(--orange-500)',
        COMPLETED: 'var(--green-600)',
        ROLLBACK_COMPLETED: '#EC1D31', // red-500 (wireframe §3-a)
        FAILED: 'var(--red-500)',
        STOPPED: 'var(--gray-500)'
    };

    function renderDeployCard(status) {
        var card = document.getElementById('cardDeploy');
        if (!card) return;
        var body = card.querySelector('.ds-customization__card-body');
        var bottom = document.getElementById('deployBottom');

        if (!status) {
            showCardError('cardDeploy');
            return;
        }

        var label = escapeHtml(status.status || '미배포');
        var code = status.statusCode || 'NOT_DEPLOYED';
        var color = STATUS_COLOR_MAP[code] || 'var(--gray-300)';

        var spinnerHtml = (code === 'REQUESTED')
            ? '<div class="ncua-spinner ncua-spinner--xs" style="display:inline-block;vertical-align:middle;margin-right:4px;"><div class="ncua-spinner__content"></div></div>'
            : '';

        body.innerHTML = '<span class="ds-customization__kpi-number" id="deployStatus" style="color:' + color + ';">' + spinnerHtml + label + '</span>';

        var bottomHtml = '';
        if (code === 'FAILED') {
            bottomHtml +=
                '<span class="ds-customization__deploy-prompt" style="color:var(--orange-600);display:flex;align-items:center;gap:4px;">' +
                '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="none" color="var(--orange-600)" style="flex-shrink:0;"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M22 12c0 5.523-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2s10 4.477 10 10"></path></svg>' +
                '커스터마이징 검증이 필요합니다.</span>';
        }
        bottomHtml += '<button class="ncua-btn ncua-btn--sm ncua-btn--primary" id="btnDeploy"><span class="ncua-btn__label">배포하기</span></button>';
        if (bottom) { bottom.innerHTML = bottomHtml; }

        var btnDeploy = document.getElementById('btnDeploy');
        if (btnDeploy) {
            btnDeploy.addEventListener('click', function () {
                window.location.href = '/development/deploy.php';
            });
        }
    }

    // =========================================================================
    // 업데이트 노트 (async ajax)
    // =========================================================================
    function loadUpdateNote() {
        fetch('/development/update_note_ps.php', { method: 'POST' })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                var listArea = document.getElementById('updateListArea');
                if (!listArea) return;

                if (!json.success || !json.data || !json.data.updateList || json.data.updateList.length === 0) {
                    listArea.innerHTML = '<li class="ds-customization__update-item" style="color:var(--gray-400);font-size:var(--font-size-xs,12px);">업데이트 내역이 없습니다.</li>';
                    return;
                }

                var moreLink = document.getElementById('updateMoreLink');
                if (moreLink && json.data.moreUrl) {
                    moreLink.href = json.data.moreUrl;
                }

                var html = '';
                json.data.updateList.forEach(function (item) {
                    var title = escapeHtml(item.title || '');
                    var date = escapeHtml(item.date || '');
                    var url = item.url || '';
                    var badge = item.isNew
                        ? ' <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="3" fill="#FCCEEE"></rect><path d="M5.729 3.743L10.217 9.804V3.743H11.625V12.257H10.283L5.784 6.174V12.257H4.376V3.743H5.729Z" fill="#C11574"></path></svg>'
                        : '';

                    var titleHtml = url
                        ? '<a href="' + escapeHtml(url) + '" target="_blank" rel="noopener noreferrer" class="ds-customization__update-title">' + title + '</a>'
                        : '<span class="ds-customization__update-title">' + title + '</span>';

                    html += '<li class="ds-customization__update-item">' +
                        '<span class="ds-customization__update-title-group">' + titleHtml + badge + '</span>' +
                        '<span class="ds-customization__update-date">' + date + '</span>' +
                        '</li>';
                });

                listArea.innerHTML = html;
            })
            .catch(function () {
                var listArea = document.getElementById('updateListArea');
                if (listArea) {
                    listArea.innerHTML = '<li class="ds-customization__update-item" style="color:var(--gray-400);font-size:var(--font-size-xs,12px);">데이터를 불러올 수 없습니다.</li>';
                }
            });
    }

    // =========================================================================
    // 에러 배너 (Story 7.2 — UX스펙 §2, PRD §5.1)
    // =========================================================================
    function loadErrorBanner() {
        var bannerEl = document.getElementById('errorBanner');
        if (!bannerEl) return;

        fetch('/development/error_log_ps.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'mode=count'
        })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                if (!json.success || !json.data) {
                    bannerEl.style.display = 'none';
                    return;
                }

                var count = parseInt(json.data.count, 10) || 0;
                if (count > 0) {
                    var titleEl = bannerEl.querySelector('.ncua-message-notification__title');
                    if (titleEl) {
                        titleEl.textContent = '에러 ' + count + '건이 발생했습니다.';
                    }
                    bannerEl.style.display = '';
                } else {
                    bannerEl.style.display = 'none';
                }
            })
            .catch(function () {
                bannerEl.style.display = 'none';
            });
    }

    // 에러 배너 클릭 → 읽음 처리 후 에러 로그 보기 이동
    var errorBannerEl = document.getElementById('errorBanner');
    if (errorBannerEl) {
        errorBannerEl.addEventListener('click', function () {
            fetch('/development/error_log_ps.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'mode=dismiss'
            }).finally(function () {
                window.location.href = '/development/error_log.php';
            });
        });
    }

    // =========================================================================
    // 카드 + 업데이트 노트 병렬 로딩 시작
    // =========================================================================
    loadErrorBanner();
    loadSystemVersion();
    loadCustomizationSummary();
    if (isStaging) {
        loadDeployStatus();
    } else {
        loadStagingInfo();
    }
    loadUpdateNote();

    // =========================================================================
    // 소스 검증 버튼 (AC3)
    // =========================================================================
    var btnVerification = document.getElementById('btnVerification');
    if (btnVerification) {
        btnVerification.addEventListener('click', function () {
            var url = this.getAttribute('data-url');
            if (url) window.location.href = url;
        });
    }

    // =========================================================================
    // 스테이징 서버 신청 모달 (Story 1B.3)
    // =========================================================================

    function showStagingErrorModal(data) {
        var modalEl = document.getElementById('modalStagingError');
        if (!modalEl) return;

        document.getElementById('stagingErrorTitle').textContent = data.title || '';
        document.getElementById('stagingErrorBody').textContent = data.body || '';

        var reasonEl = document.getElementById('stagingErrorReason');
        if (data.reason || data.tables) {
            document.getElementById('stagingErrorReasonText').textContent = data.reason || '';
            document.getElementById('stagingErrorTables').textContent = data.tables || '';
            reasonEl.style.display = '';
        } else {
            reasonEl.style.display = 'none';
        }

        var faqLinkEl = document.getElementById('stagingErrorFaqLink');
        var faqUrlEl = document.getElementById('stagingErrorFaqUrl');
        if (faqLinkEl && faqUrlEl) {
            if (data.faqUrl) {
                faqUrlEl.href = data.faqUrl;
                faqLinkEl.style.display = '';
            } else {
                faqLinkEl.style.display = 'none';
            }
        }

        var backdrop = modalEl.querySelector('.ncua-modal-backdrop');
        modalEl.style.display = 'block';
        if (backdrop) backdrop.style.display = '';
    }

    window.closeStagingErrorModal = function () {
        var el = document.getElementById('modalStagingError');
        if (!el) return;
        var backdrop = el.querySelector('.ncua-modal-backdrop');
        el.style.display = 'none';
        if (backdrop) backdrop.style.display = 'none';
    };

    function openStagingApplyModal(terms) {
        var modalEl = document.getElementById('modalStagingApply');
        if (!modalEl) return;

        var termsEl = document.getElementById('stagingApplyTerms');
        if (termsEl && terms) termsEl.innerHTML = terms;

        var chk = document.getElementById('stagingAgreeChk');
        if (chk) chk.checked = false;

        var btn = document.getElementById('stagingApplyBtn');
        if (btn) btn.disabled = true;

        var backdrop = modalEl.querySelector('.ncua-modal-backdrop');
        modalEl.style.display = 'block';
        if (backdrop) backdrop.style.display = '';
    }

    window.closeStagingApplyModal = function () {
        var el = document.getElementById('modalStagingApply');
        if (!el) return;
        var backdrop = el.querySelector('.ncua-modal-backdrop');
        el.style.display = 'none';
        if (backdrop) backdrop.style.display = 'none';
    };

    window.submitStagingApply = function () {
        var chk = document.getElementById('stagingAgreeChk');
        if (!chk || !chk.checked) return;

        var applyUrl = (document.getElementById('stagingBtn') || {}).getAttribute
            ? document.getElementById('stagingBtn').getAttribute('data-apply-url')
            : '/development/staging_ps.php';

        applyUrl = applyUrl || '/development/staging_ps.php';

        fetch(applyUrl, { method: 'POST' })
            .then(function (res) {
                if (!res.ok) { return Promise.reject(new Error('HTTP ' + res.status)); }
                return res.json();
            })
            .then(function (data) {
                window.closeStagingApplyModal();
                if (data.success) {
                    if (typeof NCDSToast === 'function') {
                        try {
                            NCDSToast({
                                message: '스테이징 서버 신청이 완료되었습니다.',
                                subMessage: '생성 완료 후에는 개발 담당자에게 안내해 드립니다. 최대 1시간까지 소요될 수 있습니다.',
                                color: 'success'
                            });
                        } catch (e) { console.error(e); }
                    } else {
                        alert('스테이징 서버 신청이 완료되었습니다.\n생성 완료 후에는 개발 담당자에게 안내해 드립니다.\n최대 1시간까지 소요될 수 있습니다.');
                    }
                    setTimeout(function () { location.reload(); }, 3500);
                } else {
                    if (typeof NCDSToast === 'function') {
                        try { NCDSToast({ message: data.message || '스테이징 서버 신청에 실패했습니다. 관리자에게 문의하세요.', color: 'error' }); } catch (e) { console.error(e); }
                    } else {
                        alert(data.message || '스테이징 서버 신청에 실패했습니다. 관리자에게 문의하세요.');
                    }
                }
            })
            .catch(function (err) {
                window.closeStagingApplyModal();
                console.error('[staging apply error]', err);
                if (typeof NCDSToast === 'function') {
                    try { NCDSToast({ message: '신청 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.', color: 'error' }); } catch (e) { console.error(e); }
                } else {
                    alert('신청 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.');
                }
            });
    };

    function validateStagingRequest() {
        var validationUrl = document.getElementById('stagingBtn')
            ? document.getElementById('stagingBtn').getAttribute('data-validate-url')
            : null;

        if (!validationUrl) {
            return Promise.resolve({ success: true, terms: '' });
        }

        return fetch(validationUrl, { method: 'POST' })
            .then(function (res) { return res.json(); })
            .catch(function () {
                return {
                    success: false,
                    errorType: '',
                    title: '사전 검증 중 오류가 발생했습니다.',
                    body: '잠시 후 다시 시도해 주세요.',
                };
            });
    }

    function bindStagingApplyButton() {
        var btnStaging = document.getElementById('stagingBtn');
        if (btnStaging && btnStaging.tagName === 'BUTTON') {
            btnStaging.addEventListener('click', function () {
                validateStagingRequest().then(function (data) {
                    if (data.success) {
                        openStagingApplyModal(data.terms);
                    } else {
                        showStagingErrorModal(data);
                    }
                });
            });
        }
    }

    // 체크박스 토글
    document.addEventListener('change', function (e) {
        if (e.target && e.target.id === 'stagingAgreeChk') {
            var btn = document.getElementById('stagingApplyBtn');
            if (btn) btn.disabled = !e.target.checked;
        }
    });

    // 모달 backdrop 클릭 시 닫기
    document.addEventListener('click', function (e) {
        if (!e.target.classList.contains('ncua-modal-backdrop')) return;

        var errorModal = document.getElementById('modalStagingError');
        if (errorModal && errorModal.contains(e.target)) window.closeStagingErrorModal();

        var applyModal = document.getElementById('modalStagingApply');
        if (applyModal && applyModal.contains(e.target)) window.closeStagingApplyModal();
    });

})();
