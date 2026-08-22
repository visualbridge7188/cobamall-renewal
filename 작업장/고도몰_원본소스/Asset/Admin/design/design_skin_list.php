<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/design/design-skin-list.css')?>" rel="stylesheet"/>
<script type="text/javascript" src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/ncua-dropdown-wrapper.v0.1.js')?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/spinner-modal.js') ?>"></script>

<article class="ncua-content design-skin-list">
    <header class="page-header ncua-page-header js-affix">
        <h3 class="ncua-help-manual"><?= end($naviMenu->location); ?>
        </h3>
        <span class="ncua-page-header__actions">
            <button type="button" class="ncua-btn ncua-btn--md ncua-btn--primary js-register">저장</button>
        </span>
    </header>
    <?php if (!$hasResponsiveSkin || $skinConf['skin_type'] !== 'responsive'): ?>

    <!-- 온보딩 -->
    <section class="ncua-card max-width-center onboarding">
      <header class="ncua-card__header">
          <h4 class="ncua-card__title">디자인 에디터 시작하기</h4>
          <div class="onboarding__progress">
            <span class="onboarding__progress-count"><b>0</b>/<span class="onboarding__progress-count-total"></span></span>
          </div>
      </header>
      <div class="ncua-card__body">
        <ol class="onboarding__list">
          <li class="onboarding__item <?= $hasResponsiveSkin ? 'is-completed' : '' ?>">
            <span class="onboarding__item-count"></span>
            <details class="onboarding__item-content" <?= !$hasResponsiveSkin ? 'open' : '' ?>>
              <summary class="onboarding__item-title">반응형 스킨 다운로드하기 <span class="icon-new"></span></summary>
              <p>디자인 에디터 전용 반응형 스킨이 필요합니다.</p>
              <p>아래 버튼을 클릭하여 반응형 스킨을 먼저 다운로드하세요.</p>
              <button class="ncua-btn ncua-btn--xs ncua-btn--secondary js-download-responsive-skin" type="button">반응형 스킨 다운로드</button>
            </details>
          </li>
          <li class="onboarding__item">
            <span class="onboarding__item-count"></span>
            <details class="onboarding__item-content" <?= $hasResponsiveSkin && $skinConf['skin_type'] !== 'responsive' ? 'open' : '' ?>>
              <summary class="onboarding__item-title">반응형 스킨을 사용 스킨으로 게시하기</summary>
              <p>다운로드한 반응형 스킨을 게시하면 디자인 에디터로 쇼핑몰 디자인을 편집할 수 있습니다.</p>
              <ul>
                <li class="ncua-notice-info">[반응형] 탭 선택 후 → 원하는 반응형 스킨의 [게시하기] 클릭</li>
              </ul>
            </details>
          </li>
        </ol>
        <?php if ($hasResponsiveSkin): ?>
        <p class="onboarding__do-not-show-again">
            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                    <input type="checkbox" name="" value="" class="js-onboarding-do-not-show-again" />
                </span>
                <span><span class="ncua-checkbox-field__text">다시 보지 않기</span></span>
            </label>
            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-close-onboarding">닫기</button>
        </p>
        <?php endif; ?>
      </div>
    </section>
    <!-- // 온보딩 -->

    <?php endif; ?>


    <?php if ($mallCnt > 1): ?>
        <div class="ncua-horizontal-tab ncua-horizontal-tab--md ncua-horizontal-tab--underline-fill design-skin-list--common-tab max-width-center">
            <div class="swiper swiper-initialized swiper-horizontal">
                <div class="swiper-wrapper ncua-gap-8" style="transition-duration: 0ms; transition-delay: 0ms;">
                    <?php foreach ($mallList as $key => $val): ?>
                        <div role="presentation" class="swiper-slide ncua-horizontal-tab__item <?= $val['sno'] == $mallSno ? 'active' : 'passive'; ?>" data-html="true" data-content="<?= $val['mallName']; ?>" data-placement="top">
                            <a href="design_skin_list.php?mallSno=<?= $val['sno']; ?>" data-sno="<?= $val['sno'] ?>" class="ncua-tab-button ncua-tab-button--sm ncua-tab-button--button-primary js-tab-button <?= $val['sno'] == $mallSno ? 'is-active' : '' ?>">
                                <?= $val['mallName']; ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- 현재 설정된 디자인 스킨 -->
    <?php if ($skinConf['skin_type'] === 'responsive') { ?>
        <!-- 반응형 스킨 -->
        <?php include $activeDesignSkinResponsiveSection; ?>
    <?php } else { ?>
        <!-- PC / 모바일 스킨 (adaptive) -->
        <?php include $activeDesignSkinSection; ?>
    <?php } ?>

    <!-- 보유 스킨 리스트 -->
    <?php if ($skinTabData['responsiveSkinCount'] > 0 || $skinTabData['adaptiveSkinCount'] > 0) { ?>
        <?php include $ownedSkinListSection; ?>
    <?php } ?>

    <!-- 해외몰 홈아이콘 설정 -->
    <?php if ($mallCnt > 1) { ?>
        <?php include $overseasMallIconSettingSection; ?>
    <?php } ?>

    <!-- 인기 급상승 스킨 -->
    <?php include $topTrendingSkinsSection; ?>
</article>

<script type="text/javascript">
/**
 * TODO: 접근 동선에 따른 구분값
 * ex) 디자인 > 디자인 스킨 리스트, 모바일샵 > 디자인 스킨 리스트
 */
