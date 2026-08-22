<input type="hidden" id="depth-toggle-hidden-seoTag" value="<?=$toggle['seoTag_'.$SessScmNo]?>">
<div id="depth-toggle-line-seoTag" class="depth-toggle-line display-none"></div>
<div id="depth-toggle-layer-seoTag">
    <?php if(empty($data['seoTag']['data']['sno']) === false) { ?><input type="hidden" name="seoTagSno" value="<?=$data['seoTag']['data']['sno']; ?>" /> <?php } ?>
    
    <div class="ncua-table ncua-table--vertical">
        <table>
            <tr>
                <th><div data-tooltip-seq="024">개별 설정 사용여부</div></th>
                <td>
                    <div>
                        <div class="ncua-switch ncua-switch--xs">
                            <label class="ncua-switch__option ncua-switch__option--left <?= gd_isset($checked['seoTagFl']['y']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="seoTagFl" value="y" <?=gd_isset($checked['seoTagFl']['y']); ?> />
                                <span class="ncua-switch__label">사용함</span>
                            </label>
                            <label class="ncua-switch__option ncua-switch__option--right <?= gd_isset($checked['seoTagFl']['n']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="seoTagFl" value="n" <?=gd_isset($checked['seoTagFl']['n']); ?> />
                                <span class="ncua-switch__label">사용안함</span>
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
            <?php
            foreach($data['seoTag']['config'] as $k => $v) {
                $maxLength = 200;
                if (gd_in_array($k, ['description', 'keyword']) === true) {
                    $maxLength = 300;
                }
                
                // Placeholder 설정
                $placeholder = '';
                switch($k) {
                    case 'title':
                        $placeholder = '타이틀을 입력하세요';
                        break;
                    case 'author':
                        $placeholder = '메타태그 작성자를 입력하세요';
                        break;
                    case 'description':
                        $placeholder = '메타태그에 대한 설명을 입력하세요';
                        break;
                    case 'keyword':
                        $placeholder = '메타태그 키워드를 입력하세요';
                        break;
                    default:
                        $placeholder = $v . '을(를) 입력하세요';
                }
                ?>
                <tr>
                    <th><div><?=$v?></div></th>
                    <td>
                        <div>
                            <div class="ncua-input ncua-input--md ncua-input-full-width">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--md">
                                        <input type="text" name="seoTag[<?=$k?>]" value="<?= $data['seoTag']['data'][$k] ?? '' ; ?>" maxlength="<?=$maxLength;?>" placeholder="<?=$placeholder?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="js-replace-code-<?=$data['seoTag']['target']?> display-none">
        <div class="ncua-table ncua-table--horizontal">
            <table>
                <thead>
                <tr>
                    <th>번호</th>
                    <th>치환코드</th>
                    <th>설명</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $num = 1;
                foreach($data['seoTag']['replaceCode'] as $k1 =>$v1) { ?>
                    <tr class="text-center">
                        <td><?=$num++?></td>
                        <td><?=$k1?></td>
                        <td><?=$v1?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
