/**
 * 관리자 패널 호출
 * @param string menuCode 현재 폴더명
 * @param string menuKey 현재 화일 키
 * @param string menuFile 현재 화일 명
 *
 * @event adminPanelApiComplete - 패널 API 처리 완료 시 발행 (popupCos 팝업이 있는 경우 렌더링 완료 후 발행)
 * @event adminPanelPopupClosed - popupCos 팝업 닫기/N일간 보지 않기 클릭 시 발행
 */
function adminPanelApiAjax(menuCode, menuKey, menuFile) {
    var params = {
        menuCode: menuCode,
        menuKey: menuKey,
        menuFile: menuFile,
    };

    $.ajax({
        method: 'POST',
        cache: false,
        url: '/share/admin_panel_api.php',
        data: params,
        async: true,
        dataType: 'json',
        success: function (data) {
            if (data === null || typeof data == 'undefined') {
                $(document).trigger('adminPanelApiComplete');
                return;
            }
            // 쇼핑몰 관리 비밀번호 변경안내 팝업 노출 시 화면 뒤에 팝업이 노출되지 않게 처리
            if ($('.bootstrap-dialog-title').text().trim() === "쇼핑몰 관리 비밀번호 변경안내") {
                $(document).trigger('adminPanelApiComplete');
                return;
            }

            var _hasPopupCos = false;
            $.each(data, function (index, value) {
                switch (index) {
                    case 'banner' :
                        $.each(value, function (idx, val) {
                            var panel = $("#panel_" + index + '_' + val.panelCode);
                            if (panel) {
                                panel.css({
                                    width: val.panelData.width + 'px',
                                    height: val.panelData.height + 'px'
                                });
                                panel.append(val.panelData.posts[0].postBodyText);
                            }
                        });
                        break;

                    case 'board' :
                        const unifiedBuffer = []; // 공지 + 업데이트 통합 누적
                        let unifiedSharePath = '';

                        $.each(value, function (idx, val) {
                            if (val.panelData.indexOf('Client error') !== -1) return;
                            var panel = $("#panel_" + index + '_' + val.panelCode);

                            switch (val.panelCode) {
                                case 'noticeAPI':
                                case 'patchAPI':
                                    // 신규: 통합 패널이 있으면 누적, 없으면 기존 분리 패널 렌더(BC)
                                    const unifiedPanel = $("#panel_board_noticeUpdateAPI");
                                    if (unifiedPanel.length) {
                                        const isNotice = val.panelCode === 'noticeAPI';
                                        unifiedSharePath = val.gdSharePath;
                                        $.each(val.panelData, function (i, item) {
                                            unifiedBuffer.push({
                                                tabKey: isNotice ? 'notice' : 'update',
                                                badgeClass: isNotice ? 'badge-notice' : 'badge-update',
                                                badgeLabel: isNotice ? '공지' : '업데이트',
                                                publishedAt: item.publishedAt,
                                                title: item.title,
                                                url: item.url,
                                                isNewPost: item.isNewPost,
                                                isTopFixed: item.isTopFixed
                                            });
                                        });
                                    } else if (panel.length) {
                                        let html = '';
                                        $.each(val.panelData, function (i, item) {
                                            html += `
                                                <li>
                                                    <a href="${item.url}" target="_blank">
                                                        ${item.title}
                                                        ${item.isNewPost ? `<img src="${val.gdSharePath}img/icon_new.png" alt="NEW" class="img-fix">` : ''}
                                                    </a>
                                                    <span>${item.publishedAt}</span>
                                                </li>`;
                                        });
                                        panel.html(html);
                                    }
                                    break;

                                case 'sellerTipAPI':
                                    if (!panel.length) return;
                                    panel.empty();
                                    val.panelData.sort(function (a, b) {
                                        if (!!a.isTopFixed !== !!b.isTopFixed) {
                                            return a.isTopFixed ? -1 : 1; // 상단 고정 먼저
                                        }
                                        return (Date.parse(b.publishedAt) || 0) - (Date.parse(a.publishedAt) || 0); // 그 다음 최신 날짜 우선 (NaN 가드)
                                    });
                                    $.each(val.panelData, function (i, item) {
                                        const $li = $('<li>');
                                        const $a = $('<a>')
                                            .attr('href', item.url)
                                            .attr('target', '_blank')
                                            .appendTo($li);
                                        if (item.isTopFixed) {
                                            $a.addClass('bold');
                                        }
                                        $('<span>')
                                            .addClass('ellipsis')
                                            .text(item.title)
                                            .appendTo($a);
                                        if (item.isNewPost) {
                                            $a.append(' ');
                                            $('<img>')
                                                .attr('src', val.gdSharePath + 'img/icon_new.png')
                                                .attr('alt', 'NEW')
                                                .addClass('img-fix')
                                                .appendTo($a);
                                        }
                                        $('<span>')
                                            .addClass('published-at')
                                            .text(item.publishedAt)
                                            .appendTo($li);
                                        panel.append($li);
                                    });
                                    break;

                                default:
                                    if (!panel.length) return;
                                    panel.html(add_new_mark(val.gdSharePath, val.panelData));
                                    break;
                            }
                        });

                        if (unifiedBuffer.length) {
                            renderUnifiedNoticeUpdate(unifiedBuffer, unifiedSharePath);
                        }
                        break;

                    case 'link' :
                        $.each(value, function (idx, val) {
                            var html = '';
                            var panel = $("#panel_" + index + '_' + val.panelCode);
                            if (panel) {
                                html = '<a href="' + val.panelData + '" target="_blank" class="btn btn-sm btn-link">더보기</a>';
                                panel.html(html);
                            }
                        });
                        // 통합 탭 더보기 URL 초기 swap (전체 탭 default)
                        applyMoreLinkSwap('all');
                        break;

                    case 'customer' :
                        $.each(value, function (idx, val) {
                            var html = '';
                            var panel = $("#panel_" + index + '_' + val.panelCode);
                            if (panel) {
                                html = '<span class="call">' + val.panelData.tel + '</span>' +
                                    '<table>' +
                                    '<tr><td>평일</td><td>' + val.panelData.text1 + '</td></tr>' +
                                    '<tr><td></td><td>' + val.panelData.text2 + '</td></tr>' +
                                    '</table>';
                                panel.html(html);
                            }
                        });
                        break;

                    case 'popup' :
                        $.each(value, function (idx, val) {
                            if ($("#panel_" + val.panelCode)) {
                                $("#panel_" + val.panelCode).html(val.panelData);
                            }
                        });

                        // 팝업 열지 않기
                        $.each($.cookie(), function (idx, val) {
                            var prefix = idx.split('_');
                            if (prefix[0] == 'adminPanel') {
                                var popupId = idx.replace('adminPanel_', '');
                                if ($('#' + popupId)) {
                                    $('#' + popupId).hide();
                                }
                            }
                        });
                        break;

                    case 'popupCos' :
                        $.each(value, function (idx, val) {
                            var panelCode = val.panelCode;
                            var panelData = val.panelData;
                            var panelSelector = "#panel_" + index + "_" + panelCode;
                            var panel = $(panelSelector);

                            if (panel.length && panelData.posts.length > 0) {
                                _hasPopupCos = true;
                                // 다시 보지 않기 쿠키가 유효하지 않은 경우 제거 후 재노출 (신규 콘텐츠 추가, 쿠키 유지기간 사용안함 전환)
                                removeStaleAdminPanelCookie(panelSelector, panelData);

                                // 패널 style sheet 로드
                                panel.html('<style>' + panelPopupStyles(panelSelector, panelCode, panelData) + '</style>');

                                // slick 미로드 시 동적 로드 후 팝업 생성 (쿠키 체크 포함)
                                loadSlickAndCreatePopup(val.gdSharePath, panel, panelCode, panelData);
                            }
                        });

                        // 팝업 열지 않기
                        $.each($.cookie(), function (idx, val) {
                            var prefix = idx.split('_');
                            if (prefix[0] == 'adminPanel') {
                                var popupId = idx.replace('adminPanel_', '');
                                if ($('#' + popupId)) {
                                    $('#' + popupId).hide();
                                }
                            }
                        });
                        break;

                    case 'kakaoAlrim' :
                        $.each(value, function (idx, val) {
                            var panelCode = val.panelCode;
                            var panelData = val.panelData;
                            var panelSelector = "#panel_" + index + "_" + panelCode;
                            var panel = $(panelSelector);

                            if (panel.length) {
                                // 패널 style sheet 로드
                                panel.html('<style>' + panelPopupStyles(panelSelector, panelCode, panelData) + '</style>');

                                // 팝업 생성
                                addAdminPopupPanel(panel, panelCode, panelData);
                            }
                        });

                        // 팝업 열지 않기
                        $.each($.cookie(), function (idx, val) {
                            var prefix = idx.split('_');
                            if (prefix[0] == 'adminPanel') {
                                var popupId = idx.replace('adminPanel_', '');
                                if ($('#' + popupId)) {
                                    $('#' + popupId).hide();
                                }
                            }
                        });
                        break;
                }
            });
            if (!_hasPopupCos) {
                $(document).trigger('adminPanelApiComplete');
            }
        },
        error: function (data, text) {
            //alert('error : ' + text);
            $(document).trigger('adminPanelApiComplete');
        }
    });

    if (params['menuCode'] == 'base' && params['menuFile'] == 'index') {
        if(!$.cookie('adminPanel_pg_register_pop')) {
            $.ajax({
                method: 'POST',
                cache: false,
                url: '/share/layer_pg_register.php',
                data: params,
                async: true,
                dataType: 'text',
                success: function (data) {
                    if (data == null) {
                        return;
                    }
                    $("#panel_pgPanel").append(data);
                },
                error: function (e) {
                    console.error('Failed to get layer_pg_register : ', e);
                }
            });
        }
        $.ajax({
            method: 'POST',
            cache: false,
            url: '/share/layer_super_admin_commerce_login.php',
            data: params,
            async: true,
            dataType: 'json',
            success: function (data) {
                if (data === null || typeof data == 'undefined') {
                    return;
                }
                $("#panel_login_notice_panel").append(data.result);
            },
            error: function (e) {
                console.error('Failed to get layer_super_admin_commerce_login : ', e);
            }
        });
        $.ajax({
            method: 'POST',
            cache: false,
            url: '/share/layer_super_admin_security.php',
            data: params,
            async: true,
            dataType: 'text',
            success: function (data) {
                if (data === null || typeof data == 'undefined') {
                    return;
                }
                $("#panel_noticePanel").append(data);
            },
            error: function (data, text) {
                //alert('error : ' + text);
            }
        });

        if (!$.cookie('adminPanel_popupNotice-pop_ssl_endDate')) {
            $.ajax({
                method: 'GET',
                cache: false,
                url: '/share/layer_ssl_end_date.php',
                async: true,
                dataType: 'text',
                success: function (data) {
                    if (data === null) {
                        return;
                    }
                    $("#panel_ssl_noticePanel").append(data);
                }
            });
        }

        // 이나무 체크리스트 이전 팝업
        let today = new Date().toISOString().slice(0, 10);
        let migrationChecklistTodayNotVisible = localStorage.getItem('migrationChecklistTodayNotVisible');
        let migrationChecklistPopupNotVisible = localStorage.getItem('migrationChecklistPopupNotVisible');
        if (migrationChecklistTodayNotVisible != today && migrationChecklistPopupNotVisible != 'true') {
            $.ajax({
                url: '/share/layer_check_list_migration.php',
                method: 'GET',
                dataType: 'text',
                success: function(data) {
                    var dialog = new BootstrapDialog({
                        message: data,
                        title: '이전 체크리스트',
                        size: 'normal',
                        backdrop: false,
                        closeByBackdrop: false,
                        onshow: function(dialog) {
                            dialog.getModal().appendTo('#header');
                            dialog.getModalDialog().css({
                                position: 'fixed',
                                left: '20px',
                                top: '20px',
                                right: 'auto',
                                bottom: 'auto',
                                margin: 0,
                                zIndex: 1001
                            });
                            dialog.getModal().css({zIndex: 1000});
                            dialog.getModal().removeClass('modal');

                        },
                        onshown: function(dialog) {
                            document.body.classList.remove('modal-open');
                        }
                    });

                    dialog.realize();
                    dialog.getModal().data('bs.modal').options.backdrop = false;
                    dialog.open();
                }
            });
        }
    }
}