const deviceType = 'front';
// const deviceType = 'mobile';

/**
 * description
 * 아래 타입은 임의로 정의한 값입니다.
 * 실제 사용할 스킨 타입으로 변경해주세요
 * PC/Mobile: adaptive,
 * 반응형: responsive
 */
const SKIN_TYPE = {
    ADAPTIVE: 'adaptive',
    RESPONSIVE: 'responsive',
};

const originSkinListData = JSON.parse('<?= json_encode($skinList, JSON_UNESCAPED_UNICODE) ?>');

// 현재 사용 중인 스킨 타입 및 모바일샵 설정
const currentSkinType = '<?= $skinConf['skin_type'] ?? 'adaptive' ?>';
const mobileShopFl = '<?= $mobileShopFl ?? 'n' ?>';
const isMyappInstalled = <?= json_encode($isMyappInstalled) ?>;
const isMyappReleased = <?= json_encode($isMyappReleased) ?>;

const Utils = {
    getSkinPreviewUrl: (deviceType, skinCode) => {
        return '<?= $skinPreviewUrl ?>' + `${deviceType}^|^${skinCode}`;
    },
    getPublishUrl: () => {
        if (deviceType === 'front') {
            return `./design_skin_list_ps.php?mode=clearCache&mallSno=` + <?= $mallSno; ?>;
        } else {
            return `../mobile/design_skin_list_ps.php?mode=clearCache&mallSno=` + <?= $mallSno; ?>;
        }
    },
    findCard: (element, selector) => {
        return element?.closest(selector) || null;
    },
    findActiveSkinCard: (element) => Utils.findCard(element, '.active_skin--card'),
    findResponsiveCard: (element) => Utils.findCard(element, '.active_skin--responsive--card'),
    findOwnedSkinCard: (element) => Utils.findCard(element, '.owned-skin-card'),
    replaceMenuPosition: (menu) => {
        menu.style.position = 'absolute';
        menu.style.top = '100%';
        menu.style.left = '-105px';
        menu.style.marginTop = '4px';
        menu.style.zIndex = '3000';
        return menu;
    },
    createHiddenInput: (name, value) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        return input;
    },
    appendFormData: (form, data) => {
        Object.entries(data).forEach(([key, value]) => {
            form.appendChild(Utils.createHiddenInput(key, value));
        });
        return form;
    },
    createSkinTokenizer: (sno, skinCode) => {
        return sno + '<?= STR_DIVISION; ?>' + skinCode;
    },
    /**
     * 스킨 게시 시 조건별 컨펌 설정 반환
     */
    getSkinPublishConfig: (currentType, targetType, mobileShopFlValue, skinName) => {
        const isCurrentAdaptive = currentType === SKIN_TYPE.ADAPTIVE;
        const isTargetResponsive = targetType === SKIN_TYPE.RESPONSIVE;

        // 적응형 → 적응형
        if (isCurrentAdaptive && !isTargetResponsive) {
            return {
                message: '사용스킨을 변경하시겠습니까?',
                subMessage: `<p class="layer-description--sub">'현재 사용스킨' 변경 시 게시판에 적용된 스킨은 '게시판 기본스킨'으로 변경됩니다.</p>`,
                onConfirm: 'publish'
            };
        }

        // 적응형 → 반응형 (모바일샵 사용 중)
        if (isCurrentAdaptive && isTargetResponsive && mobileShopFlValue === 'y') {
            return {
                message: '모바일샵 설정을 변경해 주세요.',
                subMessage: `<p class="layer-description--sub">선택하신 반응형 스킨은 PC와 모바일이 통합 관리됩니다. 스킨 적용을 위해 모바일샵 설정을 '사용 안함'으로 변경한 뒤 다시 진행해주세요.</p>`,
                onConfirm: 'redirect'
            };
        }

        // 반응형 → 적응형
        if (!isCurrentAdaptive && !isTargetResponsive) {
            const skinDevice = targetType === SKIN_TYPE.RESPONSIVE ? 'front' : targetType;
            const deviceLabel = skinDevice === 'front' ? 'PC' : '모바일';
            const otherDeviceLabel = skinDevice === 'front' ? '모바일' : 'PC';
            return {
                message: `${skinName} 스킨을 게시하시겠습니까?`,
                subMessage: `<p class="layer-description--sub">현재 선택한 ${deviceLabel} 스킨만 게시할 경우, 일부 디바이스에서 페이지 접근 오류가 발생할 수 있습니다.<br><br>정상적인 서비스 제공을 위해 ${otherDeviceLabel} 스킨을 함께 게시해 주세요.<br><br>※ 모바일 스킨은 [모바일샵 설정 > 사용함] 상태에서만 정상적으로 노출됩니다.<br>※ [확인] 클릭 시 스킨이 게시되며, '모바일샵 설정' 페이지로 바로 이동합니다.</p>`,
                onConfirm: 'publish'
            };
        }

        // 그 외 (적응형 → 반응형 (모바일샵 미사용) / 반응형 → 반응형)
        return {
            message: `${skinName} 스킨을 게시하시겠습니까?`,
            subMessage: '<p class="layer-description--sub">웹사이트를 방문하는 고객에게 해당 스킨으로 변경되어 보이며, 기존에 사용중이던 스킨은 보관됩니다.</p>',
            onConfirm: 'publish'
        };
    },
};

