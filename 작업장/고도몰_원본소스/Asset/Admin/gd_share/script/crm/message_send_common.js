// 필요 전역: messageConfig, window.messageInput, window.alternativeMessageInput, window.carouselState,
//   window.shortLinkKeys, window.shortLinkAlternativeKeys, MobileMessageReplaceCode, NCDSValidator
function extractShortLinks(contents, isMain = true) {
    const shortLinkKeys = isMain ? window.shortLinkKeys : window.shortLinkAlternativeKeys;
    const shortLinks = [];
    shortLinkKeys.forEach((url, key) => {
        if (contents.includes(`{${key}}`)) {
            shortLinks.push({ key, url });
        }
    });
    return shortLinks;
}

function extractReplaceKeys(str, isKakao = false) {
    if (!str) return [];

    const regex = isKakao ? /#\{([^}]+)\}/g : /\{([^}]+)\}/g;
    const matchedKeys = [...str.matchAll(regex)].map(m => m[1]);

    const allReplaceCodeKeys = [
        MobileMessageReplaceCode.CRM_RECIPE,
        MobileMessageReplaceCode.MEMBER,
        MobileMessageReplaceCode.GOODS,
        MobileMessageReplaceCode.ORDER,
        MobileMessageReplaceCode.PROMOTION,
        MobileMessageReplaceCode.BOARD,
        MobileMessageReplaceCode.REGULAR,
        MobileMessageReplaceCode.PRESENT,
    ].flatMap(category => MobileMessageReplaceCode.getAllKeys(category));

    return matchedKeys.filter(key =>
        allReplaceCodeKeys.includes(MobileMessageReplaceCode.normalizeReplaceKey(key)) || key.startsWith('short_link_')
    );
}

function convertReplaceKeysToSmsReplaceKeys(content, replaceKeys) {
    if (!content || !replaceKeys.length) return content;
    return replaceKeys.reduce((result, key) => {
        const pattern = new RegExp('(?<!\\$)\\{' + key + '\\}', 'g');
        return result.replace(pattern, '${' + key + '}');
    }, content);
}

function generateSmsLmsRequest() {
    const use080Reject = document.querySelector('input[name="use080Reject"]').checked;
    const mainContents = window.messageInput.getValue() ?? '';
    const trackingLink = extractShortLinks(mainContents);
    const replaceKeys = extractReplaceKeys(mainContents).filter(key => !trackingLink.some(link => link.key === key));
    const convertedContents = convertReplaceKeysToSmsReplaceKeys(mainContents, [...replaceKeys, ...trackingLink.map(link => link.key)]);
    return {
        isAdText: use080Reject,
        trackingLink: trackingLink,
        replaceKeys: replaceKeys,
        title: null,
        content: convertedContents
    }
}

