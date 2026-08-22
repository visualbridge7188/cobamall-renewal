
/**
 * 선택/삭제/체크 기능
 * @param {Object} config - 설정 객체
 * @param {string} config.tableBodyId - tbody id
 * @param {string} config.deleteBtnId - 삭제 버튼 id
 * @param {string} config.allCheckId - 전체 체크박스 id
 * @param {string} [config.noDataMessage] - 데이터가 없을 때 표시할 메시지
 * @param {number} [config.noDataColspan=3] - no-data 행의 colspan 값
 * @param {string} [config.checkboxSelector='input[type="checkbox"]'] - 체크박스 셀렉터
 */
function createCheckboxManager(config) {
    const {
        tableBodyId,
        deleteBtnId,
        allCheckId,
        noDataMessage = '',
        noDataColspan = 3,
        checkboxSelector = 'input[type="checkbox"]'
    } = config;
    
    return {
        _checkedIds: [],
        selectors: {
            tableBodyId,
            deleteBtnId,
            allCheckId,
            checkboxSelector
        },
        noDataConfig: {
            message: noDataMessage,
            colspan: noDataColspan
        },
        init() {
            this._bindCheckboxEvents();
            this._bindDeleteEvent();
            this._bindAllCheckEvent();
            this._updateDeleteButtonState(); // 초기 로딩 시 무조건 disabled
        },
        _getTableBody() {
            return document.getElementById(this.selectors.tableBodyId);
        },
        _getAllCheck() {
            return document.getElementById(this.selectors.allCheckId);
        },
        _getDeleteBtn() {
            return document.getElementById(this.selectors.deleteBtnId);
        },
        _getCheckboxes() {
            const tbody = this._getTableBody();
            if (!tbody) return [];
            return Array.from(tbody.querySelectorAll(this.selectors.checkboxSelector));
        },
        _bindCheckboxEvents() {
            const tbody = this._getTableBody();
            if (!tbody) return;
            tbody.addEventListener('change', (event) => this._handleCheckboxChange(event));
        },
        _bindDeleteEvent() {
            const btn = this._getDeleteBtn();
            if (!btn) return;
            btn.addEventListener('click', () => this.deleteSelected());
        },
        _handleCheckboxChange(event) {
            const target = event.target;
            if (target.type !== 'checkbox') return;
            const tr = target.closest('tr');
            const id = tr?.id;
            if (!id) return;
            const allCheck = this._getAllCheck();
            if (target === allCheck) return; // 전체 체크박스는 별도 처리
            if (target.checked) {
                if (!this._checkedIds.includes(id)) this._checkedIds.push(id);
            } else {
                this._checkedIds = this._checkedIds.filter((v) => v !== id);
            }
            this._syncAllCheckState();
            this._updateDeleteButtonState();
        },
        _syncAllCheckState() {
            const allCheck = this._getAllCheck();
            const checkboxes = this._getCheckboxes();
            const checkedCount = checkboxes.filter(cb => cb !== allCheck && cb.checked).length;
            const totalCount = checkboxes.filter(cb => cb !== allCheck).length;
            if (allCheck) {
                allCheck.checked = checkedCount > 0 && checkedCount === totalCount;
            }
        },
        _bindAllCheckEvent() {
            const allCheck = this._getAllCheck();
            if (!allCheck) return;
            allCheck.addEventListener('change', (event) => {
                const { checked } = event.target;
                const checkboxes = this._getCheckboxes();
                checkboxes.forEach((cb) => {
                    if (cb === allCheck) return;
                    cb.checked = checked;
                });
                if (checked) {
                    this._checkedIds = checkboxes.filter(cb => cb !== allCheck)
                        .map(cb => cb.closest('tr')?.id).filter(Boolean);
                } else {
                    this._checkedIds = [];
                }
                this._updateDeleteButtonState();
            });
        },
        _updateDeleteButtonState() {
            const btn = this._getDeleteBtn();
            if (!btn) return;
            btn.disabled = this._checkedIds.length === 0;
        },
        deleteSelected() {
            if (!this._checkedIds.length) return;
            this._checkedIds.forEach((id) => {
                const tr = document.getElementById(id);
                if (tr) tr.remove();
            });
            this._checkedIds = [];
            this._syncAllCheckState();
            this._updateDeleteButtonState();
            this._addNoDataRowIfEmpty();
            this._reorderNumbers();
        },
        _addNoDataRowIfEmpty() {
            if (!this.noDataConfig.message) {
                return;
            }
            
            const tbody = this._getTableBody();
            if (!tbody) return;
            
            if (tbody.querySelectorAll('tr').length === 0) {
                const noDataRow = document.createElement('tr');
                noDataRow.className = 'tr-no-data';
                noDataRow.innerHTML = `<td colspan="${this.noDataConfig.colspan}" class="no-data"><div>${this.noDataConfig.message}</div></td>`;
                tbody.appendChild(noDataRow);
            }
        },
        _reorderNumbers() {
            const tbody = this._getTableBody();
            if (!tbody) return;
            
            const rows = tbody.querySelectorAll('tr:not(.tr-no-data)');
            rows.forEach((row, index) => {
                const numberSpan = row.querySelector('.number');
                if (numberSpan) {
                    numberSpan.textContent = index + 1;
                }
            });
        }
    };
};