<div class="ncua-search-result">
    <form id="frmList" action="./faq_ps.php" method="post">
        <input type="hidden" name="mode" value="delete">

        <!-- summary -->
        <div class="ncua-search-result__summary">
            <p class="ncua-search-result__summary-count">
                검색 <strong><?=$pageInfo['searchCount'] ?></strong>개 /
                전체 <strong><?=$pageInfo['totalCount'] ?></strong>개
            </p> 
        </div>
        <!-- // summary -->
        <div class="ncua-search-result__content">
            <!-- 검색 결과 액션 -->
            <div class="ncua-search-result__actions">
                <div>
                    <button type="submit" class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete">선택 삭제</button>
                </div>
            </div>
            <!-- // 검색 결과 액션 -->
            <!-- table -->
            <div class="ncua-table ncua-table--horizontal">
                <table>
                    <colgroup>
                        <col width="56px">
                        <col width="80px">
                    <?php if($gGlobal['isUse']){?>
                        <col width="120px">
                    <?php }?>
                        <col width="120px">
                        <col width="*">
                        <col width="80px">
                        <col width="120px">
                        <col width="120px">
                    </colgroup>
                    <thead>
                    <tr>
                        <th><div>
                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                <input type="checkbox" id="selectedAll" />
                            </span>
                        </label>    
                        </div></th>
                        <th><div>번호</div></th>
                        <?php if($gGlobal['isUse']){?>
                            <th><div>상점 구분</div></th>
                        <?php }?>
                        <th><div>카테고리</div></th>
                        <th><div>제목</div></th>
                        <th><div>유형</div></th>
                        <th><div>등록일</div></th>
                        <th><div>수정</div></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (gd_isset($data)) {
                        $i = 0;
                        foreach ($data as $val) {
                            $i++;
                            ?>
                            <tr class="text-center">
                                <td><div>
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="chk[]" value="<?=$val['sno']; ?>" />
                                    </span>
                                </label>    
                                </div></td>
                                <td><div><?=$i ?></div></td>
                                <?php if($gGlobal['isUse']){?>
                                    <td>
                                        <div>
                                            <span class="flag flag-16 flag-<?= gd_isset($gGlobal['mallList'][$val['mallSno']]['domainFl'], 'kr'); ?>"></span><?= gd_isset($gGlobal['mallList'][$val['mallSno']]['mallName'], '기준몰'); ?>
                                        </div>
                                    </td>
                                <?php }?>
                                <td><div><?=gd_code_item($val['category'],$val['mallSno']); ?></div></td>
                                <td>
                                    <div class="faq-list-table-title">
                                        <a class="ncua-faq-search-result-subject" href="./faq_register.php?sno=<?=$val['sno']; ?>"><?=$val['subject']; ?></a>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <?=($val['isBest'] == 'y') ? '베스트' : '일반'; ?>
                                    </div>
                                </td>
                                <td><div><?=gd_date_format('Y-m-d', $val['regDt']); ?></div></td>
                                <td>
                                    <div>
                                        <a href="./faq_register.php?sno=<?=$val['sno']; ?>" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray faq-list-modify-btn">수정</a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td class="no-data" colspan="<?= ($gGlobal['isUse']) ? '8' : '7'; ?>"><div>검색된 정보가 없습니다.</div></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <!-- // table -->
            <div class="ncua-table-bottom"></div>
        </div>
    </form>
</div>
