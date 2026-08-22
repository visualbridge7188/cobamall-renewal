<link rel="stylesheet" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/share/layer-goods-option-grid-config.css')?>">
<article class="ncua-content" style="padding:20px;">
<form name="goodsGridOptionForm" id="goodsGridOptionForm" action="./goods_ps.php" method="post" target="ifrmProcess">
    <input type="hidden" name="mode" value="get_goods_option_admin_grid_list" />
    <input type="hidden" name="goodsOptionGridMode" value="<?=$goodsGridOptionMode?>" />
    <input type="hidden" name="gridSort" value="" class="js-grid-sort-value" />

    <div class="ncua-col-setting">
        <!-- 좌측 패널: 전체 조회항목 -->
        <div class="ncua-col-setting__panel">
            <div class="ncua-col-setting__panel-title-row">
                <span class="ncua-col-setting__panel-title">전체 조회항목</span>
            </div>
            <div class="ncua-col-setting__panel-body">
                <div class="ncua-col-setting__panel-head-bar">
                    <div class="ncua-col-setting__head-left">
                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-select-all-left"><span class="ncua-btn__label">전체선택</span></button>
                        <span class="ncua-col-setting__count js-count-left">선택 <span class="ncua-col-setting__count-num">0</span>개 / 전체 <span class="js-count-left-total">0</span></span>
                    </div>
                    <div class="ncua-selectbox ncua-selectbox--xs js-goods-grid-sort-box"></div>
                </div>
                <ul class="ncua-col-setting__list js-list-all">
                </ul>
            </div>
        </div>

        <!-- 중앙 이동 버튼 -->
        <div class="ncua-col-setting__actions">
            <button type="button" class="ncua-btn ncua-btn--sm only-icon ncua-btn--secondary-gray js-add" title="선택 항목을 노출 조회항목에 추가" aria-label="선택 항목을 노출 조회항목에 추가" disabled>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"></path></svg>
            </button>
            <button type="button" class="ncua-btn ncua-btn--sm only-icon ncua-btn--secondary-gray js-remove" title="노출 조회항목에서 제거" aria-label="노출 조회항목에서 제거" disabled>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"></path></svg>
            </button>
        </div>

        <!-- 우측 패널: 노출 조회항목 -->
        <div class="ncua-col-setting__panel">
            <div class="ncua-col-setting__panel-title-row">
                <span class="ncua-col-setting__panel-title">노출 조회항목</span>
            </div>
            <div class="ncua-col-setting__panel-body">
                <div class="ncua-col-setting__panel-head-bar">
                    <div class="ncua-col-setting__head-left">
                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-select-all-right"><span class="ncua-btn__label">전체선택</span></button>
                        <span class="ncua-col-setting__count js-count-right">선택 <span class="ncua-col-setting__count-num">0</span>개 / 전체 <span class="js-count-right-total">0</span></span>
                    </div>
                    <div class="ncua-button-group ncua-button-group--xs has-border">
                        <button type="button" class="ncua-button-group__item is-only-icon js-reorder" data-direction="bottom" title="맨 아래로">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 13 6 6 6-6M6 7l6 6 6-6"/></svg>
                        </button>
                        <button type="button" class="ncua-button-group__item is-only-icon js-reorder" data-direction="down" title="아래로">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <button type="button" class="ncua-button-group__item is-only-icon js-reorder" data-direction="up" title="위로">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m18 15-6-6-6 6"/></svg>
                        </button>
                        <button type="button" class="ncua-button-group__item is-only-icon js-reorder" data-direction="top" title="맨 위로">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m18 17-6-6-6 6M18 11l-6-6-6 6"/></svg>
                        </button>
                    </div>
                </div>
                <ul class="ncua-col-setting__list js-list-active">
                </ul>
            </div>
        </div>
    </div>

    <div class="ncua-col-setting__note">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-4m0-4h.01M22 12c0 5.523-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2s10 4.477 10 10"/></svg>
        <span>Shift 버튼을 누른 상태에서 선택하면 여러 항목을 동시에 선택할 수 있습니다.</span>
    </div>

</form>

<script type="text/javascript">
<!--
var CHECK_SVG = '<svg class="ncua-col-setting__item-check" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 6 9 17l-5-5"/></svg>';

