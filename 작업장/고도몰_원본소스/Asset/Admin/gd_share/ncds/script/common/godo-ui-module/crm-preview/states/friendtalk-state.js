/**
 * Friendtalk State
 * 카카오 친구톡 발송 타입의 미리보기 처리
 * 5가지 메시지 타입 지원: TEXT, IMAGE, WIDE_IMAGE, WIDE_ITEM_LIST, CAROUSEL_FEED
 * HTML을 동적으로 생성하는 방식
 * 순수 바닐라 자바스크립트
 */
class FriendtalkState {
    constructor(container, data, options = {}) {
        this.container = container;
        this.data = data;
        this.section = null;
        
        this.messageType = CRM_FRIENDTALK_MESSAGE_TYPE.TEXT;
        this.slickInstance = null;
        this.slides = []; // 캐러셀용 슬라이드 데이터
        this.currentSlideIndex = 0;
    }

    /**
     * 메시지 타입 설정
     * @param {string} type - 'TEXT' | 'IMAGE' | 'WIDE_IMAGE' | 'WIDE_ITEM_LIST' | 'CAROUSEL_FEED'
     */
    setMessageType(type) {
        if (!CRM_FRIENDTALK_MESSAGE_TYPE[type]) {
            console.error(`FriendtalkState: 잘못된 메시지 타입입니다. (${type})`);
            return;
        }

        const previousType = this.messageType;
        this.messageType = type;

        // 타입이 변경되면 다시 렌더링
        if (previousType !== type) {
            this.render();
        }
    }

    /**
     * UI 렌더링 - FRIENDTALK 미리보기 HTML 동적 생성
     */
    render() {
        // 기존 slick 정리
        this.destroySlick();

        // 컨테이너 비우기
        this.container.innerHTML = '';

        // 캐러셀 타입 변경 시 슬라이드 초기화 (renderMessageInput에서 다시 추가)
        if (this.messageType === CRM_FRIENDTALK_MESSAGE_TYPE.CAROUSEL_FEED) {
            this.slides = [];
        }

        // FRIENDTALK 섹션 생성
        this.section = this.createFriendtalkSection();

        // 컨테이너에 추가
        this.container.appendChild(this.section);

        // 캐러셀인 경우 슬라이드가 있을 때만 slick 적용
        if (this.messageType === CRM_FRIENDTALK_MESSAGE_TYPE.CAROUSEL_FEED && this.slides.length > 0) {
            this.initSlick();
        }

        // 데이터 적용
        this.applyData();
    }

    /**
     * FRIENDTALK 섹션 HTML 생성
     * @returns {HTMLElement}
     */
    createFriendtalkSection() {
        const section = document.createElement('section');
        section.setAttribute('data-component', 'kakao-message-preview');
        section.className = 'mobile-preview mobile-preview--kakao';
        
        const profile = document.createElement('span');
        profile.className = 'ncua-profile ncua-profile--float ncua-profile--kakao';
        profile.textContent = '(광고) 채널명';
        section.appendChild(profile);
        
        // 캐러셀인 경우 슬라이더로 감싸기
        if (this.messageType === CRM_FRIENDTALK_MESSAGE_TYPE.CAROUSEL_FEED) {
            const slider = document.createElement('div');
            slider.className = 'mobile-preview__slider js-mobile-preview';
            
            // 슬라이드 생성
            this.slides.forEach((slideData, index) => {
                const preview = this.createPreviewElement(slideData);
                slider.appendChild(preview);
            });
            
            section.appendChild(slider);
        } else {
            // 일반 타입
            const preview = this.createPreviewElement();
            section.appendChild(preview);
        }
        
        return section;
    }

