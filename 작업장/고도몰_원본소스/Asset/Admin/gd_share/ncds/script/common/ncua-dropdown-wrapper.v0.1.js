
/**
 * 드롭다운 메뉴 아이템 타입
 * @typedef {Object} DropdownMenuItem
 * @property {string} text - 메뉴 아이템 텍스트 (HTML 지원)
 * @property {string} type - 메뉴 아이템 type
 * @property {string} [group] - 그룹 이름 (data.group 사용 시)
 * @property {string[]} [itemClassNames] - 커스텀 CSS 클래스명 배열 (기본값: ['ncua-dropdown__item'])
 */

/**
 * 드롭다운 이벤트 핸들러
 * @typedef {Object} DropdownEvents
 * @property {function(Event, HTMLElement, DropdownMenuItem): void} [itemClick] - 메뉴 아이템 클릭 시 호출되는 콜백
 */

/**
 * 드롭다운 데이터 객체
 * @typedef {Object} DropdownData
 * @property {DropdownMenuItem[]} items - 메뉴 아이템 배열 (필수)
 * @property {string[]} [group] - 그룹 이름 배열 (그룹화 사용 시)
 */

/**
 * 드롭다운 레이아웃 설정
 * @typedef {Object} DropdownLayout
 * @property {function(HTMLDivElement): HTMLDivElement} [menu] - 메뉴 위치 설정 커스텀 함수
 *   메뉴 엘리먼트를 받아서 스타일을 적용한 후 동일한 엘리먼트를 반환해야 함
 */

/**
 * 드롭다운 옵션
 * @typedef {Object} DropdownOptions
 * @property {DropdownData} data - 데이터 객체 (필수)
 * @property {DropdownEvents} [events] - 이벤트 핸들러 객체
 * @property {DropdownLayout} [layout] - 레이아웃 설정
 */

