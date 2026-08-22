$(function(){
    if ($('#scroll_right, #scroll_left').length > 0) {
        $('#scroll_right, #scroll_left').gb_quick_menu({
            //HEADER_ID: '#header_warp'
        });
    }
    // top으로 이동
    $('.btn_scroll_top a').click(
        function() {
            $('html, body').stop().animate({scrollTop: $('body').offset().top}, 300);
            return false;
        }
    );
});

$(function(){
    var cateArray = new Array('','','','');
    var arrayNum = 0;
    var menupage = 0;
    var total_width = $('.gnb_menu_box').innerWidth();
    var gd_display_cate = function(){
        var depth1 = 0;
        $('.gnb_menu_box > ul > li').each(function(){
            depth1 += $(this).innerWidth();
            if(depth1 > total_width){
                arrayNum++;
                depth1 =0;
                depth1 += $(this).innerWidth();
                cateArray[arrayNum] += "<li>"+$(this).html()+"</li>";
            }else{
                cateArray[arrayNum] += "<li>"+$(this).html()+"</li>";
            }
        });
        $('.gnb_menu_box > ul').html(cateArray[0]);
        $('header .gnb .depth0').css({
            'overflow':'visible',
            'height':'100%',
        });
    };
    gd_display_cate();
    /* 상단 메뉴 */
    var gd_topmenu = function(){
        $('.depth0 li').on({
            'mouseover':function(){
                $(this).find('> ul').stop(true,true).fadeIn('fast');
                $(this).find('> a').addClass('active');
                //console.log('open');
            },
            'mouseleave':function(){
                $(this).find('> ul').stop(true,true).fadeOut('fast');
                $(this).find('> a').removeClass('active');
                //console.log('hide');
            }
        });
    };
    gd_topmenu();



    /* 메뉴 우측 버튼 */
    $('.gnb_right').on({
        'click':function(){
            if(arrayNum > menupage){
                menupage++;
                if(menupage == arrayNum) $(this).find('a').addClass('active');
                $('.gnb_left').find('a').removeClass('active');
                $('.gnb_menu_box > ul').html(cateArray[menupage]);
                gd_topmenu();
            }
        }
    });
    /* 메뉴 좌측 버튼 */
    $('.gnb_left').on({
        'click':function(){
            if(arrayNum >= menupage && menupage != 0){
                menupage--;
                if(menupage == 0) $(this).find('a').addClass('active');
                $('.gnb_right').find('a').removeClass('active');
                $('.gnb_menu_box > ul').html(cateArray[menupage]);
                gd_topmenu();
            }
        }
    });



    /* 전체메뉴 버튼 */
    /*
     $('.gnb_all').on({
     'click':function(){
     if($('.gnb_allmenu').css('display') =='none') $('.gnb_allmenu').stop(true,true).slideDown('fast');
     else $('.gnb_allmenu').slideUp('fast');

     }
     });
     */

    $ ( '.layer_type .sub_depth0 li'). on ({
        'mouseover': function () {
            $ (this) .find ( '> ul'). stop (true, true) .fadeIn ( 'fast');
            $ (this) .find ( '> a'). addClass ( 'active');
            //console.log('open ');
        },
        'mouseleave': function () {
            $ (this) .find ( '> ul'). stop (true, true) .fadeOut ({ complete : function(){  // dbook.fe 반응형
					$(this).hide();
				}});
            $ (this) .find ( '> a'). removeClass ( 'active');
            //console.log('hide ');
        }
    });

    /* 해외몰 홈아이콘 타입 선택형(국기) */
    $('.top_country_list1 .country_tit').on({
        'click':function(){
            if($(this).parent().find('ul').css('display') =='none'){
                $(this).addClass('active');
                $(this).parent().find('ul').slideDown('fast');
            }else{
                $(this).parent().find('ul').slideUp('fast');
                $(this).removeClass('active');
            }
        }
    });

    /* 해외몰 홈아이콘 타입 선택형(국기,언어) */
    $('.top_country_list2 .country_tit').on({
        'click':function(){
            if($(this).parent().find('ul').css('display') =='none'){
                $(this).addClass('active');
                $(this).parent().find('ul').slideDown('fast');
            }else{
                $(this).parent().find('ul').slideUp('fast');
                $(this).removeClass('active');
            }
        }
    });

    /* location 경로 */
    $('.location_select').on({
        'mouseenter':function() {
            if ($(this).find('ul').css('display') == 'none') {
                $(this).find('.location_tit').addClass('active');
                $(this).find('ul').slideDown('fast');
            }
        },
        'mouseleave':function() {
            $(this).find('ul').slideUp('fast');
            $(this).find('.location_tit').removeClass('active');
        }
    });

    /* 상단 마이페이지 레이어 */
    $('.top_mypage_cont').on({
        'mouseenter':function(){
            if($(this).find('ul').css('display') =='none'){
                $(this).find('.top_mypage_tit').addClass('active');
                $(this).find('ul').show();
            }
        },
        'mouseleave':function(){
            $(this).find('ul').hide();
            $(this).find('.top_mypage_tit').removeClass('active');
        }
    });

    /* 상단 검색 */
    $('.top_search_cont input[name="keyword"]').on({
        'focus':function(){
            if($("input[name=recentCount]").val() > 0) {
                $(this).parents().find('.search_cont').show();
            }
        },
        'blur':function(){
            $('body').click(function(e){
                if (!$('.search_cont').has(e.target).length && e.target.name != 'keyword') {
                    $(this).parents().find('.search_cont').hide();
                }
            });
            $('.btn_top_search_close').click(function(){
                $(this).parents().find('.search_cont').hide();
            });
        }
    });

    /* 레이어, 추가 내용 */
    $('.btn_common_box, .btn_layer').find('a').on({
        'click':function(e){
            var tg = $(this).attr('href');
            if(tg.substr(0, 1) == '#'){
                e.preventDefault();
                if($(tg).css('display') == 'none'){
                    $(tg).show();
                    $(tg).find('.ly_close').attr('href',tg);
                }else{
                    $(tg).hide();
                }
            }
        }
    });
    $('.ly_close').on({
        click:function(){
            var tg = $(this).attr('href');
            if (tg.substr(0, 1) == '#') {
                $(tg).hide();
            }

            if ($(this).parents('.js_password_layer').length) {
                $('.js_password_layer').find('input[name="writerPw"]').val('');
            }
        }
    });
});


