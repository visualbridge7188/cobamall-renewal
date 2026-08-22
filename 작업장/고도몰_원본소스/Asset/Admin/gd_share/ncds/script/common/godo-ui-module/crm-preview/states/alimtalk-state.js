/**
 * Alimtalk State
 * 카카오 알림톡 발송 타입의 미리보기 처리
 * HTML을 동적으로 생성하는 방식
 * 순수 바닐라 자바스크립트
 */
class AlimtalkState {
    constructor(container, data, options = {}) {
        this.container = container;
        this.data = data;
        this.section = null;
    }

    /**
     * UI 렌더링 - ALIMTALK 미리보기 HTML 동적 생성
     */
    render() {
        // 컨테이너 비우기
        this.container.innerHTML = '';
        
        // ALIMTALK 섹션 생성
        this.section = this.createAlimtalkSection();
        
        // 컨테이너에 추가
        this.container.appendChild(this.section);
        
        // 데이터 적용
        this.applyData();
    }

    /**
     * ALIMTALK 섹션 HTML 생성
     * @returns {HTMLElement}
     */
    createAlimtalkSection() {
        const section = document.createElement('section');
        section.setAttribute('data-component', 'kakao-notification-message-preview');
        section.className = 'mobile-preview mobile-preview--alim';
        
        section.innerHTML = `
            <span class="ncua-profile ncua-profile--float ncua-profile--kakao">채널명</span>
            <section class="ncua-prev-alim">
                <h2 class="ncua-prev-alim__title">알림톡 도착</h2>
                <section class="ncua-prev-alim__content">
                    <!-- header -->
                    <header class="ncua-prev-alim__em" hidden>
                        <h3 class="ncua-prev-alim__em-title"></h3>
                        <span class="ncua-prev-alim__em-title-sub"></span>
                    </header>
                    
                    <!-- figure -->
                    <figure class="ncua-prev-alim__media" hidden>
                        <img src="" alt="">
                    </figure>
                    
                    <!-- list -->
                    <section class="ncua-prev-alim__list" hidden>
                        <h3 class="ncua-prev-alim__list-title" hidden></h3>
                        <div class="ncua-prev-alim__highlight" hidden>
                            <div>
                                <p class="ncua-prev-alim__highlight-title"></p>
                                <span class="ncua-prev-alim__highlight-desc"></span>
                            </div>
                            <img src="" alt="">
                        </div>
                        <div class="ncua-prev-alim-table" hidden>
                            <table>
                                <tbody></tbody>
                                <tfoot></tfoot>
                            </table>
                        </div>
                    </section>
                    
                    <!-- text -->
                    <div class="ncua-prev-alim__text" style="white-space: pre-wrap;"></div>
                    
                    <!-- extra info -->
                    <p class="ncua-prev-alim__info ncua-prev-alim__info--extra" hidden></p>
                    <p class="ncua-prev-alim__info ncua-prev-alim__info--channel" hidden></p>
                </section>
                
                <!-- buttons -->
                <footer class="ncua-prev-alim__footer" hidden></footer>
            </section>
        `;
        
        return section;
    }

    /**
     * 저장된 데이터를 UI에 적용
     */
    applyData() {
        if (this.data.content) this.setContent(this.data.content);
        if (this.data.image) this.setImage(this.data.image);
        if (this.data.buttons && this.data.buttons.length > 0) this.setButtons(this.data.buttons);
    }

    /**
     * 제목 설정
     * @param {string} value
     */
    setTitle(value) {
        // 알림톡은 제목 변경 불가 (항상 "알림톡 도착"으로 고정)
        return;
    }

    /**
     * 강조 제목 설정
     * @param {string} value
     */
    setEmTitle(value) {
        if (!this.section) return;
        
        const element = this.section.querySelector('.ncua-prev-alim__em-title-sub');
        if (!element) return;
        
        const header = element.closest('.ncua-prev-alim__em');
        element.textContent = value;
        
        if (header) {
            const mainTitle = this.section.querySelector('.ncua-prev-alim__em-title');
            const hasContent = value || (mainTitle && mainTitle.textContent);
            header.hidden = !hasContent;
        }
    }