function generateKakaoFriendtalkRequest() {
    const campaignMessageType = document.querySelector('input[name="campaignMessageType"]:checked')?.value ?? 'TEXT';
    const isCarouselFeed = campaignMessageType === 'CAROUSEL_FEED';
    const carouselData = isCarouselFeed ? carouselState.getCarouselsData() : null;

    const mainContents = window.messageInput.getValue() ?? '';
    let trackingLink = isCarouselFeed ? carouselData.trackingLink : extractShortLinks(mainContents);
    let replaceKeys = (isCarouselFeed ? carouselData.replaceKeys : extractReplaceKeys(mainContents, true)).filter(key => !trackingLink.some(link => link.key === key));

    if (campaignMessageType === 'WIDE_ITEM_LIST') {
        const campaignHeader = document.querySelector('input[name="friendtalkCampaignHeader"]')?.value || null;
        const headerReplaceKeys = extractReplaceKeys(campaignHeader, true).filter(key => !trackingLink.some(link => link.key === key));
        replaceKeys = [...replaceKeys, ...headerReplaceKeys];
    }

    const alternativeContents = window.alternativeMessageInput.getValue() ?? '';
    const alternativeTrackingLink = extractShortLinks(alternativeContents, false);
    const alternativeReplaceKeys = extractReplaceKeys(alternativeContents).filter(key => !alternativeTrackingLink.some(link => link.key === key));
    const convertedAlternativeContents = convertReplaceKeysToSmsReplaceKeys(alternativeContents, [...alternativeReplaceKeys, ...alternativeTrackingLink.map(link => link.key)]);

    const use080Reject = document.querySelector('input[name="use080Reject"]').checked;

    return {
        campaignName: document.querySelector('input[name="friendtalkCampaignName"]')?.value ?? '',
        type: campaignMessageType,
        content: isCarouselFeed ? null : mainContents,
        replaceKeys: [...new Set([...replaceKeys, ...alternativeReplaceKeys])],
        buttons: isCarouselFeed ? null : collectFriendtalkLinkButtons(),
        coupon: isCarouselFeed ? null : collectFriendtalkCouponButton(),
        carousels: carouselData?.carousels,
        header: isCarouselFeed ? null : (document.querySelector('input[name="friendtalkCampaignHeader"]')?.value || null),
        items: collectFriendtalkItems(),
        imageLink: isCarouselFeed ? null : (document.querySelector('input[name="friendtalkCampaignImageLink"]')?.value.replace(/^\s+|\s+$/g, '') || null),
        imageUrl: isCarouselFeed ? null : (document.querySelector('input[name="friendtalkCampaignImageUrl"]')?.value.replace(/^\s+|\s+$/g, '') || null),
        linkPlatformType: messageConfig.kakaoFriendTalk.linkPlatformType,
        alternativeInfo: isCarouselFeed ? null : {
            title: null,
            content: convertedAlternativeContents,
            replaceKeys: alternativeReplaceKeys,
            isAdText: use080Reject,
            trackingLink: alternativeTrackingLink
        },
        trackingLink: trackingLink
    }
}

function collectFriendtalkLinkButtons() {
    const linkButtonTableBody = document.querySelector('#linkButtonTableBody');
    const buttons = [];
    if (linkButtonTableBody) {
        linkButtonTableBody.querySelectorAll('tr').forEach(row => {
            const buttonName = row.querySelector('.ncua-link-button-name').value;
            const webUrlInput = row.querySelector('input[name="linkButtonWebUrl"]')?.value;
            const iosUrlInput = row.querySelector('input[name="linkButtonIOSUrl"]')?.value;
            const aosUrlInput = row.querySelector('input[name="linkButtonAOSUrl"]')?.value;

            const linkPlatformType = messageConfig.kakaoFriendTalk.linkPlatformType;
            const isMobileApp = linkPlatformType === 'MOBILE_APP';

            buttons.push({
                name: buttonName.replace(/^\s+|\s+$/g, ''),
                url: {
                    mobileUrl: isMobileApp ? null : (webUrlInput?.replace(/^\s+|\s+$/g, '') || ''),
                    iosUrl: !isMobileApp ? null : (iosUrlInput?.replace(/^\s+|\s+$/g, '') || ''),
                    aosUrl: !isMobileApp ? null : (aosUrlInput?.replace(/^\s+|\s+$/g, '') || '')
                }
            });
        });
    }

    return buttons.length > 0 ? buttons : null;
}

function collectFriendtalkCouponButton() {
    const couponTable = document.querySelector('#kakaoFriendTalkcouponTable');
    if (!couponTable) return null;

    const friendtalkCouponNo = document.querySelector('input[name="friendtalkCouponNo"]')?.value;
    if (!friendtalkCouponNo) return null;

    const titleInput = couponTable.querySelector('input[name="couponTitle"]');
    const descriptionInput = couponTable.querySelector('input[name="couponDescription"]');
    const webLinkInput = couponTable.querySelector('input[name="couponUrl"]');

    return {
        couponNo: parseInt(friendtalkCouponNo) || null,
        title: titleInput?.value?.replace(/^\s+|\s+$/g, '') || '',
        description: descriptionInput?.value?.replace(/^\s+|\s+$/g, '') || '',
        url: webLinkInput?.value?.replace(/^\s+|\s+$/g, '') || ''
    };
}