function gd_btn_all_menu_close(){ // 반응형
    //$('.gnb_allmenu').slideUp('fast');
    $('.gnb_allmenu_wrap,.btn_all_menu_open').removeClass('on');
	setTimeout(function(){
		$('body').css({'overflow':''});
		$('.layerDim').addClass('dn');
	}, 200);
}

$(document).ready(function() {
    /* 퀵검색 */
    var qs_id = $('#quick_search');
    var cname = qs_id.attr('class');
    var position, position_m;

    if(cname == 'q_left'){
        position = qs_id.innerWidth();
        qs_id.css('left','-'+position+'px');
        // 리사이즈 시 닫힌 상태면 position 재계산
        $(window).on('resize', function(){
            if(!$('.quick_search_cont').hasClass('on')){
                position = qs_id.innerWidth();
                qs_id.css('left','-'+position+'px');
                position_m = '-'+ position;
            }
        });
    }else if(cname == 'q_right'){
        position = qs_id.innerWidth();
        qs_id.css('right','-'+position+'px');
        // 리사이즈 시 닫힌 상태면 position 재계산
        $(window).on('resize', function(){
            if(!$('.quick_search_cont').hasClass('on')){
                position = qs_id.innerWidth();
                qs_id.css('right','-'+position+'px');
                position_m = '-'+ position;
            }
        });
    }else{
        $(window).load(function(){
            position = qs_id.innerHeight();
            qs_id.css('top','-'+position+'px');
        });
        // 리사이즈 시 닫힌 상태면 position 재계산
        $(window).on('resize', function(){
            if(!$('.quick_search_cont').hasClass('on')){
                position = qs_id.innerHeight();
                qs_id.css('top','-'+position+'px');
                position_m = '-'+ position;
            }
        });
    }
    position_m = position;

    function gd_quick_motion(){

        if(cname == 'q_left'){
            position = qs_id.innerWidth();
            if(qs_id.css('left') == '0px') position_m = '-'+ position;
            else position_m = 0;

            qs_id.animate({
                left : position_m
            }, 500, function(){
                //console.log(position_m);
            });
        }else if(cname =='q_right'){
            position = qs_id.innerWidth();
            if(qs_id.css('right') == '0px') position_m = '-'+ position;
            else position_m = 0;

            qs_id.animate({
                right : position_m
            }, 500, function(){
                //console.log(position_m);
            });
        } else {
            position = qs_id.innerHeight();
            if(qs_id.css('top') == '0px') position_m = '-'+ position;
            else position_m = 0;

            qs_id.animate({
                top : position_m
            }, 500, function(){
                //console.log(position_m);
            });

        }
        if(position_m == 0) $('.quick_search_cont').addClass('on');
        else $('.quick_search_cont').removeClass('on');
    }
    $('#quick_search .btn_quick_search_open, #quick_search .btn_quick_search_close').on({
        'click':function(e){
            e.preventDefault();
            gd_quick_motion();
        }
    });

    /* 퀵검색 컬러 & 혜택조건 */
    $('.color_box span label, .benefit_box span label').on({
        'click':function(){
            if(!$(this).parent().find('input').is(':checked')) $(this).addClass('active');
            else $(this).removeClass('active');
        }
    });

    $('.btn_all_menu_open').on('click',function(){ // dbook.fe 수정
		if ( !$(this).hasClass('on') ) {
			gd_btn_all_menu_open();
			$(this).addClass('on');
		} else {
			gd_btn_all_menu_close();
			$(this).removeClass('on');
		}
    });


});




// 레이어박스 센터정렬 플러그인 (최상단)
jQuery.fn.center = function() {
    var top = ($(window).height() - this.outerHeight()) / 2;
    var left = ($(window).width() - this.outerWidth()) / 2;

    this.css({
        position:'absolute',
        margin:0,
        top: (top > 0 ? top : 0) + 'px',
        left: (left > 0 ? left : 0) + 'px'
    });

    return this;
};