function buildItem(key, label) {
    return '<li class="ncua-col-setting__item" data-field-key="' + key + '">' +
        '<div class="ncua-col-setting__item-inner">' +
        '<span class="ncua-col-setting__item-text">' + label + '</span>' +
        CHECK_SVG +
        '</div></li>';
}

function buildActiveItem(key, label) {
    return '<li class="ncua-col-setting__item" data-field-key="' + key + '">' +
        '<div class="ncua-col-setting__item-inner">' +
        '<span class="ncua-col-setting__item-text">' + label + '</span>' +
        CHECK_SVG +
        '</div>' +
        '<input type="hidden" name="goodsOptionGridList[]" value="' + key + '" />' +
        '</li>';
}

function updateCountLeft() {
    var selected = $('.js-list-all .ncua-col-setting__item.is-selected:not(.is-in-active)').length;
    var total = $('.js-list-all .ncua-col-setting__item:not(.is-in-active)').length;
    $('.js-count-left .ncua-col-setting__count-num').text(selected);
    $('.js-count-left .js-count-left-total').text(total);
    $('.js-add').prop('disabled', selected === 0);
}

function updateCountRight() {
    var selected = $('.js-list-active .ncua-col-setting__item.is-selected').length;
    var total = $('.js-list-active .ncua-col-setting__item').length;
    $('.js-count-right .ncua-col-setting__count-num').text(selected);
    $('.js-count-right .js-count-right-total').text(total);
    $('.js-remove').prop('disabled', selected === 0);
}

function syncLeftDimmed() {
    var activeKeys = $('.js-list-active .ncua-col-setting__item').map(function() {
        return $(this).data('field-key');
    }).get();
    $('.js-list-all .ncua-col-setting__item').each(function() {
        var key = $(this).data('field-key');
        if ($.inArray(key, activeKeys) !== -1) {
            $(this).addClass('is-in-active');
        } else {
            $(this).removeClass('is-in-active');
        }
    });
}

