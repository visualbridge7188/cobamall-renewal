
/**
 * NCDS 멀티 셀렉트 매니저
 * 여러 select 요소를 ncua span 구조로 표준 래핑해주는 객체 인스턴스 반환
 * @module NCDSMultiSelectManager
 * @param {Object} options - 옵션 객체
 * @param {Element|NodeList|Array<Element>} [options.targetGroups] - 그룹 부모 element(기본: 전체)
 * @param {string} [sizeClass='ncua-select--xs'] - select 크기용 클래스명 (옵션: 'ncua-select--xs', 'ncua-select--sm', 'ncua-select--md')
 * @returns {Object} NCDSMultiSelectManager 인스턴스
 */
function createNcuaMultiSelectManager({ targetGroups, sizeClass } = {}) {
    const SELECTORS = {
        SELECT_GROUP: '.ncua-select-group',
        SELECT: 'select',
    };
    const CLASSES = {
        SELECT: 'ncua-select',
        CONTENT: 'ncua-select__content',
    };
    const _sizeClass = sizeClass ?? 'ncua-select--xs';
    let _groups = null;

    /**
     * 해당 select가 이미 감싸진 상태인지 판별
     * @param {Element} select
     * @returns {boolean}
     */
    const isWrappedSelect = (select) => {
        const parent = select.parentElement;
        const grandParent = parent?.parentElement;
        return (
            parent?.classList.contains(CLASSES.CONTENT) &&
            grandParent?.classList.contains(CLASSES.SELECT)
        );
    };

    /**
     * 단일 select 엘리먼트 감싸기
     * @param {Element} select
     * @param {string} sizeClass
     */
    const wrapSingleSelect = (select, sizeClassArg) => {
        const sClass = sizeClassArg ?? _sizeClass;
        const contentSpan = document.createElement('span');
        contentSpan.className = CLASSES.CONTENT;
        const outerSpan = document.createElement('span');
        outerSpan.className = `${CLASSES.SELECT} ${sClass}`;
        const oldParent = select.parentNode;
        const oldNext = select.nextSibling;
        contentSpan.appendChild(select);
        outerSpan.appendChild(contentSpan);
        if (oldParent) {
            oldParent.insertBefore(outerSpan, oldNext);
        }
    };

    /**
     * 모든 셀렉트 그룹 내 select 요소들을 ncua span 구조로 변경
     * 이미 래핑된 요소는 스킵
     */
    const _wrapAll = () => {
        if (!_groups) return;
        _groups.forEach((group) => {
            const selects = group.querySelectorAll(SELECTORS.SELECT);
            selects.forEach((select) => {
                if (!isWrappedSelect(select)) {
                    wrapSingleSelect(select);
                }
            });
        });
    };

    /**
     * 셀렉트 그룹(NodeList/배열/단일 element 등) 정규화 및 조회
     * 옵션 미지정 시, 전체 그룹을 자동 조회하여 배열로 반환
     * @private
     * @returns {Element[]} 그룹 element 배열
     */
    const _resolveGroups = () => {
        if (targetGroups) {
            return (NodeList.prototype.isPrototypeOf(targetGroups) || Array.isArray(targetGroups)
                ? Array.from(targetGroups)
                : [targetGroups]);
        }
        return Array.from(document.querySelectorAll(SELECTORS.SELECT_GROUP));
    };

    return {
        init() {
            _groups = _resolveGroups();
            _wrapAll();
        },
        destroy() {
            if (!_groups) return;
            _groups.forEach((group) => {
                const wrapperList = group.querySelectorAll(`.${CLASSES.SELECT}`);
                wrapperList.forEach((wrapEl) => {
                    const contentSpan = wrapEl.querySelector(`.${CLASSES.CONTENT}`);
                    const select = contentSpan?.querySelector('select');
                    if (select && wrapEl.parentNode) {
                        wrapEl.parentNode.insertBefore(select, wrapEl.nextSibling);
                        wrapEl.remove();
                    }
                });
            });
            _groups = null;
        },
        update() {
            _wrapAll();
        },
    };
}