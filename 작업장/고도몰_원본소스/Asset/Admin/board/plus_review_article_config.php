<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/plus-review-article-config.css')?>" rel="stylesheet"/>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/switch.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/textarea.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/select-member-combo-box.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/table-row-manager.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-multi-select/ncds-multi-select.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/charcount.js')?>"></script>

<article class="ncua-content">
    <div class="plus_review_article_config">
        <form id="frm" action="plus_review_ps.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="mode" value="save">
            <header class="page-header js-affix ncua-page-header">
                <h3><?php echo end($naviMenu->location); ?></h3>
                <div class="ncua-page-header__actions">
                    <button type="submit" class="ncua-btn ncua-btn--md ncua-btn--primary">저장</button>
                </div>
            </header>

            <!-- 기본 설정 -->
            <?php include $plusReviewBasicConfig ?>

            <!-- 리뷰작성 혜택 설정 -->
            <?php include $plusReviewBenefitConfig ?>

            <!-- 기능설정 -->
            <?php include $plusReviewFunctionConfig ?>

            <!-- 리뷰작성 설정 -->
            <?php include $plusReviewWriteConfig ?>

        </form>
    </div>
</article>

<!-- 기능 설정 > 추가정보 양식 설정 > 추가정보 양식 [추가] 템플릿 -->
<script id="templateAddForm" type="text/html">
    <tr data-row="<%=index%>" class="ncua-sort-item">
        <td>
            <div class="ncua-sort-item__checkbox">
                <div class="ncua-sort-item__checkbox-drag-icon"></div>
                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                        <input type="checkbox" name="addFormNo[]">
                    </span>
                </label>
            </div>
        </td>

        <td>
            <div>
                <input type="hidden" name="addForm[labelNumber][<%=index%>]" value="<%=maxNum%>">
                <div class="ncua-input ncua-input--xs ncua-input-full-width">
                    <div class="ncua-input__content">
                        <div class="ncua-input__field ncua-input__field--xs">
                            <input type="text"  name="addForm[labelName][<%=index%>]" placeholder="(예시) 키">
                        </div>
                    </div>
                </div>
            </div>
        </td>

        <td>
            <div class="ncua-select ncua-select--xs">
                <span class="ncua-select__content">
                    <select class="ncua-select__tag" name="addForm[inputType][<%=index%>]">
                        <option value="text">텍스트 입력</option>
                        <option value="select">셀렉트 박스</option>
                    </select>
                </span>
            </div>
        </td>

        <td>
            <div class="ncua-flex-column ncua-flex-gap">
                <ul class="js-label-value-list ncua-add-info-input-value">
                  
                    <li class="ncua-add-info-input-value__item">
                        <div class="ncua-input ncua-input--xs ncua-input-full-width">
                            <div class="ncua-input__content">
                                <div class="ncua-input__field ncua-input__field--xs">
                                    <input type="text" placeholder="(예시)  160~165" data-type="text" name="addForm[labelValue][<%=index%>][]">
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </td>
        <td>
            <div>
                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                    <input type="checkbox" name="addForm[useFl][<%=index%>]" value="y" checked>
                    </span>
                </label>
            </div>
        </td>
        <td>
            <div>
                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                        <input type="checkbox" name="addForm[requireFl][<%=index%>]" value="y" checked>
                    </span>
                </label>
            </div>
        </td>
    </tr>
</script>