// 레이어박스 센터정렬 플러그인 (현재위치)
jQuery.fn.currentCenter = function() {
    this.css({
        'position': 'fixed',
        'left': '50%',
        'top': '50%'
    });

    this.css({
        'margin-left': -this.outerWidth() / 2 + 'px',
        'margin-top': -this.outerHeight() / 2 + 'px'
    });

    return this;
};

// 레이어박스 좌상단 고정 플러그인 (iframe 내부용)
jQuery.fn.positionTopLeft = function() {
    this.css({
        'position': 'fixed',
        'left': '0',
        'top': '0',
        'margin-left': '0',
        'margin-top': '0'
    });

    return this;
};

/* 스크롤배너(오른쪽) */
(function (){
    $.fn.gb_quick_menu = function(options){
        //초기값
        var defaults = {
            HEADER_ID: '.scroll_wrap',
            FIXED_CLS: 'ban_fixed',
            FIXED_SIZE :1450,
            LEFT_QUICK_ID : '#scroll_left',
            CONTENTS_WARP : '#wrap',
        };
        //초기값 옵션 배열 저장
        var options = $.extend({}, defaults, options);
        var el = $(this);
        var scqTop = 0;
        var quickTop = $(options.HEADER_ID).offset().top;
        var qucik_left = $(options.LEFT_QUICK_ID).css('left');

        $(window).on({
            'resize':function(){

            },
            'scroll':function(){
                var win_width = $(window).innerWidth();
                scqTop = $(this).scrollTop();
                if(scqTop <= quickTop){
                    el.removeClass(options.FIXED_CLS).removeAttr('style');
                }else{
                    if(win_width > options.FIXED_SIZE){
                        el.addClass(options.FIXED_CLS).removeAttr('style');
                    }else{
                        el.removeClass(options.FIXED_CLS).removeAttr('style');
                        el.stop(true,true).css('top', (scqTop-quickTop+20));

                    }
                }
            }
        });
        var gd_leftmove = function(){
            var win_width = $(window).innerWidth();
            if(win_width <= options.FIXED_SIZE){
                gd_left_animate(qucik_left);
            }else{
                gd_left_animate(0);
            }
        };
        var gd_left_animate = function(left_quick_position){
            $('#wrap').stop(true,true).animate({
                'marginLeft':left_quick_position,
            },'5000',function(){

            });
        };
        $(window).load(function(){

        })
    };
})(jQuery);

// 체크박스 처리 로직 초기화
function gd_init_checkbox_ui() {
    $(document).on('click', 'input[type=radio]', function(e){
        $(this).parents('form:first').find("input[name='" + $(this).prop("name") + "']").each(function() {
            if ($(this).prop("checked")) {
                $("label[for=" + $(this).attr("id") + "]").addClass("on");
            } else {
                $("label[for=" + $(this).attr("id") + "]").removeClass("on");
            }
        });
    });

    $(document).on('click', 'input[type=checkbox]', function(e){
        if($(this).prop('readonly') === false) {
            if($(this).prop("checked")) {
                $("label[for="+$(this).attr("id")+"]").addClass("on");
            } else {
                $("label[for="+$(this).attr("id")+"]").removeClass("on");
            }
        } else {
            e.preventDefault();
        }
    });
}

// 라디오박스,체크박스 이미지화 스크립트
function gd_trigger_checkbox_ui() {
    var $input = $('input[type=radio], input[type=checkbox]');
    // 템플릿에서 check 처리한 경우 예외처리 추가
    if(!$input.find('label.on')){
        $input.each(function(){
            var $item = $("label[for="+$(this).attr("id")+"]");
            if($(this).prop("checked")) {
                $item.addClass("on");
            } else {
                $item.removeClass("on");
            }
        });
    }
}

// 체크박스 전체 선택
function gd_checkbox_all() {
    // 체크박스 전체 선택 이벤트
    if ($(':checkbox.gd_checkbox_all').length > 0) {
        // 이벤트 중복 실행을 막아준다.
        $(':checkbox.gd_checkbox_all').off('click');
        $(':checkbox.gd_checkbox_all').click(function (e) {
            var $target = $(e.target);
            var targetName = $target.data('target-name');
            var targetId = $target.data('target-id');
            var targetFormName = $target.data('target-form');
            if (typeof targetFormName == 'undefined') targetFormName = "";
            if (_.isUndefined(targetId)) {
                $(targetFormName + ' :checkbox[name="' + targetName + '"]').prop('checked', !$target.prop('checked')).trigger('click');
            } else {
                $(targetFormName + ' :checkbox[id*="' + targetId + '"]').prop('checked', !$target.prop('checked')).trigger('click');
            }
        });
    }
}

