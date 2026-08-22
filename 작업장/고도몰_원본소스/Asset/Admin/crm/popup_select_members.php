<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/select-members.css')?>" rel="stylesheet"/>
<article class="ncua-content ncua-select-members">
    <div class="ncua-select-members__content">
        <!-- 회원 검색 폼 -->
        <article class="ncua-select-members__article">
            <h2 class="ncua-select-members__title">회원선택</h2>
            <form id="formSearch" method="post">
                <input type="hidden" name="detailSearch" value="<?= $search['detailSearch']; ?>"/>
                <input type="hidden" name="sendMode" value="<?= $search['sendMode']; ?>"/>
                <input type="hidden" name="searchKind" value="fullLikeSearch"/>

                <div class="ncua-table ncua-table--vertical">
                    <table>
                        <tbody>
                        <tr>
                            <th><div>검색어</div></th>
                            <td>
                                <div class="ncua-gap-4">
                                    <span class="ncua-select ncua-select--xs">
                                        <span class="ncua-select__content">
                                            <?= gd_select_box('key', 'key', $combineSearch, null, null, null, null, 'ncua-select__tag'); ?>
                                        </span>
                                    </span>
                                    <span class="ncua-select ncua-select--xs js-search-kind-select-box">
                                        <span class="ncua-select__content">
                                            <?= gd_select_box('searchKind', 'searchKind', $searchKind, null, null, null, null, 'ncua-select__tag'); ?>
                                        </span>
                                    </span>
                                    <div class="ncua-input ncua-input--xs">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs">
                                                <input type="text" name="keyword" value="" placeholder="검색어를 입력하세요." />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>메일수신동의</div></th>
                            <td>
                                <div class="ncua-flex-gap">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="maillingFl" value="" <?= $checked['maillingFl'][''] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">전체</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="maillingFl" value="y" <?= $checked['maillingFl']['y'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">수신</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="maillingFl" value="n" <?= $checked['maillingFl']['n'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">수신거부</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>SMS수신동의</div></th>
                            <td>
                                <div class="ncua-flex-gap">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="smsFl" value="" <?= $checked['smsFl'][''] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">전체</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="smsFl" value="y" <?= $checked['smsFl']['y'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">수신</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="smsFl" value="n" <?= $checked['smsFl']['n'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">수신거부</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        </tbody>

                        <tbody class="js-search-detail display-none">
                        <tr>
                            <th><div>회원구분</div></th>
                            <td>
                                <div class="ncua-flex-gap">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="memberFl" value="" <?= $checked['memberFl'][''] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">전체</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="memberFl" value="personal" <?= $checked['memberFl']['personal'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">개인회원</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="memberFl" value="business" <?= $checked['memberFl']['business'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">사업자회원</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>회원등급</div></th>
                            <td>
                                <div>
                                <span class="ncua-select ncua-select--xs">
                                    <span class="ncua-select__content">
                                        <?= gd_select_box('groupSno', 'groupSno', $groups, null, $search['groupSno'], '등급', null, 'ncua-select__tag'); ?>
                                    </span>
                                </span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>가입승인</div></th>
                            <td>
                                <div class="ncua-flex-gap">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="appFl" value="" <?= $checked['appFl'][''] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">전체</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="appFl" value="y" <?= $checked['appFl']['y'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">승인</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="appFl" value="n" <?= $checked['appFl']['n'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">미승인</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>주문금액</div></th>
                            <td>
                                <div class="ncua-gap-8">
                                    <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-80">
                                                <input name="saleAmt[]" type="text" value="<?= $search['saleAmt'][0] ?? '' ?>" />
                                            </div>
                                        </div>
                                        <div class="ncua-input-text"><?= gd_currency_string(); ?></div>
                                    </div>
                                    <span>~</span>
                                    <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-80">
                                                <input name="saleAmt[]" type="text" value="<?= $search['saleAmt'][1] ?? '' ?>" />
                                            </div>
                                        </div>
                                        <div class="ncua-input-text"><?= gd_currency_string(); ?></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>마일리지</div></th>
                            <td>
                                <div class="ncua-gap-8">
                                    <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-80">
                                                <input name="mileage[]" type="text" value="<?= $search['mileage'][0] ?? '' ?>" />
                                            </div>
                                        </div>
                                        <div class="ncua-input-text"><?= Globals::get('gSite.member.mileageBasic.unit'); ?></div>
                                    </div>
                                    <span>~</span>
                                    <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-80">
                                                <input name="mileage[]" type="text" value="<?= $search['mileage'][1] ?? '' ?>" />
                                            </div>
                                        </div>
                                        <div class="ncua-input-text"><?= Globals::get('gSite.member.mileageBasic.unit'); ?></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>예치금</div></th>
                            <td>
                                <div class="ncua-gap-8">
                                    <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-80">
                                                <input name="deposit[]" type="text" value="<?= $search['deposit'][0] ?? '' ?>" />
                                            </div>
                                        </div>
                                        <div class="ncua-input-text"><?= Globals::get('gSite.member.depositConfig.unit'); ?></div>
                                    </div>
                                    <span>~</span>
                                    <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-80">
                                                <input name="deposit[]" type="text" value="<?= $search['deposit'][1] ?? '' ?>" />
                                            </div>
                                        </div>
                                        <div class="ncua-input-text"><?= Globals::get('gSite.member.depositConfig.unit'); ?></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>회원가입일</div></th>
                            <td>
                                <div>
                                    <div id="datepicker-sign-up-container"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>가입경로</div></th>
                            <td>
                                <div class="ncua-flex-gap">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="entryPath" value="" <?= $checked['entryPath'][''] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">전체</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="entryPath" value="pc"  <?= $checked['entryPath']['pc'] ?? '' ?>/>
                                    </span>
                                        <span class="ncua-radio-field__text">PC</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="entryPath" value="mobile" <?= $checked['entryPath']['mobile'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">모바일</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>최종로그인일</div></th>
                            <td>
                                <div>
                                    <div id="datepicker-login-container"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>방문횟수</div></th>
                            <td>
                                <div class="ncua-gap-8">
                                    <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-80">
                                                <input name="loginCnt[]" type="text" value="<?= $search['loginCnt'][0] ?? '' ?>" />
                                            </div>
                                        </div>
                                        <div class="ncua-input-text">회</div>
                                    </div>
                                    <span>~</span>
                                    <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-80">
                                                <input name="loginCnt[]" type="text" value="<?= $search['loginCnt'][1] ?? '' ?>" />
                                            </div>
                                        </div>
                                        <div class="ncua-input-text">회</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>장기 미로그인</div></th>
                            <td>
                                <div>
                                    <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-80">
                                                <input name="novisit" type="text" value="<?= $search['novisit'] ?? '' ?>" />
                                            </div>
                                        </div>
                                        <div class="ncua-input-text">일 이상 로그인하지 않은 회원</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>성별</div></th>
                            <td>
                                <div class="ncua-flex-gap">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="sexFl" value="" <?= $checked['sexFl'][''] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">전체</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="sexFl" value="m" <?= $checked['sexFl']['m'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">남자</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="sexFl" value="w" <?= $checked['sexFl']['w'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">여자</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>연령층</div></th>
                            <td>
                                <div class="ncua-gap-8">
                                <span class="ncua-select ncua-select--xs">
                                    <span class="ncua-select__content">
                                        <?= gd_select_box('age', 'age', gd_array_change_key_value(range(10, 70, 10)), '대', $search['age'], '전체', null, 'ncua-select__tag'); ?>
                                    </span>
                                </span>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="under14" value="y" <?= $checked['under14'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-checkbox-field__text">만 14세 미만 회원만 보기</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>생일</div></th>
                            <td>
                                <div class="ncua-gap-8">
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="birthFl" value="y">
                                    </span>
                                        <span class="ncua-checkbox-field__text">특정일 검색</span>
                                    </label>
                                    <span class="ncua-select ncua-select--xs">
                                    <span class="ncua-select__content">
                                        <select name="calendarFl" class="ncua-select__tag">
                                            <option value="">전체</option>
                                            <option value="s">양력</option>
                                            <option value="l">음력</option>
                                        </select>
                                    </span>
                                </span>
                                    <div id="datepicker-birthday-container"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>결혼여부</div></th>
                            <td>
                                <div class="ncua-flex-gap">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="marriFl" value="" <?= $checked['marriFl'][''] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">전체</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="marriFl" value="n" <?= $checked['marriFl']['n'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">미혼</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="marriFl" value="y" <?= $checked['marriFl']['y'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">기혼</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>결혼기념일</div></th>
                            <td>
                                <div>
                                    <div id="datepicker-marriage-container"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>연결계정</div></th>
                            <td>
                                <div class="ncua-flex-gap">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="connectSns" value="" <?= $checked['connectSns'][''] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">전체</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="connectSns" value="payco" <?= $checked['connectSns']['payco'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">페이코</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="connectSns" value="facebook" <?= $checked['connectSns']['facebook'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">페이스북</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="connectSns" value="naver" <?= $checked['connectSns']['naver'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">네이버</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="connectSns" value="kakao" <?= $checked['connectSns']['kakao'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">카카오</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="connectSns" value="wonder" <?= $checked['connectSns']['wonder'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">위메프</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="connectSns" value="google" <?= $checked['connectSns']['google'] ?? '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text">구글</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="ncua-btn-group ncua-align-right">
                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--text ncua-btn--toggle js-search-toggle">상세검색 <span>펼침</span></button>
                    <button type="reset" class="ncua-btn ncua-btn--xs ncua-btn--text has-underline">초기화</button>
                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary search-members-btn">검색</button>
                </div>
            </form>

            <!-- 검색 결과 -->
            <div id="layerSelectMembersResult"></div>
        </article>
    
        <menu class="ncua-select-members__select-actions">
            <li>
                <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray" id="addMember">추가</button>
            </li>
            <li>
                <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray" id="delMember">삭제</button>
            </li>
        </menu>
        <div id="layerSelectedMembersResult"></div>
    </div>
    <footer class="ncua-select-members__footer">
        <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray" onclick="self.close();">취소</button>
        <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--primary" id="addSelectedMembers">확인</button>
    </footer>
</article>
<script type="text/javascript" src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-datepicker-factory/ncds-datepicker-factory.js') ?>"></script>
<script type="text/javascript" src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-checkbox-group/ncds-checkbox-group.js') ?>"></script>
<script type="text/javascript">
    // 검색 영역
    const formSearch = document.querySelector('#formSearch');
    const formSelectedList = document.querySelector('#formSelectedList');
    let selectedMemberNos = [];

    document.addEventListener('DOMContentLoaded', function() {
        initializeEvents();
        initializeElements();
        initializeDetailSearch();
        initializeCheckboxActions();

        // 회원 조회
        loadContents(new FormData(formSearch));

        // opener에서 선택된 회원 추가
        setAlreadySelectedMembers();

        // 선택된 회원 리스트 노출
        loadSelectedContents();
    });

    function initializeElements() {
        createCRMDatePicker({containerId: 'datepicker-sign-up-container', startAttrName: 'entryDt[]', endAttrName: 'entryDt[]', buttons: []});
        createCRMDatePicker({containerId: 'datepicker-login-container', startAttrName: 'lastLoginDt[]', endAttrName: 'lastLoginDt[]', buttons: []});
        createCRMDatePicker({containerId: 'datepicker-birthday-container', startAttrName: 'birthDt[]', endAttrName: 'birthDt[]', buttons: []});
        createCRMDatePicker({containerId: 'datepicker-marriage-container', startAttrName: 'marriDate[]', endAttrName: 'marriDate[]', buttons: []});
    }

    function initializeDetailSearch() {
        formSearch.querySelector('.js-search-toggle').addEventListener('click', function() {
            formSearch.querySelector('.js-search-detail').classList.toggle('opened');
        });
    }
    
    function initializeCheckboxActions() {
        document.querySelector('#layerSelectMembersResult').addEventListener('change', function(e) {
            if (!e.target.matches('input[type=checkbox]')) return;
            const hasChecked = this.querySelectorAll('input[name="chk[]"]:checked').length > 0;
            document.querySelector('#addMember').classList.toggle('active', hasChecked);
        });

        document.querySelector('#layerSelectedMembersResult').addEventListener('change', function(e) {
            if (!e.target.matches('input[type=checkbox]')) return;
            const hasChecked = this.querySelectorAll('input[name="chk[]"]:checked').length > 0;
            document.querySelector('#delMember').classList.toggle('active', hasChecked);
        });
    }

    function initializeEvents() {
        // 회원 선택 확인(완료) 버튼
        document.querySelector('#addSelectedMembers').addEventListener('click', (e) => {
            if (!window.opener || window.opener.closed) return;
            window.opener.setSelectedMembers(selectedMemberNos);
            window.close();
        });

        document.querySelector('input[type=checkbox][name=birthFl]').addEventListener('change', (e) => {
            if (e.target.checked) {
                createCRMDatePicker({
                    containerId: 'datepicker-birthday-container',
                    buttons: [],
                    datePickerOptions: [{
                        element: 'start-date',
                        attrName: 'birthDt[]',
                        options: {
                            mode: 'single',
                            dateFormat: 'Y-m-d',
                            locale: 'ko'
                        }
                    }]
                });
            } else {
                createCRMDatePicker({containerId: 'datepicker-birthday-container', startAttrName: 'birthDt[]', endAttrName: 'birthDt[]', buttons: []});
            }
        });

        document.querySelectorAll('input[type=radio][name=maillingFl], input[type=radio][name=smsFl]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (e.target.value !== 'y') {
                    const alert = NCDSAlert({
                        message: '검색 시 수신거부 회원이 포함됩니다. 정보통신망법에 따라 수신거부한 회원에게는 광고성 정보를 발송할 수 없으며, 위반 시 과태료가 부과되므로 유의하시기 바랍니다.',
                        iconType: 'error',
                    });
                    setTimeout(() => alert.close(), 3000);
                }
            });
        });

        document.querySelector('select[name=key]').addEventListener('change', (e) => {
            const searchKindSelectBox = document.querySelector('.js-search-kind-select-box');
            if (e.target.value === 'company' || e.target.value === 'busiNo') {
                searchKindSelectBox.classList.add('display-none');
            } else {
                searchKindSelectBox.classList.remove('display-none');
            }
        });

        formSearch.querySelector('.search-members-btn').addEventListener('click', (e) => {
            const formData = new FormData(formSearch);
            formData.set('searchAction', 'Y');
            loadContents(formData);
        });

        // 선택 영역 회원 추가
        document.querySelector('#addMember').addEventListener('click', (e) => {
            const checkedMemberNos = Array.from(document.querySelectorAll('.js-select-members-result input[name="chk[]"]:checked')).map(input => input.value);

            if (!checkedMemberNos.length) return;

            selectedMemberNos = [...new Set([...selectedMemberNos, ...checkedMemberNos])];

            loadSelectedContents();
        });

        // 선택 영역 제외
        document.querySelector('#delMember').addEventListener('click', (e) => {
            const checkedMemberNos = Array.from(document.querySelectorAll('.js-selected-members-result input[name="chk[]"]:checked')).map(input => input.value);

            if (!checkedMemberNos) return;

            selectedMemberNos = selectedMemberNos.filter(memberNo => !checkedMemberNos.includes(memberNo));

            loadSelectedContents()
        });
    }

    // opener에서 선택 되어있는 회원 포함
    const setAlreadySelectedMembers = () => {
        if (!window.opener || window.opener.closed) return;

        const openerSelectedMemberNos = window.opener.getSelectedMembers();
        if (!openerSelectedMemberNos) return;

        selectedMemberNos = [...new Set([...selectedMemberNos, ...openerSelectedMemberNos])];

        loadSelectedContents();
    }

    function loadContents(formData) {
        $.ajax({
            url: './popup_select_members/layer_select_members_result.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                $('#layerSelectMembersResult').html(data);
            },
        });
    }

    function loadSelectedContents(formData = null) {
        if (!formData) {
            formData = new FormData();
            selectedMemberNos.forEach(memberNo => {
                formData.append('selectedMemberNos[]', memberNo);
            });
            const sortSelected = document.querySelector('#sortSelected');
            if(sortSelected) formData.append('sort', sortSelected.value);
            const pageNumSelected = document.querySelector('#pageNumSelected');
            if (pageNumSelected) formData.append('pageSize', pageNumSelected.value);
        }

        $.ajax({
            url: './popup_select_members/layer_selected_members_result.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                $('#layerSelectedMembersResult').html(data);
                refreshSelectedMemberCounts();
            },
        });
    }

    function refreshSelectedMemberCounts() {
        document.querySelector('.selected-count').innerText = selectedMemberNos.length;
    }
</script>
