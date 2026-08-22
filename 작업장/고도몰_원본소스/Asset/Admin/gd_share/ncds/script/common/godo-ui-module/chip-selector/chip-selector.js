/**
 * ChipSelector - 카테고리 선택 및 변수 칩 버튼 UI 모듈
 * 카테고리별 변수를 칩 버튼 형태로 표시하고 선택할 수 있는 UI를 제공합니다.
 */
class ChipSelector {
    /**
     * @param {Object} options - 초기화 옵션
     * @param {string|HTMLElement} options.container - 렌더링될 컨테이너 셀렉터 또는 요소
     * @param {string} options.title - 섹션 제목
     * @param {string} options.tooltipSeq - 툴팁 시퀀스 (optional)
     * @param {string} options.cautionHTML - 안내 텍스트 HTML (optional)
     * @param {Array} options.categories - 카테고리 목록
     * @param {Function} options.onSelect - chip 선택 시 콜백
     * @param {Function} options.onCategoryChange - 카테고리 변경 시 콜백 (optional)
     * @param {boolean} options.hideCategorySelector - 카테고리 선택 드롭다운 숨김 (default false)
     */
    constructor(options) {
        if (!options || !options.container) {
            throw new Error('ChipSelector: container 옵션이 필요합니다.');
        }

        // 문자열이면 querySelector, 아니면 그대로 사용
        this.container = typeof options.container === 'string' 
            ? document.querySelector(options.container)
            : options.container;
            
        if (!this.container) {
            throw new Error('ChipSelector: container를 찾을 수 없습니다.');
        }

        this.title = options.title || '치환코드';
        this.tooltipSeq = options.tooltipSeq != null ? String(options.tooltipSeq) : null;
        this.cautionHTML = options.cautionHTML || null;
        this.categories = options.categories || [];
        this.onSelectCallback = options.onSelect || null;
        this.onCategoryChangeCallback = options.onCategoryChange || null;
        this.hideCategorySelector = options.hideCategorySelector === true;
        
        // 초기 카테고리 설정
        this.currentCategoryValue = this.categories[0]?.value || '';
        
        // 초기 렌더링
        this.render();
    }

    /**
     * 전체 UI 렌더링
     */
    render() {
        const html = this.buildHTML();
        this.container.innerHTML = html;
        this.bindEvents();
        return this;
    }