const popupLayers = {
    skinInfo: (params) => {
        const modifyUrl = './layer_skin_modify.php';
        /**
         * TODO: 유효성 경고
         */
        $.get(modifyUrl, params, (data) => {
            ncds_layer_popup({
                message: data,
                title: '스킨 정보 변경',
                size: 'wide',
                callback: (dialog) => {

                }
            });
        });
    },
    skinUpload: (params) => {
        const uploadUrl = './layer_skin_upload.php';
        /**
         * TODO: 유효성 경고, 시스템 알럿
         */
        $.get(uploadUrl, params, (data) => {
            ncds_layer_popup({
                message: data,
                title: '스킨 업로드',
                size: 'size-wide',
                callback: (dialog) => {
                }
            });
        }).fail(function(xhr, status, error) {
            console.error('파일 로드 실패:', {
                url: uploadUrl,
                status: xhr.status,
                statusText: xhr.statusText,
                error: error,
                responseText: xhr.responseText
            });
            if (window.NCDSAlert) {
                NCDSAlert({
                    message: '스킨 업로드 파일을 찾을 수 없습니다. (경로: ' + uploadUrl + ')',
                    iconType: 'error'
                });
            }
        });
    },
    skinCopy: (params) => {
        const copyUrl = './layer_skin_copy.php';
        /**
         * TODO: 유효성 경고, 시스템 알럿
         */
        $.get(copyUrl, params, (data) => {
            ncds_layer_popup({
                message: data,
                title: '스킨 복사',
                size: 'size-wide',
                callback: (dialog) => {
                }
            });
        });
    },
    skinDelete: (params) => {
        const { skinName, skinType, skinCode } = params;
        NCDSConfirm({
            message: skinName + ' 스킨을 정말로 삭제 하시겠습니까? <p class="layer-description--sub">삭제시 복구가 불가능합니다.</p>',
            iconType: 'danger'
        }).then(function(result) {
            if(!result) return;
            const loadingModal = window.spinnerModal({ message: '삭제 중...' });
            loadingModal.open();

            $.ajax({
                type: 'POST',
                url: 'design_skin_list_ps.php',
                data: {
                    'mode': 'deleteSkin',
                    'skinName': skinCode,
                    'skinType': skinType
                },
                success: function(res) {
                    loadingModal.close();
                    if(!res) {
                        return;
                    }
                    if(res == 'ok') {
                        NCDSToast({
                            message: skinName + ' 스킨이 삭제 되었습니다. 잠시후 완료됩니다. 만약 ' + skinName + ' 스킨이 남아 있다면 다시 한 번 삭제 진행을 해주세요.',
                            color: 'success'
                        });
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    } else {
                        /**
                         * TODO: 시스템 알럿
                         */
                    }
                },
                complete: function(xhr, status) {
                    loadingModal.close();
                }
            });
        })
    },
    skinDownload: (params) => {
        const { skinName, skinType, skinCode } = params;
        /**
         * TODO: 시스템 알럿
         */
        NCDSConfirm({
            message: skinName + ' 스킨을 ZIP 파일로 압축 후 다운로드 하시겠습니까?',
            iconType: 'info'
        }).then(function(result) {
            if(!result) return;

            const loadingModal = window.spinnerModal({ message: '압축 중...' });
            loadingModal.open();
            setTimeout(() => {
                location.href = 'design_skin_list_ps.php?mode=downSkin&skinType=' + skinType + '&skinName=' + skinCode;
                loadingModal.close();

                NCDSToast({
                    message: '다운로드를 요청했습니다. 상단 또는 하단의 상태바에서 파일 저장 여부를 확인해 주세요.',
                    color: 'success'
                });
            }, 100);
        });
    },
    skinPublish: (params) => {
        const { skinName, skinType, skinCode } = params;

        // 기존 게시 흐름
        const proceedWithPublish = () => {
            const config = Utils.getSkinPublishConfig(
                currentSkinType,
                skinType,
                mobileShopFl,
                skinName,
            );

            NCDSConfirm({
                message: config.message,
                subMessage: config.subMessage
            }).then(function(result) {
                if(!result) return;

                // 스킨 변경 폼 제출 함수
                const submitSkinChangeForm = () => {
                    const form = document.createElement('form');
                    form.action = 'design_skin_list_ps.php';
                    form.method = 'post';
                    form.target = 'ifrmProcess';
                    const sno = '<?php echo $mallSno; ?>';
                    const formData = {
                        mode: 'skinChange',
                        skinType: skinType,
                        skinUse: 'Live',
                        [`${skinType}Skin`]: skinCode,
                        sno: sno
                    };
                    Utils.appendFormData(form, formData);
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                };

                switch (config.onConfirm) {
                    case 'redirect':
                        // 모바일샵 설정 페이지로 이동만 (스킨 변경 없음)
                        location.href = '/mobile/mobile_config.php';
                        break;

                    case 'publish':
                    default:
                        // 스킨 변경 (리다이렉트는 백엔드에서 자동 처리)
                        submitSkinChangeForm();
                        break;
                }
            });
        };

        // 마이앱 사전 체크: 적응형↔반응형 스킨 유형 전환 + 마이앱 사용 중
        if (currentSkinType !== skinType && isMyappInstalled && isMyappReleased) {
            NCDSConfirm({
                message: '마이앱(MyApp) 서비스 운영에 주의가 필요합니다.',
                subMessage: '<p class="layer-description--sub">'
                    + '마이앱 서비스 운영 중 스킨 유형을 변경할 경우, 서비스 운영에 다음과 같은 문제가 발생할 수 있습니다.'
                    + '<br><br>'
                    + '1. 사용자의 기존 앱 로그인 정보 및 세션이 유지되지 않습니다.<br>'
                    + '2. 스킨 변경 시 도메인 정보 변경으로 인해 앱 내 페이지 이동 시 404 오류가 발생할 수 있습니다.<br>'
                    + '3. 변경된 화면 구성에 따라 앱 스토어(Apple/Google) 재심사 및 승인이 필요합니다.'
                    + '<br><br>'
                    + '위 내용을 확인하였으며, 스킨 변경을 진행하시겠습니까?'
                    + '</p>',
                btnText: {
                    cancelLabel: '취소',
                    confirmLabel: '동의 후 변경하기'
                }
            }).then(function(agreed) {
                if (!agreed) return;
                proceedWithPublish();
            });
            return;
        }

        proceedWithPublish();
    }
}

