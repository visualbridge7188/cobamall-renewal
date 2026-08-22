<form id="frmMobileShop" name="frmMobileShop" action="./mobile_ps.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="mobile_config" />
<input type="hidden" name="mobileShopIconTmp" value="<?php echo $data['mobileShopIcon']; ?>"/>
    <div class="page-header js-affix">
        <h3 class="ncua-help-manual"><?= end($naviMenu->location); ?></h3>
        <input type="submit" value="저장" class="btn btn-red">
    </div>

    <div class="table-title gd-help-manual">
        모바일샵 사용 여부
    </div>
    <table class="table table-cols">
    <colgroup><col class="width-md" /><col/></colgroup>
    <tr>
        <th>사용 여부</th>
        <td>
            <label class="radio-inline"><input type="radio" name="mobileShopFl" value="y" <?php echo gd_isset($checked['mobileShopFl']['y']);?> />사용함</label>
            <label class="radio-inline"><input type="radio" name="mobileShopFl" value="n" <?php echo gd_isset($checked['mobileShopFl']['n']);?> />사용안함</label>
        </td>
    </tr>
    <?php if ($data['mobileShopFl'] == 'y') {?>
    <tr>
        <th>모바일샵 주소</th>
        <td class="form-inline">
            <?php if ($browserCheck === true) {?>
            <input type="button" class="btn btn-sm btn-gray" value="모바일샵 미리보기" onclick="window.open('<?php echo URI_MOBILE;?>', 'mobileShop', 'width=375, height=667, scrollbars=yes');" />
            <?php } else {?>
            <input type="button" class="btn btn-sm btn-gray" value="모바일샵 미리보기" onclick="alert('현재 사용하시는 Browser로는 모바일샵을 보실 수 없습니다.')" />
            <?php }?>
            <label>
                <?php echo URI_MOBILE;?>
            </label>
        </td>
    </tr>
    <?php }?>
    <tr>
        <th>홈 화면 아이콘</th>
        <td class="form-inline">
            <input type="file" class="upload" id="mobileShopIcon" name="mobileShopIcon">
            <ul class="notice-info">
                <li>권장 이미지 사이즈 : 100px x 100 px  / 500kb이하</li>
                <li>권장 확장자 : png</li>
            </ul>
            <div>
                <?php
                if (empty($data['mobileShopIcon']) === false) {
                    echo gd_html_image($data['mobileShopIcon'], '바로가기 아이콘');
                    if (stripos($data['mobileShopIcon'], 'commonimg') === false) {
                        echo '<label class="checkbox-inline" style="padding-left:10px"><input type="checkbox" name="mobileShopIconDel" value="y" />체크 시 삭제</label>';
                    }
                }
                ?>
            </div>
        </td>
    </tr>
    </table>

    <input type="hidden" name="mobileShopGoodsFl" value="each" /> <!-- 상품 출력 : 추후 지원함. 현재는 모바일샵 별도 출력상태 적용 -->
    <input type="hidden" name="mobileShopCategoryFl" value="same" /> <!-- 카테고리 출력 : 추후 지원함. 현재는 "온라인 쇼핑몰 출력상태와 동일하게 적용"으로 설정-->
    <!--
    <div class="table-title gd-help-manual">
        모바일샵 출력상태 설정
    </div>
    <table class="table table-cols">
    <colgroup><col class="width-md" /><col/></colgroup>
    <tr>
        <th>상품 출력</th>
        <td>
            <div class="radio">
                <label><input type="radio" name="mobileShopGoodsFl" value="same" <?php echo gd_isset($checked['mobileShopGoodsFl']['same']);?> />온라인 쇼핑몰 출력상태와 동일하게 적용</label>
            </div>
            <div class="radio">
                <label><input type="radio" name="mobileShopGoodsFl" value="each" <?php echo gd_isset($checked['mobileShopGoodsFl']['each']);?> />모바일샵 별도 출력상태 적용</label>
            </div>
        </td>
    </tr>
    <tr>
        <th>카테고리 출력</th>
        <td>
            <p class="notice-info mgb0">
                추후 지원함. 현재는 &quot;온라인 쇼핑몰 출력상태와 동일하게 적용&quot;으로 설정
            </p>
            <label><input type="radio" name="mobileShopCategoryFl" value="same" <?php echo gd_isset($checked['mobileShopCategoryFl']['same']);?> />온라인 쇼핑몰 출력상태와 동일하게 적용</label><br />
            <label><input type="radio" name="mobileShopCategoryFl" value="each" <?php echo gd_isset($checked['mobileShopCategoryFl']['each']);?> />모바일샵 별도 출력상태 적용</label>
        </td>
    </tr>
    </table>
    -->
</form>

<script type="text/javascript">
    <!--
    $(document).ready(function(){
        const currentSkinType = '<?= $currentSkinType ?? 'adaptive' ?>';
        const initialMobileShopFl = '<?= $data['mobileShopFl'] ?? 'n' ?>';
        const isMyappInstalled = <?= json_encode($isMyappInstalled ?? false) ?>;
        const isMyappReleased = <?= json_encode($isMyappReleased ?? false) ?>;

        // 마이앱 사용 중 + 적응형 스킨 사용 중 + 모바일샵 사용안함인 경우 안내 얼럿
        if (isMyappInstalled && isMyappReleased && currentSkinType === 'adaptive' && initialMobileShopFl === 'n') {
            NCDSAlert({
                message: '모바일샵을 \'사용함\'으로 변경해 주세요.',
                subMessage: '현재 적응형 스킨이 적용된 상태에서 모바일샵이 \'사용안함\'으로 설정되어 있습니다. 이 설정에서는 마이앱 실행 시 사용자 랜딩이 정상적으로 이루어지지 않을 수 있습니다. 원활한 서비스 제공을 위해 모바일샵을 \'사용함\'으로 변경해 주세요.',
                iconType: 'warning'
            });
        }

        $("#frmMobileShop").validate({
            submitHandler: function (form) {
                const selectedMobileShopFl = $('input[name="mobileShopFl"]:checked').val();

                // 반응형 스킨 사용 중에 모바일샵 사용안함 -> 사용함으로 변경하려는 경우
                if (currentSkinType === 'responsive' && initialMobileShopFl === 'n' && selectedMobileShopFl === 'y') {
                    NCDSConfirm({
                        message: '모바일샵 사용여부를 변경할 수 없습니다.',
                        subMessage: '<p class="layer-description--sub">현재 반응형 스킨을 사용 중입니다. 설정을 변경하시려면 먼저 \'디자인 스킨 리스트\'에서 적응형 스킨으로 변경해 주세요.</p>'
                    }).then(function(result) {
                        if (result) {
                            location.href = '/design/design_skin_list.php';
                        }
                    });
                    return false;
                }

                form.target = 'ifrmProcess';
                form.submit();
                return false;
            },
            rules: {
            },
            messages: {
            }
        });
    });
    //-->
</script>