// 레이어 박스 이벤트
function gd_center_layer(){
    //$('.btn_open_layer').off('click');
    $(document).on('click', '.btn_open_layer', function() {
        // @qnibus 레이어 안에 레이어가 있는 경우 종속된 보이지 않는 레이어가 이미 떠있는 레이어 기준으로 center 처리 되어 보여져 레이아웃이 깨짐
        $('.layer_wrap').removeAttr('style');
        $('body').css('overflow','hidden');
        var target = $(this).attr('href');
        $(target).removeClass('dn');
        $('#layerDim').removeClass('dn');
        $(target).find('> div').center();

        return false;
    });

    $(document).on('click', '.layer_wrap .layer_close, .btn_box .btn_cancel', function(){
        $(this).closest('.layer_wrap').addClass('dn');
        // 창이 2개 이상 떠있는 경우 Dim처리 안되게
        if (!$('.layer_wrap').is(':visible')) {
            $('#layerDim').addClass('dn');
            $('body').removeAttr('style');
        }
        return false;
    });
}

// 레이어 박스 창닫기 (현재 열려 있는 창만 닫는다)
function gd_close_layer() {
    if ($('.layer_wrap').is(':visible') || $('#layerDim').is(':visible')) {
        if ($('.layer_wrap .layer_close, .btn_box .btn_cancel').length > 0) {
            $('.layer_wrap .layer_close, .btn_box .btn_cancel').trigger('click');
        } else {
            // 딤만 떠있는 경우
            $('.layer_wrap').addClass('dn');
            $('#layerDim').addClass('dn');
        }
    }
}

// chosen 셀렉트 박스
function gd_select_remodeling() {
    var selector = '.chosen-select';
    var config = {
        disable_search_threshold: 10000,
        no_results_text: __('검색결과가 없습니다.')
    };

    if ($(selector).length > 0) $(selector).chosen(config);
}

// 카트탭 레이어
function gd_carttab_layer() {
    $('.cart_tab_list li > a').on({
        'click':function() {
            if ($(this).hasClass('btn_alert_login') == false) {
                $('.btn_shop_cart_box .btn_shop_cart_close').show();
                $('.btn_shop_cart_box .btn_shop_cart_open').hide();
                $(this).parent().addClass('on').siblings().removeClass('on');
                $('#shop_cart_wrap .shop_cart_cont > .cart_tab_box').eq($(this).index()).fadeIn('fast').siblings().removeClass('on').hide();
                $('.shop_cart_cont').slideDown('fast');
                gd_cart_tab_action($(this).attr('href'));
            }
        }
    });

    $('.btn_shop_cart_box .btn_shop_cart_open, .btn_shop_cart_box .btn_shop_cart_close').on({
        'click':function() {
            if ($('.shop_cart_cont').css('display') != 'none') {
                $('.shop_cart_cont').slideUp('fast');
                $('.btn_shop_cart_box .btn_shop_cart_close').hide();
                $('.btn_shop_cart_box .btn_shop_cart_open').show();
                $('.cart_tab_list li').removeClass('on');
            } else {
                $('.cart_tab_list li').eq(0).addClass('on');
                $('.cart_tab_box').eq(0).addClass('on');
                $('.shop_cart_cont').slideDown('fast');
                $('.btn_shop_cart_box .btn_shop_cart_close').show();
                $('.btn_shop_cart_box .btn_shop_cart_open').hide();
                $('#shop_cart_wrap .shop_cart_cont > .cart_tab_box').hide().eq(0).fadeIn('fast');
                gd_cart_tab_action('#cart_tab_today');
            }

            $('.chart_view_horizontal ul').slick('reinit');
        }
    });
}

// 파일첨부 꾸미기
function gd_file_attach() {
    $(document).on('change', '.file_upload_sec .file', function(){
        var i = $(this).val();
        $('label[for=' + $(this).attr('id') + ']').find('.file_text').val(i);
    });
}

/*
 카테고리/브랜드 카테고리 마우스 오버
 */
function gd_menu_over() {

    $(document).on('mouseenter mouseleave', 'img.gd_menu_over', function (event) {
        $(this).attr({
            src: $(this).attr('data-other-src')
            , 'data-other-src': $(this).attr('src')
        });

    });

    $(document).on('mouseenter', 'span.gd_menu_over', function (event) {
        var width = $(this).closest("strong").width();
        var height = $(this).closest("strong").height() - 7;
        $(this).html("<img src='" + $(this).data('other-src') + "' style='max-width:" + width + "px;max-height:" + height + "px'>");

    });

    $(document).on('mouseleave', 'span.gd_menu_over', function (event) {
        $(this).html($(this).data('other-text'));
    });
}

function gd_btn_all_menu_open(){
    // __NDE_CATEGORY_LOADER__가 있으면 캐시된 데이터 사용
    if (window.__NDE_CATEGORY_LOADER__ && window.__NDE_CATEGORY_LOADER__.getCategories) {
        var cachedData = window.__NDE_CATEGORY_LOADER__.getCategories();
        if (cachedData && cachedData.length > 0) {
            gd_render_all_menu(cachedData);
            return;
        }
    }

    // __NDE_CATEGORY_LOADER__가 없거나 캐시가 없으면 서버에서 직접 조회
    gd_fetch_and_render_all_menu();
}

