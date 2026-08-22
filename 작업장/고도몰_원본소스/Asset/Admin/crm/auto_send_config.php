<?php if (include $autoSendConfigBlock) return; ?>

<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/crm-common.css')?>" rel="stylesheet"/>
<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/auto-send-config.css')?>" rel="stylesheet"/>
<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-preview.css')?>" rel="stylesheet"/>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/file-input.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/switch.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/charcount.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/ncds-jquery-validator.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/unsaved-changes-guard.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/scroll-sticky.js')?>"></script>
<!-- CRM Preview -->
<script src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/godo-ui-module/crm-preview/crm-preview-loader.js')?>" data-exclude="friendtalk"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/godo-ui-module/chip-selector/chip-selector.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/godo-ui-module/message-input/message-input.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/godo-ui-module/godo-ui-module.js')?>"></script>

<form id="autoSendConfigForm" name="autoSendConfigForm" method="post" action="./auto_send_config_ps.php" enctype="multipart/form-data">
    <input type="hidden" name="mode" value="<?= $mode ?>" />
    <input type="hidden" name="code" value="<?= $code ?>" />

    <div class="ncua-page-header page-header js-affix affix-top">
        <h3 class="ncua-help-manual">
            <button type="button" class="ncua-btn ncua-btn--md only-icon ncua-btn--secondary-gray ncua-history-back js-btn-back" onclick="handleAutoSendConfigBack();"  >뒤로가기</button>
            <?php echo end($naviMenu->location); ?>
        </h3>
        <div class="ncua-page-header__actions">
            <button type="submit" class="ncua-btn ncua-btn--md ncua-btn--primary"><span class="ncua-btn__label">저장</span></button>
        </div>
    </div>

    <article class="ncua-content auto_send_config">
        <!-- 발송 설정 -->
        <?php include $autoSendConfigOptions; ?>

        <div class="ncua-split-layout">
            <div class="section-container" id="section-container">
                <!-- 수신 대상 설정 -->
                <?php include $autoSendConfigContents ?>
            </div>
            <aside class="ncua-panel">
                <!-- 알림 메시지 미리보기 -->
                <?php include $autoSendConfigPreview; ?>
            </aside>
        </div>
    </article>
</form>