    /**
     * 미리보기 요소 생성
     * @param {Object} slideData - 슬라이드 데이터 (선택)
     * @returns {HTMLElement}
     */
    createPreviewElement(slideData = null) {
        const preview = document.createElement('section');
        
        // WIDE 타입일 때만 ncua-prev--lg 클래스 추가
        const isWideType = [
            CRM_FRIENDTALK_MESSAGE_TYPE.WIDE_IMAGE,
            CRM_FRIENDTALK_MESSAGE_TYPE.WIDE_ITEM_LIST,
            CRM_FRIENDTALK_MESSAGE_TYPE.CAROUSEL_FEED
        ].includes(this.messageType);
        
        preview.className = isWideType ? 'ncua-prev ncua-prev--lg' : 'ncua-prev';
        
        // 제목
        const title = document.createElement('h2');
        title.className = 'ncua-prev__title';
        
        // TEXT 타입이거나 title이 없으면 숨김
        if (this.messageType === CRM_FRIENDTALK_MESSAGE_TYPE.TEXT) {
            title.hidden = true;
        } else {
            title.textContent = (slideData && slideData.title) || '';
            title.hidden = !(slideData && slideData.title);
        }
        preview.appendChild(title);
        
        // 이미지
        const media = document.createElement('figure');
        media.className = 'ncua-prev__media';
        const img = document.createElement('img');
        img.src = (slideData && slideData.image) || '';
        img.alt = '';
        media.appendChild(img);
        
        // TEXT 타입이거나 이미지가 없으면 숨김
        media.hidden = this.messageType === CRM_FRIENDTALK_MESSAGE_TYPE.TEXT || !(slideData && slideData.image);
        preview.appendChild(media);
        
        // 아이템 리스트 (WIDE_ITEM_LIST 전용)
        if (this.messageType === CRM_FRIENDTALK_MESSAGE_TYPE.WIDE_ITEM_LIST) {
            const itemContainer = document.createElement('div');
            itemContainer.className = 'ncua-prev__prd-list';
            itemContainer.hidden = true;
            preview.appendChild(itemContainer);
        }
        
        // 텍스트 제목 (CAROUSEL_FEED 전용)
        if (this.messageType === CRM_FRIENDTALK_MESSAGE_TYPE.CAROUSEL_FEED) {
            const textTitle = document.createElement('p');
            textTitle.className = 'ncua-prev__text-title';
            textTitle.textContent = (slideData && slideData.contentTitle) || '';
            textTitle.hidden = !(slideData && slideData.contentTitle);
            preview.appendChild(textTitle);
        }
        
        // 텍스트
        const text = document.createElement('p');
        text.className = 'ncua-prev__text friendtalk-contents-side-preview';
        text.style.whiteSpace = 'pre-wrap';
        if (slideData && slideData.content) {
            text.innerHTML = CrmSanitizer.sanitizeHTML(slideData.content);
        }
        preview.appendChild(text);
        
        // 버튼
        const buttons = document.createElement('div');
        // WIDE 타입일 때 horizontal 클래스 추가
        buttons.className = isWideType ? 'ncua-prev__btns ncua-prev__btns--horizontal' : 'ncua-prev__btns';
        buttons.hidden = true;
        if (slideData && slideData.buttons && slideData.buttons.length > 0) {
            slideData.buttons.forEach(btn => {
                buttons.appendChild(this.createButtonElement(btn));
            });
            buttons.hidden = false;
        }
        preview.appendChild(buttons);
        
        // 쿠폰
        const coupon = document.createElement('button');
        coupon.type = 'button';
        coupon.className = 'ncua-prev__coupon';
        coupon.hidden = true;
        
        const couponText = document.createElement('span');
        couponText.className = 'ncua-prev__coupon-text';
        
        const couponTitle = document.createElement('span');
        couponTitle.className = 'ncua-prev__coupon-title';
        couponText.appendChild(couponTitle);
        
        const couponDesc = document.createElement('time');
        couponDesc.className = 'ncua-prev__coupon-desc';
        couponText.appendChild(couponDesc);
        
        coupon.appendChild(couponText);
        
        const couponIcon = document.createElement('span');
        couponIcon.className = 'ncua-prev__coupon-icon';
        coupon.appendChild(couponIcon);
        
        if (slideData && slideData.coupon) {
            couponTitle.textContent = slideData.coupon.text || '쿠폰';
            couponDesc.textContent = slideData.coupon.date ? slideData.coupon.date : '';
            coupon.hidden = false;
        }
        
        preview.appendChild(coupon);
        
        return preview;
    }