/**
 * 관리자 패널 팝업창 Cookie 생성
 * @param string name 팝업창 이름 (코드_창종류)
 * @param int expireDay 쿠키 기간
 * @param object elemnt elemnt
 * @param string value 쿠키 값 (미지정 시 'true')
 * @return
 */
function adminPanelCookie(name, expireDay, elemnt, value) {
    if (expireDay == '') {
        expireDay = 7;
    }

    var cookieName = 'adminPanel_' + name.replace('#', '');

    $.cookie(cookieName, value || 'true', {expires: expireDay, path: '/'});
    setTimeout("$(name).hide()");

    return;
}

function edu_panel(panel_data) {
    var result = [];
    var li_by_data = $(panel_data).find('li:lt(2)');
    try {
        result.push('<div class="edunews-items">');
        result.push('<ul>');
        $.each(li_by_data, function (idx, item) {
            var a_by_item = $(item).find('a');
            var img_by_item = $(item).find('img');
            result.push('<li><a href="' + a_by_item.attr('href') + '" target="_blank">');
            result.push('<div class="edunews-head">');
            if (img_by_item.length > 0) {
                result.push('<img src="' + img_by_item.attr('src') + '">');
            }
            result.push('</div>');
            result.push('<div class="edunews-body">');
            result.push('<div class="edunews-title">' + $(item).find('.edunews-title').text() + '</div>');
            result.push('<div class="edunews-date">' + $(item).find('.edunews-date').text() + '</div>');
            result.push('</div>');
            result.push('</a></li>');
        });
        result.push('</ul>');
        result.push('</div>');
    } catch (e) {
        return panel_data;
    }
    return result.join('');
}