// 서버에서 카테고리 조회 후 렌더링 (fallback - 캐시 없이 단순 조회)
function gd_fetch_and_render_all_menu() {
    $.ajax({
        method: "POST",
        cache: false,
        url: "../goods/goods_ps.php",
        data: "mode=get_all_category",
        success: function(data) {
            var categories = [];

            if (!data || data === 'false') {
                gd_render_all_menu(categories);
                return;
            }

            try {
                var getData = $.parseJSON(data);
                $.each(getData, function (categoryKey, categoryVal) {
                    $.each(categoryVal, function (key, val) {
                        categories.push({
                            id: val.cateCd,
                            label: val.cateNm,
                            href: '../goods/goods_list.php?cateCd=' + val.cateCd,
                            children: val.children ? gd_map_children(val.children) : null
                        });
                    });
                });
            } catch (e) {
                // 파싱 실패 시 빈 메뉴로 처리
            }

            gd_render_all_menu(categories);
        },
        error: function () {
            gd_render_all_menu([]);
        }
    });
}

// 하위 카테고리 매핑 함수
function gd_map_children(children) {
    if (!children || children.length === 0) return null;
    return children.map(function(child) {
        return {
            id: child.cateCd,
            label: child.cateNm,
            href: '../goods/goods_list.php?cateCd=' + child.cateCd,
            children: child.children ? gd_map_children(child.children) : null
        };
    });
}

// 전체 메뉴 렌더링 함수
function gd_render_all_menu(categories) {
    if (!categories) categories = [];

    var addHtml = '<div class="gnb_allmenu"><div class="gnb_allmenu_box">';
    addHtml += '<ul>';

    $.each(categories, function (key, val) {
        addHtml += '<li><div class="all_menu_cont"><a href="' + val.href + '">' + val.label + '</a>';
        if(val.children && val.children.length > 0) {
            addHtml += '<span class="icon_plus"></span><ul class="all_depth1">';
            $.each(val.children, function (key1, val1) {
                var hasDepth2 = val1.children && val1.children.length > 0;
                addHtml += '<li><a href="' + val1.href + '">' + val1.label + '</a>';
                if(hasDepth2) {
                    addHtml += '<span class="icon_plus icon_plus_depth2"></span><ul class="all_depth2">';
                    $.each(val1.children, function (key2, val2) {
                        var hasDepth3 = val2.children && val2.children.length > 0;
                        addHtml += '<li><a href="' + val2.href + '">' + val2.label + '</a>';
                        if(hasDepth3) {
                            addHtml += '<span class="icon_plus icon_plus_depth3"></span><ul class="all_depth3">';
                            $.each(val2.children, function (key3, val3) {
                                addHtml += '<li><a href="' + val3.href + '">' + val3.label + '</a></li>';
                            });
                            addHtml += '</ul>';
                        }
                        addHtml += '</li>';
                    });
                    addHtml += '</ul>';
                }
                addHtml += '</li>';
            });
            addHtml += '</ul>';
        }
        addHtml += '</div></li>';
    });

    // 커스텀 메뉴 항목 추가 (디자인에디터 사이드 메뉴와 동일 규격)
    if (window.__NDE_SIDE_CUSTOM_MENUS__ && window.__NDE_SIDE_CUSTOM_MENUS__.length > 0) {
        $.each(window.__NDE_SIDE_CUSTOM_MENUS__, function (idx, menu) {
            if (menu.hidden === true || menu.hidden === 'true') return;
            if (!menu.link || !menu.title) return;
            var target = (menu.isOpenNewTab === true || menu.isOpenNewTab === 'true') ? ' target="_blank" rel="noopener noreferrer"' : '';
            addHtml += '<li><div class="all_menu_cont"><a href="' + menu.link + '"' + target + '>' + menu.title + '</a></div></li>';
        });
    }

    addHtml += '</ul>';
    addHtml += '</div><span class="btn_all_menu_close" onClick="gd_btn_all_menu_close();"><span>전체메뉴닫기</span></span></div>';

    $('.gnb_allmenu_wrap .nav-box').html(addHtml);
    $('.gnb_allmenu_wrap').addClass('on');
    $('.layerDim').removeClass('dn');

    $('.gnb_allmenu_box .icon_plus').click(function(e){
        e.stopPropagation();
        var $li = $(this).parent('li').length ? $(this).parent('li') : $(this).parent('.all_menu_cont').parent('li');
        $li.toggleClass('on');
        $(this).next('ul').slideToggle();
    });
    $('header .all-btn-close').click(function(){
        gd_btn_all_menu_close();
    });

};

// 함수 호출
$(document).ready(function() {
    gd_init_checkbox_ui();
    gd_trigger_checkbox_ui();
    gd_checkbox_all();
    gd_center_layer();
    gd_carttab_layer();
    gd_select_remodeling();
    gd_file_attach();
    gd_menu_over();
});