$(document).ready(function () {
    $(document).off('click', '.js-list-all .ncua-col-setting__item');
    $(document).off('click', '.js-list-active .ncua-col-setting__item');

    var lastLeftRow = null;
    $(document).on('click', '.js-list-all .ncua-col-setting__item', function (e) {
        $('.js-list-active .ncua-col-setting__item').removeClass('is-selected');

        if (e.shiftKey && lastLeftRow) {
            var items = $('.js-list-all .ncua-col-setting__item');
            var ia = items.index(lastLeftRow);
            var ib = items.index(this);
            var bot = Math.min(ia, ib);
            var top = Math.max(ia, ib);
            for (var i = bot; i <= top; i++) {
                items.eq(i).addClass('is-selected');
            }
        } else {
            $(this).toggleClass('is-selected');
        }
        lastLeftRow = $(this);
        updateCountLeft();
        updateCountRight();
    });

    var lastRightRow = null;
    $(document).on('click', '.js-list-active .ncua-col-setting__item', function (e) {
        $('.js-list-all .ncua-col-setting__item').removeClass('is-selected');

        if (e.shiftKey && lastRightRow) {
            var items = $('.js-list-active .ncua-col-setting__item');
            var ia = items.index(lastRightRow);
            var ib = items.index(this);
            var bot = Math.min(ia, ib);
            var top = Math.max(ia, ib);
            for (var i = bot; i <= top; i++) {
                items.eq(i).addClass('is-selected');
            }
        } else {
            $(this).toggleClass('is-selected');
        }
        lastRightRow = $(this);
        updateCountLeft();
        updateCountRight();
    });

    $('.js-select-all-left').click(function () {
        var items = $('.js-list-all .ncua-col-setting__item:not(.is-in-active)');
        var allSelected = items.filter('.is-selected').length === items.length;
        items.toggleClass('is-selected', !allSelected);
        updateCountLeft();
    });

    $('.js-select-all-right').click(function () {
        var items = $('.js-list-active .ncua-col-setting__item');
        var allSelected = items.filter('.is-selected').length === items.length;
        items.toggleClass('is-selected', !allSelected);
        updateCountRight();
    });

    $('.js-add').click(function () {
        var selectedItems = $('.js-list-all .ncua-col-setting__item.is-selected');
        if (selectedItems.length === 0) { NCDSAlert({message: '이동할 항목을 선택해주세요.', iconType: 'error'}); return; }
        var dupCount = 0;
        selectedItems.each(function () {
            var key = $(this).data('field-key');
            var label = $(this).find('.ncua-col-setting__item-text').text();
            var exists = false;
            $('.js-list-active .ncua-col-setting__item').each(function () {
                if ($(this).data('field-key') === key) { exists = true; dupCount++; return false; }
            });
            if (!exists) { $('.js-list-active').append(buildActiveItem(key, label)); }
            $(this).removeClass('is-selected');
        });
        if (dupCount > 0) { NCDSAlert({message: '중복된 항목은 추가 되지 않습니다.', iconType: 'error'}); }
        syncLeftDimmed(); updateCountLeft(); updateCountRight();
    });

    $('.js-remove').click(function () {
        var selectedItems = $('.js-list-active .ncua-col-setting__item.is-selected');
        if (selectedItems.length === 0) { NCDSAlert({message: '삭제할 항목을 선택해주세요.', iconType: 'error'}); return; }
        selectedItems.remove();
        syncLeftDimmed(); updateCountLeft(); updateCountRight();
    });

    $('.js-reorder').click(function () {
        var direction = $(this).data('direction');
        var selected = $('.js-list-active .ncua-col-setting__item.is-selected');
        if (selected.length === 0) { NCDSAlert({message: '순서 변경을 원하시는 항목을 선택해주세요.', iconType: 'error'}); return; }
        var indices = selected.map(function () { return $('.js-list-active .ncua-col-setting__item').index(this); }).get();
        for (var i = 1; i < indices.length; i++) {
            if (indices[i] !== indices[i-1] + 1) { NCDSAlert({message: '비연속 선택된 항목은 순서 변경이 불가합니다.', iconType: 'error'}); return; }
        }
        var list = $('.js-list-active');
        var items = list.children('.ncua-col-setting__item');
        switch (direction) {
            case 'up': if (indices[0] > 0) selected.first().prev().before(selected); break;
            case 'down': if (indices[indices.length - 1] < items.length - 1) selected.last().next().after(selected); break;
            case 'top': list.prepend(selected); break;
            case 'bottom': list.append(selected); break;
        }
    });

    $(document).on('keydown.gridConfig', function (e) {
        var selected = $('.js-list-active .ncua-col-setting__item.is-selected');
        if (selected.length === 0) return;
        if (e.keyCode === 38) { selected.first().prev().before(selected); e.preventDefault(); }
        else if (e.keyCode === 40) { selected.last().next().after(selected); e.preventDefault(); }
    });

    var sortBoxEl = document.querySelector('.js-goods-grid-sort-box');
    if (sortBoxEl && window.ncua && window.ncua.SelectBox) {
        new window.ncua.SelectBox(sortBoxEl, {
            options: [
                { id: '', text: '기본순서' },
                { id: 'desc', text: '이름순' },
                { id: 'asc', text: '이름역순' }
            ],
            value: '',
            placeholder: '기본순서',
            size: 'xs',
            onChange: function(value) {
                $('.js-grid-sort-value').val(value);
                $('#goodsGridOptionForm input[name="mode"]').val('get_grid_option_list_sort');
                $.post('goods_ps.php', $('#goodsGridOptionForm').serialize(), function (data) {
                    if (data) {
                        var result = $.parseJSON(data);
                        if (result) {
                            var html = '';
                            $.each(result, function (key, val) { html += buildItem(key, val); });
                            $('.js-list-all').html(html);
                            syncLeftDimmed(); updateCountLeft();
                        }
                    }
                });
            }
        });
    }

    $.post('goods_ps.php', $('#goodsGridOptionForm').serialize(), function (data) {
        if (data) {
            var result = $.parseJSON(data);
            if (result.all) {
                var html = '';
                $.each(result.all, function (key, val) { html += buildItem(key, val); });
                $('.js-list-all').html(html);
            }
            if (result.select) {
                var html = '';
                $.each(result.select, function (key, val) { html += buildActiveItem(key, val); });
                $('.js-list-active').html(html);
            }
            syncLeftDimmed(); updateCountLeft(); updateCountRight();
        }
    });
});
//-->
</script>
</article>
