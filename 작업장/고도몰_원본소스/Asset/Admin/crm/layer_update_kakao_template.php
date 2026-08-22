<div class="modal-dialog__content layer_update_kakao_template">
    <div class="ncua-table ncua-table--vertical">
        <table>
            <tbody>
                <tr>
                    <th class="ncua-required"><div>구분</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="kakaoType" />
                                </span>
                                <span class="ncua-radio-field__text">주문/배송</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="kakaoType" />
                                </span>
                                <span class="ncua-radio-field__text">정기결제(배송)</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="kakaoType" />
                                </span>
                                <span class="ncua-radio-field__text">선물하기</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="kakaoType" />
                                </span>
                                <span class="ncua-radio-field__text">회원</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="kakaoType" />
                                </span>
                                <span class="ncua-radio-field__text">게시판</span>
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="ncua-required"><div>템플릿 카테고리</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <div class="ncua-select ncua-select--xs">
                                <div class="ncua-select__content">
                                    <select class="ncua-select__tag">
                                        <option value="">대분류</option>
                                        <option value="1">Option 1</option>
                                        <option value="2">Option 2</option>
                                        <option value="3">Option 3</option>
                                    </select>
                                </div>
                            </div>
                            <div class="ncua-select ncua-select--xs">
                                <div class="ncua-select__content">
                                    <select class="ncua-select__tag" disabled>
                                        <option value="">중분류</option>
                                        <option value="1">Option 1</option>
                                        <option value="2">Option 2</option>
                                        <option value="3">Option 3</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="ncua-required"><div>템플릿 제목</div></th>
                    <td>
                        <div>
                            <div class="ncua-input ncua-input--xs ">
                                <div class="ncua-input__content-wrap">
                                    <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs ncua-input-width-520">
                                        <input maxlength="10" type="text" placeholder="제목을 입력하세요" class="kakao-template-title" data-charcount-key="kakaoTemplateTitle" /></div>
                                    </div>
                                    <div class="ncua-input__field-text-count" data-charcount-text="kakaoTemplateTitle">
                                        <output class="ncua-input__field-text-count-current">0</output>
                                        <span>/10</span>
                                    </div>
                                </div>
                                <!-- NOTE [저장] 유효성 검사 : 내용을 입력하지 않은 경우 --> 
                                <span class="ncua-hint-text destructive ncua-input__hint-text">템플릿 제목을 입력해 주세요.</span>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><div>보안 템플릿 설정</div></th>
                    <td>
                        <div>
                            <div class="ncua-switch ncua-switch--xs">
                                <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                    <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" checked="" name="demo-switch" />
                                    <span class="ncua-switch__label">사용함</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                    <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" name="demo-switch" />
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="content-border-box">
        <div class="template_type">
            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                    <input type="radio" name="templateType" />
                </span>
                <span class="ncua-radio-field__text">메시지형</span>
            </label>
            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                    <input type="radio" name="templateType" />
                </span>
                <span class="ncua-radio-field__text">강조표기형</span>
            </label>
            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                    <input type="radio" name="templateType" />
                </span>
                <span class="ncua-radio-field__text">이미지형</span>
            </label>
            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                    <input type="radio" name="templateType" />
                </span>
                <span class="ncua-radio-field__text">아이템리스트형</span>
            </label>
        </div>

        <div class="template_content ncua-card__body-block-section-wrap">
            <section class="ncua-card__body-block-section">
                <div class="ncua-card__body-title--xs">본문 입력</div>
                <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs">
                    <textarea class="ncua-input__textarea kakao-template-textarea-content" placeholder="템플릿 본문을 작성해 주세요." maxlength="1000"></textarea>
                    <div class="ncua-input__text-count-wrap">
                        <div class="ncua-hint-text">본문을 입력해 주세요.</div>
                        <div class="ncua-input__text-count">
                            <span class="ncua-input__text-count-text">
                                <span class="ncua-input__text-count-text-count">0</span>
                                /1000
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="ncua-card__body-block-section">
                <div class="ncua-card__body-title--xs" data-tooltip-seq="008">사용 가능한 변수</div>
                <div class="chip-selector">
                    <div class="ncua-select ncua-select--xs">
                        <div class="ncua-select__content">
                            <select class=" ncua-select__tag">
                                <option value="1">회원</option>
                                <option value="2">게시판</option>
                                <option value="3">통계</option>
                            </select>
                        </div>
                    </div>
                    <div class="chip-button-wrap">
                        <button class="chip-button"><span class="chip-button-text">{rc_mallNm} 쇼핑몰 명/상점명</span></button>
                        <button class="chip-button"><span class="chip-button-text">{memId} 회원 아이디</span></button>
                        <button class="chip-button"><span class="chip-button-text">{memNm} 회원명</span></button>
                        <button class="chip-button"><span class="chip-button-text">{sleepScheduleDt} 휴면회원 전환 예정 일</span></button>
                        <button class="chip-button"><span class="chip-button-text">{rc_scheduleDt} 일반 회원 전환 일</span></button> 
                        <button class="chip-button"><span class="chip-button-text">{smsAgreementDt} SMS 수신동의일</span></button>
                        <button class="chip-button"><span class="chip-button-text">{mailAgreementDt}메일 수신동의일</span></button>
                        <button class="chip-button"><span class="chip-button-text">{mileage} 보유한 마일리지</span></button>
                        <button class="chip-button"><span class="chip-button-text">{deposit}보유한 예치금</span></button>
                        <button class="chip-button"><span class="chip-button-text">{groupNm} 회원등급</span></button>
                        <button class="chip-button"><span class="chip-button-text">{rc_mileage} 보유한 마일리지</span></button>
                        <button class="chip-button"><span class="chip-button-text">{rc_deposit}보유한 예치금</span></button>
                        <button class="chip-button"><span class="chip-button-text">{rc_certificationCode} 인증코드</span></button>
                        <button class="chip-button"><span class="chip-button-text">{wriNm} 작성자</span></button>
                        <button class="chip-button"><span class="chip-button-text">{orderNo} 주문번호</span></button>
                        <button class="chip-button"><span class="chip-button-text">{orderName} 주문명</span></button>
                        <button class="chip-button"><span class="chip-button-text">{settlePrice} 결제금액</span></button>
                        <button class="chip-button"><span class="chip-button-text">{bankAccount} 입금계좌번호</span></button>
                    </div>
                </div>
            </section>

            <section class="ncua-card__body-block-section">
                <div class="ncua-card__body-title-wrap has-checkbox">
                    <span class="ncua-card__body-title--xs">부가 정보 문구</span>
                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                            <input type="checkbox" />
                        </span>
                        <span class="ncua-checkbox-field__text">추가</span>
                    </label>
                </div>
                <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs">
                    <textarea class="ncua-input__textarea kakao-template-textarea-add-info" placeholder="템플릿 본문을 작성해 주세요." maxlength="200"></textarea>
                    <div class="ncua-input__text-count-wrap">
                        <!-- NOTE [저장] 유효성 검사 : 내용을 입력하지 않은 경우 -->
                        <div class="ncua-hint-text destructive">본문을 입력해 주세요.</div>
                        <div class="ncua-input__text-count">
                            <span class="ncua-input__text-count-text">
                                <span class="ncua-input__text-count-text-count">0</span>
                                /200
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="ncua-card__body-block-section channel_add_message">
                <div class="ncua-card__body-title-wrap has-checkbox">
                    <span class="ncua-card__body-title--xs">채널 추가 문구(고정 내용)</span>
                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                            <input type="checkbox" />
                        </span>
                        <span class="ncua-checkbox-field__text">추가</span>
                    </label>
                </div>
                <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs">
                    <textarea class="ncua-input__textarea" maxlength="36" readonly>채널 추가하고 이 채널의 광고와 마케팅을 카카오톡으로 받기</textarea>
                    <div class="ncua-input__text-count-wrap">
                        <div class="ncua-hint-text"></div>
                        <div class="ncua-input__text-count">
                            <span class="ncua-input__text-count-text">
                                <span class="ncua-input__text-count-text-count">0</span>
                                /36
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="ncua-card__body-block-section">
                <div class="ncua-card__body-title-wrap">
                    <span class="ncua-card__body-title--xs">버튼</span>
                    <div>
                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray auto-send-icon auto-send-icon--plus">
                            <span class="ncua-btn__label">페이지 링크 추가</span>
                        </button>
                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray auto-send-icon auto-send-icon--plus">
                            <span class="ncua-btn__label">배송 조회 추가</span>
                        </button>
                    </div>
                </div>
                <div class="ncua-table ncua-table--horizontal">
                    <table>
                        <colgroup> 
                            <col width="79px">
                            <col width="auto">
                            <col width="auto">
                            <col width="88px">
                        </colgroup>
                        <thead>
                            <tr>
                                <th><div class="ncua-align-center">타입</div></th>
                                <th class="ncua-required"><div>버튼명</div></th>
                                <th class="ncua-required"><div>URL</div></th>
                                <th><div class="ncua-align-center">관리</div></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="ncua-align-center">페이지 링크</div>
                                </td>
                                <td>
                                    <div>
                                        <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count">
                                            <div class="ncua-input__content-wrap">
                                                <div class="ncua-input__content">
                                                    <div class="ncua-input__field ncua-input__field--xs">
                                                        <input maxlength="20" type="text" placeholder="버튼명을 입력해주세요" class="kakao-button-page-link" data-charcount-key="kakaoButtonPageLink" />
                                                    </div>
                                                </div>
                                                <div class="ncua-input__field-text-count" data-charcount-text="kakaoButtonPageLink">
                                                    <output class="ncua-input__field-text-count-current">0</output>
                                                    <span>/20</span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- NOTE [저장] 유효성 검사 : 내용을 입력하지 않은 경우 -->
                                        <span class="ncua-hint-text destructive ncua-input__hint-text">버튼명을 입력해 주세요.</span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="ncua-input ncua-input--xs">
                                            <div class="ncua-input__content-wrap">
                                                <div class="ncua-input__content">
                                                    <div class="ncua-input__field ncua-input__field--xs">
                                                        <input type="text" placeholder="버튼명을 입력해주세요" />
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="ncua-hint-text ncua-input__hint-text">외부 링크 입력 시 캠페인 통계가 집계되지 않습니다.</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="ncua-align-center">
                                    <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray auto-send-icon auto-send-icon--minus">
                                        <span class="ncua-btn__label">삭제</span>
                                    </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="ncua-table ncua-table--horizontal">
                    <table>
                        <colgroup> 
                            <col width="79px">
                            <col width="auto">
                            <col width="auto">
                            <col width="88px">
                        </colgroup>
                        <thead>
                            <tr>
                                <th><div class="ncua-align-center">타입</div></th>
                                <th class="ncua-required"><div>버튼명</div></th>
                                <th class="ncua-required"><div>URL</div></th>
                                <th><div class="ncua-align-center">관리</div></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="ncua-align-center">배송 조회</div>
                                </td>
                                <td>
                                    <div>
                                        <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count">
                                            <div class="ncua-input__content-wrap">
                                                <div class="ncua-input__content">
                                                    <div class="ncua-input__field ncua-input__field--xs">
                                                        <input maxlength="20" type="text" placeholder="버튼명을 입력해주세요" class="kakao-button-delivery-search" data-charcount-key="kakaoButtonDeliverySearch" />
                                                    </div>
                                                </div>
                                                <div class="ncua-input__field-text-count" data-charcount-text="kakaoButtonDeliverySearch">
                                                    <output class="ncua-input__field-text-count-current">0</output>
                                                    <span>/20</span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- NOTE [저장] 유효성 검사 : 내용을 입력하지 않은 경우 -->
                                        <span class="ncua-hint-text destructive ncua-input__hint-text">버튼명을 입력해 주세요.</span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <p>본문에 택배사명과 송장번호가 포함되면 버튼이 카카오 배송조회 페이지로 연결됩니다. 단, 미지원 택배사는 조회되지 않습니다.</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="ncua-align-center">
                                        <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray minus-icon">
                                            <span class="ncua-btn__label">삭제</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
<div class="modal-dialog__footer">
    <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray js-layer-close">
        <span class="ncua-btn__label">취소</span>
    </button>
    <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--primary js-layer-close">
        <span class="ncua-btn__label">저장</span>
    </button>
</div>
