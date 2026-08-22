<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/goods/present-config.css')?>" rel="stylesheet"/>
<script defer type="text/javascript" src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/switch.js')?>" ></script>
<script defer type="text/javascript" src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/charcount.js')?>" ></script>
<script defer type="text/javascript" src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/notification.js')?>" ></script>
<script defer type="text/javascript" src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-multi-select/ncds-multi-select.js')?>" ></script>
<script defer type="text/javascript" src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/ncds-jquery-validator.js')?>" ></script>

<article class="ncua-content present-config">
    <header class="ncua-page-header page-header js-affix">
        <h3 class="ncua-help-manual">
            <?= end($naviMenu->location); ?>
        </h3>

        <div class="ncua-page-header__actions">
            <button type="button" class="ncua-btn ncua-btn--md ncua-btn--primary save-btn">저장</button>
        </div>
    </header>

    <?php if ($showPresentBanner !== false): ?>
    <div class="ncua-full-width-notification ncua-full-width-notification--info" role="alert">
        <div class="ncua-full-width-notification__container">
        <div class="ncua-full-width-notification__content">
            <div class="ncua-full-width-notification__content-wrapper">
            <div class="ncua-info-circle-violet"></div>
            <div class="ncua-full-width-notification__text-container">
                <span class="ncua-full-width-notification__title">SMS, 알림톡 사용설정이 필수로 되어야 해요.</span>
                <a href="<?= URI_ADMIN ?>crm/auto_send.php" class="ncua-full-width-notification__link" target="_blank">
                    <span class="ncua-full-width-notification__supporting-text">SMS,알림톡 사용 설정</span>
                </a>
            </div>
            </div>
            <div class="ncua-full-width-notification__actions-container">
            <button
                type="button"
                class="ncua-notification__action-button ncua-notification__action-button--text ncua-full-width-notification__link"
                onclick="dismissBanner()"
                >
                다시보지 않기
            </button>
            <button type="button" class="ncua-full-width-notification__close-button ncua-close-icon-violet" aria-label="알림 닫기" onclick="NCDSNotification.close('.ncua-full-width-notification');">
            </button>
            </div>
        </div>
        </div>
    </div>
    <?php endif;?>

    <form id="presentConfigForm">
        <input type="hidden" name="mode" value="presentConfig">
    <!-- 기능설정 -->
    <?php include $functionConfig ?>

    <!-- 선물카드 설정 -->
    <?php include $giftCardConfig ?>

    <!-- 상품 설정 -->
    <?php include $goodsConfig ?>
    </form>

    <div style="display: none;">
        <?php // 팝업 속 선택된 상품 노출을 위한 테이블  ?>
        <table class="apply-goods-table js-track-change" <?= ($data['applyType'] === 'applyGoods') ? 'id="tbl_add_goods_set"' : '' ?>>
            <tbody>
            <?php if ($data['applyType'] === 'applyGoods'): ?>
            <?php foreach ($presentGoodsDatas as $key => $val): ?>
                <tr id="tbl_add_goods_<?= $val['goodsNo'] ?>">
                    <td>
                        <input type="hidden" name="itemGoodsNm[]" value="<?= strip_tags($val['goodsNm']) ?>"/>
                        <input type="hidden" name="itemGoodsNo[]" id="<?=$goodsType?>_present_goods_<?= $val['goodsNo'] ?>" value="<?= $val['goodsNo'] ?>"/>
                    </td>
                    <td class="center" data-name="goodsImage">
                        <?= gd_html_goods_image($val['goodsNo'], $val['imageName'], $val['imagePath'], $val['imageStorage'], 30, $val['goodsNm'], '_blank'); ?>
                    </td>
                    <td data-name="goodsPrice" data-value="<div><?=$val['goodsPrice']?></div>"></td>
                    <td data-name="scmNm" data-value="<div><?=$val['scmNm']?></div>"></td>
                    <td data-name="totalStock" data-value="<div><?=$val['totalStock']?></div>"></td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        <table class="except-goods-table js-track-change" <?= ($data['applyType'] === 'category') ? 'id="tbl_add_goods_set"' : '' ?>>
            <tbody>
            <?php if ($data['applyType'] === 'category'): ?>
            <?php foreach ($presentGoodsDatas as $key => $val): ?>
                <tr id="tbl_add_goods_<?= $val['goodsNo'] ?>">
                    <td>
                        <input type="hidden" name="itemGoodsNm[]" value="<?= strip_tags($val['goodsNm']) ?>"/>
                        <input type="hidden" name="itemGoodsNo[]" id="<?=$goodsType?>_present_goods_<?= $val['goodsNo'] ?>" value="<?= $val['goodsNo'] ?>"/>
                    </td>
                    <td class="center" data-name="goodsImage">
                        <?= gd_html_goods_image($val['goodsNo'], $val['imageName'], $val['imagePath'], $val['imageStorage'], 30, $val['goodsNm'], '_blank'); ?>
                    </td>
                    <td data-name="goodsPrice" data-value="<div><?=$val['goodsPrice']?></div>"></td>
                    <td data-name="scmNm" data-value="<div><?=$val['scmNm']?></div>"></td>
                    <td data-name="totalStock" data-value="<div><?=$val['totalStock']?></div>"></td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</article>