    /**
     * HTML 생성
     */
    buildHTML() {
        const tooltipAttr = this.tooltipSeq ? ` data-tooltip-seq="${this.tooltipSeq}"` : '';
        
        // 카테고리 옵션 생성
        const optionsHTML = this.categories.map(cat => {
            const isSelected = cat.value === this.currentCategoryValue ? ' selected' : '';
            return `<option value="${this.escapeHTML(cat.value)}"${isSelected}>${this.escapeHTML(cat.label)}</option>`;
        }).join('');

        const selectedCategory = this.categories.find(c => c.value === this.currentCategoryValue);
        const chipsHTML = this.renderChipButtonsHTML(selectedCategory?.variables || []);

        // 안내 텍스트 HTML (있는 경우에만 추가, XSS 방지)
        const cautionContent = this.cautionHTML ? this.sanitizeHTML(this.cautionHTML) : '';

        const categorySelectorHTML = this.hideCategorySelector ? '' : `
                    <div class="ncua-select ncua-select--xs">
                        <div class="ncua-select__content">
                            <select class="ncua-select__tag js-chip-category-select">
                                ${optionsHTML}
                            </select>
                        </div>
                    </div>`;

        return `
            <div class="variable-selector-wrap">
                <div class="ncua-card__body-title-wrap">
                    <p class="ncua-card__body-title--xs tooltip-align"${tooltipAttr}>${this.escapeHTML(this.title)}</p>
                </div>
                ${cautionContent}
                <div class="chip-selector">${categorySelectorHTML}
                    <div class="chip-button-wrap">
                        ${chipsHTML}
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * 변수 칩 버튼들 HTML 생성
     */
    renderChipButtonsHTML(variables) {
        return variables.map(v =>
            `<button type="button" class="chip-button" data-variable="${this.escapeHTML(v.insertKey ?? v.key)}">
                <span class="chip-button-text">${this.escapeHTML(v.key)} ${this.escapeHTML(v.label)}</span>
            </button>`
        ).join('');
    }

    /**
     * 이벤트 바인딩
     */
    bindEvents() {
        const selectEl = this.container.querySelector('.js-chip-category-select');

        // 카테고리 선택 이벤트 (중복 방지)
        if (selectEl && !selectEl.hasAttribute('data-event-bound')) {
            selectEl.setAttribute('data-event-bound', 'true');
            selectEl.addEventListener('change', () => {
                this.currentCategoryValue = selectEl.value;
                this.updateChipButtons();
                
                // 카테고리 변경 콜백 호출
                if (typeof this.onCategoryChangeCallback === 'function') {
                    this.onCategoryChangeCallback(this.currentCategoryValue);
                }
            });
        }

        // 초기 chip 버튼 이벤트 바인딩
        this.bindChipEvents();
    }

    /**
     * Chip 버튼 클릭 이벤트 바인딩
     */
    bindChipEvents() {
        const chipWrap = this.container.querySelector('.chip-button-wrap');
        if (!chipWrap) return;

        // 이미 이벤트가 바인딩되어 있으면 중복 방지
        if (chipWrap.hasAttribute('data-events-bound')) return;
        chipWrap.setAttribute('data-events-bound', 'true');

        // 이벤트 위임: 부모 요소에 하나의 리스너만 등록
        chipWrap.addEventListener('click', (e) => {
            // 클릭된 요소가 chip-button이거나 그 자식인 경우
            const button = e.target.closest('.chip-button');
            if (!button) return;

            const variable = button.getAttribute('data-variable');
            if (typeof this.onSelectCallback === 'function') {
                this.onSelectCallback(variable, button);
            }
        });
    }

    /**
     * Chip 버튼 업데이트
     * 이벤트 위임을 사용하므로 innerHTML 교체 후에도
     * 부모 요소의 이벤트 리스너가 새 버튼에 자동 작동
     */
    updateChipButtons() {
        const chipWrap = this.container.querySelector('.chip-button-wrap');
        if (!chipWrap) return;

        const selectedCategory = this.categories.find(c => c.value === this.currentCategoryValue);
        chipWrap.innerHTML = this.renderChipButtonsHTML(selectedCategory?.variables || []);
    }

    /**
     * 카테고리 목록 설정
     * @param {Array} categories - 새로운 카테고리 목록
     */
    setCategories(categories) {
        this.categories = categories;
        this.currentCategoryValue = this.categories[0]?.value || '';
        this.render();
        return this;
    }

    /**
     * 현재 선택된 카테고리 반환
     * @returns {string} 현재 카테고리 값
     */
    getSelectedCategory() {
        return this.currentCategoryValue;
    }

    /**
     * 선택 콜백 함수 설정
     * @param {Function} callback - chip 선택 시 호출될 콜백 함수
     */
    onSelect(callback) {
        if (typeof callback === 'function') {
            this.onSelectCallback = callback;
        }
        return this;
    }

    /**
     * 카테고리 변경 콜백 함수 설정
     * @param {Function} callback - 카테고리 변경 시 호출될 콜백 함수
     * @returns {ChipSelector}
     */
    onCategoryChange(callback) {
        if (typeof callback === 'function') {
            this.onCategoryChangeCallback = callback;
        }
        return this;
    }

    /**
     * 안내 텍스트 HTML 설정
     * @param {string} html - 안내 텍스트 HTML
     */
    setCautionHTML(html) {
        this.cautionHTML = html || null;
        this.render();
        return this;
    }

    /**
     * 전체 새로고침
     */
    refresh() {
        this.updateChipButtons();
        return this;
    }

    /**
     * HTML 문자열에서 위험 요소를 제거하고 안전한 HTML을 반환
     * @param {string} html
     * @returns {string}
     */
    sanitizeHTML(html) {
        if (typeof html !== 'string') return '';
        if (!html.trim()) return html;

        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        const dangerousTags = ['script', 'iframe', 'object', 'embed', 'form', 'input', 'textarea', 'select', 'link', 'style', 'meta', 'base', 'svg', 'math', 'noscript'];
        const urlAttrs = ['href', 'src', 'action', 'formaction', 'xlink:href', 'data'];

        dangerousTags.forEach(function (tag) {
            const elements = doc.querySelectorAll(tag);
            for (let i = 0; i < elements.length; i++) { elements[i].remove(); }
        });

        const allElements = doc.querySelectorAll('*');
        for (let i = 0; i < allElements.length; i++) {
            const el = allElements[i];
            const attrs = [];
            for (let j = 0; j < el.attributes.length; j++) { attrs.push(el.attributes[j].name); }
            for (let k = 0; k < attrs.length; k++) {
                const attrName = attrs[k].toLowerCase();
                const attrValue = (el.getAttribute(attrs[k]) || '').trim().toLowerCase();
                if (attrName.indexOf('on') === 0) { el.removeAttribute(attrs[k]); continue; }
                if (urlAttrs.indexOf(attrName) !== -1 && attrValue.indexOf('javascript:') === 0) { el.removeAttribute(attrs[k]); }
            }
        }

        return doc.body.innerHTML;
    }

    /**
     * HTML 이스케이프
     * @param {string} str - 이스케이프할 문자열
     * @returns {string} 이스케이프된 문자열
     */
    escapeHTML(str) {
        if (typeof str !== 'string') return str;
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    /**
     * 컨테이너 초기화
     */
    destroy() {
        if (this.container) {
            this.container.innerHTML = '';
        }
    }
}

// CommonJS/AMD 지원
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ChipSelector;
}