const DROPDOWN_ITEMS = {
    ACTIVE_RECEIVED_SKIN: [{
        text: '스킨정보',
        type: 'skinInfo',
    }, {
        text: '복사',
        type: 'skinCopy',
    }],
    ACTIVE_ADAPTIVE_SKIN: [{
        text: '스킨정보',
        type: 'skinInfo',
    }, {
        text: '다운로드',
        type: 'skinDownload',
    }, {
        text: '복사',
        type: 'skinCopy',
    }],
    OWNED_RECEIVED_SKIN: [{
        text: '스킨정보',
        type: 'skinInfo',
    }, {
        text: '복사',
        type: 'skinCopy',
    }, {
        text: '삭제',
        type: 'skinDelete',
        itemClassNames: ['is-danger'],
        group: 'danger',
    }],
    OWNED_ADAPTIVE_SKIN: [{
        text: '스킨정보',
        type: 'skinInfo',
    }, {
        text: '다운로드',
        type: 'skinDownload',
    }, {
        text: '복사',
        type: 'skinCopy',
    }, {
        text: '삭제',
        type: 'skinDelete',
        itemClassNames: ['is-danger'],
        group: 'danger',
    }],
};

/**
 * 현재 설정된 디자인 스킨
 */
const activeSkinList = {
    initPcDropdown: () => {
        const dropdownEl = document.querySelector('.ncua-dropdown--pc');
        if (!dropdownEl) return;
        new NcuaDropdownWrapper(dropdownEl, {
            data: {
                items: DROPDOWN_ITEMS.ACTIVE_ADAPTIVE_SKIN,
            },
            events: {
                itemClick: (e, el, item) => {
                    const skinCard = Utils.findActiveSkinCard(e.target);
                    const skinCode = skinCard.getAttribute('data-skin-code');
                    const skinType = skinCard.getAttribute('data-skin-type');
                    const skinName = skinCard.getAttribute('data-skin-name');
                    switch(item.type) {
                        case 'skinInfo':
                            popupLayers.skinInfo({
                                skinType,
                                skinCode
                            });
                            break;
                        case 'skinCopy':
                            popupLayers.skinCopy({
                                skinType,
                                skinName: skinCode
                            });
                            break;
                        case 'skinDownload':
                            popupLayers.skinDownload({
                                skinType,
                                skinName,
                                skinCode
                            });
                            break;
                    }
                }
            },
            layout: {
                menu: (menu) => Utils.replaceMenuPosition(menu)
            }
        });
    },
    initMobileDropdown: () => {
        const dropdownEl = document.querySelector('.ncua-dropdown--mobile');
        if (!dropdownEl) return;
        new NcuaDropdownWrapper(dropdownEl, {
            data: {
                items: DROPDOWN_ITEMS.ACTIVE_ADAPTIVE_SKIN,
            },
            events: {
                itemClick: (e, el, item) => {
                    const skinCard = Utils.findActiveSkinCard(e.target);
                    const skinCode = skinCard.getAttribute('data-skin-code');
                    const skinType = skinCard.getAttribute('data-skin-type');
                    const skinName = skinCard.getAttribute('data-skin-name');
                    switch(item.type) {
                        case 'skinInfo':
                            popupLayers.skinInfo({
                                skinType,
                                skinCode
                            });
                            break;
                        case 'skinCopy':
                            popupLayers.skinCopy({
                                skinType,
                                skinName: skinCode
                            });
                            break;
                        case 'skinDownload':
                            popupLayers.skinDownload({
                                skinType,
                                skinName,
                                skinCode
                            });
                            break;
                    }
                }
            },
            layout: {
                menu: (menu) => Utils.replaceMenuPosition(menu)
            }
        });
    },
    initResponsiveDropdown: () => {
        const dropdownEl = document.querySelector('.ncua-dropdown--responsive');
        if (!dropdownEl) return;
        new NcuaDropdownWrapper(dropdownEl, {
            data: {
                items: DROPDOWN_ITEMS.ACTIVE_RECEIVED_SKIN,
            },
            events: {
                itemClick: (e, el, item) => {
                    const skinCard = Utils.findResponsiveCard(e.target);
                    const skinCode = skinCard.getAttribute('data-skin-code');
                    const skinType = skinCard.getAttribute('data-skin-type');
                    switch(item.type) {
                        case 'skinInfo':
                            popupLayers.skinInfo({
                                skinType,
                                skinCode
                            });
                            break;
                        case 'skinCopy':
                            popupLayers.skinCopy({
                                skinType,
                                skinName: skinCode
                            });
                            break;
                    }
                }
            },
            layout: {
                menu: (menu) => Utils.replaceMenuPosition(menu)
            }
        });
    }
};