function renderUnifiedNoticeUpdate(items, gdSharePath) {
    const panel = $("#panel_board_noticeUpdateAPI");
    if (!panel.length) return;

    items.sort(function (a, b) {
        if (!!a.isTopFixed !== !!b.isTopFixed) {
            return a.isTopFixed ? -1 : 1; // 상단 고정 공지 먼저
        }
        return (Date.parse(b.publishedAt) || 0) - (Date.parse(a.publishedAt) || 0); // 그 다음 최신 날짜 우선 (NaN 가드)
    });

    panel.empty();
    items.forEach(function (item, idx) {
        const $li = $('<li>')
            .attr('data-notice-type', item.tabKey)
            .attr('data-overall-index', idx);
        $('<span>')
            .addClass('board-badge ' + item.badgeClass)
            .text(item.badgeLabel)
            .appendTo($li);
        const $a = $('<a>')
            .attr('href', item.url)
            .attr('target', '_blank')
            .appendTo($li);

        if (item.isTopFixed) {
            $a.addClass('bold');
        }
        $('<span>')
            .addClass('ellipsis')
            .text(item.title)
            .appendTo($a);
        if (item.isNewPost) {
            $a.append(' ');
            $('<img>')
                .attr('src', gdSharePath + 'img/icon_new.png')
                .attr('alt', 'NEW')
                .addClass('img-fix')
                .appendTo($a);
        }
        $('<span>')
            .addClass('published-at')
            .text(item.publishedAt)
            .appendTo($li);
        panel.append($li);
    });

    bindNoticeTabHandler();
    applyNoticeTabFilter(panel, 'all');
}