    /**
     * 저장된 데이터를 UI에 적용
     */
    applyData() {
        if (this.messageType === CRM_FRIENDTALK_MESSAGE_TYPE.CAROUSEL_FEED) {
            // 캐러셀: 각 슬라이드 데이터 적용
            this.applySlideData();
        } else {
            // 일반: 전역 데이터 적용
            if (this.data.title) this.setTitle(this.data.title);
            if (this.data.content) this.setContent(this.data.content);
            if (this.data.image) this.setImage(this.data.image);
            if (this.data.buttons && this.data.buttons.length > 0) this.setButtons(this.data.buttons);
            if (this.data.coupon) this.setCoupon(this.data.coupon);
        }
    }

    /**
     * 슬라이드 데이터 적용
     */
    applySlideData() {
        if (!this.section || !this.slickInstance) return;
        
        const currentSlide = this.getCurrentSlide();
        if (!currentSlide) return;
        
        const slideData = this.slides[this.currentSlideIndex];
        if (!slideData) return;
        
        // 현재 슬라이드에 데이터 적용
        const title = currentSlide.querySelector('.ncua-prev__title');
        if (title) {
            title.textContent = slideData.title || '';
            title.hidden = !slideData.title;
        }
        
        const img = currentSlide.querySelector('.ncua-prev__media img');
        if (img) {
            img.src = slideData.image || '';
        }
        
        const contentTitle = currentSlide.querySelector('.ncua-prev__text-title');
        if (contentTitle) {
            contentTitle.textContent = slideData.contentTitle || '';
            contentTitle.hidden = !slideData.contentTitle;
        }
        
        const content = currentSlide.querySelector('.ncua-prev__text');
        if (content) {
            content.innerHTML = CrmSanitizer.sanitizeHTML(slideData.content || '');
        }
        
        const buttonsContainer = currentSlide.querySelector('.ncua-prev__btns');
        if (buttonsContainer && slideData.buttons && slideData.buttons.length > 0) {
            buttonsContainer.innerHTML = '';
            slideData.buttons.forEach(btn => {
                buttonsContainer.appendChild(this.createButtonElement(btn));
            });
            buttonsContainer.hidden = false;
        }
        
        const couponBtn = currentSlide.querySelector('.ncua-prev__coupon');
        if (couponBtn && slideData.coupon) {
            this.updateCouponContent(couponBtn, slideData.coupon);
        }
    }

    /**
     * 제목 설정
     * @param {string} value
     */
    setTitle(value) {
        const element = this.getTargetElement('.ncua-prev__title');
        if (element) {
            element.textContent = value;
            element.hidden = !value;
            
            // 캐러셀인 경우 슬라이드 데이터에도 저장
            if (this.messageType === CRM_FRIENDTALK_MESSAGE_TYPE.CAROUSEL_FEED && this.slides[this.currentSlideIndex]) {
                this.slides[this.currentSlideIndex].title = value;
            }
        }
    }

    /**
     * 내용 설정
     * @param {string} value
     */
    setContent(value) {
        const element = this.getTargetElement('.ncua-prev__text');
        if (element) {
            element.innerHTML = CrmSanitizer.sanitizeHTML(value);
            
            // 캐러셀인 경우 슬라이드 데이터에도 저장
            if (this.messageType === CRM_FRIENDTALK_MESSAGE_TYPE.CAROUSEL_FEED && this.slides[this.currentSlideIndex]) {
                this.slides[this.currentSlideIndex].content = value;
            }
        }
    }

    /**
     * 내용 제목 설정
     * @param {string} value
     */
    setContentTitle(value) {
        const element = this.getTargetElement('.ncua-prev__text-title');
        if (element) {
            element.textContent = value;
            element.hidden = !value;
            
            // 캐러셀인 경우 슬라이드 데이터에도 저장
            if (this.messageType === CRM_FRIENDTALK_MESSAGE_TYPE.CAROUSEL_FEED && this.slides[this.currentSlideIndex]) {
                this.slides[this.currentSlideIndex].contentTitle = value;
            }
        }
    }

    /**
     * 이미지 설정
     * @param {string} src
     */
    setImage(src) {
        const element = this.getTargetElement('.ncua-prev__media img');
        if (element) {
            element.src = src;
            
            // 이미지가 있으면 미디어 영역 표시
            const media = element.closest('.ncua-prev__media');
            if (media) {
                media.hidden = !src;
            }
            
            // 캐러셀인 경우 슬라이드 데이터에도 저장
            if (this.messageType === CRM_FRIENDTALK_MESSAGE_TYPE.CAROUSEL_FEED && this.slides[this.currentSlideIndex]) {
                this.slides[this.currentSlideIndex].image = src;
            }
        }
    }