/**
 * 보유 스킨 리스트
 */
const ownedSkinList = {
    MAX_SKIN_COUNT: 5,
    getSection: () => document.querySelector('.owned-skin-list-section'),
    getViewAllButton: () => ownedSkinList.getSection()?.querySelector('.js-view-all'),
    getViewAllLabel: () => ownedSkinList.getSection()?.querySelector('.js-view-all .ncua-btn__label'),
    getCurrentTab: () => ownedSkinList.getSection()?.querySelector('.js-tab-button.is-active')?.dataset.tab || ownedSkinList.getSection()?.dataset.defaultTab,
    getUploadButton: () => ownedSkinList.getSection()?.querySelector('.js-skin-upload'),
    getTotalCountElement: () => ownedSkinList.getSection()?.querySelector('.js-total-count'),
    getViewAllContainer: () => ownedSkinList.getSection()?.querySelector('.owned-skin-list--view-all'),
    isViewAll: () => ownedSkinList.getSection()?.classList.contains('is-view-all'),
    updateTotalCount: (count) => {
        const countElement = ownedSkinList.getTotalCountElement();
        if (countElement) {
            countElement.textContent = count;
        }
    },
    updateViewAllButton: (count) => {
        const viewAllContainer = ownedSkinList.getViewAllContainer();
        if (viewAllContainer) {
            viewAllContainer.style.display = count > ownedSkinList.MAX_SKIN_COUNT ? '' : 'none';
        }
    },
    attachSkinTabEvent: () => {
        const tabs = ownedSkinList.getSection().querySelectorAll('.js-tab-button');
        if (tabs.length === 0) return;

        tabs.forEach((tab) => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();

                tabs.forEach((t) => t.classList.remove('is-active'));
                tab.classList.add('is-active');

                const activeTab = ownedSkinList.getCurrentTab();
                const shownListLen = ownedSkinList.isViewAll() ? null : ownedSkinList.MAX_SKIN_COUNT;
                ownedSkinList.drawOwnedSkinList(activeTab, shownListLen, originSkinListData);
                ownedSkinList.getUploadButton().classList.toggle('is-visible-hidden', activeTab === SKIN_TYPE.RESPONSIVE);
            });
        });
    },
    attachSkinUploadEvent: () => {
        ownedSkinList.getUploadButton()?.addEventListener('click', (e) => {
            e.preventDefault();
            const params = {
                skinType: '<?php echo isset($skinType) ? $skinType : ''; ?>'
            };
            popupLayers.skinUpload(params);
        });
    },
    createOwnedSkinCard: (skinData) => {
        const { skin_code, skin_device, skin_cover, skin_name, registered_date, skin_sno, default_design_page_id } = skinData;
        if(!skin_code) return;
        const skinCard = document.createElement('div');
        skinCard.classList.add('owned-skin-card');
        skinCard.setAttribute('data-skin-code', skin_code);
        skinCard.setAttribute('data-skin-name', skin_name);
        skinCard.setAttribute('data-default-design-page-id', default_design_page_id || 'default');
        skinCard.setAttribute('data-skin-sno', skin_sno);
        /**
         * TODO: 반응형 스킨에 해당하는 스킨타입 세팅 필요
         */
        const currentTab = ownedSkinList.getCurrentTab();
        skinCard.setAttribute('data-skin-type', currentTab === SKIN_TYPE.RESPONSIVE ? SKIN_TYPE.RESPONSIVE : skin_device);
        let skinPreviewUrl = Utils.getSkinPreviewUrl(skin_device, skin_code);
        const editSkinLabel = currentTab === SKIN_TYPE.RESPONSIVE ? '디자인 에디터로 편집하기' : 'HTML로 편집하기';
        /**
         * TODO: HTML로 편집하기 신규 url
         */
        skinCard.innerHTML = `
            <div class="owned-skin-card--thumb owned-skin-card--thumb-${skin_code}">
                <img src="${skin_cover || ''}" alt="스킨 썸네일"/>
                <div class="owned-skin-card--thumb--overlay ncua-flex ncua-align-center ncua-align-items-center">
                    <a href="${skinPreviewUrl}" target="_blank" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-skin-preview">
                        <span class="ncua-btn__label">미리보기</span>
                    </a>
                </div>
            </div>
            <div class="owned-skin-card--info">
                <div class="owned-skin-card--info--title">${skin_name || skin_code}</div>
                <div class="owned-skin-card--info--desc">
                    ${ currentTab !== SKIN_TYPE.RESPONSIVE ? `<span class="owned-skin-card--info-type">${skin_device === 'front' ? 'PC' : '모바일'}</span>` : '' }
                    <span class="owned-skin-card--info-code">${skin_code}</span>
                    ${ registered_date ? `<span class="owned-skin-card--info-time">${registered_date}</span>` : '' }
                </div>
            </div>
            <div class="owned-skin-card--btns">
                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-skin-edit">
                    <span class="ncua-btn__label">${editSkinLabel}</span>
                </button>
                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-skin-publish">
                    <span class="ncua-btn__label">게시하기</span>
                </button>
                <div class="ncua-dropdown ncua-dropdown--owned-skin ncua-dropdown--owned-skin-${skin_code}"></div>
            </div>
        `;

        return skinCard;
    },
    initOwnedSkinCardDropdown: (skinCardElement, tabType) => {
        const dropdownElement = skinCardElement.querySelector('.ncua-dropdown--owned-skin');
        if (!dropdownElement) {
            return;
        }
        new NcuaDropdownWrapper(dropdownElement, {
            data: {
                items: tabType === SKIN_TYPE.RESPONSIVE ? DROPDOWN_ITEMS.OWNED_RECEIVED_SKIN : DROPDOWN_ITEMS.OWNED_ADAPTIVE_SKIN
            },
            events: {
                itemClick: (e, el, item) => {
                    const skinCard = Utils.findOwnedSkinCard(e.target);
                    const skinCode = skinCard.getAttribute('data-skin-code');
                    if(!skinCode) return;
                    const skinType = skinCard.getAttribute('data-skin-type');
                    const skinName = skinCard.getAttribute('data-skin-name');
                    const data = originSkinListData.find(skin => skin.skin_code === skinCode);

                    switch(item.type) {
                        case 'skinInfo':
                            popupLayers.skinInfo({
                                skinType,
                                skinCode
                            });
                            break;
                        case 'skinCopy':
                            popupLayers.skinCopy({
                                skinType,
                                skinName: skinCode
                            });
                            break;
                        case 'skinDelete':
                            popupLayers.skinDelete({
                                skinType,
                                skinName,
                                skinCode
                            });
                            break;
                        case 'skinDownload':
                            popupLayers.skinDownload({
                                skinType,
                                skinName,
                                skinCode
                            });
                            break;
                    }
                }
            },
            layout: {
                menu: (menu) => Utils.replaceMenuPosition(menu)
            }
        });
    },
    drawOwnedSkinList(tabType, maxLen, list) {
        const skinList = ownedSkinList.getSection().querySelector('.owned-skin-list');
        if(!skinList) return;
        skinList.innerHTML = '';

        // skin_type이 tabType과 일치하는 스킨만 필터링
        const filteredList = list.filter(skin => skin.skin_type === tabType);

        // 전체 개수 업데이트
        ownedSkinList.updateTotalCount(filteredList.length);

        // 모두 보기 버튼 표시 여부 업데이트
        ownedSkinList.updateViewAllButton(filteredList.length);

        filteredList.forEach((skin, idx) => {
            try {
                if(maxLen && maxLen <= idx) return;
                const skinCard = ownedSkinList.createOwnedSkinCard(skin);
                skinList.appendChild(skinCard);
                ownedSkinList.initOwnedSkinCardDropdown(skinCard, tabType);
            } catch (error) {
                console.error(error, skin);
            }
        });
    },
};