function collectFriendtalkItems() {
    const items = [];
    document.querySelectorAll('.js-kakao-friendtalk-itemlist-section tbody tr').forEach(row => {
        const itemKey = row.dataset.itemKey;
        const titleInput = row.querySelector('input[name="itemTitle"]');
        const urlInput = row.querySelector('input[name="itemWebLink"]');
        const imageUrlInput = row.querySelector('input[name="itemImageUrl"]');

        if (titleInput && titleInput.value.replace(/^\s+|\s+$/g, '')) {
            items.push({
                key: itemKey,  // 서버에서 파일 매칭용
                title: titleInput.value.replace(/^\s+|\s+$/g, ''),
                imageUrl: imageUrlInput?.value || null,  // 업로드된 imageUrl 사용
                url: urlInput?.value?.replace(/^\s+|\s+$/g, '') || ''
            });
        }
    });

    return items.length > 0 ? items : null;
}

function generateKakaoAlimtalkRequest() {
    const mainContents = document.querySelector('textarea[name="messageContent"]').value ?? '';
    const alternativeContents = window.alternativeMessageInput.getValue() ?? '';
    const alternativeTrackingLink = extractShortLinks(alternativeContents, false);
    const alternativeReplaceKeys = extractReplaceKeys(alternativeContents).filter(key => !alternativeTrackingLink.some(link => link.key === key));
    const convertedAlternativeContents = convertReplaceKeysToSmsReplaceKeys(alternativeContents, [...alternativeReplaceKeys, ...alternativeTrackingLink.map(link => link.key)]);

    const use080Reject = document.querySelector('input[name="use080Reject"]').checked;
    return {
        templateCode: document.querySelector('select[name="selectKakaoAlimtalkTemplate"]').value,
        title: null,
        content: mainContents,
        replaceKeys: [...new Set([...extractReplaceKeys(mainContents, true), ...alternativeReplaceKeys])],
        alternativeInfo: {
            title: null,
            isAdText: use080Reject,
            content: convertedAlternativeContents,
            replaceKeys: alternativeReplaceKeys,
            trackingLink: alternativeTrackingLink
        }
    }
}

function generateMyappRequest() {
    const mainContents = window.messageInput.getValue() ?? '';
    const alternativeContents = window.alternativeMessageInput.getValue() ?? '';
    const alternativeTrackingLink = extractShortLinks(alternativeContents, false);
    const alternativeReplaceKeys = extractReplaceKeys(alternativeContents).filter(key => !alternativeTrackingLink.some(link => link.key === key));
    const convertedAlternativeContents = convertReplaceKeysToSmsReplaceKeys(alternativeContents, [...alternativeReplaceKeys, ...alternativeTrackingLink.map(link => link.key)]);

    const use080Reject = document.querySelector('input[name="use080Reject"]').checked;

    const myappSendCondition = document.querySelector('input[type="radio"][name="myappSendCondition"]:checked').value ?? '';
    const myappNotificationType = document.querySelector('input[type="radio"][name="myappNotificationType"]:checked').value ?? '';
    const myappTitle = document.querySelector('input[name="myappTitle"]').value ?? '';
    const myappUnsubscribeGuide = document.querySelector('input[name="myappUnsubscribeGuide"]').value ?? '';
    const pushUrl = document.querySelector('#mobileUrl').innerText + document.querySelector('input[name="myappPushPath"]')?.value;
    const platforms = { ALL: ['AOS', 'IOS'], ANDROID: ['AOS'], IOS: ['IOS'] }[document.querySelector('input[name="myappPlatform"]:checked')?.value] ?? null;
    return {
        sendCondition: myappSendCondition,
        notificationType: myappNotificationType,
        platforms: platforms,
        title: myappTitle,
        content: mainContents,
        unsubscribeGuide: myappUnsubscribeGuide,
        pushUrl: pushUrl,
        imageUrl: document.querySelector('input[name="myappImageUrl"]')?.value || null,
        replaceKeys: [...new Set([...extractReplaceKeys(mainContents), ...alternativeReplaceKeys])],
        alternativeInfo: {
            isAdText: use080Reject,
            content: convertedAlternativeContents,
            replaceKeys: alternativeReplaceKeys,
            trackingLink: alternativeTrackingLink
        }
    }
}

function generateSendMethodRequest() {
    const sendMethod = document.querySelector('input[name="sendMethod"]:checked').value;
    let sendMethodRequest = {};
    switch (sendMethod) {
        case 'SMS':
            sendMethodRequest.smsLmsRequest = generateSmsLmsRequest();
            break;
        case 'FRIENDTALK':
            sendMethodRequest.kakaoFriendtalkRequest = generateKakaoFriendtalkRequest();
            break;
        case 'ALIMTALK':
            sendMethodRequest.kakaoAlimtalkRequest = generateKakaoAlimtalkRequest();
            break;
        case 'MYAPP':
            sendMethodRequest.myappRequest = generateMyappRequest();
            break;
        default:
            break;
    }

    return sendMethodRequest;
}