(function () {
    "use strict";

    function createObservable(v) {
        const ls = new Set();
        function obs(nv) { if (!arguments.length) return v; if (!Object.is(v, nv)) { v = nv; ls.forEach(f => f(v)); } return v; }
        obs.subscribe = f => (ls.add(f), () => ls.delete(f));
        return obs;
    }

    /**
     * NCUA Dropdown 컴포넌트
     * @constructor
     * @param {HTMLElement} element - 드롭다운 컨테이너 엘리먼트
     * @param {DropdownOptions} options - 드롭다운 옵션 (data 필수)
     *
     * @example
     * // 기본 사용
     * const dropdown = new NcuaDropdownWrapper(document.getElementById('dropdown'), {
     *     data: {
     *         items: [
     *             { text: '수정' },
     *             { text: '삭제' }
     *         ]
     *     },
     *     events: {
     *         itemClick: (e, item) => {
     *             console.log('클릭됨:', item.text);
     *         }
     *     }
     * });
     *
     * @example
     * // 그룹 사용
     * const dropdown = new NcuaDropdownWrapper(element, {
     *     data: {
     *         items: [
     *             { text: '복사', group: 'edit' },
     *             { text: '붙여넣기', group: 'edit' },
     *             { text: '삭제', group: 'danger', itemClassNames: ['is-danger'] }
     *         ],
     *     },
     *     events: {
     *         itemClick: (e, item) => {
     *             console.log('클릭됨:', item.text);
     *         }
     *     }
     * });
     *
     * @example
     * // 커스텀 메뉴 위치 설정
     * const dropdown = new NcuaDropdownWrapper(element, {
     *     data: {
     *         items: [
     *             { text: '수정' },
     *             { text: '삭제' }
     *         ]
     *     },
     *     layout: {
     *         menu: function(menuElement) {
     *             menuElement.style.position = 'absolute';
     *             menuElement.style.top = '100%';
     *             menuElement.style.left = '0'; // 메뉴 위치 변경
     *             menuElement.style.marginTop = '8px';
     *             menuElement.style.zIndex = '5000';
     *             return menuElement;
     *         }
     *     },
     *     events: {
     *         itemClick: (e, item) => {
     *             console.log('클릭됨:', item.text);
     *         }
     *     }
     * });
     */
    function NcuaDropdownWrapper(element, options = {}) {
        if (!element) {
            throw new Error('NcuaDropdownWrapper: 엘리먼트가 필요합니다.');
        }
        if (!options.data) {
            throw new Error('NcuaDropdownWrapper: data 옵션이 필요합니다.');
        }
        if (!options.data.items) {
            throw new Error('NcuaDropdownWrapper: data.items 옵션이 필요합니다.');
        }

        this.element = element;
        this.options = {
            ...options,
            events: {
                itemClick: options.events?.itemClick || null
            },
            layout: {
                menu: options.layout?.menu || null
            },
        };
        this.isOpen = createObservable(false);
        this.triggerButton = null;
        this.menu = null;
        this.init();
    }

    /**
     * 메뉴 아이템 배열 반환
     * @returns {DropdownMenuItem[]}
     */
    NcuaDropdownWrapper.prototype.getItems = function () {
        return this.options.data?.items || [];
    }

    /**
     * 드롭다운 초기화
     * 이벤트 리스너 등록 및 DOM 구조 생성
     */
    NcuaDropdownWrapper.prototype.init = function () {
        this._boundHandleOutsideClick = this.handleOutsideClick.bind(this);

        this.isOpen.subscribe((isOpen) => {
            if (isOpen) {
                this.menu = this.createMenuElement();
                this.element.appendChild(this.menu);
                setTimeout(() => {
                    document.addEventListener('click', this._boundHandleOutsideClick);
                }, 0);
            } else {
                if (this.menu) {
                    this.menu.remove();
                    this.menu = null;
                }
                document.removeEventListener('click', this._boundHandleOutsideClick);
            }
        });

        this.element.innerHTML = '';
        this.element.classList.add('ncua-dropdown');
        this.element.style.position = 'relative';
        this.triggerButton = this.createTriggerButtonElement();
        this.element.appendChild(this.triggerButton);
    }

    /**
     * 외부 클릭 감지 핸들러
     */
    NcuaDropdownWrapper.prototype.handleOutsideClick = function (e) {
        if (!this.element.contains(e.target)) {
            this.isOpen(false);
        }
    }

    /**
     * 드롭다운 토글 (열기/닫기)
     */
    NcuaDropdownWrapper.prototype.toggle = function () {
        this.isOpen(!this.isOpen())
    }

    /**
     * 메뉴 위치 업데이트 (트리거 버튼 기준)
     * 기본 위치: 트리거 버튼 아래, 오른쪽 정렬
     * @param {HTMLDivElement} menu - 메뉴 엘리먼트
     * @returns {HTMLDivElement} 스타일이 적용된 메뉴 엘리먼트
     */
    NcuaDropdownWrapper.prototype.setMenuPosition = function (menu) {
        menu.style.position = 'absolute';
        menu.style.top = '100%';
        menu.style.marginTop = '4px';
        menu.style.zIndex = '3000';
        return menu;
    };

    /**
     * 트리거 버튼 엘리먼트 생성
     * @returns {HTMLButtonElement}
     */
    NcuaDropdownWrapper.prototype.createTriggerButtonElement = function () {
        const triggerButtonElement = document.createElement('button');
        triggerButtonElement.classList.add(...['ncua-dropdown__trigger', 'ncua-dropdown__trigger--icon']);
        triggerButtonElement.type = 'button';
        triggerButtonElement.style.width = '100%';
        triggerButtonElement.style.height = '100%';
        triggerButtonElement.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggle();
        });
        const iconElement = this.createTriggerIconElement();
        triggerButtonElement.appendChild(iconElement);
        return triggerButtonElement;
    }

    /**
     * 트리거 아이콘 엘리먼트 생성 (3-dot 메뉴 아이콘)
     * @returns {SVGElement}
     */
    NcuaDropdownWrapper.prototype.createTriggerIconElement = function () {
        const container = document.createElement('div');
        const svg = `
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66667 8.0013C6.66667 7.26492 7.26362 6.66797 8 6.66797C8.73638 6.66797 9.33333 7.26492 9.33333 8.0013C9.33333 8.73768 8.73638 9.33464 8 9.33464C7.26362 9.33464 6.66667 8.73768 6.66667 8.0013Z" fill="#171818"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M11.3333 8.0013C11.3333 7.26492 11.9303 6.66797 12.6667 6.66797C13.403 6.66797 14 7.26492 14 8.0013C14 8.73768 13.403 9.33464 12.6667 9.33464C11.9303 9.33464 11.3333 8.73768 11.3333 8.0013Z" fill="#171818"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M2 8.0013C2 7.26492 2.59695 6.66797 3.33333 6.66797C4.06971 6.66797 4.66667 7.26492 4.66667 8.0013C4.66667 8.73768 4.06971 9.33464 3.33333 9.33464C2.59695 9.33464 2 8.73768 2 8.0013Z" fill="#171818"/>
            </svg>
        `;
        container.innerHTML = svg;
        return container.firstElementChild;
    }

    /**
     * 드롭다운 메뉴 엘리먼트 생성
     * @returns {HTMLDivElement}
     */
    NcuaDropdownWrapper.prototype.createMenuElement = function () {
        const menuElement = document.createElement('div');
        menuElement.classList.add(...['ncua-dropdown__menu']);
        const itemsElement = this.createMenuItems(this.getItems());
        menuElement.appendChild(itemsElement);

        if (this.options.layout?.menu) {
            return this.options.layout.menu(menuElement);
        }
        return this.setMenuPosition(menuElement);
    }

    /**
     * 메뉴 아이템들 생성
     * ncua-dropdown__menu > ncua-dropdown__items > ncua-dropdown__group > ncua-dropdown__item
     * @param {DropdownMenuItem[]} items - 메뉴 아이템 배열
     * @returns {HTMLDivElement}
     */
    NcuaDropdownWrapper.prototype.createMenuItems = function (items = []) {
        const menuItemsElement = document.createElement('div');
        menuItemsElement.classList.add(...['ncua-dropdown__items']);

        const normalizedItems = items.map(item => ({
            ...item,
            group: item.group || 'noname'
        }));

        const groups = [...new Set(normalizedItems.map(item => item.group))];

        groups.forEach((groupName) => {
            const groupElement = this.createMenuGroup(groupName, normalizedItems);
            if (groupElement && groupElement.children.length > 0) {
                menuItemsElement.appendChild(groupElement);
            }
        });

        return menuItemsElement;
    }

    /**
     * 메뉴 그룹 생성
     * @param {string} groupName - 그룹 이름
     * @param {DropdownMenuItem[]} items - 메뉴 아이템 배열
     * @returns {HTMLDivElement} 그룹 엘리먼트 (빈 그룹일 수 있음)
     */
    NcuaDropdownWrapper.prototype.createMenuGroup = function (groupName, items) {
        const menuGroupElement = document.createElement('div');
        menuGroupElement.classList.add(...['ncua-dropdown__group', `ncua-dropdown__group--${groupName}`]);
        items.forEach((item) => {
            if (item.group === groupName) {
                const itemElement = this.createMenuItem(item);
                menuGroupElement.appendChild(itemElement);
            }
        });
        return menuGroupElement;
    }

    /**
     * 개별 메뉴 아이템 생성
     * @param {DropdownMenuItem} item - 메뉴 아이템 객체
     * @returns {HTMLDivElement}
     */
    NcuaDropdownWrapper.prototype.createMenuItem = function (item) {
        const menuItemElement = document.createElement('div');
        const classNames = [...['ncua-dropdown__item'], ...(item.itemClassNames || [])];
        menuItemElement.classList.add(...classNames);
        const clickableLayerElement = document.createElement('button');
        clickableLayerElement.classList.add(...['ncua-dropdown__item-clickable-layer']);
        clickableLayerElement.addEventListener('click', (e) => {
            e.preventDefault();
            this.options.events?.itemClick?.(e, this.element, item);
            this.isOpen(false);
        });
        const contentElement = document.createElement('div');
        contentElement.classList.add(...['ncua-dropdown__item-content']);
        contentElement.innerHTML = item.text;

        menuItemElement.appendChild(clickableLayerElement);
        menuItemElement.appendChild(contentElement);
        return menuItemElement;
    }

    /**
     * 드롭다운 인스턴스 파괴 및 이벤트 리스너 정리
     */
    NcuaDropdownWrapper.prototype.destroy = function () {
        this.isOpen(false);
        if (this._boundHandleOutsideClick) {
            document.removeEventListener('click', this._boundHandleOutsideClick);
        }
        if (this.element) {
            this.element.innerHTML = '';
        }
    };

    window.NcuaDropdownWrapper = NcuaDropdownWrapper;
})();
