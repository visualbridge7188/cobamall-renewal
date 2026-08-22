/**
 * Table Manager 컴포넌트
 * 테이블 행의 선택 및 순서 변경 기능 (순수 네이티브 DOM)
 */
(function (window) {
  "use strict";

  const NCUA_DIRECTION_ICONS = [
    { direction: "bottom", text: "맨아래", icon: "ncua-chevron-icon-bottom" },
    { direction: "down", text: "아래", icon: "ncua-chevron-icon-down" },
    { direction: "up", text: "위", icon: "ncua-chevron-icon-up" },
    { direction: "top", text: "맨위", icon: "ncua-chevron-icon-top" },
  ];

  const NCUA_DEFAULT_STEPS = {
    top: -100,
    up: -1,
    down: 1,
    bottom: 100,
  };

  const DEFAULTS = {
    multipleSelection: false,
    direction: {
      enabled: true,
      target: null,
      buttons: NCUA_DIRECTION_ICONS,
    },
    selector: {
      table: "ncua-table-row-target",
      item: "tr.ncua-sort-item",
      select: "ncua-item--selected",
      checkTarget: null, // td 선택자 (null이면 TR 전체에서 감지, 값이 있으면 해당 td만 감지)
    },
    events: {
      init: null,
      select: null,
      unselect: null,
      clear: null,
      direction: null,
      changedRow: null,
    },
  };

  /**
   * 옵션 병합 헬퍼 (깊은 복사)
   */
  function extend(target, source) {
    const result = {};
    for (const key of Object.keys(target)) {
      if (
        target[key] &&
        typeof target[key] === "object" &&
        !Array.isArray(target[key])
      ) {
        result[key] = extend(target[key], {});
      } else {
        result[key] = target[key];
      }
    }

    for (const key of Object.keys(source)) {
      if (
        source[key] &&
        typeof source[key] === "object" &&
        !Array.isArray(source[key]) &&
        result[key] &&
        typeof result[key] === "object"
      ) {
        result[key] = extend(result[key], source[key]);
      } else {
        result[key] = source[key];
      }
    }

    return result;
  }

  /**
   * Table Row Manager 생성자
   * @constructor
   * @param {HTMLElement} tableElement - 테이블 엘리먼트
   * @param {Object} options - 설정 옵션
   */
  function NcuaTableRowManager(tableElement, options) {
    this.tableElement = tableElement;
    if (!this.tableElement) {
      throw new Error("TableRowManager: 테이블 요소를 찾을 수 없습니다.");
    }

    this.options = extend(DEFAULTS, options || {});
    this.sortable = null;
    this.tableElement.classList.add(this.options.selector.table);
    this.directionContainer = null;
    this.selectEventHandler = null;
    this.lastSelectedIndex = -1; // Shift 범위 선택용
    this.init();
  }

  NcuaTableRowManager.prototype.init = function () {
    this.initSelectEvent();

    if (typeof this.initChangeEvent === "function") {
      this.initChangeEvent();
    }

    if (this.options.direction && this.options.direction.enabled) {
      this.initDirectionButtons();
    }

    if (typeof this.options.events.init === "function") {
      this.options.events.init.call(this);
    }
  };

  NcuaTableRowManager.prototype.STEPS = NCUA_DEFAULT_STEPS;

  NcuaTableRowManager.prototype.initDirectionButtons = function () {
    if (
      !this.options.direction.enabled ||
      this.options.direction.target === null
    ) {
      return;
    }

    this.directionContainer = this.options.direction.target;
    this.directionContainer.innerHTML = '';
    this.createButtonGroup(this.options.direction.buttons);
    
    // 초기 버튼 상태 설정 (선택된 항목이 없으므로 모두 비활성화)
    this.updateDirectionButtons();
  };

  /**
   * 방향 버튼 그룹 생성 및 렌더링
   * @param {Array} buttons - 버튼 설정 배열
   * @param {Object} buttons[].direction - 버튼 방향
   * @param {string} buttons[].text - 버튼 텍스트
   * @param {string} buttons[].icon - 버튼 아이콘
   * @returns {HTMLDivElement} 생성된 버튼 그룹 엘리먼트
   */
  NcuaTableRowManager.prototype.createButtonGroup = function (buttons) {
    const buttonGroup = document.createElement("div");
    buttonGroup.className =
      "ncua-button-group ncua-button-group--xs has-border";
    buttons.forEach((btn) => {
      buttonGroup.appendChild(this.createDirectionButton(btn));
    });
    this.directionContainer.prepend(buttonGroup);
    this.buttonGroup = buttonGroup; // 버튼 그룹 참조 저장
  };

  /**
   * 개별 방향 버튼 HTML 생성
   * @param {Object} btnConfig - 버튼 설정 객체
   * @param {string} btnConfig.direction - 버튼 방향
   * @param {string} btnConfig.text - 버튼 텍스트
   * @param {string} btnConfig.icon - 버튼 아이콘
   * @returns {HTMLButtonElement} 생성된 버튼 엘리먼트
   */
  NcuaTableRowManager.prototype.createDirectionButton = function (btnConfig) {
    const { direction, text, icon } = btnConfig;
    const button = document.createElement("button");
    button.type = "button";
    button.className = `ncua-button-group__item ${icon}`;
    button.dataset.direction = direction;
    button.textContent = text;

    button.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();

      if (typeof this.options.events.direction === "function") {
        this.options.events.direction.call(this, direction);
      }
    });

    return button;
  };

  /**
   * jQuery UI Sortable 초기화 (jQuery 필요)
   * @param {Object} options - jQuery UI Sortable 옵션
   * @returns {Object} jQuery UI Sortable 객체
   * @param {Object} options.items - 정렬할 항목 선택자
   * @param {string} options.placeholder - 정렬 시 플레이스홀더 선택자
   * @param {string} options.cursor - 정렬 시 커서 모양
   * @param {Function} options.start - 정렬 시작 시 이벤트 핸들러
   * @param {Function} options.stop - 정렬 종료 시 이벤트 핸들러
   * @param {Function} options.update - 정렬 업데이트 시 이벤트 핸들러
   * @param {Function} options.change - 정렬 변경 시 이벤트 핸들러
   */
  NcuaTableRowManager.prototype.initSortable = function (options) {
    if (typeof window.jQuery === "undefined") {
      return null;
    }
    if (!this.tableElement) {
      return null;
    }
    
    // options가 없으면 빈 객체로 초기화
    options = options || {};
    
    this.sortable = window.jQuery(this.tableElement).sortable(options);
    return this.sortable;
  };

  /**
   * 행 선택 이벤트 초기화 (순수 네이티브)
   */
  NcuaTableRowManager.prototype.initSelectEvent = function () {
    this.selectEventHandler = (e) => {
      const target = e.target.closest(this.options.selector.item);
      if (!target || !this.tableElement.contains(target)) return;

      if (e.target.closest("input, button, select, textarea")) {
        return;
      }

      e.preventDefault();
      e.stopPropagation();

      // Shift + 클릭: 범위 선택
      if (
        e.shiftKey &&
        this.lastSelectedIndex !== -1 &&
        this.options.multipleSelection
      ) {
        const items = this.getAllItems();
        const clickedIndex = items.indexOf(target);
        this.selectRange(this.lastSelectedIndex, clickedIndex);
      }
      // 일반 클릭: 단일 선택/해제
      else {
        if (target.classList.contains(this.options.selector.select)) {
          this.unselectItem(target);
          this.lastSelectedIndex = -1; // 선택 해제 시 초기화
        } else {
          const items = this.getAllItems();
          const clickedIndex = items.indexOf(target);
          this.selectItem(target);
          this.lastSelectedIndex = clickedIndex;
        }
      }
    };

    this.tableElement.addEventListener("click", this.selectEventHandler);
  };

  /**
   * 항목 선택
   * @param {HTMLElement} item - 선택할 항목
   * @returns {NcuaTableRowManager} 체이닝을 위한 this 반환
   */
  NcuaTableRowManager.prototype.selectItem = function (item) {
    item.classList.add(this.options.selector.select);

    if (typeof this.options.events.select === "function") {
      this.options.events.select.call(this, item, this.getSelectedItems());
    }

    this.updateDirectionButtons();

    return this;
  };

  /**
   * 항목 선택 해제
   * @param {HTMLElement} item - 선택 해제할 항목
   * @returns {NcuaTableRowManager} 체이닝을 위한 this 반환
   */
  NcuaTableRowManager.prototype.unselectItem = function (item) {
    item.classList.remove(this.options.selector.select);

    if (typeof this.options.events.unselect === "function") {
      this.options.events.unselect.call(this, item, this.getSelectedItems());
    }

    this.updateDirectionButtons();

    return this;
  };

  /**
   * 모든 선택 해제
   * @returns {NcuaTableRowManager} 체이닝을 위한 this 반환
   */
  NcuaTableRowManager.prototype.clearSelection = function () {
    this.getSelectedItems().forEach((item) => {
      item.classList.remove(this.options.selector.select);
    });

    if (typeof this.options.events.clear === "function") {
      this.options.events.clear.call(this);
    }

    this.updateDirectionButtons();

    return this;
  };

  /**
   * 모든 항목 선택
   * @returns {NcuaTableRowManager} 체이닝을 위한 this 반환
   */
  NcuaTableRowManager.prototype.selectAll = function () {
    this.getAllItems().forEach((item) => {
      item.classList.add(this.options.selector.select);
    });

    if (typeof this.options.events.select === "function") {
      const selectedItems = this.getSelectedItems();
      // 전체 선택은 일괄 작업이므로 이벤트를 한 번만 호출
      this.options.events.select.call(this, null, selectedItems);
    }

    this.updateDirectionButtons();

    return this;
  };

  /**
   * 범위 선택 (Shift + 클릭)
   * @param {number} startIndex - 시작 인덱스
   * @param {number} endIndex - 끝 인덱스
   */
  NcuaTableRowManager.prototype.selectRange = function (startIndex, endIndex) {
    const minIndex = Math.min(startIndex, endIndex);
    const maxIndex = Math.max(startIndex, endIndex);

    // 범위 내 모든 항목 선택
    this.getAllItems().forEach((item, idx) => {
      if (idx >= minIndex && idx <= maxIndex) {
        if (!item.classList.contains(this.options.selector.select)) {
          item.classList.add(this.options.selector.select);
        }
      }
    });

    this.lastSelectedIndex = endIndex;
    this.updateDirectionButtons();
  };

  /**
   * @returns {Array}
   */
  NcuaTableRowManager.prototype.getAllItems = function () {
    return Array.from(
      this.tableElement.querySelectorAll(this.options.selector.item)
    );
  };

  /**
   * 선택된 항목들 가져오기
   * @returns {Array} 선택된 항목들
   */
  NcuaTableRowManager.prototype.getSelectedItems = function () {
    return this.getAllItems().filter((item) =>
      item.classList.contains(this.options.selector.select)
    );
  };

  /**
   * 선택 반전
   * @returns {NcuaTableRowManager} 체이닝을 위한 this 반환
   */
  NcuaTableRowManager.prototype.toggleSelection = function () {
    this.getAllItems().forEach((item) => {
      if (item.classList.contains(this.options.selector.select)) {
        this.unselectItem(item);
      } else {
        this.selectItem(item);
      }
    });
    return this;
  };

  /**
   * 특정 인덱스 항목 선택
   * @param {number} index - 선택할 항목의 인덱스
   * @returns {NcuaTableRowManager} 체이닝을 위한 this 반환
   */
  NcuaTableRowManager.prototype.selectByIndex = function (index) {
    const items = this.getAllItems();
    if (items[index]) {
      this.selectItem(items[index]);
      this.lastSelectedIndex = index;
    }
    return this;
  };

  /**
   * 선택된 항목들이 연속적인지 확인
   * @returns {boolean} true: 연속적, false: 연속적이지 않음
   */
  NcuaTableRowManager.prototype.isContinuousSelection = function () {
    const selectClass = this.options.selector.select;
    const selectedIndexes = [];

    this.getAllItems().forEach((item, idx) => {
      if (item.classList.contains(selectClass)) {
        selectedIndexes.push(idx);
      }
    });

    if (selectedIndexes.length <= 1) return true;

    for (let i = 1; i < selectedIndexes.length; i++) {
      if (selectedIndexes[i] !== selectedIndexes[i - 1] + 1) {
        return false;
      }
    }

    return true;
  };

  /**
   * 선택된 행 이동
   * @param {number} step - 이동 방향 및 거리 (-1: 한칸위, 1: 한칸아래, -100: 맨위, 100: 맨아래)
   */
  NcuaTableRowManager.prototype.moveRow = function (step) {
    const selected = this.getSelectedItems();
    if (selected.length === 0) {
      NCDSAlert({message: '선택된 항목이 없습니다.', iconType: 'error'});
      return;
    }

    const isUpward = step < 0; // 방향 (위: true, 아래: false)
    const isExtreme = Math.abs(step) === this.STEPS.bottom;

    // 이동 가능한 항목들만 필터링
    const movableItems = this._getMovableItems(selected, isUpward);
    if (movableItems.length === 0) return;

    if (isExtreme) {
      this._moveToExtreme(movableItems, isUpward);
    } else {
      this._moveOneStep(movableItems, isUpward);
    }

    // 이벤트 콜백
    if (typeof this.options.events.changedRow === "function") {
      this.options.events.changedRow.call(this, this.getAllItems());
    }

    // 이동 후 버튼 상태 업데이트
    this.updateDirectionButtons();
  };

  /**
   * 이동 가능한 항목들만 필터링
   * 1. 최상단(최하단)에 있는 항목은 이동 못함
   * 2. 이동 못하는 항목과 연속인 항목도 이동 못함
   * 3. 나머지는 이동 가능
   * @param {Array} selected - 선택된 항목들
   * @param {boolean} isUpward - 위로 이동 여부
   * @returns {Array} 이동 가능한 항목들 (원래 순서 유지)
   */
  NcuaTableRowManager.prototype._getMovableItems = function (selected, isUpward) {
    const items = this.getAllItems();
    
    // 선택된 항목들을 원래 순서대로 정렬
    const sortedSelected = selected.slice().sort((a, b) => {
      return items.indexOf(a) - items.indexOf(b);
    });
    
    // 이동 못하는 항목들의 인덱스 집합
    const immovableIndexes = new Set();
    
    // 1. 최상단(최하단)에 있는 항목은 이동 못함
    sortedSelected.forEach((item) => {
      const index = items.indexOf(item);
      if (isUpward) {
        if (index === 0) {
          immovableIndexes.add(index);
        }
      } else {
        if (index === items.length - 1) {
          immovableIndexes.add(index);
        }
      }
    });
    
    // 2. 이동 못하는 항목과 연속인 항목도 이동 못함
    const selectedIndexes = sortedSelected.map(item => items.indexOf(item));
    let hasNewImmovable = true;
    
    // 더 이상 추가할 수 없을 때까지 반복
    while (hasNewImmovable) {
      hasNewImmovable = false;
      
      selectedIndexes.forEach((index) => {
        // 이미 이동 못하는 항목이면 스킵
        if (immovableIndexes.has(index)) {
          return;
        }
        
        // 이전 항목과 연속이고 이동 못하는 항목인지 확인
        if (index > 0 && immovableIndexes.has(index - 1) && selectedIndexes.includes(index - 1)) {
          immovableIndexes.add(index);
          hasNewImmovable = true;
        }
        
        // 다음 항목과 연속이고 이동 못하는 항목인지 확인
        if (index < items.length - 1 && immovableIndexes.has(index + 1) && selectedIndexes.includes(index + 1)) {
          immovableIndexes.add(index);
          hasNewImmovable = true;
        }
      });
    }
    
    // 3. 이동 못하는 항목을 제외한 나머지 반환
    return sortedSelected.filter((item) => {
      const index = items.indexOf(item);
      return !immovableIndexes.has(index);
    });
  };

  /**
   * 맨 위/맨 아래로 이동
   */
  NcuaTableRowManager.prototype._moveToExtreme = function (movableItems, isUpward) {
    if (movableItems.length === 0) return;
    
    const parent = movableItems[0].parentNode;
    const items = this.getAllItems();

    if (isUpward) {
      // 맨 위로: 순서대로 맨 앞에 삽입
      const firstItem = items[0];
      movableItems.forEach((item) => {
        parent.insertBefore(item, firstItem);
      });
    } else {
      // 맨 아래로: 순서를 유지하면서 맨 뒤에 추가
      movableItems.forEach((item) => {
        parent.appendChild(item);
      });
    }
  };

  /**
   * 한 칸 위/아래로 이동
   */
  NcuaTableRowManager.prototype._moveOneStep = function (movableItems, isUpward) {
    if (movableItems.length === 0) return;
    
    const parent = movableItems[0].parentNode;

    if (isUpward) {
      // 위로 이동: 첫 번째 이동 가능한 항목의 이전 형제와 마지막 이동 가능한 항목의 다음 형제 사이에 삽입
      const firstMovable = movableItems[0];
      const lastMovable = movableItems[movableItems.length - 1];
      const target = firstMovable.previousElementSibling;
      if (target) {
        parent.insertBefore(target, lastMovable.nextSibling);
      }
    } else {
      // 아래로 이동: 마지막 이동 가능한 항목의 다음 형제를 첫 번째 이동 가능한 항목 앞에 삽입
      const firstMovable = movableItems[0];
      const lastMovable = movableItems[movableItems.length - 1];
      const target = lastMovable.nextElementSibling;
      if (target) {
        parent.insertBefore(target, firstMovable);
      }
    }
  };

  /**
   * 방향 버튼 상태 업데이트 (이동 가능한 항목이 있는지 확인하여 활성화/비활성화)
   */
  NcuaTableRowManager.prototype.updateDirectionButtons = function () {
    if (!this.buttonGroup || !this.options.direction.enabled) {
      return;
    }
    
    const selected = this.getSelectedItems();

    // 위로 이동 가능한 항목이 있는지 확인
    const movableUp = this._getMovableItems(selected, true).length > 0;
    // 아래로 이동 가능한 항목이 있는지 확인
    const movableDown = this._getMovableItems(selected, false).length > 0;

    // 각 버튼의 활성화 상태 설정
    this.disableDirectionButton(selected, { movableUp, movableDown });
  };

  NcuaTableRowManager.prototype.disableDirectionButton = function (selected, { movableUp, movableDown }) {
    this.buttonGroup.querySelectorAll("button").forEach((btn) => {
      if (selected.length === 0) {
        btn.disabled = false;
        return;
      }

      const direction = btn.dataset.direction;
      
      if (direction === "top" || direction === "up") {
        btn.disabled = !movableUp;
      } else if (direction === "bottom" || direction === "down") {
        btn.disabled = !movableDown;
      } else {
        btn.disabled = false;
      }
    });
  };

  /**
   * 컴포넌트 해제 (메모리 누수 방지)
   */
  NcuaTableRowManager.prototype.destroy = function () {
    // 이벤트 리스너 제거
    if (this.selectEventHandler) {
      this.tableElement.removeEventListener("click", this.selectEventHandler);
      this.selectEventHandler = null;
    }

    // 버튼 컨테이너 정리
    if (this.directionContainer) {
      this.directionContainer.innerHTML = "";
      this.directionContainer = null;
    }

    // jQuery UI Sortable 정리
    if (this.sortable && typeof window.jQuery !== "undefined") {
      window.jQuery(this.tableElement).sortable("destroy");
      this.sortable = null;
    }
  };

  /**
   * 체크박스 기반 Table Row Manager (상속)
   * @constructor
   * @param {HTMLElement} tableElement - 테이블 엘리먼트
   * @param {Object} options - 설정 옵션
   */
  function NcuaTableRowManagerCheckbox(tableElement, options) {
    NcuaTableRowManager.call(this, tableElement, options);

    // 체크박스 전용 옵션
    this.options.selector.checkbox =
      this.options.selector.checkbox || "input[type='checkbox']";
  }

  NcuaTableRowManagerCheckbox.prototype = Object.create(
    NcuaTableRowManager.prototype
  );
  NcuaTableRowManagerCheckbox.prototype.constructor =
    NcuaTableRowManagerCheckbox;

  /**
   * checkTarget에 지정된 td를 클릭했는지 확인
   * @param {Event} e - 클릭 이벤트
   * @returns {boolean} true: checkTarget td 클릭, false: 그 외
   */
  NcuaTableRowManagerCheckbox.prototype.isCheckboxTdClicked = function (e) {
    // 클릭한 td를 찾기
    const clickedTd = e.target.closest("td");
    if (!clickedTd) return false;

    // 클릭한 td에 체크박스가 있는지 확인
    const checkboxInTd = clickedTd.querySelector(
      this.options.selector.checkbox
    );
    if (!checkboxInTd) return false;

    // checkTarget 선택자가 없으면 체크박스가 있는 모든 td 허용
    if (!this.options.selector.checkTarget) return true;

    // 해당 row에서 checkTarget에 맞는 td 찾기
    const row = clickedTd.closest("tr");
    if (!row) return false;

    const targetTd = row.querySelector(this.options.selector.checkTarget);
    return clickedTd === targetTd;
  };

  /**
   * 로우 클릭 시 체크박스 자동 체크
   * 체크박스를 직접 클릭한 경우에만 이벤트 처리 (td 클릭은 무시)
   */
  NcuaTableRowManagerCheckbox.prototype.initSelectEvent = function () {
    // 로우 클릭 이벤트는 비활성화 (체크박스 직접 클릭만 처리)
    // 체크박스 클릭은 initChangeEvent의 change 이벤트에서 처리됨
    this.selectEventHandler = null;
  };

  NcuaTableRowManagerCheckbox.prototype.initChangeEvent = function () {
    // 체크박스 직접 클릭 이벤트
    this.checkboxChangeHandler = (e) => {
      const checkbox = e.target;
      if (!checkbox.matches(this.options.selector.checkbox)) return;

      const target = checkbox.closest(this.options.selector.item);
      if (!target || !this.tableElement.contains(target)) return;

      // checkTarget이 있으면 해당 td만 감지
      if (this.options.selector.checkTarget && !this.isCheckboxTdClicked(e)) {
        return;
      }

      if (checkbox.checked) {
        const items = this.getAllItems();
        const clickedIndex = items.indexOf(target);
        this.selectItem(target);
        this.lastSelectedIndex = clickedIndex;
      } else {
        this.unselectItem(target);
        this.lastSelectedIndex = -1;
      }
    };

    this.tableElement.addEventListener("change", this.checkboxChangeHandler);
  };

  NcuaTableRowManagerCheckbox.prototype.selectItem = function (item) {
    const checkbox = item.querySelector(this.options.selector.checkbox);
    if (checkbox) {
      checkbox.checked = true;
    }

    NcuaTableRowManager.prototype.selectItem.call(this, item);
    return this;
  };

  NcuaTableRowManagerCheckbox.prototype.unselectItem = function (item) {
    const checkbox = item.querySelector(this.options.selector.checkbox);
    if (checkbox) {
      checkbox.checked = false;
    }
    NcuaTableRowManager.prototype.unselectItem.call(this, item);
    return this;
  };

  NcuaTableRowManagerCheckbox.prototype.destroy = function () {
    if (this.checkboxChangeHandler) {
      this.tableElement.removeEventListener(
        "change",
        this.checkboxChangeHandler
      );
      this.checkboxChangeHandler = null;
    }

    NcuaTableRowManager.prototype.destroy.call(this);
  };

  // 전역으로 노출
  window.NcuaTableRowManager = NcuaTableRowManager;
  window.NcuaTableRowManagerCheckbox = NcuaTableRowManagerCheckbox;
})(window);
