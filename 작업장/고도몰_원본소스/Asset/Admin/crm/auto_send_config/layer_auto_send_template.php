<link rel="stylesheet" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-load-template.css')?>">

<input type="hidden" id="templateChannel" value="<?= $channel ?>" />
<div class="modal-dialog__content layer-load-template" id="layerLoadTemplate">
</div>

<div class="modal-dialog__footer">
	<button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray js-layer-close">
		<span class="ncua-btn__label">취소</span>
	</button>
	<button type="button" class="ncua-btn ncua-btn--sm ncua-btn--primary" id="btnApply">
		<span class="ncua-btn__label">추가</span>
	</button>
</div>

<script type="text/javascript">
(function() {
    const searchState = {
        channel: document.getElementById('templateChannel').value,
        category: '',
        keyword: '',
    };

    let isSearching = false;

    const searchTemplates = (page = 1) => {
        if (isSearching) return;
        isSearching = true;

        if (page === 1) {
            searchState.category = document.getElementById('searchCategory')?.value ?? '';
            searchState.keyword = document.getElementById('searchKeyword')?.value ?? '';
        }

        $.post('./auto_send_config/layer_auto_send_template_content.php', {
            ...searchState,
            page,
        }, function(response) {
            document.getElementById('layerLoadTemplate').innerHTML = response;
            if (document.getElementById('searchCategory')) document.getElementById('searchCategory').value = searchState.category;
            if (document.getElementById('searchKeyword')) document.getElementById('searchKeyword').value = searchState.keyword;
        }).always(function() {
            isSearching = false;
        });
    };

    searchTemplates();

    // 이벤트 위임 (동적으로 로드되는 요소를 위해)
    document.getElementById('layerLoadTemplate').addEventListener('click', function(e) {
        if (e.target.closest('#btnSearch')) {
            searchTemplates(1);
            return;
        }

        const pageLink = e.target.closest('[data-page]');
        if (pageLink) {
            e.preventDefault();
            searchTemplates(parseInt(pageLink.dataset.page));
        }
    });

    document.getElementById('layerLoadTemplate').addEventListener('keypress', function(e) {
        if (e.target.id === 'searchKeyword' && e.key === 'Enter') {
            e.preventDefault();
            searchTemplates(1);
        }
    });

    document.getElementById('btnApply').addEventListener('click', function() {
        const selected = document.querySelector('input[name="template"]:checked');
        if (!selected) {
            NCDSAlert({
                message: '템플릿을 선택하세요.',
                iconType: 'error'
            });
            return;
        }

        const sno = selected.value;
        const contents = selected.dataset.contents;
        const myappImage = selected.dataset.myappImage || '';
        const subject = selected.dataset.subject || '';
        const url = selected.dataset.url || '';
        if (window.onTemplateSelect && typeof window.onTemplateSelect === 'function') {
            window.onTemplateSelect(contents, sno, myappImage, subject, url);
        }

        document.querySelector('.js-layer-close').click();
    });
})();
</script>