<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/share/popup-goods.css')?>" rel="stylesheet"/>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/supply-combo-box.js')?>"></script>
<article class="ncua-content ncua-popup-goods">
    <div class="ncua-popup-goods__content">
        <!-- 상품선택 리스트-->
        <?php include $selectGoods; ?>
    
        <!-- 등록상품 리스트-->
        <?php if($checkCheckboxType) {?>
            <menu class="ncua-popup-goods__select-actions">
                <li>
                    <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray" id="addGoods">추가</button>
                </li>
                <li>
                    <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray" id="delGoods">삭제</button>
                </li>
            </menu>
            <?php include $addGoods; ?>
        <?php } ?>
    </div>
    <footer class="ncua-popup-goods__footer">
        <button type="button" id="goodsChoiceCancel" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray" onclick="self.close();">취소</button>
        <?php if($checkCheckboxType) {?>
            <button type="button" id="goodsChoiceConfirm" class="goodsChoiceConfirm ncua-btn ncua-btn--sm ncua-btn--secondary">선택완료</button>
        <?php } else {?>
            <button type="button" id="goodsChoiceConfirm" class="goodsRadioChoiceConfirm ncua-btn ncua-btn--sm ncua-btn--primary">선택완료</button>
        <?php }?>
    </footer>
</article>
<script src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-multi-select/ncds-multi-select.js')?>"></script>
<script type="text/javascript">
    <!--
    $(document).ready(function(){
        function searchGoods() {
            var formData = $("#frmSearchBase").serialize();
            
            $.ajax({
                url: window.location.href, // 현재 URL
                type: 'POST',
                data: formData,
                success: function(response) {
                    // 응답에서 상품 리스트 부분만 추출하여 업데이트
                    var $response = $(response);
                    var newGoodsList = $response.find('#iframe_goodsChoiceList').html();
                    $('#iframe_goodsChoiceList').html(newGoodsList);
                },
                error: function() {
                    NCDSAlert({message: '검색 중 오류가 발생했습니다.'});
                }
            });
        }
        /**
         * [전체 삭제]인 경우를 체크하여 html() 강제 치환 - 2018.10.01 parkjs
         * selectedGoodsList 값이 none인 상태에서 넘겨받은 html의 length가 0일 경우 전체 삭제로 간주함.
         **/
        if ($('#selectedGoodsList').val() == "none" && $('#tbl_add_goods_result > tbody').attr('contents-length') == 0) {
            $('#tbl_add_goods_result > tbody').html("");
        }

        $('.goodsRadioChoiceConfirm').bind('click',function(){
            if($('input[type=radio][name="itemGoodsNo[]"]:checked').length<1) {
                NCDSAlert({message: '상품을 선택해주세요.'});
                return;
            }

            var resultJson = {
                "info": []
            };

            var checkedGoodsNo = $('input[type=radio][name="itemGoodsNo[]"]:checked').val();
            var imgSrc = $('#tbl_add_goods_'+checkedGoodsNo).find('.itemImage img').attr('src');
            var name = $('#tbl_add_goods_'+checkedGoodsNo).find('.itemName').text();
            var price = $('#tbl_add_goods_'+checkedGoodsNo).find('.itemPrice').text();

            resultJson.info.push({
                "goodsNo": checkedGoodsNo,
                "goodsImgageSrc": imgSrc,
                "goodsName": name,
                "goodsPrice": price,
            });

            opener.setAddGoods(resultJson);
            self.close();
        })

        $('input').keydown(function(e) {
            if (e.keyCode == 13) {
                $("input[name='setGoodsList']").val( encodeURIComponent($("#tbl_add_goods_result tbody").html()));
                $("#frmSearchBase").submit();
                return false
            }
        });


        $('.search-goods-btn').click(function() {

            $("input[name='setGoodsList']").val( encodeURIComponent($("#tbl_add_goods_result tbody").html()));
            $("#frmSearchBase").submit();
            searchGoods();
        });

        $('.pagination li a').click(function() {

            $("input[name='page']").val($(this).data('page'));
            $('.search-goods-btn').click();
        });

    });

    $('select[name=\'pageNum\']').change(function (e) {
        $('input[name=\'pageNum\']').val(e.target.value);
        $('.search-goods-btn').click();
    });

    $('select[name=\'sort\']').change(function (e) {
        $('input[name=\'sort\']').val(e.target.value);
        $('.search-goods-btn').click();
    })

    $( ".js-goods-popup" ).click(function() {
        goods_register_popup($(this).data('goodsno'));
    });


    function search_register() {
        $("#allCheck").click();

        $("#addGoods").click();

    }

    //-->

const datePicker = new ncua.DatePicker(document.querySelector('#datepicker-container'), {
    size: 'xs', 
    datePickerOptions: [
        {
        element: 'start-date',
        attrName: 'searchDate[]',
        options: {
            mode: 'single',
            static: true,
            dateFormat: 'Y-m-d',
            clickOpens: true,
            allowInvalidPreload: true,
            allowInput: true,
            locale: 'ko',
        },
        },
        {
        element: 'end-date',
        attrName: 'searchDate[]',
        options: {
            mode: 'single',
            static: true,
            dateFormat: 'Y-m-d',
            clickOpens: true,
            allowInvalidPreload: true,
            allowInput: true,
            locale: 'ko',
        },
        },
    ],
});
datePicker.setDate(["<?php echo $search['searchDate'][0]; ?>", "<?php echo $search['searchDate'][1]; ?>"]);


const categoryMultiSelect = createNcuaMultiSelectManager({
    targetGroups: document.querySelector('.js-category-box'),
});


const brandMultiSelect = createNcuaMultiSelectManager({
    targetGroups: document.querySelector('.js-brand-box'),
});

try {
    /**
     * 공급사 선택 ComboBox 초기화
     */
    const initSupplyComboBoxHandler = () => {
        if (typeof window.initSupplyComboBox === 'undefined') {
            setTimeout(initSupplyComboBoxHandler, 100);
            return;
        }

        // 초기 데이터 준비
        const initData = [];
        <?php if (($search['scmFl'] == 'y') && !empty($search['scmNo'])) { ?>
            <?php foreach ($search['scmNo'] as $k => $v) { ?>
                initData.push({
                    id: '<?= $v ?>',
                    label: '<?= addslashes($search['scmNoNm'][$k]) ?>'
                });
            <?php } ?>
        <?php } ?>

        const applySupplyComboBox = window.initSupplyComboBox({
            scmLayerSelector: 'scmLayer',
            dataInputNm: 'scmNo',
            comboBoxLayerId: 'ncua-combo-box-layer',
            radioGroupClass: 'supply-radio-group',
            initData: initData,
            tagIdPrefix: 'info_scm',
            tagHiddenName: 'scmNoNm',
            apiUrl: '../ncds/layer_scm.php',
        });
    }

    initSupplyComboBoxHandler();
} catch (error) {
    console.error('공급사 ComboBox 초기화 오류:', error);
}
categoryMultiSelect.init();
brandMultiSelect.init();

document.querySelector('[type="reset"]')?.addEventListener('click', (e) => {
    const url = window.location.href;
    
    window.location.href = url;
});
</script>