function applyNoticeTabFilter(list, key) {
    const UNIFIED_NOTICE_ALL_LIMIT = 8; // '전체' 탭에 노출할 최대 항목 수

    if (key === 'all') {
        list.find('li').each(function () {
            const idx = parseInt($(this).attr('data-overall-index'), 10);
            if (idx < UNIFIED_NOTICE_ALL_LIMIT) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    } else {
        list.find('li').hide();
        list.find('li[data-notice-type="' + key + '"]').show();
    }
}

function bindNoticeTabHandler() {
    const tabs = $('.board-notice-tabs');
    if (!tabs.length) return;

    // 기존 핸들러 제거 후 재바인딩 (중복 방지)
    tabs.off('click', '[data-notice-tab]');
    tabs.on('click', '[data-notice-tab]', function (e) {
        e.preventDefault();
        const tab = $(this);
        const key = tab.attr('data-notice-tab');
        tabs.find('button').removeClass('is-active');
        tab.addClass('is-active');
        applyNoticeTabFilter($('#panel_board_noticeUpdateAPI'), key);
        applyMoreLinkSwap(key);
    });
}

/**
 * 통합 탭(전체/공지/업데이트) 클릭 시 visible 더보기 버튼의 href 를 해당 탭의 URL 로 교체.
 *
 * 동작:
 *  1. visible: #panel_link_noticeLink (화면에 노출되는 더보기 a 태그)
 *  2. hidden 슬롯: #panel_link_patchLink, #panel_link_allLink (URL 보관용)
 *  3. 첫 호출에서 visible 의 원본 href (noticeLink URL) 를 originalHref 에 백업 — 공지 탭 복원용
 *  4. key 에 따라 URL 선택: update → patchLink, all → allLink, notice/그 외 → 보존된 원본
 *
 * @param {string} key 탭 키 ('all' | 'notice' | 'update')
 */
function applyMoreLinkSwap(key) {
    const more = $('#panel_link_noticeLink a');
    if (!more.length) return;
    const patch = $('#panel_link_patchLink a');
    const all = $('#panel_link_allLink a');

    // 첫 호출 시 noticeLink 원본 URL 보존 (이후 swap 으로 덮여도 공지 탭에서 복원 가능)
    if (!more.data('originalHref')) {
        more.data('originalHref', more.attr('href'));
    }

    let href = more.data('originalHref'); // default: notice 탭 (원본 noticeLink URL)
    if (key === 'update' && patch.length) {
        href = patch.attr('href');
    } else if (key === 'all' && all.length) {
        href = all.attr('href');
    }
    more.attr('href', href);
}

function add_new_mark(gd_share_path, panel_data) {
    var li_by_mark = $(panel_data);
    var start = moment().subtract(7, 'days').format('YYYY-MM-DD');
    var end = moment().add(1, 'days').format('YYYY-MM-DD');
    $.each(li_by_mark, function (idx, item) {
        $.each($(item).find('li'), function (idx2, item2) {
            var date = $(item2).find('span').text();
            //console.log(start, end, date);
            var a_tag = $(item2).find('a');
            if (a_tag.text().length > 30) {
                a_tag.text(a_tag.text().str_cut(50));
            }
            if (moment(date).isBetween(start, end)) {
                a_tag.append(' <img src="' + gd_share_path + 'img/icon_new.png" alert="NEW" class="img-fix">');
            }
        });
    });
    return li_by_mark.html();
}

function panelPopupStyles(panel, panelCode, panelData) {
    var actionBoxHeight = 52; // 52은 action박스 고정 높이
    var zIndex = panelCode === 'modal' ? 1003 : 1000; // panelCode에 따라 z-index 설정

    // 위치 조합 확인 및 값 설정
    var isTopLeft = panelData.positionTop && panelData.positionLeft;
    var isBottomRight = !isTopLeft && panelData.positionBottom && panelData.positionRight;

    // transform 값 설정 (% 단위일 때만 중앙 정렬용 transform 적용, px일 때는 transform 없음)
    var posTopVal = removeUnderscore(panelData.positionTop || '');
    var posLeftVal = removeUnderscore(panelData.positionLeft || '');
    var posBotVal = removeUnderscore(panelData.positionBottom || '');
    var posRightVal = removeUnderscore(panelData.positionRight || '');

    var transformStr = 'none';
    if (isTopLeft && posTopVal.indexOf('%') > -1 && posLeftVal.indexOf('%') > -1) {
        transformStr = `translate(-${posLeftVal}, -${posTopVal})`;
    } else if (isBottomRight && posBotVal.indexOf('%') > -1 && posRightVal.indexOf('%') > -1) {
        transformStr = `translate(${posRightVal}, ${posBotVal})`;
    }

    var styles = `
        ${panel} .popup_container {
            -webkit-font-smoothing: antialiased;
            position: fixed;
            display: inline-block;
            width: ${panelData.width}px;
            height: ${panelData.height + actionBoxHeight}px;
            
            ${isTopLeft ? `top: ${posTopVal};` : ''}
            ${isTopLeft ? `left: ${posLeftVal};` : ''}
            ${isBottomRight ? `bottom: ${posBotVal};` : ''}
            ${isBottomRight ? `right: ${posRightVal};` : ''}

            transform: ${transformStr};
            background-color: rgb(255, 255, 255);
            border-radius: 12px;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 8px;
            overflow: hidden;
            z-index: ${zIndex};
            font-family: Noto Sans KR, Roboto, sans-serif;
        }
        
        ${panel} .popup_slider {
            height: ${panelData.height}px;
        }
        
        ${panel} .slick-slide {
            height: ${panelData.height}px;
        }
        
        ${panel} .slick-slide a{
           outline: none;
        }
        
        ${panel} .slick-slide p{
            font-size: 16px;
            line-height: 1.5;
            margin: 0;
        }

        ${panel} .controls {
            display: flex;
            padding: 0 16px;
            height: ${actionBoxHeight}px;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: #0C111D;
        }
        
        ${panel} .gray-text {
            color: #98A2B3;
        }
        
        ${panel} .slide-pagination
        {
            display: flex;
            gap: 12px;
            justify-content: space-between;
            align-items: center;
        }
        
        ${panel} .slide-count {
            display: flex;
            gap: 2px;
        }
        
        ${panel} .slide-pagination-arrow {
            display: inline-block;
            cursor: pointer;
            width: 36px;
            height: 36px;
            line-height: 36px;
            text-align: center;
        }

        ${panel} .actionBox {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        ${panel} input[type="checkbox"] {
            left: 18px;
            top: 2px;
        }

        ${panel} .button {
            cursor: pointer;
            width: 53px;
            height: 36px;
            border: 1px solid #D0D5DD;
            background: #FFF;
            border-radius: 8px;
            font-weight: 700;
        }
        
        ${panel} label {
            color: #344054;
            font-weight: 500;
        }
    `;

    return styles;
}

function addAdminPopupPanel(panel, panelCode, panelData) {
    if (panelData.additionalInput) {
        Object.assign(panelData, parseAdditionalInput(panelData.additionalInput));
    }

    var modalContainer = $('<div class="popup_container"></div>');

    // slider 추가
    var sliderContainer = addAdminPopupPanelSlides(panel, panelData);

    // control 추가
    var controls = addAdminPopupPanelControls(panel, panelCode, panelData);

    modalContainer.append(sliderContainer);
    modalContainer.append(controls);
    panel.append(modalContainer);

    if (panelData.isDimmed) {
        panel.append($('<div class="modal-background"></div>').css({
                position: 'fixed',
                top: '0',
                left: '0',
                width: '100%',
                height: '100%',
                background: 'rgba(0, 0, 0, 0.5)',
                zIndex: '1002',
                display: 'block'
            })
        );
    }

    // Slick 슬라이더 초기화
    sliderContainer.slick({
        infinite: panelData.infinite,
        autoplay: panelData.autoplay,
        autoplaySpeed: panelData.delay,
        prevArrow: panel.find('.slide-pagination-prev'),
        nextArrow: panel.find('.slide-pagination-next')
    });
}

function addAdminPopupPanelSlides(panel, panelData) {
    var sliderContainer = $('<div class="popup_slider"></div>');

    panelData.posts.forEach(function (post) {
        sliderContainer.append(`
            <div class='popup'>
                ${post.postBodyText}
            </div>
        `);
    });

    sliderContainer.on('init reInit afterChange', function (event, slick, currentSlide) {
        var current = (currentSlide || 0) + 1;
        var totalSlides = slick.slideCount;
        var slideInfo = `${current} <span class="gray-text"> / ${totalSlides}</span>`;
        panel.find('.slide-count').html(slideInfo);
    });

    return sliderContainer;
}

function addAdminPopupPanelControls(panel, panelCode, panelData) {
    var cookieAliveDay = panelData.cookieAliveDay;
    var alwaysShow = cookieAliveDay == null;
    var cookieLabel = alwaysShow
        ? ''
        : (Number(cookieAliveDay) === 0 ? '다시 보지 않기' : cookieAliveDay + '일간 다시 보지 않기');

    var controls = $(`
        <div class="controls">
            <div class="slide-pagination">
                <div class="slide-pagination-arrow slide-pagination-prev">
                    <img src="/admin/gd_share/img/btn_chevron_left.png">
                </div>
                <div class="slide-count"></div>
                <div class="slide-pagination-arrow slide-pagination-next">
                    <img src="/admin/gd_share/img/btn_chevron_right.png">
                </div>
            </div>
            <div class="actionBox">
                ${!alwaysShow ? `<div class="checkbox">
                    <input type="checkbox" id="${panelCode}_notShowAgain">
                    <label for="${panelCode}_notShowAgain">${cookieLabel}</label>
                </div>` : ''}
                <button class="button">닫기</button>
            </div>
        </div>
    `);

    // 닫기 버튼에 이벤트 리스너 추가
    controls.find('.button').on('click', function () {
        closeAdminPopupPanel(panel, panelCode, panelData);
    });

    if (!alwaysShow) {
        controls.find(`#${panelCode}_notShowAgain, label[for="${panelCode}_notShowAgain"]`).on('click', function () {
            closeAdminPopupPanel(panel, panelCode, panelData);
        });
    }

    return controls;
}

function closeAdminPopupPanel(panel, panelCode, panelData) {
    if (panelData.isDimmed === 'true') {
        $('.modal-background').hide();
    }

    // 체크박스 확인
    var checkboxSelector = panel.selector+` #${panelCode}_notShowAgain`;

    if ($(checkboxSelector).is(':checked')) {
        var expireDay = Number(panelData.cookieAliveDay) === 0 ? 36500 : Number(panelData.cookieAliveDay);
        adminPanelCookie(panel.selector, expireDay, this, getAdminPanelPostNos(panelData));
    }

    $(panel.selector).hide();
    $(document).trigger('adminPanelPopupClosed');
}

/**
 * 다시 보지 않기 쿠키에 저장할 postNo 목록 반환
 * @param object panelData 패널 데이터
 * @return string postNo 목록 (postNo가 없으면 'true')
 */
function getAdminPanelPostNos(panelData) {
    var postNos = panelData.posts
        .filter(function (post) { return post.postNo != null; })
        .map(function (post) { return post.postNo; });

    return postNos.length > 0 ? postNos.join(',') : 'true';
}

/**
 * 다시 보지 않기 쿠키가 유효하지 않은 경우 제거 (팝업 재노출)
 * - 쿠키 유지기간을 '사용안함'으로 변경한 경우
 * - 다시 보지 않기 이후 신규 콘텐츠(postNo)가 추가된 경우
 * @param string panelSelector 패널 selector
 * @param object panelData 패널 데이터
 * @return
 */
function removeStaleAdminPanelCookie(panelSelector, panelData) {
    var cookieName = 'adminPanel_' + panelSelector.replace('#', '');
    var cookieValue = $.cookie(cookieName);

    if (!cookieValue) {
        return;
    }

    // 쿠키 유지기간 사용안함 전환 시 강제 재노출
    if (panelData.cookieAliveDay == null) {
        $.removeCookie(cookieName, {path: '/'});
        return;
    }

    if (cookieValue === 'true') {
        return;
    }

    var seenPostNos = cookieValue.split(',');
    var hasNewPost = panelData.posts.some(function (post) {
        return post.postNo != null && seenPostNos.indexOf(String(post.postNo)) === -1;
    });

    if (hasNewPost) {
        $.removeCookie(cookieName, {path: '/'});
    }
}

function parseAdditionalInput(input) {
    var params = {};

    if (input) {
        input.split('&').forEach(function (part) {
            var item = part.split('=');
            params[item[0]] = item[1];
        });
    }

    // 자동 롤링 설정
    params.autoplay = params.autoplay === 'true';
    // 딤드 처리 여부
    params.isDimmed = params.isDimmed === 'true';
    // 딜레이 설정 (밀리초 단위로 변환)
    params.delay = parseInt(params.delay) || 5000;
    // 무한 롤링 설정
    params.infinite = params.infinite === 'true';

    return params;
}

// slick 미로드 시 동적 로드 후 팝업 생성
function loadSlickAndCreatePopup(gdSharePath, panel, panelCode, panelData) {
    if (typeof $.fn.slick !== 'undefined') {
        addAdminPopupPanel(panel, panelCode, panelData);
        $(document).trigger('adminPanelApiComplete');
        return;
    }

    var slickBasePath = gdSharePath + 'script/slider/slick/';
    $('head').append('<link rel="stylesheet" href="' + slickBasePath + 'slick.css">');
    $.getScript(slickBasePath + 'slick.min.js', function () {
        addAdminPopupPanel(panel, panelCode, panelData);
        $(document).trigger('adminPanelApiComplete');
    });
}

// 위치값 정제 함수 (ex. 50_% => 50 , %)
function removeUnderscore(value) {
    if (typeof value === 'string') {
        return value.replace("_", "");
    }
    return '0';
}