    /**
     * 버튼 목록 설정
     * @param {Array} buttons
     */
    setButtons(buttons) {
        const buttonContainer = this.getTargetElement('.ncua-prev__btns');
        if (!buttonContainer) return;
        
        if (!buttons || buttons.length === 0) {
            buttonContainer.hidden = true;
            return;
        }

        // 기존 버튼 제거
        buttonContainer.innerHTML = '';

        // 새 버튼 추가 (헬퍼 메서드 사용)
        buttons.forEach(button => {
            buttonContainer.appendChild(this.createButtonElement(button));
        });

        buttonContainer.hidden = false;
        
        // 캐러셀인 경우 슬라이드 데이터에도 저장
        if (this.messageType === CRM_FRIENDTALK_MESSAGE_TYPE.CAROUSEL_FEED && this.slides[this.currentSlideIndex]) {
            this.slides[this.currentSlideIndex].buttons = buttons;
        }
    }

    /**
     * 쿠폰 설정
     * @param {Object|null} coupon
     */
    setCoupon(coupon) {
        const couponBtn = this.getTargetElement('.ncua-prev__coupon');
        if (!couponBtn) return;
        
        if (!coupon) {
            couponBtn.hidden = true;
            return;
        }

        // 헬퍼 메서드로 쿠폰 내용 업데이트
        this.updateCouponContent(couponBtn, coupon);
        
        // 캐러셀인 경우 슬라이드 데이터에도 저장
        if (this.messageType === CRM_FRIENDTALK_MESSAGE_TYPE.CAROUSEL_FEED && this.slides[this.currentSlideIndex]) {
            this.slides[this.currentSlideIndex].coupon = coupon;
        }
    }