function validateSendMethodContent() {
    const sendMethod = document.querySelector('input[name="sendMethod"]:checked').value;
    switch (sendMethod) {
        case 'SMS':
            const r = validateMessageInput();
            if (!r.success) return r;
            break;
        case 'FRIENDTALK':
            const campaignMessageType = document.querySelector('input[name="campaignMessageType"]:checked')?.value;
            switch (campaignMessageType) {
                case 'TEXT':
                    for (const r of [validateMessageInput(), validateCampaignName(), validateLinkButtons(), validateCoupon()]) {
                        if (!r.success) return r;
                    }
                    break;
                case 'IMAGE':
                    for (const r of [validateMessageInput(), validateCampaignName(), validateCampaignImage(), validateCampaignImageLink(), validateLinkButtons(), validateCoupon()]) {
                        if (!r.success) return r;
                    }
                    break;
                case 'WIDE_IMAGE':
                    for (const r of [validateMessageInput(), validateMessageContainsLink(campaignMessageType), validateCampaignName(), validateCampaignImage(), validateCampaignImageLink(), validateMessageContainsUrl(), validateLinkButtons(), validateCoupon()]) {
                        if (!r.success) return r;
                    }
                    break;
                case 'WIDE_ITEM_LIST':
                    for (const r of [validateCampaignName(), validateLinkButtons(), validateCoupon(), validateItemList()]) {
                        if (!r.success) return r;
                    }
                    break;
                case 'CAROUSEL_FEED':
                    window.carouselState.saveCurrentCarouselData();
                    for (const r of [validateCampaignName(), validateCarousel()]) {
                        if (!r.success) return r;
                    }
                    break;
            }
            break;
        case 'MYAPP':
            for (const r of [validateMessageInput(), validateMyappPushTitle(), validateUnsubscribeGuide(), validateMyappPushUrlCheck()]) {
                if (!r.success) return r;
            }
            break;
    }

    return {success: true};
}

