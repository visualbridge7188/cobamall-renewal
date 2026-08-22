<link rel="stylesheet" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'css/layer-checklist.css')?>">
<div class="modal-dialog__content">
    <article class="ncua-content layer-checklist">
        <div class="checklist-content">
            <div class="checklist-item">
                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                        <input type="checkbox" />
                    </span>
                    <span>
                        <span class="ncua-checkbox-field__text">이전 데이터 검수</span>
                        <span class="ncua-checkbox-field__support-text">PG, 상품, 주문, 회원, 게시판, 기본 설정 데이터</span>
                    </span>
                </label>
            </div>
            <div class="checklist-item">
                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                        <input type="checkbox" />
                    </span>
                    <span>
                        <span class="ncua-checkbox-field__text">쇼핑몰 오픈 준비</span>
                        <span class="ncua-checkbox-field__support-text">디자인, 부가서비스 등 쇼핑몰 오픈에 필요한 설정 세팅</span>
                    </span>
                </label>
            </div>
            <div class="checklist-item">
                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                        <input type="checkbox" />
                    </span>
                    <span>
                        <span class="ncua-checkbox-field__text">쇼핑몰 회원 임시 패스워드 변환 고지 안내</span>
                        <span class="ncua-checkbox-field__support-text">2013년 1월 1일 이전 가입 후 미로그인 회원 대상 변경 안내</span>
                    </span>
                </label>
            </div>
            <div class="checklist-item">
                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                        <input type="checkbox" />
                    </span>
                    <span>
                        <span class="ncua-checkbox-field__text">네이버 쇼핑 EP DB URL 설정</span>
                        <span class="ncua-checkbox-field__support-text">연결 url 점검</span>
                    </span>
                </label>
            </div>
            <div class="checklist-item">
                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                        <input type="checkbox" />
                    </span>
                    <span>
                        <span class="ncua-checkbox-field__text">광고영역 URL 설정 확인</span>
                        <span class="ncua-checkbox-field__support-text">네이버 쇼핑광고, 브랜드 검색, 파워링크 등</span>
                    </span>
                </label>
            </div>
            <div class="checklist-item">
                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                        <input type="checkbox" />
                    </span>
                    <span>
                        <span class="ncua-checkbox-field__text">외부 연동 솔루션 체크 (open api)</span>
                        <span class="ncua-checkbox-field__support-text">주문, 상품 등 데이터 수집 연동 점검</span>
                    </span>
                </label>
            </div>
        </div>
    </article>
</div>
<div class="modal-dialog__footer">
    <button type="button" id="todayNotVisible" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray">오늘 하루 다시 보지 않기</button>
    <button type="button" id="popupNotVisible" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray">팝업 다시 보지 않기</button>
    <a href="#" id="" class="ncua-btn ncua-btn--sm ncua-btn--primary">상세 가이드 보기</a>
</div>


<script type="text/javascript">
    const CHECKLIST_KEY = 'migrationChecklistChecked';
    /**
     * BODY에 NCDS 클래스 추가 및 제거
     */
    
    function addNcdsClass() {
        if (!document.body.classList.contains('ncds')) {
            document.body.classList.add('ncds');
        }
    }

    function removeNcdsClass() {
        document.body.classList.remove('ncds');
    }

    function observePopupRemoval() {
        const currentScript = document.currentScript || document.scripts[document.scripts.length - 1];
        const popupContainer = currentScript.closest('.layer_popup, .modal, [class*="popup"]') || currentScript.parentElement;
        
        if (popupContainer) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.removedNodes.forEach(function(node) {
                        if (node === popupContainer || node.contains(popupContainer)) {
                            removeNcdsClass();
                            observer.disconnect();
                        }
                    });
                });
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }

    /**
     * 버튼 노출 제어
     */
    function updateButtonVisibility() {
        const checkboxes = document.querySelectorAll('.checklist-item input[type="checkbox"]');
        const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
        
        const todayNotVisibleBtn = document.getElementById('todayNotVisible');
        const popupNotVisibleBtn = document.getElementById('popupNotVisible');
        
        if (allChecked) {
            todayNotVisibleBtn.style.display = 'none';
            popupNotVisibleBtn.style.display = 'inline-flex';
        } else {
            todayNotVisibleBtn.style.display = 'inline-flex';
            popupNotVisibleBtn.style.display = 'none';
        }
    }

    /**
     * 체크리스트 체크 이벤트
     */
    function checkChecklist(event) {
        const checkbox = event.target;
        const checklistItem = checkbox.closest('.checklist-item');
        
        if (checkbox.checked) {
            checklistItem.classList.add('active');
            saveChecklistState();
        } else {
            checklistItem.classList.remove('active');
            saveChecklistState();
        }

        updateButtonVisibility();
    }

    document.querySelectorAll('.checklist-item input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', checkChecklist);
    });

    restoreChecklistState();
    
    // body에 이미 ncds 클래스가 있으면 추가/감시 로직 스킵
    if (!document.body.classList.contains('ncds')) {
        addNcdsClass();
        observePopupRemoval();
    }
    
    updateButtonVisibility();
    bindButtonEvents();

    // 오늘 하루 보지 않기 저장
    function setTodayNotVisible() {
        const today = new Date().toISOString().slice(0, 10);
        localStorage.setItem('migrationChecklistTodayNotVisible', today);
        $('div.bootstrap-dialog-close-button').click();
    }

    // 팝업 다시 보지 않기 저장
    function setPopupNotVisible() {
        localStorage.setItem('migrationChecklistPopupNotVisible', 'true');
        $('div.bootstrap-dialog-close-button').click();
    }

    // 상세 가이드 보기 이동
    function goToGuide() {
        window.open('https://migration-help.nhn-commerce.com/enamoo_end', '_blank');
    }

    // 체크리스트 상태 저장
    function saveChecklistState() {
        const checkboxes = document.querySelectorAll('.checklist-item input[type="checkbox"]');
        const checkedStates = Array.from(checkboxes).map(checkbox => checkbox.checked);
        localStorage.setItem(CHECKLIST_KEY, JSON.stringify(checkedStates));
    }

    // 체크리스트 상태 복원
    function restoreChecklistState() {
        const checkboxes = document.querySelectorAll('.checklist-item input[type="checkbox"]');
        const savedStates = JSON.parse(localStorage.getItem(CHECKLIST_KEY) || '[]');
        checkboxes.forEach((checkbox, idx) => {
            checkbox.checked = !!savedStates[idx];
            checkbox.closest('.checklist-item').classList.toggle('active', checkbox.checked);
        });
    }

    // 버튼 이벤트 바인딩
    function bindButtonEvents() {
        document.getElementById('todayNotVisible').addEventListener('click', setTodayNotVisible);
        document.getElementById('popupNotVisible').addEventListener('click', setPopupNotVisible);
        document.querySelector('.modal-dialog__footer .ncua-btn--primary').addEventListener('click', goToGuide);
    }
</script>
