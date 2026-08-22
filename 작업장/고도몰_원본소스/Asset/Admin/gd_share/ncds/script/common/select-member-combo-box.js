/**
 * 회원등급 선택 ComboBox 관리 모듈
 */
(function() {
    'use strict';

    /**
     * 회원등급 관리 모듈
     */
    const MemberGroupManager = {
        /**
         * 회원등급 레이어 데이터 조회
         * @param {string} parentLayerFormID - 부모 레이어 폼 ID
         * @param {string} dataInputNm - 데이터 입력 필드명
         * @param {string} dataFormID - 데이터 폼 ID
         * @param {string} keyword - 검색 키워드
         * @param {number} pagelink - 페이지 번호
         * @param {string} apiUrl - API URL (기본값: '../share/ncds/layer_member_group.php')
         * @param {Object} validationConfig - 검증 설정 (선택적)
         * @returns {Promise<Object|null>} 회원등급 데이터
         */
        async layer_member_group(parentLayerFormID, dataInputNm, dataFormID, keyword, pagelink, apiUrl = '../share/ncds/layer_member_group.php', validationConfig = {}) {
            // 검증 설정이 있으면 실행
            if (validationConfig?.shouldSkip?.(dataInputNm)) {
                return;
            }

            // 기본 검증 로직 (하위 호환성)
            if (dataInputNm === 'bdAuthReplyGroup') {
                const replyRadio = document.querySelector('input[name="bdReplyFl"]:checked');
                if (replyRadio?.value === 'n') {
                    return;
                }
            } else if (dataInputNm === 'bdAuthMemoGroup') {
                const memoRadio = document.querySelector('input[name="bdMemoFl"]:checked');
                if (memoRadio?.value === 'n') {
                    return;
                }
            } else if (dataInputNm === 'bdAuthWriteGroup') {
                const writeRadio = document.querySelector('input[name="bdAuthWrite"][value="group"]');
                if (writeRadio?.disabled) {
                    return;
                }
            }

            const addParam = {
                parentFormID: parentLayerFormID,
                dataInputNm,
                dataFormID,
                pagelink: `page=${pagelink}`,
                keyword,
            };

            const queryString = new URLSearchParams(addParam).toString();
            
            try {
                const response = await fetch(`${apiUrl}?${queryString}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return await response.json();
            } catch (error) {
                return null;
            }
        },

        /**
         * 선택된 회원등급 제거
         * @param {string} dataInputNm - 데이터 입력 필드명
         * @param {number|string} id - 제거할 회원등급 ID
         */
        removeMemberGroup(dataInputNm, id) {
            const element = document.getElementById(`${dataInputNm}_${id}`);
            if (!element) return;
            
            element.remove();
            
            const config = window.memberGroupRemoveMap?.[dataInputNm];
            if (!config) return;

            const memberGroupLayer = document.getElementById(config.parentLayerId);
            if (!memberGroupLayer) return;

            const remainingItems = memberGroupLayer.querySelectorAll(`div[id^="${dataInputNm}_"]`);
            if (remainingItems.length > 0) return;

            memberGroupLayer.classList.remove('active');
            memberGroupLayer.querySelector('h5')?.remove();
        },

        /**
         * API 응답 데이터를 ComboBox 옵션 형식으로 변환
         * @param {Array|Object} res - API 응답 데이터
         * @returns {Array} 변환된 옵션 배열
         */
        _transformOptions(res) {
            if (!res) return [];
            const options = Array.isArray(res) ? res : [res];
            return options.map(item => ({
                id: item.sno,
                label: item.groupNm
            }));
        },

        /**
         * 회원등급 ComboBox 공통 초기화 함수
         * @param {Object} config - 설정 객체
         * @param {string} config.comboboxId - ComboBox 요소 ID
         * @param {string} config.parentLayerId - 부모 레이어 ID
         * @param {string} config.dataInputNm - 데이터 입력 필드명
         * @param {string} config.dataFormID - 데이터 폼 ID
         * @param {string} config.apiUrl - API URL (선택적)
         * @param {Object} config.validationConfig - 검증 설정 (선택적)
         * @param {string} config.placeholder - Placeholder 텍스트 (기본값: '회원등급 선택/검색')
         * @param {string} config.labelText - 레이블 텍스트 (기본값: '선택된 회원등급 : ')
         * @returns {Object|null} ComboBox 인스턴스
         */
        initMemberGroupComboBox(config) {
            const { 
                comboboxId, 
                parentLayerId, 
                dataInputNm, 
                dataFormID, 
                apiUrl = '../share/ncds/layer_member_group.php',
                validationConfig = {},
                placeholder = '회원등급 선택/검색',
                labelText = '선택된 회원등급 : '
            } = config;

            const comboboxElement = document.getElementById(comboboxId);
            if (!comboboxElement) return null;

            // 클로저로 상태 관리 (전역 변수 사용 X)
            let pageCount = 1;
            let keyword = '';
            
            const comboBox = new window.ncua.ComboBox(comboboxElement, {
                options: [],
                size: 'xs',
                placeholder,
                multiple: true,
                showFooterButtons: true,
                onSearch: async (searchKeyword) => {
                    pageCount = 1;
                    keyword = searchKeyword;
                    const res = await MemberGroupManager.layer_member_group(
                        parentLayerId, 
                        dataInputNm, 
                        dataFormID, 
                        keyword, 
                        pageCount,
                        apiUrl,
                        validationConfig
                    );
                    const options = MemberGroupManager._transformOptions(res);
                    comboBox.updateOptions(options);
                },
                onScrollBottom: async () => {
                    const nextPageCount = pageCount + 1;
                    const res = await MemberGroupManager.layer_member_group(
                        parentLayerId, 
                        dataInputNm, 
                        dataFormID, 
                        keyword, 
                        nextPageCount,
                        apiUrl,
                        validationConfig
                    );
                    const options = MemberGroupManager._transformOptions(res);
                    if (options.length > 0) {
                        pageCount = nextPageCount;
                        const currentOptionsCount = comboBox.getOptions().length;
                        comboBox.updateOptions([...comboBox.getOptions(), ...options]);
                        requestAnimationFrame(() => {
                            comboBox.setFocusIndex(currentOptionsCount);
                        });
                    }
                },
                onComplete: () => {
                    const items = comboBox.getValues();
                    const parentElement = document.getElementById(parentLayerId);
                    if (!parentElement) return;

                    let hasNewItem = false;

                    items.forEach(item => {
                        if (document.getElementById(`${dataInputNm}_${item.id}`)) {
                            if (!window.NCDSAlert) {
                                alert('동일한 등급이 이미 존재합니다.');
                                return;
                            }

                            if (document.querySelector('.ncds-alert-modal')) {
                                return;
                            }
                            
                            NCDSAlert({ message: '동일한 등급이 이미 존재합니다.', iconType: 'error' });
                            return;
                        }

                        hasNewItem = true;
                        
                        const tagElement = new window.ncua.Tag({
                            text: item.label,
                            close: true,
                            size: 'sm',
                            onButtonClick: () => {
                                MemberGroupManager.removeMemberGroup(dataInputNm, item.id);
                            },
                        });

                        const tagWrapper = document.createElement('div');
                        tagWrapper.id = `${dataInputNm}_${item.id}`;
                        tagWrapper.innerHTML = `
                            <input type="hidden" name="${dataInputNm}[${item.id}]" value="${item.id}">
                            <input type="hidden" name="${dataInputNm}Nm[]" value="${item.label}">
                        `;
                        tagWrapper.appendChild(tagElement.element);
                        parentElement.appendChild(tagWrapper);
                    });

                    // 상태 초기화 (다음 검색을 위해)
                    pageCount = 1;
                    keyword = '';
                    
                    if (!hasNewItem) return;

                    if (!parentElement.classList.contains('active')) {
                        parentElement.classList.add('active');
                        if (!parentElement.querySelector('h5')) {
                            parentElement.insertAdjacentHTML('afterbegin', `<h5>${labelText}</h5>`);
                        }
                    }
                },
                onSearchAll: async () => {
                    try {
                        keyword = '';
                        pageCount = 1;
                        const res = await MemberGroupManager.layer_member_group(
                            parentLayerId, 
                            dataInputNm, 
                            dataFormID, 
                            keyword, 
                            pageCount,
                            apiUrl,
                            validationConfig
                        );
                        const options = MemberGroupManager._transformOptions(res);
                        comboBox.updateOptions(options);
                    } catch (error) {
                    }
                },
                onSelectAll: async () => {
                    const selectedValues = comboBox.getValues();
                    const getEnabledOptions = () => comboBox.getOptions()
                        .filter(option => !option.disabled);
                    const enabledOptions = getEnabledOptions();

                    if (selectedValues.length === enabledOptions.length) {
                        comboBox.scrollToBottom();
                        await new Promise((resolve) => setTimeout(resolve, 500));
                        const selectAllText = getEnabledOptions().length > enabledOptions.length ? '전체 선택' : '전체 해제';
                        comboBox.setSelectAllButtonText(selectAllText);
                        return;
                    }
                    comboBox.setSelectAllButtonText('전체 선택');
                },
            });

            // 라디오 버튼 변경 이벤트 등록
            const parentDiv = comboboxElement.closest('[data-combobox-id]');
            const memberGroupLayer = document.getElementById(parentLayerId);

            // removeMemberGroup용 매핑 등록
            if (!window.memberGroupRemoveMap) {
                window.memberGroupRemoveMap = {};
            }
            window.memberGroupRemoveMap[dataInputNm] = {
                parentLayerId
            };

            if (!parentDiv) {
                return comboBox;
            }

            const radioName = parentDiv.querySelector('input[type="radio"]')?.name;
            
            if (!radioName || !memberGroupLayer) {
                return comboBox;
            }

            // 라디오 버튼 변경 핸들러
            parentDiv.addEventListener('change', (e) => {
                if (e.target.name !== radioName) return;

                const isGroup = e.target.value === 'group';
                comboBox.setDisabled(!isGroup);
                
                const hasGroups = memberGroupLayer.querySelectorAll(`div[id^="${dataInputNm}"]`).length > 0;
                memberGroupLayer.classList.toggle('active', hasGroups && isGroup);
            });
            
            // 초기 상태 설정
            const checkedGroup = parentDiv.querySelector(`input[name="${radioName}"][value="group"]:checked`);
            comboBox.setDisabled(!checkedGroup);
            
            const hasGroups = memberGroupLayer.querySelectorAll(`div[id^="${dataInputNm}"]`).length > 0;
            memberGroupLayer.classList.toggle('active', hasGroups);
            
            return comboBox;
        }
    };

    // 전역 객체로 노출
    window.MemberGroupManager = MemberGroupManager;

    // HTML에서 호출을 위한 전역 함수 노출
    window.removeMemberGroup = (dataInputNm, id) => {
        MemberGroupManager.removeMemberGroup(dataInputNm, id);
    };

    window.initMemberGroupComboBox = (config) => {
        return MemberGroupManager.initMemberGroupComboBox(config);
    };

})();