/**
 * 해외몰 홈아이콘 관리
 */
const overseasMallList = {
    getSection: () => document.querySelector('.overseas-mall-icon-setting-section'),
    getBody: () => overseasMallList.getSection()?.querySelector('.ncua-card__body'),
    getViewAllButton: () => overseasMallList.getSection()?.querySelector('.view-all-button'),
    isViewAll: () => overseasMallList.getSection()?.classList.contains('is-collapsed'),
    initOverseasMallList: () => {
        const mallIcons = overseasMallList.getSection()?.querySelectorAll('.mall-icon');
        Array.from(mallIcons).forEach((mallIcon) => overseasMallList.initFileInput(mallIcon));
    },
    initFileInput: (mallIcon) => {
        const mallKey = mallIcon.dataset.mallKey;
        const fileInput = document.querySelector(`input[type="file"][name="mallIcon[${mallKey}]"]`);

        new ncua.FileInput({
            container: mallIcon,
            buttonLabel: '파일 찾기',
            accept: 'image/*',
            maxFileCount: 1,
            onFileSelect: (files) => {
                // 숨겨진 input[type=file]에 파일 할당
                if (fileInput && files && files.length > 0) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(files[0]);
                    fileInput.files = dataTransfer.files;
                }

                /** TODO: 필요시 파일 validation 처리 */
                // 예시)
                // if(true) {
                //     NCDSAlert({message: '파일 업로드 오류가 발생했습니다.', iconType: 'error'});
                //     return;
                // }
                const container = document.querySelector(`#${mallIcon.id}_container`);
                if(!container) return;
                const tag = overseasMallList.createTag(files, () => {
                    tag.destroy();
                    tag.getElement().remove();
                    container.classList.add('is-display-none');
                    container.innerHTML = '';
                    // 파일 input 초기화
                    if (fileInput) {
                        fileInput.value = '';
                    }
                });

                container.innerHTML = '';
                container.appendChild(tag.getElement());
                container.classList.remove('is-display-none');

                overseasMallList.createFileImage(files).then(fileImage => {
                    container.appendChild(fileImage);
                });
            }
        });
    },
    createTag: (newFiles, onButtonClick) => {
        return new ncua.Tag({
            text: newFiles[0].name,
            size: 'sm',
            close: true,
            onButtonClick
        });
    },
    createFileImage: (newFiles) => {
        return new Promise((resolve) => {
            const fileReader = new FileReader();
            fileReader.onload = function(e) {
                const fileImage = document.createElement('img');
                fileImage.className = 'ncua-file-input__file-image';
                fileImage.src = e.target.result;
                resolve(fileImage);
            };
            fileReader.readAsDataURL(newFiles[0]);
        });
    },
    showMenu: () => {
        const section = overseasMallList.getSection();
        if(!section) return;
        section.classList.remove('is-collapsed');
        const button = overseasMallList.getViewAllButton();
        if(!button) return;
        const icon = button.querySelector('.view-all-icon');
        if(!icon) return;
        icon.classList.toggle('chevron-up', true);
        icon.classList.toggle('chevron-down', false);
    },
    hideMenu: () => {
        const section = overseasMallList.getSection();
        if(!section) return;
        section.classList.add('is-collapsed');
        const button = overseasMallList.getViewAllButton();
        if(!button) return;
        const icon = button.querySelector('.view-all-icon');
        if(!icon) return;
        icon.classList.toggle('chevron-up', false);
        icon.classList.toggle('chevron-down', true);
    }
}

