<link rel="stylesheet" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-load-template.css')?>">

<div class="modal-dialog__content layer-load-template">
    <ul>
        <li class="ncua-caution-text">치환코드는 지정된 페이지에서만 사용하실 수 있습니다.</li>
        <li class="ncua-notice-info">예) 주문 관련 치환코드는 주문리스트에서 SMS 발송 시에만 사용하실 수 있습니다.</li>
        <li class="ncua-notice-info">중복 선택은 불가합니다.</li>
    </ul>
    <form id="formTemplateSearch" method="get">
    <div class="ncua-table ncua-table--vertical">
        <table>
            <tbody>
                <tr>
                    <th><div>검색</div></th>
                    <td>
                        <div class="ncua-gap-4">
                            <div class="ncua-select ncua-select--xs">
                                <div class="ncua-select__content">
                                    <select class="ncua-select__tag" id="searchCategory" name="pushType">
                                        <option value="">전체</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['value'] ?>"><?= $category['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="ncua-input ncua-input--xs">
                                <div class="ncua-input__content-wrap">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" id="searchKeyword" name="keyword" placeholder="제목 또는 내용을 입력해 주세요." />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary" id="btnSearch">
                                <span class="ncua-btn__label">검색</span>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    </form>
    <div id="searchTemplateResult"></div>
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
    const formTemplateSearch = document.querySelector('#formTemplateSearch');

    async function searchTemplates(formData = null) {
        try {
            if (!formData) {
                formData = new FormData(formTemplateSearch);
            }

            const params = new URLSearchParams(formData).toString();
            const response = await fetch(`./mobile_send/layer_mobile_send_myapp_push_template_result.php?${params}`, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP ${response.status}: ${errorText}`);
            }
            $('#searchTemplateResult').html(await response.text());
        } catch (e) {
            NCDSAlert({
                message: '처리 중에 오류가 발생하여 실패되었습니다.',
                subMessage: '템플릿 정보를 불러오는 중 오류가 발생하였습니다.',
                iconType: 'error'
            });
        }
    }

    async function applyTemplateMyappPushContents(selectedTemplate) {
        const pushSubject = selectedTemplate.dataset.pushSubject;
        const pushContent = selectedTemplate.dataset.pushContent;

        const convertedContents = pushContent
            .replace(/\\r\\n/g, '\n')
            .replace(/\\n/g, '\n');
        const pushImage = selectedTemplate.dataset.pushImage
        const pushUrl = selectedTemplate.dataset.pushUrl
        const pushWithdraw = selectedTemplate.dataset.pushWithdraw

        const templateData = {
            title: pushSubject,
            message: convertedContents,
            imageUrl: pushImage,
            pushUrl: pushUrl,
            pushWithdraw: pushWithdraw
        }

        setTemplate(templateData);
    }

    async function urlToFile(url, filename) {
        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error();

            const blob = await res.blob();
            const mime = blob.type || "image/jpeg";

            return new File([blob], filename, { type: mime });
        } catch (e) {
            console.log(e);
            NCDSAlert({ message: '템플릿 이미지 로드중 오류가 발생했습니다', iconType: 'error' })
        }
    }

    document.getElementById('btnSearch').addEventListener('click', function() {
        searchTemplates();
    });

    document.getElementById('searchKeyword').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchTemplates();
        }
    });

    document.getElementById('btnApply').addEventListener('click', function() {
        const templateRadios = document.querySelectorAll('input[name="template"]');
        if (!templateRadios || templateRadios.length === 0) {
            NCDSAlert({ message: '템플릿을 선택해 주세요.', iconType: 'error' });
            return;
        }

        const selected = document.querySelector('input[name="template"]:checked');
        if (!selected) {
            NCDSAlert({ message: '템플릿을 선택해 주세요.', iconType: 'error' });
            return;
        }

        applyTemplateMyappPushContents(selected);
        layer_close();
    });

    searchTemplates();
</script>
