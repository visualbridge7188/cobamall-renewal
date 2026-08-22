/**
 * NCDS 체크박스 그룹 전체 선택 기능
 * 
 * 컨테이너 내 value="all" 체크박스와 개별 체크박스 간 동기화
 * 
 * @example
 * // HTML
 * <div class="js-checkbox-group">
 *     <input type="checkbox" value="all" checked />
 *     <input type="checkbox" value="option1" />
 *     <input type="checkbox" value="option2" />
 * </div>
 * 
 * // JavaScript
 * new CheckboxGroup('.js-checkbox-group');
 * // 또는 옵션과 함께
 * new CheckboxGroup('.js-checkbox-group', { allValue: 'all', initSync: true });
 */
(function() {
    'use strict';

    /**
     * @param {string|HTMLElement} container - 컨테이너 셀렉터 또는 요소
     * @param {Object} options - 옵션
     * @param {string} options.allValue - 전체 선택 체크박스의 value (기본값: 'all')
     * @param {boolean} options.initSync - 초기화 시 체크박스 동기화 여부 (기본값: true)
     */
    function CheckboxGroup(container, options) {
        options = options || {};

        this.container = typeof container === 'string' 
            ? document.querySelector(container) 
            : container;
        
        if (!this.container) {
            console.warn('CheckboxGroup: 컨테이너를 찾을 수 없습니다.');
            return;
        }

        this.options = {
            allValue: options.allValue || 'all',
            initSync: options.initSync !== false
        };

        this._init();
    }

    /**
     * 전체 선택 체크박스 반환
     * @returns {HTMLInputElement|null}
     */
    CheckboxGroup.prototype.getAllCheckbox = function() {
        return this.container.querySelector('input[type="checkbox"][value="' + this.options.allValue + '"]');
    };

    /**
     * 개별 체크박스 목록 반환
     * @returns {NodeList}
     */
    CheckboxGroup.prototype.getItemCheckboxes = function() {
        return this.container.querySelectorAll('input[type="checkbox"]:not([value="' + this.options.allValue + '"])');
    };

    CheckboxGroup.prototype._init = function() {
        this._syncItemsFromAll();
        this._bindEvents();
    };

    /**
     * 전체 체크박스 상태에 따라 개별 체크박스 동기화
     */
    CheckboxGroup.prototype._syncItemsFromAll = function() {
        const allCheckbox = this.getAllCheckbox();
        if (!allCheckbox || !allCheckbox.checked) return;

        const itemCheckboxes = this.getItemCheckboxes();
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = allCheckbox.checked;
        });
    };

    /**
     * 개별 체크박스 상태에 따라 전체 체크박스 동기화
     */
    CheckboxGroup.prototype._syncAllFromItems = function() {
        const allCheckbox = this.getAllCheckbox();
        if (!allCheckbox) return;

        const itemCheckboxes = this.getItemCheckboxes();
        const items = Array.from(itemCheckboxes).filter(function(cb) {
            return !cb.disabled;
        });
        
        const allChecked = items.length > 0 && items.every(function(cb) {
            return cb.checked;
        });
        
        allCheckbox.checked = allChecked;
    };

    CheckboxGroup.prototype._bindEvents = function() {
        this.container.addEventListener('change', (e) => {
            if (e.target.type !== 'checkbox') return;

            if (e.target.value === this.options.allValue) {
                this._handleAllChange(e.target.checked);
            } else {
                this._syncAllFromItems();
            }
        });
    };

    CheckboxGroup.prototype._handleAllChange = function(checked) {
        const itemCheckboxes = this.getItemCheckboxes();
        for (let i = 0; i < itemCheckboxes.length; i++) {
            if (!itemCheckboxes[i].disabled) {
                itemCheckboxes[i].checked = checked;
            }
        }
    };

    /**
     * 선택된 값 배열 반환
     * @returns {string[]}
     */
    CheckboxGroup.prototype.getSelectedValues = function() {
        const itemCheckboxes = this.getItemCheckboxes();
        return Array.prototype.slice.call(itemCheckboxes)
            .filter(function(cb) { return cb.checked; })
            .map(function(cb) { return cb.value; });
    };

    /**
     * 모든 체크박스 선택/해제
     * @param {boolean} checked
     */
    CheckboxGroup.prototype.setAll = function(checked) {
        const allCheckbox = this.getAllCheckbox();
        if (allCheckbox) {
            allCheckbox.checked = checked;
        }
        this._handleAllChange(checked);
    };

    // 전역 노출
    window.CheckboxGroup = CheckboxGroup;
})();