<script type="text/javascript">
    var mileageUseConfig = '<?=$mileageUseFl;?>';
    var mileageAlertUseFl = false;
    function layer_register(parentLayerFormID, dataInputNm, dataFormID) {
        var addParam = {
            "parentFormID": parentLayerFormID,
            "dataInputNm": dataInputNm,
            "dataFormID": dataFormID,
        };
        layer_add_info('member_group', addParam);
    }

    const PROGRESS_CIRCLE_RADIUS = 45;
    const PROGRESS_CIRCLE_CIRCUMFERENCE = 2 * Math.PI * PROGRESS_CIRCLE_RADIUS;
    const progressMigrationTemplate = `
    <div class="ncua-progress-container-dimmed js-progress-migration" style="display: none;"></div>
    <div class="ncua-progress-container js-progress-migration" style="display: none;">
        <div>
            <p>상품후기게시판 통합중입니다.</p>
            <div class="sub-message">
                복사 중 브라우저를 닫거나 컴퓨터를 종료하면 파일이 정상적으로 복사되지 않아 오류가 발생할 수 있으니 주의 바랍니다.
            </div>
            
        </div>
        <div class="ncua-progress-circle ncua-progress-circle-xxs ncua-progress-circle-circle">
            <div class="ncua-progress-circle__content">
                <svg class="ncua-progress-circle__svg" viewBox="0 0 100 100">
                    <circle class="ncua-progress-circle__background" cx="50" cy="50" r="45" stroke-width="10" fill="none"></circle>
                    <circle
                        class="ncua-progress-circle__progress"
                        cx="50"
                        cy="50"
                        r="45"
                        stroke-width="10"
                        fill="none"
                        stroke-dasharray="${PROGRESS_CIRCLE_CIRCUMFERENCE}"
                        stroke-dashoffset="${PROGRESS_CIRCLE_CIRCUMFERENCE}"
                        transform="rotate(-90 50 50)"
                        stroke-linecap="round">
                    </circle>
                </svg>
                <div class="ncua-progress-circle__label-container">
                    <div id="progressView" class="ncua-progress-circle__label">0%</div>
                </div>
            </div>
        </div>
    </div>
    `;

    const totalMigratioCount = <?=$totalMigratioCount?>;
    $(document).ready(function () {
        // 글자수 카운트
        const charCountManager = createCharCountManager({
            targetClasses: ['add_form_search_title'],
        });
        charCountManager.init();

        // 상품후기 게시판 통합
        const hiddenProgressMigration = () => {
            $(".js-progress-migration").hide();
            $(".js-progress-migration").remove();
        }
        // [상품후기 게시판 통합] 버튼 클릭 이벤트
        $('.js-btn-migration').bind('click',function (e) {
           e.preventDefault();

            <?php if($diskLimit){ ?>
                NCDSAlert({message: '게시판 통합에 필요한 솔루션 디스크 용량이 부족합니다. 디스크 용량 추가 후 다시 시도해주세요.', iconType: 'error'});
            return true;
            <?php } ?>

            const checkMigration = '<?=$checkMigration?>';
            if (checkMigration == 'ready') {
                NCDSConfirm({message: '상품후기 게시판에 등록된 게시글을 불러옵니다. <br/> 계속하시겠습니까? 게시글이 많을 경우 처리 시간이 많이 소요될 수 있습니다.', callback: (data)=>{
                    if (!data) {
                        return false;
                    }
                    

                    if (totalMigratioCount === 0) {
                        NCDSAlert({message: '통합할 게시글이 없습니다.', iconType: 'error'});
                        hiddenProgressMigration();
                        return false;
                    }
                    
                    // 진행률 모달 표시
                    $('body').append(progressMigrationTemplate);
                    $(".js-progress-migration").show();

                    $.ajax({
                        method: 'post',
                        url: './plus_review_ps.php',
                        data: {'mode' : 'migration'},
                        dataType: 'json'
                    }).success(function (data) {
                        if (data.errorMsg) {
                            hiddenProgressMigration();
                            NCDSAlert({message:data.errorMsg, iconType: 'error'});
                            return;
                        }
                        // 마이그레이션 시작
                        startProgress();
                    }).error(function (e) {
                        hiddenProgressMigration();
                        NCDSAlert({message:e, iconType: 'error'});
                    });
                }});
                return;
            }
            
            if(checkMigration == 'completed'){
                NCDSAlert({message: '상품후기 게시판 통합이 모두 완료된 상태입니다.', iconType: 'success'});
                return;
            }


            NCDSAlert({message: '상품후기 게시판에 등록된 게시글이 없습니다.', iconType: 'error'});

            
        })

        // 리뷰작성 설정 > 최소 글자수 제한
        $('[name=minLimitLengthFl]').bind('click', function () {
            $('[name=minContentsLength]').prop('disabled', $(this).val() == 'n');
            $('[name=minContentsLength]').closest('.ncua-input').toggleClass('is-disabled', $(this).val() == 'n');
            $('[name=unMinLimitLengthFl]').prop('disabled', $(this).val() == 'n');
        })

        // 설정 > 포토리뷰 게시판 위젯 > 썸네일 사이즈
        $('[name="photoWidget[thumSizeType]"]').bind('change', function () {
            $('[name="photoWidget[thumWidth]"]').prop('disabled', $(this).val() == 'auto');
            $('[name="photoWidget[thumWidth]"]').closest('.ncua-input').toggleClass('is-disabled', $(this).val() == 'auto');
        })

         // 설정 > 포토리뷰 게시판 위젯 > 레아이웃, 전체리뷰 게시판 위젯 >  출력 리뷰 개수
        $('input[name^="photoWidget["], input[name="articleWidget[rows]"]').keyup(function (e) {
            
            var code = e.keyCode || e.which;
            var name = $(this).attr('name');
            if (code == '9') {
                return true;
            }
            $(this).trigger('blur');
            $(this).val($(this).val().replace(/[^0-9]/g, ""));
            $(this).trigger('focus');
            if ($(this).val() == '') {
                return false;
            }
            if (name.indexOf('thumWidth') > -1) {
                if ($(this).val() < 1) {
                    NCDSAlert({message: '1이상 설정하실 수 있습니다.', iconType: 'error'});
                    $(this).val('');
                    return false;
                }
            } else {
                if ($(this).val() > 20 || $(this).val() < 1) {
                    NCDSAlert({message: '1~20까지만 설정하실 수 있습니다.', iconType: 'error'});
                    $(this).val('');
                    return false;
                }
            }
        });

        // 설정 > 포토리뷰 게시판 위젯 > [위젯생성], 전체리뷰 게시판 위젯 > [위젯생성]
        $('.js-btn-widget').bind('click', function (e) {
            e.preventDefault();
            var mode = $(this).data('mode');
            var title = mode === 'article' ? '전체리뷰' : '포토리뷰';
            var layerSize = BootstrapDialog.SIZE_WIDE;
            var params, rows, template;
            if (_.isUndefined(mode) || _.isEmpty(mode)) {
                mode = 'photo';
            }

            switch (mode) {
                case 'photo':
                    var cols = $('[name="photoWidget[cols]"]').val();
                    rows = $('[name="photoWidget[rows]"]').val();

                    if ((cols > 20 || cols == 0 || _.isEmpty(cols)) || (rows > 20 || rows == 0 || _.isEmpty(cols))) {
                        NCDSAlert({message: '1~20까지만 설정하실 수 있습니다.', iconType: 'error'});
                        $('[name="photoWidget[cols]"]').val('');
                        $('[name="photoWidget[rows]"]').val('');
                        return;
                    }

                    var thumSizeType = $('[name="photoWidget[thumSizeType]"]:checked').val();
                    var thumWidth = $('[name="photoWidget[thumWidth]"]').val();

                    if ($('[name="photoWidget[thumSizeType]"]:checked').length < 1) {
                        NCDSAlert({message: '썸네일 사이즈 타입을 선택해주세요.', iconType: 'error'});
                        return;
                    }

                    if ($('[name="photoWidget[thumSizeType]"][value=menual]').is(':checked')) {
                        if (_.isEmpty(thumWidth) || thumWidth == 0) {
                            NCDSAlert({message: '수동설정 사이즈를 1이상 입력해주세요.', iconType: 'error'});
                            return;
                        }
                    }
                    params = {'mode': mode, 'cols': cols, 'rows': rows, 'thumSizeType': thumSizeType, 'thumWidth': thumWidth};
                    break;
                case 'article':
                    rows = $('[name="articleWidget[rows]"]').val();
                    template = $('[name="articleWidget[template]"]:checked').val();
                    if (rows > 20 || rows == 0 || _.isEmpty(rows)) {
                        NCDSAlert({message: '1~20까지만 설정하실 수 있습니다.', iconType: 'error'});
                        $('[name="articleWidget[rows]"]').val('');
                        return;
                    }
                    layerSize = BootstrapDialog.SIZE_WIDE_XLARGE;
                    params = {'mode': mode, 'template': template, 'rows': rows};
                    break;
            }

            $.ajax({
                method: 'get',
                url: 'plus_review_widget_preview.php',
                data: params,
                dataType: 'text'
            }).success(function (data) {
                // 레이어 모달 팝업 띄우기
                BootstrapDialog.show({
                    title: title + ' 게시판 위젯생성',
                    size: layerSize,
                    message: data,
                    cssClass: 'ncds-modal',
                    closable: true
                });
            }).error(function (e) {
                NCDSAlert({message:e, iconType: 'error'});
            });
        })


        try {
            /**
             * 회원등급 ComboBox 초기화
             */
            const initMemberGroupComboBoxes = () => {
                if (typeof initMemberGroupComboBox === 'undefined') {
                    setTimeout(initMemberGroupComboBoxes, 100);
                    return;
                }

                const memberGroupComboBoxes = {};

                const comboBoxAuthWriteMember = initMemberGroupComboBox({
                    comboboxId: 'layer_member_group_auth_write_combobox',
                    parentLayerId: 'member_groupLayer_auth_write',
                    dataInputNm: 'authWriteGroup',
                    dataFormID: 'info_member_auth_write_member',
                    pageCountVar: 'comboBoxMemberGroupAuthWriteMemberPageCount',
                    keywordVar: 'comboBoxMemberGroupAuthWriteMemberKeyword',
                    labelText: '선택된 회원등급 :',
                    tagClass: 'auth-write-member-tag ncua-select-group-tag',
                });

                if (comboBoxAuthWriteMember) {
                    memberGroupComboBoxes['layer_member_group_auth_write_combobox'] = comboBoxAuthWriteMember;
                }

                const comboBoxAuthMemoMember = initMemberGroupComboBox({
                    comboboxId: 'layer_member_group_auth_memo_combobox',
                    parentLayerId: 'member_groupLayer_auth_memo',
                    dataInputNm: 'authMemoWriteGroup',
                    dataFormID: 'info_member_auth_memo_member',
                    pageCountVar: 'comboBoxMemberGroupAuthMemoMemberPageCount',
                    keywordVar: 'comboBoxMemberGroupAuthMemoMemberKeyword',
                    labelText: '선택된 회원등급 :',
                    tagClass: 'auth-memo-member-tag ncua-select-group-tag',
                });

                if (comboBoxAuthMemoMember) {
                    memberGroupComboBoxes['layer_member_group_auth_memo_combobox'] = comboBoxAuthMemoMember;
                }
            }

            initMemberGroupComboBoxes();
        } catch {
            // ignore
        }

       
        const initTableRowManager = (retryCount) => {
            retryCount = retryCount || 0;
            const maxRetries = 100; // 최대 5초 대기 (50ms * 100)
            
            if (typeof window.NcuaTableRowManagerCheckbox === 'undefined') {
                if (retryCount >= maxRetries) {
                    console.error('TableRowManager: NcuaTableRowManagerCheckbox를 로드할 수 없습니다. 스크립트 파일이 올바르게 로드되었는지 확인해주세요.');
                    return;
                }
                setTimeout(function() {
                    initTableRowManager(retryCount + 1);
                }, 50);
                return;
            }
            
            // 테이블 tbody 요소 확인
            const tbodyElement = document.querySelector('#addFormTable tbody');
            if (!tbodyElement) {
                console.error('TableRowManager: #addFormTable tbody 요소를 찾을 수 없습니다.');
                return;
            }
            
            // 방향 버튼을 추가할 타겟 요소 확인
            const directionTarget = document.querySelector(".add-info-order-buttons");
            if (!directionTarget) {
                console.error('TableRowManager: .ncua-search-result__actions 요소를 찾을 수 없습니다.');
            }
            
            const manager = new NcuaTableRowManagerCheckbox(tbodyElement, {
                multipleSelection: true,
                direction: {
                    enabled: true,
                    target: directionTarget,
                },
                selector: {
                    checkTarget: "td:first-child",
                },
                events: {
                    init: function () {
                        this.initSortable({
                            items: this.options.selector.item,
                            placeholder: 'ncua-sort-placeholder',
                            cursor: "grab",
                            update: function () {
                                manager.updateDirectionButtons();
                            },
                            stop: function () {
                                manager.updateDirectionButtons();
                            },
                        });
                    },
                    direction: function(direction) {
                        const sortButton = {
                            'top': -100,
                            'up': -1,
                            'down': 1,
                            'bottom': 100
                        };
                    
                        this.moveRow(sortButton[direction]);
                    },
                },
            });
            
            // js-checkall 체크박스의 change 이벤트 감지
            const checkAllCheckbox = document.querySelector('#addFormTable .js-checkall');
            if (checkAllCheckbox) {
                checkAllCheckbox.addEventListener('change', function() {
                    if (!this.checked) {
                        manager.clearSelection();
                    } else {
                        manager.selectAll();
                    }
                });
            }
        }
        
        initTableRowManager();


        // 설정 > 리뷰작성 혜택 설정 > 마일리지 사용유무
        if ($('input[name="mileageFl"]:checked').val() === 'n') {
            mileageAlertUseFl = true;
        }

        $(':radio[name=mileageFl]').bind('click', function () {
            if ($(this).val() == 'y') {
                if (mileageAlertUseFl) {
                    $('.js-miliage-use-tr').show();
                    if (mileageUseConfig === 'n') {
                        // TODO NCDS Confirm 으로 변경해야할 것으로 보여짐. 다른 설정 영향있는 듯.
                        BootstrapDialog.show({
                            title: '경고',
                            message: '마일리지 지급을 위해서는 마일리지 기본설정이 "사용함"이어야 이용 가능합니다.<br/>"고객 > 마일리지/예치금 관리 > 마일리지 기본 설정"을 "사용함"으로 설정 후 이용해 주세요.',
                            buttons: [{
                                label: '확인',
                                cssClass: 'btn-black',
                                hotkey: 13,
                                size: BootstrapDialog.SIZE_LARGE,
                                action: function (dialog) {
                                    dialog.close();
                                    $(':radio[name="mileageFl"][value="n"]').trigger('click');
                                }
                            }],
                        });
                    }
                } else {
                    mileageAlertUseFl = true;
                }
            } else {
                $('.js-miliage-use-tr').hide();
            }
        });

        // 기능 설정 > 추가정보 양식 설정 > 입력형태 : 셀렉트박스
        $('body').on('change', 'select[name^="addForm[inputType]"]', function () {
            if (($(this).val() == 'select')) {
                const addBtn = `<button type="button" class="ncua-btn ncua-btn--xs  ncua-btn--secondary-gray js-btn-labal-value-add">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"></path></svg>
                    추가
                </button>`;
                $(this).closest('tr').find('[name^="addForm[labelValue]"]').data('type', 'select').attr('placeholder', '(예시) 160~165')
                $(this).closest('tr').find('.ncua-add-info-input-value').after(addBtn);
            }
            else {
                $(this).closest('tr').find('[name^="addForm[labelValue]"]').data('type', 'input').attr('placeholder', '(예시) 160~165');
                $(this).closest('tr').find('.js-label-value-list>li:not(:first)').remove();
                $(this).closest('tr').find('.js-btn-labal-value-add').remove();
            }
        });

        $('body').off('keypress').on('keypress', '[name^="addForm[labelValue]"]', function (e) {
            var type = $(this).data('type');
            if (e.which == 13) {
                $(this).closest('tr').find('.js-btn-labal-value-add').trigger('click');
                $(this).closest('tr').find('[name^="addForm[labelValue]"]:last').focus();
                e.preventDefault();
                return false
            }
        });
        
        // 기능 설정 > 추가정보 양식 설정 > 입력형태 : 셀렉트박스 > 입력값 [추가]
        $('body').on('click', '.js-btn-labal-value-add', function () {
            const row = $(this).closest('tr').data('row');
            const removeBtn = `<div class="js-btn-labal-remove-li ncua-remove-icon-button"></div>`;
            const addFormRow = 
            `<li class="ncua-add-info-input-value__item">
                <div class="ncua-input ncua-input--xs ncua-input-full-width">
                    <div class="ncua-input__content">
                        <div class="ncua-input__field ncua-input__field--xs">
                            <input type="text" name="addForm[labelValue][${row}][]" placeholder="(예시) 160~165">
                        </div>
                    </div>
                </div>
                ${removeBtn}
            </li>`;
            $(this).closest('tr').find('ul:last').append(addFormRow);
        })
        

        // 기능 설정 > 추가정보 양식 설정 > 추가정보 양식 [추가]
        $('body').on('click', '.js-btn-form-add', function () {
            var templateAddForm = _.template($('#templateAddForm').html());
            if ($('#addFormTable tbody tr').length > 10) {
                NCDSAlert({message: '최대 10개까지 등록 가능합니다.', iconType: 'error'});
                return;
            }

            var maxIndex = 0;
            $('#addFormTable tbody tr').each(function () {
                var index = $(this).data('row');
                if (maxIndex < index) {
                    maxIndex = index;
                }
            });

            var $labelMaxNum = $('input[name="labelMaxNum"]');
            $labelMaxNum.val( Number($labelMaxNum.val()) + 1 );
            var $newRow = $(templateAddForm({index: maxIndex + 1,maxNum: $labelMaxNum.val()}));
            $('#addFormTable tbody:last').append($newRow);
            
            // 새로 추가된 행의 입력형태가 셀렉트박스인 경우 처리
            var $selectType = $newRow.find('select[name^="addForm[inputType]"]');
            if ($selectType.val() === 'select') {
                $selectType.trigger('change');
            }
        })
        
        // 기능 설정 > 추가정보 입력
        $(':radio[name=addFormFl]').bind('click', function () {
            if ($(this).val() == 'y') {
                $('.js-add-form-tr').show();
                $('input[name=addFormSearchFl]').attr( 'disabled', false );
                $('input[name=addFormSearchFl]').closest('.ncua-switch').removeClass('ncua-switch--disabled');
            }
            else {
                $('.js-add-form-tr').hide();
                $('input[name=addFormSearchFl]').attr( 'disabled', true );
                $('input[name=addFormSearchFl]').closest('.ncua-switch').addClass('ncua-switch--disabled');
                $("input:radio[name='addFormSearchFl']:radio[value='n']").prop('checked', true);
            }
        })

        // 기능 설정 > 추가정보 양식 설정 > 입력형태 : 셀렉트 박스 > 입력값 [삭제]
        $('body').on('click', '.js-btn-labal-remove-li', function () {
            $(this).closest('li').remove();
        })

        // 기능 설정 > 추가정보 양식 설정 > [선택 삭제]
        $('body').on('click', '.js-btn-remove-tr', function () {
            const deleteItem = $('input[name="addFormNo[]"]:checked');
            if (deleteItem.length < 1) {
                NCDSAlert({message: '삭제할 항목을 선택해주세요.', iconType: 'error'});
                return;
            }
            deleteItem.each(function () {
                $(this).closest('tr').remove();
            });
        })


        // 설정 > 사용여부
        $('input[name=useFl]').bind('click', function () {
            if ($(this).val() == 'y') {
                NCDSAlert({message: '사용함 설정 시 기존 "상품후기" 게시판과 함께 노출됩니다. <br> 상품후기 게시판을 사용안함으로 설정해주세요.', iconType: 'error'});
            }
        })

        // 폼검증
        $("#frm").validate({
            ignore: ':hidden',
            dialog: false,
            invalidHandler: function(event, validator) {
                if (validator.errorList.length > 0) {
                    NCDSAlert({
                        message: validator.errorList[0].message,
                        iconType: 'error'
                    });
                }
            },
            submitHandler: function (form) {
                form.target = 'ifrmProcess';
                // 기능설정 > 추가정보 입력
                if ($(':radio[name=addFormFl]:checked').val() == 'y') {
                    if ($('input[name^="addForm[labelName]"]').length < 1) {
                        NCDSAlert({message: '추가정보 양식을 입력해주세요.', iconType: 'error'});
                        return;
                    }
                }

                var minLength = 1;
                var maxLength = 2000;
                var minLimitLength = $('[name=minContentsLength]').val();
                // 리뷰작성 설정 > 최소 글자수 제한
                if ($('[name=minLimitLengthFl]:checked').val() == 'y') {
                    if (minLimitLength == 0) {
                        NCDSAlert({message: '최소 1자 이상은 입력하셔야 합니다.', iconType: 'error'});
                        return;
                    }

                    if (minLimitLength < minLength) {
                        NCDSAlert({message: '최소 ' + minLength + '자 이상은 입력하셔야 합니다.', iconType: 'error'});
                        return;
                    }
                    if (minLimitLength > maxLength) {
                        NCDSAlert({message: '최대 ' + maxLength + '자 까지 입력가능합니다.', iconType: 'error'});
                        return;
                    }
                }

                // 설정 > 쓰기권한 설정
                if ($('input[name="authWrite"]:checked').val() === 'group' && $('[id^="authWriteGroup_"]').length < 1) {
                    NCDSAlert({message: '쓰기권한의 특정회원등급이 선택되지 않았습니다.', iconType: 'error'});
                    return false;
                }

                // 설정 > 댓글권한 설정
                if ($('input[name="authMemoWrite"]:checked').val() === 'group' && $('[id^="authMemoWriteGroup_"]').length < 1) {
                    NCDSAlert({message: '댓글권한의 특정회원등급이 선택되지 않았습니다.', iconType: 'error'});
                    return false;
                }

                form.submit();
            },
            rules: {
                minContentsLength: {
                    required: function () {
                        return $('[name=minLimitLengthFl][value=y]').is(':checked');
                    }
                },
                articlePagetCnt : {
                    min : 1,
                    number : true,
                },
                'mileageAmount[review]': {
                    required: function () {
                        return $('[name=mileageFl][value=y]').is(':checked');
                    }
                },
                'mileageAmount[photo]': {
                    required: function () {
                        return $('[name=mileageFl][value=y]').is(':checked');
                    }
                },
                'mileageAmount[first]': {
                    required: function () {
                        return $('[name=mileageFl][value=y]').is(':checked');
                    }
                },
                'mileageAmountLimit[review]': {
                    required: function () {
                        return $('[name=mileageAmountLimitFl][value=y]').is(':checked');
                    }
                },
                bdNewFl: 'required',
                uploadMaxSize: 'required'
            },
            messages: {
                minContentsLength: {
                    required: '최소 1자 이상은 입력하셔야 합니다.',
                },
                articlePagetCnt : {
                    min : '[전체리뷰 게시판 페이지당 게시물 수] 입력항목에 1 이상 입력해주시기 바랍니다.',
                },
                'mileageAmount[review]': {
                    required: '플러스리뷰 작성 마일리지 지급 설정 항목은 필수입니다.'
                },
                'mileageAmount[photo]': {
                    required: '포토리뷰 작성 마일리지 지급 설정 항목은 필수입니다.'
                },
                'mileageAmount[first]': {
                    required: '상품별 첫 리뷰 작성 마일리지 지급 설정 항목은 필수입니다.'
                },
                'mileageAmountLimit[review]': {
                    required: '최대 지급 마일리지 제한 금액을 입력해주세요.'
                },
                bdNewFl: {
                    required: 'NEW아이콘 효력 항목은 필수입니다.'
                },
                uploadMaxSize: {
                    required: '첨부이미지 최대크기 항목은 필수입니다.'
                }
            }
        });


        /**
         * 마이그레이션 완료 처리
         */
        const handleMigrationComplete = () => {
            updateProgress(totalMigratioCount, totalMigratioCount);
            setTimeout(function() {
                hiddenProgressMigration();
                NCDSAlert({message: '완료되었습니다.', iconType: 'success'});
            }, 500);
        };

        /**
         * 마이그레이션 에러 처리
         */
        const handleMigrationError = (errorData) => {
            hiddenProgressMigration();
            const errorMessage = errorData.message || errorData.error?.message || '처리 중 오류가 발생했습니다.';
            NCDSAlert({message: errorMessage, iconType: 'error'});
        };

        /**
         * 마이그레이션 진행 처리
         */
        const handleMigrationProgress = (migrationCount) => {
            updateProgress(migrationCount, totalMigratioCount);

            if (migrationCount === 0) {
                handleMigrationComplete();
            } else {
                // 진행률이 남아있으면 계속 진행
                setTimeout(function() {
                    startProgress();
                }, 500);
            }
        };

        /**
         * 마이그레이션 진행 시작
         */
        function startProgress() {
            $.ajax({
                method: 'post',
                url: './plus_review_ps.php',
                async: false,
                data: {
                    mode: 'applyMigration',
                },
                dataType: 'json'
            })
            .success(function(data) {
                const ERROR_CODE = {
                    SUCCESS: 0,
                    ERROR: 1
                };

                if (data.error == ERROR_CODE.SUCCESS) {
                    const migrationCount = Number(data.migrationCount);
                    handleMigrationProgress(migrationCount);

                    return;
                } 
                
                handleMigrationError(data);
            })
            .error(function(e) {
                handleMigrationError(e);
            });
        }

        /**
         * 프로그레스 업데이트
         *
         * @param data
         */
        function updateProgress(current, total) {
            var percentage = parseInt(100 * (current / total));
            if(percentage > 100) percentage = 100;

            $(".ncua-progress-circle__label").text(percentage + "%");

            const progressPercentage = parseFloat(percentage) || 0;
            const strokeDashoffset = PROGRESS_CIRCLE_CIRCUMFERENCE * (1 - progressPercentage / 100);
            const progressCircle = $('.ncua-progress-circle__progress');
            
            if (progressCircle.length > 0) {
                progressCircle.attr('stroke-dasharray', PROGRESS_CIRCLE_CIRCUMFERENCE);
                progressCircle.attr('stroke-dashoffset', strokeDashoffset);
            }
        }

        // 리뷰 작성 설정 > 리뷰 승인 설정
        $(':radio[name=viewPermissionFl]').bind('click', function () {
            if ($(this).val() == 'd') {
                $('.js-review-view-tr').show();
            }
            else {
                $('.js-review-view-tr').hide();
            }
        });

        // 설정 > 리뷰 작성 예외 상품
        $('input[name="exceptGoodsFl"]').on('click', function() {
            if ($(this).val() === 'y') {
                $('#configExceptGoods').show();
            } else {
                $('#configExceptGoods').hide();
            }
        });

        // 설정 > 예외상품 설정 [선택삭제]
        $('.js-delete-exceptGoods').on('click', function () {
            const target = $('input[name="exceptGoodsChk"]:checked');
            const parent = $(this).closest('.ncua-search-result__content');

            if (target.length === 0) {
                NCDSAlert({message: '선택한 상품이 없습니다', iconType: 'error'});
                return false;
            }

            const tbodyID = parent.find('tbody').prop('id');
            
            // 삭제
            target.each(function() {
                target.closest('tr').remove();
            });

            if ($('#' + tbodyID).find('tr').length === 0) {
                $('#' + tbodyID).append('<tr class="tr-no-data"><td colspan="4" class="no-data"><div>추가된 예외상품이 없습니다.</div></td></tr>');
            } else { // 번호 순서 조정            
                $.each($('#' + tbodyID).find('.number'), function (index) {
                    $(this).html(index + 1);
                });
            }

            parent.find('.js-checkall').prop('checked', false);

        });

        // 전체리뷰 게시판 위젯 템플릿 [미리보기] 
        $('.js-btn-preview-template').mouseover(function () {
            var targetClass = $(this).data('target');
            $('.' + targetClass).show();
        }).mouseout(function () {
            var targetClass = $(this).data('target');
            $('.' + targetClass).hide();
        });

        // 특정회원등급 외 선택
        $('input[name="authWrite"], input[name="authMemoWrite"]').on('click', function () {
            if ($(this).val() !== 'group') {
                if ($(this).attr('name') === 'authWrite') {
                    if ($('#authWriteGroup').length > 0) {
                        $('#authWriteGroup').html('').removeClass('active');
                    }
                } else {
                    if ($('#authMemoWriteGroup').length > 0) {
                        $('#authMemoWriteGroup').html('').removeClass('active');
                    }
                }
            }
        });

        // 설정 > 쓰기권한 추가 기준
        $('input[name="authWriteExtra"]').on('click', function() {
            if ($(this).val() === 'all') {
                $('.ncua-mileage-limit-price').addClass('display-none');
                $('#authWriteStatus').prop('disabled', true);
                $('#authWriteStatusDuration').prop('disabled', true);
                $('input[name="authWriteStatusDurationFl"]').prop('disabled', true);
            } else {
                $('.ncua-mileage-limit-price').removeClass('display-none');
                $('#authWriteStatus').prop('disabled', false);
                $('#authWriteStatusDuration').prop('disabled', false);
                $('input[name="authWriteStatusDurationFl"]').prop('disabled', false);
            }
        });

        // textarea input destructive 처리
        function checkReviewBenefitLength(textarea) {
            const ncuaTextarea = $(textarea).closest('.ncua-input--textarea');
            const textLength = $(textarea).val().length;
            const maxLength = parseInt($(textarea).attr('maxlength'));

            if (textLength >= maxLength) {
                ncuaTextarea.addClass('destructive');
            } else {
                ncuaTextarea.removeClass('destructive');
            }
        }
        // 리뷰작성 혜택 설정 > 리뷰혜택 안내문구, 기능설정 > 리뷰작성 안내 문구 설정
        $('textarea[name="reviewBenefitInfo"], textarea[name="reviewPlaceHolder"]').on('input', function() {
            checkReviewBenefitLength(this);
        }).on('paste', function() {
            checkReviewBenefitLength(this);
        })

        // 리뷰작성 혜택 설정 > 마일리지 지급 방법
        $('input[name="mileageAddFl"]').on('click', function() {
            updateMileageAddDuration();
        });

        // 리뷰작성 혜택 설정 > 마일리지 지급 설정 : 최대 마일리지 지급 설정 노출
        $('select[name="mileageUnit[review]"]').on('change', function() {
            if ($(this).val() === 'percent') {
                $('.mileage_unit_percent').css('display', 'flex');
            } else {
                $('.mileage_unit_percent').css('display', 'none');
            }
        });

        // 쓰기권한 추가 기준 & 중복작성 제한 조건에 따른 마일리지 중복 지급 제한 설정
        $('input[name="authWriteExtra"], input[name="orderDuplicateIgnoreFl"]').on('click', function() {
            if ($('input[name="authWriteExtra"]:checked').val() === 'all' && $('input[name="orderDuplicateIgnoreFl"]:checked').val() === 'n') {
                $('input:radio[name="mileageDuplicateIgnoreFl"]:input[value="n"]').prop("checked", true);
                $('input:radio[name="mileageDuplicateIgnoreFl"]:input[value="y"]').prop('disabled', true);
            } else {
                $('input:radio[name="mileageDuplicateIgnoreFl"]:input[value="y"]').prop('disabled', false);
            }
        });

        // 리뷰작성 혜택 설정 > 마일리지 지급 설정 : 최대 지급 마일리지 제한 설정
        $('input[name="mileageAmountLimitFl"]').on('click', function() {
            updateMileageAmountLimit();
        });

        configInit();
    });

    function updateMileageAddDuration() {
        const selectedValue = $('input[name="mileageAddFl"]:checked').val();
        if (selectedValue === 'a') { //리뷰 등록 시 지급
            $('.ncua-select select[name="mileageAddDuration"]').prop('disabled', false);
        } else { //수동지급
            $('.ncua-select select[name="mileageAddDuration"]').prop('disabled', true);
        }
    }

    function updateMileageAmountLimit() {
        const mileageAmountLimitFl = $('input[name="mileageAmountLimitFl"]:checked').val();
            if (mileageAmountLimitFl === 'y') {
                $('input[name="mileageAmountLimit[review]"]').prop('readonly', false);
                $('input[name="mileageAmountLimit[review]"]').closest('.ncua-input').removeClass('is-disabled');
            } else {
                $('input[name="mileageAmountLimit[review]"]').prop('readonly', true);
                $('input[name="mileageAmountLimit[review]"]').closest('.ncua-input').addClass('is-disabled');
            }
    }

    // 설정 초기화
    function configInit() {
        $('[name=minLimitLengthFl]:checked').trigger('click');
        $('[name="photoWidget[thumSizeType]"]:checked').trigger('click');
        $(':radio[name=mileageFl]:checked').trigger('click');
        $(':radio[name=addFormFl]:checked').trigger('click');
        $(':radio[name=viewPermissionFl]:checked').trigger('click');
        $('input[name="exceptGoodsFl"]:checked').trigger('click');
        $('input[name="authWriteExtra"]:checked').trigger('click');
        $('select[name="mileageUnit[review]"]').trigger('change');
        $('input[name="authWriteExtra"]:checked, input[name="orderDuplicateIgnoreFl"]:checked').trigger('click');
        if ($('input[name="mileageAmountLimitFl"]').is(':checked')) {
            $('input[name="mileageAmountLimit[review]"]').prop('readonly', false);
        }
        updateMileageAddDuration();
        updateMileageAmountLimit();
    }

    /**
     * 구매 상품 범위 등록 / 예외 등록 Ajax layer
     *
     * @param typeStr
     * @param modeStr
     * @param isDisabled
     */
    function layer_except_register(typeStr, modeStr, isDisabled) {
        var layerFormID = 'exceptGoodsForm';
        var mode = 'plusReview';
        var typeStrId = typeStr.substr(0, 1).toUpperCase() + typeStr.substr(1);
        var parentFormID, dataFormID, dataInputNm, layerTitle;

        if (typeof modeStr === 'undefined') {
            parentFormID = 'present' + typeStrId;
            dataFormID = 'id' + typeStrId;
            dataInputNm = 'present' + typeStrId;
            layerTitle = '조건 - ';
        } else {
            parentFormID = 'except' + typeStrId;
            dataFormID = 'idExcept' + typeStrId;
            dataInputNm = 'except' + typeStrId;
            layerTitle = '예외 상품 선택';
        }

        var addParam = {
            "mode": mode,
            "layerFormID": layerFormID,
            "parentFormID": parentFormID,
            "dataFormID": dataFormID,
            "dataInputNm": dataInputNm,
            "layerTitle": layerTitle
        };

        // 레이어 창
        if (typeStr === 'goods') {
            addParam['scmFl'] = $('input[name="scmFl"]:checked').val();
            addParam['scmNo'] = $('input[name="scmNo"]').val();
        }

        if (!_.isUndefined(isDisabled) && isDisabled === true) {
            addParam.disabled = 'disabled';
        }

        layer_add_info(typeStr, addParam);
    }
    //-->
</script>

<script type="text/javascript">
    const code = '251112002';
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
        });
    }
</script>
