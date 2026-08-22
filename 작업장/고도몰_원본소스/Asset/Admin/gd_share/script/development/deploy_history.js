/**
 * 배포 내역 페이지 JavaScript
 * Story 2A.5 / Story 8.36
 *
 * 와이어프레임 §14 기준:
 * - [상세] 버튼 클릭 → 배포 상세 모달(560px) 오픈 (Story 8.36: 아코디언 제거)
 * - 액션 컬럼: 직전 COMPLETED 1건에만 [롤백] 표시
 * - 파일 목록: 추가/수정/삭제 그룹 헤더 + 파일 경로
 *
 * @copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 */
(function () {
    'use strict';

    var STATE = {
        page: 1,
        pageSize: 10,
        totalCount: 0,
        histories: []
    };

    /* ── 초기화 ── */
    function init() {
        bindEvents();
        loadDeployHistory(1);
    }

    /* ── 이벤트 바인딩 ── */
    function bindEvents() {
        document.addEventListener('click', function (e) {
            /* [상세] 버튼 → 배포 상세 모달 오픈 */
            var detailBtn = e.target.closest('[data-action="showDetail"]');
            if (detailBtn) {
                e.stopPropagation();
                openDeployDetailModal(detailBtn.getAttribute('data-deploy-id'));
                return;
            }

            /* 롤백 버튼 */
            var rollbackBtn = e.target.closest('[data-action="showRollbackConfirm"]');
            if (rollbackBtn) {
                e.stopPropagation();
                showRollbackConfirm();
                return;
            }

            /* 목록 페이지네이션 */
            var pageBtn = e.target.closest('#paginationWrap [data-page]');
            if (pageBtn) {
                loadDeployHistory(parseInt(pageBtn.getAttribute('data-page'), 10));
            }

            /* 모달 내 파일 페이지 버튼 */
            var filePageBtn = e.target.closest('[data-action="filePageChange"]');
            if (filePageBtn) {
                e.stopPropagation();
                loadFileChanges(
                    filePageBtn.getAttribute('data-deploy-id'),
                    parseInt(filePageBtn.getAttribute('data-file-page'), 10)
                );
                return;
            }
        });

        /* ESC 키로 모달 닫기 */
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeDeployDetailModal();
        });
    }

    /* ── AJAX: 배포 내역 목록 조회 ── */
    function loadDeployHistory(page) {
        STATE.page = page;

        ajaxPost('./deploy_history_ps.php', buildParams({
            siteKey: SITE_KEY,
            mode: 'list',
            page: page,
            pageSize: STATE.pageSize
        }), function (data) {
            STATE.histories = data.histories || [];
            STATE.totalCount = data.totalCount || 0;
            renderTable();
            renderSearchSummary();
            renderPagination();
        }, function () {
            renderEmpty();
        });
    }

    /* ── 테이블 렌더링 ── */
    function renderTable() {
        var tbody = document.getElementById('deployHistoryBody');
        var tableWrap = document.getElementById('tableWrap');

        if (!STATE.histories.length) {
            renderEmpty();
            return;
        }

        tableWrap.style.display = '';
        var emptyState = document.getElementById('deployEmptyState');
        if (emptyState) emptyState.style.display = 'none';

        var html = '';

        for (var i = 0; i < STATE.histories.length; i++) {
            var h = STATE.histories[i];
            var id = h.deployId;
            var deployDate = formatDate(h.createdDateTime || h.createdAt || '');
            var deployer = h.managerName || h.managerId || '-';
            var version = h.currentTagLabel || h.tuningTagName || '-';
            var rawStatus = h.rawDeployStatus || '';
            var statusLabel = h.deployStatus || '-';
            var isRollback = h.isRollback || 'n';

            /* 액션: [확인하기] xxs (모든 행) + [롤백] xxs destructive (최신 1건이 배포 성공일 때만) */
            var actionHtml = '<button class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray" data-action="showDetail" data-deploy-id="' + escapeAttr(id) + '"><span class="ncua-btn__label">확인하기</span></button>';

            if (i === 0 && rawStatus === 'COMPLETED' && isRollback !== 'y' && STATE.page === 1) {
                actionHtml += ' <button class="ncua-btn ncua-btn--xxs ncua-btn--destructive" data-action="showRollbackConfirm"><span class="ncua-btn__label">롤백</span></button>';
            }

            html += '<tr class="ds-deploy-history__row" data-deploy-id="' + escapeAttr(id) + '">' +
                '<td><div>' + deployDate + '</div></td>' +
                '<td><div>' + escapeHtml(deployer) + '</div></td>' +
                '<td><div><code class="deploy-history__version-code">' + escapeHtml(version) + '</code></div></td>' +
                '<td style="text-align:center;"><div>' + renderStatusBadge(statusLabel, rawStatus, isRollback) + '</div></td>' +
                '<td style="text-align:center;"><div>' + actionHtml + '</div></td>' +
                '</tr>';
        }

        tbody.innerHTML = html;
    }

    /* ── 상태 Badge (DES-SPEC-033 xs pill-outline) ── */
    function renderStatusBadge(label, rawStatus, isRollback) {
        var color = 'neutral';
        var leadingIcon = '';
        if (rawStatus === 'COMPLETED' && isRollback === 'y') {
            color = 'neutral';
        } else if (rawStatus === 'COMPLETED') {
            color = 'success';
        } else if (rawStatus === 'FAILED') {
            color = 'error';
        } else if (rawStatus === 'REQUESTED' || rawStatus === 'IN_PROGRESS' || rawStatus === 'PROCESSING') {
            color = 'blue';
            leadingIcon = '<svg class="ds-deploy-history__spinner-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>';
        }
        return '<span class="ncua-badge ncua-badge--pill-outline ncua-badge--xs ncua-badge--' + color + '">' +
            leadingIcon +
            '<span class="ncua-badge__label">' + escapeHtml(label) + '</span>' +
            '</span>';
    }

    /* ── 배포 상세 모달 오픈 (와이어프레임 §14-a-3) ── */
    function openDeployDetailModal(deployId) {
        var modalWrapper = document.getElementById('deployDetailModal');
        if (!modalWrapper) return;

        /* 배포 정보 영역: STATE에서 찾기 */
        var h = null;
        for (var i = 0; i < STATE.histories.length; i++) {
            if (String(STATE.histories[i].deployId) === String(deployId)) {
                h = STATE.histories[i];
                break;
            }
        }

        var infoEl = modalWrapper.querySelector('#deployDetailInfo');
        if (infoEl && h) {
            var deployDate = formatDate(h.createdDateTime || h.createdAt || '');
            var deployer = h.managerName || h.managerId || '-';
            var version = h.currentTagLabel || h.tuningTagName || '-';
            var rawStatus = h.rawDeployStatus || '';
            var statusLabel = h.deployStatus || '-';
            var isRollback = h.isRollback || 'n';
            infoEl.innerHTML =
                '<dl class="ds-deploy-history__detail-dl">' +
                '<div class="ds-deploy-history__detail-row"><dt>배포일</dt><dd>' + escapeHtml(deployDate) + '</dd></div>' +
                '<div class="ds-deploy-history__detail-row"><dt>배포자</dt><dd>' + escapeHtml(deployer) + '</dd></div>' +
                '<div class="ds-deploy-history__detail-row"><dt>버전명</dt><dd><code class="deploy-history__version-code">' + escapeHtml(version) + '</code></dd></div>' +
                '<div class="ds-deploy-history__detail-row"><dt>상태</dt><dd>' + renderStatusBadge(statusLabel, rawStatus, isRollback) + '</dd></div>' +
                '</dl>';
        } else if (infoEl) {
            infoEl.innerHTML = '<p class="ds-deploy-history__file-empty">배포 정보를 불러올 수 없습니다.</p>';
        }

        /* 파일 목록 로딩 */
        var filesEl = modalWrapper.querySelector('#deployDetailFiles');
        if (filesEl) {
            filesEl.innerHTML = '<div class="ds-deploy-history__detail-loading">불러오는 중...</div>';
        }

        /* 모달 표시 (backdrop 클릭 시 닫기) */
        modalWrapper.onclick = function (e) {
            if (e.target === modalWrapper) closeDeployDetailModal();
        };
        modalWrapper.style.display = 'flex';

        /* 파일 변경 목록 조회 */
        loadFileChanges(deployId, 1);
    }

    /* ── 배포 상세 모달 닫기 ── */
    function closeDeployDetailModal() {
        var modalWrapper = document.getElementById('deployDetailModal');
        if (!modalWrapper) return;
        modalWrapper.style.display = 'none';
    }

    /* ── AJAX: 파일 변경 목록 ── */
    function loadFileChanges(deployId, filePage) {
        ajaxPost('./deploy_history_ps.php', buildParams({
            siteKey: SITE_KEY,
            mode: 'files',
            deployId: deployId,
            filePage: filePage
        }), function (data) {
            renderModalFileContent(deployId, data, filePage);
        }, function (err) {
            renderModalFileError(err);
        });
    }

    /* ── 모달 파일 목록 렌더링 (와이어프레임 §14-a-3) ── */
    function renderModalFileContent(deployId, data, filePage) {
        var modalWrapper = document.getElementById('deployDetailModal');
        if (!modalWrapper) return;

        var filesEl = modalWrapper.querySelector('#deployDetailFiles');
        if (!filesEl) return;

        var files      = data.files      || [];
        var addCount     = data.addCount     || 0;
        var modCount     = data.modCount     || 0;
        var delCount     = data.delCount     || 0;
        var renamedCount = data.renamedCount || 0;
        var totalCount   = data.totalCount   || 0;
        var pageSize   = data.pageSize   || 10;

        var html = '<p class="ds-deploy-history__detail-file-title">변경 파일 목록</p>';

        if (!data.success || (!addCount && !modCount && !delCount && !renamedCount)) {
            html += '<p class="ds-deploy-history__file-empty">배포된 파일이 없습니다.</p>';
        } else {
            /* 파일을 changeType별로 미리 분류 */
            var filesByType = {};
            for (var fi2 = 0; fi2 < files.length; fi2++) {
                var ft = files[fi2].changeType;
                if (!filesByType[ft]) filesByType[ft] = [];
                filesByType[ft].push(files[fi2]);
            }

            var groups = [
                { type: 'A', label: 'ADD',    labelKo: '추가',       count: addCount,     badgeColor: 'success' },
                { type: 'M', label: 'MOD',    labelKo: '수정',       count: modCount,     badgeColor: 'blue' },
                { type: 'D', label: 'DEL',    labelKo: '삭제',       count: delCount,     badgeColor: 'error' },
                { type: 'R', label: 'RENAME', labelKo: '이름 변경', count: renamedCount, badgeColor: 'blue' }
            ];

            html += '<ul class="ds-deploy-history__group-list">';
            for (var g = 0; g < groups.length; g++) {
                var grp = groups[g];
                if (!grp.count) continue;

                var grpFiles = filesByType[grp.type] || [];

                html += '<li class="ds-deploy-history__group">' +
                    '<div class="ds-deploy-history__group-label">' +
                    '<span class="ncua-badge ncua-badge--pill-dark-color ncua-badge--' + grp.badgeColor + ' ncua-badge--xs"><span class="ncua-badge__label">' + grp.label + '</span></span> ' +
                    grp.labelKo + ' (' + grp.count + '건)</div>';

                html += '<ul class="ds-deploy-history__file-list">';
                for (var fi = 0; fi < grpFiles.length; fi++) {
                    html += '<li class="deploy-detail__file-path">' +
                        escapeHtml(grpFiles[fi].filePath) + '</li>';
                }
                html += '</ul></li>';
            }
            html += '</ul>';

            /* 파일 페이지네이션 */
            if (totalCount > pageSize) {
                html += renderFilePagination(deployId, totalCount, filePage, pageSize);
            }
        }

        filesEl.innerHTML = html;
    }

    function renderModalFileError(err) {
        var modalWrapper = document.getElementById('deployDetailModal');
        if (!modalWrapper) return;
        var filesEl = modalWrapper.querySelector('#deployDetailFiles');
        if (filesEl) {
            filesEl.innerHTML = '<p class="ds-deploy-history__file-empty">' + escapeHtml(err || '조회에 실패했습니다.') + '</p>';
        }
    }

    /* ── 파일 페이지네이션 ── */
    function renderFilePagination(deployId, totalCount, current, pageSize) {
        var totalPages = Math.ceil(totalCount / pageSize);
        var startPage = Math.max(1, current - 4);
        var endPage   = Math.min(totalPages, startPage + 9);
        if (endPage - startPage < 9) startPage = Math.max(1, endPage - 9);

        var html = '<div class="ds-deploy-history__file-pagination ncua-pagination ncua-pagination--pc">' +
            '<ul class="ncua-pagination__list">';
        for (var p = startPage; p <= endPage; p++) {
            html += '<li class="ncua-pagination__item">' +
                '<button class="ncua-pagination__page-num' + (p === current ? ' is-current' : '') + '"' +
                ' data-action="filePageChange" data-deploy-id="' + escapeAttr(deployId) + '" data-file-page="' + p + '">' +
                p + '</button></li>';
        }
        html += '</ul>' +
            '<p class="ncua-pagination__page-info"><em class="ncua-pagination__current-num">' + current + '</em> / ' + totalPages + '</p>' +
            '</div>';
        return html;
    }

    /* ── 검색 건수 ── */
    function renderSearchSummary() {
        var el = document.getElementById('totalCount');
        if (el) el.textContent = String(STATE.totalCount);
    }

    /* ── 빈 상태 (DES-SPEC-037 — thead 유지, tbody 하단 EmptyState 표시) ── */
    function renderEmpty() {
        var tbody      = document.getElementById('deployHistoryBody');
        var tableWrap  = document.getElementById('tableWrap');
        var emptyState = document.getElementById('deployEmptyState');
        tableWrap.style.display = '';
        tbody.innerHTML = '';
        if (emptyState) emptyState.style.display = '';
        document.getElementById('paginationWrap').innerHTML = '';
        document.getElementById('totalCount').textContent = '0';
    }

    /* ── 목록 페이지네이션 ── */
    function renderPagination() {
        var wrap = document.getElementById('paginationWrap');
        if (STATE.totalCount <= STATE.pageSize) {
            wrap.innerHTML = '';
            return;
        }

        var totalPages = Math.ceil(STATE.totalCount / STATE.pageSize);
        var current    = STATE.page;
        var startPage  = Math.max(1, current - 4);
        var endPage    = Math.min(totalPages, startPage + 9);
        if (endPage - startPage < 9) startPage = Math.max(1, endPage - 9);

        var html = '<div class="ncua-pagination ncua-pagination--pc"><ul class="ncua-pagination__list">';
        for (var p = startPage; p <= endPage; p++) {
            html += '<li class="ncua-pagination__item">' +
                '<button class="ncua-pagination__page-num' + (p === current ? ' is-current' : '') + '" data-page="' + p + '">' + p + '</button></li>';
        }
        html += '</ul><p class="ncua-pagination__page-info"><em class="ncua-pagination__current-num">' +
            current + '</em> / ' + totalPages + '</p></div>';

        wrap.innerHTML = html;
    }

    /* ── 롤백 확인 모달 ── */
    function showRollbackConfirm() {
        NCDSConfirm({
            message: '롤백 확인',
            subMessage: '롤백을 실행하면 배포된 소스가 이전 상태로 복원됩니다.<br>이 작업은 되돌릴 수 없습니다. 계속하시겠습니까?',
            btnText: { confirmLabel: '롤백 실행', cancelLabel: '취소' },
            confirmHierarchy: 'destructive',
            callback: function (result) {
                if (result) window.location.href = 'deploy.php?rollback=true';
            }
        });
    }

    /* ── 유틸 ── */
    function formatDate(dateStr) {
        if (!dateStr) return '-';
        var d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        return d.getFullYear() + '-' +
            String(d.getMonth() + 1).padStart(2, '0') + '-' +
            String(d.getDate()).padStart(2, '0') + ' ' +
            String(d.getHours()).padStart(2, '0') + ':' +
            String(d.getMinutes()).padStart(2, '0');
    }

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(String(str)));
        return div.innerHTML;
    }

    function escapeAttr(str) {
        if (!str) return '';
        return String(str).replace(/"/g, '&quot;');
    }

    function ajaxPost(url, data, onSuccess, onError) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;
            try {
                var res = JSON.parse(xhr.responseText);
                if (res.success) onSuccess(res.data);
                else onError(res.error || '오류가 발생했습니다.');
            } catch (e) { onError('서버 응답을 처리할 수 없습니다.'); }
        };
        xhr.send(data);
    }

    function buildParams(params) {
        var parts = [];
        for (var key in params) {
            if (params.hasOwnProperty(key)) {
                parts.push(encodeURIComponent(key) + '=' + encodeURIComponent(params[key]));
            }
        }
        return parts.join('&');
    }

    /* ── 외부 노출 ── */
    window.__deployHistory = {
        openDeployDetailModal: openDeployDetailModal,
        closeDeployDetailModal: closeDeployDetailModal
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
