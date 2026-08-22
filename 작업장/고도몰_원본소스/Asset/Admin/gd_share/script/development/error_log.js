/**
 * 에러 로그 보기 페이지 JavaScript
 * - loadErrorLogs(page): ajax로 에러 로그 목록 조회 (SelectBox 종류 필터 연동)
 * - 행 클릭 → 에러 상세 모달 표시 (wireframe 4-a-3)
 */
(function () {
    'use strict';

    var currentPage = 1;
    var currentType = '';
    var totalPageCount = 1;
    var PAGE_GROUP_SIZE = 10;

    // =========================================================================
    // 헬퍼
    // =========================================================================

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // =========================================================================
    // SelectBox 초기화
    // =========================================================================

    var selectBoxEl = document.querySelector('#errorTypeFilter .ncua-selectbox');
    var selectBoxInstance = null;

    if (selectBoxEl && window.ncua && window.ncua.SelectBox) {
        selectBoxInstance = new window.ncua.SelectBox(selectBoxEl, {
            options: [
                { id: '', text: '전체 종류' },
                { id: 'WARNING', text: 'WARNING' },
                { id: 'ERROR', text: 'ERROR' },
                { id: 'CRITICAL', text: 'CRITICAL' },
                { id: 'ALERT', text: 'ALERT' },
                { id: 'EMERGENCY', text: 'EMERGENCY' }
            ],
            value: '',
            size: 'xs',
            onChange: function (value) {
                currentType = value || '';
                currentPage = 1;
                loadErrorLogs(1);
            }
        });
    }

    // =========================================================================
    // Badge HTML 생성 (DES-SPEC-033 pill-outline)
    // =========================================================================

    function buildBadgeHtml(logLevel) {
        var label = escapeHtml(logLevel);
        var color = 'neutral';

        if (logLevel === 'EMERGENCY' || logLevel === 'ERROR') {
            color = 'error';
        } else if (logLevel === 'ALERT') {
            color = 'pink';
        } else if (logLevel === 'CRITICAL' || logLevel === 'WARNING') {
            color = 'warning';
        }

        return '<span class="ncua-badge ncua-badge--pill-outline ncua-badge--' + color + ' ncua-badge--xs">' +
            '<span class="ncua-badge__label">' + label + '</span></span>';
    }

    // =========================================================================
    // 행 렌더링
    // =========================================================================

    function renderRow(item, index) {
        var rowId = 'row-' + currentPage + '-' + index;
        var timestamp = escapeHtml(item.timestamp || '');
        var errorType = item.logLevel || '';
        var message = escapeHtml(item.body || '');
        var file = escapeHtml(item.errorFile || '');
        var line = escapeHtml(String(item.longErrorLine || ''));

        var itemJson = JSON.stringify(item).replace(/&/g, '&amp;').replace(/"/g, '&quot;');

        return '<tr class="ds-error-log__row" data-row-id="' + rowId + '" data-error-item="' + itemJson + '" onclick="window.__errorLog.openDetailModal(this)">' +
            '<td>' + timestamp + '</td>' +
            '<td style="text-align:center">' + buildBadgeHtml(errorType) + '</td>' +
            '<td><span class="ds-error-log__message-text" title="' + message + '">' + message + '</span></td>' +
            '<td><span class="ds-error-log__file-cell" title="' + file + '">' + file + '</span></td>' +
            '<td style="text-align:right">' + line + '</td>' +
            '<td style="text-align:center"><button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray" onclick="event.stopPropagation();window.__errorLog.openDetailModal(this.closest(\'tr\'))"><span class="ncua-btn__label">확인하기</span></button></td>' +
            '</tr>';
    }

    // =========================================================================
    // 에러 로그 로드
    // =========================================================================

    function loadErrorLogs(page) {
        currentPage = page || 1;

        var formData = new FormData();
        formData.append('page', currentPage);
        if (currentType) {
            formData.append('logLevel', currentType);
        }

        fetch('/development/error_log_ps.php', {
            method: 'POST',
            body: formData
        })
        .then(function (res) { return res.json(); })
        .then(function (json) {
            if (!json.success) {
                renderEmpty();
                return;
            }

            var data = json.data || {};
            var items = data.items || [];
            var totalCount = data.totalCount || 0;
            var searchCount = data.searchCount || items.length;
            var pageCount = data.pageCount || 1;
            totalPageCount = pageCount;

            var totalEl = document.getElementById('totalCount');
            var searchEl = document.getElementById('searchCount');
            if (totalEl) totalEl.textContent = totalCount;
            if (searchEl) searchEl.textContent = searchCount;

            var tbody = document.getElementById('errorLogBody');
            if (!tbody) return;

            if (items.length === 0) {
                renderEmpty();
                return;
            }

            var emptyEl = document.getElementById('errorLogEmpty');
            if (emptyEl) emptyEl.style.display = 'none';

            var html = '';
            for (var i = 0; i < items.length; i++) {
                html += renderRow(items[i], i);
            }
            tbody.innerHTML = html;

            renderPagination(currentPage, pageCount);
        })
        .catch(function () {
            renderEmpty();
        });
    }

    function renderEmpty() {
        var tbody = document.getElementById('errorLogBody');
        if (tbody) tbody.innerHTML = '';
        var emptyEl = document.getElementById('errorLogEmpty');
        if (emptyEl) emptyEl.style.display = '';
        var paginationEl = document.getElementById('errorLogPagination');
        if (paginationEl) paginationEl.innerHTML = '';
    }

    // =========================================================================
    // 페이지네이션 렌더링
    // =========================================================================

    function renderPagination(current, total) {
        var paginationEl = document.getElementById('errorLogPagination');
        if (!paginationEl || total <= 1) {
            if (paginationEl) paginationEl.innerHTML = '';
            return;
        }

        var groupStart = Math.floor((current - 1) / PAGE_GROUP_SIZE) * PAGE_GROUP_SIZE + 1;
        var groupEnd = Math.min(groupStart + PAGE_GROUP_SIZE - 1, total);
        var isFirstGroup = groupStart === 1;
        var isLastGroup = groupEnd === total;

        var html = '<div class="ncua-pagination ncua-pagination--pc">';

        html += buildNavBtnHtml('first', isFirstGroup, 'm18 17-5-5 5-5m-7 10-5-5 5-5');
        html += buildNavBtnHtml('prev', isFirstGroup, 'm15 18-6-6 6-6');

        html += '<ul class="ncua-pagination__list">';
        for (var i = groupStart; i <= groupEnd; i++) {
            var activeClass = (i === current) ? ' is-current' : '';
            html += '<li class="ncua-pagination__item">' +
                '<button class="ncua-pagination__page-num' + activeClass + '">' + i + '</button></li>';
        }
        html += '</ul>';

        html += '<p class="ncua-pagination__page-info">' +
            '<em class="ncua-pagination__current-num">' + current + '</em> / ' + total + '</p>';

        html += buildNavBtnHtml('next', isLastGroup, 'm9 18 6-6-6-6');
        html += buildNavBtnHtml('last', isLastGroup, 'm6 17 5-5-5-5m7 10 5-5-5-5');

        html += '</div>';

        paginationEl.innerHTML = html;
    }

    function buildNavBtnHtml(type, disabled, iconPath) {
        var disClass = disabled ? ' is-disable' : '';
        var disAttr = disabled ? ' disabled' : '';
        var iconColor = disabled ? '#D0D5DD' : '#0C111D';
        return '<button class="ncua-btn ncua-btn--xs only-icon' + disClass +
            ' ncua-pagination__' + type + ' ncua-btn--secondary-gray"' + disAttr + '>' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="none" color="' + iconColor + '">' +
            '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' + iconPath + '"></path></svg></button>';
    }

    var paginationRoot = document.getElementById('errorLogPagination');
    if (paginationRoot) {
        paginationRoot.addEventListener('click', function (e) {
            var btn = e.target.closest('button');
            if (!btn || btn.disabled || btn.classList.contains('is-disable')) return;

            if (btn.classList.contains('ncua-pagination__page-num')) {
                loadErrorLogs(parseInt(btn.textContent, 10));
                return;
            }

            var groupStart = Math.floor((currentPage - 1) / PAGE_GROUP_SIZE) * PAGE_GROUP_SIZE + 1;

            if (btn.classList.contains('ncua-pagination__first')) {
                loadErrorLogs(1);
            } else if (btn.classList.contains('ncua-pagination__prev')) {
                loadErrorLogs(groupStart - PAGE_GROUP_SIZE);
            } else if (btn.classList.contains('ncua-pagination__next')) {
                loadErrorLogs(groupStart + PAGE_GROUP_SIZE);
            } else if (btn.classList.contains('ncua-pagination__last')) {
                loadErrorLogs(totalPageCount);
            }
        });
    }

    // =========================================================================
    // 에러 상세 모달 (wireframe 15-e)
    // =========================================================================

    var selectedRow = null;

    function openDetailModal(rowEl) {
        var raw = rowEl.getAttribute('data-error-item');
        if (!raw) return;
        var item;
        try {
            item = JSON.parse(raw);
        } catch (e) {
            return;
        }

        // 선택 행 하이라이트
        if (selectedRow) selectedRow.classList.remove('ds-error-log__row--selected');
        rowEl.classList.add('ds-error-log__row--selected');
        selectedRow = rowEl;

        var modalWrapper = document.getElementById('errorDetailModal');
        if (!modalWrapper) return;

        // 에러 내용 (코드 블록)
        var msgEl = modalWrapper.querySelector('#errorDetailMessage');
        if (msgEl) {
            var errorText = item.body || '';
            if (item.stackTrace) {
                errorText += '\n' + item.stackTrace;
            }
            msgEl.textContent = errorText;
        }

        // Request 정보 (코드 블록 — URL + 발생 시각 + IP)
        var reqEl = modalWrapper.querySelector('#errorDetailRequest');
        if (reqEl) {
            var lines = [];
            lines.push('URL: ' + (item.requestUrl || '-'));
            lines.push('발생 시각: ' + (item.timestamp || '-'));
            reqEl.textContent = lines.join('\n');
        }

        var backdrop = modalWrapper.querySelector('.ncua-modal-backdrop');
        if (backdrop) {
            backdrop.style.display = '';
            backdrop.onclick = function (e) {
                if (e.target === backdrop) closeDetailModal();
            };
        }

        modalWrapper.style.display = 'block';
    }

    function closeDetailModal() {
        var modalWrapper = document.getElementById('errorDetailModal');
        if (modalWrapper) {
            var backdrop = modalWrapper.querySelector('.ncua-modal-backdrop');
            if (backdrop) backdrop.style.display = 'none';
            modalWrapper.style.display = 'none';
        }
        if (selectedRow) {
            selectedRow.classList.remove('ds-error-log__row--selected');
            selectedRow = null;
        }
    }

    // ESC 키로 모달 닫기
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            var modalWrapper = document.getElementById('errorDetailModal');
            if (modalWrapper && modalWrapper.style.display !== 'none') {
                closeDetailModal();
            }
        }
    });

    // =========================================================================
    // 전역 인터페이스
    // =========================================================================

    window.__errorLog = {
        loadErrorLogs: loadErrorLogs,
        openDetailModal: openDetailModal,
        closeDetailModal: closeDetailModal
    };

    loadErrorLogs(1);

})();