    /**
     * 강조 부제목 설정
     * @param {string} value
     */
    setEmSubTitle(value) {
        if (!this.section) return;
        
        const element = this.section.querySelector('.ncua-prev-alim__em-title');
        if (!element) return;
        
        const header = element.closest('.ncua-prev-alim__em');
        element.textContent = value;
        
        if (header) {
            const subTitle = this.section.querySelector('.ncua-prev-alim__em-title-sub');
            const hasContent = value || (subTitle && subTitle.textContent);
            header.hidden = !hasContent;
        }
    }

    /**
     * 내용 설정
     * @param {string} value
     */
    setContent(value) {
        if (!this.section) return;
        
        const element = this.section.querySelector('.ncua-prev-alim__text');
        if (element) {
            element.innerHTML = CrmSanitizer.sanitizeHTML(value);
        }
    }

    /**
     * 이미지 설정
     * @param {string} src
     */
    setImage(src) {
        if (!this.section) return;
        
        const media = this.section.querySelector('.ncua-prev-alim__media');
        if (!media) return;
        
        const img = media.querySelector('img');
        if (img) {
            img.src = src;
        }
        
        // hidden 속성과 display 스타일 함께 처리
        if (src) {
            media.hidden = false;
            media.style.display = '';
        } else {
            media.hidden = true;
            media.style.display = 'none';
        }
    }

    /**
     * 하이라이트 제목 설정
     * @param {string} value
     */
    setHighlightTitle(value) {
        if (!this.section) return;
        
        const element = this.section.querySelector('.ncua-prev-alim__highlight-title');
        if (element) {
            element.textContent = value;
            element.hidden = !value;
        }
        this.updateHighlightVisibility();
    }

    /**
     * 하이라이트 설명 설정
     * @param {string} value
     */
    setHighlightDesc(value) {
        if (!this.section) return;

        const element = this.section.querySelector('.ncua-prev-alim__highlight-desc');
        if (element) {
            element.textContent = value;
            element.hidden = !value;
        }
        this.updateHighlightVisibility();
    }

    /**
     * 하이라이트 이미지 설정
     * @param {string} src
     */
    setHighlightImage(src) {
        if (!this.section) return;
        
        const highlight = this.section.querySelector('.ncua-prev-alim__highlight');
        if (highlight) {
            const img = highlight.querySelector('img');
            if (img) {
                img.src = src;
                // 하이라이트 이미지도 display: none으로 처리
                img.style.display = src ? '' : 'none';
            }
        }
        this.updateHighlightVisibility();
    }
    
    /**
     * 하이라이트 표시 여부 업데이트
     * 제목, 설명, 이미지 중 하나라도 있으면 표시
     */
    updateHighlightVisibility() {
        if (!this.section) return;
        
        const highlight = this.section.querySelector('.ncua-prev-alim__highlight');
        if (!highlight) return;
        
        const title = highlight.querySelector('.ncua-prev-alim__highlight-title');
        const desc = highlight.querySelector('.ncua-prev-alim__highlight-desc');
        const img = highlight.querySelector('img');
        
        const hasTitle = title && !title.hidden;
        const hasDesc = desc && !desc.hidden;
        const hasImage = img && img.src && img.style.display !== 'none';
        
        highlight.hidden = !(hasTitle || hasDesc || hasImage);
        
        // 리스트 섹션 전체 표시 여부도 업데이트
        this.updateListSectionVisibility();
    }

    /**
     * 리스트 헤더 설정 (templateHeader)
     * @param {string} value
     */
    setListHeader(value) {
        if (!this.section) return;
        
        const listSection = this.section.querySelector('.ncua-prev-alim__list');
        if (!listSection) return;
        
        const element = listSection.querySelector('.ncua-prev-alim__list-title');
        if (element) {
            element.textContent = value;
            element.hidden = !value;
        }

        // 리스트 섹션 표시 여부는 개별 영역으로 제어하지 않고
        // 헤더, 하이라이트, 테이블 중 하나라도 있으면 표시되도록 별도 메서드 사용
        this.updateListSectionVisibility();
    }
    
    /**
     * 리스트 섹션 표시 여부 업데이트
     * 헤더, 하이라이트, 아이템 중 하나라도 있으면 표시
     */
    updateListSectionVisibility() {
        if (!this.section) return;
        
        const listSection = this.section.querySelector('.ncua-prev-alim__list');
        if (!listSection) return;
        
        const header = listSection.querySelector('.ncua-prev-alim__list-title');
        const highlight = listSection.querySelector('.ncua-prev-alim__highlight');
        const tableDiv = listSection.querySelector('.ncua-prev-alim-table');
        const hasHeader = header && !header.hidden;
        const hasHighlight = highlight && !highlight.hidden;
        const hasItems = tableDiv && !tableDiv.hidden;
        
        listSection.hidden = !(hasHeader || hasHighlight || hasItems);
    }