const topTrendingSkins = {
    getSection: () => document.querySelector('.top-trending-skins-section'),
    getViewAllButton: () => topTrendingSkins.getSection()?.querySelector('.js-view-all'),
};

/**
 * 현재 설정된 디자인 스킨
 */
document.addEventListener('DOMContentLoaded', () => {
    activeSkinList.initPcDropdown();
    activeSkinList.initMobileDropdown();

    /**
     * 반응형 스킨이 설정된 경우
     */
    activeSkinList.initResponsiveDropdown();

    document.querySelectorAll('.active-design-skin-section').forEach(section => {
        section.addEventListener('click', function(e) {
            const target = e.target.closest('.js-skin-edit, .js-skin-publish');
            if (!target) return;
            e.preventDefault();
            const skinCard = target.closest('.js-active-skin-card');
            if (!skinCard) return;
            const skinCode = skinCard.getAttribute('data-skin-code');
            const skinName = skinCard.getAttribute('data-skin-name');
            const skinType = skinCard.getAttribute('data-skin-type');
            const skinSno = skinCard.getAttribute('data-skin-sno');
            const defaultDesignPageId = skinCard.getAttribute('data-default-design-page-id') || 'default';

            switch(true) {
                case target.classList.contains('js-skin-edit'):
                    if (skinType === SKIN_TYPE.RESPONSIVE) {
                        window.open(`/design/design_editor.php?skinSno=${skinSno}`, '_blank');
                    } else {
                        window.open(`/design/layer_popup_design_page_edit.php?designPageId=${defaultDesignPageId}&skinType=${skinType}&skinCode=${skinCode}`, 'designPageEdit', 'width=1400,height=900,scrollbars=yes,resizable=yes');
                    }
                    break;
                case target.classList.contains('js-skin-publish'):
                    popupLayers.skinPublish({
                        skinType,
                        skinCode,
                        skinName
                    });
                    break;
            }
        });
    });
});

/**
 * 보유 스킨 리스트
 */
document.addEventListener('DOMContentLoaded', () => {
    ownedSkinList.attachSkinTabEvent();
    ownedSkinList.attachSkinUploadEvent();
    ownedSkinList.drawOwnedSkinList(ownedSkinList.getCurrentTab(), ownedSkinList.MAX_SKIN_COUNT, originSkinListData);

    ownedSkinList.getViewAllButton()?.addEventListener('click', function(e) {
        e.preventDefault();
        ownedSkinList.getSection().classList.toggle('is-view-all');
        const shownListLen = ownedSkinList.isViewAll() ? null : ownedSkinList.MAX_SKIN_COUNT;
        ownedSkinList.drawOwnedSkinList(ownedSkinList.getCurrentTab(), shownListLen, originSkinListData);
        const viewAllIcon = ownedSkinList.getViewAllButton()?.querySelector('.view-all-icon');
        if(!viewAllIcon) return;
        viewAllIcon.classList.toggle('chevron-up', ownedSkinList.isViewAll());
        viewAllIcon.classList.toggle('chevron-down', !ownedSkinList.isViewAll());
        ownedSkinList.getViewAllLabel().textContent = ownedSkinList.isViewAll() ? '닫기' : '모두 보기';
    });

    ownedSkinList.getSection()?.querySelector('.owned-skin-list').addEventListener('click', function(e) {
        const target = e.target.closest('.js-skin-edit, .js-skin-publish');
        if (!target) return;
        e.preventDefault();
        const skinCard = Utils.findOwnedSkinCard(target);
        const skinName = skinCard.getAttribute('data-skin-name');
        const skinCode = skinCard.getAttribute('data-skin-code');
        const skinType = skinCard.getAttribute('data-skin-type');
        const skinSno = skinCard.getAttribute('data-skin-sno');
        const defaultDesignPageId = skinCard.getAttribute('data-default-design-page-id') || 'default';
        // TODO: 게시하기
        switch(true) {
            case target.classList.contains('js-skin-edit'):
                if (skinType === SKIN_TYPE.RESPONSIVE) {
                    window.open(`/design/design_editor.php?skinSno=${skinSno}`, '_blank');
                } else {
                    window.open(`/design/layer_popup_design_page_edit.php?designPageId=${defaultDesignPageId}&skinType=${skinType}&skinCode=${skinCode}`, 'designPageEdit', 'width=1400,height=900,scrollbars=yes,resizable=yes');
                }
                break;
            case target.classList.contains('js-skin-publish'):
                popupLayers.skinPublish({
                    skinType,
                    skinCode,
                    skinName
                });
                break;
        }
    });
});
/**
 * 해외몰 홈 아이콘 관리
 */
<?php if ($mallCnt > 1) { ?>
document.addEventListener('DOMContentLoaded', () => {
    overseasMallList.initOverseasMallList();
    overseasMallList.hideMenu();

    overseasMallList.getViewAllButton()?.addEventListener('click', function(e) {
        e.preventDefault();
        overseasMallList.isViewAll() ? overseasMallList.showMenu() : overseasMallList.hideMenu();
    });

    // 저장 버튼 클릭 시, 해외몰 아이콘 설정 폼 등록 처리
    document.querySelector('.js-register')?.addEventListener('click', function(e) {
        e.preventDefault();
        const form = document.getElementById('frmMallIcon');
        if (form) {
            form.submit();
        }
    });
});
<?php } ?>

