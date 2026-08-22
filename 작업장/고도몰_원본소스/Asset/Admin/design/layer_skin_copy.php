<script type="text/javascript">
    <!--
    // validator 플러그인 직접 선언
    $.validator.addMethod( "alphanumeric", function( value, element ) {
        return this.optional( element ) || /^\w+$/i.test( value );
    }, "Letters, numbers, and underscores only please" );

    $(document).ready(function(){
        // TODO: validation 체크 부탁드립니다.
        // 스킨 복사 하기
        $("#frmSkin").validate({
            invalidHandler: function(event, validator) {
                if (validator.errorList.length > 0) {
                    NCDSAlert({
                        message: validator.errorList[0].message,
                        iconType: 'error'
                    });
                }
            },
            submitHandler: function (form) {
                $('.js-copy-btn').html('복사중입니다. 잠시만 기다려 주세요.');
                const loadingModal = window.spinnerModal({ message: '복사 중...' });
                loadingModal.open();
    
                // iframe load 이벤트 감지
                $('iframe[name="ifrmProcess"]').off('load').on('load', function() {
                  loadingModal.close();
                });
                
                form.target = 'ifrmProcess';
                form.submit();
            },
            dialog: false,
            rules: {
                copySkinCode: {
                    required: true,
                    alphanumeric: true
                },
                copySkin: "required"
            },
            messages: {
                copySkinCode: {
                    required: '스킨코드를 입력해 주세요.',
                    alphanumeric: '영문과 숫자, _ 만 입력해주세요.'
                }
            }
        });
    });
    //-->
</script>

<form id="frmSkin" name="frmSkin" action="design_skin_list_ps.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="mode" value="copySkin" />
    <input type="hidden" name="skinName" value="<?php echo $skinName;?>" />
    <input type="hidden" name="skinType" value="<?php echo $skinType;?>" />
    
    <div class="modal-dialog__content">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <colgroup>
                    <col width="144px"/>
                    <col/>
                </colgroup>
                <tbody>
                    <tr>
                        <th class="ncua-required"><div>스킨코드</div></th>
                        <td>
                            <div class="ncua-flex ncua-flex-column ncua-align-items-start">
                                <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="copySkinCode" value="<?php echo $skinName;?>_C" maxlength="20" placeholder="스킨코드를 입력하세요. (영문, 숫자, _ 만 입력)" style="ime-mode:disabled;"/>
                                        </div>
                                    </div>
                                    <span class="ncua-hint-text ncua-input__hint-text">스킨코드는 영문, 숫자, _ 만 입력 하세요. (특수문자, 공백, 한글 입력 금지)</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>스킨명</div></th>
                        <td>
                            <div class="ncua-flex ncua-flex-column ncua-align-items-start">
                                <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="copySkinName" placeholder="스킨명을 입력하세요." maxlength="20"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="board-template-write-footer modal-dialog__footer">
        <!-- TODO: 반응형 스킨 복사 submit 이후 작업 -->
        <div class="js-copy-btn">
            <input type="submit" value="스킨 복사하기" class="ncua-btn ncua-btn--sm ncua-btn--primary">
        </div>
    </div>
</form>