    /**
     * 추가 정보 설정 (templateExtra)
     * @param {string} value
     */
    setExtraInfo(value) {
        if (!this.section) return;
        
        const element = this.section.querySelector('.ncua-prev-alim__info--extra');
        if (element) {
            element.textContent = value;
            element.hidden = !value;
        }
    }

    /**
     * 채널 추가 메시지 설정 (channelExtra)
     * @param {string} value
     */
    setChannelMessage(value) {
        if (!this.section) return;
        
        const element = this.section.querySelector('.ncua-prev-alim__info--channel');
        if (element) {
            element.textContent = value;
            element.hidden = !value;
        }
    }

    /**
     * 버튼 목록 설정
     * @param {Array} buttons - 버튼 객체 배열 (각 버튼은 text, type, url 속성을 가질 수 있음)
     *                          type: 'add-channel'인 경우 채널 추가 스타일 적용
     */
    setButtons(buttons) {
        if (!this.section) return;
        
        const footer = this.section.querySelector('.ncua-prev-alim__footer');
        if (!footer) return;
        
        if (!buttons || buttons.length === 0) {
            footer.hidden = true;
            return;
        }

        // 기존 버튼 제거
        footer.innerHTML = '';

        // 새 버튼 추가
        buttons.forEach((button) => {
            const btnClass = button.type === 'add-channel' 
                ? 'ncua-prev-btn ncua-prev-alim__btn--add-channel' 
                : 'ncua-prev-btn';
            const btn = document.createElement('button');
            btn.className = btnClass;
            btn.type = 'button';
            btn.textContent = button.text || '버튼';
            footer.appendChild(btn);
        });

        footer.hidden = false;
    }

    /**
     * 아이템 리스트 설정 (테이블 형태)
     * @param {Array} items
     */
    setItemList(items) {
        if (!this.section) return;

        const tableDiv = this.section.querySelector('.ncua-prev-alim-table');
        if (!tableDiv) return;

        const table = tableDiv.querySelector('table');
        if (!table) return;

        const tbody = table.querySelector('tbody');
        const tfoot = table.querySelector('tfoot');

        if (!tbody) return;

        // 기존 아이템 제거
        tbody.innerHTML = '';
        if (tfoot) {
            tfoot.innerHTML = '';
        }

        // 아이템이 없으면 테이블 숨김
        if (!items || items.length === 0) {
            tableDiv.hidden = true;
            this.updateListSectionVisibility();
            return;
        }

        // 새 아이템 추가
        items.forEach(item => {
            if (item.isSummary) {
                // 요약 행은 tfoot에 추가
                if (tfoot) {
                    tfoot.innerHTML = '';
                    const tr = document.createElement('tr');
                    const th = document.createElement('th');
                    th.textContent = item.title || '요약';
                    const td = document.createElement('td');
                    td.textContent = item.desc || '';
                    tr.appendChild(th);
                    tr.appendChild(td);
                    tfoot.appendChild(tr);
                }
            } else {
                // 일반 행은 tbody에 추가
                const tr = document.createElement('tr');
                const th = document.createElement('th');
                th.textContent = item.title || '';
                const td = document.createElement('td');
                td.textContent = item.desc || '';
                tr.appendChild(th);
                tr.appendChild(td);
                tbody.appendChild(tr);
            }
        });

        tableDiv.hidden = false;

        // 리스트 섹션 표시 여부 업데이트
        this.updateListSectionVisibility();
    }

    /**
     * 리셋 (필드 초기화)
     */
    reset() {
        this.setContent('');
        this.setImage('');
        this.setButtons([]);
        this.setEmTitle('');
        this.setEmSubTitle('');
        this.setListHeader('');
        this.setHighlightTitle('');
        this.setHighlightDesc('');
        this.setHighlightImage('');
        this.setExtraInfo('');
        this.setChannelMessage('');
        this.setItemList([]);
    }

    /**
     * State 정리
     * Alimtalk State는 정리할 리소스가 없으므로 빈 구현
     */
    destroy() {
        // No cleanup needed
    }
}

// Window 전역 객체로 노출
window.AlimtalkState = AlimtalkState;
