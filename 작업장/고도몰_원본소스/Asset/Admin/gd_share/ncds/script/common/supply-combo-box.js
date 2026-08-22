/**
 * 공급사(공급업체) 선택 ComboBox 공통 관리 모듈
 */
(function () {
    'use strict';
    function createObservable(v) {
        const ls = new Set();
        function obs(nv) { if (!arguments.length) return v; if (!Object.is(v, nv)) { v = nv; ls.forEach(f => f(v)); } return v; }
        obs.subscribe = f => (ls.add(f), () => ls.delete(f));
        return obs;
    }

    /**
     * 공급사 관리 모듈 (initSupplyComboBox 호출 즉시 자동 초기화)
     * @param {object} config
     * @param {string} config.scmLayerSelector
     * @param {string} config.dataInputNm
     * @param {string} config.comboBoxLayerId
     * @param {string} config.radioGroupClass
     * @param {Array} [config.initData]
     * @param {string} [config.apiUrl] - API URL (기본값: '../share/layer_scm.php')
     * @param {boolean} [config.multiple] - 다중 선택 여부 (기본값: true)
     * @param {string} [config.tagHiddenName] - 태그 hidden input name (선택)
     * @param {boolean} [config.tagCloseButton] - 태그 닫기 버튼 표시 여부 (기본값: true)
     * @param {string} [config.tagIdPrefix] - 태그 ID 접두사 (선택)
     * @returns {object} 공급사 관리 모듈
     */
    const SupplyComboBoxManager = {
        initSupplyComboBox: (config) => {
            const scmLayerSelector = config.scmLayerSelector;
            const dataInputNm = config.dataInputNm;
            const scmLayer = document.getElementById(scmLayerSelector);
            const comboBoxLayer = document.getElementById(config.comboBoxLayerId);
            const apiUrl = config.apiUrl || '../ncds/layer_scm.php';
            const scmDatas = createObservable(Array.isArray(config.initData) ? [...config.initData] : []);

            let page = 1;
            let keyword = '';
            let comboBox = null;
            let isLoadingOptions = false;

            /**
             * @type {boolean} isMultiple - 단일선택 콤보박스 옵션
             */
            const isMultiple = config.multiple ?? true;
            /**
             * @type {boolean} isTagCloseButton - 태그 닫기 버튼 표시 여부
             */
            const isTagCloseButton = config.tagCloseButton ?? true;
            /**
             * @type {string} tagHiddenName - hidden input name
             */
            const tagHiddenName = config.tagHiddenName;

            /**
             * @type {Array} supplyButtonNamesTypes - 공급사 input name 목록
             */
            const supplyButtonNamesTypes = config.supplyButtonNamesTypes || ['y', 'provider'];
            /**
             * @type {boolean} autoHideTagArea - 선택한 데이터가 없을때 태그 영역 자동 숨김 옵션
             */
            const autoHideTagArea = config.autoHideTagArea ?? true;

            /**
             * @returns {boolean} 라디오 버튼에서 공급사가 선택되었는지 판단
             */
            const isSupplyRadioSelected = () => {
                const scmFlRadioGroup = document.querySelector(`.${config.radioGroupClass}`);
                if (!scmFlRadioGroup) return false;
                const scmFlInputName = scmFlRadioGroup.querySelector('input[type="radio"], input[type="checkbox"]')?.name || 'scmFl';
                const checkedInput = scmFlRadioGroup.querySelector(`input[name="${scmFlInputName}"]:checked`);
                return supplyButtonNamesTypes.includes(checkedInput?.value);
            };

            const updateTagAreaVisibility = () => {
                const isSupplySelected = isSupplyRadioSelected();
                const shouldShowLayer = autoHideTagArea
                    ? (isSupplySelected && scmDatas().length > 0)
                    : isSupplySelected;
                toggleScmLayer(shouldShowLayer);
            };

            const setupRadioButtons = () => {
                const scmFlRadioGroup = document.querySelector(`.${config.radioGroupClass}`);
                if (!scmFlRadioGroup || !comboBox || !scmLayer) return;

                const isSupplySelected = isSupplyRadioSelected();
                const scmFlInputName = scmFlRadioGroup.querySelector('input[type="radio"], input[type="checkbox"]')?.name || 'scmFl';

                const updateSupplyState = (isSupplySelected) => {
                    comboBox.setDisabled(!isSupplySelected);
                    updateTagAreaVisibility();
                };

                updateSupplyState(isSupplySelected);

                scmFlRadioGroup.addEventListener('change', (e) => {
                    const target = e.target;
                    if (!target || target.name !== scmFlInputName) return;
                    if (target.type !== 'radio' && target.type !== 'checkbox') return;
                    
                    const isSupplySelected = supplyButtonNamesTypes.includes(target.value);
                    updateSupplyState(isSupplySelected);
                });
            };

            const tagUtils = {
                useOtherTagId: () => {
                    return config.tagIdPrefix !== undefined;
                },
                getTagIdPrefix: () => {
                    if (tagUtils.useOtherTagId()) {
                        return config.tagIdPrefix;
                    }
                    return dataInputNm;
                },
                clearAllTags: () => {
                    if (!scmLayer) return;
                    scmLayer.querySelectorAll('.ncua-tag').forEach(tag => tag.remove());
                }
            };

            const scmDataParser = (list) => {
                if (!Array.isArray(list)) {
                    return [];
                }
                const currentValues = comboBox.getOptions() || [];
                const ids = new Set(currentValues.map(value => value.id));

                return list.reduce((acc, data) => {
                    if (ids.has(data.scmNo)) {
                        return acc;
                    }
                    ids.add(data.scmNo);

                    if (data.scmType === 'x') {
                        acc.push({
                            id: data.scmNo,
                            label: `${data.companyNm} (탈퇴)`,
                            disabled: true
                        });
                    } else {
                        acc.push({
                            id: data.scmNo,
                            label: data.companyNm
                        });
                    }
                    return acc;
                }, []);
            };

            const validators = {
                selectSupply: () => NCDSAlert({ message: '공급사를 선택해 주세요.', iconType: 'error' }),
                alreadyExistsSupply: () => NCDSAlert({ message: '동일한 공급사가 이미 존재합니다.', iconType: 'error' }),
                error: (message) => NCDSAlert({ message: message, iconType: 'error' }),
                alreadyExistsSupplyCount: (selectedOptions, duplicateDatas) => {
                    NCDSAlert({ message: `선택한 ${selectedOptions.length}개의 공급사 중 ${selectedOptions.length - duplicateDatas.length}개의 공급사가 추가되었습니다.`, iconType: 'info' });
                }
            };

            const scmDataUtils = {
                add: (items) => {
                    scmDatas([...scmDatas(), ...items]);
                },
                remove: (id) => {
                    scmDatas(scmDatas().filter(scm => scm.id !== id));
                },
                replace: (items) => {
                    scmDatas([...items]);
                },
                clear: () => {
                    scmDatas([]);
                },
                has: (id) => {
                    return scmDatas().some(scm => scm.id === id);
                }
            };

            const ncdsLayerRegister = async (isDisabled, params) => {
                const addParam = {
                    mode: 'checkbox',
                    layerFormID: 'addSearchForm',
                    dataFormID: 'info_scm',
                    parentFormID: 'scmLayer',
                    dataInputNm: 'scmNo',
                    key: 'companyNm',
                    childRow: '',
                    callFunc: '',
                    scmCommissionSet: '',
                    keyword: keyword,
                    pagelink: params?.pagelink || page
                };
                const searchParams = new URLSearchParams({ ...addParam, ...params });
                try {
                    const response = await fetch(`${apiUrl}?${searchParams.toString()}`);
                    return await response.json();
                } catch (error) {
                    if (window.NCDSAlert) validators.error(error.message);
                    return [];
                }
            };

            const renderTags = () => {
                if (!scmLayer) return;
                tagUtils.clearAllTags();

                scmDatas().forEach(data => {
                    if (data.disabled) return;
                    const tagElement = new ncua.Tag({
                        text: data.label,
                        size: 'sm',
                        close: isTagCloseButton,
                        onButtonClick: () => {
                            tagElement.destroy();
                            tagElement.getElement().remove();
                            scmDataUtils.remove(data.id);

                            if (!isMultiple) {
                                comboBox.setValues([]);
                            }
                        }
                    });
                    tagElement.element.id = `${tagUtils.getTagIdPrefix()}_${data.id}`;

                    const inputElement = createHiddenInputElement(data);

                    if (tagUtils.useOtherTagId()) {
                        const inputNmElement = createHiddenInputNmElement(data);
                        tagElement.element.prepend(inputNmElement);
                    }

                    scmLayer.appendChild(tagElement.element);
                    tagElement.element.prepend(inputElement);
                });
            };

            const createHiddenInputElement = (data) => {
                const inputElement = document.createElement('input');
                inputElement.type = 'hidden';
                inputElement.value = data.id;
                const inputName = tagUtils.useOtherTagId()
                    ? dataInputNm
                    : scmLayerSelector;
                inputElement.name = isMultiple ? `${inputName}[]` : inputName;
                return inputElement;
            }

            const createHiddenInputNmElement = (data) => {
                const inputNmElement = document.createElement('input');
                inputNmElement.type = 'hidden';
                inputNmElement.value = data.label;
                inputNmElement.name = `${tagHiddenName}${isMultiple ? '[]' : ''}`;
                return inputNmElement;
            }

            const setupComboBox = () => {
                comboBox = new ncua.ComboBox(comboBoxLayer, {
                    options: [],
                    placeholder: '공급사 선택',
                    multiple: isMultiple,
                    size: 'xs',
                    onSearch: async (searchValue) => {
                        page = 1;
                        keyword = searchValue;
                        isLoadingOptions = true;
                        comboBox.updateOptions([]);
                        const response = await ncdsLayerRegister(false);
                        const processedResponse = scmDataParser(response);
                        comboBox.updateOptions(processedResponse);
                        isLoadingOptions = false;
                        if (!isMultiple) {
                            comboBox.open();
                        }
                    },
                    onScrollBottom: async () => {
                        const nextPage = page + 1;
                        const response = await ncdsLayerRegister(false, {
                            pagelink: nextPage
                        });
                        const processedResponse = scmDataParser(response);
                        if (processedResponse.length > 0) {
                            page = nextPage;
                            const currentOptions = comboBox.getOptions();
                            const currentOptionsCount = currentOptions.length;
                            const mergedOptions = [...currentOptions, ...processedResponse];
                            comboBox.updateOptions(mergedOptions);
                            requestAnimationFrame(() => {
                                comboBox.setFocusIndex(currentOptionsCount);
                            });
                        }
                    },
                    onComplete: () => {
                        const selectedOptions = comboBox.getValues();
                        if (selectedOptions.length === 0 && window.NCDSAlert) {
                            validators.selectSupply();
                            return;
                        }
                        const duplicateItems = scmDatas().filter(scm =>
                            selectedOptions.some(option => option.id === scm.id)
                        );

                        if (duplicateItems.length === 0) {
                            scmDataUtils.add(selectedOptions);
                            return;
                        }

                        if (duplicateItems.length === selectedOptions.length) {
                            validators.alreadyExistsSupply();
                            return;
                        }

                        validators.alreadyExistsSupplyCount(selectedOptions, duplicateItems);
                        const newItems = selectedOptions.filter(option =>
                            !scmDataUtils.has(option.id)
                        );
                        scmDataUtils.add(newItems);
                    },
                    onSearchAll: async () => {
                        try {
                            isLoadingOptions = true;
                            comboBox.updateOptions([]);
                            keyword = '';
                            page = 1;
                            const response = await ncdsLayerRegister(false);
                            const processedTagDatas = scmDataParser(response);
                            comboBox.updateOptions(processedTagDatas);
                            isLoadingOptions = false;
                        } catch (error) {
                            isLoadingOptions = false;
                            if (window.NCDSAlert) {
                                validators.error(error.message);
                            }
                        }
                    },
                    onChange: () => {
                        if (isMultiple || isLoadingOptions) {
                            return;
                        }
                        const selectedOptions = comboBox.getValues();
                        if (selectedOptions.length === 0 && window.NCDSAlert) {
                            validators.selectSupply();
                            return;
                        }

                        const hasDuplicate = selectedOptions.some(option => scmDataUtils.has(option.id));
                        if (hasDuplicate && window.NCDSAlert) {
                            validators.alreadyExistsSupply();
                            comboBox.setValues([]);
                            return;
                        }
                        scmDataUtils.replace(selectedOptions);
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
            };

            const toggleScmLayer = (isShow) => {
                if (!scmLayer) return;
                if (isShow) {
                    scmLayer.removeAttribute('style');
                } else {
                    scmLayer.style.display = 'none';
                }
            };

            scmDatas.subscribe(function (data) {
                renderTags();
                updateTagAreaVisibility();
            });

            setupComboBox();
            setupRadioButtons();
            renderTags();

            // public API
            return {
                getDatas: () => [...scmDatas()],
                scmDatas,
                comboBox,
                renderTags,
                toggleScmLayer,
            };
        }
    };

    window.SupplyComboBoxManager = SupplyComboBoxManager;
    window.initSupplyComboBox = config => SupplyComboBoxManager.initSupplyComboBox(config);
})();