    /**
     * 아이템 리스트 설정
     * @param {Array} items
     */
    setItemList(items) {
        // WIDE_ITEM_LIST 타입에서만 사용
        if (this.messageType !== CRM_FRIENDTALK_MESSAGE_TYPE.WIDE_ITEM_LIST) {
            return;
        }

        const itemContainer = this.getTargetElement('.ncua-prev__prd-list');
        if (!itemContainer) return;

        const media = this.getTargetElement('.ncua-prev__media');
        const mediaImg = this.getTargetElement('.ncua-prev__media img');

        // 기존 media-text 요소 제거
        if (media) {
            const existingMediaText = media.querySelector('.ncua-prev__media-text');
            if (existingMediaText) {
                existingMediaText.remove();
            }
        }

        if (!items || items.length === 0) {
            itemContainer.hidden = true;
            if (media) media.hidden = true;
            return;
        }

        // 첫번째 아이템: ncua-prev__media 영역에 표시
        const firstItem = items[0];
        const hasFirstImage = !!firstItem.image;
        if (mediaImg) {
            mediaImg.src = firstItem.image || '';
            // 이미지가 없으면 빈/깨진 이미지가 노출되지 않도록 img 만 숨긴다.
            mediaImg.hidden = !hasFirstImage;
        }
        if (media) {
            // 이미지가 없어도 타이틀이 있으면 영역을 표시한다(2~4번 아이템과 동일하게 타이틀이 보이도록).
            media.hidden = !(hasFirstImage || firstItem.title);
            // 이미지가 없을 때는 흰색 오버레이 대신 일반 텍스트로 렌더하도록 modifier 부여.
            media.classList.toggle('ncua-prev__media--no-image', !hasFirstImage);

            if (firstItem.title) {
                const mediaText = document.createElement('p');
                mediaText.className = 'ncua-prev__media-text';
                mediaText.textContent = firstItem.title;
                media.appendChild(mediaText);
            }
        }

        // 두번째 아이템부터: ncua-prev__prd-list 에 표시
        itemContainer.innerHTML = '';
        const remainingItems = items.slice(1);

        if (remainingItems.length === 0) {
            itemContainer.hidden = true;
        } else {
            remainingItems.forEach(item => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'ncua-prev__prd';

                const picture = document.createElement('picture');
                picture.className = 'ncua-prev__prd-media';
                const img = document.createElement('img');
                img.src = item.image || '';
                img.alt = '';
                picture.appendChild(img);
                itemDiv.appendChild(picture);

                const textDiv = document.createElement('div');
                textDiv.className = 'ncua-prev__prd-text';

                const titleP = document.createElement('p');
                titleP.textContent = item.title || '';
                textDiv.appendChild(titleP);

                if (item.desc) {
                    const descP = document.createElement('p');
                    descP.textContent = item.desc;
                    textDiv.appendChild(descP);
                }

                itemDiv.appendChild(textDiv);
                itemContainer.appendChild(itemDiv);
            });
            itemContainer.hidden = false;
        }
    }

    /**
     * Slick 초기화 (캐러셀 타입)
     */
    initSlick() {
        this.destroySlick(); // 기존 인스턴스 제거
        
        if (!this.section) return;
        
        const slider = this.section.querySelector('.js-mobile-preview');
        
        if (!slider) {
            console.error('FriendtalkState: 슬라이더 요소를 찾을 수 없습니다.');
            return;
        }

        // 슬라이더에 최소 1개 이상의 슬라이드 필요
        const slides = slider.querySelectorAll('.ncua-prev');
        if (slides.length === 0) {
            console.warn('FriendtalkState: 슬라이드가 없습니다.');
            return;
        }

        // Slick이 있는지 확인 (jQuery 기반)
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.slick === 'undefined') {
            console.error('FriendtalkState: Slick 라이브러리가 필요합니다.');
            return;
        }

        try {
            // jQuery로 Slick 초기화
            const $slider = jQuery(slider);
            $slider.slick({
                dots: true,
                arrows: false,
                infinite: false,
                slidesToShow: 1,
                slidesToScroll: 1,
                adaptiveHeight: false,
                centerMode: false,
                variableWidth: true,
            });
            
            this.slickInstance = slider;
            
            // 슬라이드 변경 이벤트 리스너
            $slider.on('afterChange', (event, slick, currentSlide) => {
                this.currentSlideIndex = currentSlide;
                this.container.dispatchEvent(new CustomEvent('previewSlideChange', {
                    bubbles: true,
                    detail: { index: currentSlide }
                }));
            });
        } catch (error) {
            console.error('FriendtalkState: Slick 초기화 실패', error);
        }
    }

    /**
     * Slick 해제
     */
    destroySlick() {
        if (this.slickInstance && typeof jQuery !== 'undefined') {
            try {
                const $slider = jQuery(this.slickInstance);
                if ($slider.hasClass('slick-initialized')) {
                    $slider.off('afterChange');
                    $slider.slick('unslick');
                }
            } catch (error) {
                console.error('FriendtalkState: Slick 해제 실패', error);
            }
        }
        this.slickInstance = null;
    }

    /**
     * 슬라이드 추가
     */
    addSlide() {
        if (this.messageType !== CRM_FRIENDTALK_MESSAGE_TYPE.CAROUSEL_FEED) {
            return;
        }

        const newSlideData = {
            title: '',
            contentTitle: '',
            content: '',
            image: '',
            buttons: [],
            coupon: null
        };
        
        const newSlide = this.createPreviewElement(newSlideData);
        
        if (this.slickInstance && typeof jQuery !== 'undefined') {
            const $slider = jQuery(this.slickInstance);
            if ($slider.hasClass('slick-initialized')) {
                $slider.slick('slickAdd', newSlide);
            }
        } else {
            const slider = this.section.querySelector('.js-mobile-preview');
            if (slider) {
                slider.appendChild(newSlide);
            }
        }

        this.slides.push(newSlideData);

        // slick 미초기화 상태에서 슬라이드가 추가되면 lazy init
        if (this.messageType === CRM_FRIENDTALK_MESSAGE_TYPE.CAROUSEL_FEED && !this.slickInstance) {
            this.initSlick();
        }
    }

    /**
     * 슬라이드 삭제
     * @param {number} index
     */
    removeSlide(index) {
        if (this.messageType !== CRM_FRIENDTALK_MESSAGE_TYPE.CAROUSEL_FEED) {
            return;
        }

        if (this.slickInstance && typeof jQuery !== 'undefined') {
            const $slider = jQuery(this.slickInstance);
            if ($slider.hasClass('slick-initialized')) {
                $slider.slick('slickRemove', index);
            }
        }

        this.slides.splice(index, 1);
        
        // 현재 인덱스 조정
        if (this.currentSlideIndex >= this.slides.length) {
            this.currentSlideIndex = Math.max(0, this.slides.length - 1);
        }
    }

    /**
     * 슬라이드 인덱스 설정
     * @param {number} index
     */
    setSlideIndex(index) {
        if (this.messageType !== CRM_FRIENDTALK_MESSAGE_TYPE.CAROUSEL_FEED) {
            return;
        }

        this.currentSlideIndex = index;
        
        if (this.slickInstance && typeof jQuery !== 'undefined') {
            const $slider = jQuery(this.slickInstance);
            if ($slider.hasClass('slick-initialized')) {
                $slider.slick('slickGoTo', index);
            }
        }
    }

    /**
     * 현재 슬라이드 요소 반환
     * @returns {HTMLElement|null}
     */
    getCurrentSlide() {
        if (!this.slickInstance) return null;
        
        const slides = this.slickInstance.querySelectorAll('.slick-slide:not(.slick-cloned)');
        return slides[this.currentSlideIndex] || null;
    }

    /**
     * 현재 슬라이드 인덱스 반환
     * @returns {number}
     */
    getCurrentSlideIndex() {
        return this.currentSlideIndex;
    }

    /**
     * 슬라이드 개수 반환
     * @returns {number}
     */
    getSlideCount() {
        if (this.messageType !== CRM_FRIENDTALK_MESSAGE_TYPE.CAROUSEL_FEED) {
            return 0;
        }
        return this.slides.length;
    }

    /**
     * 현재 메시지 타입 반환
     * @returns {string}
     */
    getMessageType() {
        return this.messageType;
    }

    /**
     * 대상 요소 반환 (캐러셀인 경우 현재 슬라이드 기준)
     * @param {string} selector
     * @returns {HTMLElement|null}
     */
    getTargetElement(selector) {
        if (!this.section) return null;
        
        if (this.messageType === CRM_FRIENDTALK_MESSAGE_TYPE.CAROUSEL_FEED) {
            const currentSlide = this.getCurrentSlide();
            if (currentSlide) {
                return currentSlide.querySelector(selector);
            }
        }
        
        return this.section.querySelector(selector);
    }

    /**
     * 버튼 요소 생성 (헬퍼 메서드)
     * @param {Object} buttonData - {text, url, type}
     * @returns {HTMLElement}
     */
    createButtonElement(buttonData) {
        const button = document.createElement('button');
        button.className = 'ncua-prev__btn';
        button.type = 'button';
        button.textContent = buttonData.text || '버튼';

        return button;
    }

    /**
     * 쿠폰 내용 업데이트 (헬퍼 메서드)
     * @param {HTMLElement} couponElement - 쿠폰 버튼 요소
     * @param {Object} couponData - {text, date}
     */
    updateCouponContent(couponElement, couponData) {
        if (!couponElement) return;
        
        const couponTitle = couponElement.querySelector('.ncua-prev__coupon-title');
        const couponDesc = couponElement.querySelector('.ncua-prev__coupon-desc');
        
        if (couponTitle) {
            couponTitle.textContent = couponData.text || '쿠폰';
        }
        
        if (couponDesc) {
            couponDesc.textContent = couponData.date ? couponData.date : '';
        }
        
        couponElement.hidden = false;
    }

    /**
     * 리셋 (필드 초기화)
     */
    reset() {
        this.setTitle('');
        this.setContentTitle('');
        this.setContent('');
        this.setImage('');
        this.setButtons([]);
        this.setCoupon(null);
        this.setItemList([]);
    }

    /**
     * State 정리
     * Slick 슬라이더 및 관련 리소스를 정리
     */
    destroy() {
        this.destroySlick();
        this.slides = [];
        this.currentSlideIndex = 0;
    }
}

// Window 전역 객체로 노출
window.FriendtalkState = FriendtalkState;
