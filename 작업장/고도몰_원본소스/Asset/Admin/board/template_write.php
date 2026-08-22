<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/template-write.css')?>" rel="stylesheet"/>
<script type="text/javascript" src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-editor/0.0/ncds-editor.js')?>"></script>
<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/ncds-editor/0.0/ncds-editor.css')?>" rel="stylesheet"/>
<script type="text/javascript" src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/bootstrap-modal-event-controller.js')?>"></script>
<script type="text/javascript" src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/froala-editor-modal-handler.js')?>"></script>

<?php if($req['mode'] == 'popup') {?>
<div class="page-header js-affix <?php if($req['mode'] == 'popup') {?>board-template-write-header<?php }?>">
    <h3 class="<?php if($req['mode'] == 'popup') {?>board-template-write-header-title<?php }?>">게시글 양식 등록</h3>
    <button class="close" onclick="self.close();">×</button>
</div>
<?php }?>
<?php include $templateWriteForm ?>
<script type="text/javascript">
    <!--
    // Froala Editor Modal Handler 초기화
    if (typeof window.FroalaEditorModalHandler !== 'undefined') {
        window.FroalaEditorModalHandler.init({
            targetSelector: '#ncds-board-template-write-form'
        });
    }

    const mode = '<?=$req['mode']?>';

    $(document).ready(function () {
        // Form Process

        $("#ncds-board-template-write-form").validate({
            dialog : false,
            submitHandler: function (form) {
                form.target = 'ifrmProcess';
                if(mode == 'popup') {
                    $.ajax({
                        method: "POST",
                        url: "./template_ps.php",
                        data: $("#ncds-board-template-write-form").serialize(),
                        dataType: 'json'
                    }).success(function (result) {
                        if (result['result'] == 'ok') {
                            $("#bdTemplateSno option",opener.document).remove();
                            if($("select[id^=bdCategoryTemplateSno]",opener.document)) $("select[id^=bdCategoryTemplateSno]",opener.document).empty();
                            var target = result.data;
                            for (var k in target){
                                if (typeof target[k] !== 'function') {
                                    $("#bdTemplateSno",opener.document).append("<option value='"+k+"'>"+target[k]+"</option>");
                                    if($("select[id^=bdCategoryTemplateSno]",opener.document)) $("select[id^=bdCategoryTemplateSno]",opener.document).append("<option value='"+k+"'>"+target[k]+"</option>");
                                }
                            }
                            $("#bdTemplateSno",opener.document).val(result.selected);
                            opener.$("#bdTemplateSno option:selected").trigger('change');
                            self.close();
                        }
                        else {
                            NCDSAlert({message: result.msg});
                        }
                    }).error(function (e) {
                        NCDSAlert({message: e.responseText});
                    });
                }
                else {
                    form.submit();
                }
            },
            invalidHandler: function(event, {errorList}) {
                NCDSAlert({message: errorList?.[0]?.message, iconType: 'error'});
            },
            rules: {
                subject: 'required',
                contents: {
                    required: function (textarea) {
                        const editor = document.querySelector(`#${textarea.id}`);

                        return editor.ncdsEditor?.getHTML().length === 0;
                    }
                }
            },
            messages: {
                subject: {
                    required: '제목을 입력해 주세요.'
                },
                contents: {
                    required: '내용을 입력해 주세요.'
                }
            }
        })

        $(".js-template-write-cancel").on("click", function () {
            if (window.opener) {
                window.close();
                return;
            }

            layer_close();

        });
    });
    //-->
</script>