/**
 * 인기 급상승 스킨
 */
document.addEventListener('DOMContentLoaded', () => {
    topTrendingSkins.getViewAllButton()?.addEventListener('click', (e) => {
        e.preventDefault();
        let popup = window.open('<?= $designCenterUrl; ?>', 'popup', 'width=' + screen.width + ',height=' + screen.height + ',scrollbars=yes');
        window.onfocus = function() {
            if (popup && popup.closed) {
                location.reload();
                popup = null;
            }
        };
    })
});
</script>

<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/aggregator/TutorialHandler.js"></script>
<script type="text/javascript">
    var _isTutorialInitialized = false;
    function initTutorialHandler() {
        if (_isTutorialInitialized) {
            return;
        }
        _isTutorialInitialized = true;

        window.GodoTutorial.tutorialHandlerInstance = new GodoTutorial.TutorialHandler({
            category: 'PAYMENT_TUTORIAL',
            code: '<?= $tutorialCode ?>',
            mode: '<?= $tutorialMode ?>',
            linkModalUrl: '<?= $tutorialLinkModalUrl ?>',
            linkModalSize: <?= json_encode($tutorialLinkModalSize ?? []) ?>,
            stepGuideSteps: <?= json_encode($tutorialStepGuideSteps ?? []) ?>,
            stepGuideButtonLabel: {
                prev: '이전',
                next: '다음',
                done: 'Web FTP 열기',
                skip: '나중에 하기'
            },
            stepGuideLinks: <?= json_encode($tutorialStepGuideLinks ?? []) ?>,
        });
    }

    // adminPanelApiAjax 완료 후 튜토리얼 시작
    $(document).one('adminPanelApiComplete', function() {
        var popupCosModal = $('#panel_popupCos_modal');
        if (popupCosModal.children().length > 0 && popupCosModal.is(':visible')) {
            $(document).one('adminPanelPopupClosed', function() {
                initTutorialHandler();
            });
        } else {
            initTutorialHandler();
        }
    });

    // adminPanelApiAjax 지연 대비
    setTimeout(function() {
        var popupCosModal = $('#panel_popupCos_modal');
        if (!_isTutorialInitialized && !popupCosModal.is(':visible')) {
            initTutorialHandler();
        }
    }, 5000);

</script>

<script>
<?php
$isUsingResponsiveSkin = ($skinConf['skin_type'] ?? '') === 'responsive';
$completedStepCount    = ($hasResponsiveSkin ? 1 : 0) + ($isUsingResponsiveSkin ? 1 : 0);
?>
const HIDE_ONBOARDING_COOKIE = 'hideDesignSkinOnboarding';

const isOnboardingHiddenByCookie = () => {
    return document.cookie
        .split(';')
        .some(c => c.trim().startsWith(HIDE_ONBOARDING_COOKIE + '='));
};

const startSkinOnboarding = () => {
    const onboarding = document.querySelector('.onboarding');
    const progressElem = document.querySelector('.onboarding__progress');
    const onboardingItems = document.querySelectorAll('.onboarding__item-content');
    
    if (!onboarding || !progressElem || !onboardingItems.length) return;

    // '다시 보지 않기' 쿠키가 있으면 온보딩 숨기고 종료
    if (isOnboardingHiddenByCookie()) {
        onboarding.setAttribute('hidden', true);
        return;
    }

    const downloadSkinBtn = document.querySelector('.js-download-responsive-skin');
    const closeBtn = onboarding.querySelector('.js-close-onboarding');
    const onboardingDoNotShowAgainCheckbox = onboarding.querySelector('.js-onboarding-do-not-show-again');

    downloadSkinBtn?.addEventListener('click', () => {
        let popup = window.open('<?= $freeSkinUrl ?>', 'popup', 'width=' + screen.width + ',height=' + screen.height + ',scrollbars=yes');
        window.addEventListener('focus', function() {
            if (popup && popup.closed) {
                location.reload();
                popup = null;
            }
        });
    });

    closeBtn?.addEventListener('click', () => {
        // '다시 보지 않기' 체크 시 만료시간 없는(세션) 쿠키 저장
        if (onboardingDoNotShowAgainCheckbox?.checked) {
            document.cookie = HIDE_ONBOARDING_COOKIE + '=1; path=/';
        }
        onboarding.setAttribute('hidden', true);
    });

    const totalSteps = onboardingItems.length;
    const completedSteps = <?= $completedStepCount ?>;
    const progressValue = totalSteps > 0 ? (completedSteps / totalSteps) * 100 : 0;

    // 진행률 수치 및 ProgressBar 렌더
    const countElem = progressElem.querySelector('.onboarding__progress-count');
    countElem.querySelector('b').innerText = completedSteps;
    progressElem.querySelector('.onboarding__progress-count-total').innerText = totalSteps;
    countElem.insertAdjacentElement('beforebegin', ncua.ProgressBar.create({ value: progressValue }).getElement());

    // summary 기본 토글 동작 차단 (details 수동 제어)
    onboardingItems.forEach(item => {
        item.querySelector('.onboarding__item-title').addEventListener('click', e => e.preventDefault());
    });
};

document.addEventListener('DOMContentLoaded', startSkinOnboarding);
</script>
