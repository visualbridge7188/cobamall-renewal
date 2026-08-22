<section class="ncua-card">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">리스트화면 설정</h4>
        <p class="ncua-card__sub-info"><strong>*는 필수 입력</strong> 항목입니다.</p>
    </header>
    <section class="ncua-card__body board-template-list">
        <div class="ncua-table ncua-table--vertical">
            <table class="list-setting-table">
                <tr>
                    <th class="ncua-required"><div data-tooltip-seq="017">공지사항 노출설정</div></th>
                    <td>
                        <div class="ncua-gap-8">
                            항목 수 : <div class="ncua-input ncua-input--xs ncua-input-width-120" style="display: inline-block;">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="bdNoticeCount" value="<?= gd_isset($data['bdNoticeCount']) ?>" class="js-number" maxlength="5"/>
                                    </div>
                                </div>
                            </div>
                            개
                            <div class="ncua-flex ncua-flex-gap">
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="bdListInNotice" value="y" <?= gd_isset($checked['bdListInNotice']['y']) ?> />
                                    </span>
                                    <span><span class="ncua-checkbox-field__text">리스트 내 노출</span></span>
                                </label>
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="bdOnlyMainNotice" value="y" <?= gd_isset($checked['bdOnlyMainNotice']['y']) ?> />
                                    </span>
                                    <span><span class="ncua-checkbox-field__text">첫페이지만 노출</span></span>
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th class="ncua-required"><div>제목글 제한</div></th>
                    <td >
                        <div class="ncua-gap-8">
                            <div class="ncua-input ncua-input--xs ncua-input-width-120">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="bdSubjectLength" value="<?= gd_isset($data['bdSubjectLength']) ?>" class="js-number" maxlength="5"/>
                                    </div>
                                </div>
                            </div> 자
                        </div>
                    </td>
                </tr>

                <tr class="if-is-gallery-hide">
                    <th class="ncua-required"><div data-tooltip-seq="018">페이지당 게시물수</div></th>
                    <td >
                        <div class="ncua-gap-8">
                            <div class="ncua-input ncua-input--xs ncua-input-width-120">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="bdListCount" value="<?= gd_isset($data['bdListCount']) ?>" class="js-number" maxlength="5"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr class="if-is-gallery-show">
                    <th class="ncua-required"><div>페이지당 노출 수</div></th>
                    <td>
                        <div class="ncua-gap-8">
                            <div class="ncua-input ncua-input--xs">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="bdListColsCount" value="<?= gd_isset($data['bdListColsCount']) ?>" class="js-number" maxlength="5"/>
                                    </div>
                                </div>
                            </div> *
                            <div class="ncua-input ncua-input--xs">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="bdListRowsCount" value="<?= gd_isset($data['bdListRowsCount']) ?>" class="js-number" maxlength="5"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr <?php if($data['bdId'] !== 'goodsreview' && $data['bdId'] !== 'goodsqa'){ ?> class="display-none" <?php } ?> >
                    <th class="ncua-required"><div>상품상세 페이지 내<br />페이지별 게시물 수</div></th>
                    <td>
                        <div class="ncua-gap-8 ncua-flex-column ncua-align-items-flex-start">
                            <div class="ncua-flex ncua-gap-8 ncua-align-items-center">
                                <span>PC : </span>
                                <div class="ncua-input ncua-input--xs ncua-input-width-80" style="display: inline-block;">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="bdGoodsPageCountPc" value="<?= gd_isset($data['bdGoodsPageCountPc']) ?>" class="js-number" maxlength="3"/>
                                        </div>
                                    </div>
                                </div> 개
                            </div>
                            <div class="ncua-flex ncua-gap-8 ncua-align-items-center">
                                <span>모바일 : </span>
                                <div class="ncua-input ncua-input--xs ncua-input-width-80" style="display: inline-block;">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="bdGoodsPageCountMobile" value="<?= gd_isset($data['bdGoodsPageCountMobile']) ?>" class="js-number" maxlength="3"/>
                                        </div>
                                    </div>
                                </div> 개
                            </div>
                        </div>
                    </td>
                </tr>

                <tr class="if-is-gallery_event_qa_default-show">
                    <th><div>대표 이미지 노출 여부</div></th>
                    <td>
                        <div class="ncua-flex-gap ncua-flex-column ncua-align-items-flex-start">
                            <div class="ncua-switch ncua-switch--xs">
                                <label class="ncua-switch__option ncua-switch__option--left <?= gd_isset($checked['bdListImageFl']['y']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="bdListImageFl" value="y" <?= gd_isset($checked['bdListImageFl']['y']) ?> />
                                    <span class="ncua-switch__label">사용함</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right <?= gd_isset($checked['bdListImageFl']['n']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="bdListImageFl" value="n" <?= gd_isset($checked['bdListImageFl']['n']) ?> />
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                            <div class="ncua-flex ncua-gap-8">
                                <div>대표 이미지 설정 : </div>
                                <div class="ncua-flex-gap ncua-flex"> 
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text js-bdListImageTarget-goods">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="bdListImageTarget"
                                                value="goods" <?= gd_isset($checked['bdListImageTarget']['goods']) ?> />
                                        </span>
                                        <span><span class="ncua-radio-field__text">상품 이미지</span></span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text js-bdListImageTarget-upload ">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="bdListImageTarget"
                                                value="upload" <?= gd_isset($checked['bdListImageTarget']['upload']) ?> />
                                        </span>
                                        <span><span class="ncua-radio-field__text">업로드 이미지</span></span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text js-bdListImageTarget-editor">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="bdListImageTarget"
                                                value="editor" <?= gd_isset($checked['bdListImageTarget']['editor']) ?> />
                                        </span>
                                        <span><span class="ncua-radio-field__text">에디터 이미지</span></span>
                                    </label>
                                    <div class="notice-info js-bdListImageTarget-notice display-none">
                                        ※ '상품연동 / 업로드 파일 사용 / 에디터 사용' 중 1개 이상을 사용함으로 설정해야 합니다.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr class="if-is-gallery_event_qa_default-show" id="trListImageSize">
                    <th class="ncua-required"><div data-tooltip-seq="019">리스트 이미지 크기</div></th>
                    <td>
                        <div class="ncua-gap-8">
                            <div class="ncua-input ncua-input--xs ncua-input-width-120" style="display: inline-block;">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="bdListImageSize[width]" class="js-number"
                                            value="<?= $data['bdListImageSizeWidth'] ?>">
                                    </div>
                                </div>
                            </div> *
                            <div class="ncua-input ncua-input--xs ncua-input-width-120" style="display: inline-block;">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="bdListImageSize[height]" class="js-number"
                                            value="<?= $data['bdListImageSizeHeight'] ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr class="if-is-qa_default-show" id="trListNoticeImage">
                    <th><div data-tooltip-seq="020">공지글 이미지 노출 여부</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input name="bdListNoticeImageDisplayPc" type="checkbox" value='y' <?= gd_isset($checked['bdListNoticeImageDisplayPc']['y']) ?> />
                                </span>
                                <span><span class="ncua-checkbox-field__text">PC 쇼핑몰</span></span>
                            </label>
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input name="bdListNoticeImageDisplayMobile" type="checkbox" value='y' <?= gd_isset($checked['bdListNoticeImageDisplayMobile']['y']) ?> />
                                </span>
                                <span><span class="ncua-checkbox-field__text">모바일 쇼핑몰</span></span>
                            </label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><div data-tooltip-seq="021">검색 시 답변글 노출여부</div></th>
                    <td>
                        <div class="ncua-flex-column ncua-align-items-flex-start ncua-flex-gap">
                            <div class="ncua-switch ncua-switch--xs">
                                <label class="ncua-switch__option ncua-switch__option--left <?= gd_isset($checked['bdRecommendFl']['y']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input name="bdIncludeReplayInSearchFl" type="radio" class="ncua-switch__radio ncua-switch__radio--left"
                                    value='y' <?= gd_isset($checked['bdIncludeReplayInSearchFl']['y']) ?> />
                                    <span class="ncua-switch__label">사용</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right <?= gd_isset($checked['bdRecommendFl']['n']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input name="bdIncludeReplayInSearchFl" type="radio" class="ncua-switch__radio ncua-switch__radio--right"
                                    value='n' <?= gd_isset($checked['bdIncludeReplayInSearchFl']['n']) ?> />
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                            <div class="js-bdIncludeReplayInSearchFl-show ncua-flex ncua-flex-gap" style="display:none">
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input name="bdIncludeReplayInSearchType[]" type="checkbox"
                                            value='1' <?= gd_isset($checked['bdIncludeReplayInSearchType']['front']['y']) ?> />
                                    </span>
                                    <span><span class="ncua-checkbox-field__text">쇼핑몰 화면 적용</span></span>
                                </label>
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input name="bdIncludeReplayInSearchType[]" type="checkbox"
                                            value='2' <?= gd_isset($checked['bdIncludeReplayInSearchType']['admin']['y']) ?> />
                                    </span>
                                    <span><span class="ncua-checkbox-field__text">관리자 화면 적용</span></span>
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr class="if-is-event-show" data-table-bottom-shape>
                    <th><div>종료된 이벤트</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdEndEventType"
                                        value="read" <?= gd_isset($checked['bdEndEventType']['read']) ?> />
                                </span>
                                <span><span class="ncua-radio-field__text">읽기가능</span></span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdEndEventType"
                                        value="msg" <?= gd_isset($checked['bdEndEventType']['msg']) ?>/>
                                </span>
                                <span><span class="ncua-radio-field__text">접속제한 알럿메세지</span></span>
                            </label>
                            <div class="ncua-input ncua-input--xs ncua-input-width-320 is-disabled">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="bdEndEventMsg" value="<?= gd_isset($data['bdEndEventMsg']) ?>"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </section>
</section>