function validateCarousel() {
    let slideIndex = 1;
    for (let index = 0; index < window.carouselState.carousels.length; index++) {
        const carousel = window.carouselState.carousels[index];
        if (carousel.isDeleted) continue;

        if (!carousel.imageUrl) {
            window.carouselState.setActiveIndex(index);
            const response = validateCampaignImage(slideIndex);
            if (!response.success) return response;
        }

        if (!carousel.imageLink || !(/^https?:\/\//.test(carousel.imageLink))) {
            window.carouselState.setActiveIndex(index);
            const response = validateCampaignImageLink(slideIndex);
            if (!response.success) return response;
        }

        if (!carousel.message.replace(/^\s+|\s+$/g, '')) {
            window.carouselState.setActiveIndex(index);
            const response = validateMessageInput(index, slideIndex);
            if (!response.success) return response;
        } else {
            const response = validateMessageContainsLink('CAROUSEL_FEED', index, slideIndex);
            if (!response.success) return response;
        }

        if (carousel.buttons.length > 0) {
            const urlPattern = /^https?:\/\//;
            let shouldValidate = false;
            for (const button of carousel.buttons) {
                if (!button.name) shouldValidate = true;
                const { iosUrl, aosUrl, mobileUrl } = button.url;
                if (!shouldValidate) {
                    if (iosUrl || aosUrl) {
                        shouldValidate = !iosUrl || !aosUrl || !urlPattern.test(iosUrl) || !urlPattern.test(aosUrl);
                    } else {
                        shouldValidate = !mobileUrl || !urlPattern.test(mobileUrl);
                    }
                }
            }

            if (shouldValidate) {
                window.carouselState.setActiveIndex(index);
                const response = validateLinkButtons(slideIndex);
                if (!response.success) return response;
            }
        }

        if (carousel.coupon) {
            if (!carousel.coupon.title || !carousel.coupon.description || !carousel.coupon.url || !(/^https?:\/\//.test(carousel.coupon.url))) {
                const response = validateCoupon(slideIndex);
                if (!response.success) return response;
            }
        }
        slideIndex++;
    }
    return {success: true};
}

function validateMessageInput(carouselIndex = null, carouselSlideIndex = null) {
    const messageContent = carouselIndex !== null ? window.carouselState.carousels[carouselIndex].message.replace(/^\s+|\s+$/g, '') : window.messageInput.getValue().replace(/^\s+|\s+$/g, '');
    if (!messageContent) {
        if (carouselIndex !== null) {
            window.carouselState.setActiveIndex(carouselIndex);
        }
        NCDSValidator.highlight(window.messageInput.getTextareaContainer());
        const message = carouselIndex !== null ? `캐러셀 ${carouselSlideIndex}번 내용을 입력해주세요.` : '발송할 내역이 없습니다.';
        return {success: false, message, subMessage: '메시지 내용을 입력 해주세요.'};
    }

    return {success: true};
}

function validateMessageContainsLink(campaignMessageType, carouselIndex = null, carouselSlideIndex = null) {
    const urlPattern = /https?:\/\//;
    const content = carouselIndex !== null ? window.carouselState.carousels[carouselIndex].message.replace(/^\s+|\s+$/g, '') : window.messageInput.getValue().replace(/^\s+|\s+$/g, '');
    if (urlPattern.test(content)) {
        if (carouselIndex !== null) {
            window.carouselState.setActiveIndex(carouselIndex);
        }
        NCDSValidator.highlight(window.messageInput.getTextareaContainer());
        const label = campaignMessageType === 'CAROUSEL_FEED' ? '캐러셀' : '와이드 이미지';
        const message = carouselIndex !== null
            ? `${label} ${carouselSlideIndex}번 메시지 타입은 메시지 내용에 링크(URL)를 포함할 수 없습니다.`
            : `${label} 메시지 타입은 메시지 내용에 링크(URL)를 포함할 수 없습니다.`;
        return {success: false, message};
    }
    return {success: true};
}

function validateCampaignName() {
    const campaignNameContainer = document.querySelector('input[name="friendtalkCampaignName"]');
    const campaignNameText = campaignNameContainer?.value?.replace(/^\s+|\s+$/g, '') ?? '';
    if (!campaignNameText) {
        NCDSValidator.highlight(campaignNameContainer);
        return {success: false, message: '캠페인명을 입력해주세요.'};
    }
    return {success: true};
}

function validateCampaignImage(carouselIndex = null) {
    const campaignImageUrl = document.querySelector('input[name="friendtalkCampaignImageUrl"]');
    if (!campaignImageUrl?.value) {
        const message = carouselIndex ? `캐러셀 ${carouselIndex}번 이미지를 등록해주세요.` : '이미지를 등록해주세요.';
        const imageButton = document.querySelector('#campaign-image-file-input-container .ncua-image-file-input');
        if (imageButton) {
            NCDSValidator.highlight(imageButton);
        }
        return {success: false, message};
    }
    return {success: true};
}

function validateCampaignImageLink(carouselIndex = null) {
    const campaignImageLink = document.querySelector('input[name="friendtalkCampaignImageLink"]');
    if (!campaignImageLink?.value) {
        const message = carouselIndex ? `캐러셀 ${carouselIndex}번 WEB 링크를 입력해주세요.` : 'WEB 링크를 입력해주세요.';
        NCDSValidator.highlight(campaignImageLink);
        return {success: false, message};
    }

    const urlPattern = /^https?:\/\//;
    const urlFormatMessage = carouselIndex
        ? `캐러셀 ${carouselIndex}번 http:// 또는 https:// 로 시작하는 URL을 입력해주세요.`
        : 'http:// 또는 https:// 로 시작하는 URL을 입력해주세요.';

    if (!urlPattern.test(campaignImageLink.value)) {
        NCDSValidator.highlight(campaignImageLink);
        return {success: false, message: urlFormatMessage};
    }

    return {success: true};
}

function validateMessageContainsUrl() {
    const messageContent = window.messageInput.getValue();
    const isContainUrl = /https?:\/\/[^\s]+/.test(messageContent);

    if (isContainUrl) {
        NCDSValidator.highlight(window.messageInput.getTextareaContainer());
        return {success: false, message: '와이드 이미지 메시지 타입은 메시지 내용에 링크(URL)을 포함할 수 없습니다.'};
    }
    return {success: true};
}

function validateLinkButtons(carouselIndex = null) {
    const rows = document.querySelectorAll('#linkButtonTableBody tr');
    for (const row of rows) {
        const buttonName = row.querySelector('.ncua-link-button-name');
        if (!buttonName?.value) {
            NCDSValidator.highlight(buttonName);
            const message = carouselIndex ? `캐러셀 ${carouselIndex}번 버튼명을 입력해주세요.` : '버튼명을 입력해주세요.';
            return {success: false, message};
        }

        const webUrl = row.querySelector('input[name="linkButtonWebUrl"]');
        if (webUrl && !webUrl.value) {
            NCDSValidator.highlight(webUrl);
            const message = carouselIndex ? `캐러셀 ${carouselIndex}번 WEB 링크를 입력해주세요.` : 'WEB 링크를 입력해주세요.';
            return {success: false, message};
        }

        const urlPattern = /^https?:\/\//;
        const urlFormatMessage = carouselIndex
            ? `캐러셀 ${carouselIndex}번 http:// 또는 https:// 로 시작하는 URL을 입력해주세요.`
            : 'http:// 또는 https:// 로 시작하는 URL을 입력해주세요.';
        if (webUrl?.value && !urlPattern.test(webUrl.value)) {
            NCDSValidator.highlight(webUrl);
            return {success: false, message: urlFormatMessage};
        }

        const iosUrl = row.querySelector('input[name="linkButtonIOSUrl"]');
        const aosUrl = row.querySelector('input[name="linkButtonAOSUrl"]');
        if (iosUrl && (!iosUrl.value || !aosUrl?.value)) {
            if (!iosUrl.value) NCDSValidator.highlight(iosUrl);
            if (!aosUrl?.value) NCDSValidator.highlight(aosUrl);
            const message = carouselIndex ? `캐러셀 ${carouselIndex}번 iOS, AOS 링크 모두 입력해주세요.` : 'iOS, AOS 링크 모두 입력해주세요.';
            return {success: false, message};
        }
        if (iosUrl?.value && !urlPattern.test(iosUrl.value)) {
            NCDSValidator.highlight(iosUrl);
            return {success: false, message: urlFormatMessage};
        }
        if (aosUrl?.value && !urlPattern.test(aosUrl.value)) {
            NCDSValidator.highlight(aosUrl);
            return {success: false, message: urlFormatMessage};
        }
    }
    return {success: true};
}

function validateCoupon(carouselIndex = null) {
    const couponTable = document.querySelector('#kakaoFriendTalkcouponTable');
    if (couponTable) {
        const selectCoupon = couponTable.querySelector('#selectCoupon');
        if (selectCoupon) {
            NCDSValidator.highlight(selectCoupon);
            const message = carouselIndex ? `캐러셀 ${carouselIndex}번 쿠폰을 선택해주세요.` : '쿠폰을 선택해주세요.';
            return {success: false, message};
        }

        const couponTitle = couponTable.querySelector('input[name="couponTitle"]');
        const couponDescription = couponTable.querySelector('input[name="couponDescription"]');
        const couponUrl = couponTable.querySelector('input[name="couponUrl"]');

        if (couponTitle) {
            const validCouponUrl = /^https?:\/\//.test(couponUrl?.value ?? '');
            if (!couponTitle.value || !couponDescription.value || !couponUrl.value || !validCouponUrl) {
                if (!couponTitle.value) NCDSValidator.highlight(couponTitle);
                if (!couponDescription.value) NCDSValidator.highlight(couponDescription);
                if (!couponUrl.value || !validCouponUrl) NCDSValidator.highlight(couponUrl);

                if (!couponTitle.value) {
                    const message = carouselIndex ? `캐러셀 ${carouselIndex}번 쿠폰 타이틀을 입력해주세요.` : '쿠폰 타이틀을 입력해주세요.';
                    return {success: false, message};
                }

                if (!couponDescription.value) {
                    const message = carouselIndex ? `캐러셀 ${carouselIndex}번 쿠폰 설명을 입력해주세요.` : '쿠폰 설명을 입력해주세요.';
                    return {success: false, message};
                }

                if (!couponUrl.value) {
                    const message = carouselIndex ? `캐러셀 ${carouselIndex}번 WEB 링크를 입력해주세요.` : 'WEB 링크를 입력해주세요.';
                    return {success: false, message};
                }

                if (!validCouponUrl) {
                    const message = carouselIndex ? `캐러셀 ${carouselIndex}번 http:// 또는 https:// 로 시작하는 URL을 입력해주세요.` : 'http:// 또는 https:// 로 시작하는 URL을 입력해주세요.';
                    return {success: false, message};
                }
            }
        }
    }
    return {success: true};
}

function validateItemList() {
    const kakaoFriendtalkItemListSection = document.querySelectorAll('.js-kakao-friendtalk-itemlist-section tbody tr');

    if (kakaoFriendtalkItemListSection.length < 3 || kakaoFriendtalkItemListSection.length > 4) {
        return {success: false, message: '와이드 아이템 리스트는 최소 3개 , 최대 4개까지 가능합니다.'};
    }

    for (const item of kakaoFriendtalkItemListSection) {
        const itemImageUrl = item.querySelector('input[name="itemImageUrl"]');
        const itemTitleContainer = item.querySelector('input[name="itemTitle"]');
        const itemLinkContainer = item.querySelector('input[name="itemWebLink"]');

        if (!itemImageUrl?.value) {
            const imageButton = item.querySelector('.campaign-item-image-file-input-container .ncua-image-file-input');
            if (imageButton) {
                NCDSValidator.highlight(imageButton);
            }
            return {success: false, message: '이미지를 등록해주세요.'};
        }

        if (!itemTitleContainer?.value?.replace(/^\s+|\s+$/g, '')) {
            NCDSValidator.highlight(itemTitleContainer);
            return {success: false, message: '타이틀을 입력해주세요.'};
        }

        if (!itemLinkContainer?.value?.replace(/^\s+|\s+$/g, '')) {
            NCDSValidator.highlight(itemLinkContainer);
            return {success: false, message: 'WEB 링크를 입력해주세요.'};
        }

        const urlPattern = /^https?:\/\//;
        const message = 'http:// 또는 https:// 로 시작하는 URL을 입력해주세요.';
        if (!urlPattern.test(itemLinkContainer.value)) {
            NCDSValidator.highlight(itemLinkContainer);
            return {success: false, message};
        }
    }

    return {success: true};
}

function validateMyappPushTitle() {
    const myappPushTitleContainer = document.querySelector('input[name="myappTitle"]');
    if (!myappPushTitleContainer.value) {
        NCDSValidator.highlight(myappPushTitleContainer);
        return {success: false, message: '푸시 제목을 입력해주세요.'};
    }

    return {success: true};
}

function validateUnsubscribeGuide() {
    const myappNotificationType = document.querySelector('input[type="radio"][name="myappNotificationType"]:checked').value;
    if (myappNotificationType === 'AD') {
        const unsubscribeGuideContainer = document.querySelector('input[name="myappUnsubscribeGuide"]');
        if (!unsubscribeGuideContainer.value) {
            NCDSValidator.highlight(unsubscribeGuideContainer);
            return {success: false, message: '수신동의 철회 방법을 입력해주세요.'};
        }
    }
    return {success: true};
}

function validateMyappPushUrlCheck(){
    const pushUrlPathContainer = document.querySelector('input[name="myappPushPath"]');
    // URL 경로가 비어있으면 검증 불필요 (메인페이지 이동)
    if (!pushUrlPathContainer.value) {
        return {success: true};
    }
    if (document.querySelector('input[name="validatePushUrl"]').value !== 'y') {
        NCDSValidator.highlight(pushUrlPathContainer);
        return {success: false, message: '입력한 푸시 URL을 검증해주세요.'};
    }
    return {success: true};
}

function validateSendPassword(sendPassword) {
    return new Promise((resolve) => {
        $.ajax({
            url: './mobile_send_ps.php',
            type: 'POST',
            data: { mode: 'validateSendPassword', sendPassword },
            dataType: 'json',
            success: (response) => {
                if (response.success) return resolve({ success: true });
                resolve({ success: false, message: response.message, subMessage: response.data?.subMessage });
            },
            error: () => {
                resolve({ success: false, message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.' });
            }
        });
    });
}