// ================================================================
// NDE Enhancement — 디자인 에디터 컴포넌트 인핸스먼트
// ================================================================
// 포함 모듈:
//   1. Select        — 네이티브 <select>를 커스텀 드롭다운 UI로 변환
//   2. Filter Button — 필터 패널 토글 + 아코디언 + 초기화
//   3. Breadcrumb    — 모바일 클릭 기반 드롭다운 토글 (※ 데스크톱 hover는 위쪽 .location_select 핸들러가 담당)
//   4. Mobile Title  — 뒤로가기 버튼 이벤트 바인딩
//
// 규칙:
//   - JS 셀렉터(SEL): js-* 프리픽스 클래스만 사용 (스타일 변경과 무관)
//   - CSS 클래스: 스타일링 전용 (JS에서 조회하지 않음, 생성 시에만 부여)
//   - 상태 토글(STATE): CSS + JS 클래스 동시 토글
// ================================================================
(function ($) {
    'use strict';

    var DESKTOP_BREAKPOINT = '(min-width: 768px)';

    // godomall5 placeholder 정규화 (=텍스트= → 텍스트)
    var GODOMALL_PLACEHOLDER = {'카테고리선택': '카테고리 선택'};

    var normalizeOptionText = function (text) {
        var stripped = text.replace(/^=|=$/g, '').trim();
        return GODOMALL_PLACEHOLDER[stripped] || stripped;
    };

    // ── JS hook selectors (로직 바인딩 전용 — 스타일 변경 시 영향 없음) ──
    var SEL = {
        select: '.js-nde-select',
        selectTrigger: '.js-nde-select-trigger',
        selectLabel: '.js-nde-select-label',
        selectSizer: '.js-nde-select-sizer',
        selectMenu: '.js-nde-select-menu',
        selectOption: '.js-nde-select-option',
        filterBtn: '.js-nde-filter-btn',
        filterClose: '.js-nde-filter-close',
        filterReset: '.js-nde-filter-reset',
        filterSubmit: '.js-nde-filter-submit',
        filterChevron: '.js-nde-filter-chevron',
        breadcrumb: '.js-nde-breadcrumb',
        mobileTitle: '.js-nde-mobile-title'
    };

    // ── 초기화 guard ──
    var INIT = {
        select: 'js-nde-select--init',
        filter: 'js-nde-filter--init',
        filterPanel: 'js-nde-filter-panel--bound',
        breadcrumb: 'js-nde-breadcrumb--init',
        mobileTitle: 'js-nde-mobile-title--init'
    };

    // ── 상태 (CSS + JS 동시 토글) ──
    var STATE = {
        selectOpen: 'nde-c-select--open js-nde-select--open',
        optionSelected: 'nde-c-select__option--selected js-nde-select-option--selected',
        filterActive: 'nde-c-filter-btn--active js-nde-filter--active',
        panelOpen: 'side_cont--open',
        backdrop: 'nde-c-filter-backdrop'
    };

    // ----------------------------------------
    // 1. Select Enhancement
    // ----------------------------------------

    var initSelect = function () {
        $(SEL.select + ':not(.' + INIT.select + ')').each(function () {
            var $container = $(this);
            var $select = $container.find('select');

            if (!$select.length || !$select[0].options.length) return;

            $container.addClass(INIT.select);
            buildSelectUI($container, $select);
            bindSelectEvents($container, $select);
            observeSelectOptions($container, $select);
        });
    };

    var buildSelectUI = function ($container, $select) {
        var select = $select[0];

        // 네이티브 select 숨기기
        $select.addClass('nde-c-select__hidden')
            .attr('tabindex', '-1')
            .attr('aria-hidden', 'true');

        var selectedOpt = select.options[select.selectedIndex];
        var selectedText = normalizeOptionText($(selectedOpt).text().trim());

        // Trigger button (CSS: nde-c-select__trigger / JS: js-nde-select-trigger)
        var $trigger = $('<button type="button" class="nde-c-select__trigger js-nde-select-trigger"></button>');
        var $label = $('<span class="nde-c-select__label js-nde-select-label"></span>');
        var $labelText = $('<span></span>').text(selectedText);
        $label.append($labelText);

        // Sizer spans — 가장 긴 옵션 텍스트 기준으로 trigger 너비 결정
        $.each(select.options, function (_, opt) {
            $label.append(
                $('<span class="nde-c-select__sizer js-nde-select-sizer" aria-hidden="true"></span>')
                    .text(normalizeOptionText($(opt).text().trim()))
            );
        });

        $trigger.append($label);

        // Dropdown menu (CSS: nde-c-select__menu / JS: js-nde-select-menu)
        var $menu = $('<ul class="nde-c-select__menu js-nde-select-menu"></ul>');
        $.each(select.options, function (_, opt) {
            var $li = $('<li class="nde-c-select__option js-nde-select-option"></li>')
                .attr('data-value', opt.value)
                .text(normalizeOptionText($(opt).text().trim()));
            if (opt.selected) $li.addClass(STATE.optionSelected);
            $menu.append($li);
        });

        $container.append($trigger).append($menu);
        $container.data('nde-label-text', $labelText);
    };

    var bindSelectEvents = function ($container, $select) {
        var $trigger = $container.find(SEL.selectTrigger);
        var $menu = $container.find(SEL.selectMenu);

        // form.reset() 동기화
        var form = $select[0].form;
        if (form) {
            $(form).on('reset', function () {
                requestAnimationFrame(function () {
                    syncSelectUI($container, $select);
                });
            });
        }

        // Toggle open/close
        $trigger.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('.js-nde-select--open').not($container).removeClass(STATE.selectOpen);
            $container.toggleClass(STATE.selectOpen);
        });

        // Option 선택
        $menu.on('click', SEL.selectOption, function () {
            var $target = $(this);
            var value = $target.attr('data-value') || '';

            $menu.find('.js-nde-select-option--selected').removeClass(STATE.optionSelected);
            $target.addClass(STATE.optionSelected);

            var $labelText = $container.data('nde-label-text');
            if ($labelText) $labelText.text($target.text());

            $select.val(value);
            $select[0].dispatchEvent(new Event('change', {bubbles: true}));
            $container.removeClass(STATE.selectOpen);
        });
    };

    /** <select> 옵션 변경 시 커스텀 UI 재구성 (AJAX 카테고리 depth 로딩) */
    var rebuildSelectMenu = function ($container, $select) {
        var select = $select[0];
        var selectedOpt = select.options[select.selectedIndex];
        var $labelText = $container.data('nde-label-text');
        if ($labelText) $labelText.text(normalizeOptionText($(selectedOpt).text().trim()));

        var $label = $container.find(SEL.selectLabel);
        $label.find(SEL.selectSizer).remove();
        $.each(select.options, function (_, opt) {
            $label.append(
                $('<span class="nde-c-select__sizer js-nde-select-sizer" aria-hidden="true"></span>')
                    .text(normalizeOptionText($(opt).text().trim()))
            );
        });

        var $menu = $container.find(SEL.selectMenu);
        $menu.empty();
        $.each(select.options, function (_, opt) {
            var $li = $('<li class="nde-c-select__option js-nde-select-option"></li>')
                .attr('data-value', opt.value)
                .text(normalizeOptionText($(opt).text().trim()));
            if (opt.selected) $li.addClass(STATE.optionSelected);
            $menu.append($li);
        });
    };

    /** MutationObserver로 <select> 자식 변경 감지 */
    var observeSelectOptions = function ($container, $select) {
        if (typeof MutationObserver === 'undefined') return;
        var observer = new MutationObserver(function () {
            rebuildSelectMenu($container, $select);
        });
        observer.observe($select[0], {childList: true});
    };

    /** form.reset() 시 커스텀 UI를 네이티브 select와 동기화 */
    var syncSelectUI = function ($container, $select) {
        var select = $select[0];
        var opt = select.options[select.selectedIndex];
        var $labelText = $container.data('nde-label-text');
        if ($labelText) $labelText.text(normalizeOptionText($(opt).text().trim()));

        var $menu = $container.find(SEL.selectMenu);
        $menu.find('.js-nde-select-option--selected').removeClass(STATE.optionSelected);
        $menu.find(SEL.selectOption).each(function () {
            if ($(this).attr('data-value') === select.value) {
                $(this).addClass(STATE.optionSelected);
                return false;
            }
        });
    };

    /** 커스텀 셀렉트 value setter (필터 초기화용) */
    var setSelectValue = function ($container, value) {
        var $select = $container.find('select');
        if (!$select.length) return;

        $select.val(value);
        var opt = $select[0].options[$select[0].selectedIndex];
        var $labelText = $container.data('nde-label-text');
        if ($labelText) $labelText.text(normalizeOptionText($(opt).text().trim()));

        var $menu = $container.find(SEL.selectMenu);
        $menu.find('.js-nde-select-option--selected').removeClass(STATE.optionSelected);
        $menu.find(SEL.selectOption + '[data-value="' + value + '"]').addClass(STATE.optionSelected);
        $select[0].dispatchEvent(new Event('change', {bubbles: true}));
    };

    // 외부 클릭 시 열린 셀렉트 닫기
    $(document).on('mousedown.ndeSelect', function (e) {
        if (!$(e.target).closest(SEL.select).length) {
            $('.js-nde-select--open').removeClass(STATE.selectOpen);
        }
    });

    // ----------------------------------------
    // 2. Filter Button Enhancement
    // ----------------------------------------

    var initFilterButton = function () {
        $(SEL.filterBtn + ':not(.' + INIT.filter + ')').each(function () {
            var $btn = $(this);
            $btn.addClass(INIT.filter);

            $btn.on('click', function () {
                var $section = $btn.closest('.nde-c-section-goods-search, .goods_search_wrap');
                if (!$section.length) return;
                var $panel = $section.find('.side_cont').first();
                if (!$panel.length) return;

                if ($panel.hasClass(STATE.panelOpen)) {
                    closeFilterPanel($btn, $panel);
                } else {
                    openFilterPanel($btn, $panel);
                }
            });
        });
    };

    var ensureFilterPanelUI = function ($panel) {
        if ($panel.hasClass(INIT.filterPanel)) return;
        $panel.addClass(INIT.filterPanel);

        // 가격 입력 숫자 필터링
        $panel.find('.price_box input[type="text"]').each(function () {
            var $input = $(this);
            this.inputMode = 'numeric';
            if (!$input.attr('placeholder')) $input.attr('placeholder', ' ');
            $input.on('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });

        // 아코디언 섹션
        $panel.find('.sub_search_box dl').each(function () {
            var $dl = $(this);
            var $dt = $dl.find('dt');
            if (!$dt.length || $dt.find(SEL.filterChevron).length) return;
            $dt.append('<span class="nde-c-filter-chevron js-nde-filter-chevron"></span>');
            $dt.on('click', function () {
                $dl.toggleClass('filter_section--collapsed');
            });
        });

        // 패널 닫기 / 초기화 / 적용 버튼
        $panel.find(SEL.filterClose).on('click', function () {
            var $btn = $panel.closest('.nde-c-section-goods-search, .goods_search_wrap').find(SEL.filterBtn);
            closeFilterPanel($btn, $panel);
        });
        $panel.find(SEL.filterReset).on('click', function () {
            resetAllFilters($panel);
        });
        $panel.find(SEL.filterSubmit).on('click', function () {
            var $submitBtn = $panel.find('.quick_btn input[type="button"]');
            if ($submitBtn.length) $submitBtn[0].click();
            var $btn = $panel.closest('.nde-c-section-goods-search, .goods_search_wrap').find(SEL.filterBtn);
            closeFilterPanel($btn, $panel);
        });
    };

    var resetAllFilters = function ($panel) {
        $panel.find('input[type="checkbox"]').prop('checked', false);
        $panel.find('.color_box label').removeClass('active');
        $panel.find('input[type="text"]').val('');
        $panel.find('select').each(function () {
            this.selectedIndex = 0;
        });
        $panel.find(SEL.select).each(function () {
            var $container = $(this);
            var $s = $container.find('select');
            if ($s.length && $s[0].options.length) {
                setSelectValue($container, $s[0].options[0].value || '');
            }
        });
    };

    var openFilterPanel = function ($btn, $panel) {
        if ($panel.hasClass(STATE.panelOpen)) return;
        ensureFilterPanelUI($panel);
        initSelect(); // 래핑 스크립트보다 늦게 생성된 셀렉트 초기화
        $panel.addClass(STATE.panelOpen);
        $btn.addClass(STATE.filterActive);
        var $backdrop = $('<div class="' + STATE.backdrop + '"></div>');
        $panel.parent().append($backdrop);
        document.body.style.overflow = 'hidden';
        $backdrop.on('click', function () {
            closeFilterPanel($btn, $panel);
        });
    };

    var closeFilterPanel = function ($btn, $panel) {
        $panel.removeClass(STATE.panelOpen);
        $btn.removeClass(STATE.filterActive);
        $('.' + STATE.backdrop).remove();
        document.body.style.overflow = '';
    };

    // ----------------------------------------
    // 3. Breadcrumb Enhancement (모바일 전용)
    //    ※ 데스크톱 hover는 위쪽 .location_select mouseenter/mouseleave 핸들러가 담당
    // ----------------------------------------

    var BREADCRUMB_ACTIVE = 'active';
    var BREADCRUMB_ACTIVE_TYPO = 'actvie'; // godomall5 레거시 CSS 오타 호환

    var initBreadcrumb = function () {
        $(SEL.breadcrumb + ':not(.' + INIT.breadcrumb + ')').each(function () {
            var $bc = $(this);
            $bc.addClass(INIT.breadcrumb);

            $bc.on('click', function (e) {
                if (window.matchMedia(DESKTOP_BREAKPOINT).matches) return;

                var $target = $(e.target);
                if ($target.closest('.location_select ul').length) return;

                var $tit = $target.closest('.location_tit');
                if (!$tit.length) return;

                e.preventDefault();
                $bc.find('.location_tit').not($tit)
                    .removeClass(BREADCRUMB_ACTIVE + ' ' + BREADCRUMB_ACTIVE_TYPO);
                $tit.toggleClass(BREADCRUMB_ACTIVE).toggleClass(BREADCRUMB_ACTIVE_TYPO);
            });

            $(document).on('mousedown.ndeBreadcrumb', function (e) {
                if (!$bc[0].contains(e.target)) {
                    $bc.find('.location_tit')
                        .removeClass(BREADCRUMB_ACTIVE + ' ' + BREADCRUMB_ACTIVE_TYPO);
                }
            });
        });
    };

    // ----------------------------------------
    // 4. Mobile Title Enhancement
    // ----------------------------------------

    var initMobileTitle = function () {
        $(SEL.mobileTitle + ':not(.' + INIT.mobileTitle + ')').each(function () {
            var $el = $(this);
            $el.addClass(INIT.mobileTitle);
            var $btn = $el.find('button[aria-label="뒤로가기"]');
            if ($btn.length) {
                $btn.on('click', function () {
                    window.history.back();
                });
            }
        });
    };

    // ----------------------------------------
    // 초기화
    // ----------------------------------------

    $(function () {
        initSelect();
        initFilterButton();
        initBreadcrumb();
        initMobileTitle();
    });

})(jQuery);
