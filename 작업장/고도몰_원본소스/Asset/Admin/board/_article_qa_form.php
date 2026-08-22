<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/ncds-editor/'. NCDS_EDITOR_VERSION .'/ncds-editor.css')?>" rel="stylesheet"/>
<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/article-qa-form.css')?>" rel="stylesheet"/>
<script defer type="text/javascript" src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-editor/'. NCDS_EDITOR_VERSION .'/ncds-editor.js')?>"></script>

<article class="ncua-content article-qa-form">
    <form method="post" id="frmWrite" action="article_ps.php">
        <header class="page-header ncua-page-header js-affix">
            <h3 class="<?php if (!gd_is_provider()) { ?>ncua-help-manual<?php } ?>"><button type="button" class="ncua-btn ncua-btn--md only-icon ncua-btn--secondary-gray ncua-history-back js-btn-back">뒤로가기</button><?php echo end($naviMenu->location); ?></h3>
            <div class="ncua-page-header__actions">
                <?php if($req['popupMode'] !='yes') { // CRM 팝업모드가 아닐 경우 ?>
                <button type="button" class="ncua-btn ncua-btn--md ncua-btn--secondary-gray" onclick="goList('<?=$adminList;?>');">목록</button>
                <?php } ?>
                <button type="submit" class="ncua-btn ncua-btn--md ncua-btn--primary">등록</button>
            </div>
        </header>
        <?php include '_article_detail.php' ?>
        <?php include $articleReply ?>
    </form>
</article>
<script>
    var bdId = '<?=$req['bdId']?>';
    var replyStatusComplete='<?=$replyStatusComplete?>';
    $(document).ready(function() {
        const editor = document.querySelector(`[data-godo-editor="article-reply-editor"]`);
        $('#frmWrite').find('[name=queryString]').val(getUrlVars());

        $("#frmWrite").validate({
            submitHandler: function (form) {
                form.target = 'ifrmProcess';
                form.submit();
            },
        });

        $('#bdTemplateSno').bind('change', async function() {
            if($(this).val() == '' || $(this).val() == 0 ) {
                return false;
            }

            if (editor.ncdsEditor.getHTML().length > 0) {
                if (!(await NCDSConfirm({ message: '본문이 삭제되고 템플릿내용이 삽입됩니다. 진행하시겠습니까?' }))) {
                    return false;
                }
            }

            $.ajax({
                method: "POST",
                url: "./template_ps.php",
                data: {mode : 'getData',sno : $(this).val()},
                dataType: 'json'
            }).success((result) => {
                editor.ncdsEditor.setHTML(result['contents']);
            }).error((e) => {
                NCDSAlert({ message: e.responseText });
            });
        })
        
        $('.js-template-register').bind('click', function (e) {
            $.ajax({
                url: 'template_write.php?templateType=admin',
                success: function (data) {
                    var layerForm = data;
                    layer_popup($(layerForm), '게시글 양식 등록', 'wide')
                },
                error: function (e) {
                    NCDSAlert({ message: e.responseText });
                }
            });
        })

        document.querySelector('.js-btn-back').addEventListener('click', function() {
            history.back();
        });
    })
</script>