<script type="text/javascript">
    const AUTO_SEND_CODE = '<?= $code ?>';
    const USE_JOIN_POLICY = <?= $useJoinPolicy ? 'true' : 'false' ?>;

    /**
     * 뒤로가기 버튼 핸들러
     */
    const handleAutoSendConfigBack = () => {
        // unsavedGuard가 있고 변경사항이 있으면 확인 대화
        if (window.unsavedGuard && window.unsavedGuard.isChanged()) {
            NCDSConfirm({
                message: '페이지를 이동하시겠습니까?',
                subMessage: '저장하지 않은 내용이 있습니다.<br/>페이지를 이동하면 설정한 내용이 모두 사라집니다.',
                btnText: {
                    confirmLabel: '이동',
                    cancelLabel: '취소'
                },
                callback: (result) => {
                    if (result) {
                        window.unsavedGuard?.reset();
                        history.back();
                    }
                }
            });
        } else {
            // 변경사항이 없으면 바로 이동
            history.back();
        }
    };

    // 파일 크기 검증
    const validateFiles = (newFiles, maxSize) => {
        // 파일 크기 검증 (3MB = 3 * 1024 * 1024 bytes)
        const maxSizeBytes = maxSize * 1024 * 1024;
        const oversizedFile = newFiles.find(file => file?.size > maxSizeBytes);

        if (oversizedFile) {
            return {
                valid: false,
                message: '이미지 업로드에 실패했습니다.',
                subMessage: '권장사이즈에 맞춰 이미지를 업로드 해주시길 바랍니다.'
            };
        }

        return {
            valid: true,
        };
    }
   
    const MAX_UPLOAD_FILE_SIZE = 3;

    // 마이앱 이미지 상태: { mode: 'FILE'|'URL'|'DELETE', file?: File, url?: string }
    // mode 미설정 = 변경 없음 (기존 이미지 유지)
    const myappImageState = {};
    const myappImageFileInputs = {};

    const isCompleteDataUrl = (value) => {
        if (typeof value !== 'string') return false;

        const dataUrlRegex = /^data:image\/[a-zA-Z0-9.+-]+;base64,[A-Za-z0-9+/=]+$/;
        return dataUrlRegex.test(value);
    };

    // 마이앱 ImageFileInput 생성
    const createMyappImageFileInput = (containerId, recipient) => {
        return new ncua.ImageFileInput({
            container: containerId,
            buttonLabel: '파일 찾기',
            size: 'sm',
            accept: '.jpg,.png,.jpeg',
            multiple: false,
            maxFileCount: 1,
            showFileInput: false,
            hintItems: [
                '권장 사이즈: 640*320px',
                '3MB 이내인 jpg, png 형식의 파일을 등록해 주세요.',
            ],
            onChange: (newFiles) => {
                const validation = validateFiles(newFiles, MAX_UPLOAD_FILE_SIZE);
                if (!validation.valid) {
                    NCDSAlert({message: validation.message, subMessage: validation.subMessage, iconType: 'error'});
                    return;
                }

                myappImageFileInputs[recipient].renderImagePreviews();

                if (newFiles?.length > 0) {
                    myappImageState[recipient] = { mode: 'FILE', file: newFiles[0] };

                    const section = document.getElementById(containerId).closest('section');
                    const existingImageInput = section?.querySelector('.myapp-existing-image');
                    if (existingImageInput) existingImageInput.value = '';

                    const fileSection = document.getElementById(containerId).closest('[data-target-type]');
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        if (isCompleteDataUrl(e.target.result)) {
                            PreviewFacade.setPreview(fileSection, { image: e.target.result });
                        }
                    }
                    reader.readAsDataURL(newFiles[0]);
                }
            },
        });
    };

    // 마이앱 이미지 URL 설정 (setFiles는 초기화 시에만 동작하므로 컴포넌트 재생성)
    const setMyappImageByUrl = (recipient, imageUrl) => {
        const containerId = `myapp-image-file-input-container-${recipient}`;
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = '';
        myappImageFileInputs[recipient] = createMyappImageFileInput(containerId, recipient);
        myappImageFileInputs[recipient].setFiles([{
            fileName: imageUrl.split('/').pop(),
            fileImageUrl: imageUrl
        }]);

        myappImageState[recipient] = { mode: 'URL', url: imageUrl };

        const existingImageInput = container.closest('section')?.querySelector('.myapp-existing-image');
        if (existingImageInput) existingImageInput.value = '';

        PreviewFacade.setPreview(container.closest('[data-target-type]'), { image: imageUrl });
    };

    // 마이앱 이미지 초기화
    const clearMyappImage = (recipient) => {
        const imageInput = myappImageFileInputs[recipient];
        if (imageInput?.clearFiles) imageInput.clearFiles();
        if (imageInput?.renderImagePreviews) imageInput.renderImagePreviews();

        myappImageState[recipient] = { mode: 'DELETE' };

        const section = document.querySelector(`[data-target-type="${recipient}"]`);
        const existingImageInput = section?.querySelector('.myapp-existing-image');
        if (existingImageInput) existingImageInput.value = '';

        PreviewFacade.setPreview(section, { image: '' });
    };

    document.addEventListener('DOMContentLoaded', function() {
        // input , textarea 글자수 카운트 (myapp-title, kakao 필드만 - SMS/MYAPP textarea는 MessageInput이 처리)
        const charCountManager = createCharCountManager({
            targetClasses: ['layer-template-title', 'myapp-template-title','kakao-template-title','kakao-button-page-link','kakao-button-delivery-search', 'kakao-template-textarea-content', 'kakao-template-textarea-add-info'],
        });
        charCountManager.init();

        // 마이앱 이미지 - 수신자별 초기화
        const recipients = ['member', 'admin', 'provider', 'recipient'];

        // 마이앱 이미지 삭제 버튼 캡처 (컴포넌트가 DOM을 제거하기 전에 선점)
        document.addEventListener('click', (e) => {
            const deleteBtn = e.target?.closest?.('.ncua-image-file-input__preview-remove-button');
            if (!deleteBtn) return;

            const recipient = recipients.find(r => deleteBtn.closest(`#myapp-image-file-input-container-${r}`));
            if (!recipient) return;

            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            NCDSConfirm({
                message: '첨부파일을 삭제하시겠습니까?',
                btnText: { confirmLabel: '확인', cancelLabel: '취소' },
                callback: (result) => {
                    if (result) clearMyappImage(recipient);
                },
            });
        }, {capture: true});

        recipients.forEach(recipient => {
            const containerId = `myapp-image-file-input-container-${recipient}`;
            const container = document.getElementById(containerId);
            if (!container) return;

            myappImageFileInputs[recipient] = createMyappImageFileInput(containerId, recipient);

            // 기존 이미지가 있으면 초기 미리보기 설정
            const existingImageInput = container.closest('section').querySelector('.myapp-existing-image');
            if (existingImageInput?.value) {
                myappImageFileInputs[recipient].setFiles([{
                    fileName: existingImageInput.value.split('/').pop(),
                    fileImageUrl: existingImageInput.value
                }]);
                PreviewFacade.setPreview(container.closest('[data-target-type]'), { image: existingImageInput.value });
            }
        });

        // 수신 대상 체크박스 초기화
        initRecipientCheckboxes();

        // 각 섹션의 첫 번째 탭 활성화
        initSectionTabs();

        // 카카오 벤더 미지원 시 disabled 처리
        initKakaoVendorSupport();

        // 섹션 컨테이너 이벤트 초기화
        initSectionContainerEvents();

        // 미리보기 탭 기능 초기화
        initPreviewTabs();

        // 카카오 알림톡 구분(카테고리) 변경 시 템플릿 드롭다운 업데이트
        initKakaoCategoryChange();

        // 스크롤 시 미리보기 섹션 전환 초기화
        initPreviewScrollSync();

        // MessageInput/ChipSelector 모듈 초기화
        initMessageModules(recipients);

        // 재발송 옵션 표시/숨김 초기화
        initResendEnabledToggle();

        // 폼 유효성 검사 초기화
        initFormValidation();

        // CrmPreview 초기화 (MessageInput 등 모든 모듈 초기화 이후 실행)
        const initCrmPreviewAndLoadFirstSection = () => {
            initCrmPreview();
            updatePreviewState();

            if (hasCheckedRecipients()) {
                const firstSection = getFirstVisibleSection();
                if (firstSection) {
                    PreviewFacade.setActiveSection(firstSection);
                    PreviewFacade.syncPreviewWithSection(firstSection);
                }
            }
        };

        if (typeof CrmPreview !== 'undefined' && typeof GodoUIModule !== 'undefined') {
            initCrmPreviewAndLoadFirstSection();
        }

        window.addEventListener('crmPreviewLoaded', function() {
            if (!crmPreview) {
                initCrmPreviewAndLoadFirstSection();
            }
        });

        // 저장하지 않은 변경사항 이탈 방지 (모든 초기화 완료 후 실행)
        try {
            window.unsavedGuard = new UnsavedChangesGuard('#autoSendConfigForm', (proceed) => {
                NCDSConfirm({
                    message: '페이지를 이동하시겠습니까?',
                    subMessage: '저장하지 않은 내용이 있습니다.<br/>페이지를 이동하면 설정한 내용이 모두 사라집니다.',
                    btnText: {
                        confirmLabel: '이동',
                        cancelLabel: '취소'
                    },
                    callback: (result) => {
                        if (result) proceed();
                    }
                });
            });
        } catch {
            // ignore
        }
    });

    // 채널 설정 (발송수단 → 미리보기 탭/CRM 타입 매핑)
    const CHANNEL_CONFIG = {
        'SMS': { previewTab: 'sms', crmType: 'SMS' },
        'KAKAO_ALRIM_TALK': { previewTab: 'kakao', crmType: 'ALIMTALK' },
        'MYAPP_PUSH': { previewTab: 'app', crmType: 'MYAPP' }
    };

    const PREVIEW_TAB_TO_CRM_TYPE = Object.fromEntries(
        Object.values(CHANNEL_CONFIG).map(c => [c.previewTab, c.crmType])
    );

    let crmPreview = null;

    const PreviewFacade = {
        _activeSection: null,

        getActiveSection() { return this._activeSection; },
        setActiveSection(section) { this._activeSection = section; },
        isActiveSection(section) { return !section || section === this._activeSection; },

        findActiveSectionByScroll() {
            const sections = document.querySelectorAll('[data-target-type]');
            let activeSection = null;
            let maxVisibleHeight = 0;
            sections.forEach(section => {
                if (section.style.display === 'none') return;
                const rect = section.getBoundingClientRect();
                const viewportHeight = window.innerHeight;
                const visibleTop = Math.max(rect.top, 0);
                const visibleBottom = Math.min(rect.bottom, viewportHeight);
                const visibleHeight = Math.max(0, visibleBottom - visibleTop);
                if (visibleHeight > maxVisibleHeight) {
                    maxVisibleHeight = visibleHeight;
                    activeSection = section;
                }
            });
            return activeSection;
        },

        setPreview(section, { title, content, image }) {
            if (!crmPreview || !this.isActiveSection(section)) return;
            if (title !== undefined) crmPreview.setTitle(title);
            if (content !== undefined) crmPreview.setContent(resolveReplaceCode(content));
            if (image !== undefined) crmPreview.setImage(image || '');
        },

        setKakaoTemplatePreview(section, templateData) {
            if (!crmPreview || !templateData || !this.isActiveSection(section)) return;

            const wasChecked = crmPreview.isCheckboxChecked();
            crmPreview.reset().setSendType('ALIMTALK').render();
            if (wasChecked) crmPreview.setCheckboxChecked(true);

            if (templateData.templateTitle) {
                crmPreview.setEmTitle(resolveKakaoReplaceCode(templateData.templateTitle));
            }
            if (templateData.templateSubtitle) {
                crmPreview.setEmSubTitle(resolveKakaoReplaceCode(templateData.templateSubtitle));
            }

            if (templateData.templateImageUrl) {
                crmPreview.setImage(templateData.templateImageUrl);
            }

            if (templateData.templateItems && templateData.templateItems.length > 0) {
                if (templateData.templateHeader) {
                    crmPreview.setListHeader(resolveKakaoReplaceCode(templateData.templateHeader));
                }
                if (templateData.templateItemHighlight) {
                    if (templateData.templateItemHighlight.title) {
                        crmPreview.setHighlightTitle(templateData.templateItemHighlight.title);
                        crmPreview.setHighlightDesc(templateData.templateItemHighlight.description);
                        if (templateData.templateItemHighlight.imageUrl) {
                            crmPreview.setHighlightImage(templateData.templateItemHighlight.imageUrl);
                        }
                    }
                }

                const itemList = templateData.templateItems.map(item => ({
                    title: resolveKakaoReplaceCode(item.title),
                    desc: resolveKakaoReplaceCode(item.description),
                    image: item.imageUrl
                }));

                if (templateData.templateItemSummary) {
                    itemList.push({
                        title: templateData.templateItemSummary.title,
                        desc: templateData.templateItemSummary.description,
                        image: templateData.templateItemSummary.imageUrl,
                        isSummary: true
                    });
                }

                crmPreview.setItemList(itemList);
            }

            const bodyContent = templateData.baseContents || templateData.templateContent || '';
            crmPreview.setContent(resolveKakaoReplaceCode(bodyContent));

            if (templateData.templateExtra) {
                crmPreview.setExtraInfo(resolveKakaoReplaceCode(templateData.templateExtra));
            }

            if (templateData.templateButtons && templateData.templateButtons.length > 0) {
                const buttons = [...templateData.templateButtons]
                    .sort((a, b) => {
                        if (a.type === 'ADD_CHANNEL') return -1;
                        if (b.type === 'ADD_CHANNEL') return 1;
                        return a.order - b.order;
                    })
                    .map(btn => ({
                        text: btn.name,
                        url: btn.mobileUrl || btn.pcUrl || '',
                        type: btn.type === 'ADD_CHANNEL' ? 'add-channel' : 'WL'
                    }));

                if (templateData.templateButtons.some(btn => btn.type === 'ADD_CHANNEL')) {
                    crmPreview.setChannelMessage('채널 추가하고 이 채널의 마케팅 메시지 등을 카카오톡으로 받기');
                }
                crmPreview.setButtons(buttons);
            }
        },

        resetKakaoPreview(section) {
            if (!crmPreview || !this.isActiveSection(section)) return;
            const wasChecked = crmPreview.isCheckboxChecked();
            crmPreview.reset().setSendType('ALIMTALK').render();
            if (wasChecked) crmPreview.setCheckboxChecked(true);
            crmPreview.setContent('메시지 내용을 입력하세요.');
        },

        switchPreviewType(crmType) {
            if (!crmPreview) return;
            const wasChecked = crmPreview.isCheckboxChecked();
            crmPreview.reset().setSendType(crmType).render();
            if (wasChecked) crmPreview.setCheckboxChecked(true);
        },

        updatePreviewContent(section) {
            if (!crmPreview || !section) return;

            const tabInfo = getActiveTabInfo(section);
            if (!tabInfo) return;

            const recipient = section.dataset.targetType;

            if (tabInfo.channel === 'SMS') {
                const input = messageInputInstances[recipient]?.SMS;
                const smsContent = input ? normalizeNewlines(input.getValue()) : '';
                this.setPreview(section, { content: smsContent || '메시지 내용을 입력하세요.' });
            } else if (tabInfo.channel === 'KAKAO_ALRIM_TALK') {
                const tabContent = section.querySelector('[data-tab="KAKAO_ALRIM_TALK"]');
                if (!tabContent) return;

                const dropdown = tabContent.querySelector('.kakao-template-dropdown');
                const selectedCode = dropdown?.value;

                if (selectedCode) {
                    const dataScript = tabContent.querySelector('.kakao-templates-data');
                    if (dataScript) {
                        try {
                            const kakaoTemplates = JSON.parse(dataScript.textContent);
                            const categories = ['order', 'regular', 'present', 'member', 'board'];
                            for (const category of categories) {
                                const found = (kakaoTemplates[category] || []).find(t => t.templateCode === selectedCode);
                                if (found) {
                                    this.setKakaoTemplatePreview(section, found);
                                    return;
                                }
                            }
                        } catch (e) { /* ignore */ }
                    }
                }

                const textarea = section.querySelector('.kakao-template-preview textarea');
                const kakaoContent = textarea?.value || '';
                this.setPreview(section, { content: kakaoContent || '메시지 내용을 입력하세요.' });
            } else if (tabInfo.channel === 'MYAPP_PUSH') {
                const input = messageInputInstances[recipient]?.MYAPP_PUSH;
                const myappContent = input ? normalizeNewlines(input.getValue()) : '';
                this.setPreview(section, {
                    title: section.querySelector('.myapp-template-title')?.value || '',
                    content: myappContent || '메시지 내용을 입력하세요.',
                    image: getMyappImageSrc(section),
                });
            }
        },

        syncPreviewTabVisibility(section) {
            const availableChannels = new Set();
            section.querySelectorAll('.ncua-tab-button[data-tab-target]').forEach(btn => {
                availableChannels.add(btn.getAttribute('data-tab-target'));
            });

            document.querySelectorAll('.ncua-tab-button[data-preview-tab]').forEach(btn => {
                const previewTab = btn.getAttribute('data-preview-tab');
                const hasChannel = Object.entries(CHANNEL_CONFIG).some(
                    ([channel, config]) => config.previewTab === previewTab && availableChannels.has(channel)
                );
                const slide = btn.closest('.swiper-slide');
                if (slide) slide.style.display = hasChannel ? '' : 'none';
            });
        },

        syncPreviewWithSection(section) {
            if (!section) return;

            this.syncPreviewTabVisibility(section);

            // 섹션 전환 시 "변수로 변환해서 보기" 체크 해제
            if (crmPreview?.isCheckboxChecked()) {
                crmPreview.setCheckboxChecked(false);
            }

            const tabInfo = getActiveTabInfo(section);
            if (!tabInfo) return;

            const previewTabButton = document.querySelector(`.ncua-tab-button[data-preview-tab="${tabInfo.previewTab}"]`);
            if (previewTabButton && !previewTabButton.classList.contains('is-active')) {
                document.querySelectorAll('.ncua-tab-button[data-preview-tab]').forEach(btn => btn.classList.remove('is-active'));
                previewTabButton.classList.add('is-active');
            }
            this.switchPreviewType(tabInfo.crmType);

            this.updatePreviewContent(section);
        },

        syncChannelTabFromPreviewTab(previewTab) {
            const section = this._activeSection;
            if (!section) return;
            const channelEntry = Object.entries(CHANNEL_CONFIG).find(([, config]) => config.previewTab === previewTab);
            if (!channelEntry) return;
            const [channel] = channelEntry;
            const tabButton = section.querySelector(`.ncua-tab-button[data-tab-target="${channel}"]`);
            if (!tabButton || tabButton.classList.contains('is-active')) return;
            section.querySelectorAll('.tab-content-item').forEach(item => item.style.display = 'none');
            section.querySelectorAll('.ncua-tab-button[data-tab-target]').forEach(btn => btn.classList.remove('is-active'));
            const tabContent = section.querySelector(`[data-tab="${channel}"]`);
            if (tabContent) tabContent.style.display = 'block';
            tabButton.classList.add('is-active');
        }
    };

    // MessageInput/ChipSelector 인스턴스 관리
    const messageInputInstances = {};
    const chipSelectorInstances = {};

    // 템플릿 변수 데이터 (PHP에서 JSON 출력)
    const templateAllVariables = <?= json_encode($groupedTemplateAllVariables, JSON_UNESCAPED_UNICODE) ?>;
    const channelTemplateVariables = <?= json_encode($channelTemplateVariables ?? [], JSON_UNESCAPED_UNICODE) ?>;

    // 치환코드 → 기본값 매핑 빌드
    const replaceCodeDefaults = {};
    Object.values(templateAllVariables).flat().forEach(v => {
        if (v.code && v['default']) {
            replaceCodeDefaults[`{${v.code}}`] = v['default'];
        }
    });

    // 채널별 ChipSelector categories 빌드
    const channelChipSelectorCategories = {};
    Object.entries(channelTemplateVariables).forEach(([channel, groups]) => {
        channelChipSelectorCategories[channel] = Object.entries(groups).map(([category, variables]) => ({
            value: category,
            label: category,
            variables: variables.map(v => ({
                key: `{${v.code}}`,
                label: v.description
            }))
        }));
    });

    const getFirstVisibleSection = () => {
        return document.querySelector('[data-target-type]:not([style*="display: none"])');
    };

    const getActiveTabInfo = (section) => {
        const activeTabButton = section?.querySelector('.ncua-tab-button[data-tab-target].is-active');
        if (!activeTabButton) return null;

        const channel = activeTabButton.getAttribute('data-tab-target');
        const config = CHANNEL_CONFIG[channel];
        return config ? { channel, ...config } : null;
    };

    const getMyappImageSrc = (section) => {
        const imagePreview = section.querySelector('.ncua-image-file-input__preview-container img');
        if (imagePreview?.src && !imagePreview.src.endsWith('/')) return imagePreview.src;
        return section.querySelector('.myapp-existing-image')?.value || '';
    };

    const initCrmPreview = () => {
        if (typeof GodoUIModule === 'undefined' || typeof CrmPreview === 'undefined') return;

        crmPreview = GodoUIModule.render({
            type: 'MessagePreview',
            target: '.crm-preview-container',
            sendType: 'SMS',
            checkboxText: '변수로 변환해서 보기',
        });

        crmPreview.onVariableCheckboxChange(() => {
            const section = PreviewFacade.getActiveSection() || getFirstVisibleSection();
            if (section) PreviewFacade.updatePreviewContent(section);
        });
    };

    const resolveReplaceCode = (text) => {
        if (!text) return text;
        if (crmPreview?.isCheckboxChecked()) return text;
        return text.replace(/\{[^}]+\}/g, (match) => replaceCodeDefaults[match] ?? match);
    };

    const normalizeNewlines = (text) => {
        if (!text) return text;
        return text.replace(/<br\s*\/?>\r?\n?/gi, '\n').replace(/\\r\\n|\\r|\\n/g, '\n').replace(/\r\n|\r/g, '\n');
    };

    const resolveKakaoReplaceCode = (text) => {
        if (!text) return text;
        const normalized = normalizeNewlines(text);
        if (crmPreview?.isCheckboxChecked()) return normalized;
        return normalized.replace(/#\{([^}]+)\}/g, (match, code) => replaceCodeDefaults[`{${code}}`] ?? match);
    };

    // MessageInput/ChipSelector 모듈 초기화
    const initMessageModules = (recipients) => {
        if (typeof GodoUIModule === 'undefined' || typeof MessageInput === 'undefined' || typeof ChipSelector === 'undefined') return;

        const cautionHTML = `<ul class="replace-code-caution"><li class="ncua-caution-text" style="display:none" data-caution-template>템플릿 사용시에 미지원 치환코드가 포함된 경우, 해당 값은 공란으로 발송되므로 미리보기를 확인해주시기 바랍니다.</li></ul>`;

        recipients.forEach(recipient => {
            messageInputInstances[recipient] = {};
            chipSelectorInstances[recipient] = {};

            // SMS MessageInput
            const smsContainer = document.getElementById(`sms-message-input-container-${recipient}`);
            if (smsContainer) {
                const smsInitialValue = normalizeNewlines(smsContainer.dataset.initialValue || '');
                const smsInput = GodoUIModule.render({
                    type: 'MessageInput',
                    target: `#sms-message-input-container-${recipient}`,
                    name: `recipients[${recipient}][SMS][templateContent]`,
                    maxLength: 2000,
                    useBytes: true,
                    hintText: 'SMS 건당 1포인트 차감',
                    initialValue: smsInitialValue,
                    allowEmoji: false,
                    onInput: (input) => {
                        const normalized = normalizeNewlines(input);
                        if (normalized !== input && smsInput.textarea) {
                            const pos = Math.max(0, smsInput.textarea.selectionStart - (input.length - normalized.length));
                            smsInput.textarea.value = normalized;
                            smsInput.textarea.setSelectionRange(pos, pos);
                            smsInput.updateCount();
                        }
                        PreviewFacade.setPreview(smsContainer.closest('[data-target-type]'), { content: normalized });
                        // SMS/LMS 포인트 힌트 업데이트
                        const byteLength = smsInput.calculateLength(normalized);
                        smsInput.setHint(byteLength > 90 ? 'LMS 건당 3포인트 차감' : 'SMS 건당 1포인트 차감');
                        // 빈 내용일 때 안내문구 숨김
                        if (normalized === '') {
                            const caution = document.querySelector(`#sms-chip-selector-container-${recipient} [data-caution-template]`);
                            if (caution) caution.style.display = 'none';
                            crmPreview.setContent('메시지 내용을 입력하세요.');
                        }
                    }
                });
                messageInputInstances[recipient].SMS = smsInput;

                // 초기 힌트 상태 설정
                const hintEl = smsContainer.querySelector('[data-message-hint]');
                if (hintEl) hintEl.classList.add('destructive');
                if (smsInitialValue) {
                    const byteLength = smsInput.calculateLength(smsInitialValue);
                    smsInput.setHint(byteLength > 90 ? 'LMS 건당 3포인트 차감' : 'SMS 건당 1포인트 차감');
                }
            }

            // SMS ChipSelector
            const smsChipContainer = document.getElementById(`sms-chip-selector-container-${recipient}`);
            if (smsChipContainer) {
                chipSelectorInstances[recipient].SMS = GodoUIModule.render({
                    type: 'ChipSelector',
                    target: `#sms-chip-selector-container-${recipient}`,
                    title: '사용 가능한 변수',
                    tooltipSeq: '008',
                    cautionHTML: cautionHTML,
                    categories: channelChipSelectorCategories['SMS'] || [],
                    onSelect: (variable) => {
                        messageInputInstances[recipient]?.SMS?.insertText(variable);
                    }
                });
            }

            // MYAPP MessageInput
            const myappContainer = document.getElementById(`myapp-message-input-container-${recipient}`);
            if (myappContainer) {
                const myappInitialValue = normalizeNewlines(myappContainer.dataset.initialValue || '');
                const myappInput = GodoUIModule.render({
                    type: 'MessageInput',
                    target: `#myapp-message-input-container-${recipient}`,
                    name: `recipients[${recipient}][MYAPP_PUSH][templateContent]`,
                    maxLength: 300,
                    useBytes: false,
                    initialValue: myappInitialValue,
                    onInput: (input) => {
                        const normalized = normalizeNewlines(input);
                        if (normalized !== input && myappInput.textarea) {
                            const pos = Math.max(0, myappInput.textarea.selectionStart - (input.length - normalized.length));
                            myappInput.textarea.value = normalized;
                            myappInput.textarea.setSelectionRange(pos, pos);
                            myappInput.updateCount();
                        }
                        PreviewFacade.setPreview(myappContainer.closest('[data-target-type]'), { content: normalized });
                        // 빈 내용일 때 안내문구 숨김
                        if (normalized === '') {
                            const caution = document.querySelector(`#myapp-chip-selector-container-${recipient} [data-caution-template]`);
                            if (caution) caution.style.display = 'none';
                            crmPreview.setContent('메시지 내용을 입력하세요.');
                        }
                    }
                });
                messageInputInstances[recipient].MYAPP_PUSH = myappInput;
            }

            // MYAPP ChipSelector
            const myappChipContainer = document.getElementById(`myapp-chip-selector-container-${recipient}`);
            if (myappChipContainer) {
                chipSelectorInstances[recipient].MYAPP_PUSH = GodoUIModule.render({
                    type: 'ChipSelector',
                    target: `#myapp-chip-selector-container-${recipient}`,
                    title: '사용 가능한 변수',
                    tooltipSeq: '008',
                    cautionHTML: cautionHTML,
                    categories: channelChipSelectorCategories['MYAPP_PUSH'] || [],
                    onSelect: (variable) => {
                        messageInputInstances[recipient]?.MYAPP_PUSH?.insertText(variable);
                    }
                });
            }
        });
    };

    // 재발송 옵션 표시/숨김 처리
    const initResendEnabledToggle = () => {
        const resendEnabledRadios = document.querySelectorAll('input[type="radio"][name="options[isResendEnabled]"]');
        const resendOptionsContainer = document.querySelector('.resend-enabled-options');
        
        if (!resendOptionsContainer || resendEnabledRadios.length === 0) return;

        // 재발송 옵션 영역 표시/숨김 함수
        const toggleResendOptions = () => {
            const checkedRadio = document.querySelector('input[type="radio"][name="options[isResendEnabled]"]:checked');
            if (checkedRadio) {
                if (checkedRadio.value === 'y') {
                    resendOptionsContainer.style.display = '';
                } else {
                    resendOptionsContainer.style.display = 'none';
                }
            }
        };

        // 초기 상태 설정
        toggleResendOptions();

        // 라디오 버튼 변경 시 처리
        resendEnabledRadios.forEach(radio => {
            radio.addEventListener('change', toggleResendOptions);
        });
    };

    // 폼 유효성 검사 - 헬퍼 함수들
    const getRecipientSection = (recipientType) => {
        return document.querySelector('[data-target-type="' + recipientType + '"]');
    };

    let isSubmitting = false;

    const submitForm = (form) => {
        if (isSubmitting) return;
        isSubmitting = true;

        const formData = new FormData(form);

        // SMS/MYAPP 내용 normalizeNewlines 적용
        Object.entries(messageInputInstances).forEach(([recipient, channels]) => {
            Object.entries(channels).forEach(([channel, input]) => {
                const name = `recipients[${recipient}][${channel}][templateContent]`;
                if (formData.has(name)) {
                    formData.set(name, normalizeNewlines(input.getValue()));
                }
            });
        });

        // 카카오 벤더 미지원 시 카카오 데이터 제거
        document.querySelectorAll('[data-target-type]').forEach(section => {
            if (section.dataset.kakaoVendorSupported === 'false') {
                const recipient = section.dataset.targetType;
                const prefix = `recipients[${recipient}][KAKAO_ALRIM_TALK]`;
                for (const key of [...formData.keys()]) {
                    if (key.startsWith(prefix)) {
                        formData.delete(key);
                    }
                }
            }
        });

        // 마이앱 이미지 상태 전송
        for (const [recipient, state] of Object.entries(myappImageState)) {
            formData.append(`recipients[${recipient}][MYAPP_PUSH][imageMode]`, state.mode);
            if (state.mode === 'FILE') {
                formData.append(`recipients[${recipient}][MYAPP_PUSH][image]`, state.file, state.file.name);
            } else if (state.mode === 'URL') {
                formData.append(`recipients[${recipient}][MYAPP_PUSH][imageUrl]`, state.url);
            }
        }

        $.ajax({
            url: form.action,
            type: form.method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if (data.result) {
                    if (data.insertedSnoMap) {
                        Object.entries(data.insertedSnoMap).forEach(function([recipientType, channels]) {
                            Object.entries(channels).forEach(function([channelName, sno]) {
                                const input = form.querySelector('input[name="recipients[' + recipientType + '][' + channelName + '][templateSno]"]');
                                if (input) {
                                    input.value = sno;
                                }
                            });
                        });
                    }
                    window.unsavedGuard?.reset();
                    NCDSToast({ message: '저장되었습니다.', color: 'success' });
                } else {
                    NCDSToast({ message: data.message || '일시적인 오류가 발생하였습니다.</br>다시 시도해 주세요.', color: 'error' });
                }
            },
            error: function() {
                NCDSToast({ message: '일시적인 오류가 발생하였습니다.</br>다시 시도해 주세요.', color: 'error' });
            },
            complete: function() {
                isSubmitting = false;
            }
        });
    };

    const showConfirmAndSubmit = (form, message, subMessage) => {
        NCDSConfirm({
            message,
            subMessage,
            btnText: { confirmLabel: '확인', cancelLabel: '취소' },
            callback: (result) => {
                if (result) submitForm(form);
            }
        });
    };

    // 발송수단 미선택 섹션 찾기
    const findSectionWithNoChannel = (recipientCheckboxes) => {
        for (const checkbox of recipientCheckboxes) {
            const section = getRecipientSection(checkbox.dataset.recipient);
            if (!section) continue;
            const channelCheckboxes = section.querySelectorAll('input[type="checkbox"][name$="[isEnabled]"]:checked');
            if (channelCheckboxes.length === 0) return section;
        }
        return null;
    };

    // SMS 내용 미입력 섹션 찾기
    const findSectionWithEmptySms = (recipientCheckboxes) => {
        for (const checkbox of recipientCheckboxes) {
            const recipient = checkbox.dataset.recipient;
            const section = getRecipientSection(recipient);
            if (!section) continue;
            const smsCheckbox = section.querySelector('input[type="checkbox"][name$="[SMS][isEnabled]"]:checked');
            if (!smsCheckbox) continue;
            const input = messageInputInstances[recipient]?.SMS;
            if (input && input.getValue().trim() === '') return section;
        }
        return null;
    };

    // 카카오 템플릿 미선택 섹션 찾기
    const findSectionWithNoKakaoTemplate = (recipientCheckboxes) => {
        for (const checkbox of recipientCheckboxes) {
            const section = getRecipientSection(checkbox.dataset.recipient);
            if (!section) continue;
            const kakaoCheckbox = section.querySelector('input[type="checkbox"][name$="[KAKAO_ALRIM_TALK][isEnabled]"]:checked');
            if (!kakaoCheckbox) continue;
            const kakaoTemplateSelect = section.querySelector('.kakao-template-dropdown');
            if (kakaoTemplateSelect && kakaoTemplateSelect.value === '') return section;
        }
        return null;
    };

    // 마이앱 제목/내용 미입력 섹션 찾기
    const findSectionWithEmptyMyapp = (recipientCheckboxes) => {
        for (const checkbox of recipientCheckboxes) {
            const recipient = checkbox.dataset.recipient;
            const section = getRecipientSection(recipient);
            if (!section) continue;
            const myappCheckbox = section.querySelector('input[type="checkbox"][name$="[MYAPP_PUSH][isEnabled]"]:checked');
            if (!myappCheckbox) continue;
            const myappTitle = section.querySelector('.myapp-template-title');
            const input = messageInputInstances[recipient]?.MYAPP_PUSH;
            if ((myappTitle && myappTitle.value.trim() === '') || (input && input.getValue().trim() === '')) return section;
        }
        return null;
    };

    // 마이앱 URL이 입력되었지만 검증되지 않은 섹션 찾기 (member만 해당)
    const findSectionWithUnverifiedMyappUrl = (recipientCheckboxes) => {
        for (const checkbox of recipientCheckboxes) {
            const recipientType = checkbox.dataset.recipient;
            // member인 경우만 검사
            if (recipientType !== 'member') continue;
            
            const section = getRecipientSection(recipientType);
            if (!section) continue;
            const myappCheckbox = section.querySelector('input[type="checkbox"][name$="[MYAPP_PUSH][isEnabled]"]:checked');
            if (!myappCheckbox) continue;
            const myappUrlInput = section.querySelector('.myapp-url-input');
            // URL이 입력되어 있고, 검증되지 않은 경우
            if (myappUrlInput && myappUrlInput.value.trim() !== '' && myappUrlInput.dataset.verified !== 'true') {
                return section;
            }
        }
        return null;
    };

    // SMS 대체발송 내용 미입력 여부 확인
    const hasEmptySmsAlternativeContent = (recipientCheckboxes) => {
        const alternativeChannels = [
            { channel: 'KAKAO_ALRIM_TALK', dataAttr: 'kakaoAlternativeBySms' },
            { channel: 'MYAPP_PUSH', dataAttr: 'myappAlternativeBySms' }
        ];
        for (const checkbox of recipientCheckboxes) {
            const recipient = checkbox.dataset.recipient;
            const section = getRecipientSection(recipient);
            if (!section) continue;
            for (const { channel, dataAttr } of alternativeChannels) {
                if (section.dataset[dataAttr] !== 'true') continue;
                const channelCheckbox = section.querySelector(`input[type="checkbox"][name$="[${channel}][isEnabled]"]:checked`);
                if (!channelCheckbox) continue;
                const input = messageInputInstances[recipient]?.SMS;
                if (input && input.getValue().trim() === '') return true;
            }
        }
        return false;
    };

    // 폼 유효성 검사 초기화
    const initFormValidation = () => {
        const form = document.getElementById('autoSendConfigForm');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const recipientCheckboxes = form.querySelectorAll('input[type="checkbox"][data-recipient]:checked');

            // 1. 수신 대상 체크박스 확인
            if (recipientCheckboxes.length === 0) {
                showConfirmAndSubmit(form,
                    '자동알림을 비활성화 상태로 저장하시겠습니까?',
                    '모든 수신 대상이 선택되어 있지 않아</br>자동알림이 발송되지 않습니다.'
                );
                return false;
            }

            // 2. 발송수단 미선택 확인
            const sectionWithNoChannel = findSectionWithNoChannel(recipientCheckboxes);
            if (sectionWithNoChannel) {
                const channelHint = sectionWithNoChannel.querySelector('.channel-hint');
                if (channelHint) channelHint.hidden = false;
                const targetY = sectionWithNoChannel.getBoundingClientRect().top + window.scrollY - 30;
                window.scrollTo({ top: targetY, behavior: 'smooth' });
                return false;
            }


            const sectionWithEmptySms = findSectionWithEmptySms(recipientCheckboxes); // 3. SMS 내용 미입력 확인
            const sectionWithNoKakaoTemplate = findSectionWithNoKakaoTemplate(recipientCheckboxes); // 4. 카카오 템플릿 미선택 확인
            const sectionWithEmptyMyapp = findSectionWithEmptyMyapp(recipientCheckboxes); // 5. 마이앱 제목/내용 미입력 확인
            if (sectionWithEmptySms || sectionWithNoKakaoTemplate || sectionWithEmptyMyapp) {
                showConfirmAndSubmit(form,
                    '발송을 설정한 항목 중 발송 내용이 없는 항목이 있습니다.</br>이대로 저장하시겠습니까?',
                    '발송 내용이 없는 경우, 저장되더라도 발송되지 않습니다.'
                );
                return false;
            }

            // 5-1. 마이앱 URL 검증 여부 확인
            const sectionWithUnverifiedMyappUrl = findSectionWithUnverifiedMyappUrl(recipientCheckboxes);
            if (sectionWithUnverifiedMyappUrl) {
                NCDSAlert({
                    message: '입력한 푸시URL을 검증해 주세요.',
                    iconType: 'error'
                });
                const urlInput = sectionWithUnverifiedMyappUrl.querySelector('.myapp-url-input');
                if (urlInput) {
                    urlInput.focus();
                    const targetY = sectionWithUnverifiedMyappUrl.getBoundingClientRect().top + window.scrollY - 100;
                    window.scrollTo({ top: targetY, behavior: 'smooth' });
                }
                return false;
            }

            // 6. SMS 대체발송 내용 미입력 확인
            if (hasEmptySmsAlternativeContent(recipientCheckboxes)) {
                showConfirmAndSubmit(form,
                    '대체 발송에 사용할 SMS 발송 내역이 입력되지 않았습니다.</br>이대로 저장하시겠습니까?',
                    '발송 내용이 없는 경우, 저장되더라도 발송되지 않습니다.'
                );
                return false;
            }

            submitForm(form);
        });
    };

    // 체크된 수신 대상 체크박스가 있는지 확인
    const hasCheckedRecipients = () => {
        const checkedCheckboxes = document.querySelectorAll('input[type="checkbox"][data-recipient]:checked');
        return checkedCheckboxes.length > 0;
    };

    // 미리보기 상태 업데이트 (체크박스 유무에 따라)
    const updatePreviewState = () => {
        const previewContentWrap = document.querySelector('.preview-content-wrap');
        if (!previewContentWrap) return;

        const hasRecipients = hasCheckedRecipients();
        const nonePreview = previewContentWrap.querySelector('[data-preview-content="none"]');
        const crmPreviewContainer = previewContentWrap.querySelector('.crm-preview-container');
        const previewTitleElement = document.getElementById('preview-recipient-title');

        if (!hasRecipients) {
            if (crmPreviewContainer) crmPreviewContainer.style.display = 'none';
            if (nonePreview) nonePreview.style.display = 'flex';
            if (previewTitleElement) previewTitleElement.textContent = '';
        } else {
            if (nonePreview) nonePreview.style.display = 'none';
            if (crmPreviewContainer) crmPreviewContainer.style.display = 'block';
        }
    };

    // 수신 대상 체크박스 초기화
    const initRecipientCheckboxes = () => {
        // 섹션 표시/숨김 처리
        const toggleRecipientSection = (recipientType, shouldShow) => {
            const section = document.querySelector('[data-target-type="' + recipientType + '"]');
            if (section) {
                section.style.display = shouldShow ? 'block' : 'none';
            }
        };

        // 해당 수신대상의 모든 채널 체크박스 언체크
        const uncheckAllChannels = (recipientType) => {
            const section = document.querySelector('[data-target-type="' + recipientType + '"]');
            if (!section) return;

            const channelCheckboxes = section.querySelectorAll('input[type="checkbox"][name$="[isEnabled]"]');
            channelCheckboxes.forEach(chk => {
                chk.checked = false;
                const hidden = chk.previousElementSibling;
                if (hidden && hidden.type === 'hidden' && hidden.name === chk.name) {
                    hidden.value = 'n';
                }
            });
        };

        // 해당 수신대상 섹션으로 스크롤 이동
        const scrollToSection = (recipientType) => {
            const section = document.querySelector('[data-target-type="' + recipientType + '"]');
            if (section) {
                const targetY = section.getBoundingClientRect().top + window.scrollY - 100;
                window.scrollTo({ top: targetY, behavior: 'smooth' });
            }
        };

        document.querySelectorAll('input[type="checkbox"][data-recipient]').forEach(checkbox => {
            // 초기 상태 설정
            toggleRecipientSection(checkbox.dataset.recipient, checkbox.checked);

            checkbox.addEventListener('change', function(e) {
                const recipientType = this.dataset.recipient;

                if (!this.checked) {
                    // 언체크 시 NCDSConfirm 표시
                    // 먼저 체크 상태를 유지 (확인 시에만 언체크 처리)
                    this.checked = true;

                    NCDSConfirm({
                        message: '해당 수신대상에게 알림을 발송하지 않겠습니까?',
                        subMessage: '수신대상을 선택 해제하면 설정된 발송수단이 모두 해제되며, \n이 대상에게는 알림이 발송되지 않습니다.',
                        btnText: {
                            confirmLabel: '확인',
                            cancelLabel: '취소'
                        },
                        callback: (result) => {
                            if (result) {
                                // 확인: 발송수단 모두 언체크, 수신대상 언체크, 섹션 숨김
                                uncheckAllChannels(recipientType);
                                this.checked = false;
                                toggleRecipientSection(recipientType, false);
                                updatePreviewState();
                            }
                            // 취소: 체크 상태 유지 (이미 this.checked = true)
                        }
                    });
                } else {
                    // 가입승인 코드 + 승인 정책 미사용 시 회원 체크 차단
                    if (AUTO_SEND_CODE === 'APPROVAL' && !USE_JOIN_POLICY && recipientType === 'member') {
                        this.checked = false;
                        NCDSAlert({
                            message: '가입승인 절차 또는 연령 제한 설정이 되어 있는 경우 사용할 수 있는 알림입니다.',
                            subMessage: '[회원 > 회원 관리 > 회원 가입 정책 관리]에서 가입 설정을 변경 후 다시 시도해 주세요.',
                            iconType: 'error'
                        });
                        return;
                    }

                    // 체크 시: 섹션 노출 및 스크롤 이동
                    toggleRecipientSection(recipientType, true);
                    updatePreviewState();

                    // DOM 업데이트 후 스크롤 이동
                    setTimeout(() => {
                        scrollToSection(recipientType);
                    }, 50);
                }
            });
        });

        // 초기 미리보기 상태 설정
        updatePreviewState();
    };

    // 각 섹션의 첫 번째 탭 활성화
    const initSectionTabs = () => {
        document.querySelectorAll('[data-target-type]').forEach(section => {
            // 모든 탭 숨기고 버튼 비활성화
            section.querySelectorAll('.tab-content-item').forEach(item => item.style.display = 'none');
            section.querySelectorAll('.ncua-tab-button[data-tab-target]').forEach(btn => btn.classList.remove('is-active'));

            // 첫 번째 탭 활성화
            const firstTabButton = section.querySelector('.ncua-tab-button[data-tab-target]');
            if (firstTabButton) {
                const channel = firstTabButton.dataset.tabTarget;
                const tabContent = section.querySelector('[data-tab="' + channel + '"]');
                if (tabContent) tabContent.style.display = 'block';
                firstTabButton.classList.add('is-active');
            }
        });
    };

    // 카카오 벤더 미지원 시 카카오 알림톡 탭 내 하위 요소 disabled + 언체크 처리
    const initKakaoVendorSupport = () => {
        document.querySelectorAll('[data-target-type]').forEach(section => {
            if (section.dataset.kakaoVendorSupported === 'false') {
                // 카카오 체크박스 언체크 + disabled
                const kakaoCheckbox = section.querySelector('input[type="checkbox"][name$="[KAKAO_ALRIM_TALK][isEnabled]"]');
                if (kakaoCheckbox) {
                    kakaoCheckbox.checked = false;
                    kakaoCheckbox.disabled = true;
                }

                // 카카오 탭 내 모든 요소 disabled + 체크박스/라디오 언체크
                const kakaoTab = section.querySelector('[data-tab="KAKAO_ALRIM_TALK"]');
                if (!kakaoTab) return;
                kakaoTab.querySelectorAll('input, select, textarea, button').forEach(el => {
                    el.disabled = true;
                    if (el.type === 'checkbox' || el.type === 'radio') {
                        el.checked = false;
                    }
                });
            }
        });
    };

    // 섹션 컨테이너 이벤트 초기화
    const initSectionContainerEvents = () => {
        const sectionContainer = document.getElementById('section-container');
        if (!sectionContainer) return;

        // 탭 버튼 클릭 시 탭 전환
        sectionContainer.addEventListener('click', function(e) {
            const tabButton = e.target.closest('.ncua-tab-button[data-tab-target]');
            if (tabButton) {
                const section = tabButton.closest('[data-target-type]');
                if (!section) return;

                const channel = tabButton.dataset.tabTarget;

                // 모든 탭 숨기고 버튼 비활성화
                section.querySelectorAll('.tab-content-item').forEach(item => item.style.display = 'none');
                section.querySelectorAll('.ncua-tab-button[data-tab-target]').forEach(btn => btn.classList.remove('is-active'));

                // 클릭한 탭 활성화
                const tabContent = section.querySelector('[data-tab="' + channel + '"]');
                if (tabContent) tabContent.style.display = 'block';
                tabButton.classList.add('is-active');

                // 미리보기 탭 동기화 및 내용 업데이트
                PreviewFacade.syncPreviewWithSection(section);
            }

            // MYAPP URL - 검증 버튼 클릭
            const verifyBtn = e.target.closest('.myapp-url-verify-btn');
            if (verifyBtn && !verifyBtn.disabled) {
                const td = verifyBtn.closest('td');
                const urlInput = td.querySelector('.myapp-url-input');
                const path = urlInput.value.trim();

                if (!path) {
                    NCDSAlert({message: 'URL 경로를 입력해주세요.', iconType: 'warning'});
                    return;
                }

                // 도메인은 형제 span에서 가져오기
                const domainSpan = verifyBtn.closest('div').querySelector('span');
                const mallDomain = domainSpan ? domainSpan.textContent.trim().replace(/\/+$/, '') : '';
                const fullUrl = path.startsWith('http') ? path : mallDomain + path;

                verifyBtn.disabled = true;
                const btnLabel = verifyBtn.querySelector('.ncua-btn__label');
                const originalText = btnLabel.textContent;

                fetch(fullUrl, {method: 'HEAD'})
                    .then(response => {
                        if (response.ok) {
                            urlInput.dataset.verified = 'true';
                            window.open(fullUrl, 'myappUrlPreview', 'width=430,height=800,scrollbars=yes,resizable=yes');
                        } else {
                            urlInput.dataset.verified = 'false';
                            NCDSAlert({
                                message: 'URL에 접근할 수 없습니다.\n경로를 확인해주세요.\n\n' + fullUrl,
                                iconType: 'error'
                            });
                        }
                    })
                    .catch(() => {
                        // CORS 에러일 경우 검증 성공으로 처리
                        urlInput.dataset.verified = 'true';
                        window.open(fullUrl, 'myappUrlPreview', 'width=430,height=800,scrollbars=yes,resizable=yes');
                    })
                    .finally(() => {
                        verifyBtn.disabled = false;
                        btnLabel.textContent = originalText;
                    });
            }
        });

        // change 이벤트
        sectionContainer.addEventListener('change', function(e) {
            // 채널 체크박스 선택 시 힌트 숨김
            if (e.target.matches('input[type="checkbox"][name$="[isEnabled]"]')) {
                const section = e.target.closest('[data-target-type]');
                if (section) {
                    const channelHint = section.querySelector('.channel-hint');
                    if (channelHint) {
                        channelHint.hidden = true;
                    }
                }
            }
        });

        // input 이벤트 (myapp-title, myapp-url 등 수동 input만 처리)
        sectionContainer.addEventListener('input', function(e) {
            if (e.target.classList.contains('myapp-template-title')) {
                PreviewFacade.setPreview(e.target.closest('[data-target-type]'), { title: e.target.value });
            }
            if (e.target.classList.contains('myapp-url-input')) {
                const verifyBtn = e.target.closest('td').querySelector('.myapp-url-verify-btn');
                // URL이 변경되면 검증 상태 초기화
                e.target.dataset.verified = 'false';
                if (e.target.value.trim()) {
                    verifyBtn.disabled = false;
                    verifyBtn.classList.remove('is-disable');
                } else {
                    verifyBtn.disabled = true;
                    verifyBtn.classList.add('is-disable');
                }
            }
        });

        // 카카오 알림톡 템플릿 미리보기 (MutationObserver)
        // 카카오 탭이 활성화된 섹션의 textarea 변경만 미리보기에 반영
        const kakaoTextareaObservers = new Set();

        const observeKakaoPreview = () => {
            const kakaoTextareas = document.querySelectorAll('.kakao-template-preview textarea');
            kakaoTextareas.forEach(textarea => {
                if (kakaoTextareaObservers.has(textarea)) return;
                kakaoTextareaObservers.add(textarea);

                const observer = new MutationObserver(() => {
                    const section = textarea.closest('[data-target-type]');
                    if (!section || section.style.display === 'none') return;

                    const tabInfo = getActiveTabInfo(section);
                    if (tabInfo?.channel === 'KAKAO_ALRIM_TALK') {
                        PreviewFacade.setPreview(section, { content: textarea.value });
                    }
                });
                observer.observe(textarea, { attributes: true, childList: true, subtree: true });
            });
        };

        const containerObserver = new MutationObserver(() => {
            observeKakaoPreview();
        });
        containerObserver.observe(sectionContainer, { childList: true, subtree: false });
    };

    const initPreviewScrollSync = () => {
        const previewTitleElement = document.getElementById('preview-recipient-title');
        if (!previewTitleElement) return;

        const updatePreviewTitle = (section) => {
            const headerTitle = section?.querySelector('.ncua-card__title');
            if (headerTitle) {
                const fullTitle = headerTitle.textContent.trim();
                const recipientTitle = fullTitle.replace(' 알림 발송수단 설정', '');
                previewTitleElement.textContent = recipientTitle + ' ';
            }
        };

        const updatePreviewByScroll = () => {
            if (!hasCheckedRecipients()) return;
            const activeSection = PreviewFacade.findActiveSectionByScroll();
            if (activeSection) {
                updatePreviewTitle(activeSection);
                if (PreviewFacade.getActiveSection() !== activeSection) {
                    PreviewFacade.setActiveSection(activeSection);
                    PreviewFacade.syncPreviewWithSection(activeSection);
                }
            }
        };

        let scrollTimeout;
        window.addEventListener('scroll', () => {
            if (scrollTimeout) return;
            scrollTimeout = setTimeout(() => {
                updatePreviewByScroll();
                scrollTimeout = null;
            }, 50);
        });

        document.querySelectorAll('input[type="checkbox"][data-recipient]').forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                setTimeout(updatePreviewByScroll, 10);
            });
        });

        updatePreviewByScroll();
    };


    // 미리보기 탭 기능 초기화
    const initPreviewTabs = () => {
        const previewContentWrap = document.querySelector('.preview-content-wrap');
        if (!previewContentWrap) return;

        const tabButtons = document.querySelectorAll('.ncua-tab-button[data-preview-tab]');
        if (tabButtons.length === 0) return;

        tabButtons.forEach((button) => {
            button.addEventListener('click', (event) => {
                const targetTab = event.currentTarget.getAttribute('data-preview-tab');

                tabButtons.forEach(btn => btn.classList.remove('is-active'));
                event.currentTarget.classList.add('is-active');

                if (hasCheckedRecipients()) {
                    PreviewFacade.switchPreviewType(PREVIEW_TAB_TO_CRM_TYPE[targetTab]);
                    PreviewFacade.syncChannelTabFromPreviewTab(targetTab);
                    const activeSection = PreviewFacade.getActiveSection();
                    if (activeSection) PreviewFacade.updatePreviewContent(activeSection);
                }
            });
        });
    };

    // 고도몰 템플릿 여부 판별 (템플릿 이름이 'g:'로 시작하면 고도몰 템플릿)
    const isGodomallTemplate = (templateName) => {
        return templateName && templateName.startsWith('g:');
    };

    // 카카오 템플릿 드롭다운 업데이트 공통 함수
    const updateKakaoTemplateDropdown = (tabContent, recipient, kakaoTemplates, selectedCategory, selectedType, shouldResetSelection = false) => {
        const dropdown = tabContent.querySelector('.kakao-template-dropdown');
        if (!dropdown) return;

        // 드롭다운 옵션 업데이트
        dropdown.innerHTML = '<option value="">템플릿을 선택하세요</option>';

        // 선택된 카테고리의 템플릿 가져오기
        const categoryTemplates = kakaoTemplates[selectedCategory] || [];

        // 고도몰/사용자 템플릿 필터링
        const filteredTemplates = categoryTemplates.filter((template) => {
            const isGodomall = isGodomallTemplate(template.templateName);
            return selectedType === 'godomall' ? isGodomall : !isGodomall;
        });

        const currentHiddenInput = tabContent.querySelector(`.kakao-selected-template-${selectedType}[data-recipient="${recipient}"]`);
        const savedTemplateCode = shouldResetSelection ? '' : (currentHiddenInput ? currentHiddenInput.value : '');

        filteredTemplates.forEach((template) => {
            const option = document.createElement('option');
            option.value = template.templateCode;
            option.textContent = template.templateName;
            if (savedTemplateCode && template.templateCode === savedTemplateCode) {
                option.selected = true;
            }
            dropdown.appendChild(option);
        });

        // 미리보기 업데이트
        const previewTextarea = tabContent.querySelector('.kakao-template-preview textarea');
        let savedTemplate = null;
        if (savedTemplateCode && !shouldResetSelection) {
            savedTemplate = filteredTemplates.find(t => t.templateCode === savedTemplateCode);
        }
        if (previewTextarea) {
            previewTextarea.value = savedTemplate ? normalizeNewlines(savedTemplate.templateContent) : '';
        }
        const kakaoSection = tabContent.closest('[data-target-type]');
        if (savedTemplate) {
            PreviewFacade.setKakaoTemplatePreview(kakaoSection, savedTemplate);
        } else {
            PreviewFacade.resetKakaoPreview(kakaoSection);
        }

        return filteredTemplates;
    };

    // 카카오 알림톡 구분 및 템플릿 타입 변경 시 템플릿 드롭다운 업데이트
    const initKakaoCategoryChange = () => {
        // 구분(카테고리) 라디오 버튼 변경 이벤트
        const categoryRadios = document.querySelectorAll('input[name$="[KAKAO_ALRIM_TALK][category]"]');
        categoryRadios.forEach((radio) => {
            radio.addEventListener('change', (event) => {
                const selectedCategory = event.target.value;
                const section = event.target.closest('[data-target-type]');
                if (!section) return;
                const recipient = section.dataset.targetType;

                const tabContent = section.querySelector('[data-tab="KAKAO_ALRIM_TALK"]');
                if (!tabContent) return;

                // 현재 선택된 템플릿 타입(고도몰/사용자) 확인
                const checkedTypeRadio = tabContent.querySelector('.kakao-template-type-radio:checked');
                const selectedType = checkedTypeRadio ? checkedTypeRadio.value : 'godomall';

                // JSON 데이터 파싱
                const dataScript = tabContent.querySelector('.kakao-templates-data');
                if (!dataScript) return;

                let kakaoTemplates = {};
                try {
                    kakaoTemplates = JSON.parse(dataScript.textContent);
                } catch (e) {
                    console.error('카카오 템플릿 데이터 파싱 오류:', e);
                    return;
                }

                // 구분 변경 시 템플릿 선택 초기화 (shouldResetSelection = true)
                updateKakaoTemplateDropdown(tabContent, recipient, kakaoTemplates, selectedCategory, selectedType, true);
            });
        });

        // 템플릿 타입(고도몰/사용자) 라디오 버튼 변경 이벤트
        const templateTypeRadios = document.querySelectorAll('.kakao-template-type-radio');

        templateTypeRadios.forEach((radio) => {
            radio.addEventListener('change', (event) => {
                const selectedType = event.target.value; // 'godomall' or 'user'
                const previousType = selectedType === 'godomall' ? 'user' : 'godomall';
                const recipient = event.target.dataset.recipient;

                const section = document.querySelector(`[data-target-type="${recipient}"]`);
                if (!section) return;

                const tabContent = section.querySelector('[data-tab="KAKAO_ALRIM_TALK"]');
                if (!tabContent) return;

                const dropdown = tabContent.querySelector('.kakao-template-dropdown');
                if (!dropdown) return;

                // 직전 선택 템플릿 저장용 hidden input
                const previousHiddenInput = tabContent.querySelector(`.kakao-selected-template-${previousType}[data-recipient="${recipient}"]`);

                // 현재 선택된 템플릿을 이전 유형의 hidden input에 저장
                if (previousHiddenInput && dropdown.value) {
                    previousHiddenInput.value = dropdown.value;
                }

                // 현재 선택된 카테고리 확인
                const checkedCategoryRadio = tabContent.querySelector('input[name$="[KAKAO_ALRIM_TALK][category]"]:checked');
                const selectedCategory = checkedCategoryRadio ? checkedCategoryRadio.value : 'order';

                // JSON 데이터 파싱
                const dataScript = tabContent.querySelector('.kakao-templates-data');
                if (!dataScript) return;

                let kakaoTemplates = {};
                try {
                    kakaoTemplates = JSON.parse(dataScript.textContent);
                } catch (e) {
                    console.error('카카오 템플릿 데이터 파싱 오류:', e);
                    return;
                }

                // 템플릿 타입 변경 시에는 저장된 값 유지 시도
                updateKakaoTemplateDropdown(tabContent, recipient, kakaoTemplates, selectedCategory, selectedType, false);
            });
        });

        // 템플릿 드롭다운 선택 변경 시 미리보기 textarea 업데이트 및 hidden input 저장
        const templateDropdowns = document.querySelectorAll('.kakao-template-dropdown');
        templateDropdowns.forEach((dropdown) => {
            dropdown.addEventListener('change', (event) => {
                const selectedCode = event.target.value;
                const recipient = event.target.dataset.recipient;

                const section = document.querySelector(`[data-target-type="${recipient}"]`);
                if (!section) return;

                const tabContent = section.querySelector('[data-tab="KAKAO_ALRIM_TALK"]');
                if (!tabContent) return;

                // 현재 선택된 템플릿 유형 확인
                const checkedRadio = tabContent.querySelector('.kakao-template-type-radio:checked');
                const currentType = checkedRadio ? checkedRadio.value : 'godomall';

                // 현재 유형의 hidden input에 선택값 저장
                const currentHiddenInput = tabContent.querySelector(`.kakao-selected-template-${currentType}[data-recipient="${recipient}"]`);
                if (currentHiddenInput) {
                    currentHiddenInput.value = selectedCode;
                }

                if (!selectedCode) {
                    // 빈 값 선택 시 미리보기 초기화
                    const previewTextarea = tabContent.querySelector('.kakao-template-preview textarea');
                    if (previewTextarea) {
                        previewTextarea.value = '';
                    }
                    PreviewFacade.resetKakaoPreview(section);
                    return;
                }

                const dataScript = tabContent.querySelector('.kakao-templates-data');
                if (!dataScript) return;

                let kakaoTemplates = {};
                try {
                    kakaoTemplates = JSON.parse(dataScript.textContent);
                } catch (e) {
                    console.error('카카오 템플릿 데이터 파싱 오류:', e);
                    return;
                }

                // 모든 카테고리에서 선택된 템플릿 찾기
                let selectedTemplate = null;
                const categories = ['order', 'regular', 'present', 'member', 'board'];
                for (const category of categories) {
                    const categoryTemplates = kakaoTemplates[category] || [];
                    const found = categoryTemplates.find(t => t.templateCode === selectedCode);
                    if (found) {
                        selectedTemplate = found;
                        break;
                    }
                }

                if (selectedTemplate) {
                    const previewTextarea = tabContent.querySelector('.kakao-template-preview textarea');
                    if (previewTextarea) {
                        previewTextarea.value = normalizeNewlines(selectedTemplate.templateContent);
                    }
                    PreviewFacade.setKakaoTemplatePreview(section, selectedTemplate);
                }
            });
        });
    };

    let isLoadingTemplate = false;

    const load_as_template = (recipient, channel) => {
        if (isLoadingTemplate) return;
        isLoadingTemplate = true;

        // 콜백 설정: 템플릿 선택 시 해당 채널의 MessageInput에 내용 적용
        window.onTemplateSelect = (contents, sno, myappImage, subject, url) => {
            const channelKey = channel; // 'SMS' or 'MYAPP_PUSH'
            const input = messageInputInstances[recipient]?.[channelKey];
            if (input) {
                input.setValue('');
                input.insertText(normalizeNewlines(contents)); // onInput 콜백 → preview 자동 업데이트
            }

            // 마이앱: 제목, URL, 이미지 처리
            if (channel === 'MYAPP_PUSH') {
                const section = document.querySelector(`[data-target-type="${recipient}"]`);

                // 제목
                const titleInput = section?.querySelector('.myapp-template-title');
                if (titleInput) {
                    titleInput.value = subject || '';
                    titleInput.dispatchEvent(new Event('input', { bubbles: true }));
                }

                // URL
                const urlInput = section?.querySelector('.myapp-url-input');
                if (urlInput) {
                    urlInput.value = url || '';
                    urlInput.dataset.verified = 'false';
                    urlInput.dispatchEvent(new Event('input', { bubbles: true }));
                }

                // 이미지: 있으면 세팅, 없으면 기존 이미지 제거
                if (myappImage) {
                    setMyappImageByUrl(recipient, myappImage);
                } else {
                    clearMyappImage(recipient);
                }
            }

            // 템플릿 불러오기 후 안내문구 표시
            const containerSelector = channel === 'SMS'
                ? `#sms-chip-selector-container-${recipient}`
                : `#myapp-chip-selector-container-${recipient}`;
            const cautionEl = document.querySelector(`${containerSelector} [data-caution-template]`);
            if (cautionEl) cautionEl.style.display = '';
        };

        $.post('./auto_send_config/layer_auto_send_template.php', { channel: channel }, function (data) {
            ncds_layer_popup({ message: data, title: '템플릿 불러오기' , size: 'wide-sm'});
        }).always(function() {
            isLoadingTemplate = false;
        });
    }

    const save_as_template = (recipient, channel) => {
        if (isLoadingTemplate) return;
        isLoadingTemplate = true;
        try {
            const channelKey = channel; // 'SMS' or 'MYAPP_PUSH'
            const input = messageInputInstances[recipient]?.[channelKey];
            const contents = input ? normalizeNewlines(input.getValue()) : '';

            if (!contents) {
                NCDSAlert({
                    message: '내용을 입력해 주세요.',
                    iconType: 'error'
                });
                return;
            }

            const postData = { channel: channel, contents: contents };
            if (channel === 'MYAPP_PUSH') {
                const section = document.querySelector(`[data-target-type="${recipient}"]`);
                postData.title = section?.querySelector('.myapp-template-title')?.value || '';
                postData.url = section?.querySelector('.myapp-url-input')?.value || '';

                const imageState = myappImageState[recipient];
                if (imageState?.mode === 'FILE' && imageState.file) {
                    window._templateImageFile = imageState.file;
                    postData.image = '';
                } else {
                    window._templateImageFile = null;
                    postData.image = getMyappImageSrc(section);
                }
            }

            $.post('./auto_send_config/layer_auto_send_save_template.php', postData, function (data) {
                ncds_layer_popup({
                    message: data,
                    title: '템플릿으로 저장하기<div class="modal-title-description">작성한 내용을 저장할 카테고리와 제목을 선택하여 주시기 바랍니다.</div>',
                    size: 'wide-sm'
                });
            });
        } finally {
            isLoadingTemplate = false;
        }
    }

    const check_kakao_template_comment = () => {
        $.post('./layer_kakao_template_comment.php', null, function (data) {
            ncds_layer_popup({ message: data, title: '카카오 알림톡 템플릿 검수 답변<div class="modal-title-description">템플릿 검수에 대한 문의사항은 <a href="https://cs.kakao.com/requests?category=481&locale=ko&node=46235&service=159 " target="_blank" class="ncua-link">카카오톡 고객센터</a>로 문의하시기 바랍니다.</div>' });
        });
    }
</script>

<!-- 툴팁 가이드 -->
<script defer type="text/javascript">
    const code = 251226001;
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
        });
    }
</script>
