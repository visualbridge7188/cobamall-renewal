<div class="ncua-search-result">
    <form id="frmList" action="../board/board_ps.php" method="post">
        <input type="hidden" name="mode" value="delete">
        <div class="ncua-search-result__summary">
          <p class="ncua-search-result__summary-count">
            검색 <strong><?=$cnt['search'] ?></strong>개 / 총 <strong><?=$cnt['total']?></strong>개
          </p>
        </div>
        <div class="ncua-search-result__content">
            <div class="ncua-search-result__actions">
                <button type="submit" id="btnDeleteSelected" class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete"><span class="ncua-btn__label">선택 삭제</span></button>
            </div> 
            <div class="ncua-table ncua-table--horizontal">
                <table>
                    <colgroup>
                        <col width="56px"> <!-- 체크박스 -->
                        <col width="80px"> <!-- 번호 -->
                        <col width="120px"> <!-- 아이디 -->
                        <col width="140px"> <!-- 이름 -->
                        <col width="5.5%"> <!-- 신귀게시글 -->
                        <col width="5.5%"> <!-- 전체게시글 -->
                        <col width="5.5%"> <!-- 미답변 -->
                        <col width="5.5%"> <!-- 유형 -->
                        <col width="10.5%"> <!-- PC쇼핑몰 스킨 -->
                        <col width="10.5%"> <!-- 모바일쇼핑몰 스킨 -->
                        <col width="80px"> <!-- URL복사 -->
                        <col width="5.5%"> <!-- 쇼핑몰 -->
                        <col width="5.5%"> <!-- 게시글 -->
                        <col width="5.5%"> <!-- 수정 -->
                    </colgroup>
                    <thead>
                    <tr>
                        <th>
                            <div class="ncua-align-center">
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" class="js-checkall" data-target-name="sno[]">
                                    </span>
                                </label>
                            </div>    
                        </th>
                        <th><div>번호</div></th>
                        <th><div>아이디</div></th>
                        <th><div>이름</div></th>
                        <th><div>신규게시글</div></th>
                        <th><div>전체게시글</div></th>
                        <th><div>미답변</div></th>
                        <th><div>유형</div></th>
                        <th><div>PC쇼핑몰 스킨</div></th>
                        <th><div>모바일쇼핑몰 스킨</div></th>
                        <th><div>URL복사</div></th>
                        <th><div>쇼핑몰</div></th>
                        <th><div>게시글</div></th>
                        <th><div>수정</div></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (isset($data) && is_array($data)) {
                        foreach ($data as $val) {
                            $bdQuestionCnt = $val['bdQuestionCnt'];
                            if(is_numeric($bdAnswerCnt)) {
                                $bdQuestionCnt = number_format($val['bdQuestionCnt ']);
                            }
                            ?>
                            <tr>
                                <td>
                                    <div>
                                        <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                <input name="sno[]" type="checkbox" value="<?= $val['sno'] ?>" <?php if ($val['bdBasicFl'] == 'y') echo 'disabled' ?>>
                                            </span>
                                        </label>
                                        <input name="seoTagSno[<?= $val['sno'] ?>]" type="hidden" value="<?php echo $val['seoTagSno']; ?>">
                                    </div>
                                </td>
                                <td><div><?=number_format($val['listNo']); ?></div></td>
                                <td>
                                    <div>
                                        <a href="./board_register.php?sno=<?=$val['sno']; ?>" class="btn-link"><?=$val['bdId']; ?></a>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <a href="./board_register.php?sno=<?=$val['sno']; ?>"><?=$val['bdNm']; ?></a>
                                    </div>
                                </td>
                                <td><div><?=number_format($val['bdNewListCnt']); ?></div></td>
                                <td><div><?=number_format($val['bdListCnt']); ?></div></td>
                                <td><div><?=$bdQuestionCnt; ?></div></td>
                                <td><div><?=$val['bdKindStr']; ?></div></td>
                                <td>
                                    <div class="ncua-flex-column ncua-gap-4">
                                        <?php if($gGlobal['isUse']) {?>
                                            <?php foreach($gGlobal['useMallList'] as $gVal) {
                                                $domainPostfix = $gVal['domainFl'] == 'kr' ? '' : ucfirst($gVal['domainFl']);
                                                if(!$val['theme'.$domainPostfix.'Sno'] || empty($val['theme'.$domainPostfix.'Nm'])){
                                                    continue;
                                                }?>
                                                <div>
                                                    <span class="flag flag-16 flag-<?= $gVal['domainFl'] ?>"></span>
                                                    <a href="board_theme_register.php?sno=<?=$val['theme'.$domainPostfix.'Sno']?>" target="_blank"><?=$val['theme'.$domainPostfix.'Nm']?></a>
                                                </div>
                                            <?php }?>
                                        <?php }
                                        else {?>
                                            <a href="board_theme_register.php?sno=<?=$val['themeSno'] ?>" target="_blank"><?=$val['themeNm']; ?></a>
                                        <?php }?>
                                    </div>
                                </td>
                                <td>
                                    <div class="ncua-flex-column ncua-gap-4">
                                        <?php if($gGlobal['isUse']) {?>
                                            <?php foreach($gGlobal['useMallList'] as $gVal) {
                                                $domainPostfix = $gVal['domainFl'] == 'kr' ? '' : ucfirst($gVal['domainFl']);
                                                if(!$val['mobileTheme'.$domainPostfix.'Sno'] || empty($val['mobileTheme'.$domainPostfix.'Nm'])){
                                                    continue;
                                                }?>
                                            <div>
                                                <span class="flag flag-16 flag-<?= $gVal['domainFl'] ?>"></span>
                                                <a href="board_theme_register.php?sno=<?=$val['mobileTheme'.$domainPostfix.'Sno']?>" target="_blank"><?=$val['mobileTheme'.$domainPostfix.'Nm']?></a>
                                            </div>
                                        <?php }?>
                                        <?php }
                                        else {?>
                                                <a href="board_theme_register.php?sno=<?=$val['mobileThemeSno'] ?>" target="_blank"><?=$val['mobileThemeNm']; ?></a>
                                        <?php }?>
                                    </div>
                                </td>
                                <td>
                                    <div class="ncua-gap-4 ncua-flex-column">
                                        <button type="button" data-clipboard-text="<?=$val['pageUrl'] ?>" class="ncua-clipboard ncua-btn ncua-btn--xxs ncua-btn--secondary-gray" title="<?=$val['bdNm']; ?>">
                                            PC
                                        </button>
                                        <button type="button" data-clipboard-text="<?=$val['pageMobileUrl'] ?>" class="ncua-clipboard ncua-btn ncua-btn--xxs ncua-btn--secondary-gray" title="<?=$val['bdNm']; ?>">
                                            모바일
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <a href="<?=$val['pageUrl'] ?>" target="_blank" data-fl="user" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray user-board">보기</a>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <a href="./article_list.php?bdId=<?=$val['bdId'] ?>" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray">관리</a>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <a href="./board_register.php?sno=<?=$val['sno']; ?>" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray">수정</a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="ncua-table-bottom"></div>
        </div>
        <div class="ncua-pagination"><?=$pagination?></div>
    </form>
</div>  
