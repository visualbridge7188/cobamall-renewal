<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/plus-review-milage.css')?>" rel="stylesheet"/>

<form id="frmMilageAdd" name="frmMilageAdd" action="./plus_review_ps.php" method="post" target="ifrmProcess" class="content-form js-setup-form">
    <article class="ncua-content modal-dialog__content">
        <div class="ncua-table ncua-table--vertical plus-review-milage">
            <table>
                <colgroup>
                    <col width="110px">
                    <col>
                </colgroup>
                <tr>
                    <th><div>지급방법</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <div class="milage-add-way">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input name="milageAddWay" type="radio" value='autoSet' checked/>
                                    </span>
                                    <span class="ncua-radio-field__text">설정된 마일리지 지급</span>

                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input name="milageAddWay" type="radio" value='direct'/>
                                    </span>
                                    <span class="ncua-radio-field__text">직접 지급</span>
                                </label>
                            </div>
                            <div class="plus-review-milage__bullet-list">설정된 마일리지 지급 시 '플러스리뷰 게시판 설정 > 리뷰작성 혜택 설정'의 설정에 따라 지급됩니다.</div>
                        </div>
                    </td>
                </tr>
                <tr class="js-mileage-tr-autoSet">
                    <th><div>마일리지 지급 조건</div></th>
                    <td>
                        <div>
                            리뷰 작성 글자수 최소 <?= $data['mileageAddminLimit'] ?>자 이상 입력 시에만 지급
                            <?php if ($data['authWriteExtra'] === 'buyer' && $data['mileageAddLimitGoodsPrice'] !== '') { ?>
                            <br/>구매 상품 가격 <?= $data['mileageAddLimitGoodsPrice']; ?>원 이상인 상품 리뷰 등록 시 마일리지 지급
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <tr class="js-mileage-tr-autoSet">
                    <th><div>마일리지 지급</div></th>
                    <td>
                        <div class="ncua-flex-column ncua-gap-8">
                            <div>
                                플러스리뷰 작성 시 <?= $data['mileageAmount']['review'] ?>원 지급
                            </div>
                            <div> 포토리뷰 작성 시 <?= $data['mileageAmount']['photo'] ?>원 추가지급
                            </div>
                            <div>상품별 첫 리뷰 작성 시 <?= $data['mileageAmount']['first'] ?>원 추가지급
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="js-mileage-tr-direct">
                    <th><div class="ncua-required">금액설정</div></th>
                    <td>
                        <div class="ncua-gap-8">
                            <span>(+)</span>
                            <div class="ncua-input ncua-input--xs">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" class="ncua-text-input-unit js-number" name="mileageValue" 
                                            value="" maxlength="8"/>
                                    </div>
                                </div>
                            </div>
                            원
                        </div>
                    </td>
                </tr>
                <tr class="js-mileage-tr-direct">
                    <th class="plus-review-milage-width-100"><div>회원안내</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <div class="member-guide">
                                <div class="member-guide-item">
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="guideSend[]" value="sms"/>
                                        </span>
                                        <span><span class="ncua-checkbox-field__text">SMS발송</span></span>
                                    </label>
                                    <a href="#member" class="btn-link js-link-sms-auto">상세설정 ></a>
                                </div>
                                <div class="member-guide-item">
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="guideSend[]" value="email"/>
                                        </span>
                                        <span><span class="ncua-checkbox-field__text">이메일발송</span></span>
                                    </label>
                                    <a href="#point" class="btn-link js-link-mail-auto">상세설정 ></a>
                                </div>
                            </div>
                            <div class="member-guide-notice">
                                <span class="plus-review-milage__bullet-list"> 
                                    SMS는 잔여포인트가 있어야 발송됩니다. (잔여포인트 :
                                    <span class="text-darkred bold"><?= number_format(gd_get_sms_point(), 1); ?></span>) 
                                </span>
                                <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary js-link-sms-charge">메시지 포인트 충전하기</button>

                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </article>
    <div class="modal-dialog__footer">
        <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray" onclick="layer_close()">취소</button>
        <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--primary js-btn-milage-act">처리</button>
    </div>
</form>
<script type="text/javascript">
    var reviewCount = <?=$mileageAmount['count']['review'] ?? 0;?>;
    var photoCount = <?=$mileageAmount['count']['photo'] ?? 0;?>;
    var firstCount = <?=$mileageAmount['count']['first'] ?? 0;?>;
    var reviewAmount = <?=$mileageAmount['amount']['review'] ?? 0;?>;
    var photoAmount = <?=$mileageAmount['amount']['photo'] ?? 0;?>;
    var firstAmount = <?=$mileageAmount['amount']['first'] ?? 0;?>;

    $(document).ready(function () {

        <?php if($mileageFl == 'direct'){ ?>
            $(':radio[name=milageAddWay]:radio[value=autoSet]').prop('disabled', true);
            $(':radio[name=milageAddWay]:radio[value=autoSet]').prop("checked", false);
            $(':radio[name=milageAddWay]:radio[value=direct]').prop("checked", true);
        <?php } else{ ?>
            $(':radio[name=milageAddWay]:radio[value=direct]').prop("checked", false);
            $(':radio[name=milageAddWay]:radio[value=autoSet]').prop("checked", true);
        <?php }?>

        $(':radio[name=milageAddWay]').bind('click', function () {
            if ($(this).val() == 'direct') {
                $('.js-mileage-tr-direct').show();
                $('.js-mileage-tr-autoSet').hide();
            }
            else {
                $('.js-mileage-tr-autoSet').show();
                $('.js-mileage-tr-direct').hide();
            }
        });
        $(':radio[name=milageAddWay]:checked').trigger('click');

        $('.js-btn-milage-act').click(function (e) {
            if ( $(':radio[name=milageAddWay]:checked').val() == 'autoSet') {
                if (reviewAmount + photoAmount + firstAmount > 0) {
                    var msg = '아래와 같이 마일리지가 지급됩니다. 계속하시겠습니까?';
                    msg += '<br/>게시글 ' + reviewCount + '건에 대해 플러스리뷰 작성 시 ' + reviewAmount + '원 지급';
                    msg += '<br/>게시글 ' + photoCount + '건에 대해 포토리뷰 작성 시 ' + photoAmount + '원 추가지급';
                    msg += '<br/>게시글 ' + firstCount + '건에 대해 상품별 첫 리뷰 작성 시 ' + firstAmount + '원 추가지급';
                    BootstrapDialog.confirm({
                        type: BootstrapDialog.TYPE_WARNING,
                        title: '마일리지 지급',
                        message: msg,
                        closable: false,
                        callback: function (result) {
                            if (result) {
                                giveMileage();
                            }
                        }
                    });
                } else {
                    NCDSAlert({ message: '선택된 모든 게시글에 지급될 마일리지가 없습니다.', iconType: 'error' });
                }
            } else {
                giveMileage();
            }
        });

    });

    var $js_link_sms_charge = $('.js-link-sms-charge');
    if ($js_link_sms_charge.length > 0) {
        $js_link_sms_charge.click(function (e) {
            window.open('<?php echo URI_ADMIN; ?>crm/popup_charge_message_points.php', 'sms_charge', 'width=1400, height=700, scrollbars=no');
        });
    }

    var $js_link_sms_auto = $('.js-link-sms-auto');
    var hash = '';
    if ($js_link_sms_auto.length > 0) {
        $js_link_sms_auto.click(function (e) {
            if ($(this).attr('href')) {
                hash = $(this).attr('href');
            }
            window.open('<?php echo URI_ADMIN; ?>crm/auto_send.php?popupMode=yes' + hash, 'sms_auto', 'width=1400, height=700, scrollbars=no');
        });
    }

    var $js_link_mail_auto = $('.js-link-mail-auto');
    var hash = '';
    if ($js_link_mail_auto.length > 0) {
        $js_link_mail_auto.click(function (e) {
            if ($(this).attr('href')) {
                hash = $(this).attr('href');
            }
            window.open('<?= URI_ADMIN ?>crm/mail_config_auto.php?popupMode=yes' + hash, 'mail_auto', 'width=1400, height=700, scrollbars=no');
        });
    }

    function giveMileage() {
        var sno = '<?=$sno?>';
        var snoArry = sno.split(",");

        if( $(':radio[name=milageAddWay]:checked').val() == 'autoSet'){

            parameter = { mode: 'milageAdd', sno: snoArry , milageAddWay: 'autoSet'};

        }else{

            var mileageValue = $('input[name=\'mileageValue\']').val();
            var guideSendArry = [];
            var dialogMessage;
            var title;
            if(mileageValue == ''){
                NCDSAlert({ message: '금액을 입력해주세요', iconType: 'error' });
                return;
            };

            $(':checkbox[name=\'guideSend[]\']:checked').each(function() {
                guideSendArry.push($(this).val());
            });

            parameter = {
                mode: 'milageAdd',
                sno: snoArry,
                milageAddWay: 'direct',
                mileageValue: mileageValue,
                guideSend: guideSendArry
            };

        }

        layer_close();
        var processingAlert = NCDSAlert({ message: '처리중', iconType: 'info' });

        ajax_with_layer('../board/plus_review_ps.php',parameter , function (data, textStatus, jqXHR) {
            layer_close();
            // 처리중 Alert 닫기
            if (processingAlert && typeof processingAlert.close === 'function') {
                processingAlert.close();
            }
            if (data[0]) {
                dialogMessage = "지급이 완료되었습니다. 처리된 내역을 확인하시겠습니까?";
                title = "마일리지 지급 처리 완료";

            } else {
                if(data[1]){
                    dialogMessage = data[1];
                }else{
                    dialogMessage = '마일리지 지급 처리 중 오류가 발생하였습니다.';
                }
                title = "마일리지 지급 오류";
            }
            NCDSConfirm({message: title, subMessage: dialogMessage, 
                btnText: {
                    confirmLabel: "처리내역 확인",
                    cancelLabel: "계속진행",
                },
                callback: function (result) {
                    if (result) {
                        top.location.href = '<?php echo URI_ADMIN; ?>member/member_batch_mileage_list.php';
                    } else {
                        top.location.reload();
                    }
                }
            });
        });
    }
    //-->
</script>