<script type="text/javascript">
    const code = '251119001';
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
        });
    }
</script>

<script type="text/javascript">
    // 배너 다시 보지 않기
    function dismissBanner() {
        NCDSNotification.close('.ncua-full-width-notification');

        const formData = new FormData();
        formData.append('mode', 'showPresentBanner');

        const errorAlert = () => {
            NCDSAlert({
                message: '요청하신 작업을 완료하지 못했습니다.<br>잠시 후 다시 시도해 주세요.',
                iconType: 'error'
            });
        }

        fetch("/goods/present_config_ps.php", {
            method: "POST",
            body: formData,
        }).then((response) => response.json())
            .then(data => {
                if (data.result !== 'success') {
                    errorAlert();
                }
            })
            .catch(() => {
                errorAlert();
            });
    }

    // 선물하기 설정 - 저장
    const goodsLimit = <?=($goodsLimit)?>;
    let isModify = false;  // 수정 여부
    let allowUnload = false;  // 이탈 가능 여부
    const saveBtn = document.querySelector('.save-btn');

    // 변경 감지
    document.addEventListener('change', (e) => {
        if (!e.target.closest('.js-track-change')) return;
        isModify = true;
    });

    // F5 / 닫기 / 주소이동용 이탈감지
    window.addEventListener('beforeunload', (e) => {
        if (!isModify) return;
        if (allowUnload) return;

        e.preventDefault();
        e.returnValue = '';
    });

    // 링크 클릭 이탈 감지
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (!link) return;

        const href = link.getAttribute('href');

        // href 없거나 이동 목적 아님
        if (!href) return;
        if (href === '#') return;
        if (href.startsWith('javascript:')) return;

        // 새 탭 / 다운로드는 제외
        if (link.target === '_blank') return;
        if (link.hasAttribute('download')) return;

        if (!isModify) return;

        if (!confirm('이 페이지를 떠나시겠습니까? 변경사항이 저장되지 않을 수 있습니다.')) {
            e.preventDefault();
            return;
        }

        allowUnload = true;  // 이탈 허용
    });

    // 저장 이벤트
    saveBtn.addEventListener("click", () => {
        const form = document.getElementById("presentConfigForm");
        const formData = new FormData(form);
        const goodsNoData = document.querySelectorAll('#tbl_add_goods_set input[name="itemGoodsNo[]"]');

        if (goodsNoData.length > 0) {
            goodsNoData.forEach(element => {
                formData.append('goodsNo[]', element.value);
            });
        } else {
            formData.append('goodsNo[]', "");
        }

        fetch("/goods/present_config_ps.php", {
            method: "POST",
            body: formData,
        }).then((response) => response.json())
            .then((data) => {
                switch (data.result) {
                    case 'success':
                        isModify = false;

                        const duration = 3000;
                        NCDSToast({
                            message: "저장이 완료 되었습니다.",
                            color: 'success',
                            autoClose: duration,
                        });

                        setTimeout(() => {
                            location.reload();
                        }, duration);
                        break;
                    case 'fail_goods_limit':
                        let messageGoodsType = '';
                        const goodsType = selectedGoodsType();
                        if (goodsType === 'applyGoods') {
                            goodsInfo = applyGoodsInfo;
                            messageGoodsType = '직접선택';
                        } else if (goodsType === 'exceptGoods') {
                            goodsInfo = exceptGoodsInfo;
                            messageGoodsType = '예외상품';
                        }

                        NCDSAlert({
                            message: `최대 ${goodsLimit.toLocaleString()}개까지 등록할 수 있습니다.`,
                            subMessage: `${messageGoodsType} 옵션은 최대 ${goodsLimit.toLocaleString()}개까지 등록이 가능합니다.<br>이미 등록한 상품을 삭제한 후 재시도 해주세요.`,
                            iconType: 'error'
                        });
                        break;
                    case 'fail_card_display':
                        NCDSAlert({
                            message: `선물카드를 <?=$minDisplayCardCount?>개 이상 선택해 주세요.`,
                            iconType: 'error'
                        });
                        break;
                    case 'fail_card_validate':
                        NCDSAlert({
                            message: '업로드 가능한 용량, 형식을 확인해주세요.',
                            subMessage: [
                                '사이즈: 800 x 400 px',
                                '확장자: JPG, PNG',
                                '파일크기: 최대 500KB<br>',
                                '가로:세로 비율이 2:1이 아닐경우, 500 x 250 px이하일 경우',
                                '업로드가 불가능합니다.',
                            ].join('<br>'),
                            iconType: 'error'
                        });
                        break;
                    case 'fail_card_message_length':
                        // default 메시지 사용
                    default:
                        NCDSAlert({
                            message: "저장에 실패하였습니다. 다시 시도해 주세요.",
                            iconType: 'error'
                        });
                }
            })
            .catch(() => {
                NCDSAlert({
                    message: "저장에 실패하였습니다. 다시 시도해 주세요.",
                    iconType: 'error'
                });
            });
    });

    // 기능설정 > 선물하기 기능
    const giftUseFlInputs = document.querySelectorAll('input[name="useFl"]');
    const goodsConfig = document.querySelector('.present-config__goods-config');
    const giftCardConfig = document.querySelector('.present-config__gift-card-config');
    const autoCancel = document.querySelector('.present-config__auto-cancel');

    // 표시/숨김 처리 함수
    const toggleConfigVisibility = (value) => {
        const isHidden = (value !== 'y');
        const configElements = [goodsConfig, giftCardConfig, autoCancel];

        configElements.forEach(element => {
            if (element) {
                element.classList.toggle('display-none', isHidden);
            }
        });
    };

    // 모든 라디오 버튼에 이벤트 리스너 추가
    giftUseFlInputs.forEach(input => {
        input.addEventListener('change', (e) => {
            toggleConfigVisibility(e.target.value);
        });
    });

    // 초기 로드 시 현재 선택된 값에 따라 표시/숨김 처리
    const checkedInput = document.querySelector('input[name="useFl"]:checked');
    if (checkedInput) {
        toggleConfigVisibility(checkedInput.value);
    }

    // 상품 설정 > goodsConfig 라디오 버튼
    const goodsConfigInputs = document.querySelectorAll('input[name="applyType"]');
    const goodsConfigCategory = document.querySelector('.present-config__goods-config-category');
    const goodsConfigSelected = document.querySelector('.present-config__goods-config-selected');
    const goodsExceptionConfig = document.querySelector('.present-config__goods-exception-config');

    // goodsConfig 선택값에 따른 영역 표시/숨김 처리 함수
    const toggleGoodsConfigVisibility = (value) => {
        if (!goodsConfigCategory || !goodsConfigSelected || !goodsExceptionConfig) {
            return;
        }

        switch(value) {
            case 'all':
                // 모든 상품 선택 시: 모든 영역 숨김
                goodsConfigCategory.classList.add('display-none');
                goodsConfigSelected.classList.add('display-none');
                goodsExceptionConfig.classList.add('display-none');
                break;
            case 'category':
                // 카테고리별 선택 시: category와 exception-config 영역만 노출
                goodsConfigCategory.classList.remove('display-none');
                goodsConfigSelected.classList.add('display-none');
                goodsExceptionConfig.classList.remove('display-none');
                break;
            case 'applyGoods':
                // 직접선택 선택 시: selected 영역만 노출
                goodsConfigCategory.classList.add('display-none');
                goodsConfigSelected.classList.remove('display-none');
                goodsExceptionConfig.classList.add('display-none');
                break;
        }
    };

    // goodsConfig 라디오 버튼에 이벤트 리스너 추가
    goodsConfigInputs.forEach(input => {
        input.addEventListener('change', (e) => {
            toggleGoodsConfigVisibility(e.target.value);
            switchSelectedTableId(e.target.value);
        });
    });

    // 초기 로드 시 현재 선택된 값에 따라 표시/숨김 처리
    const checkedGoodsConfigInput = document.querySelector('input[name="applyType"]:checked');
    if (checkedGoodsConfigInput) {
        toggleGoodsConfigVisibility(checkedGoodsConfigInput.value);
    }

    // 예외/직접선택에 따른 데이터 스위칭
    function switchSelectedTableId(apply_type) {
        // 모든 상품일땐 id 스위칭 제외
        if (apply_type === 'all') {
            return;
        }
        document.querySelectorAll('#tbl_add_goods_set').forEach(el => {
            el.removeAttribute('id');
        });

        let target;
        let prefix = '';
        if (apply_type === 'category') {
            prefix = 'exceptGoods';
            target = document.querySelector('.except-goods-table');
        } else if (apply_type === 'applyGoods') {
            prefix = 'applyGoods';
            target = document.querySelector('.apply-goods-table');
        }
        if (target) {
            target.id = 'tbl_add_goods_set';
            goodsPager.updatePrefix(prefix);
        }
    }

    // 체크박스 해제
    const resetCheckAll = (targetElement) => {
        const checkAll = targetElement
            .closest('.ncua-search-result__content')
            ?.querySelector('.js-checkall');

        if (checkAll) {
            checkAll.checked = false;
        }
    }

    // 선물하기 설정 > 선물카드 설정
    let cardCount = 0;  // 카드 전역 카운터
    const cardTextInputQuery = '.present-card-message-field input[type="text"]';
    const addCardBtn = document.querySelector('.add-card-btn');
    const newCardImage = document.querySelector('#new-card-image');
    const cardTable = document.querySelector('.present-config__gift-card-table table');
    const deleteCardBtn = document.querySelector('.delete-card-btn');

    // 글자수 카운터 매니저 (지연 초기화)
    let cardMessageCharCountManager = null;
    
    // createCharCountManager 초기화 함수
    const initCharCountManager = () => {
        if (typeof createCharCountManager === 'undefined') {
            return false;
        }
        if (!cardMessageCharCountManager) {
            cardMessageCharCountManager = createCharCountManager({
                targetClasses: ['present-card-message']
            });
            // 초기화하여 모든 요소의 글자수를 업데이트
            cardMessageCharCountManager.init();
        }
        return true;
    };

    // 에러 검증 함수
    const validateCardMessageInput = (input) => {
        const ncuaInput = input.closest('.ncua-input');
        const currentLength = input.value.length;
        
        if (currentLength === 0) {
            // 에러 표시
            if (ncuaInput && typeof NCDSValidator !== 'undefined') {
                NCDSValidator.highlight(input);
                NCDSValidator.addErrorIcon(input);
                
                // 힌트 텍스트 표시
                if (ncuaInput.hasAttribute('data-show-hint-text') && ncuaInput.getAttribute('data-show-hint-text') === 'true') {
                    let hintText = ncuaInput.querySelector('.ncua-hint-text');
                    if (!hintText) {
                        hintText = NCDSValidator.createHintTextElement();
                        ncuaInput.appendChild(hintText);
                    }
                    hintText.classList.add(NCDSValidator.ERROR_CLASS_NAME);
                    hintText.textContent = '0자 이상 30자 이하로 입력해주세요.';
                }
            }
        } else {
            // 에러 제거
            if (ncuaInput && typeof NCDSValidator !== 'undefined') {
                NCDSValidator.unhighlight(input);
                NCDSValidator.removeErrorIcon(input);
                NCDSValidator.removeHintText(input);
            }
        }
    }

    // input 카운터 업데이트 (createCharCountManager 사용)
    const updateText = (input) => {
        // 첫글자 공백 제거
        input.value = input.value.trimStart();

        // createCharCountManager가 로드되었는지 확인하고 초기화
        if (initCharCountManager() && cardMessageCharCountManager) {
            // 글자수 카운터 업데이트
            cardMessageCharCountManager.update(input);
        }
        // 에러 검증
        validateCardMessageInput(input);
    }

    // 카드 노출 유효성 검사
    const validateDisplayFl = (target) => {
        const radios = document.querySelectorAll(
            'input[type="radio"][name^="card["][name$="[displayFl]"]'
        );

        const isDisplayEnabled = [...radios].some(r => r.checked && r.value === 'y');

        if (!isDisplayEnabled) {
            NCDSAlert({
                message: '선물카드를 <?=$minDisplayCardCount?>개 이상 선택해 주세요.',
                iconType: 'error',
                callback: () => {
                    // 체크 다시 돌려놓기
                    document.querySelector(
                        `input[type="radio"][name="${CSS.escape(target.name)}"][value="y"]`
                    ).checked = true;
                }
            });
        }
    }

    // 카드 동적 생성 input 대응
    document.addEventListener('input', function (e) {
        const target = e.target;

        // 카드 글자 수 카운터
        if (target.matches(cardTextInputQuery)) {
            updateText(target);
        }

        // 카드 노출 유효성 검사
        if (target.type === 'radio' && target.name.includes('[displayFl]')) {
            validateDisplayFl(target);
        }
    });

    // 카드 input 초기값 반영 (스크립트 로드 후 실행)
    const initCardInputCounters = () => {
        if (initCharCountManager()) {
            // createCharCountManager.init()이 이미 모든 요소의 글자수를 업데이트함
            // 에러 검증만 별도로 수행
            document.querySelectorAll(cardTextInputQuery)
                .forEach(validateCardMessageInput);
        } else {
            // createCharCountManager가 아직 로드되지 않았으면 재시도
            setTimeout(initCardInputCounters, 100);
        }
    };
    
    // DOMContentLoaded 이벤트에서 초기화 시도
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCardInputCounters);
    } else {
        // 이미 로드된 경우 즉시 실행
        initCardInputCounters();
    }

    // 파일 트리거
    addCardBtn.addEventListener('click', () => {
        const card_limit = parseInt('<?= $cardLimit ?>');
        const upload_type = document.querySelectorAll('.upload-type');
        const count = Array.from(upload_type)
            .filter(el => el.innerText.trim() === '직접등록')
            .length;

        if (count >= card_limit) {
            NCDSAlert({
                message: `최대 ${card_limit}개까지 등록할 수 있습니다.`,
                subMessage: `선물카드는 최대 ${card_limit}개까지 등록이 가능합니다.<br/>이미 등록한 이미지를 삭제한 후 재시도 해주세요.`,
                iconType: 'error'
            });
            return;
        }
        newCardImage.click()
    });

    // 카드 추가 이벤트
    newCardImage.addEventListener('change', () => {
        const file = newCardImage.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('presentCard', file);

        // 카드 이미지 유효성 검사
        fetch("/goods/present_card_valid_ps.php", {
            method: "POST",
            body: formData,
        }).then((response) => response.json())
            .then(async (data) => {
                if (data.result === 'success') {
                    // 카운터 증가
                    cardCount++;

                    // 성공 시 tr 생성 및 파일 이동
                    await createNewCard(file);
                    moveCardImageFile(file);
                    updateCardSortOrder();
                } else {
                    NCDSAlert({
                        message: '업로드 가능한 용량, 형식을 확인해주세요.',
                        subMessage: [
                            '사이즈: 800 x 400 px',
                            '확장자: JPG, PNG',
                            '파일크기: 최대 500KB<br>',
                            '가로:세로 비율이 2:1이 아닐경우, 500 x 250 px이하일 경우',
                            '업로드가 불가능합니다.',
                        ].join('<br>'),
                        iconType: 'error'
                    });
                }
            })
            .catch(() => {
                NCDSAlert({
                    message: "처리중에 오류가 발생하여 실패하였습니다."
                });
            })
            .finally(() => {
                newCardImage.value = '';
            })
    });

    // 새로운 카드 생성
    function createNewCard(file) {
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const template = document.querySelector('#card-row-template');
                const row = template.content.firstElementChild.cloneNode(true);

                // row 데이터 name, value 변경
                row.querySelectorAll('[data-name]').forEach(el => {
                    el.name = el.dataset.name.replace('__INDEX__', `${cardCount}`);

                    if (el.dataset.value) {
                        el.value = el.dataset.value.replace('__INDEX__', `${cardCount}`);
                    }
                });

                // 이미지 src 변경
                const imgEl = row.querySelector('img[data-src]');
                imgEl.src = imgEl.dataset.src.replace('__IMG__', e.target.result);

                // row 추가
                cardTable.querySelector('tbody').prepend(row);

                // 글자 수 초기화
                row.querySelectorAll(cardTextInputQuery).forEach(el => {
                    updateText(el);
                });

                resolve();
            };

            reader.readAsDataURL(file);
        })
    }

    // 생성된 태그 하위 file로 이미지 옮기기
    function moveCardImageFile(file) {
        const data_transfer = new DataTransfer();
        data_transfer.items.add(file);

        const newImageInput = document.querySelector(`[name="card_image[${cardCount}]"]`);
        newImageInput.files = data_transfer.files
    }

    // 순번 재배치
    function updateCardSortOrder() {
        const card_sorts = document.querySelectorAll('.card-sort');
        let sort_index = 1;
        Array.from(card_sorts).forEach((card_sort) => {
            card_sort.textContent = `${sort_index}`;
            sort_index++;
        });
    }

    // 카드 삭제 이벤트
    deleteCardBtn.addEventListener('click', () => {
        let isChecked = cardTable.querySelectorAll('.delete-card-sno:checked').length > 0;
        if (!isChecked) {
            NCDSAlert({
                message: '선택된 카드가 없습니다.',
                iconType: 'error',
            });
            return false;
        }

        NCDSConfirm({
            message: '선택하신 선물카드가 삭제됩니다. 진행 하시겠습니까?',
            callback: (result) => {
                if (result) {
                    let deleteCards = [];
                    const deleteCardSnos = document.querySelector('.delete-card-snos');

                    if (deleteCardSnos.value.trim() !== '') {
                        deleteCards = deleteCardSnos.value.split(',');
                    }

                    cardTable.querySelectorAll('.delete-card-sno:checked').forEach(checkbox => {
                        if (checkbox.value !== '') {
                            deleteCards.push(checkbox.value);
                        }

                        const tr = checkbox.closest('tr');
                        if (tr) tr.remove();
                    })

                    deleteCards = [...new Set(deleteCards)];
                    deleteCardSnos.value = deleteCards.join(',');
                    updateCardSortOrder();
                    initSelectDeleteButtons();
                }
            }
        });
    });

    // 선물하기 설정 > 상품 설정
    let exceptGoodsInfo = [];
    let applyGoodsInfo = [];
    const popupBtn = document.querySelectorAll('.select-goods-popup');
    const goodsDeleteBtn = document.querySelectorAll('.delete-check-goods');
    const categoryCheckbox = Array.from(
        document.querySelectorAll('input[type="checkbox"][name^="cateCd["]')
    );
    const categoryCheckAll = document.querySelector('.js-checkall[data-target-name="cateCd"]');
    const CATEGORY_UNIT = 3;
    const isTopCategory = (cateCd) => cateCd.length === CATEGORY_UNIT;

    // 레이아 팝업 정보 전달 및 노출
    Array.from(popupBtn).forEach((btn) => {
        btn.addEventListener('click', () => {
            const goodsType = selectedGoodsType();

            // 선택 상품 수 검증
            if (!validGoodsLimit(goodsType)) {
                goodsLimitErrorMessage(goodsType);
                return;
            }

            // 상품 레이어 노출
            const addParam = {
                "mode": "select",
                "layerFormID": "addGoodsForm",
                "parentFormID": "present",
                "dataFormID": `${btn.dataset.type}_present_goods`,
                "layerTitle": btn.dataset.title,
                "dataInputNm": "itemGoodsNo",
                "callFunc": "setAddGoods",
            };
            layer_add_info("goods", addParam);
        });
    });

    // 상품 삭제
    Array.from(goodsDeleteBtn).forEach((Btn) => {
        Btn.addEventListener('click', () => {
            const type = Btn.dataset.type;

            NCDSConfirm({
                message: '삭제 하시겠습니까?',
                subMessage: `해당 상품은 '선물하기' 버튼이 ${type === 'exceptGoods' ? '노출' : '미노출'}됩니다.`,
                callback: (result) => {
                    if (result) {
                        const goodsInput = document.querySelectorAll(`input[name="${type}-goodsNo[]"]:checked`);

                        // 테이블 제거, 보이는 테이블 제거
                        const table = document.querySelector('#tbl_add_goods_set');
                        const target_tbody = document.querySelector(`.${type}-goods-table-body`);
                        goodsInput.forEach(element => {
                            table.querySelector(`#tbl_add_goods_${element.value}`)?.remove();
                            target_tbody.querySelector(`input[name="${type}-goodsNo[]"][value="${element.value}"]`)?.closest('tr').remove();
                        });

                        initGoodsInfoFromHTML();
                        goodsPager.refresh();
                        resetCheckAll(Btn);
                    }
                }
            });
        })
    });

    // 하위 카테고리 자동 체크 이벤트
    categoryCheckbox.forEach((checkbox) => {
        checkbox.addEventListener('click', () => {
            checkCategory(categoryCheckbox, checkbox.value, checkbox.checked);
        })
    })

    // 하위 카테고리 자동 체크
    function checkCategory(elements, cateCd, checked) {
        elements.forEach((child) => {
            if (
                child.value !== cateCd &&
                child.value.startsWith(cateCd)
            ) {
                child.checked = checked;
                child.disabled = checked;  // UI 잠금용
            }
        });
    }

    // 카테고리 체크 초기화
    categoryCheckbox
        .filter(category => category.checked)
        .forEach(category => {
            checkCategory(categoryCheckbox, category.value, true);
        });

    // 카테고리 전체 선택 시 하위 카테고리 이벤트 블럭
    categoryCheckAll.addEventListener('click', () => {
        const checked = categoryCheckAll.checked;

        categoryCheckbox.forEach((checkbox) => {
            if (isTopCategory(checkbox.value)) {
                checkbox.checked = checked;

                // 상위 기준으로 하위 자동 처리
                checkCategory(categoryCheckbox, checkbox.value, checked);
            }
        });
    })

    // 상품 정보 초기화
    function initGoodsInfoFromHTML() {
        let goodsInfo;
        const rows = document.querySelectorAll('#tbl_add_goods_set tbody tr');

        goodsInfo = Array.from(rows).map(row => ({
                goodsNm: row.querySelector('[name="itemGoodsNm[]"]').value,
                goodsNo: parseInt(row.querySelector('[name="itemGoodsNo[]"]').value),
                goodsImg: row.querySelector('[data-name="goodsImage"] img').src,
                goodsPrice: row.querySelector('[data-name="goodsPrice"]').dataset.value,
                scmNm: row.querySelector('[data-name="scmNm"]').dataset.value,
                totalStock: row.querySelector('[data-name="totalStock"]').dataset.value,
        }));

        const apply_type = document.querySelector('input[name="applyType"]:checked').value;
        if (apply_type === 'category') {
            exceptGoodsInfo = goodsInfo;
        } else if (apply_type === 'applyGoods') {
            applyGoodsInfo = goodsInfo;
        }
    }
    initGoodsInfoFromHTML();

    // 레이어 팝업 후 콜백 함수
    function setAddGoods(jsonData) {
        try {
            let noDisplayAlert = false;
            let goodsInfo = [];
            const goodsType = selectedGoodsType();
            if (goodsType === 'applyGoods') {
                goodsInfo = applyGoodsInfo;
            } else if (goodsType === 'exceptGoods') {
                goodsInfo = exceptGoodsInfo;
            }

            // 추가 가능한 상품 수 제한 및 경고 메시지 노출
            const possibleAddGoodsLength = goodsLimit - goodsInfo.length;
            if (jsonData.info.length > possibleAddGoodsLength) {
                noDisplayAlert = true;
                goodsLimitErrorMessage(goodsType);
            }

            // 개수 제한까지 추가, 선택한 상품 HTML 그리기
            goodsInfo.push(...jsonData.info.slice(0, possibleAddGoodsLength));
            goodsInfo.sort((a, b) => a.goodsNo - b.goodsNo);

            // 렌더링
            const selectedGoodsTable = document.querySelector('#tbl_add_goods_set');
            const selectedLayerGoods = goodsInfo.map((goods) => selectedLayerGoodsForm(goods, goodsType));
            selectedGoodsTable.querySelector('tbody').innerHTML = selectedLayerGoods.join('');

            if (goodsType === 'applyGoods') {
                applyGoodsInfo = goodsInfo;
            } else if (goodsType === 'exceptGoods') {
                exceptGoodsInfo = goodsInfo;
            }

            // 페이지 랜더링
            goodsPager.refresh(1);
            return noDisplayAlert;
        } catch (e) {
            NCDSAlert({
                message: '상품을 불러오는 도중 오류가 발생하여 실패하였습니다.'
            });
        }
    }

    // 선택된 상품 설정
    function selectedGoodsType() {
        let type = '';
        const selected_apply_type = document.querySelector('input[name="applyType"]:checked');
        if (selected_apply_type.value === 'category') {
            type = 'exceptGoods';
        } else if (selected_apply_type.value === 'applyGoods') {
            type = 'applyGoods';
        }

        return type;
    }

    // 상품 최대 수 검증
    function validGoodsLimit(goodsType, addDataLength = 0) {
        let goodsInfo = [];
        if (goodsType === 'applyGoods') {
            goodsInfo = applyGoodsInfo;
        } else if (goodsType === 'exceptGoods') {
            goodsInfo = exceptGoodsInfo;
        }

        // 상품 제한 수 확인
        return goodsInfo.length + addDataLength < goodsLimit;
    }

    // 상품 최대 수 검증 메시지
    function goodsLimitErrorMessage(goodsType) {
        const isExceptGoods = goodsType === 'exceptGoods';

        NCDSAlert({
            message: `최대 ${goodsLimit.toLocaleString()}개까지 등록할 수 있습니다.`,
            subMessage: `${isExceptGoods ? '예외 ' : ''}상품 선택은 최대 ${goodsLimit.toLocaleString()}개까지 등록이 가능합니다.<br>이미 등록한 상품을 삭제한 후 재시도 해주세요.`,
            iconType: 'error'
        });
    }

    // 레이어 팝업에서 선택된 상품 폼
    const selectedLayerGoodsForm = (data, type) => {
        return `<tr id="tbl_add_goods_${data.goodsNo}">
            <td>
                <input type="hidden" name="itemGoodsNm[]" value="${data.goodsNm}"/>
                <input type="hidden" name="itemGoodsNo[]" id="${type}_present_goods_${data.goodsNo}" value="${data.goodsNo}"/>
            </td>
            <td data-name="goodsImage">
                <img src="${data.goodsImg}" width="30" alt="${data.goodsNm}" title="${data.goodsNm}" class="middle">
            </td>
            <td data-name="goodsPrice" data-value="${data.goodsPrice}"></td>
            <td data-name="scmNm" data-value="${data.scmNm}"></td>
            <td data-name="totalStock" data-value="${data.totalStock}"></td>
        </tr>`;
    }

    // 노출되는 tr 폼
    function renderTrForm(type, goodsInfoData) {
        let addInfo = '';
        if (type === 'applyGoods') {
            addInfo = `
            <td><div>${goodsInfoData.goodsPrice}</div></td>
            <td><div>${goodsInfoData.scmNm}</div></td>
            <td><div>${goodsInfoData.totalStock}</div></td>
            `;
        }
        return `<tr id="${type}-goods-${goodsInfoData.goodsNo}">
            <td>
                <div>
                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                            <input type="checkbox" name="${type}-goodsNo[]" value="${goodsInfoData.goodsNo}">
                        </span>
                    </label>
                </div>
            <td>
                <div>${goodsInfoData.goodsNo}</div>
            </td>
            <td>
                <div>${goodsInfoData.goodsImage}</div>
            </td>
            <td>
                <div>
                    <a class="text-blue hand"
                       onclick="goods_register_popup('${goodsInfoData.goodsNo}');">${goodsInfoData.goodsName}</a>
                </div>
            </td>
            ${addInfo}
        </tr>`
    }

    class TablePaginator {
        constructor(type, options = {}) {
            this.type = type;
            this.target_table = '#tbl_add_goods_set';
            this.rows = document.querySelectorAll(`${this.target_table} tr`);
            this.result = document.querySelector(`.${type}-goods-table-body`);
            this.pagination = document.querySelector(`.${type}-pagination`);

            this.rowsPerPage = options.rowsPerPage || 10;
            this.pageGroupSize = options.pageGroupSize || 10;

            this.currentPage = 1;
            this.totalPages = Math.ceil(this.rows.length / this.rowsPerPage);

            this.countText = document.querySelector(`.${this.type}-goods-count strong`);
            if (this.countText) {
                this.countText.innerText = this.rows.length;
            }

            if (this.result) {
                this.renderPage(1);
            }
        }

        renderPage(page) {
            this.currentPage = page;

            const start = (page - 1) * this.rowsPerPage;
            const end = start + this.rowsPerPage;

            const targetRows = [...this.rows].slice(start, end);

            if (targetRows.length < 1) {
                let colspan = 0;
                let empty_message = '';
                if (this.type === 'exceptGoods') {
                    colspan = 4;
                    empty_message = '선택된 예외 상품이 없습니다.';
                } else if (this.type === 'applyGoods') {
                    colspan = 7;
                    empty_message = '선택된 상품이 없습니다.';
                }

                this.result.innerHTML = `
                        <tr class="tr-no-data">
                            <td colspan="${colspan}" class="no-data">
                                <div>${empty_message}</div>
                            </td>
                        </tr>`;
                this.renderPagination();
                return;
            }

            this.result.innerHTML = targetRows.map(r => {
                const type = this.type;
                const goodsInfoData = {
                    goodsNo: r.querySelector('[name="itemGoodsNo[]"]').value,
                    goodsName: r.querySelector('[name="itemGoodsNm[]"]').value,
                    goodsImage: r.querySelector('[data-name="goodsImage"]').innerHTML,
                    goodsPrice: r.querySelector('[data-name="goodsPrice"]').dataset.value,
                    scmNm: r.querySelector('[data-name="scmNm"]').dataset.value,
                    totalStock: r.querySelector('[data-name="totalStock"]').dataset.value,
                };

                return renderTrForm(type, goodsInfoData);
            }).join("");

            this.renderPagination();
        }

        renderPagination() {
            const currentGroup = Math.ceil(this.currentPage / this.pageGroupSize);
            const startPage = (currentGroup - 1) * this.pageGroupSize + 1;

            let endPage = startPage + this.pageGroupSize - 1;
            if (endPage > this.totalPages) endPage = this.totalPages;

            let html = "";
            html += `<nav>
                        <ul class="pagination pagination-sm">`;

            // 맨 처음
            if (startPage > 1)
                html += `<li class="front-page front-page-first">
                            <a aria-label="First" onclick="goodsPager.goFirst()">
                                <img src="/admin/gd_share/img/icon_arrow_page_ll.png" class="img-page-arrow">
                                맨앞
                            </a>
                        </li>`;

            // 이전 그룹
            if (startPage > 1)
                html += `<li class="front-page front-page-prev">
                            <a aria-label="Previous" onclick="goodsPager.goPage(${startPage - 1})">
                                <img src="/admin/gd_share/img/icon_arrow_page_l.png" class="img-page-arrow">
                                이전
                            </a>
                        </li>`;

            // 페이지 번호
            for (let i = startPage; i <= endPage; i++) {
                if (i === this.currentPage) {
                    html += `<li class="active"><span onclick="goodsPager.goPage(${i})">${i}</span></li>`;
                } else {
                    html += `<li><a onclick="goodsPager.goPage(${i})">${i}</a></li>`;
                }
            }

            // 다음 그룹
            if (endPage < this.totalPages) {
                html += `<li class="front-page front-page-next">
                            <a aria-label="Next" onclick="goodsPager.goPage(${endPage + 1})">
                                <img src="/admin/gd_share/img/icon_arrow_page_r.png" class="img-page-arrow">다음
                            </a>
                        </li>`;

                // 맨 마지막
                if (this.currentPage < this.totalPages)
                    html += `<li class="front-page front-page-last">
                                <a aria-label="Last" onclick="goodsPager.goLast()">
                                    <img src="/admin/gd_share/img/icon_arrow_page_rr.png" class="img-page-arrow">
                                    맨뒤
                                </a>
                            </li>`;
            }

            html += `</ul>
                 </nav>`;

            this.pagination.innerHTML = html;
        }

        goPage(n) {
            this.renderPage(n);
        }

        goFirst() {
            this.renderPage(1);
        }

        goLast() {
            this.renderPage(this.totalPages);
        }

        refresh(page) {
            this.rows = document.querySelectorAll(`${this.target_table} tr`);
            this.totalPages = Math.ceil(this.rows.length / this.rowsPerPage);

            if (page === undefined || page === null) {
                page = this.currentPage;  // 페이지 유지
            }

            // page 범위 벗어나면 보정
            if (page > this.totalPages) {
                page = this.totalPages;
            }
            if (page < 1) {
                page = 1;
            }

            // 상품 개수 계산
            this.countText.innerText = this.rows.length;
            this.renderPage(page);
        }

        updatePrefix(prefix) {
            this.type = prefix;
            this.result = document.querySelector(`.${prefix}-goods-table-body`);
            this.pagination = document.querySelector(`.${prefix}-pagination`);
            this.countText = document.querySelector(`.${prefix}-goods-count strong`);
            this.refresh(1);
        }
    }

    const goodsPager = new TablePaginator(
        selectedGoodsType(),
        {
            rowsPerPage: 10,
            pageGroupSize: 10
        }
    );
</script>
